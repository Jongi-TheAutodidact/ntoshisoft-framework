<?php
declare(strict_types=1);
defined('ROOTPATH') or exit('Access Denied!');

/**
 * Model Core Class (Main Model)
 */

trait Model
{
	use Database;

	public $limit = 100;
	public $offset = 0;
	public $order_type 	= "desc";
	public $order_column = "id";
	public $errors 		= [];

	/**
	 * Offline-first / PWA sync option.
	 *
	 * A model opts into the framework's offline sync engine by declaring its own
	 * opt-in property (the trait deliberately does NOT declare it — PHP forbids a
	 * class from redefining a trait property with a different default, and some
	 * business models already define a column of their own with a similar name):
	 *
	 *     public bool $offlineEnabled = true;             // opt into offline sync
	 *     public string $offlineKey = 'user_id';          // optional identity column
	 *
	 * Only $allowedColumns are ever written on sync, and only $offlineKey is used
	 * to match records, so enabling offline mode never widens the write surface.
	 */
	public function isOfflineSyncable(): bool
	{
		return property_exists($this, 'offlineEnabled') && !empty($this->offlineEnabled);
	}

	/**
	 * Column used as the record identity when syncing this table.
	 */
	public function getOfflineKey(): string
	{
		return (property_exists($this, 'offlineKey') && !empty($this->offlineKey))
			? (string)$this->offlineKey
			: 'id';
	}

	/**
	 * Columns exposed for offline sync. Defaults to $allowedColumns so the
	 * mass-assignment gatekeeper also guards offline writes.
	 */
	public function offlineColumns(): array
	{
		return $this->allowedColumns;
	}

	/**
	 * Table this model reads from / writes to.
	 */
	public function getTable(): string
	{
		return $this->table ?? '';
	}


	public function findAll(): array|false
	{

		$query = "select * from $this->table";

		return $this->query($query);
	}

	public function where(array $data, array $data_not = []): array|false
	{
		$keys = array_keys($data);
		$keys_not = array_keys($data_not);
		$query = "select * from $this->table where ";

		foreach ($keys as $key) {
			$query .= "`$key` = :$key && ";
		}

		foreach ($keys_not as $key) {
			$query .= "`$key` != :$key && ";
		}

		$query = trim($query, " && ");

		$query .= " order by `$this->order_column` $this->order_type limit $this->limit offset $this->offset";
		$data = array_merge($data, $data_not);

		return $this->query($query, $data);
	}

	public function first(array|string $data, array $data_not = []): object|false
	{
		// If $data is a string, treat it as raw SQL
		if (is_string($data)) {
			$result = $this->query($data, $data_not);
			return $result ? $result[0] : false;
		}

		$keys = array_keys($data);
		$keys_not = array_keys($data_not);
		$query = "select * from $this->table where ";

		foreach ($keys as $key) {
			$query .= "`$key` = :$key && ";
		}

		foreach ($keys_not as $key) {
			$query .= "`$key` != :$key && ";
		}

		$query = trim($query, " && ");

		$query .= " limit $this->limit offset $this->offset";
		$data = array_merge($data, $data_not);

		$result = $this->query($query, $data);
		return $result ? $result[0] : false;
	}

	public function insert(array $data): false
	{

		/** remove unwanted data **/
		if (!empty($this->allowedColumns)) {
			foreach ($data as $key => $value) {

				if (!in_array($key, $this->allowedColumns)) {
					unset($data[$key]);
				}
			}
		}

		$keys = array_keys($data);
		$cols = implode(",", array_map(fn($k) => "`$k`", $keys));

		$query = "insert into $this->table ($cols) values (:" . implode(",:", $keys) . ")";
		$this->query($query, $data);

		return false;
	}

	public function update(int|string $id, array $data, string $id_column = 'id'): bool
	{

		/** remove unwanted data **/
		if (!empty($this->allowedColumns)) {
			foreach ($data as $key => $value) {

				if (!in_array($key, $this->allowedColumns)) {
					unset($data[$key]);
				}
			}
		}

		$keys = array_keys($data);
		$query = "update $this->table set ";

		foreach ($keys as $key) {
			$query .= "`$key` = :$key, ";
		}

		$query = trim($query, ", ");

		$query .= " where `$id_column` = :$id_column ";

		$data[$id_column] = $id;

		try {
			$con = $this->connect();
			$stm = $con->prepare($query);
			$result = $stm->execute($data);
			return $result;
		} catch (PDOException $e) {
			$this->handleDatabaseError($e, 'Update failed', $query, $data);
			return false;
		}
	}

	public function delete(int|string $id, string $id_column = 'id'): bool
	{

		$data[$id_column] = $id;
		$query = "delete from $this->table where `$id_column` = :$id_column ";

		try {
			$con = $this->connect();
			$stm = $con->prepare($query);
			$result = $stm->execute($data);
			return $result;
		} catch (PDOException $e) {
			$this->handleDatabaseError($e, 'Delete failed', $query, $data);
			return false;
		}
	}

	/**
	 * Helper method to get count with conditions
	 * @param array $where Conditions
	 * @return int
	 */
	public function getCount(array $where = []): int
	{
		$query = "SELECT COUNT(*) FROM {$this->table}";

		if (!empty($where)) {
			$conditions = array_map(fn($key) => "`$key` = :$key", array_keys($where));
			$query .= " WHERE " . implode(' AND ', $conditions);
		}

		$stmt = $this->connect()->prepare($query);
		$stmt->execute($where);

		return (int) $stmt->fetchColumn();
	}

	// Usage:
	// $pendingCount = $application->getCount(['status' => 'pending']);
	// $totalCount = $application->getCount();
}
