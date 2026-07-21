<?php

// Check if the app needs installation
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile) || !preg_match('/^DB_NAME=.+/m', file_get_contents($envFile))) {
    header('Location: install.php');
    exit;
}

// Silence is platinum - security redirect
header('Location: public/');
exit;