<?php
/**
 * Handles the contact form (contact.php).
 * CSRF + honeypot + per-IP rate limit → validate → store in contact_messages →
 * mail the trust inbox (Reply-To set to the sender) → redirect back with a flash.
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/csrf.php';
require_once __DIR__ . '/inc/mail.php';
require_once __DIR__ . '/inc/respond.php';

uet_session_start();

function contact_redirect(array $flash): never
{
    $_SESSION['contact_flash'] = $flash;
    header('Location: contact.php#msgform', true, 303);
    exit;
}

function contact_fail(array $errors): never
{
    $old = [];
    foreach ($_POST as $k => $v) {
        if (is_string($v) && !in_array($k, ['csrf_token', 'website'], true)) {
            $old[$k] = mb_substr($v, 0, 5000);
        }
    }
    $_SESSION['contact_old'] = $old;
    contact_redirect(['type' => 'error', 'errors' => $errors]);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: contact.php', true, 303);
    exit;
}
if (!csrf_check()) {
    contact_fail(['Your session had expired before the message was sent. Please try again.']);
}
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    header('Location: contact.php', true, 303); // honeypot
    exit;
}

try {
    $pdo = db();
} catch (Throwable $e) {
    error_log('form_process: DB connection failed: ' . $e->getMessage());
    contact_fail(['The message could not be sent because of a server problem. Please WhatsApp +91 9176189469 instead.']);
}

$ip = substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
$pdo->prepare(
    'INSERT INTO rate_limits (ip, action, window_start, hits) VALUES (?, "contact", NOW(), 1)
     ON DUPLICATE KEY UPDATE
       hits = IF(window_start < NOW() - INTERVAL 3600 SECOND, 1, hits + 1),
       window_start = IF(window_start < NOW() - INTERVAL 3600 SECOND, NOW(), window_start)'
)->execute([$ip]);
$st = $pdo->prepare('SELECT hits FROM rate_limits WHERE ip = ? AND action = "contact"');
$st->execute([$ip]);
if ((int) $st->fetchColumn() > 10) {
    contact_fail(['Too many messages were sent from this connection. Please wait an hour and try again.']);
}

// ---- validate
$p = static fn(string $k): string => trim((string) ($_POST[$k] ?? ''));
$errors = [];
$name = mb_substr($p('firstname'), 0, 120);
if (mb_strlen($name) < 2) {
    $errors[] = 'Please enter your name.';
}
$email = mb_substr($p('email'), 0, 190);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address so that we can reply.';
}
$phone = preg_replace('/[^\d+ ]/', '', $p('phone')) ?? '';
$phone = mb_substr(trim($phone), 0, 20);
$subjects = ['Making a donation', 'Applying for support', 'Referring a student', 'Volunteering', 'Donation receipt / 80G', 'Something else'];
$subject = in_array($p('subject'), $subjects, true) ? $p('subject') : '';
$message = mb_substr($p('comment'), 0, 5000);
if (mb_strlen($message) < 5) {
    $errors[] = 'Please write your message.';
}
if ($errors) {
    contact_fail($errors);
}

// ---- store
$now = date('Y-m-d H:i:s');
try {
    $pdo->prepare('INSERT INTO contact_messages (name, email, phone, subject, message, ip, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)')
        ->execute([$name, $email, $phone, $subject, $message, $ip, $now]);
    $msgId = (int) $pdo->lastInsertId();
} catch (Throwable $e) {
    error_log('form_process: insert failed: ' . $e->getMessage());
    contact_fail(['The message could not be saved because of a server problem. Please WhatsApp +91 9176189469 instead.']);
}

// ---- confirm to the visitor first, then mail the trust
$_SESSION['contact_flash'] = ['type' => 'success', 'name' => $name, 'email' => $email];
unset($_SESSION['csrf_token']);
respond_redirect_then_continue('contact.php#msgform');

$inboxes = trust_inboxes();
if ($inboxes) {
    $subj = 'Website message: ' . ($subject !== '' ? $subject : 'General enquiry') . ' - ' . $name;
    $r = send_mail([
        'to'       => $inboxes,
        'reply_to' => ['email' => $email, 'name' => $name],
        'subject'  => $subj,
        'html'     => mail_template('contact_trust', ['name' => $name, 'email' => $email, 'phone' => $phone, 'subject' => $subject, 'message' => $message, 'received' => date('d-m-Y H:i', strtotime($now)), 'id' => $msgId]),
    ]);
    $pdo->prepare('INSERT INTO mail_log (owner_type, owner_id, recipient, subject, status, error, sent_at) VALUES ("contact", ?, ?, ?, ?, ?, NOW())')
        ->execute([$msgId, implode(',', array_column($inboxes, 'email')), $subj, $r['status'], $r['error'] ?: null]);
    if (!$r['ok']) {
        error_log('form_process: trust mail failed for message #' . $msgId . ': ' . $r['error']);
    }
}

exit;
