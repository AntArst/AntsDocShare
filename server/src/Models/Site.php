<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Site
{
    public ?int $id = null;
    public string $name;
    public string $slug;
    public int $ownerUserId;
    public bool $active = true;
    public ?string $createdAt = null;
    public ?string $updatedAt = null;

    public static function findById(int $id): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM sites WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return self::fromArray($data);
    }

    public static function findBySlug(string $slug): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM sites WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return self::fromArray($data);
    }

    public static function findByUserId(int $userId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM sites WHERE owner_user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll();

        return array_map(fn($data) => self::fromArray($data), $results);
    }

    public static function findAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM sites ORDER BY created_at DESC");
        $results = $stmt->fetchAll();

        return array_map(fn($data) => self::fromArray($data), $results);
    }

    public function save(): bool
    {
        $db = Database::getConnection();

        if ($this->id === null) {
            // Insert new site
            $stmt = $db->prepare(
                "INSERT INTO sites (name, slug, owner_user_id, active) VALUES (?, ?, ?, ?)"
            );
            $result = $stmt->execute([
                $this->name,
                $this->slug,
                $this->ownerUserId,
                $this->active ? 1 : 0
            ]);

            if ($result) {
                $this->id = (int) $db->lastInsertId();
            }

            return $result;
        } else {
            // Update existing site
            $stmt = $db->prepare(
                "UPDATE sites SET name = ?, slug = ?, owner_user_id = ?, active = ? WHERE id = ?"
            );
            return $stmt->execute([
                $this->name,
                $this->slug,
                $this->ownerUserId,
                $this->active ? 1 : 0,
                $this->id
            ]);
        }
    }

    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE sites SET active = 0 WHERE id = ?");
        return $stmt->execute([$this->id]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'owner_user_id' => $this->ownerUserId,
            'active' => $this->active,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }

    private static function fromArray(array $data): self
    {
        $site = new self();
        $site->id = (int) $data['id'];
        $site->name = $data['name'];
        $site->slug = $data['slug'];
        $site->ownerUserId = (int) $data['owner_user_id'];
        $site->active = (bool) $data['active'];
        $site->createdAt = $data['created_at'];
        $site->updatedAt = $data['updated_at'];

        return $site;
    }
}

