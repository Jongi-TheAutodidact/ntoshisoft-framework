<?php
    /**
     * @var array $meetings
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
                            <h3 class="fs-4 page-title">Meetings</h3>
                            <a href="<?= ROOT ?>/admin/create-meeting" class="btn btn-warning text-dark"><i class="bi bi-plus-circle me-2"></i>Add New Meeting</a>
                        </div>
                        <hr>

                        <?= Util::displayFlash('meeting_register_success','success') ?>
                        <?= Util::displayFlash('meeting_update_success','success') ?>
                        <?= Util::displayFlash('meeting_delete_success','success') ?>

                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Meeting Title</th>
                                    <th>Convener</th>
                                    <th>Scheduled For</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; ?>
                                <?php if(!empty($meetings)): foreach ($meetings as $meet): ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td><?= esc($meet->meeting_title) ?></td>
                                        <td><?= esc($meet->firstname) . ' ' . esc($meet->surname) ?></td>
                                        <td><?= esc($meet->scheduled_for) ?></td>
                                        <td><?= esc($meet->notes) ?></td>
                                        <td>
                                            <div class="text-center d-flex gap-2 justify-content-center">
                                                <a href="<?= ROOT ?>/admin/meeting/edit/<?= $meet->id ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                                <a href="<?= ROOT ?>/admin/meeting/delete/<?= $meet->id ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php $this->view('inc/footer'); ?>