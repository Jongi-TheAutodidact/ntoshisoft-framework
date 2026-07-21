<?php
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = ltrim($path, '/');

// If the request is for the root, pass it through
if ($path === '') {
    return false;
}

// Serve existing files directly
if (file_exists(__DIR__ . '/' . rawurldecode($path))) {
    return false;
}

// Rewrite to pass URL as query parameter
$_GET['url'] = $path;
$_REQUEST['url'] = $path;
unset($_GET['url_old']);

chdir(__DIR__);
include __DIR__ . '/index.php';
