<?php
/**
 * Send the browser its redirect now and keep running afterwards, so slow work
 * (PDF generation, SMTP) does not delay the "submitted" page.
 *
 *   respond_redirect_then_continue('apply.php');
 *   // ... build PDF, send mail ... (nothing may be echoed after this point)
 *
 * Uses fastcgi_finish_request() (PHP-FPM) or litespeed_finish_request() (LiteSpeed)
 * when available; otherwise closes the connection explicitly, which Apache/mod_php
 * honours in most setups. The session is written and closed first so the next
 * request from the same visitor is not blocked.
 */

declare(strict_types=1);

function respond_redirect_then_continue(string $location): void
{
    @ignore_user_abort(true);
    @set_time_limit(180);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    http_response_code(303);
    header('Location: ' . $location);
    header('Content-Length: 0');
    header('Connection: close');
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
        return;
    }
    if (function_exists('litespeed_finish_request')) {
        litespeed_finish_request();
        return;
    }
    while (ob_get_level() > 0) {
        @ob_end_flush();
    }
    flush();
}
