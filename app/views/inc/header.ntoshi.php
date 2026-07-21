<?php

/** @var string $page_title */
/** @var ?string $meta_description */
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= esc($page_title ?? 'Home') ?> – <?= APP_NAME_SHORT ?></title>
    <link rel="icon" href="<?= ROOT . '/assets/img/logos/favicon.png' ?>" type="image/x-icon" />
    <!-- Open Graph / Social -->
    <meta property="og:title" content="<?= esc($page_title ?? APP_NAME_SHORT) ?>" />
    <meta property="og:description" content="<?= esc($meta_description ?? 'Send a voice message directly to us. Quick, personal, and easy — no typing required.') ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:locale" content="en_US" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= ROOT . '/assets/css/style.css' ?>">
   
</head>

<body>
    
    <div class="starfield" id="starfield"></div>
    <div class="theme-toggle mb-0" id="themeToggle">
        <i class="fas fa-moon" id="themeIcon"></i> <span id="themeLabel">Dark</span>
    </div>


    <div class="container">
        <?php
        switch (user()) {
            case '': ?>
                <nav class="navbar navbar-expand-lg navbar-light py-4" style="background: transparent;">
                    <div class="container">

                        <!-- Brand -->
                        <a class="navbar-brand fw-bold fs-4" href="<?= ROOT ?>">
                            <?php
                            $logos = glob(ROOTPATH . 'assets/img/logos/logo.*');
                            $logoSrc = !empty($logos) ? ROOT . '/assets/img/logos/' . basename($logos[0]) . '?v=' . filemtime($logos[0]) : ROOT . '/assets/img/logos/logo.svg';
                            ?>
                            <img src="<?= $logoSrc ?>" alt="NtoshiSoft  Logo" width="100%" style="max-height:50px;object-fit:contain;">
                        </a>

                        <!-- Mobile Toggle Button -->
                        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navMenu" aria-controls="navMenu"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <!-- Collapsible Menu -->
                        <div class="collapse navbar-collapse justify-content-end" id="navMenu">
                            <ul class="navbar-nav align-items-lg-center gap-lg-3">
                                <li class="nav-item mt-2 mt-lg-0">
                                    <a href="<?= ROOT . '/auth/login' ?>" class="btn btn-primary btn-sm px-3">Admin Login</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            <?php
                break;

            default: ?>
                <nav class="navbar navbar-expand-lg navbar-light py-3" style="background: transparent;">
                    <div class="container ">

                        <!-- Brand -->
                        <a class="navbar-brand fw-bold fs-4" href="<?= ROOT ?>">
                            <?php
                            $logos = glob(ROOTPATH . 'assets/img/logos/logo.*');
                            $logoSrc = !empty($logos) ? ROOT . '/assets/img/logos/' . basename($logos[0]) . '?v=' . filemtime($logos[0]) : ROOT . '/assets/img/logos/logo.svg';
                            ?>
                            <img src="<?= $logoSrc ?>" alt="NtoshiSoft  Logo" width="100%" style="max-height:50px;object-fit:contain;">
                        </a>

                        <!-- Mobile Toggle Button -->
                        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navMenu" aria-controls="navMenu"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <!-- Collapsible Menu -->
                        <div class="collapse navbar-collapse justify-content-end" id="navMenu">
                            <ul class="navbar-nav align-items-lg-center gap-lg-3">

                                <!-- User Dropdown -->
                                <div class="ns-dropdown mt-2">
                                    <button class="ns-dropdown-anchor text-secondary">
                                        <img src="<?= get_image(user('image'), 'user') ?>" alt="Profile" class="ns-avatar ns-avatar-sm"> &nbsp;
                                        <span class="d-md-inline"><?= user('') ? user('firstname') . ' ' . user('surname') : 'Guest' ?></span>
                                        <span class="ns-dropdown-chevron"><i class="fas fa-chevron-down"></i></span>
                                    </button>

                                    <div class="ns-dropdown-menu" style="z-index: 3000;">
                                        <a href="<?= ROOT . '/admin/users/profile/' . user('id') ?>" class="ns-dropdown-item">
                                            <i class="fas fa-user"></i> Profile
                                        </a>
                                    </div>
                                </div>
                                <?php
                                switch (user('user_role')) {
                                    case 'Admin': ?>
                                        
                                        <!-- Settings Dropdown -->
                                        <div class="ns-dropdown">
                                            <button class="ns-dropdown-anchor text-secondary">
                                                <i class="fas fa-cog"></i>
                                                <span>Settings</span>
                                                <span class="ns-dropdown-chevron"><i class="fas fa-chevron-down"></i></span>
                                            </button>

                                            <div class="ns-dropdown-menu">
                                                <a href="<?= ROOT . '/admin/settings' ?>" class="ns-dropdown-item">
                                                    <i class="fas fa-cog"></i> System Settings
                                                </a>
                                                
                                            </div>
                                        </div>
                                <?php
                                        break;

                                    default:
                                        # code...
                                        break;
                                }
                                ?>

                                <li class="nav-item">
                                    <a class="btn btn-outline-warning btn-sm rounded px-3 my-1" href="<?= ROOT . '/admin' ?>">Dashboard</a>
                                </li>

                                <li class="nav-item mt-2 mt-lg-0">
                                    <a href="<?= ROOT . '/auth/logout' ?>" class="btn btn-outline-secondary btn-sm px-3">Logout</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
        <?php
                break;
        }
        ?>
    </div>

    <main class="container" style="min-height: 60vh; padding: 2rem 0;">