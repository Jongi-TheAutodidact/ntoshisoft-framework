<?php

/**
 * @var string $page_title
 * @var object $singleUser
 * @var array $data
 */
$this->view('inc/header', $data)
?>

<main id="main" class="main">

    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="fs-4 page-title"><?= $page_title ?> List</h3>
                        <!--Display User Profile-->
                        <section class="section profile mt-5">
                            <div class="row">
                                <?php
                                switch ($_SESSION['userRole']) {
                                    case 'Admin':
                                ?>
                                        <div class="col-lg-4">
                                            <?php 
                                                switch (user('user_role')) {
                                                    case 'Admin': ?>
                                                    <a class="mb-3 btn btn-outline-<?= THEME_COLOR ?>" href="<?= ROOT ?>/admin/users" style="border-radius:20px"><i class="bi bi-arrow-left"></i> BACK TO USERS</a>
                                                    <?php
                                                        
                                                        break;
                                                    
                                                    default: ?>
                                                    <a class="mb-3 btn btn-outline-<?= THEME_COLOR ?>" href="<?= ROOT ?>/admin" style="border-radius:20px"><i class="bi bi-arrow-left"></i> <i class="fa fa-speedometer"></i> BACK </a>
                                                    <?php
                                                        
                                                        break;
                                                }
                                            ?>
                                        </div>
                                        <div class="col-lg-4">
                                            <a class="mb-3 btn btn-warning" href="<?= ROOT ?>/admin/users/edit/<?= $singleUser->id ?>" style="border-radius:20px"><i class="bi bi-pencil-square"></i> EDIT PROFILE</a>
                                        </div>
                                        <div class="col-lg-4">
                                            <a class="mb-3 btn btn-danger" href="<?= ROOT ?>/admin/users/pdf/<?= $singleUser->id ?>" style="border-radius:20px"><i class="bi bi-download"></i> DOWNLOAD PDF</a>
                                        </div>
                                        
                                <?php break;

                                    default:
                                        # Silence is platinum
                                        break;
                                }
                                ?>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="card">
                                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                                            <img src="<?= get_image($singleUser->image, 'user') ?>" alt="Profile Image" class="rounded-circle" width="40%">
                                            <h5><?= $singleUser->username ?></h5>
                                            <h3>User ID: <?= $singleUser->user_id ? $singleUser->user_id : 'User ID here' ?></h3>

                                            <div class="social-links mt-2">
                                                <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                                                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                                                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-8">

                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <!-- Bordered Tabs -->
                                            <ul class="nav nav-tabs nav-tabs-bordered">

                                                <li class="nav-item">
                                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                                                </li>

                                            </ul>
                                            <div class="tab-content pt-2">
                                                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                                    <h5 class="card-title">Profile Details</h5>

                                                    <div class="row">
                                                        <div class="col-lg-3 col-md-4 label ">Full Name</div>
                                                        <div class="col-lg-9 col-md-8"><?= $singleUser->firstname . ' ' . $singleUser->surname ?></div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-3 col-md-4 label">User Role</div>
                                                        <div class="col-lg-9 col-md-8"><?= $singleUser->user_role ?></div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-3 col-md-4 label">Gender</div>
                                                        <div class="col-lg-9 col-md-8"><?= $singleUser->gender ? $singleUser->gender : 'Gender here' ?></div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-3 col-md-4 label">Phone</div>
                                                        <div class="col-lg-9 col-md-8"><?= $singleUser->phone ?></div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-3 col-md-4 label">Email</div>
                                                        <div class="col-lg-9 col-md-8"><?= $singleUser->email ?></div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-3 col-md-4 label">User Since</div>
                                                        <div class="col-lg-9 col-md-8"><?= $singleUser->created ?></div>
                                                    </div>
                                                </div>
                                            </div><!-- End Bordered Tabs -->
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
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>