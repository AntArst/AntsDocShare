<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Product
{
    public ?int $id = null;
    public int $siteId;
    public string $itemName;
    public ?string $imageName = null;
    public ?float $price = null;
    public ?string $description = null;
    public ?array $assets = null;
    public ?string $sampleImage = null;
    public ?string $createdAt = null;

    public static function findById(int $id): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return self::fromArray($data);
    }

    public static function findBySiteId(int $siteId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM products WHERE site_id = ? ORDER BY created_at DESC");
        $stmt->execute([$siteId]);
        $results = $stmt->fetchAll();

        return array_map(fn($data) => self::fromArray($data), $results);
    }

    public static function deleteBySiteId(int $siteId): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM products WHERE site_id = ?");
        return $stmt->execute([$siteId]);
    }

    public function save(): bool
    {
        $db = Database::getConnection();
        $assetsJson = $this->assets ? json_encode($this->assets) : null;

        if ($this->id === null) {
            // Insert new product
            $stmt = $db->prepare(
                "INSERT INTO products (site_id, item_name, image_name, price, description, assets, sample_image) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $result = $stmt->execute([
                $this->siteId,
                $this->itemName,
                $this->imageName,
                $this->price,
                $this->description,
                $assetsJson,
                $this->sampleImage
            ]);

            if ($result) {
                $this->id = (int) $db->lastInsertId();
            }

            return $result;
        } else {
            // Update existing product
            $stmt = $db->prepare(
                "UPDATE products SET site_id = ?, item_name = ?, image_name = ?, price = ?, 
                 description = ?, assets = ?, sample_image = ? WHERE id = ?"
            );
            return $stmt->execute([
                $this->siteId,
                $this->itemName,
                $this->imageName,
                $this->price,
                $this->description,
                $assetsJson,
                $this->sampleImage,
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
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$this->id]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'site_id' => $this->siteId,
            'item_name' => $this->itemName,
            'image_name' => $this->imageName,
            'price' => $this->price,
            'description' => $this->description,
            'assets' => $this->assets,
            'sample_image' => $this->sampleImage,
            'created_at' => $this->createdAt
        ];
    }

    private static function fromArray(array $data): self
    {
        $product = new self();
        $product->id = (int) $data['id'];
        $product->siteId = (int) $data['site_id'];
        $product->itemName = $data['item_name'];
        $product->imageName = $data['image_name'];
        $product->price = $data['price'] ? (float) $data['price'] : null;
        $product->description = $data['description'];
        $product->assets = $data['assets'] ? json_decode($data['assets'], true) : null;
        $product->sampleImage = $data['sample_image'];
        $product->createdAt = $data['created_at'];

        return $product;
    }
}

