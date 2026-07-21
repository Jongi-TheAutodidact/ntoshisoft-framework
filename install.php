<?php

declare(strict_types=1);

/**
 * NtoshiSoft Web Framework - Installation Wizard
 * 
 * Standalone installer that works before the framework is configured.
 * Access via: http://your-domain.com/install.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath = __DIR__;
$publicPath = $basePath . '/public';

$step = $_GET['step'] ?? '1';

// If already installed and step is not "complete", redirect
$envFile = $basePath . '/.env';
$isInstalled = file_exists($envFile) && preg_match('/^DB_NAME=.+/m', file_get_contents($envFile));

if ($isInstalled && !isset($_SESSION['install_complete']) && $step !== 'done') {
    header('Location: public/');
    exit;
}

// Helper: detect root URL
function detectRootUrl(): string
{
    // Protocol detection with proxy/Cloudflare support
    $protocol = 'http://';
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $protocol = 'https://';
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $protocol = 'https://';
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
        $protocol = 'https://';
    } elseif (!empty($_SERVER['HTTP_CF_VISITOR'])) {
        $cfVisitor = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
        if (($cfVisitor['scheme'] ?? '') === 'https') {
            $protocol = 'https://';
        }
    }

    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $scriptDir = rtrim($scriptDir, '/');
    $projectRoot = $scriptDir;
    if (substr($projectRoot, -4) === '.php') {
        $projectRoot = dirname($projectRoot);
    }

    $baseUrl = $protocol . $host . $projectRoot;

    // Determine if /public suffix is needed:
    // Only add it if public/ exists as subdirectory AND no .htaccess rewrites to it
    $publicDir = __DIR__ . '/public';
    if (!is_dir($publicDir)) {
        return $baseUrl;
    }
    if (str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/public')) {
        return $baseUrl;
    }

    // Check for active .htaccess that rewrites to public/ (cPanel-style)
    // Only the active .htaccess determines URL routing; .htaccess-bkp is a backup and ignored.
    $htaccess = __DIR__ . '/.htaccess';
    if (file_exists($htaccess)) {
        $content = @file_get_contents($htaccess);
        if ($content && preg_match('/RewriteRule\s+(.*\s+)?public\/\s*\[L\]/i', $content)) {
            return $baseUrl;
        }
    }

    return $baseUrl . '/public';
}

// Helper: get DB config from session
function getDbConfig(): ?array
{
    return $_SESSION['install_db'] ?? null;
}

// Helper: connect to database
function dbConnect(): ?PDO
{
    $config = getDbConfig();
    if (!$config) return null;

    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset=utf8mb4";
        return new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        return null;
    }
}

// Handle form submissions
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === '2') {
        $host = trim($_POST['db_host'] ?? 'localhost');
        $port = trim($_POST['db_port'] ?? '3306');
        $name = trim($_POST['db_name'] ?? '');
        $user = trim($_POST['db_user'] ?? 'root');
        $pass = $_POST['db_pass'] ?? '';

        if (empty($name)) {
            $error = 'Database name is required.';
        } else {
            try {
                $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
                $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $_SESSION['install_db'] = ['host' => $host, 'port' => $port, 'name' => $name, 'user' => $user, 'pass' => $pass];
                $success = 'Database connection successful!';
            } catch (PDOException $e) {
                $error = 'Connection failed: ' . $e->getMessage();
            }
        }
    } elseif ($step === '3') {
        if (!getDbConfig()) {
            $error = 'Please configure your database first.';
        } else {
            $tables = [
                'users' => "CREATE TABLE IF NOT EXISTS `users` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `image` varchar(1024) NOT NULL DEFAULT '',
                    `user_id` varchar(1024) DEFAULT NULL,
                    `firstname` varchar(50) NOT NULL,
                    `surname` varchar(50) NOT NULL,
                    `gender` varchar(6) NOT NULL DEFAULT '',
                    `username` varchar(30) NOT NULL,
                    `email` varchar(100) NOT NULL,
                    `password` varchar(255) NOT NULL,
                    `user_role` varchar(50) NOT NULL DEFAULT 'User',
                    `phone` varchar(15) NOT NULL DEFAULT '',
                    `created` datetime NOT NULL DEFAULT current_timestamp(),
                    `reset_token_hash` varchar(34) DEFAULT NULL,
                    `reset_token_expires_at` datetime DEFAULT NULL,
                    `created_by` varchar(30) DEFAULT NULL,
                    `updated_by` varchar(30) DEFAULT NULL,
                    `date_updated` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `email` (`email`),
                    KEY `username` (`username`),
                    KEY `user_role` (`user_role`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

                'settings' => "CREATE TABLE IF NOT EXISTS `settings` (
                    `key` varchar(100) NOT NULL,
                    `value` text DEFAULT NULL,
                    PRIMARY KEY (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            ];

            try {
                $pdo = dbConnect();
                foreach ($tables as $table => $sql) {
                    $pdo->exec($sql);
                }

                $defaults = [
                    ['site_name', 'NtoshiSoft  Form'],
                    ['admin_email', ''],
                    ['email_notifications', '1'],
                    ['primary_color', '#d5ba0b'],
                    ['installed_version', '1.1.0'],
                ];
                $stmt = $pdo->prepare("INSERT IGNORE INTO `settings` (`key`, `value`) VALUES (?, ?)");
                foreach ($defaults as $s) {
                    $stmt->execute($s);
                }

                $_SESSION['install_db_ready'] = true;
                $success = 'All tables created successfully!';
            } catch (PDOException $e) {
                $error = 'Migration failed: ' . $e->getMessage();
            }
        }
    } elseif ($step === '4') {
        $firstname = trim($_POST['firstname'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $errors = [];
        if (empty($firstname)) $errors[] = 'First name is required';
        if (empty($surname)) $errors[] = 'Surname is required';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
        if (empty($username)) $errors[] = 'Username is required';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters';
        elseif ($password !== $confirm) $errors[] = 'Passwords do not match';

        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        } else {
            try {
                $pdo = dbConnect();
                $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
                $check->execute([$email, $username]);
                if ($check->fetch()) {
                    $error = 'A user with this email or username already exists.';
                } else {
                    $userId = rand(10001, 99099);
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $insert = $pdo->prepare("INSERT INTO users (image, user_id, firstname, surname, username, email, password, user_role, phone, created, created_by) VALUES ('', ?, ?, ?, ?, ?, ?, 'Admin', '', NOW(), 'Installation')");
                    $insert->execute([$userId, $firstname, $surname, $username, $email, $hashed]);
                    $pdo->prepare("UPDATE settings SET `value` = ? WHERE `key` = 'admin_email'")->execute([$email]);
                    $_SESSION['install_admin'] = ['firstname' => $firstname, 'surname' => $surname, 'email' => $email];
                    $success = 'Admin account created!';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    } elseif ($step === '5') {
        $siteName = trim($_POST['site_name'] ?? 'NtoshiSoft  Form');

        try {
            $pdo = dbConnect();
            $settings = [
                'site_name' => $siteName,
            ];
            $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
            foreach ($settings as $k => $v) {
                $stmt->execute([$k, $v]);
            }

            if (!empty($_FILES['logo']['tmp_name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES['logo']['tmp_name']);
                finfo_close($finfo);
                $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
                if (in_array($mime, $allowed)) {
                    $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                    $logoName = 'logo.' . $ext;
                    $logosDir = $publicPath . '/assets/img/logos/';
                    if (!is_dir($logosDir)) mkdir($logosDir, 0755, true);
                    move_uploaded_file($_FILES['logo']['tmp_name'], $logosDir . $logoName);
                    $_SESSION['install_logo'] = $logoName;
                }
            }

            $_SESSION['install_root_url'] = detectRootUrl();
            $_SESSION['install_site_name'] = $siteName;
            $_SESSION['install_settings_done'] = true;
            $success = 'Settings saved!';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    } elseif ($step === '6') {
        $config = getDbConfig();
        $rootUrl = $_SESSION['install_root_url'] ?? detectRootUrl();
        $siteName = $_SESSION['install_site_name'] ?? 'NtoshiSoft  Form';
        $host = parse_url($rootUrl, PHP_URL_HOST) ?: 'localhost';

        $mailHost = trim($_POST['mail_host'] ?? 'smtp.gmail.com');
        $mailUser = trim($_POST['mail_username'] ?? '');
        $mailPass = $_POST['mail_password'] ?? '';
        $mailPort = trim($_POST['mail_port'] ?? '465');
        $mailEnc = trim($_POST['mail_encryption'] ?? 'ssl');

        $envLines = [];
        $envLines[] = '# Database Configuration';
        $envLines[] = "DB_HOST={$config['host']}";
        $envLines[] = "DB_NAME={$config['name']}";
        $envLines[] = "DB_USER={$config['user']}";
        $envLines[] = "DB_PASS={$config['pass']}";
        $envLines[] = 'DB_DRIVER=mysql';
        $envLines[] = '';
        $envLines[] = '# Application Configuration';
        $envLines[] = "APP_NAME=\"{$siteName}\"";
        $envLines[] = 'APP_NAME_SHORT="NtoshiSoft "';
        $envLines[] = "APP_DOMAIN={$host}";
        $envLines[] = 'APP_TAG_LINE="Business management platform"';
        $envLines[] = 'DEFAULT_TIMEZONE="Africa/Johannesburg"';
        $envLines[] = "ROOT=\"{$rootUrl}\"";
        $envLines[] = '';
        $envLines[] = '# Mail Configuration (Password Reset & Notifications)';
        $envLines[] = "MAIL_HOST={$mailHost}";
        $envLines[] = "MAIL_USERNAME={$mailUser}";
        $envLines[] = "MAIL_PASSWORD={$mailPass}";
        $envLines[] = "MAIL_PORT={$mailPort}";
        $envLines[] = "MAIL_ENCRYPTION={$mailEnc}";
        $envLines[] = '';
        $envLines[] = '# Security Settings';
        $envLines[] = 'DEBUG=false';
        $envLines[] = 'APP_ENV=production';
        $envLines[] = 'SESSION_LIFETIME=120';
        $envLines[] = 'CSRF_TOKEN_LENGTH=32';
        $envLines[] = '';
        $envLines[] = '# Application Constants';
        $envLines[] = 'EST_YEAR=' . date('Y');
        $envLines[] = 'POLICY_ADOPT_DATE=' . date('Y-m-d');
        $envLines[] = 'DEF_CURR=R';
        $envLines[] = 'JONGI_CLI_VERS=1.0.0';
        $envLines[] = 'THEME_COLOR=primary';
        $envLines[] = 'VARIANT_COLOR=#d5ba0b';
        $envLines[] = '';
        $envLines[] = '# File Upload Settings';
        $envLines[] = 'MAX_FILE_SIZE=5242880';
        $envLines[] = 'ALLOWED_FILE_TYPES=jpg,jpeg,png,gif,pdf,webp,webm,mp3,wav,ogg';

        $envContent = implode("\n", $envLines);

        if (file_put_contents($envFile, $envContent, LOCK_EX) !== false) {
            $_SESSION['install_complete'] = true;
            header('Location: install.php?step=done');
            exit;
        } else {
            $error = 'Failed to write .env file. Please check file permissions.';
        }
    }
}

// Display step
$currentStep = (int)$step;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Wizard - NtoshiSoft </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #d5ba0b;
            --primary-dark: #b89f09;
            --bg-dark: #0a0a1a;
            --bg-card: rgba(255,255,255,0.05);
            --text: #eef5ff;
            --text-muted: #8899aa;
            --border: rgba(213,186,11,0.2);
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .install-container {
            max-width: 780px;
            width: 100%;
        }
        .install-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .install-header h1 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), #f5e642);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .install-header p { color: var(--text-muted); margin-top: 0.5rem; }
        .steps-indicator {
            display: flex;
            justify-content: center;
            gap: 0.25rem;
            margin-bottom: 2rem;
        }
        .step-dot {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 600;
            background: rgba(255,255,255,0.1);
            color: var(--text-muted);
            transition: all 0.3s;
        }
        .step-dot.active {
            background: var(--primary); color: #000;
            box-shadow: 0 0 15px rgba(213,186,11,0.4);
        }
        .step-dot.done {
            background: #28a745; color: #fff;
        }
        .step-line {
            width: 24px; height: 2px;
            background: rgba(255,255,255,0.1);
            align-self: center;
        }
        .step-line.done { background: #28a745; }
        .install-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 2rem;
            backdrop-filter: blur(10px);
        }
        .install-card h2 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }
        .form-label { font-weight: 500; font-size: 0.9rem; }
        .form-control, .form-select {
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(213,186,11,0.2);
            color: var(--text);
            border-radius: 0.5rem;
            padding: 0.6rem 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(213,186,11,0.15);
            background: rgba(0,0,0,0.4);
            color: var(--text);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none; color: #000; font-weight: 600;
            padding: 0.7rem 2rem; border-radius: 0.5rem;
            transition: all 0.3s;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 5px 20px rgba(213,186,11,0.3); }
        .btn-outline {
            background: transparent; border: 1px solid var(--border);
            color: var(--text); padding: 0.6rem 1.5rem; border-radius: 0.5rem;
            text-decoration: none; transition: all 0.3s;
        }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .alert { border-radius: 0.5rem; border: none; }
        .alert-danger { background: rgba(220,53,69,0.15); color: #f87171; border-left: 4px solid #dc3545; }
        .alert-success { background: rgba(40,167,69,0.15); color: #6fcf97; border-left: 4px solid #28a745; }
        .requirement-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.6rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .requirement-item:last-child { border-bottom: none; }
        .badge-check { color: #28a745; }
        .badge-times { color: #dc3545; }
        .logo-upload-area {
            border: 2px dashed var(--border); border-radius: 0.75rem;
            padding: 2rem; text-align: center; cursor: pointer;
            transition: all 0.3s; position: relative;
        }
        .logo-upload-area:hover { border-color: var(--primary); background: rgba(213,186,11,0.05); }
        .logo-upload-area img { max-height: 80px; margin-bottom: 0.5rem; }
        .logo-preview { max-height: 80px; margin-top: 0.5rem; border-radius: 0.5rem; }
        @media (max-width: 576px) {
            .install-card { padding: 1.25rem; }
            .steps-indicator { gap: 0.15rem; }
            .step-line { width: 12px; }
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1><i class="fas fa-cubes"></i> NtoshiSoft Framework </h1>
            <p>Installation Wizard</p>
        </div>

        <!-- Steps Indicator -->
        <div class="steps-indicator">
            <?php for ($i = 1; $i <= 6; $i++):
                $cls = $currentStep == $i ? 'active' : ($currentStep > $i ? 'done' : '');
                $icon = $currentStep > $i ? 'fa-check' : ($currentStep == $i ? 'fa-chevron-right' : '');
            ?>
                <div class="step-dot <?= $cls ?>">
                    <?php if ($icon): ?><i class="fas <?= $icon ?>" style="font-size:0.65rem"></i><?php else: ?><?= $i ?><?php endif; ?>
                </div>
                <?php if ($i < 6): ?><div class="step-line <?= $currentStep > $i ? 'done' : '' ?>"></div><?php endif; ?>
            <?php endfor; ?>
        </div>

        <!-- Step 1: Welcome + Requirements -->
        <?php if ($currentStep === 1): ?>
        <div class="install-card">
            <h2><i class="fas fa-rocket"></i> Welcome &amp; Requirements</h2>
            <p>Welcome to the NtoshiSoft  installation. Before we begin, let's check your server meets the requirements.</p>

            <?php
            $phpOk = version_compare(phpversion(), '7.4', '>=');
            $exts = ['pdo', 'pdo_mysql', 'gd', 'curl', 'fileinfo', 'mbstring', 'json'];
            $allOk = $phpOk;
            $extResults = [];
            foreach ($exts as $ext) {
                $loaded = extension_loaded($ext);
                $extResults[$ext] = $loaded;
                if (!$loaded) $allOk = false;
            }

            $writableDirs = [
                'Root directory' => is_writable($basePath),
                'Public directory' => is_writable($publicPath),
            ];
            foreach ($writableDirs as $name => $writable) {
                if (!$writable) $allOk = false;
            }
            ?>

            <div style="margin-top: 1rem;">
                <div class="requirement-item">
                    <span>PHP Version (>= 7.4)</span>
                    <span><strong><?= phpversion() ?></strong> <?= $phpOk ? '<i class="fas fa-check-circle badge-check"></i>' : '<i class="fas fa-times-circle badge-times"></i>' ?></span>
                </div>
                <?php foreach ($extResults as $ext => $loaded): ?>
                <div class="requirement-item">
                    <span><?= strtoupper($ext) ?> Extension</span>
                    <span><?= $loaded ? '<i class="fas fa-check-circle badge-check"></i>' : '<i class="fas fa-times-circle badge-times"></i>' ?></span>
                </div>
                <?php endforeach; ?>
                <?php foreach ($writableDirs as $name => $writable): ?>
                <div class="requirement-item">
                    <span><?= $name ?> (Writable)</span>
                    <span><?= $writable ? '<i class="fas fa-check-circle badge-check"></i>' : '<i class="fas fa-times-circle badge-times"></i>' ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($allOk): ?>
                <a href="install.php?step=2" class="btn btn-primary mt-3">
                    <i class="fas fa-arrow-right"></i> Start Installation
                </a>
            <?php else: ?>
                <div class="alert alert-danger mt-3">Some requirements are not met. Please fix the issues above and refresh.</div>
            <?php endif; ?>
        </div>

        <!-- Step 2: Database -->
        <?php elseif ($currentStep === 2): ?>
        <div class="install-card">
            <h2><i class="fas fa-database"></i> Database Configuration</h2>
            <p>Enter your MySQL database credentials.</p>

            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

            <form method="post">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Host</label>
                        <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Port</label>
                        <input type="text" name="db_port" class="form-control" value="<?= htmlspecialchars($_POST['db_port'] ?? '3306') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Driver</label>
                        <input type="text" class="form-control" value="mysql" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Database Name</label>
                        <input type="text" name="db_name" class="form-control" placeholder="e.g. voice_contact_db" value="<?= htmlspecialchars($_POST['db_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="db_user" class="form-control" value="<?= htmlspecialchars($_POST['db_user'] ?? 'root') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="db_pass" class="form-control" value="<?= htmlspecialchars($_POST['db_pass'] ?? '') ?>">
                    </div>
                </div>

                <?php if (empty($_SESSION['install_db'])): ?>
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-plug"></i> Test &amp; Save
                </button>
                <?php else: ?>
                <div class="alert alert-success mt-3"><i class="fas fa-check-circle"></i> Database configured: <strong><?= htmlspecialchars($_SESSION['install_db']['name']) ?></strong></div>
                <a href="install.php?step=3" class="btn btn-primary mt-3"><i class="fas fa-arrow-right"></i> Continue</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Step 3: Migrations -->
        <?php elseif ($currentStep === 3): ?>
        <div class="install-card">
            <h2><i class="fas fa-table"></i> Database Tables</h2>
            <p>Creating the required database tables...</p>

            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

            <?php if (isset($_SESSION['install_db_ready'])): ?>
                <div class="mt-3">
                    <p><i class="fas fa-check-circle badge-check"></i> The following tables have been created:</p>
                    <ul>
                        <li><code>users</code> — Admin and user accounts</li>
                        <li><code>settings</code> — Application configuration</li>
                    </ul>
                </div>
                <a href="install.php?step=4" class="btn btn-primary mt-3"><i class="fas fa-arrow-right"></i> Next: Admin Account</a>
            <?php else: ?>
                <form method="post">
                    <p>Click the button below to create all required tables.</p>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-play"></i> Create Tables</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Step 4: Admin Account -->
        <?php elseif ($currentStep === 4): ?>
        <div class="install-card">
            <h2><i class="fas fa-user-shield"></i> Admin Account</h2>
            <p>Create the administrator account to manage the application.</p>

            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

            <?php if (isset($_SESSION['install_admin'])): ?>
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle"></i> Admin <strong><?= htmlspecialchars($_SESSION['install_admin']['firstname'] . ' ' . $_SESSION['install_admin']['surname']) ?></strong> created.
                </div>
                <a href="install.php?step=5" class="btn btn-primary mt-3"><i class="fas fa-arrow-right"></i> Next: Site Settings</a>
            <?php else: ?>
            <form method="post">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="firstname" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Surname</label>
                        <input type="text" name="surname" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password (min 8 characters)</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="8">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-user-plus"></i> Create Admin</button>
            </form>
            <?php endif; ?>
        </div>

        <!-- Step 5: Site Settings -->
        <?php elseif ($currentStep === 5): ?>
        <div class="install-card">
            <h2><i class="fas fa-cog"></i> Site Settings</h2>
            <p>Configure your application name and upload your logo.</p>

            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

            <?php if (isset($_SESSION['install_settings_done'])): ?>
                <div class="alert alert-success mt-3"><i class="fas fa-check-circle"></i> Settings saved.</div>
                <a href="install.php?step=6" class="btn btn-primary mt-3"><i class="fas fa-arrow-right"></i> Next: Finalize</a>
            <?php else: ?>
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="NtoshiSoft  Form" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Logo (optional)</label>
                        <div class="logo-upload-area" onclick="document.getElementById('logoInput').click()">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 24 24' fill='none' stroke='%23d5ba0b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'/%3E%3Cpolyline points='14 2 14 8 20 8'/%3E%3Cline x1='16' y1='13' x2='8' y2='13'/%3E%3Cline x1='16' y1='17' x2='8' y2='17'/%3E%3C/svg%3E" alt="Upload" style="opacity:0.5;">
                            <p style="margin:0;color:var(--text-muted);font-size:0.9rem;">Click to upload logo (PNG, JPG, SVG)</p>
                            <input type="file" name="logo" id="logoInput" accept="image/png,image/jpeg,image/webp,image/svg+xml" style="display:none;" onchange="previewLogo(this)">
                        </div>
                        <img id="logoPreview" class="logo-preview" style="display:none;">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Save Settings</button>
            </form>
            <script>
            function previewLogo(input) {
                const preview = document.getElementById('logoPreview');
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) { preview.src = e.target.result; preview.style.display = 'block'; };
                    reader.readAsDataURL(input.files[0]);
                }
            }
            </script>
            <?php endif; ?>
        </div>

        <!-- Step 6: Finalize -->
        <?php elseif ($currentStep === 6): ?>
        <div class="install-card">
            <h2><i class="fas fa-envelope"></i> Mail Configuration (Optional)</h2>
            <p>Configure email settings for notifications. You can skip this and configure later.</p>

            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

            <form method="post">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Mail Host</label>
                        <input type="text" name="mail_host" class="form-control" value="smtp.gmail.com">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Port</label>
                        <input type="text" name="mail_port" class="form-control" value="465">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Encryption</label>
                        <select name="mail_encryption" class="form-select">
                            <option value="ssl">SSL</option>
                            <option value="tls">TLS</option>
                            <option value="">None</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Username</label>
                        <input type="email" name="mail_username" class="form-control" placeholder="your@email.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Password</label>
                        <input type="password" name="mail_password" class="form-control" placeholder="App password">
                    </div>
                </div>
                <p class="text-muted mt-2" style="font-size:0.85rem;">
                    <i class="fas fa-info-circle"></i> For Gmail, use an <strong>App Password</strong> (not your regular password).
                </p>
                <button type="submit" class="btn btn-primary mt-2">
                    <i class="fas fa-check"></i> Complete Installation
                </button>
                <a href="install.php?step=done" class="btn btn-outline mt-2 ms-2">
                    Skip for now
                </a>
            </form>
        </div>

        <!-- Done -->
        <?php elseif ($currentStep === 7 || $step === 'done'): ?>
        <div class="install-card text-center">
            <div style="font-size: 4rem; margin-bottom: 1rem;">
                <i class="fas fa-check-circle" style="color: #28a745;"></i>
            </div>
            <h2>Installation Complete!</h2>
            <p>Your NtoshiSoft  application is ready to use.</p>
            <hr style="border-color: var(--border);">
            <div class="text-start" style="font-size: 0.9rem;">
                <p><i class="fas fa-check-circle badge-check"></i> Database configured and tables created</p>
                <p><i class="fas fa-check-circle badge-check"></i> Admin account created</p>
                <p><i class="fas fa-check-circle badge-check"></i> Site settings configured</p>
                <p><i class="fas fa-check-circle badge-check"></i> .env file written</p>
            </div>
            <a href="<?= detectRootUrl() ?>" class="btn btn-primary mt-3" style="font-size:1.1rem;padding:0.8rem 3rem;">
                <i class="fas fa-arrow-right"></i> Go to Application
            </a>
            <p class="text-muted mt-2" style="font-size:0.85rem;">
                Log in with the admin credentials you created.
            </p>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Clean sensitive data if done
if ($step === 'done') {
    $_SESSION['install_complete'] = true;
}
