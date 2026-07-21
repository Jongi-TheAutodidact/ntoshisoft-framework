<?php

declare(strict_types=1);

defined('ROOTPATH') or exit('Access Denied!');

class ImageHandler
{
    public static function upload(array $file, string $uploadDir, int $maxSize = 5, array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp']): array
    {
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Upload error: ' . $file['error']];
        }

        // Check file size (in MB)
        if ($file['size'] > ($maxSize * 1024 * 1024)) {
            return ['success' => false, 'error' => "File exceeds maximum size of {$maxSize}MB"];
        }

        // Check file type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type'];
        }

        // Create directory if not exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $destination = rtrim($uploadDir, '/') . '/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success' => true,
                'path' => $destination,
                'filename' => $filename
            ];
        }

        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }

    public static function createThumbnail(string $sourcePath, int $width = 300, int $height = 300): string|false
    {
        $info = getimagesize($sourcePath);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }

        $srcWidth = imagesx($image);
        $srcHeight = imagesy($image);

        // Calculate aspect ratio
        $srcRatio = $srcWidth / $srcHeight;
        $dstRatio = $width / $height;

        if ($srcRatio > $dstRatio) {
            // Source is wider
            $tmpHeight = $height;
            $tmpWidth = (int)($height * $srcRatio);
        } else {
            // Source is taller
            $tmpWidth = $width;
            $tmpHeight = (int)($width / $srcRatio);
        }

        // Create temp image
        $tmpImage = imagecreatetruecolor($tmpWidth, $tmpHeight);
        imagecopyresampled($tmpImage, $image, 0, 0, 0, 0, $tmpWidth, $tmpHeight, $srcWidth, $srcHeight);

        // Create final thumbnail
        $thumbnail = imagecreatetruecolor($width, $height);
        $x = (int)(($tmpWidth - $width) / 2);
        $y = (int)(($tmpHeight - $height) / 2);
        imagecopy($thumbnail, $tmpImage, 0, 0, $x, $y, $width, $height);

        // Save thumbnail
        $pathInfo = pathinfo($sourcePath);
        $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($thumbnail, $thumbnailPath, 90);
                break;
            case 'image/png':
                imagepng($thumbnail, $thumbnailPath, 9);
                break;
            case 'image/webp':
                imagewebp($thumbnail, $thumbnailPath, 90);
                break;
        }

        // Clean up
        imagedestroy($image);
        imagedestroy($tmpImage);
        imagedestroy($thumbnail);

        return $thumbnailPath;
    }

    public static function delete(string $filePath): bool
    {
        if (file_exists($filePath)) {
            // Also delete thumbnail if exists
            $pathInfo = pathinfo($filePath);
            $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
            
            if (file_exists($thumbnailPath)) {
                unlink($thumbnailPath);
            }
            
            return unlink($filePath);
        }
        return false;
    }
}