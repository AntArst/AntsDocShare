<?php

namespace App\Services;

use App\Config\App;

class AssetManager
{
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_IMAGE_SIZE = 10 * 1024 * 1024; // 10MB

    public static function saveImage(string $tmpPath, int $siteId, string $originalName): ?string
    {
        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_IMAGE_TYPES)) {
            error_log("Invalid image type: $mimeType");
            return null;
        }

        // Validate file size
        if (filesize($tmpPath) > self::MAX_IMAGE_SIZE) {
            error_log("Image too large: " . filesize($tmpPath));
            return null;
        }

        // Create directory structure
        $targetDir = App::getStoragePath("assets/$siteId/images");
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        // Generate safe filename
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $fileName = $safeName . '_' . time() . '.' . $extension;
        $targetPath = $targetDir . '/' . $fileName;

        // Optimize and save image
        if (self::optimizeImage($tmpPath, $targetPath, $mimeType)) {
            return "assets/$siteId/images/$fileName";
        }

        return null;
    }

    private static function optimizeImage(string $sourcePath, string $targetPath, string $mimeType): bool
    {
        try {
            // Load image based on type
            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($sourcePath);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    return false;
            }

            if (!$image) {
                return false;
            }

            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Resize if too large (max 1920x1920)
            $maxDimension = 1920;
            if ($width > $maxDimension || $height > $maxDimension) {
                $ratio = min($maxDimension / $width, $maxDimension / $height);
                $newWidth = (int) ($width * $ratio);
                $newHeight = (int) ($height * $ratio);

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                
                // Preserve transparency for PNG and GIF
                if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                }

                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }

            // Save optimized image
            $result = false;
            switch ($mimeType) {
                case 'image/jpeg':
                    $result = imagejpeg($image, $targetPath, 85);
                    break;
                case 'image/png':
                    $result = imagepng($image, $targetPath, 8);
                    break;
                case 'image/gif':
                    $result = imagegif($image, $targetPath);
                    break;
                case 'image/webp':
                    $result = imagewebp($image, $targetPath, 85);
                    break;
            }

            imagedestroy($image);
            return $result;
        } catch (\Exception $e) {
            error_log("Image optimization failed: " . $e->getMessage());
            // Fallback: just copy the file
            return copy($sourcePath, $targetPath);
        }
    }

    public static function deleteAssets(int $siteId): bool
    {
        $targetDir = App::getStoragePath("assets/$siteId");
        
        if (!is_dir($targetDir)) {
            return true;
        }

        return self::deleteDirectory($targetDir);
    }

    private static function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $items = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                self::deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        return rmdir($dir);
    }

    public static function getAssetUrl(string $path): string
    {
        return App::getAppUrl() . '/storage/' . ltrim($path, '/');
    }

    public static function validateUpload(array $file): array
    {
        $errors = [];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload error: ' . $file['error'];
            return $errors;
        }

        if ($file['size'] > self::MAX_IMAGE_SIZE) {
            $errors[] = 'File too large. Maximum size is 10MB.';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_IMAGE_TYPES)) {
            $errors[] = 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.';
        }

        return $errors;
    }
}

