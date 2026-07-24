<div align="center">
  <img src="https://raw.githubusercontent.com/Jongi-TheAutodidact/ntoshisoft-framework/main/public/assets/img/logos/logo.png" alt="NtoshiSoft Framework" width="180"/>

  # NtoshiSoft Framework

  **Lightweight PHP MVC Framework for Rapid Business Application Development**

  [![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.0-777BB4?style=flat-square&logo=php)](https://php.net)
  [![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg?style=flat-square)](https://www.gnu.org/licenses/gpl-3.0)
  [![GitHub release](https://img.shields.io/github/v/release/Jongi-TheAutodidact/ntoshisoft-framework?style=flat-square)](https://github.com/Jongi-TheAutodidact/ntoshisoft-framework/releases)
  [![GitHub stars](https://img.shields.io/github/stars/Jongi-TheAutodidact/ntoshisoft-framework?style=flat-square)](https://github.com/Jongi-TheAutodidact/ntoshisoft-framework/stargazers)

  *Built for small to medium-sized businesses, startups, and solo developers who want to ship functional applications fast — without the overhead of enterprise frameworks.*

  [Getting Started](#getting-started) • [CLI Tool](#cli-tool) • [Documentation](#documentation) • [Contributing](#contributing)

</div>

---

<div align="center">
  <table>
    <tr>
      <td align="center"><img src="https://raw.githubusercontent.com/Jongi-TheAutodidact/ntoshisoft-framework/main/public/assets/img/framework-screenshots/Screenshot%202026-07-24%20211732.png" alt="NtoshiSoft Framework Screenshot 1" width="100%"/></td>
      <td align="center"><img src="https://raw.githubusercontent.com/Jongi-TheAutodidact/ntoshisoft-framework/main/public/assets/img/framework-screenshots/Screenshot%202026-07-24%20211904.png" alt="NtoshiSoft Framework Screenshot 2" width="100%"/></td>
    </tr>
    <tr>
      <td align="center"><img src="https://raw.githubusercontent.com/Jongi-TheAutodidact/ntoshisoft-framework/main/public/assets/img/framework-screenshots/Screenshot%202026-07-24%20212007.png" alt="NtoshiSoft Framework Screenshot 3" width="100%"/></td>
      <td align="center"><img src="https://raw.githubusercontent.com/Jongi-TheAutodidact/ntoshisoft-framework/main/public/assets/img/framework-screenshots/Screenshot%202026-07-24%20212131.png" alt="NtoshiSoft Framework Screenshot 4" width="100%"/></td>
    </tr>
  </table>
</div>

## Features

- **Lightweight MVC** — Clean separation of concerns with Models, Views, and Controllers
- **User-Centric Identity** — Everyone registers as a `users` first; role-specific profiles (Employee, Client, etc.) link back via `user_id`
- **CLI Generator** (`jongi`) — Scaffold controllers, models, migrations from the terminal
- **Migration System** — Version-controlled database schema changes
- **Built-in Auth** — Session management, CSRF protection, password hashing, role-based access
- **Mailer Integration** — SMTP email via PHPMailer (password resets, notifications)
- **Payment Gateway** — Extensible payment processing (PayFast ready)
- **Image Handler** — Upload, resize, and manage images
- **Validator** — Form input validation with error feedback
- **Logger** — File-based logging with rotating logs
- **Environment Config** — `.env` file management via `EnvLoader`/`EnvWriter`
- **Installation Wizard** — 6-step web-based setup for first-time deployment

---

## Requirements

| Requirement | Minimum |
|-------------|---------|
| PHP | 8.0 or higher |
| PDO Extension | `pdo_mysql` |
| GD Library | Image processing |
| cURL | HTTP requests |
| Fileinfo | MIME type detection |
| mbstring | Multi-byte string handling |
| JSON | Data serialization |
| MySQL | 5.7+ or MariaDB 10.3+ |
| Composer | Dependency management |
| **Web Server** | Apache (mod_rewrite) or Nginx |

---

## Getting Started

### Option 1: Install via Composer (Recommended)

```bash
composer create-project jongi-theautodidact/ntoshisoft-framework my-app
cd my-app
```

### Option 2: Clone from GitHub

```bash
git clone https://github.com/Jongi-TheAutodidact/ntoshisoft-framework.git
cd ntoshisoft-framework
composer install
```

Or [download the ZIP](https://github.com/Jongi-TheAutodidact/ntoshisoft-framework/archive/refs/heads/main.zip), extract, and run `composer install`.

---

## Post-Installation: Running the Setup Wizard

After installation, the framework guides you through a **6-step web-based wizard** to configure your application.

### 1. Live Server Prerequisite

> **Important:** On a live server, before running the install wizard, you **must** rename `.htaccess-bkp` to `.htaccess` in the project root. Without this, the browser will display a blank page because Apache URL rewriting will not be active.

```bash
mv .htaccess-bkp .htaccess
```

This step is **not required** on localhost (XAMPP) since the app is accessed directly via the `public/` directory.

### 2. Copy the Environment File

```bash
cp .env.example .env
```

### 3. Open the Installer in Your Browser

Navigate to the appropriate URL for your environment:

| Environment | URL |
|-------------|-----|
| **Localhost (XAMPP)** | `http://localhost/{project-directory}/public` |
| **Live Server** | `https://your-domain.com` |

> **Note:** The app is served via Apache (XAMPP or production). Do **not** use `php jongi serve` or `php jongi spinit` — the framework is designed to run through a proper web server.

You'll be redirected to the **Installation Wizard** (`install.php`).

### 4. Complete the 6-Step Wizard

The wizard will walk you through:

| Step | What You'll Configure |
|------|-----------------------|
| **1. Welcome & Requirements** | Server checks (PHP version, extensions, permissions) |
| **2. Database** | MySQL host, database name, credentials |
| **3. Tables** | Creates `users` and `settings` tables |
| **4. Admin Account** | Your super-admin login credentials |
| **5. Site Settings** | App name, logo upload |
| **6. Mail & Finalize** | SMTP settings (optional — skip to do later) |

Once complete, the `.env` file is written automatically and you're redirected to your application.

### 5. Log In

Navigate to your app URL and log in using the admin credentials you created during setup.

---

## What's Included Out of the Box

The installation wizard creates **only two default tables**:

| Table | Purpose |
|-------|---------|
| `users` | Admin and user accounts |
| `settings` | Application configuration (site name, colors, etc.) |

These are the minimum tables needed for the framework to function (authentication and basic settings).

> **Important:** After logging in, you'll see the full dashboard with navigation links to features like **Chatroom**, **Payments**, **Employees**, etc. These are generic modules — clicking them without their database tables will throw errors. To fully explore the framework, migrate all shipped example migrations using the cli command:
>
> ```bash
> php jongi migrate:all
> ```
>
> This creates the remaining tables for the bundled generic modules. You can add, remove, or modify any of these later to suit your project.

### Ships With — But Not Auto-Migrated

The framework also includes **example migrations, models, controllers, and CRUD views** in the codebase. These are provided as **reference implementations** — ready-made examples you can study, adapt, or use as a starting point for your own project.

> **Do not migrate all migrations during setup.** Running every included migration would create tables you may never use and clutter your database. Instead:
>
> 1. Browse `app/migrations/`, `app/models/`, `app/controllers/`, and `app/views/` to see what's available.
> 2. Pick only the ones relevant to your project.
> 3. Create your own migrations, models, and controllers as your project dictates.

This keeps your database lean and your application tailored to your specific needs.

---

## Web Server Configuration

### Apache (with mod_rewrite)

The framework ships with a `.htaccess` file inside `public/` that handles URL rewriting. No additional configuration needed if `.htaccess` is enabled.

For a cleaner setup (optional), move the rewrite rules to the root:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Nginx

```nginx
server {
    listen 80;
    server_name my-app.local;
    root /path/to/my-app/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?url=$uri&$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## CLI Tool (`jongi`)

The framework includes a **command-line scaffolding tool** to accelerate development.

```bash
php jongi help
```

### Available Commands

#### Scaffolding
| Command | Description |
|---------|-------------|
| `php jongi make:controller <name>` | Generate a new controller |
| `php jongi make:model <name>` | Generate a new model |
| `php jongi make:migration <name>` | Generate a new migration file |
| `php jongi list:migrations` | List all migration files |

### Scaffolding Examples

```bash
# Create a controller
php jongi make:controller Product

# Create a model (table name auto-pluralized: products)
php jongi make:model Product

# Create a migration
php jongi make:migration create_products_table
```

---

## Directory Structure

```
ntoshisoft-framework/
├── app/
│   ├── config/          # Route definitions
│   ├── controllers/     # Application controllers
│   ├── core/            # Framework engine (Database, Model, Router, etc.)
│   ├── jongi/           # CLI tool classes
│   ├── middleware/       # Auth, role, rate-limit middleware
│   ├── migrations/       # Database migration files
│   └── models/          # Data models
│   └── views/           # Presentation templates (.ntoshi.php)
├── logs/                # Application logs
├── public/              # Web root (index.php entry point)
│   └── assets/          # CSS, JS, images, uploads
├── vendor/              # Composer dependencies
├── .env                 # Environment configuration (gitignored)
├── .gitignore
├── composer.json
├── index.php            # Install check & redirect to public/
├── install.php          # Setup wizard
├── jongi                # CLI entry point
└── README.md
```

### Key Framework Files

| File | Purpose |
|------|---------|
| `public/index.php` | Application entry point — bootstraps routing |
| `app/core/init.php` | Loads Composer autoloader (delegates to `vendor/autoload.php`) |
| `app/core/config.php` | Defines constants from `.env` (DB, app, mail, security settings) |
| `app/core/functions.php` | Global helper functions |
| `app/core/Database.php` | PDO database wrapper (queries, transactions, error handling) |
| `app/core/Model.php` | Base trait for all models (CRUD, query building, pagination) |
| `app/core/Controller.php` | Base trait — view rendering, data passing |
| `app/core/Router.php` | URL dispatcher — matches routes to controllers |
| `app/core/Middleware.php` | Base middleware class |
| `app/config/routes.php` | Define your application routes here |

---

## Configuration

The `.env` file is created automatically during the installation wizard. Key variables:

```env
# Database
DB_HOST=localhost
DB_NAME=my_app_db
DB_USER=root
DB_PASS=""

# Application
APP_NAME="My Application"
ROOT="http://localhost/ntoshisoft-framework/public"
APP_ENV=production
DEBUG=false

# Mail (optional)
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=your@email.com
MAIL_PASSWORD="your-app-password"

# Security
SESSION_LIFETIME=120
CSRF_TOKEN_LENGTH=32
```

> **Note:** On a live server, `ROOT` will be set to `https://your-domain.com` (without `/public`). On localhost (XAMPP), it will be `http://localhost/{project-directory}/public`.

> **Security:** The `.env` file is excluded from Git via `.gitignore`. Never commit it to version control.

---

## Built With

- [PHPMailer](https://github.com/PHPMailer/PHPMailer) — Email sending
- [Dompdf](https://github.com/dompdf/dompdf) — PDF generation
- [Bootstrap 5](https://getbootstrap.com) — Frontend UI (included in assets)
- [Bootstrap Icons](https://icons.getbootstrap.com) — Icon library

---

## License

This project is licensed under the **GNU General Public License v3.0** — see the [LICENSE](https://www.gnu.org/licenses/gpl-3.0) for details.

---

<div align="center">
  <sub>Built with ❤️ by <a href="https://github.com/Jongi-TheAutodidact">Jongi Mbodla</a></sub>
  <br>
  <sub>NtoshiSoft Framework — Making business applications accessible to everyone.</sub>
</div>