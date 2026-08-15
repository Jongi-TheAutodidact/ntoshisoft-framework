<?php

/**
 * Client Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Client
{

	use Model;

	protected $table = 'clients';
	protected $primaryKey = 'id';

	// Participates in the NtoshiSoft offline-first / PWA sync engine.
	public bool $offlineEnabled = true;


	protected $allowedColumns = [
		'user_id',
		'identity_number',
		'address',
		'city',
		'province',
		'postal_code',
		'marital_status',
		'country',
		'status',
		'source_of_funds', 
		'nationality',
		'prem_col_date',
		'notes',
		'created_by',
		'updated_by',
		'date_updated',
	];

	public function validate(array $post_data, int|string|null $id = null): bool
	{
		$this->errors = [];

		if (empty($post_data['identity_number'])) {
			$this->errors['identity_number'] = "** ID Number is compulsary **";
		} else
        if (!preg_match("/^[0-9]{13}$/", $post_data['identity_number'])) {
            $this->errors['identity_number'] = "** ID number must be 13 digits long and contain only numbers **";
        }
		if (empty($post_data['address'])) {
			$this->errors['address'] = "** Address is required **";
		}
		if (empty($post_data['city'])) {
			$this->errors['city'] = "** City is required **";
		}
		if (empty($post_data['province'])) {
			$this->errors['province'] = "** Province is required **";
		}
		if (empty($post_data['country'])) {
			$this->errors['country'] = "** Country is required **";
		}
		if (empty($post_data['status'])) {
			$this->errors['status'] = "** Status is required **";
		}
		if (empty($post_data['prem_col_date'])) {
			$this->errors['prem_col_date'] = "** Premium Collection Date  required **";
		}

		if (empty($this->errors)) {
			return true;
		}

		return false;
	}

	public function allClientsWithUsersDetails(): array|false
	{
		$sql = "SELECT c.*,c.id AS client_id, u.*
				FROM clients c
				LEFT JOIN users u ON u.user_id = c.user_id
				ORDER BY c.date_created ASC";
		$result = $this->query($sql);
		if ($result) {
			return $result;
		}
		return false;
	}

	public function clientsWithUsersDetails(string $user_role): array|false
	{
		$sql = "SELECT c.*,c.id AS client_id, u.*
				FROM clients c
				LEFT JOIN users u ON u.user_id = c.user_id
				WHERE user_role = ?
				ORDER BY c.date_created ASC";
		$result = $this->query($sql, [$user_role]);
		if ($result) {
			return $result;
		}
		return false;
	}

	public function clientProfile(string $id): object|false
	{
	$sql = "SELECT c.id, c.identity_number, c.address, c.city, c.province, c.postal_code, c.country, c.status, c.date_created, u.image, u.user_id, u.firstname, u.surname, u.gender, u.user_role, u.phone, u.email
				FROM clients c
				LEFT JOIN users u  ON c.user_id = u.user_id
				WHERE c.user_id = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$id]);

		$result = $stmt->fetch(PDO::FETCH_OBJ); 

		if ($result) {
			return $result;
			return $result;
		}
		return false;
	}

	public function clientOwnProfile(int $id): object|false
	{
		$sql = "SELECT c.identity_number, c.address, c.city, c.province, c.postal_code, c.country, c.status, c.date_created, u.image, u.user_id, u.firstname, u.surname, u.gender, u.user_role, u.phone, u.email
				FROM clients c
				LEFT JOIN users u  ON c.user_id = u.user_id
				WHERE u.id = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$id]);

		$result = $stmt->fetch(PDO::FETCH_OBJ);

		if ($result) {
			return $result;
		}
		return false;
	}

	public function numClients(): int
	{
		$sql = "SELECT COUNT(*) FROM clients WHERE status = 'Active'";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchColumn();

		return $result;
	}

	public function getLastInsertId(): mixed
	{
		$stmt = $this->connect()->query("SELECT LAST_INSERT_ID() as last_id");
		return $stmt->fetch(PDO::FETCH_OBJ)->last_id;
	}

	public function updateUserRole(string $userId): void
	{
		$user = new User();

		$userRow = $user->first(['id' => $userId]);
		$role = $userRow->user_role ?? 'Client';

		$stmt = $this->connect()->prepare("UPDATE users SET user_role = ? WHERE user_id = ?");
		$stmt->execute([$role, $userId]);

		if ($stmt->rowCount() > 0) {
			echo "User role updated successfully";
		} else {
			echo "No user found with that ID";
		}
	}
	public function getClientStatusData(): array
	{
		$sql = "SELECT status AS label, COUNT(*) AS value 
            FROM clients 
            GROUP BY status";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getClientProvinceDistribution(): array
	{
		$sql = "SELECT province AS label, COUNT(*) AS value 
            FROM clients 
            GROUP BY province";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
