<?php
/**
 * Shared PDO connection to the cPanel MySQL database.
 * Usage: $pdo = db();
 * Settings come from .env (DB_HOST, DB_NAME, DB_USER, DB_PASS, optional DB_PORT).
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (!function_exists('db')) {
    function db(): PDO
    {
        static $pdo = null;
        if ($pdo instanceof PDO) {
            return $pdo;
        }
        $host = env('DB_HOST', 'localhost');
        $name = env('DB_NAME', '');
        $user = env('DB_USER', '');
        $pass = env('DB_PASS', '');
        $port = env('DB_PORT', '3306');
        $sock = env('DB_SOCKET');

        $dsn = $sock
            ? "mysql:unix_socket={$sock};dbname={$name};charset=utf8mb4"
            : "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec("SET time_zone = '+05:30'");
        return $pdo;
    }
}
