<?php

/**
 * Category Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Category
{

	use Model;

	protected $table = 'categories';
	protected $primaryKey = 'id'; 

	protected $allowedColumns = [
		'sn',
		'cat_name',
		'created_by',
		'updated_by',
		'date_created',
		'date_updated'
	];

	public function validate(array $post_data, int|string|null $id = null): bool
	{
		$this->errors = [];

		// Inputs validation
		if (empty($post_data['cat_name'])) {
			$this->errors['cat_name'] = "Fill in the Category Name";
		}


		if (empty($this->errors)) {
			return true;
		}

		return false;
	}
}
