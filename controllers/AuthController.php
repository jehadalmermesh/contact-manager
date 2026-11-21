<?php
class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCK_SECONDS = 300;

    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('contacts');
        }
        $this->view('auth/login', ['error' => null, 'username' => '']);
    }

    public function login(): void
    {
        $this->requirePost();

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        // Simple brute-force throttle per session.
        $lockedUntil = $_SESSION['login_locked_until'] ?? 0;
        if ($lockedUntil > time()) {
            $this->view('auth/login', ['error' => 'Too many failed attempts. Try again in a few minutes.', 'username' => $username]);
            return;
        }

        $user = ($username !== '' && $password !== '') ? (new User())->attempt($username, $password) : null;

        if (!$user) {
            $_SESSION['login_failures'] = ($_SESSION['login_failures'] ?? 0) + 1;
            if ($_SESSION['login_failures'] >= self::MAX_ATTEMPTS) {
                $_SESSION['login_locked_until'] = time() + self::LOCK_SECONDS;
                $_SESSION['login_failures'] = 0;
            }
            // Same message for wrong username or wrong password: don't reveal which.
            $this->view('auth/login', ['error' => 'Invalid username or password.', 'username' => $username]);
            return;
        }

        unset($_SESSION['login_failures'], $_SESSION['login_locked_until']);
        Auth::login($user);
        flash('Welcome back, ' . $user['username'] . '.');
        redirect('contacts');
    }

    public function logout(): void
    {
        $this->requirePost();
        Auth::logout();
        session_start();
        flash('You have been logged out.');
        redirect('contacts');
    }
}
