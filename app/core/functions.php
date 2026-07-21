<?php

declare(strict_types=1);

defined('ROOTPATH') or exit('Access Denied!');

/** check which php extensions are required **/
check_extensions();
function check_extensions(): void
{

	$required_extensions = [

		'gd',
		'mysqli',
		'pdo_mysql',
		'pdo_sqlite',
		'curl',
		'fileinfo',
		'intl',
		'exif',
		'mbstring',
	];

	$not_loaded = [];

	foreach ($required_extensions as $ext) {

		if (!extension_loaded($ext)) {
			$not_loaded[] = $ext;
		}
	}

	if (!empty($not_loaded)) {
		show("Please load the following extension(s) in your php.ini file: <br>" . "-" . implode("<br>", $not_loaded));
		die;
	}
}

/** returns a user readable date format **/
function get_date(string $date): string
{
	return date("jS M, Y", strtotime($date));
}

/**
 * Generates a date input field with today's date as default
 *
 * @param string $name       The name and id of the input
 * @param mixed  $value      Optional. Pre-filled value (e.g. from old_value or DB)
 * @param string $label      Optional label text
 * @param array  $attributes Optional attributes like class, required, etc.
 * @return string            HTML for the date input
 */
function get_date_input(string $name, string $value = '', string $label = '', array $attributes = []): string
{
	$id = $attributes['id'] ?? $name;
	$class = $attributes['class'] ?? 'form-control';
	$required = isset($attributes['required']) ? 'required' : '';

	// Use old_value if available, otherwise use today's date
	$displayValue = '';
	if (!empty($value)) {
		$displayValue = $value;
	} else {
		$displayValue = date('Y-m-d'); // Default to today
	}

	$html = "";

	if (!empty($label)) {
		$html .= "<label for=\"$id\" class=\"form-label\">$label</label>\n";
	}

	$html .= "<input type=\"date\" name=\"$name\" id=\"$id\" class=\"$class\" value=\"$displayValue\" $required>";

	return $html;
}

function show(mixed $stuff): void
{
	echo "<pre style='background-color:#519f44; color:#fff;padding:10px;margin-top:90px'>";
	print_r($stuff);
	echo '<br><hr>';
	echo '<em><strong>' . APP_NAME_SHORT . ' Debug: ' . date(DATE_RFC2822) . "<strong><em></pre>";
}

function formatSizeUnits(int $bytes): string {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

function esc(?string $str): string
{
	if($str)
		{
			return htmlspecialchars($str);
		}
	return '';
}


function redirect(string $path): never
{
	header("Location: " . ROOT . "/" . $path);
	die;
}

function old_value(string $key, string $default = ''): mixed
{
	if (!empty($_POST[$key]))
		return $_POST[$key];
	elseif (!empty($_FILES[$key]))
		return $_FILES[$key];

	return $default;
}

function user(string $key = ''): mixed
{
	if (!empty($_SESSION['user'])) {
		if (empty($key))
			return $_SESSION['user'];

		if (!empty($_SESSION['user']->$key)) {
			return $_SESSION['user']->$key;
		}
	}

	return '';
}


/**
 * Load an image, and if it does not exist, load a placeholder.
 *
 * @param string $file The path to the image file.
 * @param string $type The type of placeholder to use if the file does not exist.
 *                     Supported types: 'user', 'logo', 'favicon', 'vehicle'.
 * @return string The full path to the image file or the appropriate placeholder.
 */
/** load an image, and if it does not exist, load a placeholder **/
function get_image(string $file = '', string $type = 'post'): string
{

	$file = $file ?? '';
	if (file_exists($file)) {
		return ROOT . "/" . $file;
	}

	if ($type == 'user') {
		return ROOT . "/assets/img/user.png";
	} else if ($type == 'logo') {
		return ROOT . "/assets/img/logos/ntoshi-soft-white.png";
	} else if ($type == 'favicon') {
		return ROOT . "/assets/img/logos/favicon.ico";
	} else {
		return ROOT . "/assets/img/img-ph.png";
	}
}


function get_doc(string $doc_name = ''): string|false
{
	if (file_exists($doc_name))
		return ROOT . '/' . $doc_name;
	return false;
}

function extract_id_from_url(string $url): string
{
	// Parse the URL
	$parsed_url = parse_url($url);

	// Get the path component
	$path = $parsed_url['path'];

	// Split the path into segments
	$segments = explode('/', $path);

	// Extract the ID from the last segment
	$id = end($segments);

	return $id;
}

/**
 * Generates a month dropdown select element
 *
 */
function get_month_dropdown(string $name, string $selected = ''): string
{
	$months = [
		'January',
		'February',
		'March',
		'April',
		'May',
		'June',
		'July',
		'August',
		'September',
		'October',
		'November',
		'December'
	];

	$html = "<select name=\"$name\" id=\"$name\" class=\"form-control\">\n";
	$html .= "  <option value=\"\">-- Select Month --</option>\n";

	foreach ($months as $month) {
		$is_selected = ($month == $selected) ? ' selected' : '';
		$html .= "  <option value=\"$month\"$is_selected>$month</option>\n";
	}

	$html .= "</select>";

	return $html;
}

/**
 * Generates a year dropdown select element
 * Example usage - <?= get_year_dropdown('year', old_value('year'), -10, 10) ?>
 *
 */

function get_year_dropdown(string $name, string $selected = '', int $start = -5, int $end = 5): string
{
	$current_year = date('Y');
	$years = [];

	for ($i = $current_year + $start; $i <= $current_year + $end; $i++) {
		$years[] = $i;
	}

	$html = "<select name=\"$name\" id=\"$name\" class=\"form-control ntoshi-search\">\n";
	$html .= "  <option value=\"\">-- Select Year --</option>\n";

	foreach ($years as $year) {
		$is_selected = ($year == $selected) ? ' selected' : '';
		$html .= "  <option value=\"$year\"$is_selected>$year</option>\n";
	}

	$html .= "</select>";

	return $html;
}

/**
 * Generates a product dropdown select element
 * Requires $products array passed from controller
 * Example usage - <?= get_product_dropdown('product_name', old_value('product_name'), $products) ?>
 * Make sure you pass $data['products'] = $productModel->getAllProducts(); from the controller first.
 */
function get_product_dropdown(string $name, string $selected = '', array $products = []): string
{
	$html = "<select name=\"$name\" id=\"$name\" class=\"form-control\">\n";
	$html .= "  <option value=\"\">-- Select Product --</option>\n";

	if (!empty($products)) {
		foreach ($products as $prod) {
			$value = esc($prod->product_name);
			$is_selected = ($value == $selected) ? ' selected' : '';
			$html .= "  <option value=\"$value\"$is_selected>$value</option>\n";
		}
	}

	$html .= "</select>";

	return $html;
}

/**
 * Generates a supplier dropdown select element
 * Example usage - <?= get_supplier_dropdown('supplier', old_value('supplier'), $suppliers) ?>
 * Make sure you pass $data['suppliers'] = $supplierModel->getAllSuppliers(); from controller. first.
 */
function get_supplier_dropdown(string $name, string $selected = '', array $suppliers = []): string
{
	$html = "<select name=\"$name\" id=\"$name\" class=\"form-control\">\n";
	$html .= "  <option value=\"\">-- Select Supplier --</option>\n";

	if (!empty($suppliers)) {
		foreach ($suppliers as $supplier) {
			$value = esc($supplier->id);
			$label = esc($supplier->supplier_name);
			$is_selected = ($value == $selected) ? ' selected' : '';
			$html .= "  <option value=\"$value\"$is_selected>$label</option>\n";
		}
	}

	$html .= "</select>";

	return $html;
}


function image_selector(string $name, string $current_image = '', string $label = 'image', array $attributes = []): string
{
	$id = $attributes['id'] ?? $name;
	$class = $attributes['class'] ?? 'form-control';
	$required = isset($attributes['required']) ? 'required' : '';

	$image_path = get_image($current_image);
	$placeholder = get_image('', 'post');

	return <<<HTML
        <div class="mb-3">
            <label for="$id" class="form-label">$label</label>
            <input type="file" name="$name" id="$id" class="$class" $required>
            <small class="text-muted">Allowed types: JPEG, JPG, PNG, WEBP</small>
            <div class="mt-2">
                <img src="$image_path" alt="Current Image" id="preview_$id" class="img-thumbnail" style="max-height: 200px;">
            </div>
        </div>
        <script>
            document.getElementById('$id').addEventListener('change', function(event) {
                const [file] = event.target.files;
                if (file) {
                    document.getElementById('preview_$id').src = URL.createObjectURL(file);
                }
            });
        </script>
HTML;
}

function auto_fill_input(string $target_id, string $source_id, string $api_url, array $attributes = []): string
{
	$class = $attributes['class'] ?? 'form-control';
	$label = $attributes['label'] ?? '';
	$value = $attributes['value'] ?? '';

	return <<<HTML
        <div class="mb-3">
            <label class="form-label">$label</label>
            <input type="text" id="$target_id" class="$class" value="$value" readonly>
        </div>

        <script>
            document.getElementById('$source_id').addEventListener('change', function () {
                const selectedValue = this.value;
                if (!selectedValue) return;

                fetch('$api_url' + encodeURIComponent(selectedValue))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('$target_id').value = data.selling_price || '';
                        if (data.image) {
                            document.getElementById('preview_$target_id').src = data.image;
                        }
                    });
            });
        </script>
HTML;
}

/**
 * Generates a reusable status dropdown select element
 *
 * @param string $name          The name and id attribute for the select element
 * @param mixed  $selected      The selected status value
 * @param string $label         Optional label text
 * @param array  $statusOptions Array of status options (value => label)
 * @param array  $attributes    Additional HTML attributes (class, required, etc.)
 * @return string               HTML select element
 */
function get_status_dropdown(string $name, string $selected = '', string $label = '', array $statusOptions = [], array $attributes = []): string
{
	// Default set of statuses if none provided
	if (empty($statusOptions)) {
		$statusOptions = [
			'Pending' => 'Pending',
			'Processing' => 'Processing',
			'Completed' => 'Completed',
			'Cancelled' => 'Cancelled'
		];
	}

	$id = $attributes['id'] ?? $name;
	$class = $attributes['class'] ?? 'form-control';
	$required = isset($attributes['required']) ? 'required' : '';

	$html = "";

	if (!empty($label)) {
		$html .= "<label for=\"$id\" class=\"form-label\">$label</label>\n";
	}

	$html .= "<select name=\"$name\" id=\"$id\" class=\"$class\" $required>\n";
	$html .= "  <option value=\"\">-- Select Status --</option>\n";

	foreach ($statusOptions as $value => $label) {
		$is_selected = ($value == $selected) ? ' selected' : '';
		$html .= "  <option value=\"" . esc($value) . "\"$is_selected>" . esc($label) . "</option>\n";
	}

	$html .= "</select>";

	return $html;
}


/**
 * Generates a reusable status dropdown select element
 *
 * @param string $name          The name and id attribute for the select element
 * @param mixed  $selected      The selected status value
 * @param string $label         Optional label text
 * @param array  $statusOptions Array of status options (value => label)
 * @param array  $attributes    Additional HTML attributes (class, required, etc.)
 * @return string               HTML select element
 */
function get_payment_type_dropdown(string $name, string $selected = '', string $label = '', array $statusOptions = [], array $attributes = []): string
{
	// Default set of statuses if none provided
	if (empty($statusOptions)) {
		$statusOptions = [
			'Cash' => 'Cash',
			'EFT' => 'EFT',
			'Send Cash' => 'Send Cash',
			'Bitcoin' => 'Bitcoin',
			'Credit/Debit Card' => 'Credit/Debit Card',
			'Other' => 'Other',
		];
	}

	$id = $attributes['id'] ?? $name;
	$class = $attributes['class'] ?? 'form-control';
	$required = isset($attributes['required']) ? 'required' : '';

	$html = "";

	if (!empty($label)) {
		$html .= "<label for=\"$id\" class=\"form-label\">$label</label>\n";
	}

	$html .= "<select name=\"$name\" id=\"$id\" class=\"$class\" $required>\n";
	$html .= "  <option value=\"\">-- Select Type --</option>\n";

	foreach ($statusOptions as $value => $label) {
		$is_selected = ($value == $selected) ? ' selected' : '';
		$html .= "  <option value=\"" . esc($value) . "\"$is_selected>" . esc($label) . "</option>\n";
	}

	$html .= "</select>";

	return $html;
}


/**
 * Generates a Bootstrap badge for Yes/No values (1 = Yes, 0 = No)
 *
 * @param mixed $value Can be 'Yes', 'No', true, false, 1, 0
 * @return string      HTML badge output
 */
function yesNoBadge(mixed $value): string
{
	// Normalize input
	if (is_string($value)) {
		$value = strtolower($value);
		if ($value === 'yes' || $value === '1' || $value === 'true') {
			return '<span class="badge bg-success">Yes</span>';
		} elseif ($value === 'no' || $value === '0' || $value === 'false') {
			return '<span class="badge bg-danger">No</span>';
		}
	} elseif (is_numeric($value)) {
		return $value == 1
			? '<span class="badge bg-success">Yes</span>'
			: '<span class="badge bg-danger">No</span>';
	} elseif (is_bool($value)) {
		return $value
			? '<span class="badge bg-success">Yes</span>'
			: '<span class="badge bg-danger">No</span>';
	}

	return '<span class="badge bg-secondary">N/A</span>';
}


function displayFormHeaderOnCreate(): string
{
	ob_start(); ?>
	<!--CSRF TOKEN-->
	<input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
	<!--USER CREATING RECORD-->
	<input type="hidden" name="<?= esc('created_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
	<?php if (!empty($errors)) : ?>
		<div class="alert alert-danger text-center col-lg-12">
			<?= implode('<br>', $errors);  ?>
		</div>
	<?php endif; ?>
<?php
	return ob_get_clean();
}

function displayFormHeaderOnUpdate(): string
{
	ob_start(); ?>
	<!--CSRF TOKEN-->
	<input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
	<!--USER EDITING RECORD-->
	<input type="hidden" name="<?= esc('updated_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
	<!--DATE RECORD UPDATED-->
	<input type="hidden" name="<?= esc('date_updated') ?>" value="<?= date('Y-m-d H:i:s') ?>">
	<?php if (!empty($errors)) : ?>
		<div class="alert alert-danger text-center col-lg-12">
			<?= implode('<br>', $errors);  ?>
		</div>
	<?php endif; ?>
<?php
	return ob_get_clean();
}

function displayFormHeaderOnDelete(): string
{
	ob_start(); ?>
	<!--CSRF TOKEN-->
	<input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
	<!--USER DELETING RECORD-->
	<input type="hidden" name="<?= esc('deleted_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
	<!--DATE RECORD DELETED-->
	<input type="hidden" name="<?= esc('date_deleted') ?>" value="<?= date('Y-m-d H:i:s') ?>">
<?php
	return ob_get_clean();
}

function log_activity(string $action, string $entity_type, int|string|null $entity_id, string $description = ''): void
{
    $log = new ActivityLog();
    $log->insert([
        'user_id'    => user('user_id') ?: 0,
        'user_name'  => user('firstname') . ' ' . user('surname'),
        'action'     => $action,
        'entity_type'=> $entity_type,
        'entity_id'  => (int)$entity_id,
        'description'=> $description,
        'ip_address' => get_ip(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    ]);
}

function get_ip(): string
{
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	} else {
		$ip = 'Unknown';
	}

	return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : 'Unknown';
}

function get_device_info(): array
{
	$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

	// Detect device type
	$device = 'Unknown';
	if (preg_match('/mobile/i', $user_agent)) {
		$device = 'Mobile';
	} elseif (preg_match('/tablet/i', $user_agent)) {
		$device = 'Tablet';
	} elseif (preg_match('/linux/i', $user_agent)) {
		$device = 'Linux PC';
	} elseif (preg_match('/mac/i', $user_agent)) {
		$device = 'Mac';
	} elseif (preg_match('/windows/i', $user_agent)) {
		$device = 'Windows';
	}

	// Browser
	$browser = 'Unknown';
	if (preg_match('/firefox/i', $user_agent)) {
		$browser = 'Firefox';
	} elseif (preg_match('/safari/i', $user_agent)) {
		$browser = 'Safari';
	} elseif (preg_match('/chrome/i', $user_agent)) {
		$browser = 'Chrome';
	} elseif (preg_match('/edge/i', $user_agent)) {
		$browser = 'Edge';
	} elseif (preg_match('/opera/i', $user_agent)) {
		$browser = 'Opera';
	}

	return [
		'device' => $device,
		'browser' => $browser,
		'user_agent' => $user_agent
	];
}

function get_location_from_ip(string $ip): array
{
	if ($ip === '127.0.0.1' || $ip === 'Unknown') return ['country' => 'Localhost', 'city' => 'Localhost'];

	$api_url = "http://ip-api.com/json/{$ip}";

	$response = json_decode(file_get_contents($api_url));

	if ($response && $response->status === 'success') {
		return [
			'country' => $response->country,
			'city' => $response->city,
			'region' => $response->regionName,
			'lat' => $response->lat,
			'lon' => $response->lon,
			'zip' => $response->zip,
			'timezone' => $response->timezone
		];
	}

	return [
		'country' => 'Unknown',
		'city' => 'Unknown'
	];
}

function getStatusColor(string $status): string
{
	$colors = [
		'reported' => 'secondary',
		'captured' => 'info',
		'allocated' => 'primary',
		'unrecoverable' => 'danger',
		'undetected' => 'warning',
		'withdrawn' => 'dark',
		'recovered' => 'success',
		'closed' => 'success',
		'referred' => 'info'
	];

	return $colors[$status] ?? 'secondary';
}

function selected(mixed $value, mixed $compare): string
{
	return $value == $compare ? 'selected' : '';
}

/**
 * Truncates a string to a specified length without cutting words
 * 
 * @param string $string The input string
 * @param int $length Maximum length of the truncated string
 * @param string $suffix String to append if truncated (default: '...')
 * @param bool $preserve_words Whether to preserve whole words (default: true)
 * @param string $encoding Character encoding (default: 'UTF-8')
 * @return string Truncated string
 */
function truncate(string $string, int $length = 100, string $suffix = '...', bool $preserve_words = true, string $encoding = 'UTF-8'): string
{
	if (mb_strlen($string, $encoding) <= $length) {
		return $string;
	}

	if (!$preserve_words) {
		return rtrim(mb_substr($string, 0, $length, $encoding)) . $suffix;
	}

	// Find the last space within length
	$last_space = mb_strrpos(mb_substr($string, 0, $length, $encoding), ' ', 0, $encoding);

	// If no space found, just truncate
	if ($last_space === false) {
		return mb_substr($string, 0, $length, $encoding) . $suffix;
	}

	return mb_substr($string, 0, $last_space, $encoding) . $suffix;
}

/**
 * Alias for truncate with word preservation off
 */
function truncate_hard(string $string, int $length = 100, string $suffix = '...', string $encoding = 'UTF-8'): string
{
	return truncate($string, $length, $suffix, false, $encoding);
}

/**
 * Truncates middle of a string (useful for long IDs/emails)
 */
function truncate_middle(string $string, int $length = 100, string $suffix = '...', string $encoding = 'UTF-8'): string
{
	if (mb_strlen($string, $encoding) <= $length) {
		return $string;
	}

	$part_length = (int) floor(($length - mb_strlen($suffix, $encoding)) / 2);

	return mb_substr($string, 0, $part_length, $encoding) . $suffix .
		mb_substr($string, -$part_length, null, $encoding);
}

/**
 * Extracts birth date from South African ID number
 * 
 * @param string $idNumber The SA ID number (13 digits)
 * @return DateTime|false Returns DateTime object or false on failure
 */
function extractBirthDateFromSAID(string $idNumber): DateTime|false
{
	// Validate ID number format (YYMMDD...)
	if (!preg_match('/^[0-9]{13}$/', $idNumber)) {
		return false;
	}

	// Extract date parts
	$yearPrefix = (int)substr($idNumber, 0, 2);
	$month = (int)substr($idNumber, 2, 2);
	$day = (int)substr($idNumber, 4, 2);

	// Determine full year (handles 1900s and 2000s)
	$currentYear = (int)date('y');
	$fullYear = ($yearPrefix <= $currentYear)
		? 2000 + $yearPrefix
		: 1900 + $yearPrefix;

	// Validate the date
	if (!checkdate($month, $day, $fullYear)) {
		return false;
	}

	// Create DateTime object
	try {
		return new DateTime("$fullYear-$month-$day");
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Uploads a file with security checks
 * 
 * @param array $file The $_FILES array element
 * @param int $key The array key if multiple files
 * @param string $target_dir The directory to upload to (with trailing slash)
 * @param array $allowed_types Allowed file extensions (default: images and PDF)
 * @param int $max_size Maximum file size in MB (default: 5MB)
 * @return string|false Returns filename on success, false on failure
 */
function upload_file(array $file, int $key = 0, string $target_dir, array $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'], int $max_size = 5): string|false
{
	// Create target directory if it doesn't exist
	if (!file_exists($target_dir)) {
		mkdir($target_dir, 0777, true);
	}

	// Handle both single and multiple file uploads
	if (is_array($file['name'])) {
		// Multiple file upload
		$file_name = $file['name'][$key];
		$file_tmp = $file['tmp_name'][$key];
		$file_size = $file['size'][$key];
		$file_error = $file['error'][$key];
	} else {
		// Single file upload
		$file_name = $file['name'];
		$file_tmp = $file['tmp_name'];
		$file_size = $file['size'];
		$file_error = $file['error'];
	}

	// Check for upload errors
	if ($file_error !== UPLOAD_ERR_OK) {
		error_log("File upload error: $file_error");
		return false;
	}

	// Validate file size
	$max_size_bytes = $max_size * 1024 * 1024;
	if ($file_size > $max_size_bytes) {
		error_log("File too large: $file_name ($file_size bytes)");
		return false;
	}

	// Get file extension
	$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

	// Validate file type
	if (!in_array($file_ext, $allowed_types)) {
		error_log("Invalid file type: $file_name");
		return false;
	}

	// Generate unique filename to prevent overwrites
	$unique_name = date('Ymd_His') . '.' . $file_ext;
	$target_path = $target_dir . $unique_name;

	// Move the file securely
	if (move_uploaded_file($file_tmp, $target_path)) {
		// Additional security: check if file is really an image (for image types)
		if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
			$image_info = getimagesize($target_path);
			if (!$image_info) {
				unlink($target_path); // Remove the file if it's not a valid image
				error_log("Invalid image file: $file_name");
				return false;
			}
		}

		return $unique_name;
	}

	error_log("Failed to move uploaded file: $file_name");
	return false;
}

/**
 * Extracts gender from a South African ID number
 * 
 * @param string $idNumber South African ID number (13 digits)
 * @return string 'Male' or 'Female'
 * @throws InvalidArgumentException If ID number is invalid
 */
function getGenderFromSAID(string $idNumber): string
{
    // Validate ID number format
    if (strlen($idNumber) !== 13 || !ctype_digit($idNumber)) {
        throw new InvalidArgumentException("Invalid South African ID number. Must be exactly 13 digits.");
    }
    
    // The 7th digit (index 6) determines gender
    $genderDigit = (int)$idNumber[6];
    
    // 0-4 indicates female, 5-9 indicates male
    return $genderDigit < 5 ? 'Female' : 'Male';
}



