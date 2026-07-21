<?php

defined('ROOTPATH') or exit('Access Denied!');

class AdminController
{
	use Controller;

	public function __construct()
	{
		$user = new User();
		if(!user()){
			redirect('auth/login');
		}
	}

	public function index(): void
	{
		$settings = new Settings();
		$appSettings = $settings->loadSettings();

		$tables = $this->getExistingTables();

		$user = new User();
		$total_users = in_array('users', $tables) ? $user->getCount() : 0;

		$employee = new Employee();
		$total_employees = in_array('employees', $tables) ? $employee->getCount() : 0;

		if (in_array('payments', $tables)) {
			$payment = new Payment();
			$total_payments = $payment->numPayments();
			$payment_methods = $payment->getPaymentMethodsData();
			$payment_trend = $payment->getMonthlyPaymentsTrend(12);
		} else {
			$total_payments = 0;
			$payment_methods = [];
			$payment_trend = [];
		}

		if (in_array('expenditures', $tables)) {
			$expenditure = new Expenditure();
			$total_expenditure = $expenditure->sumAllExpenditures();
			$recent_expenditures = array_slice($expenditure->getAllExpenditures(), 0, 10);
		} else {
			$total_expenditure = null;
			$recent_expenditures = [];
		}

		if (in_array('meetings', $tables)) {
			$meeting = new Meeting();
			$upcoming_meetings = $meeting->getAllMeetings();
		} else {
			$upcoming_meetings = [];
		}

		$data = [
			'page_title' => 'Dashboard',
			'admin_name' => 'Admin',
			'total_users' => $total_users,
			'total_employees' => $total_employees,
			'total_payments' => $total_payments,
			'total_expenditure' => $total_expenditure,
			'recent_expenditures' => $recent_expenditures,
			'payment_methods' => $payment_methods,
			'payment_trend' => $payment_trend,
			'upcoming_meetings' => $upcoming_meetings,
			'settings' => $appSettings,
		];

		$this->view('admin/index', $data);
	}

	private function getExistingTables(): array
	{
		try {
			$db = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8mb4", DBUSER, DBPASS, [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			]);
			$stmt = $db->query("SHOW TABLES");
			return $stmt->fetchAll(PDO::FETCH_COLUMN);
		} catch (PDOException $e) {
			return [];
		}
	}

	public function saveSettings(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->jsonResponse(['error' => 'Method not allowed'], 405);
			return;
		}

		$input = json_decode(file_get_contents('php://input'), true);

		if (!$input) {
			$this->jsonResponse(['error' => 'Invalid input'], 400);
			return;
		}

		$settings = new Settings();

		foreach ($input as $key => $value) {
			$settings->set($key, $value);
		}

		$this->jsonResponse(['success' => true, 'message' => 'Settings saved successfully']);
	}

	private function jsonResponse(mixed $data, int $statusCode = 200): never
	{
		http_response_code($statusCode);
		header('Content-Type: application/json');
		echo json_encode($data);
		exit;
	}
}
