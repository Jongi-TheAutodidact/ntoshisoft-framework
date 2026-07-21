<?php
/**
 * @var array $data
 * @var array $log_entries
 * @var array $entry
 */
$this->view('inc/header', $data);
?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>

<main id="main" class="main">
    <section class="section p-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title d-flex justify-content-between align-items-center">
                    <?= esc($data['page_title']) ?>
                    <a href="<?= ROOT ?>/admin/client-onboarding/csv" class="btn btn-sm btn-success">
                        Download as CSV
                    </a>
                </h5>

                <?php if (!empty($log_entries)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered ntoshitable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date and Time</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($log_entries as $entry): ?>
                                    <tr>
                                        <td><?= esc($entry['datetime']) ?></td>
                                        <td><?= esc($entry['username']) ?></td>
                                        <td><code><?= esc($entry['password']) ?></code></td>
                                        <td><?= esc($entry['created_by']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No log entries found.</div>
                <?php endif; ?>

            </div>
        </div>
    </section>
</main>

<?php $this->view('inc/footer'); ?>