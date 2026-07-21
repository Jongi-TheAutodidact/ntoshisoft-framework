<?php

defined('ROOTPATH') or exit('Access Denied!');

class SettingsController
{
	use Controller;

	public function index(): void
	{
		$settingsModel = new Settings();
		$appSettings = $settingsModel->loadSettings();
		$data = [
			'page_title' => 'System Settings',
			'settings' => $appSettings,
			'logo_url' => $this->getLogoUrl(),
		];

		$this->view('admin/settings', $data);
	}

	public function update(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			redirect('admin/settings');
			return;
		}

		$settingsModel = new Settings();

		$fields = [
			'site_name', 'admin_email',
			'email_notifications', 'primary_color', 'meta_description'
		];

		foreach ($fields as $field) {
			if (isset($_POST[$field])) {
				$value = $_POST[$field];
				if (in_array($field, ['email_notifications'])) {
					$value = $value ? '1' : '0';
				}
				$settingsModel->set($field, $value);
			}
		}

		if (!empty($_FILES['logo']['tmp_name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
			$this->handleLogoUpload();
		}

		$_SESSION['flash'] = 'Settings saved successfully';
		redirect('admin/settings');
	}

	private function handleLogoUpload(): void
	{
		$file = $_FILES['logo'];
		$allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $file['tmp_name']);
		finfo_close($finfo);

		if (!in_array($mime, $allowedMimes)) {
			$_SESSION['flash_error'] = 'Invalid image format. Allowed: JPG, PNG, WebP, GIF, SVG';
			return;
		}

		$ext = match ($mime) {
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/webp' => 'webp',
			'image/gif' => 'gif',
			'image/svg+xml' => 'svg',
			default => 'png',
		};

		$logosDir = ROOTPATH . 'assets/img/logos/';

		if (!is_dir($logosDir)) {
			mkdir($logosDir, 0755, true);
		}

		$filename = 'logo.' . $ext;

		foreach (glob($logosDir . 'logo.*') as $oldFile) {
			unlink($oldFile);
		}

		move_uploaded_file($file['tmp_name'], $logosDir . $filename);
	}

	private function getLogoUrl(): string
	{
		$logosDir = ROOTPATH . 'assets/img/logos/';
		if (!is_dir($logosDir)) return ROOT . '/assets/img/logos/logo.svg';

		$files = glob($logosDir . 'logo.*');
		if (!empty($files)) {
			$name = basename($files[0]);
			$ts = filemtime($files[0]);
			return ROOT . '/assets/img/logos/' . $name . '?v=' . $ts;
		}

		return ROOT . '/assets/img/logos/logo.svg';
	}
}
