<?php /** No view variables needed - uses only constants/Session */ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= 'Page Not Found' . ' | ' . APP_NAME ?></title>
    <meta name="description" content='Purposefully Built, Ntoshi Web Framework is a lightweight, modular, and developer-friendly MVC framework designed for rapid application development across any sector 
    Whether you are a self-taught coder, a university dev student, or a solo entrepreneur, Ntoshi Web Framework empowers you to build full-featured web apps in minutes. Created from scratch, Ntoshi Web Framework is a FOSS initiative that cuts through complexity, gets you shipping faster, and lets you own every line of code you deploy'>
    <meta name="keywords" content="PHP,MVC,Framework,Development,Ntoshi Web Framework,Jongi Brands,Ntoshi PHP framework,lightweight PHP framework,Laravel alternative,CodeIgniter alternative,custom PHP framework,build web apps fast,rapid PHP development,PHP CLI tool,modular PHP framework,self-taught developer PHP,PHP for startups,PHP framework with CLI,PHP artisan alternative,PHP development tools,dev productivity tools,make apps faster PHP,PHP for businesses,church software PHP,PHP CRM,clean PHP framework,small business dev tools, PHP ERP">
    <meta name="author" content="Jongi Mbodla - The Tech Kaffir <jongim@jongibrandz.co.za>">


    <!-- Favicons -->
    <link href="<?= ROOT . '/assets/img/Logos/bleki-logo-1.png' ?>" rel="icon">
    <link href="<?= ROOT . '/assets/img/Logos/bleki-logo-1.png' ?>" rel="apple-touch-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!--Owl Carousel-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />

    <!-- Main Framework CSS -->
    <link rel="stylesheet" href="<?= ROOT . '/assets/css/style.css' ?>">
    <style>
        .frontend-navbar {
            padding: 0.5rem 1rem;
        }

        .navbar-brand img {
            height: 40px;
            /* Fixed height */
            width: auto;
            /* Maintain aspect ratio */
        }

        @media (max-width: 991.98px) {
            .frontend-navbar .container-fluid {
                padding-right: var(--bs-gutter-x, .75rem);
                padding-left: var(--bs-gutter-x, .75rem);
            }

            .navbar-toggler {
                margin-left: auto;
                /* Push to far right */
            }
        }
    </style>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
</head>

<body>

    <main id="main">
        <div class="container">
            <div class="row bg-light rounded shadow-sm mt-5">
                <div class="col-lg-12 text-center">
                    <img src="<?= ROOT ?>/assets/img/404.gif" width="400px" alt="Jongi Brands Page Not Found GIF">
                </div>
            </div>
            <div class="row mt-5">
                <button class="btn btn-outline-<?= THEME_COLOR ?>" onclick="history.back()">Go Back</button>
            </div>
        </div>
    </main><!-- End #main -->
</body>

</html>