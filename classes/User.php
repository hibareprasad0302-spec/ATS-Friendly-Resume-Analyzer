<?php

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function register(string $name, string $email, string $password): int
    {
        $name = trim($name);
        $email = strtolower(trim($email));

        if (empty($name)) {
            throw new InvalidArgumentException('Name is required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format.');
        }

        if (strlen($password) < 8 || !preg_match('/[0-9]/', $password)
            || !preg_match('/[a-zA-Z]/', $password)) {
            throw new InvalidArgumentException(
                'Password must be at least 8 characters with letters and numbers.'
            );
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)'
            );
            $stmt->execute([$name, $email, $hash]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new RuntimeException('An account with this email already exists.');
            }
            throw $e;
        }
    }

    public function authenticate(string $email, string $password): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, full_name, email, password_hash, role FROM users WHERE email = ?'
        );
        $stmt->execute([strtolower(trim($email))]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
            $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                     ->execute([$newHash, $user['id']]);
        }

        unset($user['password_hash']);
        return $user;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, full_name, email, role, created_at FROM users WHERE id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->query('SELECT COUNT(*) FROM users');
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT u.id, u.full_name, u.email, u.role, u.created_at,
                    (SELECT COUNT(*) FROM resume_reports r WHERE r.user_id = u.id) as report_count
             FROM users u ORDER BY u.created_at DESC LIMIT ? OFFSET ?'
        );
        $stmt->execute([$perPage, $offset]);

        return [
            'users'       => $stmt->fetchAll(),
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function updateRole(int $id, string $role): bool
    {
        if (!in_array($role, ['user', 'admin'])) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE users SET role = ? WHERE id = ?');
        return $stmt->execute([$role, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
