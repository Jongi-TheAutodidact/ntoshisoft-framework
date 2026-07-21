<?php
defined('ROOTPATH') or exit('Access Denied!');

class InstallController
{
    use Controller;

    public function index(): void
    {
        $envPath = ROOTPATH . '../.env';
        $envExists = file_exists($envPath);

        $data['page_title'] = 'System Installation';
        $data['env_exists'] = $envExists;
        $data['php_version'] = phpversion();
        $data['php_ok'] = version_compare(phpversion(), '7.4', '>=');

        $extensions = ['pdo', 'pdo_mysql', 'gd', 'curl', 'fileinfo', 'mbstring', 'json'];
        $data['extensions'] = [];
        foreach ($extensions as $ext) {
            $data['extensions'][$ext] = extension_loaded($ext);
        }

        $data['writable_dirs'] = [
            '../.env' => is_writable(ROOTPATH . '..') || !file_exists($envPath),
            '../public/uploads' => is_writable(ROOTPATH . 'uploads') || is_writable(ROOTPATH),
            '../app/private' => is_writable(ROOTPATH . '../app/private'),
        ];

        $this->view('install/index', $data);
    }

    public function requirements(): void
    {
        $this->index();
    }

    public function database(): void
    {
        $data['page_title'] = 'Database Configuration';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $host = $_POST['db_host'] ?? 'localhost';
            $port = $_POST['db_port'] ?? '3306';
            $name = $_POST['db_name'] ?? '';
            $user = $_POST['db_user'] ?? 'root';
            $pass = $_POST['db_pass'] ?? '';

            if (empty($name)) {
                $data['error'] = 'Database name is required';
            } else {
                try {
                    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
                    $pdo = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);

                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $data['db_created'] = true;
                    $data['db_host'] = $host;
                    $data['db_port'] = $port;
                    $data['db_name'] = $name;
                    $data['db_user'] = $user;
                    $data['db_pass'] = $pass;

                    $_SESSION['install_db'] = [
                        'host' => $host,
                        'port' => $port,
                        'name' => $name,
                        'user' => $user,
                        'pass' => $pass,
                    ];
                } catch (PDOException $e) {
                    $data['error'] = 'Connection failed: ' . $e->getMessage();
                }
            }
        }

        $this->view('install/database', $data);
    }

    public function run_migrations(): void
    {
        $data['page_title'] = 'Installing Database Tables';

        $dbConfig = $_SESSION['install_db'] ?? null;
        if (!$dbConfig) {
            redirect('install/database');
            return;
        }

        $results = [];

        try {
            $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $queries = [
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
                    UNIQUE KEY `reset_token_hash` (`reset_token_hash`),
                    KEY `username` (`username`),
                    KEY `user_role` (`user_role`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

                'settings' => "CREATE TABLE IF NOT EXISTS `settings` (
                    `key` varchar(100) NOT NULL,
                    `value` text DEFAULT NULL,
                    PRIMARY KEY (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            ];

            foreach ($queries as $table => $sql) {
                $pdo->exec($sql);
                $results[] = ['table' => $table, 'success' => true];
            }

            $defaultSettings = [
                ['site_name', 'NtoshiSoft  Form'],
                ['admin_email', ''],
                ['email_notifications', '1'],
                ['primary_color', '#d5ba0b'],
                ['installed_version', '1.1.0'],
            ];
            $insertStmt = $pdo->prepare("INSERT IGNORE INTO `settings` (`key`, `value`) VALUES (?, ?)");
            foreach ($defaultSettings as $setting) {
                $insertStmt->execute($setting);
            }
            $results[] = ['table' => 'settings (data)', 'success' => true];

            $_SESSION['install_db_ready'] = true;
            $data['success'] = true;
        } catch (PDOException $e) {
            $data['error'] = 'Migration failed: ' . $e->getMessage();
        }

        $data['results'] = $results;
        $this->view('install/migrations', $data);
    }

    public function admin(): void
    {
        $data['page_title'] = 'Create Admin Account';

        if (!isset($_SESSION['install_db_ready'])) {
            redirect('install/database');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstname = trim($_POST['firstname'] ?? '');
            $surname = trim($_POST['surname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            $errors = [];
            if (empty($firstname)) $errors[] = 'First name is required';
            if (empty($surname)) $errors[] = 'Surname is required';
            if (empty($email)) $errors[] = 'Email is required';
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address';
            if (empty($username)) $errors[] = 'Username is required';
            if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters';
            elseif ($password !== $confirm) $errors[] = 'Passwords do not match';

            if (empty($errors)) {
                $dbConfig = $_SESSION['install_db'];
                try {
                    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']};charset=utf8mb4";
                    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);

                    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
                    $check->execute([$email, $username]);
                    if ($check->fetch()) {
                        $data['error'] = 'A user with this email or username already exists';
                    } else {
                        $userId = rand(10001, 99099);
                        $hashed = password_hash($password, PASSWORD_DEFAULT);
                        $insert = $pdo->prepare("INSERT INTO users (image, user_id, firstname, surname, gender, username, email, password, user_role, phone, created, created_by) VALUES (?, ?, ?, ?, '', ?, ?, ?, 'Admin', '', NOW(), 'Installation')");
                        $insert->execute(['', $userId, $firstname, $surname, $username, $email, $hashed]);

                        $_SESSION['install_admin'] = [
                            'firstname' => $firstname,
                            'surname' => $surname,
                            'email' => $email,
                            'username' => $username,
                        ];

                        $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES ('admin_email', ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)")->execute([$email]);

                        $data['success'] = true;
                    }
                } catch (PDOException $e) {
                    $data['error'] = 'Database error: ' . $e->getMessage();
                }
            } else {
                $data['error'] = implode('<br>', $errors);
            }
        }

        $this->view('install/admin', $data);
    }

    public function settings(): void
    {
        $data['page_title'] = 'Site Settings';

        if (!isset($_SESSION['install_admin'])) {
            redirect('install/admin');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $siteName = trim($_POST['site_name'] ?? 'NtoshiSoft  Form');
            $adminEmail = trim($_POST['admin_email'] ?? '');
            $appDomain = trim($_POST['app_domain'] ?? 'localhost');
            $dbConfig = $_SESSION['install_db'];
            try {
                $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']};charset=utf8mb4";
                $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);

                $settings = [
                    'site_name' => $siteName,
                    'admin_email' => $adminEmail,
                ];

                $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
                foreach ($settings as $key => $value) {
                    $stmt->execute([$key, $value]);
                }

                if (!empty($_FILES['logo']['tmp_name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
                    $detectedMime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $_FILES['logo']['tmp_name']);
                    if (in_array($detectedMime, $allowedMimes)) {
                        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                        $logoName = 'logo.' . $ext;
                        $logosDir = ROOTPATH . 'assets/img/logos/';
                        if (!is_dir($logosDir)) mkdir($logosDir, 0755, true);
                        move_uploaded_file($_FILES['logo']['tmp_name'], $logosDir . $logoName);
                        $_SESSION['install_logo'] = $logoName;
                    }
                }

                $rootUrl = rtrim($appDomain, '/');
                if (!preg_match('#^https?://#', $rootUrl)) {
                    $protocol = 'http://';
                    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                        $protocol = 'https://';
                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                        $protocol = 'https://';
                    } elseif (!empty($_SERVER['HTTP_CF_VISITOR'])) {
                        $cf = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
                        if (($cf['scheme'] ?? '') === 'https') $protocol = 'https://';
                    }
                    $rootUrl = $protocol . $rootUrl;
                }
                if (!str_contains($rootUrl, '/public')) {
                    $projectRoot = dirname(ROOTPATH);
                    $hasRewrite = false;
                    // Only the active .htaccess determines URL routing; .htaccess-bkp is ignored.
                    $htaccess = $projectRoot . '/.htaccess';
                    if (file_exists($htaccess) && preg_match('/RewriteRule\s+(.*\s+)?public\/\s*\[L\]/i', @file_get_contents($htaccess) ?: '')) {
                        $hasRewrite = true;
                    }
                    if (!$hasRewrite) {
                        $rootUrl .= '/public';
                    }
                }
                $_SESSION['install_root_url'] = $rootUrl;
                $_SESSION['install_site_name'] = $siteName;
                $_SESSION['install_settings_done'] = true;
                $data['success'] = true;
            } catch (PDOException $e) {
                $data['error'] = 'Database error: ' . $e->getMessage();
            }
        }

        $this->view('install/settings', $data);
    }

    public function finish(): void
    {
        $data['page_title'] = 'Installation Complete';

        if (!isset($_SESSION['install_settings_done'])) {
            redirect('install/settings');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' || !isset($data['written'])) {
            $dbConfig = $_SESSION['install_db'];
            $rootUrl = $_SESSION['install_root_url'] ?? "http://{$_SERVER['HTTP_HOST']}";
            $siteName = $_SESSION['install_site_name'] ?? 'NtoshiSoft  Form';

            $envData = [
                'DB_HOST' => $dbConfig['host'],
                'DB_NAME' => $dbConfig['name'],
                'DB_USER' => $dbConfig['user'],
                'DB_PASS' => $dbConfig['pass'],
                'DB_DRIVER' => 'mysql',
                'APP_NAME' => $siteName,
                'APP_NAME_SHORT' => 'NtoshiSoft ',
                'APP_DOMAIN' => parse_url($rootUrl, PHP_URL_HOST) ?: 'localhost',
                'APP_TAG_LINE' => 'Business management platform',
                'DEFAULT_TIMEZONE' => 'Africa/Johannesburg',
                'ROOT' => $rootUrl,
                'MAIL_HOST' => $_POST['mail_host'] ?? 'smtp.gmail.com',
                'MAIL_USERNAME' => $_POST['mail_username'] ?? '',
                'MAIL_PASSWORD' => $_POST['mail_password'] ?? '',
                'MAIL_PORT' => $_POST['mail_port'] ?? '465',
                'MAIL_ENCRYPTION' => $_POST['mail_encryption'] ?? 'ssl',
                'DEBUG' => 'false',
                'APP_ENV' => 'production',
                'SESSION_LIFETIME' => '120',
                'CSRF_TOKEN_LENGTH' => '32',
                'EST_YEAR' => date('Y'),
                'POLICY_ADOPT_DATE' => date('Y-m-d'),
                'DEF_CURR' => 'R',
                'JONGI_CLI_VERS' => '1.0.0',
                'THEME_COLOR' => 'primary',
                'VARIANT_COLOR' => '#d5ba0b',
                'MAX_FILE_SIZE' => '5242880',
                'ALLOWED_FILE_TYPES' => 'jpg,jpeg,png,gif,pdf,webp,webm,mp3,wav,ogg',
            ];

            $envPath = ROOTPATH . '../.env';
            $written = EnvWriter::write($envData, $envPath);

            if ($written) {
                $_SESSION['install_complete'] = true;
                $data['success'] = true;
                $data['login_url'] = $rootUrl . '/auth/login';
            } else {
                $data['error'] = 'Failed to write .env file. Please check file permissions.';
                $data['env_content'] = $envData;
            }
        }

        $this->view('install/finish', $data);
    }

    public function restart(): void
    {
        $keys = ['install_db', 'install_db_ready', 'install_admin', 'install_logo', 'install_root_url', 'install_site_name', 'install_settings_done', 'install_complete'];
        foreach ($keys as $key) {
            unset($_SESSION[$key]);
        }
        redirect('install');
    }
}
