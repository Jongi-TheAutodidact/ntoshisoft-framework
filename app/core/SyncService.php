<?php

declare(strict_types=1);

defined('ROOTPATH') or exit('Access Denied!');

/**
 * SyncService Core Class — Offline-First / PWA sync engine.
 *
 * NtoshiSoft framework feature that lets an application keep working without an
 * internet connection. Models opt in by setting `public bool $offlineEnabled = true;`
 * inside the Model trait. The client (service worker + IndexedDB) caches those
 * tables, queues mutations while offline, and pushes them back here when online.
 *
 * Security model:
 *  - Only models with $offlineEnabled = true are exposed (discovered automatically).
 *  - Only $allowedColumns (offlineColumns) may be written; the mass-assignment
 *    gatekeeper also guards offline writes.
 *  - $offlineKey is the only identity column used for updates/deletes.
 *  - Each pushed item carries a client-generated UUID; sync_log (unique on
 *    client_uuid) makes replays idempotent.
 */
class SyncService
{
	use Database;

	/** Cached registry of offline-capable models, keyed by table name. */
	private static ?array $modelsCache = null;

	/**
	 * Discover every model that opted into offline sync.
	 *
	 * @return array<string, array{class:string, table:string, columns:array, key:string}>
	 */
	public function getSyncableModels(): array
	{
		if (self::$modelsCache !== null) {
			return self::$modelsCache;
		}

		$models = [];
		$dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'models';

		if (!is_dir($dir)) {
			return self::$modelsCache = $models;
		}

		foreach (glob($dir . DIRECTORY_SEPARATOR . '*.php') ?: [] as $file) {
			$class = basename($file, '.php');

			if (!class_exists($class)) {
				continue;
			}

			// Only Model-trait classes can be offline sync candidates.
			if (!class_uses($class, false) || !in_array(Model::class, class_uses($class), true)) {
				continue;
			}

			try {
				$model = new $class();
			} catch (Throwable $e) {
				continue; // Skip models that cannot be safely instantiated.
			}

			if (!$model->isOfflineSyncable()) {
				continue;
			}

			$table = $model->getTable();
			$columns = $model->offlineColumns();

			// Offline writes are gated by $allowedColumns; a syncable model without
			// any allowed columns is not safe to expose.
			if ($table === '' || empty($columns) || $columns === ['*']) {
				continue;
			}

			$models[$table] = [
				'class'   => $class,
				'table'   => $table,
				'columns' => array_values(array_unique(array_merge($columns, [$model->getOfflineKey()]))),
				'key'     => $model->getOfflineKey(),
			];
		}

		return self::$modelsCache = $models;
	}

	/**
	 * Public list of offline-capable tables for the client config endpoint.
	 *
	 * @return array<int, array{table:string, key:string, columns:array}>
	 */
	public function tables(): array
	{
		$out = [];
		foreach ($this->getSyncableModels() as $table => $meta) {
			$out[] = [
				'table'   => $table,
				'key'     => $meta['key'],
				'columns' => $meta['columns'],
			];
		}
		return $out;
	}

	/**
	 * Pull the current rows for an offline-capable table.
	 *
	 * @return array<int, object>
	 */
	public function pull(string $table): array
	{
		$meta = $this->getSyncableModels()[$table] ?? null;
		if (!$meta) {
			return [];
		}

		$cols = implode(',', array_map(fn($c) => "`" . str_replace('`', '', $c) . "`", $meta['columns']));
		$stm = $this->execute(
			"SELECT $cols FROM `" . str_replace('`', '', $table) . "` ORDER BY `" . $meta['key'] . "` ASC"
		);

		if (!$stm) {
			return [];
		}

		$rows = $stm->fetchAll(PDO::FETCH_OBJ);
		return is_array($rows) ? $rows : [];
	}

	/**
	 * Apply a batch of queued offline mutations in order.
	 *
	 * @param array<int, array{uuid:string, table:string, action:string, id:int|string|null, data:array}> $items
	 * @return array<int, array{uuid:string, success:bool, id:int|string|null, error:string}>
	 */
	public function push(array $items, string $userId): array
	{
		$models = $this->getSyncableModels();
		$results = [];

		foreach ($items as $item) {
			$uuid   = (string)($item['uuid'] ?? '');
			$table  = (string)($item['table'] ?? '');
			$action = (string)($item['action'] ?? '');
			$id     = $item['id'] ?? null;
			$data   = is_array($item['data'] ?? null) ? $item['data'] : [];

			if ($uuid === '' || $table === '' || !isset($models[$table])) {
				$results[] = $this->result($uuid, false, null, 'Unsupported table or missing item fields.');
				continue;
			}

			if (!in_array($action, ['insert', 'update', 'delete'], true)) {
				$results[] = $this->result($uuid, false, null, 'Invalid action "' . $action . '".');
				continue;
			}

			$meta = $models[$table];

			// Idempotency: if this UUID was already applied, report success so the
			// client can drop the queued item without double-writing.
			$appliedId = $this->alreadyApplied($uuid);
			if ($appliedId !== null) {
				$results[] = $this->result($uuid, true, $appliedId, 'Already applied.');
				continue;
			}

			$error = '';
			$appliedId = null;
			$ok = false;

			try {
				if ($action === 'insert') {
					$appliedId = $this->applyInsert($meta, $data, $userId);
					$ok = $appliedId !== null;
				} elseif ($action === 'update') {
					$ok = $this->applyUpdate($meta, $data, $id, $userId);
					$appliedId = $id;
				} else {
					$ok = $this->applyDelete($meta, $id);
					$appliedId = $id;
				}
			} catch (Throwable $e) {
				$error = $e->getMessage();
			}

			if ($ok) {
				$this->logApplied($uuid, $userId, $table, $action, $appliedId, $data, 'applied');
				$results[] = $this->result($uuid, true, $appliedId);
			} else {
				$this->logApplied($uuid, $userId, $table, $action, null, $data, 'failed', $error);
				$results[] = $this->result($uuid, false, null, $error !== '' ? $error : 'Operation could not be applied.');
			}
		}

		return $results;
	}

	/**
	 * Server-side sync statistics for the current user.
	 *
	 * @return array{applied:int, last_sync:string|null}
	 */
	public function status(string $userId): array
	{
		$applied = 0;
		$last = null;

		$stm = $this->execute(
			"SELECT COUNT(*) AS total FROM sync_log WHERE user_id = :u",
			['u' => $userId]
		);
		if ($stm) {
			$row = $stm->fetch(PDO::FETCH_OBJ);
			$applied = (int)($row->total ?? 0);
		}

		$stm = $this->execute(
			"SELECT MAX(date_created) AS last FROM sync_log WHERE user_id = :u",
			['u' => $userId]
		);
		if ($stm) {
			$row = $stm->fetch(PDO::FETCH_OBJ);
			$last = $row->last ?? null;
		}

		return ['applied' => $applied, 'last_sync' => $last];
	}

	/**
	 * Insert a queued record. Returns the new server-side record id, or null.
	 */
	private function applyInsert(array $meta, array $data, string $userId): ?string
	{
		$allowed = $meta['columns'];
		$clean = array_intersect_key($data, array_flip($allowed));
		unset($clean[$meta['key']]); // The server assigns the identity.

		if (in_array('created_by', $allowed, true) && empty($clean['created_by'])) {
			$clean['created_by'] = $userId;
		}
		if (in_array('date_created', $allowed, true) && empty($clean['date_created'])) {
			$clean['date_created'] = date('Y-m-d H:i:s');
		}

		if (empty($clean)) {
			return null;
		}

		$keys = array_keys($clean);
		$cols = implode(',', array_map(fn($k) => "`" . str_replace('`', '', (string)$k) . "`", $keys));
		$vals = implode(',', array_map(fn($k) => ":$k", $keys));

		$stm = $this->execute(
			"INSERT INTO `" . $meta['table'] . "` ($cols) VALUES ($vals)",
			$clean
		);

		if (!$stm) {
			return null;
		}

		$con = $this->connect();
		return $con ? (string)$con->lastInsertId() : null;
	}

	/**
	 * Update a queued record. Returns true on success.
	 */
	private function applyUpdate(array $meta, array $data, int|string|null $id, string $userId): bool
	{
		if ($id === null || $id === '') {
			return false;
		}

		$allowed = $meta['columns'];
		$clean = array_intersect_key($data, array_flip($allowed));
		unset($clean[$meta['key']]);

		if (in_array('updated_by', $allowed, true) && empty($clean['updated_by'])) {
			$clean['updated_by'] = $userId;
		}
		if (in_array('date_updated', $allowed, true) && empty($clean['date_updated'])) {
			$clean['date_updated'] = date('Y-m-d H:i:s');
		}

		if (empty($clean)) {
			return true; // Nothing to change is not a failure.
		}

		$set = [];
		$params = [];
		foreach ($clean as $k => $v) {
			$set[] = "`" . str_replace('`', '', (string)$k) . "` = :$k";
			$params[$k] = $v;
		}
		$params['__id'] = $id;

		$stm = $this->execute(
			"UPDATE `" . $meta['table'] . "` SET " . implode(', ', $set) .
				" WHERE `" . $meta['key'] . "` = :__id",
			$params
		);

		return $stm !== false;
	}

	/**
	 * Delete a queued record. Returns true on success.
	 */
	private function applyDelete(array $meta, int|string|null $id): bool
	{
		if ($id === null || $id === '') {
			return false;
		}

		$stm = $this->execute(
			"DELETE FROM `" . $meta['table'] . "` WHERE `" . $meta['key'] . "` = :__id",
			['__id' => $id]
		);

		return $stm !== false;
	}

	/**
	 * Look up a previously applied UUID. Returns the recorded record id, or null.
	 */
	private function alreadyApplied(string $uuid): ?string
	{
		$stm = $this->execute(
			"SELECT record_id FROM sync_log WHERE client_uuid = :u LIMIT 1",
			['u' => $uuid]
		);
		if (!$stm) {
			return null;
		}
		$row = $stm->fetch(PDO::FETCH_OBJ);
		return $row ? (string)($row->record_id ?? '') : null;
	}

	/**
	 * Write an audit/idempotency entry into sync_log. A duplicate client_uuid is
	 * swallowed so replays never double-log.
	 */
	private function logApplied(
		string $uuid,
		string $userId,
		string $table,
		string $action,
		int|string|null $recordId,
		array $data,
		string $status,
		string $error = ''
	): void {
		try {
			$con = $this->connect();
			if (!$con) {
				return;
			}
			$stm = $con->prepare(
				"INSERT INTO sync_log
					(client_uuid, user_id, table_name, action, record_id, payload, status, error_message, date_created)
				 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
			);
			$stm->execute([
				$uuid,
				$userId,
				$table,
				$action,
				$recordId,
				json_encode($data),
				$status,
				$error,
				date('Y-m-d H:i:s'),
			]);
		} catch (PDOException $e) {
			// Duplicate client_uuid (unique key) — already recorded elsewhere.
		}
	}

	/**
	 * Build a per-item push result.
	 */
	private function result(string $uuid, bool $success, int|string|null $id, string $error = ''): array
	{
		return [
			'uuid'    => $uuid,
			'success' => $success,
			'id'      => $id !== null ? $id : null,
			'error'   => $error,
		];
	}

	/**
	 * Execute a prepared statement without emitting the framework's HTML error
	 * page (which would corrupt JSON API responses). Returns the statement, or
	 * false on failure.
	 */
	private function execute(string $sql, array $params = []): PDOStatement|false
	{
		try {
			$con = $this->connect();
			if (!$con) {
				return false;
			}
			$stm = $con->prepare($sql);
			$stm->execute($params);
			return $stm;
		} catch (PDOException $e) {
			return false;
		}
	}
}
