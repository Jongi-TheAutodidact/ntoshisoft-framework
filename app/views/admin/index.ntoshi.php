<?php

/**
 * @var array $data
 * @var int $total_users
 * @var int $total_employees
 * @var int $total_payments
 * @var int $total_expenditure
 */
$this->view('inc/header', $data);
?>

<!-- Welcome Banner -->
<div class="glass-card my-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="gradient-text mb-2">Welcome back, <?= esc(!empty(user('firstname')) ? user('firstname') : user('username')) ?>!</h1>
            <p class="mb-0">Here's your business overview at a glance.</p>
        </div>
        <div class="d-flex gap-2">
            <?php
            switch (user('user_role')) {
                case 'Sys Admin':
                case 'Admin': ?>
                    <a href="<?= ROOT ?>/admin/company" class="btn btn-accent">
                        <i class="fas fa-shield-halved"></i> Business Profile Settings
                    </a>
            <?php
                    break;

                default:
                    # code...
                    break;
            }
            ?>
            <a href="<?= ROOT ?>" class="btn-primary">
                <i class="fas fa-external-link-alt"></i> Frontend
            </a>
        </div>
    </div>
</div>

<?php
switch (user('user_role')) {
    case 'Sys Admin': 
    case 'Admin': ?>
        <!-- Stats Cards Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <a href="<?= ROOT . '/admin/users' ?>" style="text-decoration:none" class="text-light">
                    <div class="glass-card text-center">
                        <i class="fas fa-users" style="font-size: 2rem; color: #2dd4bf;"></i>
                        <h2 class="mt-2 mb-0"><?= $total_users ?></h2>
                        <p class="text-muted mb-0">Total Users</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= ROOT . '/admin/employees' ?>" style="text-decoration:none" class="text-light">
                    <div class="glass-card text-center">
                        <i class="fas fa-user-tie" style="font-size: 2rem; color: #ffc107;"></i>
                        <h2 class="mt-2 mb-0"><?= $total_employees ?></h2>
                        <p class="text-muted mb-0">Employees</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= ROOT . '/admin/payments' ?>" style="text-decoration:none" class="text-light">
                    <div class="glass-card text-center">
                        <i class="fas fa-credit-card" style="font-size: 2rem; color: #38bdf8;"></i>
                        <h2 class="mt-2 mb-0"><?= $total_payments ?></h2>
                        <p class="text-muted mb-0">Payments Received</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= ROOT . '/admin/expenditure' ?>" style="text-decoration:none" class="text-light">
                    <div class="glass-card text-center">
                        <i class="fas fa-wallet" style="font-size: 2rem; color: #2dd4bf;"></i>
                        <h2 class="mt-2 mb-0"><?= $total_expenditure !== null ? DEF_CURR . number_format($total_expenditure, 2) : '0.00' ?></h2>
                        <p class="text-muted mb-0">Total Expenditure</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="glass-card">
                    <h3 class="gradient-text mb-3">
                        <i class="fas fa-chart-pie"></i> Payments by Method
                    </h3>
                    <canvas id="paymentMethodChart" style="max-height: 300px; width: 100%;"></canvas>
                    <?php if (!empty($payment_methods)): ?>
                        <div class="row mt-3 text-center">
                            <?php foreach ($payment_methods as $method): ?>
                                <div class="col">
                                    <span class="badge" style="background: rgba(45,212,191,0.2); color: #2dd4bf;"><?= esc($method['label']) ?></span>
                                    <p class="mt-1 mb-0"><?= DEF_CURR ?><?= number_format($method['value'], 2) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="glass-card">
                    <h3 class="gradient-text mb-3">
                        <i class="fas fa-chart-line"></i> Payment Trend (12 Months)
                    </h3>
                    <canvas id="trendChart" style="max-height: 300px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Expenditure Table -->
        <div class="glass-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h3 class="gradient-text mb-0">
                    <i class="fas fa-receipt"></i> Recent Expenditure
                </h3>
            </div>

            <?php if (empty($recent_expenditures)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
                    <p class="mt-2">No expenditure recorded yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Paid Via</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_expenditures as $exp): ?>
                                <tr style="border-bottom: 1px solid rgba(0,255,255,0.1);">
                                    <td><?= $exp->id ?></td>
                                    <td><strong><?= esc($exp->description) ?></strong></td>
                                    <td><?= esc($exp->expense_type) ?></td>
                                    <td><?= DEF_CURR ?><?= number_format($exp->amount, 2) ?></td>
                                    <td><?= esc($exp->paid_via) ?></td>
                                    <td><?= date('d M Y', strtotime($exp->expenditure_date)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
<?php
        break;

    default:
        # code...
        break;
}
?>

<!-- Upcoming Meetings -->
<?php if (!empty($upcoming_meetings)): ?>
    <div class="glass-card">
        <h3 class="gradient-text mb-3">
            <i class="fas fa-calendar-alt"></i> Meetings
        </h3>
        <div class="row g-3">
            <?php foreach (array_slice($upcoming_meetings, 0, 6) as $meet): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="d-flex align-items-center gap-3 p-2" style="background: rgba(0,0,0,0.2); border-radius: 1rem;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                            <i class="fas fa-handshake" style="font-size: 1.5rem;"></i>
                        </div>
                        <div style="min-width: 0;">
                            <strong style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;"><?= esc($meet->meeting_title ?? 'Meeting') ?></strong>
                            <small class="text-muted"><?= !empty($meet->scheduled_for) ? date('d M Y H:i', strtotime($meet->scheduled_for)) : '' ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let paymentChart, trendChart;

    function getThemeColors() {
        const isLight = document.body.classList.contains('light');
        return {
            textColor: isLight ? '#0b1120' : '#eef5ff',
            gridColor: isLight ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)',
            lineColor: '#2dd4bf',
            fillColor: isLight ? 'rgba(45,212,191,0.1)' : 'rgba(45,212,191,0.2)',
            colors: ['#2dd4bf', '#ffc107', '#38bdf8', '#f472b6', '#a78bfa', '#fb923c']
        };
    }

    function initCharts() {
        const colors = getThemeColors();

        if (paymentChart) paymentChart.destroy();
        if (trendChart) trendChart.destroy();

        // Payment Methods Doughnut
        const pmCtx = document.getElementById('paymentMethodChart').getContext('2d');
        const pmData = <?= json_encode($payment_methods ?? []) ?>;
        paymentChart = new Chart(pmCtx, {
            type: 'doughnut',
            data: {
                labels: pmData.map(d => d.label),
                datasets: [{
                    data: pmData.map(d => d.value),
                    backgroundColor: colors.colors.slice(0, pmData.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: colors.textColor,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        bodyColor: colors.textColor,
                        titleColor: colors.textColor,
                        backgroundColor: document.body.classList.contains('light') ? '#ffffff' : '#1a1a2e'
                    }
                }
            }
        });

        // Payment Trend Line
        const trendData = <?= json_encode($payment_trend ?? []) ?>;
        const labels = trendData.map(item => {
            const parts = item.month ? item.month.split('-') : [];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return parts.length >= 2 ? months[parseInt(parts[1]) - 1] + ' ' + parts[0] : '';
        });
        const counts = trendData.map(item => item.total);

        const trendCtx = document.getElementById('trendChart').getContext('2d');
        trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Payments',
                    data: counts,
                    borderColor: colors.lineColor,
                    backgroundColor: colors.fillColor,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: colors.lineColor,
                    pointBorderColor: document.body.classList.contains('light') ? '#ffffff' : '#0a0a1a',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: {
                            color: colors.textColor,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        bodyColor: colors.textColor,
                        titleColor: colors.textColor,
                        backgroundColor: document.body.classList.contains('light') ? '#ffffff' : '#1a1a2e'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: colors.textColor
                        },
                        grid: {
                            color: colors.gridColor
                        },
                        title: {
                            display: true,
                            text: 'Amount',
                            color: colors.textColor
                        }
                    },
                    x: {
                        ticks: {
                            color: colors.textColor,
                            maxRotation: 45
                        },
                        grid: {
                            color: colors.gridColor
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initCharts);

    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            setTimeout(() => initCharts(), 100);
        });
    }

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') initCharts();
        });
    });
    observer.observe(document.body, {
        attributes: true
    });
</script>

<style>
    .table {
        color: inherit;
        margin-bottom: 0;
    }

    .table th {
        border-bottom-color: rgba(0, 255, 255, 0.2);
        font-weight: 600;
    }

    body.light .table th {
        border-bottom-color: rgba(0, 0, 0, 0.1);
    }
</style>

<?php $this->view('inc/footer'); ?>