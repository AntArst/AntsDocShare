<?php

namespace App\Api;

use App\Controllers\AuthController;
use App\Controllers\SiteController;
use App\Controllers\UploadController;
use App\Services\TemplateGenerator;

class Router
{
    private string $method;
    private string $path;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function route(): void
    {
        // Remove trailing slash
        $this->path = rtrim($this->path, '/');

        // API routes
        if (str_starts_with($this->path, '/api')) {
            $this->handleApiRoutes();
            return;
        }

        // Web routes
        $this->handleWebRoutes();
    }

    private function handleApiRoutes(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Handle preflight requests
        if ($this->method === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        // Auth endpoints
        if ($this->path === '/api/auth/login' && $this->method === 'POST') {
            AuthController::login();
            return;
        }

        if ($this->path === '/api/auth/register' && $this->method === 'POST') {
            AuthController::register();
            return;
        }

        if ($this->path === '/api/auth/refresh' && $this->method === 'POST') {
            AuthController::refresh();
            return;
        }

        // Template endpoint
        if ($this->path === '/api/template/csv' && $this->method === 'GET') {
            TemplateGenerator::downloadCSV();
            return;
        }

        // Sites endpoints
        if ($this->path === '/api/sites' && $this->method === 'GET') {
            SiteController::index();
            return;
        }

        if ($this->path === '/api/sites' && $this->method === 'POST') {
            SiteController::create();
            return;
        }

        if (preg_match('#^/api/sites/(\d+)$#', $this->path, $matches)) {
            $id = (int) $matches[1];

            if ($this->method === 'GET') {
                SiteController::show($id);
                return;
            }

            if ($this->method === 'PUT') {
                SiteController::update($id);
                return;
            }

            if ($this->method === 'DELETE') {
                SiteController::delete($id);
                return;
            }
        }

        // Upload endpoint
        if ($this->path === '/api/upload' && $this->method === 'POST') {
            UploadController::upload();
            return;
        }

        // Package endpoints
        if (preg_match('#^/api/packages/(\d+)/latest$#', $this->path, $matches)) {
            if ($this->method === 'GET') {
                UploadController::getLatestPackage((int) $matches[1]);
                return;
            }
        }

        if (preg_match('#^/api/packages/(\d+)$#', $this->path, $matches)) {
            if ($this->method === 'GET') {
                UploadController::getPackage((int) $matches[1]);
                return;
            }
        }

        // 404 for unmatched API routes
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Endpoint not found']);
    }

    private function handleWebRoutes(): void
    {
        // This will be expanded with web console routes
        // For now, just show a simple message
        if ($this->path === '/' || $this->path === '') {
            require_once __DIR__ . '/../../views/dashboard.php';
            return;
        }

        if ($this->path === '/login') {
            require_once __DIR__ . '/../../views/login.php';
            return;
        }

        if ($this->path === '/sites/add') {
            require_once __DIR__ . '/../../views/site-add.php';
            return;
        }

        if (preg_match('#^/sites/(\d+)$#', $this->path, $matches)) {
            $_GET['site_id'] = $matches[1];
            require_once __DIR__ . '/../../views/site-detail.php';
            return;
        }

        // 404 for unmatched web routes
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
    }
}

