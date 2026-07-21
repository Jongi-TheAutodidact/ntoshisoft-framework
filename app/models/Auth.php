<?php

/**
 * Auth Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Auth
{

	use Model;

	protected $allowedColumns = [];

	public function validate(array $data): bool
	{
		$this->errors = [];

		if (empty($data['email'])) {
			$this->errors['email'] = "Email is required";
		} else
		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			$this->errors['email'] = "Email is not valid";
		}

		return empty($this->errors);
	}

	public function resetPassword(string $email): string
	{
		$token = bin2hex(random_bytes(16));
		$token_hash = hash('sha256', $token);
		$expiry = date('Y-m-d H:i:s', time() + 60 * 30);

		$sql = "UPDATE users SET 
            reset_token_hash = ?,
            reset_token_expires_at = ?
            WHERE email = ?";

		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$token_hash, $expiry, $email]);

		return $token; // Return plain token for email
	}

	public function getUserByResetToken(string $token_hash): object|false
	{
		$sql = "SELECT * FROM users 
            WHERE reset_token_hash = ?
            AND reset_token_expires_at > NOW()";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$token_hash]);
		return $stmt->fetch(PDO::FETCH_OBJ);
	}
}
