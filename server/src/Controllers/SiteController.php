<?php

namespace App\Controllers;

use App\Models\Site;
use App\Models\Product;
use App\Auth\AuthMiddleware;

class SiteController
{
    public static function index(): void
    {
        header('Content-Type: application/json');
        $user = AuthMiddleware::requireAuth();

        if ($user['role'] === 'admin') {
            $sites = Site::findAll();
        } else {
            $sites = Site::findByUserId($user['user_id']);
        }

        echo json_encode([
            'success' => true,
            'sites' => array_map(fn($site) => $site->toArray(), $sites)
        ]);
    }

    public static function show(int $id): void
    {
        header('Content-Type: application/json');
        $user = AuthMiddleware::requireAuth();

        $site = Site::findById($id);

        if (!$site) {
            http_response_code(404);
            echo json_encode(['error' => 'Site not found']);
            return;
        }

        // Check ownership or admin
        if ($user['role'] !== 'admin' && $site->ownerUserId !== $user['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $products = Product::findBySiteId($id);

        echo json_encode([
            'success' => true,
            'site' => $site->toArray(),
            'products' => array_map(fn($p) => $p->toArray(), $products)
        ]);
    }

    public static function create(): void
    {
        header('Content-Type: application/json');
        $user = AuthMiddleware::requireAuth();

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Site name is required']);
            return;
        }

        // Generate slug from name
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name']), '-'));
        
        // Check if slug already exists
        if (Site::findBySlug($slug)) {
            $slug .= '-' . time();
        }

        $site = new Site();
        $site->name = $data['name'];
        $site->slug = $slug;
        $site->ownerUserId = $user['user_id'];
        $site->active = true;

        if ($site->save()) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'site' => $site->toArray()
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create site']);
        }
    }

    public static function update(int $id): void
    {
        header('Content-Type: application/json');
        $user = AuthMiddleware::requireAuth();

        $site = Site::findById($id);

        if (!$site) {
            http_response_code(404);
            echo json_encode(['error' => 'Site not found']);
            return;
        }

        // Check ownership or admin
        if ($user['role'] !== 'admin' && $site->ownerUserId !== $user['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['name'])) {
            $site->name = $data['name'];
        }

        if (isset($data['active'])) {
            $site->active = (bool) $data['active'];
        }

        if ($site->save()) {
            echo json_encode([
                'success' => true,
                'site' => $site->toArray()
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update site']);
        }
    }

    public static function delete(int $id): void
    {
        header('Content-Type: application/json');
        $user = AuthMiddleware::requireAuth();

        $site = Site::findById($id);

        if (!$site) {
            http_response_code(404);
            echo json_encode(['error' => 'Site not found']);
            return;
        }

        // Check ownership or admin
        if ($user['role'] !== 'admin' && $site->ownerUserId !== $user['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        if ($site->delete()) {
            echo json_encode([
                'success' => true,
                'message' => 'Site deactivated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to deactivate site']);
        }
    }
}

