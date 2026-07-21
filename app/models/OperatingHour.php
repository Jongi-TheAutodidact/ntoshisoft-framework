<?php 
defined('ROOTPATH') OR exit('Access Denied!');

/**
 * The OperatingHour Model Class
 */

class OperatingHour
{
	
	use Model;

	protected $table = 'op_hours';
	

	protected $allowedColumns = [
		'sun',
		'mon',
		'tue',
		'wed',
        'thu',
        'fri',
        'sat',
	];

	public function validate(array $data, int|string|null $id = null): bool
	{
		$this->errors = [];
		
		if(empty($this->errors))
		{
			return true;
		}

		return false;
	}
}
