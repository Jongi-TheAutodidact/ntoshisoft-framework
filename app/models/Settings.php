<?php

/**
 * Settings Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Settings
{
	use Model;
	
	protected $table = 'settings';
	protected $primaryKey = 'key';
	protected $allowedColumns = ['key', 'value'];
	
	/**
	 * Load all settings as key-value pairs
	 */
	public function loadSettings(): array
	{
		$settings = [];
		$results = $this->findAll();
		
		if ($results) {
			foreach ($results as $row) {
				// Since findAll() returns objects, use object syntax
				$settings[$row->key] = $row->value;
			}
		}
		
		return $settings;
	}
	
	/**
	 * Get a specific setting
	 */
	public function get(string $key, mixed $default = null): mixed
	{
		$result = $this->first(['key' => $key]);
		return $result ? $result->value : $default;
	}
	
	/**
	 * Update or create a setting
	 */
	public function set(string $key, mixed $value): bool
	{
		$existing = $this->first(['key' => $key]);
		
		if ($existing) {
			return $this->update($key, ['value' => $value], 'key');
		} else {
			return $this->insert(['key' => $key, 'value' => $value]);
		}
	}
}