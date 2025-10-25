<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class User
{
    public ?int $id = null;
    public string $username;
    public string $email;
    public string $passwordHash;
    public string $role = 'user';
    public ?string $createdAt = null;

    public static function findByUsername(string $username): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return self::fromArray($data);
    }

    public static function findByEmail(string $email): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return self::fromArray($data);
    }

    public static function findById(int $id): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return self::fromArray($data);
    }

    public function save(): bool
    {
        $db = Database::getConnection();

        if ($this->id === null) {
            // Insert new user
            $stmt = $db->prepare(
                "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)"
            );
            $result = $stmt->execute([
                $this->username,
                $this->email,
                $this->passwordHash,
                $this->role
            ]);

            if ($result) {
                $this->id = (int) $db->lastInsertId();
            }

            return $result;
        } else {
            // Update existing user
            $stmt = $db->prepare(
                "UPDATE users SET username = ?, email = ?, password_hash = ?, role = ? WHERE id = ?"
            );
            return $stmt->execute([
                $this->username,
                $this->email,
                $this->passwordHash,
                $this->role,
                $this->id
            ]);
        }
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    public function setPassword(string $password): void
    {
        $this->passwordHash = password_hash($password, PASSWORD_BCRYPT);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->createdAt
        ];
    }

    private static function fromArray(array $data): self
    {
        $user = new self();
        $user->id = (int) $data['id'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->passwordHash = $data['password_hash'];
        $user->role = $data['role'];
        $user->createdAt = $data['created_at'];

        return $user;
    }
}

