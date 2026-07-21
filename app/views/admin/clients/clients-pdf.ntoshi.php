<?php
/** No view variables needed - uses only constants/Session */

$client = new Client();
$clients = $client->allClientsWithUsersDetails();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> NtoshiSoft Clients List - PDF</title>
    <!-- Favicons -->
    <link href="<?= ROOT . '/assets/img/Logos/logo.png' ?>" rel="icon">
    
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

        .rounded-circle-me-2 {
            border-radius: 50%;
            margin-right: 0.5rem;
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
                    <h3 class="fs-4 page-title">Member List</h3>
                </div>
                <div class="table-responsive bg-body-tertiary p-3 rounded shadow-sm animated-card" style="--animation-order: 1;">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0 ">
                            <thead>
                                <tr style="margin-left: 10px;">
                                    <th style="text-align: left;">#</th>
                                    <th style="text-align: left;">Image</th>
                                    <th style="text-align: left;">Name</th>
                                    <th style="text-align: left;">Phone</th>
                                    <th style="text-align: left;">City</th>
                                    <th style="text-align: left;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="customer-table-body">
                                <?php $rowId = 1;

                                if (!empty($clients)): foreach ($clients as $client): ?>
                                        <tr>
                                            <th scope="row"><?= $rowId++ ?></th>
                                            <td>
                                                <img src="<?= get_image($client->image, 'user') ?>" alt="Jane Doe" class="rounded-circle-me-2" style="width:30px; height:30px; object-fit:cover;">
                                            </td>
                                            <td><?= $client->firstname . ' ' . $client->surname ?></td></td>
                                            <td><?= $client->phone ?></td>
                                            <td><?= $client->city ?></td>
                                            <td><span class="badge bg-success"><?= $client->status ?></span></td>
                                        </tr>
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