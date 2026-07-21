<?php
    /**
     * @var array $posts
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="fs-4 page-title">Posts List</h3>
                            <a href="<?= ROOT . '/admin/post/create/null' ?>" class="btn btn-warning text-dark"><i class="bi bi-plus-circle me-2"></i>Add New Post</a>
                        </div>
                        <hr>

                        <?= Util::displayFlash('post_create_success', 'success') ?>
                        <?= Util::displayFlash('post_update_success', 'success') ?>
                        <?= Util::displayFlash('post_delete_success', 'success') ?>
                        <div class="row">
                            <!-- Table with stripped rows -->
                            <div class="table-responsive">
                                <table class="table ntoshitable table-hover table-striped">

                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Featured Image</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Content</th>
                                            <th scope="col">Author</th>
                                            <th scope="col">Category</th>
                                            <th scope="col">Date Posted</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $userRows = 1;
                                        if (!empty($posts)) :
                                            foreach ($posts as $row) :
                                        ?>
                                                <tr>
                                                    <th scope="row"><?= $userRows++ ?></th>
                                                    <td><img src="<?= get_image($row->image) ?>" style="width:100px;height:100px; object-fit:cover"></td>
                                                    <td><?= $row->title ?></td>
                                                    <td> <a href="<?= ROOT ?>/admin/post/view/<?= $row->post_id ?>"><?= substr($row->content, 0, 200) ?></a> </td>
                                                    <td><?= $row->author ?></td>
                                                    <td><?= $row->cat_name ?></td>
                                                    <td><?= $row->date_posted ?></td>
                                                    <td>
                                                        <div class="text-center d-flex">
                                                            <a href="<?= ROOT ?>/admin/post/edit/<?= $row->post_id ?>"><i class="bi bi-pencil-square m-2"></i></a>
                                                            <a href="<?= ROOT ?>/admin/post/delete/<?= $row->post_id ?>" onclick="alert('Are you sure you want to delete this record? This action cannot be reversed. Click \'OK\' to proceed OR \'JUST REFRESH THE PAGE\' to cancel the action!')"><i class="bi bi-trash m-2 text-danger "></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- End Posts Table -->
                        </div>
                    </div>

                </div>
            </div>
    </section>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>