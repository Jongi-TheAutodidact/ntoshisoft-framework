<?php

/**
 * Meeting Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Meeting
{

	use Model;

	protected $table = 'meetings';
	protected $primaryKey = 'id';

	// Participates in the NtoshiSoft offline-first / PWA sync engine.
	public bool $offlineEnabled = true;


	protected $allowedColumns = [
		'meeting_title',
		'meeting_id',
		'user_id',
		'scheduled_for',
		'notes',
		'created_by',
		'updated_by',
		'date_updated',
	];

	public function validate(array $post_data, int|string|null $id = null): bool
	{
		$this->errors = [];


		if (empty($post_data['meeting_title'])) {
			$this->errors['meeting_title'] = "Meeting title is required";
		}
		if (empty($post_data['scheduled_for'])) {
			$this->errors['scheduled_for'] = "Meeting date and time is required";
		}


		if (empty($this->errors)) {
			return true;
		}

		return false;
	}

	public function getAllMeetings(): array|false
	{
		$sql = "SELECT m.*, u.firstname, u.surname
				FROM meetings m
				LEFT JOIN users u ON u.user_id = m.user_id
				ORDER BY date_created ASC";
		$result = $this->query($sql);
		if ($result) {
			return $result;
		}
		return false;
	}

	// Get the latest meeting
	public function getLatestMeeting(): object|null
	{
		$currentDateTime = date('Y-m-d H:i:s');
		$sql = "SELECT m.*, u.firstname, u.surname
				FROM meetings m
				LEFT JOIN users u ON u.user_id = m.user_id
				WHERE scheduled_for > '$currentDateTime'
				ORDER BY scheduled_for ASC 
				LIMIT 1";

		$result = $this->query($sql);
		return $result ? $result[0] : null;
	}
}
