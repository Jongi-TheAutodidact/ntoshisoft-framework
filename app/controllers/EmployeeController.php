<?php

/**
 * EmployeeController class 
 */

defined('ROOTPATH') or exit('Access Denied!');

class EmployeeController
{
	use Controller;

	public function __construct()
	{
		$user = new User();
		if (!$user->logged_in()) {
			redirect('login');
		}
	} 

	public function index(): void
	{
		$employee = new Employee();
		$data['employees'] = $employee->getEmployeesWithUserDetails();
		$data['page_title'] = 'Employee Management';

		$this->view('admin/employees/employees', $data);
	}

	public function create(): void
	{
		$employee = new Employee();
		$user = new User();

		// Get users not already employees
		$data['available_users'] = $user->findAll();

		$_POST['employee_number'] = $employee->generateEmployeeNumber();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($employee->validate($_POST)) {
				// Insert Data into DB
				$employee->insert($_POST);
				Util::setFlash('employee_created', 'Employee record created successfully');
				redirect('admin/employees');
			}
		}

		$data['errors'] = $employee->errors;
		$data['page_title'] = 'Add New Employee';
		$this->view('admin/employees/employee-create', $data);
	}

	

	public function edit(?string $id = null): void
	{
		$employee = new Employee();
		$employee_detail = $employee->getSingleEmployeeWithUserDetails($id);

		$data = [
			'row'		=> $employee_detail,
		];

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($employee->validate($_POST, $id)) {
				// Submit Data To Update
				$employee->update($id, $_POST);
				Util::setFlash('employee_updated', 'Employee record updated successfully');
				redirect('admin/employees');
			}
		}

		$data['errors'] = $employee->errors;
		$data['page_title'] = 'Edit Employee';
		$this->view('admin/employees/employee-edit', $data);
	}

	public function detail(?string $id = null): void
	{
		$employee = new Employee();

		$employee_detail = $employee->getSingleEmployeeWithUserDetails($id);
		$employee_id = $employee_detail->id;

		$data = [
			'employee'		=> $employee_detail,
			'documents'		=> $employee->getDocuments($employee_id)
		];

		if (!$data['employee']) {
			Util::setFlash('employee_not_found', 'Employee not found', 'danger');
			redirect('admin/employees');
		}

		$data['page_title'] = 'Employee Profile';
		$this->view('admin/employees/employee-view', $data);
	}

	public function delete(?string $id = null): void
	{
		$employee = new Employee();
		$data['row'] = $employee->first(['id' => $id]);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($employee->delete($id)) {
				Util::setFlash('employee_deleted', 'Employee record deleted');
				redirect('admin/employees');
			}
		}

		$data['page_title'] = 'Delete Employee';
		$this->view('admin/employees/employee-delete', $data);
	}

	public function upload_document(string $id): void
	{
		$employee = new Employee();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (!empty($_FILES['document']['name'])) {
				$file_path = $employee->uploadDocument($id, $_FILES['document']);
				if ($file_path) {
					Util::setFlash('document_uploaded', 'Document uploaded successfully');
				} else {
					Util::setFlash('document_error', 'Document upload failed', 'danger');
				}
			}
			redirect('admin/employee/detail/' . $id);
		}

		$data['page_title'] = 'Employee Profile';
		$this->view('admin/employees/employee-view', $data);
	}

	public function delete_document(string $id, string $filename): void
	{
		$employee = new Employee();
		$folder = "uploads/employees/$id/docs/";
		$path = $folder . $filename;

		if (file_exists($path)) {
			unlink($path);
			Util::setFlash('document_deleted', 'Document removed');
		}
		redirect('admin/employees/view/' . $id);
	}

	public function update_performance(string $id): void
	{
		$employee = new Employee();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$data = [
				'performance_score' => $_POST['score'],
				'notes' => $_POST['evaluation_notes']
			];

			if ($employee->updatePerformance($id, $_POST['score'], $_POST['evaluation_notes'])) {
				Util::setFlash('performance_updated', 'Performance evaluation saved');
			}
			redirect('admin/employees/view/' . $id);
		}
	}

	public function update_schedule(string $id): void
	{
		$employee = new Employee();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$schedule = [
				'monday' => $_POST['monday'] ?? [],
				'tuesday' => $_POST['tuesday'] ?? [],
				// ... all days
				'special_notes' => $_POST['special_notes'] ?? ''
			];

			if ($employee->update($id, ['schedule' => json_encode($schedule)])) {
				Util::setFlash('schedule_updated', 'Work schedule updated');
			}
			redirect('admin/employees/view/' . $id);
		}
	}

	public function drivers(): void
	{
		$employee = new Employee();

		$data = [
			'page_title'		=> 'Company Drivers',
			'drivers'			=> $employee->getByPosition('Driver'),
		];
	
		$this->view('admin/employees/drivers', $data);
	}
}
