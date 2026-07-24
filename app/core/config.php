<?php
defined('ROOTPATH') or exit('Access Denied!');

// Load environment variables first
require_once __DIR__ . '/EnvLoader.php';
EnvLoader::load();

// Validate required environment variables
$requiredEnvVars = [
    'DB_HOST',
    'DB_NAME', 
    'DB_USER',
    'APP_NAME',
    'APP_NAME_SHORT',
    'ROOT'    
];

try {
    EnvLoader::validateRequired($requiredEnvVars);
} catch (RuntimeException $e) {
    // In development, show helpful error message
    if (!EnvLoader::isProduction()) {
        die('<h3>Environment Configuration Error</h3><p>' . $e->getMessage() . 
            '</p><p>Please copy <code>.env.example</code> to <code>.env</code> and configure your environment.</p>');
    } else {
        // In production, log and show generic error
        error_log('Environment configuration error: ' . $e->getMessage());
        die('Application configuration error. Please contact administrator.');
    }
}

// Database Configuration
define('DBNAME', EnvLoader::require('DB_NAME'));
define('DBHOST', EnvLoader::require('DB_HOST'));
define('DBUSER', EnvLoader::require('DB_USER'));
define('DBPASS', EnvLoader::get('DB_PASS', ''));
define('DBDRIVER', EnvLoader::get('DB_DRIVER', 'mysql'));

// Application Configuration
define('APP_NAME', EnvLoader::require('APP_NAME'));
define('APP_NAME_SHORT', EnvLoader::require('APP_NAME_SHORT'));
define('APP_DOMAIN', EnvLoader::get('APP_DOMAIN', 'localhost'));
define('APP_TAG_LINE', EnvLoader::get('APP_TAG_LINE', 'Professional application platform'));
define('ROOT', EnvLoader::require('ROOT'));
define('FAVICON', 'favicon.ico');
define('DEFAULT_TIMEZONE', EnvLoader::get('DEFAULT_TIMEZONE', 'Africa/Johannesburg'));
define('DEF_CURR', EnvLoader::get('DEF_CURR', 'R'));
define('EST_YEAR', EnvLoader::get('EST_YEAR', date('Y')));
define('POLICY_ADOPT_DATE', EnvLoader::get('POLICY_ADOPT_DATE', '2025-01-01'));
define('JONGI_CLI_VERS', EnvLoader::get('JONGI_CLI_VERS', '1.0.0'));
define('LOGO_IMAGE_ALT', EnvLoader::get('LOGO_IMAGE_ALT', 'NtoshiSoft Framework Logo'));

// Security Configuration
define('DEBUG', EnvLoader::bool('DEBUG', false));
define('APP_ENV', EnvLoader::get('APP_ENV', 'production'));
define('SESSION_LIFETIME', EnvLoader::int('SESSION_LIFETIME', 120));
define('CSRF_TOKEN_LENGTH', EnvLoader::int('CSRF_TOKEN_LENGTH', 32));

// Mail Configuration
define('MAIL_HOST', EnvLoader::get('MAIL_HOST', 'smtp.gmail.com'));
define('MAIL_USERNAME', EnvLoader::get('MAIL_USERNAME', ''));
define('MAIL_PASSWORD', EnvLoader::get('MAIL_PASSWORD', ''));
define('MAIL_PORT', EnvLoader::get('MAIL_PORT', '587'));
define('MAIL_ENCRYPTION', EnvLoader::get('MAIL_ENCRYPTION', 'tls'));

// Payment Gateways
define('PF_MERCHANT_KEY', '');
define('PF_MERCHANT_ID','');

// Legacy constants for backward compatibility
define('USERNAME', MAIL_USERNAME);
define('PWD', MAIL_PASSWORD);
define('PORT', MAIL_PORT);

// Application Colors
define('THEME_COLOR', EnvLoader::get('THEME_COLOR', 'primary'));
define('VARIANT_COLOR', EnvLoader::get('VARIANT_COLOR', '#007bff'));

// File Upload Configuration
define('MAX_FILE_SIZE', EnvLoader::int('MAX_FILE_SIZE', 5242880)); // 5MB
define('ALLOWED_FILE_TYPES', explode(',', EnvLoader::get('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,gif,pdf')));


define('USER_ROLES', array_map('ucwords', array_map('strtolower', [
    'Sys Admin',
    'Admin', 
    'Editor',
    'Subscriber',
    'Client',
    'User',
    'Employee',
])));

define('STAFF_CHAT', array_map('ucwords', array_map('strtolower', [
    'Sys Admin',
    'Admin', 
    'Editor',
    'User',
    'Employee',
])));

define('TECH_TEAM', array_map('ucwords', array_map('strtolower', [
    'Sys Admin',
    'Admin', 
])));

define('PROVINCES', array_map('ucwords', array_map('strtolower', [
    'Eastern Cape',
    'KwaZulu Natal', 
    'Northern Cape',
    'Western Cape',
    'Free State',
    'North West',
    'Mpumalanga',
    'Limpopo',
    'Gauteng'
])));

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);