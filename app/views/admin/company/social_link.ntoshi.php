<?php
/**
 * @var array $social_links
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
                        <?= Util::displayFlash('link_update_success', 'success') ?>
                        <div class="row mt-3">
                            <!-- Table with stripped rows -->
                            <table class="table table-striped">
                                <tbody>
                                    <?php
                                    $userRows = 1;
                                    if (!empty($social_links)) :
                                        foreach ($social_links as $row) :
                                    ?>
                                            <tr>
                                                <th scope="col">Twitter:</th>
                                                <td><?= $row->twitter_link ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Facebook:</th>
                                                <td><?= $row->facebook_link ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Tiktok:</th>
                                                <td><?= $row->tiktok_link ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Instagram:</th>
                                                <td><?= $row->instagram_link ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">LinkedIn:</th>
                                                <td><?= $row->linkedin ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">YouTube:</th>
                                                <td><?= $row->youtube_link ?></td>
                                            </tr>

                                            <?php if ($admin_user) : ?>
                                                <tr>
                                                    <th scope="col">Action:</th>
                                                    <td><a href="<?= ROOT ?>/admin/social/edit/<?= $row->id ?>"><i class="bi bi-pencil-square m-2 text-primary"></i></a></td>
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