<?php

/**
 * Chart Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Chart
{
	use Model;

	protected $table = 'charts';
	protected $primaryKey = 'id';

	protected $allowedColumns = [
		'chart_name',
		'chart_type',
		'module',
		'data_source',
		'color_scheme',
		'width',
		'height',
		'created_by',
		'updated_by',
		'date_updated',
	];

	public function validate(array $post_data, int|string|null $id = null): bool
	{
		$this->errors = [];

		if (empty($post_data['chart_name'])) {
			$this->errors['chart_name'] = "** Please provide the Chart Name **";
		}

		if (empty($post_data['module'])) {
			$this->errors['module'] = "** Which module is this chart for? **";
		}

		if (empty($post_data['data_source'])) {
			$this->errors['data_source'] = "** Data source for the chart is required **";
		}


		if (empty($this->errors)) {
			return true;
		}

		return false;
	}

	public function generateSVG(array $data, string $type = 'bar', array $options = []): string
	{
		$width = $options['width'] ?? 600;
		$height = $options['height'] ?? 400;
		$colors = $options['colors'] ?? ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];

		switch ($type) {
			case 'bar':
				return $this->generateBarChart($data, $width, $height, $colors);
			case 'pie':
				return $this->generatePieChart($data, $width, $height, $colors);
			case 'line':
				return $this->generateLineChart($data, $width, $height, $colors);
			case 'donut':
				return $this->generateDonutChart($data, $width, $height, $colors);
			default:
				return $this->generateBarChart($data, $width, $height, $colors);
		}
	}

	private function generateBarChart(array $data, int $width, int $height, array $colors): string
	{
		$svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" xmlns="http://www.w3.org/2000/svg">';
		$svg .= '<rect width="100%" height="100%" fill="#f8f9fc"/>';

		$maxValue = max(array_column($data, 'value'));
		$barWidth = ($width - 100) / count($data);
		$scale = ($height - 80) / $maxValue;

		// Draw bars
		foreach ($data as $i => $item) {
			$barHeight = $item['value'] * $scale;
			$x = 50 + ($i * $barWidth);
			$y = $height - 30 - $barHeight;
			$color = $colors[$i % count($colors)];

			$svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . ($barWidth - 10) . '" height="' . $barHeight . '" fill="' . $color . '" rx="3"/>';
			$svg .= '<text x="' . ($x + $barWidth / 2 - 10) . '" y="' . ($height - 10) . '" font-size="12" text-anchor="middle">' . $item['label'] . '</text>';
			$svg .= '<text x="' . ($x + $barWidth / 2 - 10) . '" y="' . ($y - 5) . '" font-size="12" text-anchor="middle">' . $item['value'] . '</text>';
		}

		// Draw axes
		$svg .= '<line x1="40" y1="30" x2="40" y2="' . ($height - 30) . '" stroke="#858796" stroke-width="2"/>';
		$svg .= '<line x1="40" y1="' . ($height - 30) . '" x2="' . $width . '" y2="' . ($height - 30) . '" stroke="#858796" stroke-width="2"/>';

		$svg .= '</svg>';
		return $svg;
	}

	private function generatePieChart(array $data, int $width, int $height, array $colors): string
	{
		$svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" xmlns="http://www.w3.org/2000/svg">';
		$svg .= '<rect width="100%" height="100%" fill="#f8f9fc"/>';

		$total = array_sum(array_column($data, 'value'));
		$centerX = $width / 2;
		$centerY = $height / 2;
		$radius = min($centerX, $centerY) * 0.8;
		$startAngle = 0;

		foreach ($data as $i => $item) {
			$percentage = $item['value'] / $total;
			$endAngle = $startAngle + ($percentage * 360);
			$color = $colors[$i % count($colors)];

			// Calculate start and end points
			$x1 = $centerX + $radius * cos(deg2rad($startAngle));
			$y1 = $centerY + $radius * sin(deg2rad($startAngle));
			$x2 = $centerX + $radius * cos(deg2rad($endAngle));
			$y2 = $centerY + $radius * sin(deg2rad($endAngle));

			// Draw pie slice
			$largeArc = ($endAngle - $startAngle) > 180 ? 1 : 0;
			$svg .= '<path d="M' . $centerX . ',' . $centerY . ' L' . $x1 . ',' . $y1 . ' A' . $radius . ',' . $radius . ' 0 ' . $largeArc . ',1 ' . $x2 . ',' . $y2 . ' Z" fill="' . $color . '"/>';

			// Draw label
			$midAngle = $startAngle + ($endAngle - $startAngle) / 2;
			$labelX = $centerX + ($radius * 0.7) * cos(deg2rad($midAngle));
			$labelY = $centerY + ($radius * 0.7) * sin(deg2rad($midAngle));

			$svg .= '<text x="' . $labelX . '" y="' . $labelY . '" font-size="12" text-anchor="middle" fill="#fff">' . round($percentage * 100) . '%</text>';

			$startAngle = $endAngle;
		}

		$svg .= '</svg>';
		return $svg;
	}

	private function generateLineChart(array $data, int $width, int $height, array $colors): string
	{
		$svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" xmlns="http://www.w3.org/2000/svg">';
		$svg .= '<rect width="100%" height="100%" fill="#f8f9fc"/>';

		$maxValue = max(array_column($data, 'value'));
		$minValue = min(array_column($data, 'value'));
		$valueRange = $maxValue - $minValue;
		$dataCount = count($data);

		// Calculate scale and padding
		$padding = 60;
		$graphWidth = $width - $padding * 2;
		$graphHeight = $height - $padding * 2;
		$scaleY = $valueRange > 0 ? $graphHeight / $valueRange : 1;

		// Draw axes
		$svg .= '<line x1="' . $padding . '" y1="' . ($height - $padding) . '" x2="' . ($width - $padding) . '" y2="' . ($height - $padding) . '" stroke="#858796" stroke-width="2"/>';
		$svg .= '<line x1="' . $padding . '" y1="' . $padding . '" x2="' . $padding . '" y2="' . ($height - $padding) . '" stroke="#858796" stroke-width="2"/>';

		// Draw grid lines and labels
		$gridSteps = 5;
		for ($i = 0; $i <= $gridSteps; $i++) {
			$y = $padding + ($graphHeight - ($i * ($graphHeight / $gridSteps)));
			$value = $minValue + ($i * ($valueRange / $gridSteps));

			$svg .= '<line x1="' . $padding . '" y1="' . $y . '" x2="' . ($width - $padding) . '" y2="' . $y . '" stroke="#e3e6f0" stroke-width="1"/>';
			$svg .= '<text x="' . ($padding - 10) . '" y="' . ($y + 4) . '" font-size="10" text-anchor="end">' . number_format($value) . '</text>';
		}

		// Draw data points and line
		$points = '';
		foreach ($data as $i => $item) {
			$x = $padding + ($i * ($graphWidth / ($dataCount - 1)));
			$y = $padding + ($graphHeight - (($item['value'] - $minValue) * $scaleY));

			$points .= $x . ',' . $y . ' ';

			// Draw data point
			$svg .= '<circle cx="' . $x . '" cy="' . $y . '" r="4" fill="' . $colors[0] . '" stroke="#fff" stroke-width="1.5"/>';
			$svg .= '<text x="' . $x . '" y="' . ($height - $padding + 20) . '" font-size="10" text-anchor="middle">' . $item['label'] . '</text>';
		}

		// Draw line connecting points
		$svg .= '<polyline points="' . $points . '" fill="none" stroke="' . $colors[0] . '" stroke-width="2" stroke-linejoin="round"/>';

		$svg .= '</svg>';
		return $svg;
	}

	private function generateDonutChart(array $data, int $width, int $height, array $colors): string
	{
		$svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" xmlns="http://www.w3.org/2000/svg">';
		$svg .= '<rect width="100%" height="100%" fill="#f8f9fc"/>';

		$total = array_sum(array_column($data, 'value'));
		$centerX = $width / 2;
		$centerY = $height / 2;
		$outerRadius = min($centerX, $centerY) * 0.8;
		$innerRadius = $outerRadius * 0.6; // Creates the "donut hole"
		$startAngle = 0;

		foreach ($data as $i => $item) {
			$percentage = $item['value'] / $total;
			$endAngle = $startAngle + ($percentage * 360);
			$color = $colors[$i % count($colors)];

			// Calculate start and end points for outer arc
			$outerX1 = $centerX + $outerRadius * cos(deg2rad($startAngle));
			$outerY1 = $centerY + $outerRadius * sin(deg2rad($startAngle));
			$outerX2 = $centerX + $outerRadius * cos(deg2rad($endAngle));
			$outerY2 = $centerY + $outerRadius * sin(deg2rad($endAngle));

			// Calculate start and end points for inner arc
			$innerX1 = $centerX + $innerRadius * cos(deg2rad($startAngle));
			$innerY1 = $centerY + $innerRadius * sin(deg2rad($startAngle));
			$innerX2 = $centerX + $innerRadius * cos(deg2rad($endAngle));
			$innerY2 = $centerY + $innerRadius * sin(deg2rad($endAngle));

			// Draw donut slice
			$largeArc = ($endAngle - $startAngle) > 180 ? 1 : 0;
			$svg .= '<path d="M' . $outerX1 . ',' . $outerY1 . ' 
                A' . $outerRadius . ',' . $outerRadius . ' 0 ' . $largeArc . ',1 ' . $outerX2 . ',' . $outerY2 . ' 
                L' . $innerX2 . ',' . $innerY2 . ' 
                A' . $innerRadius . ',' . $innerRadius . ' 0 ' . $largeArc . ',0 ' . $innerX1 . ',' . $innerY1 . ' 
                Z" fill="' . $color . '"/>';

			// Draw label outside the donut
			$midAngle = $startAngle + ($endAngle - $startAngle) / 2;
			$labelRadius = $outerRadius * 1.1;
			$labelX = $centerX + $labelRadius * cos(deg2rad($midAngle));
			$labelY = $centerY + $labelRadius * sin(deg2rad($midAngle));

			$svg .= '<text x="' . $labelX . '" y="' . $labelY . '" font-size="12" text-anchor="middle" fill="#333">';
			$svg .= $item['label'] . ' (' . round($percentage * 100) . '%)';
			$svg .= '</text>';

			$startAngle = $endAngle;
		}

		// Add center text (optional)
		$svg .= '<text x="' . $centerX . '" y="' . $centerY . '" font-size="16" text-anchor="middle" dominant-baseline="middle" fill="#333">';
		$svg .= 'Total: ' . number_format($total);
		$svg .= '</text>';

		$svg .= '</svg>';
		return $svg;
	}
}
