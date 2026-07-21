<?php
/**
 * CommIntel SA - Command Centre Header
 * @var string $page_title
 * @var string $threat_level
 * @var int $unread_alerts
 * @var int $unread_notifications
 */
$settings = new Settings();
$appSettings = $settings->loadSettings();
$siteName = $appSettings['site_name'] ?? 'CommIntel SA';
$threat = $threat_level ?? 'AMBER';
$unreadAlerts = $unread_alerts ?? 0;
$unreadNotifs = $unread_notifications ?? 0;
$currentRoute = $_GET['url'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title ?? 'Command Centre') ?> – CommIntel SA</title>
    <link rel="icon" href="<?= ROOT . '/assets/img/logos/favicon.png' ?>" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/sentinel/command-centre.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body class="sentinel-body">

<div class="sentinel-sidebar-overlay" id="sentinel-overlay"></div>

<div class="sentinel-wrapper">

    <!-- ============================================
         SIDEBAR
         ============================================ -->
    <aside class="sentinel-sidebar" id="sentinel-sidebar">
        <div class="sentinel-sidebar-header">
            <div class="sentinel-sidebar-logo">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="sentinel-sidebar-brand">
                Sentinel SA
                <small>Command Centre</small>
            </div>
        </div>

        <nav class="sentinel-sidebar-nav">
            <div class="sentinel-nav-section">Operations</div>
            <a href="<?= ROOT ?>/admin/sentinel/dashboard" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/dashboard') || $currentRoute == 'admin/sentinel' || $currentRoute == 'admin' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/map" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/map') ? 'active' : '' ?>">
                <i class="fa-solid fa-map"></i> Intel Map
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/incidents" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/incidents') ? 'active' : '' ?>">
                <i class="fa-solid fa-circle-exclamation"></i> Incidents
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/cases" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/cases') ? 'active' : '' ?>">
                <i class="fa-solid fa-briefcase"></i> Cases
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/alerts" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/alerts') ? 'active' : '' ?>">
                <i class="fa-solid fa-bell"></i> Alerts
                <?php if ($unreadAlerts > 0): ?>
                <span class="sentinel-badge sentinel-badge-danger sentinel-nav-badge"><?= $unreadAlerts ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/tasks" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/tasks') ? 'active' : '' ?>">
                <i class="fa-solid fa-list-check"></i> Tasks
            </a>

            <div class="sentinel-nav-section">Intelligence</div>
            <a href="<?= ROOT ?>/admin/sentinel/observations" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/observations') ? 'active' : '' ?>">
                <i class="fa-solid fa-eye"></i> Observations
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/persons" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/persons') ? 'active' : '' ?>">
                <i class="fa-solid fa-user"></i> Persons of Interest
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/vehicles" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/vehicles') ? 'active' : '' ?>">
                <i class="fa-solid fa-car"></i> Vehicles of Interest
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/notes" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/notes') ? 'active' : '' ?>">
                <i class="fa-solid fa-brain"></i> Intel Notes
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/threats" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/threats') ? 'active' : '' ?>">
                <i class="fa-solid fa-shield"></i> Threat Assessments
            </a>

            <div class="sentinel-nav-section">Registry</div>
            <a href="<?= ROOT ?>/admin/sentinel/persons" class="sentinel-nav-item">
                <i class="fa-solid fa-users-between-lines"></i> Watch Lists
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/evidence" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/evidence') ? 'active' : '' ?>">
                <i class="fa-solid fa-box"></i> Evidence
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/locations" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/locations') ? 'active' : '' ?>">
                <i class="fa-solid fa-location-dot"></i> Locations
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/reports" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/reports') ? 'active' : '' ?>">
                <i class="fa-solid fa-file"></i> Intelligence Reports
            </a>

            <div class="sentinel-nav-section">Personnel</div>
            <a href="<?= ROOT ?>/admin/sentinel/officers" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/officers') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-tie"></i> Intel Officers
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/agents" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/agents') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-secret"></i> Field Agents
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/members" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/members') ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Community Members
            </a>

            <div class="sentinel-nav-section">System</div>
            <a href="<?= ROOT ?>/admin/sentinel/geofences" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/geofences') ? 'active' : '' ?>">
                <i class="fa-solid fa-draw-polygon"></i> Geofences
            </a>
            <a href="<?= ROOT ?>/admin/sentinel/activity" class="sentinel-nav-item <?= str_contains($currentRoute, 'sentinel/activity') ? 'active' : '' ?>">
                <i class="fa-solid fa-clock-rotate-left"></i> Activity Log
            </a>
            <a href="<?= ROOT ?>/admin/settings" class="sentinel-nav-item">
                <i class="fa-solid fa-gear"></i> Settings
            </a>
        </nav>
    </aside>

    <!-- ============================================
         MAIN CONTENT AREA
         ============================================ -->
    <div class="sentinel-main">

        <!-- Top Bar -->
        <div class="sentinel-topbar">
            <div class="sentinel-topbar-left">
                <button class="sentinel-toggle-btn" id="sentinel-toggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="sentinel-breadcrumb">
                    <a href="<?= ROOT ?>/admin/sentinel/dashboard">Command Centre</a>
                    <span class="separator">/</span>
                    <span><?= esc($page_title ?? 'Dashboard') ?></span>
                </div>
            </div>
            <div class="sentinel-topbar-right">
                <div class="sentinel-status-bar">
                    <span class="sentinel-status-dot <?= $threat === 'CRITICAL' ? 'danger' : ($threat === 'HIGH' || $threat === 'AMBER' ? 'warning' : '') ?>"></span>
                    <span>THREAT LEVEL <?= $threat ?></span>
                </div>
                <a href="<?= ROOT ?>/admin/sentinel/notifications" class="sentinel-btn sentinel-btn-secondary sentinel-btn-sm position-relative">
                    <i class="fa-solid fa-bell"></i>
                    <?php if ($unreadNotifs > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.55rem;"><?= $unreadNotifs ?></span>
                    <?php endif; ?>
                </a>
                <div class="ns-dropdown">
                    <button class="ns-dropdown-anchor text-secondary" style="padding:0.3rem 0.75rem;font-size:0.8rem;">
                        <img src="<?= get_image(user('image'), 'user') ?>" class="ns-avatar ns-avatar-sm" alt="">
                        <span class="d-none d-md-inline"><?= esc(user('firstname')) ?></span>
                    </button>
                    <div class="ns-dropdown-menu">
                        <a href="<?= ROOT ?>/admin/users/profile/<?= user('id') ?>" class="ns-dropdown-item"><i class="fa-solid fa-user"></i> Profile</a>
                        <a href="<?= ROOT ?>/admin" class="ns-dropdown-item"><i class="fa-solid fa-building"></i> Main Dashboard</a>
                        <hr style="border-color:var(--sentinel-glass-border);margin:0.25rem 0;">
                        <a href="<?= ROOT ?>/auth/logout" class="ns-dropdown-item"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="sentinel-content">
