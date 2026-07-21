<?php
defined('ROOTPATH') or exit('Access Denied!');

class ChartHelper
{
    public static function renderChartCanvas(string $id, string $type = 'bar', string $width = '100%', string $height = '400px'): string
    {
        return '<canvas id="' . $id . '" style="width:' . $width . ';height:' . $height . '"></canvas>';
    }

    public static function generateChartScript(string $id, array $data, array $options = []): string
    {
        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'font' => [
                            'family' => "'Nunito', sans-serif",
                            'size' => 12
                        ]
                    ]
                ],
                'tooltip' => [
                    'backgroundColor' => '#fff',
                    'titleColor' => '#333',
                    'bodyColor' => '#666',
                    'borderColor' => 'rgba(0,0,0,0.1)',
                    'borderWidth' => 1,
                    'padding' => 12
                ]
            ]
        ];

        // Merge custom options
        $options = array_merge_recursive($defaultOptions, $options);

        $script = '<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById("' . $id . '");
        new Chart(ctx, {
            type: "' . $data['type'] . '",
            data: ' . json_encode($data['data']) . ',
            options: ' . json_encode($options) . '
        });
    });
    </script>';

        return $script;
    }
}
