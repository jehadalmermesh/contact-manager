<?php
/**
 * Creates (or resets the password of) the single admin user.
 * Usage:  php scripts/create_admin.php <username>
 * The password is typed at the prompt so it never appears in shell history or in the SQL script.
 */
if (PHP_SAPI !== 'cli') {
    exit('Run this from the command line.');
}

require __DIR__ . '/../core/Database.php';
require __DIR__ . '/../models/User.php';

$username = $argv[1] ?? 'admin';

echo "Password for '$username' (min 8 chars): ";
if (DIRECTORY_SEPARATOR === '/') { system('stty -echo'); }
$password = trim((string) fgets(STDIN));
if (DIRECTORY_SEPARATOR === '/') { system('stty echo'); }
echo PHP_EOL;

if (strlen($password) < 8) {
    fwrite(STDERR, "Password must be at least 8 characters.\n");
    exit(1);
}

(new User())->saveAdmin($username, $password);
echo "Admin user '$username' saved (password stored as a bcrypt hash).\n";
