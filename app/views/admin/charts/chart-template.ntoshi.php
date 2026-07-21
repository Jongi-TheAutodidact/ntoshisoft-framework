<?php
/** @var string $title */
/** @var string $chartId */
/** @var mixed $chartData */
$this->view('inc/header', $data); ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-<?= THEME_COLOR ?>"><?= $title ?></h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow">
                <a class="dropdown-item" href="#" onclick="downloadChart('<?= $chartId ?>')">
                    <i class="fas fa-download fa-sm me-2"></i>Download
                </a>
                <a class="dropdown-item" href="#" onclick="printChart('<?= $chartId ?>')">
                    <i class="fas fa-print fa-sm me-2"></i>Print
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="chart-area">
            <?= ChartHelper::renderChartCanvas($chartId) ?>
        </div>
    </div>
</div>

<?= ChartHelper::generateChartScript($chartId, $chartData) ?>

<script>
function downloadChart(chartId) {
    const canvas = document.getElementById(chartId);
    const link = document.createElement('a');
    link.download = chartId + '.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
}

function printChart(chartId) {
    const canvas = document.getElementById(chartId);
    const win = window.open('');
    win.document.write('<img src="' + canvas.toDataURL('image/png') + '"/>');
    win.document.close();
    win.focus();
    setTimeout(() => win.print(), 500);
}
</script>

<?php $this->view('inc/footer') ?>