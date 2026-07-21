<?php

declare(strict_types=1);

defined('ROOTPATH') or exit('Access Denied!');

/**
 * Environment Configuration Loader
 * 
 * Handles loading and parsing of .env files with proper security measures
 */
class EnvLoader
{
    private static $loaded = false;
    private static $envVars = [];

    /**
     * Load environment variables from .env file
     */
    public static function load(?string $path = null): void
    {
        if (self::$loaded) {
            return;
        }

        $envPath = $path ?? dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
        
        if (!file_exists($envPath)) {
            // In production, .env should exist, in development show helpful error
            if (self::isProduction()) {
                throw new RuntimeException('.env file is required in production');
            }
            
            // For development, show friendly message
            error_log('Warning: .env file not found. Copy .env.example to .env and configure your environment.');
            return;
        }

        self::parseEnvFile($envPath);
        self::$loaded = true;
    }

    /**
     * Parse .env file contents
     */
    private static function parseEnvFile(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments and empty lines
            if (empty(trim($line)) || str_starts_with(trim($line), '#')) {
                continue;
            }

            // Parse variable assignment
            if (str_contains($line, '=')) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove surrounding quotes if present
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }
                
                self::$envVars[$key] = $value;
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

    /**
     * Get environment variable with fallback
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? self::$envVars[$key] ?? $default;
    }

    /**
     * Check if application is in production environment
     */
    public static function isProduction(): bool
    {
        $env = self::get('APP_ENV', 'development');
        return $env === 'production';
    }

    /**
     * Check if debug mode is enabled
     */
    public static function isDebug(): bool
    {
        return filter_var(self::get('DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get required environment variable or throw exception
     */
    public static function require(string $key): string
    {
        $value = self::get($key);
        
        if ($value === null) {
            throw new RuntimeException("Required environment variable '$key' is not set");
        }
        
        return $value;
    }

    /**
     * Get boolean environment variable
     */
    public static function bool(string $key, bool $default = false): bool
    {
        return filter_var(self::get($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get integer environment variable
     */
    public static function int(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    /**
     * Validate that required environment variables are set
     */
    public static function validateRequired(array $required): array
    {
        $missing = [];
        
        foreach ($required as $key) {
            if (self::get($key) === null) {
                $missing[] = $key;
            }
        }
        
        if (!empty($missing)) {
            throw new RuntimeException(
                'Missing required environment variables: ' . implode(', ', $missing)
            );
        }
        
        return [];
    }
}