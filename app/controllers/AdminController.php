<?php

defined('ROOTPATH') or exit('Access Denied!');

class AdminController
{
	use Controller;

	public function __construct()
	{
		if (!user()) 
		{
			redirect('auth/login');
		}
	}

	public function index(): void
	{
		if (!in_array(user('user_role'), STAFF_CHAT)) {
			// Redirect to no access page
			redirect('admin/no-access');
			exit();
		}
		
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

	public function no_access(): void
	{
		$data['page_title'] = 'No Access';
		$this->view('admin/users/no-access', $data);
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

	public function companyDetails($id = null)
	{
		$user = new User();
		$company_detail = new CompanyDetail();

		// Check if current user is logged in 
		if (!$user->logged_in())
			redirect('auth/login');

		$company_detail->limit = 1;
		$data['company_details'] = $company_detail->findAll();
		$data['admin_user'] = $user->adminUser();

		$data['row'] = $company_detail->first(['id' => $id]);

		$data['page_title'] = 'Company Details';


		$this->view('admin/company/company_details', $data);
	}

	public function companyDetailsEdit($id = null)
	{
		$user = new User();
		$company_detail = new CompanyDetail();

		// Check if current user is logged in 
		if (!$user->logged_in())
			redirect('auth/login');

		// Create Logo Folder
		$folder = 'uploads/logo/';
		if (!file_exists($folder)) {
			mkdir($folder, 0777, true);
		}

		$company_detail->limit = 1;
		$data['company_details'] = $company_detail->findAll();
		$data['admin_user'] = $user->adminUser();

		$data['row'] = $company_detail->first(['id' => $id]);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($_FILES['image'] && $_FILES['image']['error'] == UPLOAD_ERR_OK && $company_detail->validate($_FILES, $_POST, $id)) {
				$destination = $folder . time() . '_' . $_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $destination);

				$_POST['image'] = $destination;
				if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
					die('Invalid CSRF Token!');
				} else {

					$company_detail->update($id, $_POST);
					Util::setFlash('company_details_update_success', 'Company details updated Successfully!!');
					redirect('admin/company');
				}
			}
		}

		$data['errors'] = $company_detail->errors;
		$data['page_title'] = 'Edit Company Details';


		$this->view('admin/company/company-details-edit', $data);
	}

	public function socialLinks($id = null)
	{
		$user = new User();
		$social_link = new SocialLink();

		// Check if current user is logged in 
		if (!$user->logged_in())
			redirect('auth/login');

		$social_link->limit = 1;
		$data['social_links'] = $social_link->findAll();
		$data['admin_user'] = $user->adminUser();

		$data['page_title'] = 'Social Links';


		$this->view('admin/company/social_link', $data);
	}

	public function socialLinksEdit($id = null)
	{
		$user = new User();
		$social_link = new SocialLink();

		// Check if current user is logged in 
		if (!$user->logged_in())
			redirect('auth/login');

		$social_link->limit = 1;
		$data['social_links'] = $social_link->findAll();
		$data['admin_user'] = $user->adminUser();

		$data['row'] = $social_link->first(['id' => $id]);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($social_link->validate($_POST, $id)) {

				$social_link->update($id, $_POST);
				Util::setFlash('link_update_success', 'Social Links updated Successfully!!');
				redirect('admin/social');
			}
		}

		$data['errors'] = $social_link->errors;
		$data['page_title'] = 'Edit Social Links';


		$this->view('admin/company/social-link-edit', $data);
	}

	public function operatingHours($id = null)
	{
		$user = new User();
		$op_hour = new OperatingHour();


		// Check if current user is logged in 
		if (!$user->logged_in())
			redirect('auth/login');

		$op_hour->limit = 1;
		$data['op_hours'] = $op_hour->findAll();
		$data['admin_user'] = $user->adminUser();

		$data['page_title'] = 'Operating Hours';

		$this->view('admin/company/op-hours', $data);
	}

	public function operatingHoursEdit($id = null)
	{
		$user = new User();
		$op_hour = new OperatingHour();


		// Check if current user is logged in 
		if (!$user->logged_in())
			redirect('auth/login');

		$op_hour->limit = 1;
		$data['op_hours'] = $op_hour->findAll();
		$data['admin_user'] = $user->adminUser();

		$data['row'] = $op_hour->first(['id' => $id]);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($op_hour->validate($_POST, $id)) {

				$op_hour->update($id, $_POST);
				Util::setFlash('ophours_update_success', 'Business Hours updated Successfully!!');
				redirect('admin/hours');
			}
		}

		$data['errors'] = $op_hour->errors;
		$data['page_title'] = 'Edit Operating Hours';


		$this->view('admin/company/op-hours-edit', $data);
	}
}
