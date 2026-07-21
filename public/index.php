<?php 

// Start the session so we can remember if we've already logged this visitor
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**  Path to this file **/
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Check if app is installed - redirect to setup if not
$envFile = dirname(__DIR__) . '/.env';
if (!file_exists($envFile) || !preg_match('/^DB_NAME=.+/m', file_get_contents($envFile))) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (!str_contains($requestUri, 'install')) {
        header('Location: ../install.php');
        exit;
    }
}

/**  Valid PHP Version? **/
$minPHPVersion = '7.4';
if (phpversion() < $minPHPVersion)
{
    die("Your PHP version must be {$minPHPVersion} or higher to run this app. Your current version is " . phpversion());
}

/** Load environment and configuration **/
require "../app/core/init.php";

/** Set error reporting based on environment and debug mode **/
if (EnvLoader::isProduction()) {
    // Production: Hide all errors
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOTPATH . 'logs' . DIRECTORY_SEPARATOR . 'php_errors.log');
} else {
    // Development: Show all errors
    error_reporting(E_ALL);
    ini_set('display_errors', DEBUG ? 1 : 0);
}

/** Set Timezone **/
date_default_timezone_set(DEFAULT_TIMEZONE);

/** Handle 404 and routing errors with try-catch **/
try {
    $routes = require '../app/config/routes.php';
    $router = new Router($routes);
    $url = trim($_GET['url'] ?? '', '/');
    $router->dispatch($url);
} catch (Exception $e) {
    error_log('Router error: ' . $e->getMessage());
    
    if (EnvLoader::isProduction()) {
        // Show generic error page in production
        http_response_code(500);
        include '../app/views/errors/server_error.ntoshi.php';
    } else {
        // Show detailed error in development
        if (DEBUG) {
            echo '<div style="background: #f8d7da; color: #721c24; padding: 20px; margin: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">';
            echo '<h3>Application Error</h3>';
            echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
            echo '<p><strong>Line:</strong> ' . $e->getLine() . '</p>';
            echo '</div>';
        }
    }
}

/** Visitor tracking - only in development or when enabled **/
if (!EnvLoader::isProduction() || EnvLoader::bool('ENABLE_VISITOR_TRACKING', false)) {
    // Only log a visit if we haven't already during this session
    if (empty($_SESSION['visit_logged'])) {
        try {
            $visitor = new Visitor();
            $onlineUser = new OnlineUser();

            // Get device, referrer, location
            $device = get_device_info();
            $referrer = $_SERVER['HTTP_REFERER'] ?? 'Direct';
            $location = get_location_from_ip(get_ip());

            // Log visit
            $visitor->logVisit([
                'ip_address' => get_ip(),
                'user_agent' => $device['user_agent'],
                'referrer' => $referrer,
                'visited_to' => $_SERVER['REQUEST_URI'],
                'device' => $device['device'],
                'country' => $location['country'],
                'city' => $location['city'],
                'visited_from' => $referrer,
            ]);

            // Mark that we've logged so we don't do it again in this session
            $_SESSION['visit_logged'] = true;
        } catch (Exception $e) {
            // Silently fail visitor tracking
            error_log('Visitor tracking failed: ' . $e->getMessage());
        }
    }

    // Track online users
    try {
        $onlineUser = new OnlineUser();
        $onlineUser->trackVisitor();
    } catch (Exception $e) {
        // Silently fail online tracking
        error_log('Online user tracking failed: ' . $e->getMessage());
    }
}

/** Clean up session garbage collection **/
if (rand(1, 100) === 1) { // 1% chance to run cleanup
    $session_lifetime = SESSION_LIFETIME * 60;
    $session_path = session_save_path();
    if (is_dir($session_path)) {
        foreach (glob($session_path . 'sess_*') as $file) {
            if (filemtime($file) + $session_lifetime < time()) {
                @unlink($file);
            }
        }
    }
}