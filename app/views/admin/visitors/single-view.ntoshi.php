<?php
/**
 * @var array $data
 */
$this->view('inc/header', $data);
?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>

<main id="main" class="main">
    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= esc($data['visit']->ip_address) ?>'s Visit</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Visited From:</strong> <?= esc($data['visit']->visited_from) ?></p>
                                <p><strong>Visited To:</strong> <?= esc($data['visit']->visited_to) ?></p>
                                <p><strong>Time:</strong> <?= date("F j, Y h:i A", strtotime($data['visit']->visited_at)) ?></p>
                                <p><strong>IP:</strong> <?= esc($data['visit']->ip_address) ?></p>
                                <p><strong>User Agent:</strong> <?= esc($data['visit']->user_agent) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Country:</strong> <?= esc($data['visit']->country) ?></p>
                                <p><strong>City:</strong> <?= esc($data['visit']->city) ?></p>
                                <p><strong>Device:</strong> <?= esc($data['visit']->device) ?></p>
                                <p><strong>Referrer:</strong> <?= esc($data['visit']->visited_from) ?></p>
                                <p><strong>Session:</strong> <?= esc(session_id()) ?></p>
                            </div>
                        </div>

                        <div class="d-grid gap-2 col-lg-4">
                            <a href="<?= ROOT ?>/admin/visitors" class="btn btn-secondary">Back to All Visits</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php $this->view('inc/footer'); ?>