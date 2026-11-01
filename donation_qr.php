<?php
/**
 * Prepares a donation QR: validates the amount on the server, issues a unique signed
 * reference, and returns the UPI parameters the page turns into a QR code and app links.
 * Nothing is stored here (only the per-IP rate counter); the reference is kept with the
 * donor's report in `donations` when they fill "After you pay".
 * The UPI ID and payee come from .env — never from the browser — so a tampered page
 * cannot redirect payments. The reference is signed (HMAC) so the "After you pay" form
 * cannot claim a different QR amount.
 *
 * POST amount, csrf_token  →  JSON { ok, ref, amount, pa, pn, tn, valid, sig }
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/csrf.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function qr_fail(string $msg, int $code = 400): never
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

uet_session_start();
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    qr_fail('Method not allowed', 405);
}
if (!csrf_check()) {
    qr_fail('Your session has expired. Please reload the page and try again.', 403);
}

$min = (float) env('DONATION_MIN', '10');
$max = (float) env('DONATION_MAX', '100000');
$raw = str_replace([',', ' ', "\u{20B9}"], '', trim((string) ($_POST['amount'] ?? '')));
if ($raw === '' || !preg_match('/^\d+(\.\d{1,2})?$/', $raw)) {
    qr_fail('Please enter a valid amount in rupees.');
}
$amount = round((float) $raw, 2);
if ($amount < $min || $amount > $max) {
    qr_fail('Please enter an amount between ' . "\u{20B9}" . number_format($min) . ' and ' . "\u{20B9}" . number_format($max) . '.');
}

$vpa   = trim((string) env('UPI_VPA', ''));
$payee = trim((string) env('UPI_PAYEE', 'Umalakshmi Educational Trust'));
if ($vpa === '' || !preg_match('/^[A-Za-z0-9.\-_]{2,}@[A-Za-z0-9.\-]{2,}$/', $vpa)) {
    error_log('donation_qr: UPI_VPA is not configured');
    qr_fail('Online payment is not available right now. Please use the bank details.', 503);
}
$valid = max(60, min(900, (int) env('QR_VALID_SECONDS', '180')));

try {
    $pdo = db();
    $ip  = substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
    $pdo->prepare(
        'INSERT INTO rate_limits (ip, action, window_start, hits) VALUES (?, "qr", NOW(), 1)
         ON DUPLICATE KEY UPDATE
           hits = IF(window_start < NOW() - INTERVAL 3600 SECOND, 1, hits + 1),
           window_start = IF(window_start < NOW() - INTERVAL 3600 SECOND, NOW(), window_start)'
    )->execute([$ip]);
    $st = $pdo->prepare('SELECT hits FROM rate_limits WHERE ip = ? AND action = "qr"');
    $st->execute([$ip]);
    if ((int) $st->fetchColumn() > 60) {
        qr_fail('Too many QR codes were requested from this connection. Please try again in an hour.', 429);
    }

    // reference: date + 6 random characters (no ambiguous letters), signed below
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $rand = '';
    for ($i = 0; $i < 6; $i++) {
        $rand .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }
    $ref = 'UET-D-' . date('Ymd') . '-' . $rand;
} catch (Throwable $e) {
    error_log('donation_qr: ' . $e->getMessage());
    qr_fail('The QR code could not be prepared because of a server problem. Please try again.', 500);
}

$amountStr = number_format($amount, 2, '.', '');
echo json_encode([
    'ok'     => true,
    'ref'    => $ref,
    'amount' => $amountStr,
    'pa'     => $vpa,
    'pn'     => $payee,
    'tn'     => 'Donation ' . $ref,
    'valid'  => $valid,
    'sig'    => hash_hmac('sha256', $ref . '|' . $amountStr, app_key()),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
