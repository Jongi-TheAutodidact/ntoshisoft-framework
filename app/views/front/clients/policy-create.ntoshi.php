<?php
/** @var array $data */
/** @var array $errors */
/** @var object|null $client */
/** @var array $clients */
/** @var array $dependants */
$this->view('inc/front-header', $data); ?>

<main id="main" class="main">
    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="<?= esc('created_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            
                            <h3 class="fs-4 page-title mb-4"><?= $data['page_title'] ?></h3>
                            
                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors); ?>
                                </div>
                            <?php endif; ?>
                            <?= Util::displayFlash('client_register_success','success') ?>
                            <!-- Client Selection -->
                            <div class="row mb-4">
                                <div class="col-lg-6">
                                    <label for="client_id" class="form-label">Client *</label>
                                    <?php if ($client): ?>
                                        <input type="hidden" name="client_id" value="<?= $client->id ?>">
                                        <input type="text" class="form-control" value="<?= esc($client->firstname.' '.$client->surname) ?>" readonly>
                                        <small class="form-text text-muted">Client ID: <?= $client->id ?></small>
                                    <?php else: ?>
                                        <select name="client_id" class="form-select" required>
                                            <option value="">Select Client</option>
                                            <?php if($clients): foreach ($clients as $c): ?>
                                                <option value="<?= $c->id ?>" <?= old_value('client_id') == $c->id ? 'selected' : '' ?>>
                                                    <?= esc($c->firstname.' '.$c->surname) ?> (ID: <?= $c->identity_number ?>)
                                                </option>
                                            <?php endforeach; endif ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-6">
                                    <label for="policy_type" class="form-label">Policy Type *</label>
                                    <select name="policy_type" class="form-select" required>
                                        <option value="Standard" <?= old_value('policy_type') == 'Standard' ? 'selected' : '' ?>>Standard</option>
                                        <option value="Premium" <?= old_value('policy_type') == 'Premium' ? 'selected' : '' ?>>Premium</option>
                                        <option value="Family" <?= old_value('policy_type') == 'Family' ? 'selected' : '' ?>>Family</option>
                                        <option value="Senior" <?= old_value('policy_type') == 'Senior' ? 'selected' : '' ?>>Senior</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Coverage & Premium -->
                            <div class="row mb-4">
                                <div class="col-lg-6">
                                    <label for="coverage_amount" class="form-label">Coverage Amount (R) *</label>
                                    <input type="number" step="0.01" min="1000" name="coverage_amount" 
                                        value="<?= old_value('coverage_amount') ?>" 
                                        class="form-control" required>
                                </div>
                                <div class="col-lg-6">
                                    <label for="premium_amount" class="form-label">Premium Amount (R) *</label>
                                    <input type="number" step="0.01" min="50" name="premium_amount" 
                                        value="<?= old_value('premium_amount') ?>" 
                                        class="form-control" required>
                                </div>
                            </div>
                            
                            <!-- Payment & Dates -->
                            <div class="row mb-4">
                                <div class="col-lg-4">
                                    <label for="payment_frequency" class="form-label">Payment Frequency *</label>
                                    <select name="payment_frequency" class="form-select" required>
                                        <option value="Monthly" <?= old_value('payment_frequency') == 'Monthly' ? 'selected' : '' ?>>Monthly</option>
                                        <option value="Quarterly" <?= old_value('payment_frequency') == 'Quarterly' ? 'selected' : '' ?>>Quarterly</option>
                                        <option value="Annually" <?= old_value('payment_frequency') == 'Annually' ? 'selected' : '' ?>>Annually</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label for="start_date" class="form-label">Start Date *</label>
                                    <input type="date" name="start_date" 
                                        value="<?= old_value('start_date', date('Y-m-d')) ?>" 
                                        class="form-control" required>
                                </div>
                                <div class="col-lg-4">
                                    <label for="end_date" class="form-label">End Date (Optional)</label>
                                    <input type="date" name="end_date" 
                                        value="<?= old_value('end_date') ?>" 
                                        class="form-control">
                                </div>
                            </div>
                            
                            <!-- Dependants Selection -->
                            <div class="row mb-4">
                                <div class="col-lg-12">
                                    <label class="form-label">Covered Dependants</label>
                                    <div class="border p-3 rounded" style="max-height: 200px; overflow-y: auto;">
                                        <?php if (!empty($dependants)): ?>
                                            <?php $selectedDeps = json_decode(old_value('covered_dependants', '[]'), true) ?>
                                            <?php foreach ($dependants as $dep): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                        name="covered_dependants[]" 
                                                        value="<?= $dep->id ?>"
                                                        id="dep<?= $dep->id ?>"
                                                        <?= in_array($dep->id, $selectedDeps) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="dep<?= $dep->id ?>">
                                                        <?= esc($dep->dep_name) ?> 
                                                        (<?= $dep->relationship ?>, 
                                                        DOB: <?= date('M j, Y', strtotime($dep->dob)) ?>)
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No dependants found for this client</p>
                                        <?php endif; ?>
                                    </div>
                                    <small class="form-text text-muted">Select dependants covered by this policy</small>
                                </div>
                            </div>
                            
                            <!-- Beneficiaries -->
                            <div class="row mb-4">
                                <div class="col-lg-12">
                                    <label for="beneficiaries" class="form-label">Beneficiaries</label>
                                    <textarea name="beneficiaries" class="form-control" 
                                        placeholder="Enter beneficiary details (name, relationship, percentage)"
                                        rows="3"><?= old_value('beneficiaries') ?></textarea>
                                    <small class="form-text text-muted">Format: Name Surname (Relationship) - XX%</small>
                                </div>
                            </div>
                            
                            <!-- Notes -->
                            <div class="row mb-4">
                                <div class="col-lg-12">
                                    <label for="notes" class="form-label">Policy Notes</label>
                                    <textarea name="notes" class="form-control" rows="3"><?= old_value('notes') ?></textarea>
                                </div>
                            </div>
                            
                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">
                                        <i class="bi bi-save me-1"></i> CREATE POLICY
                                    </button>
                                    <a href="<?= $client ? ROOT.'/admin/insurance/policies/'.$client->id : ROOT.'/admin/insurance/policies' ?>" 
                                        class="btn btn-danger">
                                        CANCEL
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php $this->view('inc/front-footer') ?>