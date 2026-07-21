<?php
/** No view variables needed */

$user = new User();
$rows = $user->findAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMF users List - PDF</title>
    <!-- Favicons -->
    <link href="<?= ROOT . '/assets/img/mmf-logo.png' ?>" rel="icon">

    <style>
        /* Main container adjustments */
        .container-fluid {
            width: 100%;
            padding: 0 15px;
            box-sizing: border-box;
        }

        /* Table container */
        .table-responsive {
            width: 100%;
            overflow: hidden;
            /* Change from auto to hidden for PDF */
            box-sizing: border-box;
        }

        /* Table styling */
        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
            table-layout: fixed;
            /* Crucial for PDF column control */
            word-wrap: break-word;
        }

        /* Table cells */
        .table th,
        .table td {
            padding: 8px 12px;
            /* Adjust padding as needed */
            vertical-align: top;
            border-top: 1px solid #dee2e6;
            word-break: break-word;
            /* Handle long text */
        }

        /* Table header */
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: #f8f9fa;
            font-weight: bold;
        }

        /* Striped rows */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Force table to respect page width */
        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        /* Cell-specific adjustments */
        .table td:nth-child(1) {
            width: 10%;
        }

        /* # column */
        .table td:nth-child(2) {
            width: 30%;
        }

        /* Name column */
        .table td:nth-child(3) {
            width: 20%;
        }

        /* Sector column */
        .table td:nth-child(4) {
            width: 20%;
        }

        /* Phone column */
        .table td:nth-child(5) {
            width: 20%;
        }

        /* City column */
        .table td:nth-child(6) {
            width: 20%;
        }

        /* Province column */
        .table td:nth-child(7) {
            width: 20%;
        }
        .bg-warning {
            background-color: #ffe600ff;
            color: #000;
            padding: 3px 5px;
            border-radius: 15px;
        }
        .bg-success {
            background-color: #008100;
            color: #fff;
            padding: 3px 5px;
            border-radius: 15px;
        }

        /* Status column */
    </style>
</head>

<body>
    <main class="container-fluid px-4">
        <div class="row my-4">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div style="text-align:center">
                        <img src="<?= ROOT . '/assets/img/Logos/logo.png' ?>" alt="<?= APP_NAME ?> Logo" width="60%">
                    </div>
                    <h3 class="fs-4 page-title">USERS LIST</h3>
                </div>
                <div class="table-responsive bg-body-tertiary p-3 rounded shadow-sm animated-card" style="--animation-order: 1;">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0 ">
                            <thead>
                                <tr>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">User Name</th>
                                    <th scope="col">User Role</th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody id="customer-table-body">
                                <?php
                                $userRows = 1;
                                if (!empty($rows)) :
                                    foreach ($rows as $row) :

                                ?>
                                        <tr>
                                            <th scope="row"><?= $userRows++ ?></th>
                                            <td><img src="<?= get_image($row->image, 'user') ?>" alt="<?= $row->firstname . ' Profile Image' ?>" class="rounded-circle me-2" style="width:30px; height:30px; object-fit:cover;"><?= $row->firstname . ' ' . $row->surname ?></td>
                                            <td><?= $row->email ?></td>
                                            <td><?= $row->username ?></td>
                                            <td>
                                                <?php
                                                switch ($row->user_role) {
                                                    case 'Admin': ?>
                                                        <span class="badge bg-success">
                                                        <?php
                                                        break;
                                                    case 'User': ?>
                                                            <span class="badge bg-warning">
                                                            <?php
                                                            break;
                                                    case 'Customer': ?>
                                                            <span class="badge bg-danger">
                                                                <?php
                                                                break;
                                                    case 'Member': ?>
                                                            <span class="badge bg-warning text-dark">
                                                                <?php
                                                                break;

                                                            default: ?>
                                                                    <span class="badge bg-secondary">
                                                                <?php
                                                                break;
                                                        }
                                                                ?>
                                                                <?= $row->user_role ?>
                                                                    </span>
                                            </td>
                                    <?php endforeach;
                                endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>