<?php

/**
 * ChartController class 
 */

defined('ROOTPATH') or exit('Access Denied!');

class ChartController
{
    use Controller;

    public function __construct()
    {
        $user = new User();
        // if (!$user->logged_in()) redirect('login');
    }

    public function index(): void
    {
        $chart = new Chart();
        $data = [
            'charts'        => $chart->findAll(),
            'modules'       => Util::getSystemModules(),
            'page_title'    => 'Chart Manager'
        ];
        // show($data['charts']);die;
        $this->view('admin/charts/index', $data);
    }

    public function create(): void
    {
        $chart = new Chart();
        $data = [
            'errors'        => [],
            'modules'       => Util::getSystemModules(),
            'page_title'    => 'Create New Chart'
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($chart->validate($_POST)) {
                $_POST['created_by'] = user('firstname') . ' ' . user('surname');
                $chart->insert($_POST);
                Util::setFlash('chart_create_success', 'Chart created successfully!');
                redirect('admin/charts');
            } else {
                $data['errors'] = $chart->errors;
            }
        }

        $this->view('admin/charts/create-chart', $data);
    }

    public function render(string $id): never
    {
        // Skip authentication for this method
        // $user = new User();
        // if (!$user->logged_in()) redirect('login');

        $chart = new Chart();
        $chartConfig = $chart->first(['id' => $id]);

        if (!$chartConfig) {
            $this->sendErrorSVG('Chart configuration not found');
            exit;
        }

        try {
            // Get data based on module and data source
            $data = $this->getChartData($chartConfig->module, $chartConfig->data_source);

            // Clear any previous output
            if (ob_get_length()) ob_clean();

            // Set proper headers
            header('Content-Type: image/svg+xml');
            header('Cache-Control: max-age=3600'); // Cache for 1 hour

            echo $chart->generateSVG($data, $chartConfig->chart_type, [
                'width' => $chartConfig->width,
                'height' => $chartConfig->height,
                'colors' => explode(',', $chartConfig->color_scheme)
            ]);
            exit;
        } catch (Exception $e) {
            $this->sendErrorSVG('Error generating chart: ' . $e->getMessage());
            exit;
        }
    }

    private function sendErrorSVG(string $message): never
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: image/svg+xml');

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="200" viewBox="0 0 400 200">';
        $svg .= '<rect width="100%" height="100%" fill="#f8d7da"/>';
        $svg .= '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#721c24" font-family="Arial" font-size="14">';
        $svg .= htmlspecialchars($message);
        $svg .= '</text></svg>';

        echo $svg;
        exit;
    }

    // In app/controllers/ChartController.php
    private function getChartData(string $module, string $dataSource): array
    {
        $modelName = ucfirst(rtrim($module, 's'));
        $model = new $modelName();

        switch ($dataSource) {
            case 'expense_by_type':
                if ($module === 'expenditures') {
                    return [
                        ['label' => 'Office Supplies', 'value' => $model->sumExpendituresByType('Office Supplies')],
                        ['label' => 'Salaries', 'value' => $model->sumExpendituresByType('Salaries')],
                        ['label' => 'Utilities', 'value' => $model->sumExpendituresByType('Utilities')],
                        ['label' => 'Maintenance', 'value' => $model->sumExpendituresByType('Maintenance')],
                        ['label' => 'Marketing', 'value' => $model->sumExpendituresByType('Marketing')],
                        ['label' => 'Other', 'value' => $model->sumExpendituresByType('Other')]
                    ];
                } elseif ($module === 'payments') {
                    return $this->getPaymentChartData($model);
                }
                break;

            case 'monthly_payments':
                return $this->getMonthlyPaymentsData($model);

                // Add other data sources as needed
        }

        return [];
    }

    private function getPaymentChartData(object $paymentModel): array
    {
        $data = [];
        $paymentMethods = $paymentModel->sumPaymentsByMethod();

        foreach ($paymentMethods as $method) {
            $data[] = [
                'label' => $method->method,
                'value' => $method->total
            ];
        }

        return $data;
    }

    private function getMonthlyPaymentsData(object $paymentModel): array
    {
        $sql = "SELECT 
                DATE_FORMAT(payment_date, '%b %Y') AS month,
                SUM(amount) AS total
            FROM payments
            WHERE payment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
            ORDER BY payment_date DESC
            LIMIT 6";

        $stmt = $paymentModel->connect()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);

        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'label' => $row->month,
                'value' => $row->total
            ];
        }

        return array_reverse($data); // To show oldest first
    }

    public function paymentMethodsChart(): void
    {
        $payment = new Payment();
        $data = [
            'type' => 'doughnut',
            'data' => [
                'labels' => [],
                'datasets' => [[
                    'label' => 'Payment Methods',
                    'data' => [],
                    'backgroundColor' => [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e'
                    ]
                ]]
            ]
        ];

        foreach ($payment->getPaymentMethodsData() as $method) {
            $data['data']['labels'][] = $method['label'];
            $data['data']['datasets'][0]['data'][] = $method['value'];
        }

        $this->view('admin/charts/chart-template', [
            'chartId' => 'paymentMethodsChart',
            'chartData' => $data,
            'title' => 'Payment Methods Distribution'
        ]);
    }

    public function monthlyPaymentsTrend(): void
    {
        $payment = new Payment();
        $data = [
            'type' => 'line',
            'data' => [
                'labels' => [],
                'datasets' => [[
                    'label' => 'Monthly Payments',
                    'data' => [],
                    'borderColor' => '#4e73df',
                    'fill' => false
                ]]
            ]
        ];

        foreach ($payment->getMonthlyPaymentsTrend() as $month) {
            $data['data']['labels'][] = $month['month'];
            $data['data']['datasets'][0]['data'][] = $month['total'];
        }

        $this->view('admin/charts/chart-template', [
            'chartId' => 'monthlyPaymentsChart',
            'chartData' => $data,
            'title' => '6-Month Payment Trend'
        ]);
    }
}
