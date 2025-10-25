<?php

namespace App\Controllers;

use App\Models\User;
use App\Auth\JWTHandler;

class AuthController
{
    public static function login(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            return;
        }

        $user = User::findByUsername($data['username']);

        if (!$user || !$user->verifyPassword($data['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }

        $token = JWTHandler::encode([
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]);

        echo json_encode([
            'success' => true,
            'token' => $token,
            'user' => $user->toArray()
        ]);
    }

    public static function register(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username, email, and password are required']);
            return;
        }

        // Check if username or email already exists
        if (User::findByUsername($data['username'])) {
            http_response_code(409);
            echo json_encode(['error' => 'Username already exists']);
            return;
        }

        if (User::findByEmail($data['email'])) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already exists']);
            return;
        }

        $user = new User();
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->setPassword($data['password']);
        $user->role = $data['role'] ?? 'user';

        if ($user->save()) {
            $token = JWTHandler::encode([
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'token' => $token,
                'user' => $user->toArray()
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create user']);
        }
    }

    public static function refresh(): void
    {
        header('Content-Type: application/json');

        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            http_response_code(400);
            echo json_encode(['error' => 'Token not provided']);
            return;
        }

        $oldToken = $matches[1];
        $decoded = JWTHandler::decode($oldToken);

        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            return;
        }

        $newToken = JWTHandler::encode([
            'user_id' => $decoded->user_id,
            'email' => $decoded->email,
            'role' => $decoded->role
        ]);

        echo json_encode([
            'success' => true,
            'token' => $newToken
        ]);
    }
}

