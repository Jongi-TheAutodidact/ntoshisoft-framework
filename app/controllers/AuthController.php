<?php

/**
 * AuthController class 
 */

defined('ROOTPATH') or exit('Access Denied!');



class AuthController
{
	use Controller;

	public function login(): void
	{
	
		$data['errors'] = [];

		/*** LOGIN USER ***/
		$data['page_title'] = 'Login';
		if (!empty($_SESSION['user'])) {
			redirect('home');
		} else
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$user = new User();
			// Check if the input is an email or username
			$input = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
			$row = $user->first([$input => $_POST['email']]);

			if ($row && password_verify($_POST['password'], $row->password)) {
				$user->authenticate($row);
				$data['remember'] = $_POST['remember'];
				$data['sess_email'] = $_SESSION['email'];
				log_activity('LOGIN', 'user', $row->user_id ?? 0, 'User logged in: ' . ($row->firstname ?? '') . ' ' . ($row->surname ?? ''));
				if (isset($_POST['remember'])) {
					setcookie('remember_email', $data['sess_email'], time() + 3600 * 24 * 365);
					setcookie('remember', $data['remember'], time() + 3600 * 24 * 365);
				} else {
					setcookie('remember_email', "", time() - 3600);
					setcookie('remember', "", time() - 3600);
				}

				redirect('home');
			}


			$data['errors']['email'] = 'Wrong email/username or password!';
		}



		/*** DISPLAY THE VIEW PAGE ***/
		$this->view('front/login', $data);
	}

	public function register(): void
	{
		$user = new User();

		// Create Users' Profile Folder
		$folder = 'uploads/users/';
		if (!file_exists($folder)) {
			mkdir($folder, 0777, true);
		}

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
						// Hash The Submitted Password
						$_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

						// Default User Role
						$_POST['user_role'] = 'User';

						// Insert New User details into DB
						$user->insert($_POST);
						
						Util::setFlash('register_success', 'Congratulations! You are now registered Successfully!! Use the credentials you created earlier to login on the menu navigation. Your username is ' . $_POST['username']);
						
						redirect('');
					}
				}
			}
		}

		$data['errors'] = [];
		$data['page_title'] = 'Register';

		/*** DISPLAY THE VIEW PAGE ***/
		$this->view('front/register', $data);
	}

	public function logout(): never
	{
		log_activity('LOGOUT', 'user', user('user_id') ?: 0, 'User logged out: ' . user('firstname') . ' ' . user('surname'));
		$user = new User();
		$user->logout();
		redirect('auth/login');
	}

}
