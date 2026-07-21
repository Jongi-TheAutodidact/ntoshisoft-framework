<?php

/**
 * UserController class 
 */

defined('ROOTPATH') or exit('Access Denied!');

class UserController
{
	use Controller;

	public function __construct()
	{
		$user = new User();

		// Check if current user is logged in 
		if (!$user->logged_in())
			redirect('auth/login');
	}

	public function index(?string $id = null): void
	{
		if(user('user_role') != 'Admin')
			{
				Util::setFlash('no_access','Oops, Apologies Buddy, you do not have permission to access the page you\'ve just requested!');
				redirect('home');
			}
			
		$user = new User();


		// Create Users' Profile Folder
		$folder = 'uploads/users/';
		if (!file_exists($folder)) {
			mkdir($folder, 0777, true);
		}

		// Check if current user is looged in 
		if (!$user->logged_in())
			redirect('auth/login');

		$data['rows'] = $user->findAll();

		$data['errors'] = $user->errors;
		$data['page_title'] = 'Users';


		$this->view('admin/users/users', $data);
	}

	public function create(): void
	{
		$user = new User();

		// Create Users' Profile Folder
		$folder = 'uploads/users/';
		if (!file_exists($folder)) {
			mkdir($folder, 0777, true);
		}

		// Check if current user is logged in 
		if (!$user->logged_in())
			redirect('auth/login');

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($user->validate($_FILES, $_POST)) {
				// Upload User Profile Image
				$destination = $folder . time() . '_' . $_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $destination);

				$_POST['image'] = $destination;

				if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
					die('Invalid CSRF Token!');
				} else {
					// Check if email does not exist
					$user_email = $user->getUserByEmail($_POST['email']);
					$username   = $user->getUserByUsername($_POST['username']);

					if ($user_email) {
						Util::setFlash('email_exists_error', 'Email already in use by another user!!');
						redirect('admin/users/new');
					} else if ($username) {
						Util::setFlash('username_exists_error', 'Username already in use by another user!!');
						redirect('admin/users/new');
					} else {

						$_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

						// Insert New User details into DB
						
						$user->insert($_POST);
						Util::setFlash('register_success', 'User Registered Successfully!!');
						redirect('admin/users');
					}
				}
			}
		} 

		$data['errors'] = $user->errors;
		$data['page_title'] = 'Create New User';

		$this->view('admin/users/create', $data);
	}

	public function profile(?string $id = null): void
	{
		$user = new User();

		$data['singleUser'] = $user->first(['id' => $id]);

		$data['page_title'] = 'User Profile';

		$this->view('admin/users/user-profile', $data);
	}

	public function edit(?string $id = null): void
	{
		$user = new User();


		// Create Users' Profile Folder
		$folder = 'uploads/users/';
		if (!file_exists($folder)) {
			mkdir($folder, 0777, true);
		}

		// Check if current user is looged in 
		if (!$user->logged_in())
			redirect('auth/login');

		$data['row'] = $user->first(['id' => $id]);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($user->validate($_FILES, $_POST)) {
				// Check if password is available, if not there's nothing to update
				if (empty($_POST['password'])) {
					unset($_POST['password']);
				} else { // else update the password
					$_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
				}

				// Update the uploaded User Profile image
				$destination = $folder . time() . '_' . $_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $destination);

				$_POST['image'] = $destination;

				if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
					die('Invalid CSRF Token!');
				} else {
					// Update User
					$user->update($id, $_POST);
					Util::setFlash('user_update_success', 'User record updated successfully!!');

					// Delete the old image
					if (file_exists($data['row']->image))
						unlink($data['row']->image);

					redirect('admin/users');
				}
			}
		}

		$data['errors'] = $user->errors;
		$data['page_title'] = 'Edit User';


		$this->view('admin/users/edit', $data);
	}

	public function delete(?string $id = null): void
	{
		$user = new User();

		// Check if current user is looged in 
		if (!$user->logged_in())
			redirect('auth/login');

		$data['row'] = $user->first(['id' => $id]);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
				die('Invalid CSRF Token!');
			} else {
				$user->delete($id);

				// Delete the image
				if (file_exists($data['row']->image))
					unlink($data['row']->image);

				Util::setFlash('user_delete_success', 'User deleted successfully!!');
				redirect('admin/users');
			}
		}

		$data['errors'] = $user->errors;
		$data['page_title'] = 'Delete User';


		$this->view('admin/users/delete', $data);
	}
}
