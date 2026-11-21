<?php
/**
 * Front controller: every request comes through this file.
 * It boots the app, then maps ?route=... to a controller method.
 */
declare(strict_types=1);

$config = require __DIR__ . '/config/config.php';

ini_set('display_errors', $config['debug'] ? '1' : '0');
error_reporting(E_ALL);

// Hardened session cookie.
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

// Basic security headers.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'; style-src 'self'");
header('Referrer-Policy: same-origin');

require __DIR__ . '/core/helpers.php';
require __DIR__ . '/core/Database.php';
require __DIR__ . '/core/Csrf.php';
require __DIR__ . '/core/Auth.php';
require __DIR__ . '/core/Controller.php';
require __DIR__ . '/models/Contact.php';
require __DIR__ . '/models/User.php';
require __DIR__ . '/controllers/ContactController.php';
require __DIR__ . '/controllers/AuthController.php';

// Whitelist of routes: route => [controller class, method]. Anything else is a 404.
$routes = [
    'contacts'         => [ContactController::class, 'index'],
    'contacts/create'  => [ContactController::class, 'create'],
    'contacts/store'   => [ContactController::class, 'store'],
    'contacts/edit'    => [ContactController::class, 'edit'],
    'contacts/update'  => [ContactController::class, 'update'],
    'contacts/delete'  => [ContactController::class, 'destroy'],
    'login'            => [AuthController::class, 'showLogin'],
    'login/submit'     => [AuthController::class, 'login'],
    'logout'           => [AuthController::class, 'logout'],
];

$route = $_GET['route'] ?? 'contacts';
if (!is_string($route) || !isset($routes[$route])) {
    http_response_code(404);
    exit('Page not found.');
}

try {
    [$class, $method] = $routes[$route];
    (new $class())->$method();
} catch (PDOException $ex) {
    // Never leak SQL or connection details to the browser in production.
    error_log($ex->getMessage());
    http_response_code(500);
    exit($config['debug'] ? 'Database error: ' . e($ex->getMessage()) : 'Something went wrong. Please try again later.');
}
