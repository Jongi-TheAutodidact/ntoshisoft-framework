<?php
/**
 * @var array $company_details
 * @var object $row
 * @var bool $admin_user
 * @var array $data
 */
$this->view('inc/header', $data);
?>

<main id="main" class="main">
    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <?= Util::displayFlash('company_details_update_success', 'success') ?>
                        <div class="row mt-3">
                            <!-- Table with stripped rows -->
                            <table class="table table-striped">
                                <tbody>
                                    <?php
                                    $userRows = 1;
                                    if (!empty($company_details)) :
                                        foreach ($company_details as $row) :
                                    ?>
                                            <tr>
                                                <th scope="col">Name:</th>
                                                <td><img src="<?= $row->image ? get_image($row->image, 'logo') : ROOT . '/assets/img/logos/logo.png?v=1784597501' ?>" alt="App Logo" width="100" height="90"></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Name:</th>
                                                <td><?= $row->name ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">About:</th>
                                                <td><?= $row->about ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Email:</th>
                                                <td><?= $row->email ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Phone:</th>
                                                <td><?= $row->phone ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Address:</th>
                                                <td><?= $row->address ?></td>
                                            </tr>


                                            <?php if ($admin_user) : ?>
                                                <tr>
                                                    <th scope="col">Action:</th>
                                                    <td><a href="<?= ROOT ?>/admin/company/edit/<?= $row->id ?>"><i class="fa fa-pencil-square m-2 text-primary"></i></a></td>
                                                </tr>
                                            <?php endif; ?>


                                        <?php endforeach;
                                    else :  ?>
                                        <div class="alert alert-danger text-center">
                                            No records found
                                        </div>
                                    <?php endif;
                                    ?>
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>