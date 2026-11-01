<?php
/**
 * Handles the "After you pay" form on donate.php: stores the donor's details and
 * optional payment screenshot, checks the signed QR reference, and mails the trust.
 * The donor's amount and reference are self-reported; the trustees verify them
 * against the bank statement before issuing the 80G receipt (status = pending).
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/csrf.php';
require_once __DIR__ . '/inc/upload.php';
require_once __DIR__ . '/inc/mail.php';

uet_session_start();

function don_redirect(array $flash): never
{
    $_SESSION['donate_flash'] = $flash;
    header('Location: donate.php#thanks', true, 303);
    exit;
}
function don_fail(array $errors): never
{
    don_redirect(['type' => 'error', 'errors' => $errors]);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: donate.php', true, 303);
    exit;
}
if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    don_fail(['The screenshot is too large. Please keep it under ' . env('MAX_DOC_MB', '5') . ' MB.']);
}
if (!csrf_check()) {
    don_fail(['Your session had expired before the form was sent. Please try again.']);
}
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    header('Location: donate.php', true, 303); // honeypot
    exit;
}
try {
    $pdo = db();
} catch (Throwable $e) {
    error_log('donation_receipt: DB connection failed: ' . $e->getMessage());
    don_fail(['Your details could not be saved because of a server problem. Please WhatsApp them to +91 9176189469.']);
}
$ip = substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
$pdo->prepare(
    'INSERT INTO rate_limits (ip, action, window_start, hits) VALUES (?, "donation", NOW(), 1)
     ON DUPLICATE KEY UPDATE
       hits = IF(window_start < NOW() - INTERVAL 3600 SECOND, 1, hits + 1),
       window_start = IF(window_start < NOW() - INTERVAL 3600 SECOND, NOW(), window_start)'
)->execute([$ip]);
$st = $pdo->prepare('SELECT hits FROM rate_limits WHERE ip = ? AND action = "donation"');
$st->execute([$ip]);
if ((int) $st->fetchColumn() > 10) {
    don_fail(['Too many submissions from this connection. Please wait an hour and try again.']);
}

// ---- validate
$p = static fn(string $k): string => trim((string) ($_POST[$k] ?? ''));
$errors = [];
$name = mb_substr($p('name'), 0, 120);
if (mb_strlen($name) < 2) {
    $errors[] = 'Please enter your full name.';
}
$phone = preg_replace('/\D+/', '', $p('phone')) ?? '';
if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
    $phone = substr($phone, 2);
}
if (strlen($phone) !== 10) {
    $errors[] = 'Please enter a 10-digit mobile number.';
}
$email = mb_substr($p('email'), 0, 190);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address — the 80G receipt is sent there.';
}
$pan = strtoupper(preg_replace('/\s+/', '', $p('pan')) ?? '');
if ($pan !== '' && !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $pan)) {
    $errors[] = 'PAN should look like ABCDE1234F.';
}
$address = mb_substr($p('address'), 0, 500);
$amountRaw = str_replace([',', ' ', "\u{20B9}"], '', $p('amount'));
if (!preg_match('/^\d+(\.\d{1,2})?$/', $amountRaw) || (float) $amountRaw < 1 || (float) $amountRaw > 10000000) {
    $errors[] = 'Please enter the amount you donated, in rupees.';
    $amount = 0.0;
} else {
    $amount = round((float) $amountRaw, 2);
}
$txnRef = mb_substr(preg_replace('/[^A-Za-z0-9\-\/ ]/', '', $p('ref')) ?? '', 0, 60);
$listOnSite = $p('list') !== '' ? 1 : 0;

// signed QR reference from the page (optional: donors paying by bank transfer have none).
// The QR amount is only used to warn the trust when the reported amount differs; it is not stored.
$intentRef = ''; $intentAmount = null; $intentState = 'none';
$ir = $p('intent_ref'); $ia = $p('intent_amount'); $sig = $p('intent_sig');
if ($ir !== '' || $ia !== '' || $sig !== '') {
    if (preg_match('/^UET-D-\d{8}-[A-Z0-9]{4,8}$/', $ir) && preg_match('/^\d+\.\d{2}$/', $ia) && hash_equals(hash_hmac('sha256', $ir . '|' . $ia, app_key()), $sig)) {
        $intentRef = $ir; $intentAmount = (float) $ia; $intentState = 'valid';
    } else {
        $intentState = 'invalid'; // tampered or stale hidden fields: ignore them, but tell the trust
    }
}

$shot = null;
try {
    $shot = upload_take('screenshot', ['required' => false, 'kinds' => ['image', 'pdf'], 'max_mb' => (float) env('MAX_DOC_MB', '5')], 'Payment screenshot');
} catch (UploadError $e) {
    $errors[] = $e->getMessage();
}
if ($txnRef === '' && $shot === null) {
    $errors[] = 'Please give the UPI / transaction reference or attach the payment screenshot, so that we can match your payment.';
}
if ($errors) {
    don_fail(array_values(array_unique($errors)));
}

// ---- store
$now = date('Y-m-d H:i:s');
try {
    $pdo->prepare('INSERT INTO donations (intent_ref, name, phone, email, pan, address, amount, txn_ref, paid_on, mode, list_on_site, status, ip, created_at)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), "upi", ?, "pending", ?, ?)')
        ->execute([$intentRef !== '' ? $intentRef : null, $name, $phone, $email, $pan, $address, $amount, $txnRef, $listOnSite, $ip, $now]);
    $donId = (int) $pdo->lastInsertId();
    $shotPath = null;
    if ($shot) {
        $dir = upload_dir() . '/donations/' . $donId;
        $stored = upload_store($shot, $dir);
        $pdo->prepare('INSERT INTO files (owner_type, owner_id, kind, original_name, stored_name, mime, size, uploaded_at) VALUES ("donation", ?, "screenshot", ?, ?, ?, ?, ?)')
            ->execute([$donId, $shot['name'], $stored, $shot['mime'], $shot['size'], $now]);
        $pdo->prepare('UPDATE donations SET screenshot_file_id = ? WHERE id = ?')->execute([(int) $pdo->lastInsertId(), $donId]);
        $shotPath = $dir . '/' . $stored;
    }
} catch (Throwable $e) {
    error_log('donation_receipt: insert failed: ' . $e->getMessage());
    don_fail(['Your details could not be saved because of a server problem. Please WhatsApp them to +91 9176189469.']);
}

// ---- mail the trust
$inboxes = trust_inboxes();
if ($inboxes) {
    $subj = 'Donation reported: ' . "\u{20B9}" . number_format($amount) . ' by ' . $name . ($intentRef !== '' ? ' (' . $intentRef . ')' : '');
    $r = send_mail([
        'to'          => $inboxes,
        'reply_to'    => ['email' => $email, 'name' => $name],
        'subject'     => $subj,
        'html'        => mail_template('donation_trust', [
            'id' => $donId, 'name' => $name, 'phone' => $phone, 'email' => $email, 'pan' => $pan, 'address' => $address,
            'amount' => $amount, 'txnRef' => $txnRef, 'listOnSite' => $listOnSite, 'received' => date('d-m-Y H:i', strtotime($now)),
            'intentRef' => $intentRef, 'intentAmount' => $intentAmount, 'intentState' => $intentState, 'hasShot' => $shot !== null,
        ]),
        'attachments' => $shotPath ? [['path' => $shotPath, 'name' => 'payment-screenshot-' . $donId . '.' . $shot['ext']]] : [],
    ]);
    $pdo->prepare('INSERT INTO mail_log (owner_type, owner_id, recipient, subject, status, error, sent_at) VALUES ("donation", ?, ?, ?, ?, ?, NOW())')
        ->execute([$donId, implode(',', array_column($inboxes, 'email')), $subj, $r['status'], $r['error'] ?: null]);
    if (!$r['ok']) {
        error_log('donation_receipt: trust mail failed for donation #' . $donId . ': ' . $r['error']);
    }
}

unset($_SESSION['csrf_token']);
don_redirect(['type' => 'success', 'name' => $name, 'email' => $email, 'amount' => number_format($amount)]);
