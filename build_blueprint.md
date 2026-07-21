# NtoshiSoft Framework — Business Application Build Blueprint
> **Version:** 1.0.0 | **Author:** Jongi Mbodla | **Last updated:** 2026-05-30
> Use this document as the canonical reference when building any production-ready business application on NtoshiSoft.

---

## Table of Contents
1. [Framework Philosophy](#1-framework-philosophy)
2. [Project Planning Phase](#2-project-planning-phase)
3. [Architecture Overview](#3-architecture-overview)
4. [The User-Centric Identity Model](#4-the-user-centric-identity-model)
5. [Step-by-Step Build Process](#5-step-by-step-build-process)
6. [Sector-Specific Patterns](#6-sector-specific-patterns)
7. [CLI Command Reference](#7-cli-command-reference)
8. [Security Checklist](#8-security-checklist)
9. [Deployment Checklist](#9-deployment-checklist)
10. [Troubleshooting & FAQ](#10-troubleshooting--faq)

---

## 1. Framework Philosophy

NtoshiSoft is built on three core principles:

### 1.1 Universal User First
Every person who interacts with the system is first and foremost a **User** (stored in the `users` table). Their role — Employee, Client, Admin, Driver, Teacher, Agent, Patient, Student, Tenant, Member, etc. — is assigned later through **role-specific models** that reference back to the user. This means:

- One login for all personas
- No separate registration flows per role
- Role assignment happens during profile/resource creation, not at signup
- A single person can wear multiple hats (e.g., Employee who is also a Client)

### 1.2 Model as a Trait, Not a Base Class
The `Model`, `Database`, and `Controller` are all **PHP traits**. This gives you:
- Mixin-style composition instead of rigid inheritance
- Models can use multiple traits if needed
- Zero boilerplate — just `use Model;` and set `$table` and `$allowedColumns`

### 1.3 Convention over Configuration
- Table names match model names (lowercase, plural): `Employee` → `employees`
- View files use `.ntoshi.php` extension
- Routes are defined in `app/config/routes.php` as a single array
- Model `$allowedColumns` is the sole gatekeeper for mass assignment
- Migrations follow the `alpha()`/`omega()` up/down pattern

---

## 2. Project Planning Phase

Before writing a single line of code, complete this planning phase. Fill in the `[square brackets]` with your project-specific details.

### 2.1 Sector & Domain Definition
```
Sector:        [e.g., Healthcare / Education / Real Estate / Logistics / Retail / Finance / HR / Agriculture]
Application:   [e.g., Clinic Management System / School Portal / Property Manager / Fleet Tracker]
Primary Users: [e.g., Doctors, Nurses, Patients / Teachers, Students, Parents / Tenants, Landlords, Agents]
Core Problem:  [What business problem does this solve?]
```

### 2.2 Entity Identification
List all real-world entities that the system must model. Map each to its data source:

| Entity | Stored As | Role-Specific Table? | Key Relationships |
|--------|-----------|---------------------|-------------------|
| Person | `users` table | Always starts here | — |
| [e.g., Patient] | `[patients]` | Yes, FK→users.id | [has many Appointments] |
| [e.g., Doctor] | `[doctors]` | Yes, FK→users.id | [belongs to Department] |
| [e.g., Appointment] | `[appointments]` | Yes | [belongs to Patient, belongs to Doctor] |
| ... | | | |

### 2.3 Route Map (User Stories → URLs)
For each user story, define the route and controller method:

| User Story | URL Pattern | Controller:Method | Auth Required | Role Required |
|------------|-------------|-------------------|---------------|---------------|
| View dashboard | `admin` | `AdminController::index` | Yes | Admin |
| List [entities] | `admin/[entities]` | `[Entity]Controller::index` | Yes | [Role] |
| Create [entity] | `admin/[entities]/create` | `[Entity]Controller::create` | Yes | [Role] |
| Edit [entity] | `admin/[entities]/edit/{id}` | `[Entity]Controller::edit` | Yes | [Role] |
| Delete [entity] | `admin/[entities]/delete/{id}` | `[Entity]Controller::delete` | Yes | [Role] |
| Public registration | `register` | `AuthController::register` | No | — |
| Login | `login` | `AuthController::login` | No | — |
| [Public feature] | `[public-route]` | `[Controller]::[method]` | No | — |

### 2.4 Migration Planning
For each entity with its own table, plan the migration:

```php
// Template for each migration
$this->addColumn('id int(10) UNSIGNED NOT NULL AUTO_INCREMENT');
$this->addColumn('user_id varchar(1024) DEFAULT NULL');            // FK to users table
$this->addColumn('[field_name] [type] [nullable] [default]');      // Business fields
$this->addColumn('date_created datetime DEFAULT NULL');
$this->addColumn('date_updated datetime DEFAULT NULL');
$this->addColumn('created_by varchar(30) DEFAULT NULL');
$this->addColumn('updated_by varchar(30) DEFAULT NULL');
$this->addColumn('deleted_by varchar(30) DEFAULT NULL');
$this->addPrimaryKey('id');
$this->addKey('[indexed_field]');
$this->addForeignKey('user_id', 'users', 'user_id');               // If FK to users is needed
$this->createTable('[table_name]');
```

### 2.5 Settings Key Planning
All application-wide configuration goes in the `settings` table (key-value):

| Key | Value Type | Default | Purpose |
|-----|-----------|---------|---------|
| `[feature]_enabled` | bool | true/false | Toggle feature on/off |
| `[feature]_config` | text | JSON | Structured config |
| ... | | | |

---

## 3. Architecture Overview

```
Browser Request
     │
     ▼
public/index.php          ← Front controller (single entry point)
     │
     ▼
app/core/init.php         ← Autoloader + core file loader
     │
     ▼
app/core/config.php       ← EnvLoader → define() all constants
     │
     ▼
app/core/Router.php       ← Loads app/config/routes.php, dispatches URL
     │
     ├──▶ Middleware Pipeline (AuthMiddleware, RoleMiddleware, RateLimitMiddleware)
     │        │
     │        ▼
     └──▶ Controller (uses Controller trait)
              │
              ├──▶ Model (uses Model + Database traits) → MySQL
              │
              └──▶ View (.ntoshi.php) ← extract($data)
                       │
                       ├── inc/header.ntoshi.php
                       ├── [content template]
                       └── inc/footer.ntoshi.php
```

### Request Lifecycle Diagram

```
HTTP Request
    │
    ▼
┌─────────────────────────────────────────────────┐
│  public/index.php                                │
│  • Start session                                 │
│  • Check if installed                            │
│  • Load bootstrap (init.php → config + core)     │
│  • Set error reporting level                     │
│  • Load routes.php                               │
│  • Dispatch URL                                  │
└──────────────────────┬──────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────┐
│  Router::dispatch($url)                          │
│  • Match URL pattern against routes              │
│  • Extract {param} placeholders                  │
│  • Load middleware stack                         │
│  • Build onion pipeline                          │
└──────────────────────┬──────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────┐
│  Middleware Pipeline (outer → inner)             │
│                                                  │
│  Middleware 1: RateLimitMiddleware                │
│  • Check request count in sliding window         │
│  • Return 429 if exceeded                        │
│                                                  │
│  Middleware 2: AuthMiddleware                     │
│  • Check logged_in()                             │
│  • Validate session timeout                      │
│  • Check CSRF token (POST/PUT/DELETE)            │
│                                                  │
│  Middleware 3: RoleMiddleware                     │
│  • Check userRole against allowed roles          │
│  • Show 403 if unauthorized                      │
└──────────────────────┬──────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────┐
│  Controller::action($params)                     │
│                                                  │
│  1. Instantiate models                           │
│     $user = new User();                          │
│     $patient = new Patient();                    │
│                                                  │
│  2. Handle POST (form submissions)               │
│     if ($_SERVER['REQUEST_METHOD'] == 'POST') {  │
│         • Validate CSRF token                    │
│         • Validate input ($model->validate())     │
│         • Insert / Update                        │
│         • Set flash message                      │
│         • Redirect                               │
│     }                                            │
│                                                  │
│  3. Fetch data for view                          │
│     $data['entities'] = $model->findAll();       │
│     $data['page_title'] = '...';                 │
│                                                  │
│  4. Render view                                  │
│     $this->view('admin/[section]/[view]', $data);│
└──────────────────────┬──────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────┐
│  View (.ntoshi.php)                              │
│                                                  │
│  extract($data) → variables available            │
│  $this->view('inc/header', $data)                │
│  ─── HTML content using $variables ───           │
│  $this->view('inc/footer', $data)                │
└─────────────────────────────────────────────────┘
```

---

## 4. The User-Centric Identity Model

This is the single most important architectural decision in NtoshiSoft. **Understand it thoroughly before building anything.**

### 4.1 The Identity Hierarchy

```
┌─────────────────────────────────────────────┐
│              users TABLE                     │
│                                              │
│  id | user_id | firstname | surname | email  │
│  password | user_role | phone | created      │
│                                              │
│  PRIMARY identity — EVERYONE starts here     │
└────────────────────┬────────────────────────┘
                     │
      ┌──────────────┼──────────────────┐
      │              │                  │
      ▼              ▼                  ▼
┌──────────┐  ┌──────────┐      ┌──────────┐
│ employees│  │ clients  │      │ [others] │
│ TABLE    │  │ TABLE    │      │ TABLE    │
│          │  │          │      │          │
│ user_id  │  │ user_id  │      │ user_id  │
│ ──────── │  │ ──────── │      │ ──────── │
│ FK to    │  │ FK to    │      │ FK to    │
│ users    │  │ users    │      │ users    │
│          │  │          │      │          │
│ Role-    │  │ Role-    │      │ Role-    │
│ specific │  │ specific │      │ specific │
│ profile  │  │ profile  │      │ profile  │
└──────────┘  └──────────┘      └──────────┘
```

### 4.2 Registration Flow

```
User arrives
     │
     ▼
[System creates user via Register / Admin creates user]
     │
     ▼
users table row created with basic role
(user_role = 'User', 'Employee', 'Client', etc.)
     │
     ▼
[Admin goes to role-specific section, e.g., admin/employees]
     │
     ▼
"Create Employee" → Dropdown of users NOT yet assigned
Select user → Fill role-specific details → Save
     │
     ▼
Role-specific table row created (FK→users.user_id)
```

### 4.3 Key Rule: Separate Login from Role Assignment

- **Login** only checks the `users` table via `User::authenticate()`
- **Role assignment** happens in the role-specific controller (e.g., `EmployeeController::create()`)
- A user dropdown in create forms should exclude users already assigned to that role:

```php
// In EmployeeController::create()
$userModel = new User();
$employeeModel = new Employee();

// Get all users minus those already employees
$allUsers = $userModel->findAll();
$existingEmployees = $employeeModel->getEmployeesWithUserDetails();
$existingUserIds = array_column($existingEmployees, 'user_id');
$availableUsers = array_filter($allUsers, fn($u) => !in_array($u->user_id, $existingUserIds));

$data['available_users'] = $availableUsers;
```

### 4.4 Model Structure Pattern for Role-Specific Models

```php
class [RoleName] {
    use Model;

    protected $table = '[table_name]';
    protected $allowedColumns = [
        'user_id',
        // ... role-specific fields
    ];

    public function validate(array $data, int|string|null $id = null): bool
    {
        $this->errors = [];
        // ... validation rules
        return empty($this->errors);
    }

    // Always join with users table to get full identity
    public function getAllWithUserDetails(): array
    {
        return $this->query("
            SELECT r.*, u.firstname, u.surname, u.email, u.phone, u.image
            FROM [table_name] r
            LEFT JOIN users u ON r.user_id = u.user_id
            ORDER BY r.id DESC
        ") ?: [];
    }

    public function getSingleWithUserDetails(string|int $id): object|false
    {
        return $this->query("
            SELECT r.*, u.firstname, u.surname, u.email, u.phone, u.image
            FROM [table_name] r
            LEFT JOIN users u ON r.user_id = u.user_id
            WHERE r.id = ?
        ", [$id])[0] ?? false;
    }
}
```

### 4.5 The `user_id` Convention

The `users` table uses `user_id` as a unique string identifier (e.g., `'10042'`, generated via `rand(10001, 99099)` at creation). All role-specific tables store this same `user_id` as the foreign key. This is NOT the auto-increment `id` column — it's a separate, visible identifier.

Always join using `r.user_id = u.user_id` (not `r.user_id = u.id`).

---

## 5. Step-by-Step Build Process

### PHASE 0: Refer to the README.md file herein

### PHASE 1: Database Design & Migrations (30 minutes)

#### Step 1.1: Plan your tables using the schema below

For each entity that needs its own table (beyond `users` and `settings`), create a migration:

**Table: `users`** — Already exists. Do NOT create a migration for it.

**Table: `settings`** — Already exists. Do NOT create a migration for it.

**New table migration pattern** (create via CLI):
```bash
php jongi make:migration [EntityName]
# → Creates app/migrations/Ntoshi_[date]_[time]_[EntityName].php
```

#### Step 1.2: Edit the generated migration file

```php
<?php
// app/migrations/Ntoshi_30th_May_2026_10_30_00_[EntityName].php

class [EntityName] extends Migration {
    public function alpha() {
        // Standard audit columns (always include)
        $this->addColumn('id int(10) UNSIGNED NOT NULL AUTO_INCREMENT');

        // Reference to users table (if this is a role-specific profile)
        $this->addColumn('user_id varchar(1024) DEFAULT NULL');

        // [SECTOR-SPECIFIC: Add your business columns here]
        // ─────────────────────────────────────────────────────
        // Healthcare: patient_id, diagnosis_code, blood_type, allergies
        // Education: student_number, grade, class_id, enrollment_date
        // Real Estate: property_id, unit_number, lease_start, rent_amount
        // Retail: sku, category_id, supplier_id, selling_price, stock_qty
        // Logistics: tracking_number, origin, destination, weight, status
        // Finance: account_number, account_type, interest_rate, balance
        // HR: employee_number, department, position, hire_date, salary
        // Agriculture: parcel_id, crop_type, planting_date, yield_estimate
        // ─────────────────────────────────────────────────────

        // Audit columns (always include)
        $this->addColumn('date_created datetime DEFAULT NULL');
        $this->addColumn('date_updated datetime DEFAULT NULL');
        $this->addColumn('created_by varchar(30) DEFAULT NULL');
        $this->addColumn('updated_by varchar(30) DEFAULT NULL');
        $this->addColumn('deleted_by varchar(30) DEFAULT NULL');

        // Keys
        $this->addPrimaryKey('id');
        $this->addKey('user_id');
        $this->addKey('date_created');
        // $this->addForeignKey('user_id', 'users', 'user_id');  // Uncomment if FK needed

        $this->createTable('[table_name]');
    }

    public function omega() {
        $this->dropTable('[table_name]');
    }
}
```

#### Step 1.3: Run the migration
```bash
php jongi migrate Ntoshi_30th_May_2026_10_30_00_[EntityName].php
```

#### Step 1.4: Repeat for each entity

---

### PHASE 2: Model Creation (15 minutes)

#### Step 2.1: Generate the model
```bash
php jongi make:model [EntityName]
# → Creates app/models/[EntityName].php
```

#### Step 2.2: Edit the model

```php
<?php
// app/models/[EntityName].php

defined('ROOTPATH') or exit('Access Denied!');

class [EntityName]
{
    use Model;

    protected $table = '[table_name]';
    protected $allowedColumns = [
        'user_id',
        // [SECTOR-SPECIFIC: Add your business columns here]
    ];

    public function validate(array $data, int|string|null $id = null): bool
    {
        $this->errors = [];

        // [SECTOR-SPECIFIC: Add validation rules here]
        // if (empty($data['field_name'])) {
        //     $this->errors['field_name'] = "Field is required";
        // }

        return empty($this->errors);
    }

    // [SECTOR-SPECIFIC: Add your custom query methods]

    /**
     * Get all records with user details
     */
    public function getAllWithUserDetails(): array
    {
        return $this->query("
            SELECT r.*, u.firstname, u.surname, u.email, u.phone, u.image
            FROM $this->table r
            LEFT JOIN users u ON r.user_id = u.user_id
            ORDER BY r.id DESC
        ") ?: [];
    }

    /**
     * Get single record by user_id with user details
     */
    public function getByUserId(string|int $userId): object|false
    {
        return $this->first(['user_id' => $userId]);
    }
}
```

#### Step 2.3: Add to autoloader (if needed)

The autoloader in `app/core/init.php` already loads models by classname from `app/models/`. No extra step needed — just naming the file `[EntityName].php` and class `[EntityName]` is enough.
[This is very important - DO NOT TRY TO RECREATE ANOTHER AUTOLOADER OR DEPEND ON THE VENDOR - THE FRAMEWORK AUTOMATICALLY ACCOMODATES THIS IN THE `app/core/init.php except there was a need for additional core class]
---

### PHASE 3: Route Definition (5 minutes)

#### Step 3.1: Add routes to `app/config/routes.php`

```php
// Frontend routes (public-facing)
'[entity]'              => ['[Entity]Controller', 'index'],
'[entity]/{id}'         => ['[Entity]Controller', 'detail'],

// Admin CRUD routes (if admin feature)
'admin/[entities]' => [
    'controller' => ['[Entity]Controller', 'index'],
    'middleware' => ['AuthMiddleware']
],
'admin/[entities]/create'  => ['[Entity]Controller', 'create'],
'admin/[entities]/edit/{id}' => ['[Entity]Controller', 'edit'],
'admin/[entities]/delete/{id}' => ['[Entity]Controller', 'delete'],

// Add RoleMiddleware for role-specific access:
'admin/[entities]' => [
    'controller' => ['[Entity]Controller', 'index'],
    'middleware' => ['AuthMiddleware', 'RoleMiddleware']  // RoleMiddleware needs config in controller
],
```

**Route URL parameter convention:**
- `{id}` captures a URL segment as a string parameter passed to the controller method
- The controller method signature should use `?string $id = null` for optional params
- Routes are matched in order — put more specific routes before generic ones
- `{id}` can represent any identifier (numeric ID, slug, UUID, etc.)

---

### PHASE 4: Controller Creation (30 minutes)

#### Step 4.1: Generate the controller
```bash
php jongi make:controller [Entity]Controller
# → Creates app/controllers/[Entity]Controller.php
```

#### Step 4.2: Build the controller

```php
<?php
// app/controllers/[Entity]Controller.php

defined('ROOTPATH') or exit('Access Denied!');

class [Entity]Controller
{
    use Controller;

    private $[entityModel];
    private $userModel;

    public function __construct()
    {
        $this->[entityModel] = new [EntityName]();
        $this->userModel = new User();
    }

    /**
     * List all [entities]
     */
    public function index(): void
    {
        $data['rows'] = $this->[entityModel]->getAllWithUserDetails();
        $data['page_title'] = '[Entity] Management';

        $this->view('admin/[entities]/[entities]', $data);
    }

    /**
     * Show creation form & handle submission
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // CSRF check
            if (!Validator::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token!');
            }

            if ($this->[entityModel]->validate($_POST)) {
                $_POST['date_created'] = date('Y-m-d H:i:s');
                $this->[entityModel]->insert($_POST);
                Util::setFlash('success', '[Entity] created successfully!');
                redirect('admin/[entities]');
            }

            $data['errors'] = $this->[entityModel]->errors;
        }

        // Get available users (not yet assigned to this entity)
        // [SECTOR-SPECIFIC: Adjust the user filtering logic]
        $allUsers = $this->userModel->findAll();
        $existing = $this->[entityModel]->getAllWithUserDetails();
        $existingUserIds = !empty($existing) ? array_column($existing, 'user_id') : [];
        $data['available_users'] = array_filter($allUsers, fn($u) => !in_array($u->user_id, $existingUserIds));

        $data['page_title'] = 'Create [Entity]';
        $this->view('admin/[entities]/[entity]-create', $data);
    }

    /**
     * Show edit form & handle submission
     */
    public function edit(?string $id = null): void
    {
        $data['row'] = $id ? $this->[entityModel]->first(['id' => $id]) : null;

        if (!$data['row']) {
            redirect('admin/[entities]');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!Validator::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token!');
            }

            if ($this->[entityModel]->validate($_POST, $id)) {
                $_POST['date_updated'] = date('Y-m-d H:i:s');
                $this->[entityModel]->update($id, $_POST);
                Util::setFlash('success', '[Entity] updated successfully!');
                redirect('admin/[entities]');
            }

            $data['errors'] = $this->[entityModel]->errors;
        }

        $data['page_title'] = 'Edit [Entity]';
        $this->view('admin/[entities]/[entity]-edit', $data);
    }

    /**
     * Show delete confirmation & handle deletion
     */
    public function delete(?string $id = null): void
    {
        $data['row'] = $id ? $this->[entityModel]->first(['id' => $id]) : null;

        if (!$data['row']) {
            redirect('admin/[entities]');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!Validator::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token!');
            }

            $this->[entityModel]->delete($id);
            Util::setFlash('success', '[Entity] deleted successfully!');
            redirect('admin/[entities]');
        }

        $data['page_title'] = 'Delete [Entity]';
        $this->view('admin/[entities]/[entity]-delete', $data);
    }

    // [SECTOR-SPECIFIC: Add more methods as needed]
    // e.g., public function detail(?string $id = null): void { ... }
    // e.g., public function reports(): void { ... }
    // e.g., public function export(): never { ... }
}
```

#### Step 4.3: Controller CRUD Template Summary

```
Method    | URL                              | Purpose
──────────┼──────────────────────────────────┼──────────────────────────
index()   | /admin/[entities]                | List all records
create()  | /admin/[entities]/create         | Create form + POST handler
edit()    | /admin/[entities]/edit/{id}      | Edit form + POST handler
delete()  | /admin/[entities]/delete/{id}    | Delete confirmation + POST handler
```

---

### PHASE 5: View Creation (30 minutes)

#### Step 5.1: Create view directory structure
```
app/views/admin/[entities]/
├── [entities].ntoshi.php          # List/index view
├── [entity]-create.ntoshi.php     # Create form
├── [entity]-edit.ntoshi.php       # Edit form
└── [entity]-delete.ntoshi.php     # Delete confirmation
```

#### Step 5.2: List View (`[entities].ntoshi.php`)

```php
<?php
/** @var array $rows */
/** @var string $page_title */
$this->view('inc/header', $data);
?>

<main class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fs-4 page-title"><?= esc($page_title) ?></h3>
                <a href="<?= ROOT ?>/admin/[entities]/create" class="btn btn-warning text-dark">
                    <i class="bi bi-plus-circle me-2"></i>Create New
                </a>
            </div>

            <?php if (!empty($rows)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle ntoshi-search">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <!-- [SECTOR-SPECIFIC: Add your columns here] -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= $row->id ?></td>
                            <td>
                                <img src="<?= get_image($row->image ?? '', 'user') ?>" class="rounded-circle me-2" width="32" height="32" alt="">
                                <?= esc($row->firstname ?? '') ?> <?= esc($row->surname ?? '') ?>
                            </td>
                            <!-- [SECTOR-SPECIFIC: Display your columns] -->
                            <td>
                                <a href="<?= ROOT ?>/admin/[entities]/edit/<?= $row->id ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= ROOT ?>/admin/[entities]/delete/<?= $row->id ?>" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <p class="text-muted">No [entities] found. <a href="<?= ROOT ?>/admin/[entities]/create">Create the first one</a>.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php $this->view('inc/footer', $data); ?>
```

#### Step 5.3: Create Form View (`[entity]-create.ntoshi.php`)

```php
<?php
/** @var array $errors */
/** @var array $available_users */
$this->view('inc/header', $data);
?>

<main class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-body">
            <h3 class="fs-4 page-title mb-3">Create [Entity]</h3>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?= implode('<br>', $errors) ?>
            </div>
            <?php endif; ?>

            <form method="post">
                <?= displayFormHeaderOnCreate() ?>

                <div class="row g-3">
                    <!-- User Selection (for role-specific entities) -->
                    <div class="col-md-6">
                        <label class="form-label">Select User</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">-- Select User --</option>
                            <?php foreach ($available_users as $user): ?>
                            <option value="<?= esc($user->user_id) ?>">
                                <?= esc($user->firstname . ' ' . $user->surname . ' (' . $user->email . ')') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- [SECTOR-SPECIFIC: Add your form fields here] -->
                    <!--
                    <div class="col-md-6">
                        <label class="form-label">[Field Name]</label>
                        <input type="text" name="[field]" class="form-control" value="<?= old_value('[field]') ?>">
                    </div>
                    -->
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="<?= ROOT ?>/admin/[entities]" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php $this->view('inc/footer', $data); ?>
```

#### Step 5.4: Edit Form View (`[entity]-edit.ntoshi.php`)

```php
<?php
/** @var array $errors */
/** @var object $row */
$this->view('inc/header', $data);
?>

<main class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-body">
            <h3 class="fs-4 page-title mb-3">Edit [Entity]</h3>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?= implode('<br>', $errors) ?>
            </div>
            <?php endif; ?>

            <form method="post">
                <?= displayFormHeaderOnUpdate() ?>

                <div class="row g-3">
                    <!-- [SECTOR-SPECIFIC: Add your form fields pre-filled with $row] -->
                    <!--
                    <div class="col-md-6">
                        <label class="form-label">[Field Name]</label>
                        <input type="text" name="[field]" class="form-control" value="<?= esc($row->field) ?>">
                    </div>
                    -->
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="<?= ROOT ?>/admin/[entities]" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php $this->view('inc/footer', $data); ?>
```

#### Step 5.5: Delete Confirmation View (`[entity]-delete.ntoshi.php`)

```php
<?php
/** @var object $row */
$this->view('inc/header', $data);
?>

<main class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-body text-center">
            <h3 class="fs-4 page-title mb-3 text-danger">Confirm Deletion</h3>
            <p class="mb-4">Are you sure you want to delete this [entity]?</p>

            <div class="alert alert-warning">
                <strong><?= esc($row->firstname ?? '') ?> <?= esc($row->surname ?? '') ?></strong>
                <!-- [SECTOR-SPECIFIC: Show identifying info] -->
            </div>

            <form method="post">
                <?= displayFormHeaderOnDelete() ?>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash me-2"></i>Delete
                </button>
                <a href="<?= ROOT ?>/admin/[entities]" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php $this->view('inc/footer', $data); ?>
```

---

### PHASE 6: Settings & Configuration (10 minutes)

If your feature needs configuration options, add them via the `Settings` model:

**In the controller or a seeder:**
```php
$settings = new Settings();
$settings->set('[feature]_enabled', 'true');
$settings->set('[feature]_config', json_encode([...]));
```

**In views, access settings via:**
```php
$settings = new Settings();
$appSettings = $settings->loadSettings();  // Returns associative array
$featureEnabled = $appSettings['[feature]_enabled'] ?? 'false';
```

---

### PHASE 7: Public-Facing Pages (if needed)

If your entity needs public-facing pages (e.g., a public directory, self-service portal):

#### Step 7.1: Add public routes
```php
// In app/config/routes.php
'[entities]' => ['[Entity]Controller', 'public_index'],
'[entities]/{id}' => ['[Entity]Controller', 'public_detail'],
```

#### Step 7.2: Add public controller methods
```php
public function public_index(): void
{
    $data['rows'] = $this->[entityModel]->getAllWithUserDetails();
    $data['page_title'] = '[Entities]';
    $this->view('front/[entities]/index', $data);
}
```

#### Step 7.3: Create front views in `app/views/front/[entities]/`

---

## 6. Sector-Specific Patterns

### 6.1 [Healthcare / Clinic Management]

```
Entities:
- Patient (role-specific, FK→users)
- Doctor (role-specific, FK→users)
- Appointment (independent, FK→patients + FK→doctors)
- MedicalRecord (independent, FK→patients)
- Prescription (independent, FK→patients + FK→doctors)

Key migrations:
  appointments: appointment_date(DATETIME), reason(TEXT), status(VARCHAR), diagnosis(TEXT)
  medical_records: record_date(DATE), diagnosis(TEXT), notes(TEXT), attachments(TEXT)
  prescriptions: medication(VARCHAR), dosage(VARCHAR), frequency(VARCHAR), duration(VARCHAR)

Controller additions:
- AppointmentController: checkAvailability(), bookAppointment(), cancelAppointment()
- PatientController: medicalHistory(), uploadRecords()

Form helpers needed:
- Doctor dropdown (only available doctors)
- Time slot picker (15-min intervals)
- Diagnosis code autocomplete (ICD-10 codes in settings)

Model validation:
- appointment: date must be future, no double-booking same doctor+time
- prescription: dosage format validation
```

### 6.2 [Education / School Management]

```
Entities:
- Student (role-specific, FK→users)
- Teacher (role-specific, FK→users)
- Class (independent)
- Subject (independent)
- Enrollment (junction: student + class + academic_year)
- Grade (independent, FK→student + FK→subject)

Key migrations:
  students: student_number(VARCHAR), grade(VARCHAR), enrollment_date(DATE), guardian_name(VARCHAR)
  teachers: employee_number(VARCHAR), department(VARCHAR), qualification(TEXT), hire_date(DATE)
  classes: name(VARCHAR), room(VARCHAR), capacity(INT), teacher_id(VARCHAR->FK)
  subjects: name(VARCHAR), code(VARCHAR), credits(INT), department(VARCHAR)
  grades: score(DECIMAL), term(VARCHAR), academic_year(YEAR), teacher_notes(TEXT)

Controller additions:
- GradeController: reportCard(), transcript(), classAverage()
- AttendanceController: markAttendance(), dailyReport()

Form helpers needed:
- Academic year dropdown
- Term/quarter selector
- Class capacity checker
```

### 6.3 [Real Estate / Property Management]

```
Entities:
- Property (independent)
- Unit (independent, FK→property)
- Tenant (role-specific, FK→users)
- Lease (independent, FK→unit + FK→tenant)
- MaintenanceRequest (independent, FK→unit)
- Payment (extends existing Payment model)

Key migrations:
  properties: name(VARCHAR), address(TEXT), city(VARCHAR), province(VARCHAR), type(VARCHAR)
  units: unit_number(VARCHAR), floor(INT), bedrooms(INT), bathrooms(INT), rent_amount(DECIMAL), status(VARCHAR)
  tenants: lease_start(DATE), lease_end(DATE), deposit_amount(DECIMAL), emergency_contact(VARCHAR)
  leases: start_date(DATE), end_date(DATE), rent_amount(DECIMAL), deposit(DECIMAL), status(VARCHAR)
  maintenance_requests: request_date(DATETIME), description(TEXT), priority(VARCHAR), status(VARCHAR), cost(DECIMAL)

Controller additions:
- LeaseController: renew(), terminate(), generateInvoice()
- MaintenanceController: assignWorker(), updateStatus(), costReport()

Form helpers needed:
- Property/unit cascading dropdown
- Lease term calculator
- Rent escalation formula in settings
```

### 6.4 [Retail / Point of Sale]

```
Entities:
- Product (independent)
- Category (independent)
- Supplier (role-specific or independent)
- Inventory (independent, FK→product)
- Sale (independent, FK→user)
- SaleItem (junction: sale + product)
- Customer (role-specific, FK→users)

Key migrations:
  products: sku(VARCHAR), name(VARCHAR), description(TEXT), selling_price(DECIMAL), cost_price(DECIMAL), tax_rate(DECIMAL)
  categories: name(VARCHAR), description(TEXT), parent_id(INT->FK)
  suppliers: supplier_name(VARCHAR), contact_person(VARCHAR), email(VARCHAR), phone(VARCHAR)
  inventory: product_id(INT->FK), quantity(INT), min_stock(INT), location(VARCHAR), batch_number(VARCHAR)
  sales: sale_date(DATETIME), total_amount(DECIMAL), tax_amount(DECIMAL), discount(DECIMAL), payment_method(VARCHAR)
  sale_items: sale_id(INT->FK), product_id(INT->FK), quantity(INT), unit_price(DECIMAL), subtotal(DECIMAL)

Controller additions:
- PosController: newSale(), addItem(), removeItem(), checkout(), printReceipt()
- InventoryController: stockAdjust(), lowStockReport(), batchImport()
- SupplierController: purchaseOrder(), paymentTracking()

Form helpers needed:
- Barcode/QR scanner input
- Product search autocomplete
- Quantity increment/decrement buttons
- Payment split (cash + card)

Dashboard metrics:
- Today's sales total
- Low stock alerts
- Top-selling products
- Revenue by payment method (reuse existing Payment model)
```

### 6.5 [Logistics / Fleet Management]

```
Entities:
- Vehicle (independent)
- Driver (role-specific, FK→users)
- Route (independent)
- Trip (independent, FK→vehicle + FK→driver)
- Maintenance (independent, FK→vehicle)
- FuelRecord (independent, FK→vehicle)

Key migrations:
  vehicles: registration_no(VARCHAR), make(VARCHAR), model(VARCHAR), year(INT), capacity(DECIMAL), status(VARCHAR)
  drivers: license_no(VARCHAR), license_expiry(DATE), employee_number(VARCHAR), medical_cert_expiry(DATE)
  routes: name(VARCHAR), origin(VARCHAR), destination(VARCHAR), distance(DECIMAL), estimated_duration(INT)
  trips: trip_date(DATETIME), route_id(INT->FK), vehicle_id(INT->FK), driver_id(INT->FK), status(VARCHAR), notes(TEXT)
  fuel_records: vehicle_id(INT->FK), fuel_date(DATE), liters(DECIMAL), cost(DECIMAL), odometer(INT), station(VARCHAR)

Controller additions:
- TripController: dispatch(), completeTrip(), delayReport()
- FuelController: logFill(), fuelEfficiencyReport()
- MaintenanceController: scheduleService(), serviceHistory()

Dashboard metrics:
- Active trips count
- Fuel efficiency (km/L)
- Vehicles due for service
- Driver hours summary
```

### 6.6 [Finance / Accounting]

```
Entities:
- Account (independent)
- Transaction (independent, FK→account)
- Invoice (independent, FK→client)
- ExpenseCategory (independent)
- Budget (independent, FK→expense_category)
- AuditLog (independent)

Key migrations:
  accounts: account_name(VARCHAR), account_type(VARCHAR), account_number(VARCHAR), balance(DECIMAL), currency(VARCHAR)
  transactions: transaction_date(DATETIME), account_id(INT->FK), type(VARCHAR), amount(DECIMAL), description(TEXT), reference(VARCHAR)
  invoices: invoice_number(VARCHAR), client_id(INT->FK), issue_date(DATE), due_date(DATE), total(DECIMAL), status(VARCHAR), tax_amount(DECIMAL)
  budgets: fiscal_year(YEAR), category(VARCHAR), allocated_amount(DECIMAL), spent_amount(DECIMAL)

Controller additions:
- InvoiceController: generate(), sendEmail(), markPaid(), downloadPDF()
- ReportController: profitLoss(), balanceSheet(), cashFlow(), taxSummary()
- BudgetController: createBudget(), varianceReport()

Integration:
- Reuse Payment model for payment tracking
- Extend Expenditure model with expense categories
- Use existing Logger for audit trail (Logger::audit())

Dashboard metrics:
- Accounts receivable/payable
- Monthly revenue vs expenses (reuse ChartHelper)
- Budget utilization
- Overdue invoices
```

### 6.7 [HR / Staff Management]

```
Entities:
- Employee (role-specific, FK→users) — already exists!
- Department (independent)
- Position (independent)
- Leave (independent, FK→employee)
- Attendance (independent, FK→employee)
- Performance (independent, FK→employee)

Key migrations:
  departments: name(VARCHAR), code(VARCHAR), manager_id(VARCHAR->FK), budget(DECIMAL)
  positions: title(VARCHAR), department_id(INT->FK), salary_range_min(DECIMAL), salary_range_max(DECIMAL)
  leaves: employee_id(VARCHAR->FK), leave_type(VARCHAR), start_date(DATE), end_date(DATE), status(VARCHAR), reason(TEXT), approved_by(VARCHAR)
  attendance: employee_id(VARCHAR->FK), date(DATE), clock_in(TIME), clock_out(TIME), status(VARCHAR), notes(TEXT)
  performance_reviews: employee_id(VARCHAR->FK), review_date(DATE), reviewer(VARCHAR), rating(INT), comments(TEXT), goals(TEXT)

Controller additions:
- LeaveController: apply(), approve(), reject(), balanceReport()
- AttendanceController: clockIn(), clockOut(), monthlyReport(), lateReport()
- PerformanceController: addReview(), employeeGoals(), departmentAverage()

Form helpers needed:
- Leave type dropdown (from constants or settings)
- Performance rating (1-5 star)
- Department head selection

Note: Employee model already exists. Extend it rather than creating a new one.
```

### 6.8 [Hospitality / Hotel Management]

```
Entities:
- Guest (role-specific, FK→users)
- Room (independent)
- Booking (independent, FK→guest + FK→room)
- Service (independent)
- BookingService (junction: booking + service)
- Housekeeping (independent, FK→room)

Key migrations:
  guests: id_document(VARCHAR), nationality(VARCHAR), vip_status(BOOLEAN), preferences(TEXT)
  rooms: room_number(VARCHAR), room_type(VARCHAR), floor(INT), rate(DECIMAL), capacity(INT), status(VARCHAR), amenities(TEXT)
  bookings: guest_id(VARCHAR->FK), room_id(INT->FK), check_in(DATE), check_out(DATE), status(VARCHAR), total_amount(DECIMAL), deposit_paid(DECIMAL)
  services: name(VARCHAR), description(TEXT), price(DECIMAL), category(VARCHAR)
  housekeeping: room_id(INT->FK), schedule_date(DATE), assigned_to(VARCHAR), status(VARCHAR), notes(TEXT)

Controller additions:
- BookingController: newBooking(), checkIn(), checkOut(), cancelBooking(), availability()
- HousekeepingController: schedule(), completeTask(), inspectionReport()

Dashboard metrics:
- Occupancy rate
- Revenue per available room (RevPAR)
- Check-ins/check-outs today
- Housekeeping pending tasks
```

### 6.9 [Agriculture / Farm Management]

```
Entities:
- Parcel/Field (independent)
- Crop (independent)
- PlantingSeason (independent, FK→parcel + FK→crop)
- Harvest (independent, FK→planting)
- Livestock (independent)
- Inventory (independent, FK→supplier)
- Worker (role-specific, FK→users)

Key migrations:
  parcels: name(VARCHAR), size_hectares(DECIMAL), soil_type(VARCHAR), gps_coordinates(VARCHAR), status(VARCHAR)
  crops: name(VARCHAR), variety(VARCHAR), growing_days(INT), expected_yield_kg(DECIMAL)
  planting_seasons: parcel_id(INT->FK), crop_id(INT->FK), planting_date(DATE), expected_harvest_date(DATE), seeds_used(DECIMAL), status(VARCHAR)
  harvests: season_id(INT->FK), harvest_date(DATE), quantity_kg(DECIMAL), quality_grade(VARCHAR), notes(TEXT)
  livestock: tag_number(VARCHAR), species(VARCHAR), breed(VARCHAR), birth_date(DATE), weight(DECIMAL), health_status(VARCHAR)

Controller additions:
- PlantingController: planSeason(), recordPlanting(), updateGrowthStage()
- HarvestController: recordHarvest(), yieldAnalysis(), qualityReport()
- LivestockController: addAnimal(), healthRecord(), breedingSchedule()

Dashboard metrics:
- Active fields/parcels
- Upcoming harvests
- Livestock health summary
- Crop yield vs projection
```

### 6.10 [Membership / Club Management]

```
Entities:
- Member (role-specific, FK→users)
- MembershipTier (independent)
- Subscription (independent, FK→member + FK→tier)
- Event (independent)
- EventRegistration (junction: event + member)
- Payment (reuse existing + FK→member)

Key migrations:
  members: membership_number(VARCHAR), join_date(DATE), referral_code(VARCHAR), emergency_contact(VARCHAR), interests(TEXT)
  membership_tiers: name(VARCHAR), description(TEXT), price(DECIMAL), duration_days(INT), benefits(TEXT), max_guests(INT)
  subscriptions: member_id(VARCHAR->FK), tier_id(INT->FK), start_date(DATE), end_date(DATE), status(VARCHAR), auto_renew(BOOLEAN), payment_method(VARCHAR)
  events: name(VARCHAR), description(TEXT), event_date(DATETIME), venue(VARCHAR), capacity(INT), price(DECIMAL), status(VARCHAR)
  event_registrations: event_id(INT->FK), member_id(VARCHAR->FK), registration_date(DATETIME), guests_count(INT), status(VARCHAR)

Controller additions:
- SubscriptionController: renew(), upgrade(), cancel(), expiryReport()
- EventController: createEvent(), manageRegistrations(), checkIn()
- MemberController: memberCard(), referralTracking(), benefitsSummary()

Dashboard metrics:
- Active members count
- Membership revenue (reuse Payment integration)
- Upcoming events attendance
- Expiring subscriptions this month
- Tier distribution
```

---

## 7. CLI Command Reference

```bash
# ── Controller Generation ──
php jongi make:controller [Name]Controller
# Creates app/controllers/[Name]Controller.php with index/create/edit/delete stubs

# ── Model Generation ──
php jongi make:model [Name]
# Creates app/models/[Name].php with Model trait, $table, $allowedColumns, validate()

# ── Migration Generation ──
php jongi make:migration [Name]
# Creates app/migrations/Ntoshi_[date]_[time]_[Name].php with alpha()/omega()

# ── Migration Execution ──
php jongi migrate [Filename]          # Run alpha() (create table)
php jongi migrate:all                 # Run ALL pending migrations
php jongi migrate:rollback [Filename] # Run omega() (drop table)
php jongi migrate:refresh [Filename]  # Rollback + re-run
php jongi list:migrations             # List all migrations

# ── Database Management ──
php jongi db:create [dbname]          # Create a database
php jongi db:drop [dbname]            # Drop a database
php jongi db:table [tablename]        # Describe table structure

# ── Development Server ──
php jongi spinit [port]               # Start PHP dev server (default: 5001)

# ── Help ──
php jongi help                        # Show all commands
```

---

## 8. Security Checklist

Every project must verify these before going to production:

### Authentication & Authorization
- [ ] Password reset tokens expire (already built: 30-min expiry in `Auth.php`)
- [ ] Session timeout enforced (already built: `SESSION_LIFETIME` in `AuthMiddleware.php`)
- [ ] Session ID regenerated periodically (already built: every 5 min)
- [ ] CSRF protection on all POST/PUT/DELETE forms (already built in middleware + Validator)
- [ ] Role-based access control on admin routes (use `RoleMiddleware` for granular control)

### Input Validation
- [ ] All user inputs pass through `Validator` or model `validate()` methods
- [ ] XSS prevention via `esc()` in all view output (already built: `functions.php`)
- [ ] File upload limits enforced (already built: `MAX_FILE_SIZE`, `ALLOWED_FILE_TYPES`)
- [ ] SQL injection prevented via PDO prepared statements (already built across all queries)

### Data Protection
- [ ] Passwords hashed with `password_hash(PASSWORD_DEFAULT)` (already built)
- [ ] Mass assignment prevented via `$allowedColumns` (already built in Model trait)
- [ ] Sensitive data excluded from debug output in production (already built in Database.php)
- [ ] `.env` file excluded from version control (add to `.gitignore`)

### Rate Limiting
- [ ] Login attempts rate-limited (use `RateLimitMiddleware` on auth routes)
- [ ] API endpoints rate-limited (use `RateLimitMiddleware` with custom params)

### Production Hardening
- [ ] `DEBUG=false` in `.env`
- [ ] `APP_ENV=production` in `.env`
- [ ] Error logging enabled (already built: `Logger` class, `logs/` directory)
- [ ] File upload directory outside web root if sensitive uploads
- [ ] HTTPS enforced via `.htaccess` or server config
- [ ] Regular log rotation configured (already built: `Logger::cleanup()`, `cleanup_logs.php`)

---

## 9. Deployment Checklist

### Pre-Deployment
- [ ] Run all migrations on production database: `php jongi migrate:all`
- [ ] Set proper file permissions (755 for dirs, 644 for files)
- [ ] Configure `.env` with production values
- [ ] Test all CRUD operations for every entity
- [ ] Verify middleware protection on admin routes
- [ ] Test password reset flow end-to-end
- [ ] Check error pages render correctly (404, 500, 403)
- [ ] Verify visitor tracking disabled in production if not needed
- [ ] Clear logs directory: `rm logs/*.log`
- [ ] Clean up installation files if needed (remove `install.php` or restrict access)

### Server Requirements
- PHP 8.0+ (8.1+ recommended)
- MySQL 5.7+ or MariaDB 10.3+
- Extensions: `pdo`, `pdo_mysql`, `gd`, `curl`, `fileinfo`, `mbstring`, `json`, `intl`, `exif`
- Apache with `mod_rewrite` (or Nginx with URL rewriting)
- SSL certificate for HTTPS

### .htaccess for production (ensure active, not `.htaccess-bkp`)
- The framework auto-detects URL rewriting and adjusts `ROOT` constant
- The `.htaccess` in project root rewrites to `public/` directory
- The `.htaccess-bkp` is a backup and should NOT be the active file

---

## 10. Troubleshooting & FAQ

### "Class not found" error
- Check that the model file is named correctly: `app/models/[ClassName].php`
- Class name must match filename (case-sensitive on Linux)
- The autoloader in `init.php` loads from `app/models/`

### "Route not found" (404)
- Check `app/config/routes.php` for the correct URL pattern
- Route keys must match the URL segment pattern
- Middleware keys must be arrays, e.g., `'middleware' => ['AuthMiddleware']`
- Check for missing `{param}` in route vs controller method signature

### "View not found" error
- View file must be at `app/views/[path].ntoshi.php` (note the `.ntoshi.php` extension)
- Controller calls `$this->view('path/to/view', $data)` without the extension
- Check that the directory exists

### Database connection errors
- Verify `.env` has correct `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
- Check MySQL is running and accessible from your PHP server
- The `Database` trait will log errors to `logs/database.log`
- Verify charset: `utf8mb4` requires MySQL 5.7+ or MariaDB 10.2+

### Migration fails
- Check table doesn't already exist (run `omega()` first to drop)
- Verify column types and syntax match MySQL requirements
- Run `php jongi list:migrations` to see all migration files
- The `alpha()` method creates tables, `omega()` drops them

### CLI commands not working
- Must run from project root directory
- Must use PHP CLI: `php jongi [command]`
- CLI mode check at top of `jongi` script

### User assignment dropdown is empty
- Ensure at least one user exists in the `users` table
- Check the filtering logic: users already assigned to this role are excluded
- Verify the `user_id` column exists in both `users` and the role-specific table
- The user dropdown shows users who are NOT yet assigned to this role

---

## Appendix: Common Patterns Quick Reference

### Pattern: CRUD Controller (standard 4-method)

```php
index(): void   →  GET    /admin/[entities]              →  list view
create(): void  →  GET    /admin/[entities]/create        →  form view
                →  POST   /admin/[entities]/create        →  validate → insert → redirect
edit($id): void →  GET    /admin/[entities]/edit/{id}     →  form view with $row
                →  POST   /admin/[entities]/edit/{id}     →  validate → update → redirect
delete($id): void → GET  /admin/[entities]/delete/{id}    →  confirm view with $row
                  → POST  /admin/[entities]/delete/{id}    →  delete → redirect
```

### Pattern: User Role Assignment Flow

```
1. User registers or admin creates user → users table (user_role = 'User')
2. Admin navigates to role-specific section (e.g., admin/employees)
3. "Create" shows dropdown of users NOT yet assigned to that role
4. Admin selects user + fills role-specific fields
5. Form submits → role table gets row with FK→users.user_id
6. Optional: update user_role in users table
```

### Pattern: View Templates

```
List view:    header + table/cards + footer     →  foreach($rows as $row)
Create form:  header + <form method="post"> + footer
Edit form:    header + <form> with values + footer  →  value="<?= esc($row->field) ?>"
Delete view:  header + confirm message + form   →  shows entity identity
```

### Pattern: Flash Messages

```php
// Set message (in controller)
Util::setFlash('success', 'Operation completed!');

// Display message (in view — auto-included in header template)
// The flash message appears as a dismissible Bootstrap alert and auto-hides after 5 seconds

// Redirect after operation
redirect('admin/[entities]');
```

### Pattern: Form CSRF + Audit Fields

Always use the three `displayFormHeader*()` functions to inject CSRF tokens and audit fields:

```php
// On CREATE forms:
<?= displayFormHeaderOnCreate() ?>
// Injects: csrf_token, created_by (current user's name)

// On UPDATE forms:
<?= displayFormHeaderOnUpdate() ?>
// Injects: csrf_token, updated_by, date_updated

// On DELETE forms:
<?= displayFormHeaderOnDelete() ?>
// Injects: csrf_token, deleted_by, date_deleted
```

### Pattern: Custom Query in Model

```php
public function getFilteredData(string $status, string $dateFrom, string $dateTo): array
{
    return $this->query("
        SELECT r.*, u.firstname, u.surname
        FROM $this->table r
        LEFT JOIN users u ON r.user_id = u.user_id
        WHERE r.status = ?
        AND r.date_created >= ?
        AND r.date_created <= ?
        ORDER BY r.id DESC
    ", [$status, $dateFrom, $dateTo]) ?: [];
}
```

### Pattern: Dashboard Aggregate Query

```php
public function getDashboardStats(): object|false
{
    $result = $this->query("
        SELECT
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
        FROM $this->table
    ");
    return $result ? $result[0] : false;
}
```

### Pattern: Settings Access

```php
// Load all settings once
$settings = new Settings();
$appSettings = $settings->loadSettings();

// Get individual setting
$value = $appSettings['key_name'] ?? 'default';

// Set a setting
$settings->set('key_name', 'value');
```

---

## The Front End:
Drawing inspiration from the html below (ONLY AS A DEMO AND NOT ALIGNED IN ANY WAY WITH THE CURRENT PROJECT), refactor the home.ntoshi.php view file in 'app/views/front' such that it displays the relevant front facing content of the project (ie in the case of CMS that are non-internal management apps like accounting system, business crm, etc...) The front page should include sections that populates data database for modules worthy of displaying in the front end, for example 'Upcoming Events' - dynamically queried, blog posts (where applicable), upcoming funeral (in the case of a funeral home), etc...Other sections are, about, contact, operating times, social links, all of which are drawn from the available module "Company Details", others should be testimonials and the 'OpenMaps' section defaulted to the address of:
Jongi Brands Tech Solutions
Office no 19, Second Floor, Harmony Building 
14 Market Street, North End
Gqeberha, EC
South Africa
6070

This nay be dynamically display using the "$company_details = new CompanyDetail" object, [$company_details[0]->about, $company_details[0]->address, $company_details[0]->name] etc...
Make sure to migrate the following for this to take effect:

 - php jongi migrate Ntoshi_18th_Feb_2024_01_50_33_CompanyDetails.php
 - php jongi migrate Ntoshi_19th_Feb_2024_06_16_25_OpHours.php
 - php jongi migrate Ntoshi_20th_Feb_2024_02_24_21_SocialLinks.php

otherwise you will get an error. Do this for all pages that return "page not found" while you have its controller, accurately set route, and views.

```html
<?php

/**
 * @var string $page_title
 * @var array $data
 * @var array $partners
 * @var int $totalPartners
 */
?>

<?php $this->view('inc/header', $data); ?>
<div class="mx-2">

    <!-- Hero Section -->
    <div class="glass-card text-center mb-5">
        <i class="fas fa-briefcase" style="font-size: 3.5rem; color: #2dd4bf;"></i>
        <h1 class="gradient-text mt-3">Join the Digital Sovereignty Movement <br> <small style="font-size: 1.7rem;">As Our Marketing Partner</small> </h1>
        <p class="lead mt-3"><strong>Partner with us to earn up to 50% upfront commission + 3.8 <i class="fas fa-percent"></i> (6 mnths) Revenue Share.</strong> No costs, no inventory, no limits. Join South Africa's digital sovereignty movement today.</p>
        <?php if (empty(user())): ?>
            <div class="mt-4">
                <a href="<?= ROOT . '/auth/register' ?>" class="btn-primary">Create Account</a>
                <a href="<?= ROOT . '/auth/login' ?>" class="btn-outline ms-3">Sign In</a>
            </div>
        <?php endif; ?>
    </div>

    <?= Util::displayFlash('application_submitted', 'success') ?>
    <?= Util::displayFlash('no_access', 'danger') ?>
    <?= Util::displayFlash('partner_success', 'success') ?>
    <?= Util::displayFlash('partner_error', 'danger') ?>

    <!-- ============================================ -->
    <!-- PARTNERSHIP OPPORTUNITIES SECTION            -->
    <!-- ============================================ -->
    <section class="mb-5" id="partnerships">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="gradient-text">
                <i class="fas fa-handshake" style="color: #f59e0b;"></i>
                Partner With Us
            </h2>
            <div>
                <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; padding: 8px 16px; margin-right: 10px;">
                    <i class="fas fa-percent"></i> 50% Commission
                </span>
                <?php
                switch (user('user_role')) {
                    case 'Marketing Partner': ?>
                        <a href="<?= ROOT . '/partner/dashboard' ?>" class="jbts-btn-success" style="padding: 8px 20px; font-size: 0.85rem;">
                            <i class="fas fa-rocket"></i> Partner Portal/Dashboard
                        </a>
                    <?php
                        break;

                    default: ?>
                        <a href="<?= ROOT . '/marketing-partners' ?>" class="btn-warning" style="padding: 8px 20px; font-size: 0.85rem;">
                            <i class="fas fa-rocket"></i> Apply as Partner
                        </a>
                <?php
                        break;
                }
                ?>


            </div>
        </div>

        <!-- Partner Program Overview Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="glass-card text-center p-3" style="border-left: 3px solid #f59e0b;">
                    <i class="fas fa-coins" style="font-size: 2rem; color: #f59e0b;"></i>
                    <h4 class="mt-2" style="color: #f59e0b;">30-50%</h4>
                    <p class="small text-muted">Upfront Commission per sale</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card text-center p-3" style="border-left: 3px solid #2dd4bf;">
                    <i class="fas fa-sync-alt" style="font-size: 2rem; color: #2dd4bf;"></i>
                    <h4 class="mt-2" style="color: #2dd4bf;">3.8 <i class="fas fa-percent"></i></h4>
                    <p class="small text-muted">Recurring Revenue Share</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card text-center p-3" style="border-left: 3px solid #a78bfa;">
                    <i class="fas fa-zero" style="font-size: 2rem; color: #a78bfa;"></i>
                    <h4 class="mt-2" style="color: #a78bfa;">R0</h4>
                    <p class="small text-muted">No Upfront Costs</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card text-center p-3" style="border-left: 3px solid #f472b6;">
                    <i class="fas fa-users" style="font-size: 2rem; color: #f472b6;"></i>
                    <h4 class="mt-2" style="color: #f472b6;">Unlimited</h4>
                    <p class="small text-muted">Earning Potential</p>
                </div>
            </div>
        </div>

        <!-- Partner Feature Cards -->
        <div class="row g-4">
            <!-- Partner Type 1: IT Consultants -->
            <div class="col-md-4">
                <div class="glass-card h-100" style="border-top: 3px solid #f59e0b;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; font-size: 0.75rem;">
                            <i class="fas fa-laptop"></i> IT Consultants & MSPs
                        </span>
                        <span class="badge" style="background: rgba(45,212,191,0.2); color: #2dd4bf;">
                            <i class="fas fa-percent"></i> 30-50% Commission
                        </span>
                    </div>
                    <h4 class="fs-5 mb-2">Independent IT Consultants</h4>
                    <p class="small text-muted" style="opacity: 0.8;">
                        Sell The Magic USB ecosystem to your existing SMME clients. Earn high-margin commissions on every sale plus recurring revenue.
                    </p>
                    <ul class="small" style="list-style: none; padding-left: 0;">
                        <li><i class="fas fa-check-circle" style="color: #2dd4bf;"></i> No inventory required</li>
                        <li><i class="fas fa-check-circle" style="color: #2dd4bf;"></i> Zero subscription fees for clients</li>
                        <li><i class="fas fa-check-circle" style="color: #2dd4bf;"></i> POPIA-compliant solution</li>
                    </ul>
                    <a href="<?= ROOT . '/marketing-partners' ?>" class="btn-warning w-100 text-center" style="padding: 8px; font-size: 0.85rem; margin-top: 10px;">
                        <i class="fas fa-handshake"></i> Partner Now
                    </a>
                </div>
            </div>

            <!-- Partner Type 2: B2B Growth Specialists -->
            <div class="col-md-4">
                <div class="glass-card h-100" style="border-top: 3px solid #a78bfa;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge" style="background: rgba(167, 139, 250, 0.2); color: #a78bfa; font-size: 0.75rem;">
                            <i class="fas fa-chart-line"></i> B2B Growth Specialists
                        </span>
                        <span class="badge" style="background: rgba(45,212,191,0.2); color: #2dd4bf;">3.8 <i class="fas fa-percent"></i> (6 mnths) Revenue Share

                        </span>
                    </div>
                    <h4 class="fs-5 mb-2">B2B Sales & Growth Agents</h4>
                    <p class="small text-muted" style="opacity: 0.8;">
                        Leverage your network of business owners and decision-makers. Offer a unique offline-first private cloud solution that solves real problems.
                    </p>
                    <ul class="small" style="list-style: none; padding-left: 0;">
                        <li><i class="fas fa-check-circle" style="color: #2dd4bf;"></i> Recurring passive income</li>
                        <li><i class="fas fa-check-circle" style="color: #2dd4bf;"></i> High-value enterprise clients</li>
                        <li><i class="fas fa-check-circle" style="color: #2dd4bf;"></i> No technical expertise needed</li>
                    </ul>
                    <a href="<?= ROOT . '/marketing-partners' ?>" class="btn-primary w-100 text-center" style="padding: 8px; font-size: 0.85rem; margin-top: 10px;">
                        <i class="fas fa-handshake"></i> Partner Now
                    </a>
                </div>
            </div>

            <!-- Partner Type 3: Digital Marketers -->
            <div class="col-md-4">
                <div class="glass-card h-100" style="border-top: 3px solid #f472b6;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge" style="background: rgba(244, 114, 182, 0.2); color: #f472b6; font-size: 0.75rem;">
                            <i class="fas fa-bullhorn"></i> Digital Marketers
                        </span>
                        <span class="badge" style="background: rgba(45,212,191,0.2); color: #2dd4bf;">
                            <i class="fas fa-percent"></i> CPA + Revenue Share
                        </span>
                    </div>
                    <h4 class="fs-5 mb-2">Digital Marketing Partners</h4>
                    <p class="small text-muted" style="opacity: 0.8;">
                        Run targeted campaigns to generate qualified leads. Earn CPA payouts plus recurring revenue from converted clients.
                    </p>
                    <ul class="small" style="list-style: none; padding-left: 0;">
                        <li><i class="fas fa-check-circle" style="color: #2dd4bf;"></i> Performance-based payouts</li>
                        <li><i class="fas fa-check-circle" style="color: #2dd4bf;"></i> Clear attribution tracking</li>
                        <li><i class="fas fa-check-circle" style="color: #2dd4bf;"></i> Ready-made campaign assets</li>
                    </ul>
                    <a href="<?= ROOT . '/marketing-partners' ?>" class="btn-outline w-100 text-center" style="padding: 8px; font-size: 0.85rem; margin-top: 10px;">
                        <i class="fas fa-handshake"></i> Partner Now
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Partners Counter -->
        <?php if (isset($totalPartners) && $totalPartners > 0): ?>
            <div class="mt-4 text-center">
                <span class="badge" style="background: rgba(45,212,191,0.1); color: #2dd4bf; padding: 8px 20px;">
                    <i class="fas fa-users"></i> <?= $totalPartners ?> Active Partners Already Onboarded
                </span>
            </div>
        <?php endif; ?>
    </section>

    <!-- Divider Between Partnerships and Jobs -->
    <hr class="my-5" style="border-color: rgba(0,255,255,0.1);">


    <!-- Call to Action: Partner vs Job -->
    <div class="glass-card text-center mt-5" style="background: linear-gradient(135deg, rgba(245,158,11,0.05), rgba(45,212,191,0.05));">
        <div class="row g-4 align-items-center">
            <div class="col-md-6">
                <i class="fas fa-handshake" style="font-size: 2.5rem; color: #f59e0b;"></i>
                <h4 class="mt-2">Prefer Partnership?</h4>
                <p class="small text-muted">Earn high commissions and recurring revenue</p>
                <a href="<?= ROOT . '/marketing-partners' ?>" class="btn-warning" style="padding: 10px 30px;">
                    <i class="fas fa-rocket"></i> Become a Partner
                </a>
            </div>
            <div class="col-md-6">
                <i class="fas fa-briefcase" style="font-size: 2.5rem; color: #2dd4bf;"></i>
                <h4 class="mt-2">Looking for a Job?</h4>
                <p class="small text-muted">Join our team and build your career</p>
                <a href="https://careers.jongibrandz.co.za" target="_blank" class="btn-primary" style="padding: 10px 30px;">
                    <i class="fas fa-search"></i> Browse Jobs
                </a>
            </div>
        </div>
    </div>
</div>

<?php $this->view('inc/footer'); ?>
```
--- 

> **Final Note:** Every business application ever built can be modeled using this framework's
> User-Centric Identity Model. Whether you're managing patients, students, tenants,
> drivers, members, or employees — the pattern is identical: **User first, role second.**
> Stick to this rule and NtoshiSoft will scale with your business across any sector.
