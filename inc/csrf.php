<?php
/**
 * CSRF protection for the public forms.
 *   csrf_field()  — echo inside every <form method="post">
 *   csrf_check()  — call first thing in every POST handler; returns false on mismatch
 * Tokens live in the PHP session (see uet_session_start in config.php).
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        uet_session_start();
        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('csrf_check')) {
    function csrf_check(): bool
    {
        uet_session_start();
        $sent = $_POST['csrf_token'] ?? '';
        $have = $_SESSION['csrf_token'] ?? '';
        return is_string($sent) && is_string($have) && $have !== '' && hash_equals($have, $sent);
    }
}
