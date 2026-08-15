<?php

declare(strict_types=1);

defined('ROOTPATH') or exit('Access Denied!');

/**
 * SyncController — Offline-First / PWA sync API.
 *
 * Provides the JSON endpoints used by the offline engine (service worker +
 * IndexedDB):
 *
 *   GET  offline/config          public  — is sync enabled, sync interval, tables
 *   GET  offline/pull/{table}    auth    — current rows for one offline table
 *   POST offline/push            auth    — apply a batch of queued mutations
 *   GET  offline/status          auth    — server-side sync statistics
 *
 * push() is POST, so AuthMiddleware also enforces the CSRF token (the client
 * sends it via the X-CSRF-TOKEN header). Only models that opt in with
 * Model::$offlineEnabled = true are ever exposed.
 */
class SyncController
{
	use Controller;

	private SyncService $sync;

	public function __construct()
	{
		$this->sync = new SyncService();
	}

	/**
	 * GET offline/config — public capabilities advertisement.
	 */
	public function config(): void
	{
		if (!OFFLINE_MODE) {
			$this->respond(['success' => false, 'message' => 'Offline mode is disabled on this server.'], 400);
		}

		$this->respond([
			'success' => true,
			'offline' => [
				'enabled'  => true,
				'interval' => SYNC_INTERVAL,
			],
			'tables' => $this->sync->tables(),
		]);
	}

	/**
	 * GET offline/pull/{table} — fetch rows for one offline-capable table.
	 */
	public function pull(?string $table = null): void
	{
		$table = (string)$table;
		$rows = $this->sync->pull($table);

		$this->respond([
			'success' => true,
			'table'   => $table,
			'rows'    => $rows,
		]);
	}

	/**
	 * POST offline/push — apply queued offline mutations.
	 */
	public function push(): void
	{
		$input = json_decode(file_get_contents('php://input'), true);
		$items = is_array($input['items'] ?? null) ? $input['items'] : [];

		if (empty($items)) {
			$this->respond(['success' => false, 'message' => 'No queued items provided.'], 400);
		}

		$results = $this->sync->push($items, (string)user('user_id'));

		$this->respond(['success' => true, 'results' => $results]);
	}

	/**
	 * GET offline/status — server-side sync statistics for the current user.
	 */
	public function status(): void
	{
		$this->respond([
			'success' => true,
			'status'  => $this->sync->status((string)user('user_id')),
		]);
	}

	/**
	 * Send a JSON response.
	 */
	private function respond(array $data, int $code = 200): void
	{
		http_response_code($code);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data);
		exit;
	}
}
