<?php
/** Escape any value before printing it in HTML (prevents XSS). */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Build an internal URL for a route, e.g. url('contacts/edit', ['id' => 3]). */
function url(string $route, array $params = []): string
{
    return 'index.php?' . http_build_query(['route' => $route] + $params);
}

function redirect(string $route, array $params = []): never
{
    header('Location: ' . url($route, $params));
    exit;
}

/** One-time flash message stored in the session. */
function flash(?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'] = $message;
        return null;
    }
    $msg = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $msg;
}
