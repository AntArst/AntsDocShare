<?php

namespace App\Auth;

class AuthMiddleware
{
    public static function authenticate(): ?array
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader) {
            return null;
        }

        // Extract token from "Bearer <token>"
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            return null;
        }

        $decoded = JWTHandler::decode($token);
        if (!$decoded) {
            return null;
        }

        return [
            'user_id' => $decoded->user_id ?? null,
            'email' => $decoded->email ?? null,
            'role' => $decoded->role ?? 'user'
        ];
    }

    public static function requireAuth(): array
    {
        $user = self::authenticate();
        
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        return $user;
    }

    public static function requireAdmin(): array
    {
        $user = self::requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden - Admin access required']);
            exit;
        }

        return $user;
    }

    public static function getAuthenticatedUser(): ?array
    {
        return self::authenticate();
    }
}

