<?php
defined('ROOTPATH') or exit('Access Denied!');

/**
 * The ContactFormModel Model Class
 */

class ContactFormModel
{

	use Model;

	protected $table = 'contact_form';

	protected $allowedColumns = [
		'contactName',
		'contactEmail',
		'contactSubject',
		'contactMessage',
	];

	public function validate(array $post_data, int|string|null $id = null): bool
	{
		$this->errors = [];

		// Inputs validation
		if (empty($post_data['contactName'])) {
			$this->errors['contactName'] = "** Full name is required **";
		}
		if (empty($post_data['contactEmail'])) {
			$this->errors['contactEmail'] = "** Email is required **";
		}else
		if (!filter_var($post_data['contactEmail'], FILTER_VALIDATE_EMAIL)) {
			$this->errors['contactEmail'] = "** Email is not valid **";
		}
		if (empty($post_data['contactSubject'])) {
			$this->errors['contactSubject'] = "** Please provide Subject Line **";
		}
		if (empty($post_data['contactMessage'])) {
			$this->errors['contactMessage'] = "** Message cannot be empty **";
		}


		if (empty($this->errors)) {
			return true;
		}

		return false;
	}

}
