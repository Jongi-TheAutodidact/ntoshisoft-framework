<?php
    /**
     * @var array $comments
     * @var array $data
     */
    $this->view('inc/header', $data); ?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>

<main id="main" class="main">

    <section class="section p-4">
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Table with stripped rows -->
                            <table class="table ntoshitable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Person</th>
                                        <th>Post</th>
                                        <th>Email</th>
                                        <th>Comment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $userRows = 1;
                                    if (!empty($comments)) :
                                        foreach ($comments as $row) :
                                    ?>
                                            <tr>
                                                <th><?= $userRows++ ?></th>
                                                <td><?= $row->fullname ?></td>
                                                <td><?= $row->title ?></td>
                                                <td><?= $row->email ?></td>
                                                <td><?= $row->comment ?></td>
                                                <td>
                                                    <div class="text-center d-flex">
                                                        <a href="<?= ROOT ?>/admin/comments/edit/<?= $row->id ?>"><i class="bi bi-pencil-square m-2 text-primary"></i></a>
                                                        <a href="<?= ROOT ?>/admin/comments/delete/<?= $row->id ?>" onclick="alert('Are you sure you want to delete this record? This action cannot be reversed. Click \'OK\' to proceed OR \'JUST REFRESH THE PAGE\' to cancel the action!')"><i class="bi bi-trash m-2 text-danger "></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                            <!-- End Table -->
                        </div>
                    </div>
                </div>
    </section>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>