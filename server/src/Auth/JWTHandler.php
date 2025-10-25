<?php

namespace App\Auth;

use App\Config\App;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTHandler
{
    private static function getSecret(): string
    {
        return App::getJWTSecret();
    }

    private static function getAlgorithm(): string
    {
        return 'HS256';
    }

    public static function encode(array $payload): string
    {
        $issuedAt = time();
        $expiresAt = $issuedAt + App::getJWTExpiration();

        $tokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expiresAt,
            'iss' => App::getAppUrl()
        ]);

        return JWT::encode($tokenPayload, self::getSecret(), self::getAlgorithm());
    }

    public static function decode(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key(self::getSecret(), self::getAlgorithm()));
        } catch (Exception $e) {
            error_log("JWT decode error: " . $e->getMessage());
            return null;
        }
    }

    public static function validate(string $token): bool
    {
        $decoded = self::decode($token);
        return $decoded !== null;
    }

    public static function getUserIdFromToken(string $token): ?int
    {
        $decoded = self::decode($token);
        if ($decoded && isset($decoded->user_id)) {
            return (int) $decoded->user_id;
        }
        return null;
    }

    public static function getRoleFromToken(string $token): ?string
    {
        $decoded = self::decode($token);
        if ($decoded && isset($decoded->role)) {
            return $decoded->role;
        }
        return null;
    }
}

