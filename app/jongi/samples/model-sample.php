<?php

/**
 * CLASSNAME Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class CLASSNAME
{

	use Model;

	protected $table = '{table}';
	protected $primaryKey = 'id'; // make sure it matches the one in your DB table


	protected $allowedColumns = [
		'tbl_column_1',
		'tbl_column_2',
		'tbl_column_3',
		'tbl_column_4',
		'tbl_column_5',
		'tbl_column_6',
		# etc....
	];

	public function validate($files_data, $post_data, $id = null)
	{
		$this->errors = [];

		// Allowed File types
		$allowed_types = [
			'image/jpeg',
			'image/jpg',
			'image/png',
			'image/webp'
		];

		// Image validation - Check inside the file 
		if (empty($files_data['image']['name'])) {
			$this->errors['image'] = 'An image is required!';
		} else 
        if (!isset($files_data['image']['type']) || !in_array($files_data['image']['type'], $allowed_types)) {
			$this->errors['image'] = 'Invalid Image File Type. Only types: ' . implode(', ', $allowed_types) . ' allowed!';
		} else 
		if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
			$this->errors['image'] = 'File upload error: ' . $_FILES['image']['error'];
		}

		// Other inputs validation
		if (empty($post_data['tbl_column_1'])) {
			$this->errors['tbl_column_1'] = "tbl_column_1 is required";
		}

		if (empty($post_data['email'])) {
			$this->errors['email'] = "Email is required";
		} else
		if (!filter_var($post_data['email'], FILTER_VALIDATE_EMAIL)) {
			$this->errors['email'] = "Email is not valid";
		} else
		if ($this->first(['email' => $post_data['email']], ['id' => $id])) {
			$this->errors['email'] = "Email is already in use!";
		}

		// Remove if not applicable (for this module)
		if (!preg_match("/^[a-zA-Z]+$/", $post_data['tbl_column_2'])) {
			$this->errors['tbl_column_2'] = "tbl_column_2 can only have letters";
		}

		if (empty($this->errors)) {
			return true;
		}

		return false;
	}
}
