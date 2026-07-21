<?php
/** No view variables needed */

$user = new User();

$id = basename($_GET['url']);

$row = $user->first(['id' => $id]); 

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $row->firstname . ' ' . $row->surname . ' User Profile ' ?></title>
    <style>
        /* Card styling */
        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            background: #fff;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .data-card {
            margin-top: 10px;
        }

        .card-body {
            padding: 15px;
        }

        /* Profile Card */
        .profile-card {
            text-align: center;
            margin-top: 10px;
        }

        .profile-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #ddd;
        }

        .profile-card h2 {
            font-size: 18px;
            margin: 10px 0;
        }

        .profile-card h3 {
            font-size: 14px;
            color: #555;
        }


        /* Profile Details */
        .label {
            font-weight: bold;
            color: #555;
        }
        .nav {
            list-style-type: none;
        }

        /* Tabs */
        .nav-tabs {
            display: flex;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }

        .nav-tabs .nav-link {
            padding: 8px 12px;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }

        .nav-tabs .active {
            color: #007bff;
            border-bottom: 3px solid #007bff;
        }
        .data-row {
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <section class="section profile mt-5">
                            <div class="row flex">
                                <div class="col-xl-4">
                                    <div class="card">
                                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                                            <img src="<?= $row->image ? ROOT . '/' . $row->image : ROOT . '/assets/img/user.png' ?>" alt="<?= $row->firstname . ' ' . $row->surname . ' Profile Image ' ?> ">
                                            <h2><?= $row->firstname . ' ' . $row->surname ?></h2>
                                            <h3>User ID: <?= $row->user_id ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-8 data-card">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <ul class="nav nav-tabs nav-tabs-bordered">
                                                <li class="nav-item">
                                                    <button class="nav-link active">Overview</button>
                                                </li>
                                            </ul>
                                            <div class="tab-content pt-2">
                                                <div class="tab-pane fade show active profile-overview">
                                                    <h2 class="card-title">Profile Details</h2>
                                                    <div class="data-row">
                                                        <div class="col-lg-3 col-md-4 label">Full Name</div>
                                                        <div class="col-lg-9 col-md-8"><?= $row->firstname . ' ' . $row->surname ?></div>
                                                    </div> 
                                                    <div class="data-row">
                                                        <div class="col-lg-3 col-md-4 label">User Role</div>
                                                        <div class="col-lg-9 col-md-8"><?= $row->user_role ?></div>
                                                    </div>
                                                    <div class="data-row">
                                                        <div class="col-lg-3 col-md-4 label">Gender</div>
                                                        <div class="col-lg-9 col-md-8"><?= $row->gender ?></div>
                                                    </div>
                                                    <div class="data-row">
                                                        <div class="col-lg-3 col-md-4 label">Phone</div>
                                                        <div class="col-lg-9 col-md-8"><?= $row->phone ?></div>
                                                    </div>
                                                    <div class="data-row">
                                                        <div class="col-lg-3 col-md-4 label">Email</div>
                                                        <div class="col-lg-9 col-md-8"><?= $row->email ?></div>
                                                    </div>
                                                    <div class="data-row">
                                                        <div class="col-lg-3 col-md-4 label">User Since</div>
                                                        <div class="col-lg-9 col-md-8"><?= date('d M Y', strtotime($row->created)) ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>