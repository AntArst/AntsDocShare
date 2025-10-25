<?php

namespace App\Config;

class App
{
    public static function getEnv(string $key, $default = null)
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }

    public static function getJWTSecret(): string
    {
        return self::getEnv('JWT_SECRET', 'default-secret-change-in-production');
    }

    public static function getJWTExpiration(): int
    {
        return (int) self::getEnv('JWT_EXPIRATION', 3600);
    }

    public static function getAppUrl(): string
    {
        return self::getEnv('APP_URL', 'http://localhost:8080');
    }

    public static function isProduction(): bool
    {
        return self::getEnv('APP_ENV', 'development') === 'production';
    }

    public static function getStoragePath(string $subPath = ''): string
    {
        $basePath = __DIR__ . '/../../storage';
        return $subPath ? $basePath . '/' . ltrim($subPath, '/') : $basePath;
    }

    public static function getViewPath(string $viewName): string
    {
        return __DIR__ . '/../../views/' . $viewName . '.php';
    }
}

