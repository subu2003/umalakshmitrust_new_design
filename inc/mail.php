<?php
/**
 * Outgoing mail through PHPMailer over SMTP (cPanel mailbox), or to .eml files when
 * MAIL_DRIVER=log (local development, or to debug delivery on the host).
 *
 *   $r = send_mail([
 *       'to'          => [['email' => 'a@b.c', 'name' => 'A']],
 *       'subject'     => '...',
 *       'html'        => '<p>...</p>',
 *       'text'        => 'plain fallback',            // optional
 *       'attachments' => [['path' => '/abs/file.pdf', 'name' => 'Application.pdf']],
 *   ]);
 *   // $r = ['ok' => bool, 'status' => 'sent'|'logged'|'failed', 'error' => string]
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once dirname(__DIR__) . '/lib/PHPMailer/src/Exception.php';
require_once dirname(__DIR__) . '/lib/PHPMailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/lib/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/** Trust inbox addresses from TRUST_INBOX (comma separated). */
function trust_inboxes(): array
{
    $out = [];
    foreach (explode(',', (string) env('TRUST_INBOX', '')) as $e) {
        $e = trim($e);
        if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)) {
            $out[] = ['email' => $e, 'name' => 'Umalakshmi Educational Trust'];
        }
    }
    return $out;
}

function send_mail(array $m): array
{
    $driver = strtolower((string) env('MAIL_DRIVER', 'smtp'));
    $mail   = new PHPMailer(true);
    try {
        $mail->CharSet  = PHPMailer::CHARSET_UTF8;
        $mail->Encoding = PHPMailer::ENCODING_BASE64;
        $mail->setFrom((string) env('MAIL_FROM', 'noreply@umalakshmitrust.com'), (string) env('MAIL_FROM_NAME', 'Umalakshmi Educational Trust'));
        if (!empty($m['reply_to']['email'])) {
            $mail->addReplyTo($m['reply_to']['email'], $m['reply_to']['name'] ?? '');
        } elseif ($reply = env('MAIL_REPLY_TO')) {
            $mail->addReplyTo($reply, 'Umalakshmi Educational Trust');
        }
        foreach ($m['to'] as $t) {
            $mail->addAddress($t['email'], $t['name'] ?? '');
        }
        foreach ($m['cc'] ?? [] as $t) {
            $mail->addCC($t['email'], $t['name'] ?? '');
        }
        $mail->Subject = (string) $m['subject'];
        $mail->isHTML(true);
        $mail->Body    = (string) $m['html'];
        $mail->AltBody = (string) ($m['text'] ?? strip_tags(preg_replace('/<br\s*\/?>|<\/p>/i', "\n", (string) $m['html'])));
        $logo = dirname(__DIR__) . '/assets/uet-logo.png';
        if (is_file($logo)) {
            $mail->addEmbeddedImage($logo, 'uetlogo', 'uet-logo.png');
        }
        foreach ($m['attachments'] ?? [] as $a) {
            if (is_file($a['path'])) {
                $mail->addAttachment($a['path'], $a['name'] ?? basename($a['path']));
            }
        }

        if ($driver === 'log') {
            $mail->preSend();
            $dir = upload_dir() . '/mail-log';
            if (!is_dir($dir)) {
                mkdir($dir, 0750, true);
            }
            $file = $dir . '/' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.eml';
            file_put_contents($file, $mail->getSentMIMEMessage());
            return ['ok' => true, 'status' => 'logged', 'error' => '', 'file' => $file];
        }

        $mail->isSMTP();
        $mail->Host       = (string) env('MAIL_HOST', 'localhost');
        $mail->Port       = (int) env('MAIL_PORT', '465');
        $mail->SMTPAuth   = (bool) env('MAIL_USER');
        $mail->Username   = (string) env('MAIL_USER', '');
        $mail->Password   = (string) env('MAIL_PASS', '');
        $enc = strtolower((string) env('MAIL_ENCRYPTION', 'ssl'));
        $mail->SMTPSecure = $enc === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : ($enc === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : false);
        $mail->SMTPAutoTLS = $enc !== 'none';
        $mail->Timeout    = 20;
        $mail->send();
        return ['ok' => true, 'status' => 'sent', 'error' => ''];
    } catch (Throwable $e) {
        return ['ok' => false, 'status' => 'failed', 'error' => $mail->ErrorInfo ?: $e->getMessage()];
    }
}

/** Render a template from /mail with the given variables; returns HTML. */
function mail_template(string $__template, array $__vars): string
{
    extract($__vars, EXTR_SKIP); // template variables ($name, $ref, ...) must not clash with these two
    ob_start();
    include dirname(__DIR__) . '/mail/' . $__template . '.php';
    return (string) ob_get_clean();
}
