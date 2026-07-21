<?php

declare(strict_types=1);

defined('ROOTPATH') or exit('Access Denied!');

class EnvWriter
{
    public static function write(array $config, string $filePath): bool
    {
        $envPath = $filePath ?: ROOTPATH . '../.env';
        $content = '';

        $content .= "# Database Configuration\n";
        $content .= "DB_HOST=" . ($config['DB_HOST'] ?? 'localhost') . "\n";
        $content .= "DB_NAME=" . ($config['DB_NAME'] ?? '') . "\n";
        $content .= "DB_USER=" . ($config['DB_USER'] ?? 'root') . "\n";
        $content .= "DB_PASS=" . ($config['DB_PASS'] ?? '') . "\n";
        $content .= "DB_DRIVER=" . ($config['DB_DRIVER'] ?? 'mysql') . "\n\n";

        $content .= "# Application Configuration\n";
        $content .= "APP_NAME=\"" . ($config['APP_NAME'] ?? 'NtoshiSoft  Form') . "\"\n";
        $content .= "APP_NAME_SHORT=\"" . ($config['APP_NAME_SHORT'] ?? 'NtoshiSoft ') . "\"\n";
        $content .= "APP_DOMAIN=" . ($config['APP_DOMAIN'] ?? 'localhost') . "\n";
        $content .= "APP_TAG_LINE=\"" . ($config['APP_TAG_LINE'] ?? 'Business management platform') . "\"\n";
        $content .= "DEFAULT_TIMEZONE=\"" . ($config['DEFAULT_TIMEZONE'] ?? 'Africa/Johannesburg') . "\"\n";
        $root = $config['ROOT'] ?? '';
        $content .= "ROOT=\"{$root}\"\n";

        $content .= "# Mail Configuration (Password Reset & Notifications)\n";
        $content .= "MAIL_HOST=" . ($config['MAIL_HOST'] ?? 'smtp.gmail.com') . "\n";
        $content .= "MAIL_USERNAME=" . ($config['MAIL_USERNAME'] ?? '') . "\n";
        $content .= "MAIL_PASSWORD=" . ($config['MAIL_PASSWORD'] ?? '') . "\n";
        $content .= "MAIL_PORT=" . ($config['MAIL_PORT'] ?? '465') . "\n";
        $content .= "MAIL_ENCRYPTION=" . ($config['MAIL_ENCRYPTION'] ?? 'ssl') . "\n\n";

        $content .= "# Security Settings\n";
        $content .= "DEBUG=" . ($config['DEBUG'] ?? 'false') . "\n";
        $content .= "APP_ENV=" . ($config['APP_ENV'] ?? 'production') . "\n";
        $content .= "SESSION_LIFETIME=" . ($config['SESSION_LIFETIME'] ?? '120') . "\n";
        $content .= "CSRF_TOKEN_LENGTH=" . ($config['CSRF_TOKEN_LENGTH'] ?? '32') . "\n\n";

        $content .= "# Application Constants\n";
        $content .= "EST_YEAR=" . ($config['EST_YEAR'] ?? date('Y')) . "\n";
        $content .= "POLICY_ADOPT_DATE=" . ($config['POLICY_ADOPT_DATE'] ?? date('Y-m-d')) . "\n";
        $content .= "DEF_CURR=" . ($config['DEF_CURR'] ?? 'R') . "\n";
        $content .= "JONGI_CLI_VERS=" . ($config['JONGI_CLI_VERS'] ?? '1.0.0') . "\n";
        $content .= "THEME_COLOR=" . ($config['THEME_COLOR'] ?? 'primary') . "\n";
        $content .= "VARIANT_COLOR=" . ($config['VARIANT_COLOR'] ?? '#d5ba0b') . "\n\n";

        $content .= "# File Upload Settings\n";
        $content .= "MAX_FILE_SIZE=" . ($config['MAX_FILE_SIZE'] ?? '5242880') . "\n";
        $content .= "ALLOWED_FILE_TYPES=" . ($config['ALLOWED_FILE_TYPES'] ?? 'jpg,jpeg,png,gif,pdf,webp') . "\n";

        $result = file_put_contents($envPath, $content, LOCK_EX);
        return $result !== false;
    }

    public static function isInstalled(): bool
    {
        $envPath = ROOTPATH . '../.env';
        if (!file_exists($envPath)) {
            return false;
        }

        $content = file_get_contents($envPath);
        return preg_match('/^DB_NAME=.+/m', $content) === 1;
    }
}
