<?php
class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function username(): ?string
    {
        return $_SESSION['username'] ?? null;
    }

    public static function login(array $user): void
    {
        // New session ID on login prevents session fixation.
        session_regenerate_id(true);
        $_SESSION['user_id']  = (int) $user['id'];
        $_SESSION['username'] = $user['username'];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    /** Guard for protected actions: send guests to the login page. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            flash('Please log in to continue.');
            redirect('login');
        }
    }
}
