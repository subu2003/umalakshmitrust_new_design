<?php
/**
 * Loads settings from a .env file and exposes them through env().
 *
 * Looks for .env in this order and uses the first one found:
 *   1. one level ABOVE the web root (recommended on cPanel: /home/<user>/.env)
 *   2. the project root (next to index.php) — denied to browsers by .htaccess
 * Copy .env.example to .env and fill in the values. Fails loudly if none exists,
 * so a mis-deployed site never silently runs without mail or database settings.
 */

declare(strict_types=1);

if (!defined('UET_ROOT')) {
    define('UET_ROOT', dirname(__DIR__));
}

if (!function_exists('env')) {
    /** Read a setting; $default when the key is absent or empty. */
    function env(string $key, ?string $default = null): ?string
    {
        $v = $_ENV[$key] ?? getenv($key);
        if ($v === false || $v === null || $v === '') {
            return $default;
        }
        return (string) $v;
    }
}

if (!function_exists('uet_load_env')) {
    function uet_load_env(): void
    {
        static $loaded = false;
        if ($loaded) {
            return;
        }
        $loaded = true;
        $candidates = [dirname(UET_ROOT) . '/.env', UET_ROOT . '/.env'];
        $file = null;
        foreach ($candidates as $c) {
            if (is_file($c) && is_readable($c)) {
                $file = $c;
                break;
            }
        }
        if ($file === null) {
            http_response_code(500);
            // Deliberately terse for visitors; the fix is for the site owner.
            exit('Site configuration missing: create .env from .env.example (see README).');
        }
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v);
            if ($v !== '' && ($v[0] === '"' || $v[0] === "'") && substr($v, -1) === $v[0]) {
                $v = substr($v, 1, -1);
            }
            if ($k !== '' && !array_key_exists($k, $_ENV)) {
                $_ENV[$k] = $v;
                putenv($k . '=' . $v);
            }
        }
    }
}

uet_load_env();

date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Kolkata'));

if (env('APP_DEBUG', '0') === '1') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
}

/** Absolute path of the private upload folder (outside the web root on cPanel). */
if (!function_exists('upload_dir')) {
    function upload_dir(): string
    {
        $dir = env('UPLOAD_DIR');
        if ($dir === null || $dir === '') {
            $dir = UET_ROOT . '/storage/uploads';
        } elseif ($dir[0] !== '/') {
            $dir = UET_ROOT . '/' . $dir;
        }
        return rtrim($dir, '/');
    }
}

/** Start the session once, with cookie settings suitable for a public form site. */
if (!function_exists('uet_session_start')) {
    function uet_session_start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_name('uetsess');
        session_start();
    }
}
