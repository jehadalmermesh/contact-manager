<?php
/**
 * CSRF protection: every POST form carries a random per-session token,
 * and every POST request is rejected unless the token matches.
 */
class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::token()) . '">';
    }

    public static function verify(): void
    {
        $sent = $_POST['csrf_token'] ?? '';
        if (!is_string($sent) || !hash_equals(self::token(), $sent)) {
            http_response_code(419);
            exit('Invalid or expired form token. Go back, refresh the page and try again.');
        }
    }
}
