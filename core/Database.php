<?php
/**
 * Single shared PDO connection.
 * Real prepared statements are enforced (emulation off) and errors throw exceptions.
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $cfg = require __DIR__ . '/../config/config.php';
            self::$pdo = new PDO($cfg['db']['dsn'], $cfg['db']['user'], $cfg['db']['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }
}
