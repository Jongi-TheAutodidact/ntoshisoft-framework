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
                        <h5 class="card-title"><?= esc($data['page_title']) ?></h5>
                        <p class="card-text">Track website visitors and current online users.</p>

                        <div class="row mx-auto d-flex mb-4">
                            <div class="col-lg-12">

                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2">Unique Today</h6>
                                        <h4 class="card-title"><?= esc($data['unique_visits_today']) ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2">Total Visits</h6>
                                        <h4 class="card-title"><?= esc($data['total_visits']) ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2">Current Online</h6>
                                        <h4 class="card-title"><?= $data['online_users'] ? count($data['online_users']) : 0 ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Online Users List</h5>
                                        <div class="table-responsive">
                                            <table class="table table-striped ntoshitable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>User's Name</th>
                                                        <th>IP Address</th>
                                                        <th>Last Active</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $counter = 1; ?>
                                                    <?php if(!empty($data['online_users'])): foreach ($data['online_users'] as $ou): ?>
                                                        <?php
                                                        if ((int)$ou->user_id === 0) {
                                                            $name = 'Guest User';
                                                        } else {
                                                            $name = trim($ou->firstname . ' ' . $ou->surname);
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td><?= $counter++ ?></td>
                                                            <td><?= esc($name) ?></td>
                                                            <td><?= esc($ou->ip_address) ?></td>
                                                            <td><?= esc($ou->last_active) ?></td>
                                                        </tr>
                                                    <?php endforeach; endif ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Recent Visits</h5>
                                        <div class="table-responsive">
                                            <table class="table table-striped ntoshitable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>IP</th>
                                                        <th>Device</th>
                                                        <th>Location</th>
                                                        <th>From</th>
                                                        <th>To</th>
                                                        <th>Time</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $counter = 1; ?>
                                                    <?php foreach ($data['recent_visits'] as $visit): ?>
                                                        <tr>
                                                            <td><?= $counter++ ?></td>
                                                            <td><?= esc($visit->ip_address) ?></td>
                                                            <td><?= esc($visit->device) ?></td>
                                                            <td><?= esc($visit->country) ?>, <?= esc($visit->city) ?></td>
                                                            <td><?= esc($visit->visited_from) ?></td>
                                                            <td><?= esc($visit->visited_to) ?></td>
                                                            <td><?= date("F j, Y h:i A", strtotime($visit->visited_at)) ?></td>
                                                            <td>
                                                                <a href="<?= ROOT ?>/admin/visitors/view/<?= $visit->id ?>" class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Visits by Country</h5>
                                        <ul class="list-group">
                                            <?php foreach ($data['visits_by_country'] as $v): ?>
                                                <li class="list-group-item"><?= esc($v->country) ?> <span class="badge bg-primary"><?= esc($v->count) ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Visits by City</h5>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>City</th>
                                                    <th>Country</th>
                                                    <th>Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['visits_by_city'] as $v): ?>
                                                    <tr>
                                                        <td><?= esc($v->city) ?></td>
                                                        <td><?= esc($v->country) ?></td>
                                                        <td><?= esc($v->count) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php $this->view('inc/footer'); ?>