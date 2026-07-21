<?php
defined('ROOTPATH') or exit('Access Denied!');

/**
 * The User Model Class
 */

class User
{

	use Model;


	protected $table = 'users';


	protected $allowedColumns = [
		'image',
		'user_id',
		'firstname',
		'surname',
		'username',
		'email',
		'phone',
		'gender',
		'user_role',
		'password',
		'otp_code',
		'otp_expires',
		'email_verified_at',
		'reset_token_hash',
		'created_by',
		'updated_by',
		'date_updated',
	];

	public function validate(array $files_data, array $post_data, int|string|null $id = null): bool
	{
		// Split URL
		$url = isset($_GET['url']) ? $_GET['url'] : null;

		if ($url) {
			// Remove trailing slash if present
			$url = rtrim($url, '/');

			// Sanitize it
			$url = filter_var($url, FILTER_SANITIZE_URL);

			// Split into individual params
			$params = explode('/', $url);
		} 

		// Check if email does not exist
		$user_email = $this->getUserByEmail($_POST['email']);
		$username = $this->getUserByUsername($_POST['username']);

		$action = '';
		$this->errors = [];

		// Allowed File types
		$allowed_types = [
			'image/jpeg',
			'image/jpg',
			'image/png',
			'image/webp'
		];

		// Image validation - Check inside the file 
		if (!isset($files_data['image']['type']) && !in_array($files_data['image']['type'], $allowed_types)) {
			$this->errors['image'] = 'Invalid Image File Type. Only types: ' . implode(', ', $allowed_types) . ' allowed!';
		}

		// // Other inputs validation
		// if (empty($post_data['email'])) {
		// 	$this->errors['email'] = "Email is required";
		// } else
		// if (!filter_var($post_data['email'], FILTER_VALIDATE_EMAIL)) {
		// 	$this->errors['email'] = "Email is not valid";
		// } else
		// if ($user_email && $params[2] != 'edit') {
		// 	$this->errors['email'] = "Email already in use by another user!!";
		// } else 
			if ($username && $params[2] != 'edit') {
			$this->errors['username'] = "Username already in use by another user!";
		}

		if (empty($post_data['firstname'])) {
			$this->errors['firstname'] = "First Name is required";
		}
		if (empty($post_data['surname'])) {
			$this->errors['surname'] = "Surname is required";
		}

		if (empty($post_data['phone'])) {
			$this->errors['phone'] = "Phone is required";
		}

		if ($post_data['user_role'] == 'Select Role') {
			$this->errors['user_role'] = "User Role is required";
		}
		if ($post_data['gender'] == 'Select Gender') {
			$this->errors['gender'] = "Gender is required";
		}

		if (!$id && (empty($post_data['password']) && $action == 'new')) {
			$this->errors['password'] = "Password is required";
		}



		if (empty($this->errors)) {
			return true;
		}

		return false;
	}
	public function pwd_validate(array $data, int|string|null $id = null): bool
	{
		$action = '';
		$this->errors = [];

		if (strlen($data['password']) < 8) {
			$this->errors['password'] = "Password must be at least 8 characters";
		}
		if (!preg_match("/[a-z]/i", $data['password'])) {
			$this->errors['password'] = "Password must contain at least 1 character";
		}
		if (!preg_match("/[0-9]/", $data['password'])) {
			$this->errors['password'] = "Password must contain at least 1 numeric value";
		}
		if ($data['password'] !== $_POST['rp_password']) {
			$this->errors['password'] = "Passwords must match";
		}

		if (empty($this->errors)) {
			return true;
		}

		return false;
	}

	public function authenticate(object $row): void
	{
		// Declare Session Variables
		$_SESSION['user'] = $row;
		$_SESSION['userRole'] = $row->user_role;
		$_SESSION['firstname'] = $row->firstname;
		$_SESSION['surname'] = $row->surname;
		$_SESSION['username'] = $row->username;
		$_SESSION['email'] = $row->email;
		$_SESSION['id'] = $row->id;
		$_SESSION['user_id'] = $row->user_id;

		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		$_SESSION['status'] = '';
	}

	public function logout(): void
	{
		if (!empty($_SESSION['user'])) {
			unset($_SESSION['user']);
			// session_destroy();
		}
	}

	public function logged_in(): bool
	{
		if (!empty($_SESSION['user']))
			return true;

		return false;
	}

	public function adminUser(): bool
	{
		if (!empty($_SESSION['user']) && $_SESSION['userRole'] == 'Admin')
			return true;

		return false;
	}

	public function userRowCount(): int
	{
		$sql = "SELECT COUNT(*) FROM users";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchColumn();

		return $result;
	}

	// Check if email already exists
	public function getUserByEmail(string $email): object|false
	{
		$sql = "SELECT * FROM users WHERE email = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$email]);
		$user = $stmt->fetch(PDO::FETCH_OBJ);
		return $user;
	}
	// Check if username already exists
	public function getUserByUsername(string $username): object|false
	{
		$sql = "SELECT * FROM users WHERE username = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$username]);
		$user = $stmt->fetch(PDO::FETCH_OBJ);
		return $user;
	}

	// Update password reset method
	public function createPasswordReset(string $email): string
	{
		$token = bin2hex(random_bytes(16));
		$token_hash = hash('sha256', $token);
		$expiry = date('Y-m-d H:i:s', time() + 1800); // 30 minutes

		$sql = "UPDATE users SET 
            reset_token_hash = ?,
            reset_token_expires_at = ?
            WHERE email = ?";

		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$token_hash, $expiry, $email]);

		return $token; // Return plain token for email
	}

	// Update password method
	public function updatePassword(int $id, string $password): void
	{
		$sql = "UPDATE users SET 
            password = ?,
            reset_token_hash = NULL,
            reset_token_expires_at = NULL
            WHERE id = ?";

		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
	}

	public function updatePwd(int $id): bool
	{
		$sql = "UPDATE users 
				SET reset_token_hash = NULL, reset_token_expires_at = NULL
				WHERE id = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$id]);

		return true;
	}

	// Get User UPdating Password
	public function getTheUserUpdatingPassword(string $user_id, string $reset_token_hash, string $email): object|array
	{
		$sql = "SELECT * FROM users
            WHERE user_id = ? 
              AND reset_token_hash = ? 
              AND email = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$user_id, $reset_token_hash, $email]);
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		return $result ?: [];
	}

	public function tokenInURL(string $reset_token_hash): object|false
	{
		$sql = "SELECT * FROM users WHERE reset_token_hash = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$reset_token_hash]);
		$reset_token_hash = $stmt->fetch(PDO::FETCH_OBJ);
		return $reset_token_hash;
	}

	public function updateByUserId(string $user_id, array $data): array|false
	{
		if (empty($data) || empty($user_id)) {
			return false;
		}

		$fields = [];
		$params = [];

		foreach ($data as $column => $value) {
			$fields[] = "{$column} = :{$column}";
			$params[$column] = $value;
		}

		$params['user_id'] = $user_id;

		$sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
		return $this->query($sql, $params);
	}

	// Update User (On Policy Application Approval)
	public function updateUser(int $id, array $data): bool
	{
		$approver = user('firstname') . ' ' . user('firstname');

		$data = [
			"user_role"    => "Client",
			"updated_by"   => $approver,
			"date_updated" => date("Y-m-d H:i:s")
		];

		return $this->update($id, $data, 'id');
	}

	public function deleteClient(string $user_id): bool
	{
		$sql = "DELETE * FROM clients WHERE user_id = ? ";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$user_id]);

		return true;
	}
}
