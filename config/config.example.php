<?php
/**
 * Copy this file to config/config.php and fill in your own values.
 * config/config.php is listed in .gitignore so real credentials never reach Git.
 */
return [
    'db' => [
        'dsn'      => 'mysql:host=127.0.0.1;dbname=contact_manager;charset=utf8mb4',
        'user'     => 'root',
        'password' => '',
    ],
    // Set to false in production so errors are logged, not shown to users.
    'debug' => true,
];
