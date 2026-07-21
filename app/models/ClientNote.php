<?php

/**
 * ClientNote Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class ClientNote
{

	use Model;

	protected $table = 'client_notes';
	protected $primaryKey = 'id'; // make sure it matches the one in your DB table


	protected $allowedColumns = [
		'client_id',
		'user_id',
		'note_title',
		'client_notes',
		'created_by',
		'updated_by',
		'date_updated',
	];

	public function validate(array $post_data, int|string|null $id = null): bool
	{
		$this->errors = [];

		
		if (empty($post_data['note_title'])) {
			$this->errors['note_title'] = "Kindly input Note Title";
		}
		if (empty($post_data['note_title'])) {
			$this->errors['note_title'] = "Notes cannot be blank";
		}

		if (empty($this->errors)) {
			return true;
		}

		return false;
	}

	public function getNotesByClient(string $client_user_id): array
    {
        $sql = "SELECT * FROM client_notes WHERE user_id = ? ORDER BY date_created DESC";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$client_user_id]);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        if ($result) {
            return $result;
        }
        return [];
    }
}
