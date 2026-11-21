<?php
/**
 * Contact model. Every query uses a prepared statement with bound parameters;
 * user input is never concatenated into SQL.
 */
class Contact
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, name, email, phone, created_at FROM contacts ORDER BY name')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, phone, created_at FROM contacts WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO contacts (name, email, phone) VALUES (:name, :email, :phone)');
        $stmt->execute([':name' => $data['name'], ':email' => $data['email'], ':phone' => $data['phone']]);
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE contacts SET name = :name, email = :email, phone = :phone WHERE id = :id');
        $stmt->execute([':name' => $data['name'], ':email' => $data['email'], ':phone' => $data['phone'], ':id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM contacts WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function emailTaken(string $email, ?int $exceptId = null): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM contacts WHERE email = :email AND id <> :id');
        $stmt->execute([':email' => $email, ':id' => $exceptId ?? 0]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Server-side validation. Returns [cleanData, errors].
     */
    public static function validate(array $input): array
    {
        $data = [
            'name'  => trim((string) ($input['name'] ?? '')),
            'email' => trim((string) ($input['email'] ?? '')),
            'phone' => trim((string) ($input['phone'] ?? '')),
        ];
        $errors = [];

        $nameLen = mb_strlen($data['name']);
        if ($nameLen < 2 || $nameLen > 100) {
            $errors['name'] = 'Name must be between 2 and 100 characters.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($data['email']) > 150) {
            $errors['email'] = 'Enter a valid email address.';
        }
        // Digits with optional leading +, spaces or dashes; 7–20 characters.
        if (!preg_match('/^\+?[0-9][0-9 \-]{5,18}[0-9]$/', $data['phone'])) {
            $errors['phone'] = 'Enter a valid phone number (digits, spaces, dashes, optional leading +).';
        }

        return [$data, $errors];
    }
}
