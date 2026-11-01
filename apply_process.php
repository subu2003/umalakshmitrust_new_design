<?php
/**
 * Handles the scholarship application form (apply.php).
 *
 *  1. CSRF, honeypot and per-IP rate limit
 *  2. Server-side validation of every field and upload
 *  3. One transaction: applications + child tables (see sql/schema.sql)
 *  4. Uploads moved to UPLOAD_DIR/applications/<ref_no>/ and recorded in `files`
 *  5. PDF copy of the form + documents (inc/pdf_application.php)
 *  6. Mail to the applicant and to the trust inbox, logged in `mail_log`
 *  7. Redirect back to apply.php with a one-shot flash (success popup / errors)
 *
 * Never echoes applicant data. Mail or PDF problems are logged but do not lose
 * an application that is already stored.
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/csrf.php';
require_once __DIR__ . '/inc/upload.php';
require_once __DIR__ . '/inc/mail.php';
require_once __DIR__ . '/inc/pdf_application.php';

uet_session_start();

function apply_redirect(array $flash, string $anchor = ''): never
{
    $_SESSION['apply_flash'] = $flash;
    header('Location: apply.php' . $anchor, true, 303);
    exit;
}

function apply_fail(array $errors): never
{
    apply_redirect(['type' => 'error', 'errors' => $errors], '#applyform');
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

function academic_year(): string
{
    $env = env('APP_ACADEMIC_YEAR');
    if ($env) {
        return $env;
    }
    $y = (int) date('Y');
    $m = (int) date('n');
    $start = $m >= 4 ? $y : $y - 1;
    return $start . '-' . substr((string) ($start + 1), -2);
}

/** true when this IP has exceeded $max submissions inside $windowSec. */
function rate_limit_exceeded(PDO $pdo, string $ip, string $action, int $max, int $windowSec): bool
{
    $pdo->prepare(
        'INSERT INTO rate_limits (ip, action, window_start, hits) VALUES (?, ?, NOW(), 1)
         ON DUPLICATE KEY UPDATE
           hits = IF(window_start < NOW() - INTERVAL ? SECOND, 1, hits + 1),
           window_start = IF(window_start < NOW() - INTERVAL ? SECOND, NOW(), window_start)'
    )->execute([$ip, $action, $windowSec, $windowSec]);
    $st = $pdo->prepare('SELECT hits FROM rate_limits WHERE ip = ? AND action = ?');
    $st->execute([$ip, $action]);
    return (int) $st->fetchColumn() > $max;
}

/** Next UET-YYYY-NNNN inside an open transaction. */
function next_ref_no(PDO $pdo): string
{
    $year = date('Y');
    $st = $pdo->prepare('SELECT ref_no FROM applications WHERE ref_no LIKE ? ORDER BY id DESC LIMIT 1 FOR UPDATE');
    $st->execute(['UET-' . $year . '-%']);
    $last = (string) $st->fetchColumn();
    $n = $last !== '' ? (int) substr($last, -4) + 1 : 1;
    return sprintf('UET-%s-%04d', $year, $n);
}

// ---------------------------------------------------------------------------
// 1. Gatekeeping
// ---------------------------------------------------------------------------

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: apply.php', true, 303);
    exit;
}
if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    // post_max_size exceeded: PHP drops the whole body
    apply_fail(['The attachments are too large in total. Please keep each file under ' . env('MAX_DOC_MB', '5') . ' MB and try again.']);
}
if (!csrf_check()) {
    apply_fail(['Your session had expired before the form was sent. Please fill in the form again.']);
}
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    header('Location: apply.php', true, 303); // honeypot: silently drop bots
    exit;
}

try {
    $pdo = db();
} catch (Throwable $e) {
    error_log('apply_process: DB connection failed: ' . $e->getMessage());
    apply_fail(['The application could not be saved because of a server problem. Please try again later or WhatsApp +91 9176189469.']);
}

$ip = client_ip();
// 20 attempts per hour per IP: generous because many Indian mobile users share one public IP (CGNAT),
// and failed validation attempts count too.
if (rate_limit_exceeded($pdo, $ip, 'apply', 20, 3600)) {
    apply_fail(['Too many applications were sent from this connection. Please wait an hour and try again.']);
}

// ---------------------------------------------------------------------------
// 2. Read and validate
// ---------------------------------------------------------------------------

$errors = [];
$p = static fn(string $k): string => trim((string) ($_POST[$k] ?? ''));
$opt = static function (string $k, array $allowed): string {
    $v = strtolower(trim((string) ($_POST[$k] ?? '')));
    return in_array($v, $allowed, true) ? $v : '';
};
$num = static function (string $k, float $min, float $max, string $label, array &$errors): ?float {
    $v = trim((string) ($_POST[$k] ?? ''));
    if ($v === '') {
        return null;
    }
    $v = str_replace([',', ' '], '', $v);
    if (!is_numeric($v) || (float) $v < $min || (float) $v > $max) {
        $errors[] = $label . ': please enter a number between ' . $min . ' and ' . number_format($max) . '.';
        return null;
    }
    return (float) $v;
};
$phoneNorm = static function (string $raw): string {
    $d = preg_replace('/\D+/', '', $raw) ?? '';
    if (strlen($d) === 12 && str_starts_with($d, '91')) {
        $d = substr($d, 2);
    }
    if (strlen($d) === 11 && $d[0] === '0') {
        $d = substr($d, 1);
    }
    return $d;
};
$sel = static fn(string $k): string => (str_starts_with($p($k), 'Select') ? '' : $p($k));

// Applicant
$name = mb_substr($p('name'), 0, 120);
if (mb_strlen($name) < 2) {
    $errors[] = 'Full name is required.';
}
$gender = $opt('gender', ['male', 'female']);
if ($gender === '') {
    $errors[] = 'Please select the gender.';
}
$dob = $p('dob');
$dobTs = $dob !== '' ? strtotime($dob) : false;
if ($dobTs === false) {
    $errors[] = 'Date of birth is required.';
} else {
    $age = (int) date('Y') - (int) date('Y', $dobTs);
    if ($age < 3 || $age > 45) {
        $errors[] = 'Date of birth does not look right (age ' . $age . ').';
    }
}
$standard = mb_substr($sel('standard'), 0, 40);
if ($standard === '') {
    $errors[] = 'Please select the standard / year of study.';
}
$course   = mb_substr($p('course'), 0, 120);
$phone    = $phoneNorm($p('phone'));
if (strlen($phone) !== 10) {
    $errors[] = 'Contact phone must be a 10-digit mobile number.';
}
$whatsapp = $p('whatsapp') !== '' ? $phoneNorm($p('whatsapp')) : '';
if ($whatsapp !== '' && strlen($whatsapp) !== 10) {
    $errors[] = 'WhatsApp number must be a 10-digit mobile number.';
}
$email = mb_substr($p('email'), 0, 190);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required (the application copy is sent there).';
}
$address = mb_substr($p('address'), 0, 1000);
if ($address === '') {
    $errors[] = 'Residential address is required.';
}

// Family
$family = [];
foreach ([['father', 'Father', 'father'], ['mother', 'Mother', 'mother'], ['sib1', 'Sibling 1', 'sibling'], ['sib2', 'Sibling 2', 'sibling'], ['guard', 'Guardian', 'guardian']] as [$k, $label, $rel]) {
    $fn = mb_substr($p($k . '_name'), 0, 120);
    $inc = $num($k . '_inc', 0, 99999999, $label . ' income', $errors);
    if (($k === 'father' || $k === 'mother') && ($fn === '' || $p($k . '_edu') === '' || $p($k . '_occ') === '' || $p($k . '_inc') === '')) {
        $errors[] = $label . ': name, education, occupation and yearly income are required.';
    }
    $family[] = [
        'relation' => $rel, 'label' => $label, 'name' => $fn,
        'education' => mb_substr($p($k . '_edu'), 0, 120), 'occupation' => mb_substr($p($k . '_occ'), 0, 120),
        'income' => $inc, 'income_display' => $inc === null ? '' : number_format($inc),
    ];
}
$otherIncome = $opt('other_income', ['yes', 'no']);
if ($otherIncome === '') {
    $errors[] = 'Please answer "Any other income?".';
}
$otherIncomeDetail = mb_substr($p('other_income_detail'), 0, 255);
$house = $opt('house', ['own', 'rent']);
if ($house === '') {
    $errors[] = 'Please answer "Own house or rented?".';
}
$rent  = $num('rent', 0, 9999999, 'Rent per month', $errors);

// Education
$school = mb_substr($p('school'), 0, 190);
if ($school === '') {
    $errors[] = 'School / College name is required.';
}
$schoolPhone = $p('school_phone') !== '' ? preg_replace('/[^\d+ -]/', '', $p('school_phone')) : '';
if (strlen(preg_replace('/\D/', '', (string) $schoolPhone)) < 6) {
    $errors[] = 'School / College phone is required.';
}
$schoolAddress = mb_substr($p('school_address'), 0, 1000);
if ($schoolAddress === '') {
    $errors[] = 'School / College address is required.';
}
$hmName   = mb_substr($p('hm_name'), 0, 120);
if ($hmName === '') {
    $errors[] = 'Headmaster / Principal name is required.';
}
$instType = mb_substr($sel('inst_type'), 0, 60);
if ($instType === '') {
    $errors[] = 'Please select the type of institution.';
}
$marks = [];
for ($i = 1; $i <= 5; $i++) {
    $sub = mb_substr($p('sub' . $i), 0, 80);
    $q = $num('q' . $i, 0, 100, 'Quarterly mark ' . $i, $errors);
    $h = $num('h' . $i, 0, 100, 'Half-yearly mark ' . $i, $errors);
    $a = $num('a' . $i, 0, 100, 'Annual mark ' . $i, $errors);
    if ($i <= 3 && ($sub === '' || $q === null || $h === null || $a === null)) {
        $errors[] = 'Summary of marks: subject ' . $i . ' needs the subject name and all three marks.';
    }
    if ($sub !== '' || $q !== null || $h !== null || $a !== null) {
        $marks[] = ['subject' => $sub !== '' ? $sub : 'Subject ' . $i, 'quarterly' => $q, 'half_yearly' => $h, 'annual' => $a];
    }
}
$qt = $num('qt', 0, 100, 'Quarterly total %', $errors);
$ht = $num('ht', 0, 100, 'Half-yearly total %', $errors);
$at = $num('at', 0, 100, 'Annual total %', $errors);
if ($qt === null || $ht === null || $at === null) {
    $errors[] = 'Summary of marks: the three total percentages are required.';
}
if ($qt !== null || $ht !== null || $at !== null) {
    $marks[] = ['subject' => 'Total', 'quarterly' => $qt, 'half_yearly' => $ht, 'annual' => $at];
}
$passed   = $opt('passed', ['yes', 'no']);
if ($passed === '') {
    $errors[] = 'Please answer "Passed all subjects?".';
}
$rank     = $num('rank', 1, 9999, 'Rank', $errors);
$strength = $num('strength', 1, 9999, 'Class strength', $errors);
if ($rank === null) {
    $errors[] = 'Rank in class is required.';
}
if ($strength === null) {
    $errors[] = 'Class strength is required.';
}
$awards   = mb_substr($p('awards'), 0, 2000);

// Funding
$applied  = $opt('applied', ['yes', 'no']);
$received = $opt('received', ['yes', 'no']);
if ($applied === '') {
    $errors[] = 'Please answer "Applied to the trust before?".';
}
if ($received === '') {
    $errors[] = 'Please answer "Received support before?".';
}
$years    = mb_substr($p('years'), 0, 120);
$fees = [];
for ($i = 1; $i <= 3; $i++) {
    $label = mb_substr($p('exp' . $i), 0, 120);
    $amt   = $num('amt' . $i, 0, 99999999, 'Amount ' . $i, $errors);
    if ($label !== '' && $amt !== null) {
        $fees[] = ['label' => $label, 'amount' => $amt, 'amount_display' => number_format($amt)];
    }
}
$total = $num('total', 0, 99999999, 'Total fees', $errors);
if ($total === null) {
    $errors[] = 'Total fees per year is required.';
}

// Declaration
$declared   = ($p('declare') === 'yes' || $p('declare') === 'on');
if (!$declared) {
    $errors[] = 'Please tick the declaration.';
}
$parentName = mb_substr($p('parent_name'), 0, 120);
if ($parentName === '') {
    $errors[] = 'Name of parent / guardian is required.';
}
$decDateTs = $p('date') !== '' ? strtotime($p('date')) : false;
if ($decDateTs === false) {
    $errors[] = 'Declaration date is required.';
    $decDateTs = time();
}

// Uploads
$maxPhoto = (float) env('MAX_PHOTO_MB', '2');
$maxDoc   = (float) env('MAX_DOC_MB', '5');
$uploadSpec = [
    'photo'     => ['label' => 'Photograph',           'kind' => 'photo',       'rules' => ['required' => true,  'kinds' => ['image'],        'max_mb' => $maxPhoto]],
    'marksheet' => ['label' => 'Mark sheet',           'kind' => 'marksheet',   'rules' => ['required' => false, 'kinds' => ['image', 'pdf'], 'max_mb' => $maxDoc]],
    'feedemand' => ['label' => 'Fee demand / receipt', 'kind' => 'fee_demand',  'rules' => ['required' => false, 'kinds' => ['image', 'pdf'], 'max_mb' => $maxDoc]],
    'income'    => ['label' => 'Income certificate',   'kind' => 'income_cert', 'rules' => ['required' => false, 'kinds' => ['image', 'pdf'], 'max_mb' => $maxDoc]],
    'bonafide'  => ['label' => 'Bonafide certificate', 'kind' => 'bonafide',    'rules' => ['required' => true,  'kinds' => ['image', 'pdf'], 'max_mb' => $maxDoc]],
];
$uploads = [];      // field => file (single)
$certUploads = [];  // list (up to 5)
try {
    $certUploads = upload_take_many('certs', ['kinds' => ['image', 'pdf'], 'max_mb' => $maxDoc], 'Award certificate', 5);
} catch (UploadError $e) {
    $errors[] = $e->getMessage();
}
foreach ($uploadSpec as $field => $spec) {
    try {
        $f = upload_take($field, $spec['rules'], $spec['label']);
        if ($f !== null) {
            $uploads[$field] = $f + ['label' => $spec['label'], 'kind' => $spec['kind']];
        }
    } catch (UploadError $e) {
        $errors[] = $e->getMessage();
    }
}

if ($errors) {
    apply_fail(array_values(array_unique($errors)));
}

// ---------------------------------------------------------------------------
// 3. Store (one transaction)
// ---------------------------------------------------------------------------

$now = date('Y-m-d H:i:s');
$academicYear = academic_year();
$ref = '';
$appId = 0;

for ($attempt = 1; $attempt <= 3; $attempt++) {
    try {
        $pdo->beginTransaction();
        $ref = next_ref_no($pdo);
        $pdo->prepare('INSERT INTO applications (ref_no, academic_year, status, submitted_at, ip, consent_declared, parent_name, declared_on)
                       VALUES (?, ?, "new", ?, ?, 1, ?, ?)')
            ->execute([$ref, $academicYear, $now, $ip, $parentName, date('Y-m-d', $decDateTs)]);
        $appId = (int) $pdo->lastInsertId();

        $pdo->prepare('INSERT INTO applicants (application_id, name, gender, dob, standard, course, phone, whatsapp, email, address)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
            ->execute([$appId, $name, $gender, date('Y-m-d', (int) $dobTs), $standard, $course, $phone, $whatsapp, $email, $address]);

        $fm = $pdo->prepare('INSERT INTO family_members (application_id, relation, name, education, occupation, yearly_income) VALUES (?, ?, ?, ?, ?, ?)');
        foreach ($family as $m) {
            if ($m['name'] === '' && $m['education'] === '' && $m['occupation'] === '' && $m['income'] === null) {
                continue;
            }
            $fm->execute([$appId, $m['relation'], $m['name'], $m['education'], $m['occupation'], $m['income']]);
        }
        $pdo->prepare('INSERT INTO family_details (application_id, other_income, other_income_detail, house, rent_per_month) VALUES (?, ?, ?, ?, ?)')
            ->execute([$appId, $otherIncome, $otherIncomeDetail, $house, $rent]);

        $pdo->prepare('INSERT INTO education (application_id, institution, inst_type, address, phone, hm_name, passed_all, `rank`, class_strength, awards)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
            ->execute([$appId, $school, $instType, $schoolAddress, $schoolPhone, $hmName, $passed, $rank === null ? null : (int) $rank, $strength === null ? null : (int) $strength, $awards]);

        $mk = $pdo->prepare('INSERT INTO marks (application_id, subject, quarterly, half_yearly, annual) VALUES (?, ?, ?, ?, ?)');
        foreach ($marks as $m) {
            $mk->execute([$appId, $m['subject'], $m['quarterly'], $m['half_yearly'], $m['annual']]);
        }

        $pdo->prepare('INSERT INTO funding (application_id, applied_before, received_before, years, total_fees) VALUES (?, ?, ?, ?, ?)')
            ->execute([$appId, $applied, $received, $years, $total]);
        $fi = $pdo->prepare('INSERT INTO fee_items (application_id, label, amount) VALUES (?, ?, ?)');
        foreach ($fees as $fee) {
            $fi->execute([$appId, $fee['label'], $fee['amount']]);
        }

        $pdo->commit();
        break;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $dup = ($e->errorInfo[1] ?? 0) === 1062; // duplicate ref_no under concurrency: retry
        if (!$dup || $attempt === 3) {
            error_log('apply_process: insert failed: ' . $e->getMessage());
            apply_fail(['The application could not be saved because of a server problem. Please try again in a few minutes or WhatsApp +91 9176189469.']);
        }
    }
}

// ---------------------------------------------------------------------------
// 4. Files
// ---------------------------------------------------------------------------

$appDir = upload_dir() . '/applications/' . $ref;
$fileRows = []; // for PDF + mail: kind,label,path,mime,name,size
try {
    $ins = $pdo->prepare('INSERT INTO files (owner_type, owner_id, kind, original_name, stored_name, mime, size, uploaded_at) VALUES ("application", ?, ?, ?, ?, ?, ?, ?)');
    $all = $uploads;
    foreach ($certUploads as $n => $cf) {
        $all['certs' . $n] = $cf + ['label' => 'Award certificate ' . ($n + 1), 'kind' => 'certificate'];
    }
    foreach ($all as $field => $f) {
        $stored = upload_store($f, $appDir);
        $ins->execute([$appId, $f['kind'], $f['name'], $stored, $f['mime'], $f['size'], $now]);
        $fileId = (int) $pdo->lastInsertId();
        if ($f['kind'] === 'photo') {
            $pdo->prepare('UPDATE applicants SET photo_file_id = ? WHERE application_id = ?')->execute([$fileId, $appId]);
        }
        $fileRows[] = ['kind' => $f['kind'], 'label' => $f['label'], 'path' => $appDir . '/' . $stored, 'mime' => $f['mime'], 'name' => $f['name'], 'size' => $f['size']];
    }
} catch (Throwable $e) {
    error_log('apply_process: file storage failed for ' . $ref . ': ' . $e->getMessage());
}

// ---------------------------------------------------------------------------
// 5. PDF copy
// ---------------------------------------------------------------------------

$app = [
    'ref_no' => $ref, 'academic_year' => $academicYear, 'submitted_at' => date('d-m-Y H:i', strtotime($now)), 'ip' => $ip,
    'applicant' => ['name' => $name, 'gender' => $gender, 'dob_display' => date('d-m-Y', (int) $dobTs), 'standard' => $standard, 'course' => $course,
                    'phone' => $phone, 'whatsapp' => $whatsapp, 'email' => $email, 'address' => $address],
    'family' => $family,
    'details' => ['other_income' => $otherIncome, 'other_income_detail' => $otherIncomeDetail, 'house' => $house, 'rent_display' => $rent === null ? '' : number_format($rent)],
    'education' => ['institution' => $school, 'inst_type' => $instType, 'address' => $schoolAddress, 'phone' => $schoolPhone, 'hm_name' => $hmName,
                    'passed_all' => $passed, 'rank' => $rank === null ? '' : (string) (int) $rank, 'class_strength' => $strength === null ? '' : (string) (int) $strength, 'awards' => $awards],
    'marks' => array_map(static fn($m) => ['subject' => $m['subject'], 'quarterly' => $m['quarterly'] === null ? '' : rtrim(rtrim(number_format($m['quarterly'], 2), '0'), '.'),
                                           'half_yearly' => $m['half_yearly'] === null ? '' : rtrim(rtrim(number_format($m['half_yearly'], 2), '0'), '.'),
                                           'annual' => $m['annual'] === null ? '' : rtrim(rtrim(number_format($m['annual'], 2), '0'), '.')], $marks),
    'funding' => ['applied_before' => $applied, 'received_before' => $received, 'years' => $years, 'total_display' => $total === null ? '' : number_format($total)],
    'fees' => $fees,
    'declaration' => ['agreed' => $declared, 'parent_name' => $parentName, 'date_display' => date('d-m-Y', $decDateTs)],
];

$pdfPath = null;
$separate = [];
try {
    $r = application_pdf_build($app, $fileRows, $appDir . '/' . $ref . '-application.pdf');
    $pdfPath  = $r['path'];
    $separate = $r['separate'];
    $pdo->prepare('INSERT INTO files (owner_type, owner_id, kind, original_name, stored_name, mime, size, uploaded_at) VALUES ("application", ?, "application_pdf", ?, ?, "application/pdf", ?, ?)')
        ->execute([$appId, $ref . '-application.pdf', basename($pdfPath), filesize($pdfPath), $now]);
    $pdo->prepare('UPDATE applications SET pdf_file_id = ? WHERE id = ?')->execute([(int) $pdo->lastInsertId(), $appId]);
} catch (Throwable $e) {
    error_log('apply_process: PDF failed for ' . $ref . ': ' . $e->getMessage());
}

// ---------------------------------------------------------------------------
// 6. Mail
// ---------------------------------------------------------------------------

$attachments = [];
if ($pdfPath) {
    $attachments[] = ['path' => $pdfPath, 'name' => 'Application-' . $ref . '.pdf'];
}
foreach ($separate as $s) {
    $attachments[] = ['path' => $s['path'], 'name' => preg_replace('/[^A-Za-z0-9._-]+/', '_', $s['label']) . '-' . $s['name']];
}
$mailLog = $pdo->prepare('INSERT INTO mail_log (owner_type, owner_id, recipient, subject, status, error, sent_at) VALUES ("application", ?, ?, ?, ?, ?, NOW())');

$subjApplicant = 'Your application ' . $ref . ' - Umalakshmi Educational Trust';
$r1 = send_mail([
    'to' => [['email' => $email, 'name' => $name]],
    'subject' => $subjApplicant,
    'html' => mail_template('application_applicant', ['ref' => $ref, 'name' => $name, 'year' => $academicYear, 'submitted' => $app['submitted_at'], 'separateCount' => count($separate)]),
    'attachments' => $attachments,
]);
$mailLog->execute([$appId, $email, $subjApplicant, $r1['status'], $r1['error'] ?: null]);
if (!$r1['ok']) {
    error_log('apply_process: applicant mail failed for ' . $ref . ': ' . $r1['error']);
}

$inboxes = trust_inboxes();
if ($inboxes) {
    $subjTrust = 'New application ' . $ref . ' - ' . mb_strtoupper($name) . ' (' . $standard . ')';
    $r2 = send_mail([
        'to' => $inboxes,
        'subject' => $subjTrust,
        'html' => mail_template('application_trust', ['ref' => $ref, 'app' => $app, 'files' => $fileRows, 'separateCount' => count($separate)]),
        'attachments' => $attachments,
    ]);
    $mailLog->execute([$appId, implode(',', array_column($inboxes, 'email')), $subjTrust, $r2['status'], $r2['error'] ?: null]);
    if (!$r2['ok']) {
        error_log('apply_process: trust mail failed for ' . $ref . ': ' . $r2['error']);
    }
}

// ---------------------------------------------------------------------------
// 7. Done
// ---------------------------------------------------------------------------

unset($_SESSION['csrf_token']); // one token per submission
apply_redirect(['type' => 'success', 'ref' => $ref, 'email' => $email]);
