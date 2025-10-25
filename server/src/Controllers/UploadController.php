<?php

namespace App\Controllers;

use App\Models\Site;
use App\Models\Product;
use App\Auth\AuthMiddleware;
use App\Services\AssetManager;
use App\Config\Database;

class UploadController
{
    public static function upload(): void
    {
        header('Content-Type: application/json');
        $user = AuthMiddleware::requireAuth();

        // Get site_id from POST or query parameter
        $siteId = $_POST['site_id'] ?? $_GET['site_id'] ?? null;

        if (!$siteId) {
            http_response_code(400);
            echo json_encode(['error' => 'site_id is required']);
            return;
        }

        $site = Site::findById((int) $siteId);

        if (!$site) {
            http_response_code(404);
            echo json_encode(['error' => 'Site not found']);
            return;
        }

        // Check ownership
        if ($user['role'] !== 'admin' && $site->ownerUserId !== $user['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        // Handle CSV file
        if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'CSV file is required']);
            return;
        }

        $csvContent = file_get_contents($_FILES['csv']['tmp_name']);
        $products = self::parseCSV($csvContent);

        if (empty($products)) {
            http_response_code(400);
            echo json_encode(['error' => 'No valid products found in CSV']);
            return;
        }

        // Handle images
        $uploadedImages = [];
        if (isset($_FILES['images'])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $originalName = $_FILES['images']['name'][$key];
                    $savedPath = AssetManager::saveImage($tmpName, $siteId, $originalName);
                    if ($savedPath) {
                        $uploadedImages[$originalName] = $savedPath;
                    }
                }
            }
        }

        // Save products to database
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // Clear existing products for this site
            Product::deleteBySiteId($siteId);

            $savedProducts = [];
            foreach ($products as $productData) {
                $product = new Product();
                $product->siteId = $siteId;
                $product->itemName = $productData['item_name'];
                
                // Use the actual saved image filename if available
                $csvImageName = $productData['image_name'] ?? null;
                if ($csvImageName && isset($uploadedImages[$csvImageName])) {
                    // Extract just the filename from the path (e.g., "assets/1/images/file.jpg" -> "file.jpg")
                    $product->imageName = basename($uploadedImages[$csvImageName]);
                } else {
                    $product->imageName = $csvImageName;
                }
                
                $product->price = isset($productData['price']) ? (float) $productData['price'] : null;
                $product->description = $productData['description'] ?? null;
                $product->assets = isset($productData['assets']) ? json_decode($productData['assets'], true) : null;
                $product->sampleImage = $productData['sample_image'] ?? null;

                if ($product->save()) {
                    $savedProducts[] = $product->toArray();
                }
            }

            // Create upload record
            $stmt = $db->prepare(
                "INSERT INTO uploads (site_id, user_id, status) VALUES (?, ?, 'completed')"
            );
            $stmt->execute([$siteId, $user['user_id']]);
            $uploadId = $db->lastInsertId();

            $db->commit();

            echo json_encode([
                'success' => true,
                'upload_id' => $uploadId,
                'products_count' => count($savedProducts),
                'images_uploaded' => count($uploadedImages),
                'products' => $savedProducts
            ]);
        } catch (\Exception $e) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save products: ' . $e->getMessage()]);
        }
    }

    private static function parseCSV(string $content): array
    {
        $lines = array_map('str_getcsv', explode("\n", trim($content)));
        $header = array_shift($lines);
        
        $products = [];
        foreach ($lines as $line) {
            if (count($line) === count($header)) {
                $products[] = array_combine($header, $line);
            }
        }

        return $products;
    }

    public static function getLatestPackage(int $siteId): void
    {
        header('Content-Type: application/json');
        $user = AuthMiddleware::requireAuth();

        $site = Site::findById($siteId);

        if (!$site) {
            http_response_code(404);
            echo json_encode(['error' => 'Site not found']);
            return;
        }

        // Check ownership
        if ($user['role'] !== 'admin' && $site->ownerUserId !== $user['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT * FROM generated_packages WHERE site_id = ? ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->execute([$siteId]);
        $package = $stmt->fetch();

        if (!$package) {
            http_response_code(404);
            echo json_encode(['error' => 'No package found for this site']);
            return;
        }

        echo json_encode([
            'success' => true,
            'package' => $package
        ]);
    }

    public static function getPackage(int $uploadId): void
    {
        header('Content-Type: application/json');
        $user = AuthMiddleware::requireAuth();

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM generated_packages WHERE upload_id = ? LIMIT 1");
        $stmt->execute([$uploadId]);
        $package = $stmt->fetch();

        if (!$package) {
            http_response_code(404);
            echo json_encode(['error' => 'Package not found']);
            return;
        }

        // Check ownership
        $site = Site::findById($package['site_id']);
        if ($user['role'] !== 'admin' && $site->ownerUserId !== $user['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        echo json_encode([
            'success' => true,
            'package' => $package
        ]);
    }
}

