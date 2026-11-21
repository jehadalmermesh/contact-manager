<?php
class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT id, username, password_hash FROM users WHERE username = :u');
        $stmt->execute([':u' => $username]);
        return $stmt->fetch() ?: null;
    }

    /** Returns the user row if the password matches its stored hash, otherwise null. */
    public function attempt(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            // Upgrade the hash automatically if PHP's default algorithm/cost changes.
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                $stmt = $this->db->prepare('UPDATE users SET password_hash = :h WHERE id = :id');
                $stmt->execute([':h' => password_hash($password, PASSWORD_DEFAULT), ':id' => $user['id']]);
            }
            return $user;
        }
        return null;
    }

    /** Insert or replace the admin account (used by scripts/create_admin.php). */
    public function saveAdmin(string $username, string $password): void
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $existing = $this->findByUsername($username);
        if ($existing) {
            $stmt = $this->db->prepare('UPDATE users SET password_hash = :h WHERE id = :id');
            $stmt->execute([':h' => $hash, ':id' => $existing['id']]);
        } else {
            $stmt = $this->db->prepare('INSERT INTO users (username, password_hash) VALUES (:u, :h)');
            $stmt->execute([':u' => $username, ':h' => $hash]);
        }
    }
}
