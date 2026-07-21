<?php

/**
 * DeletedItem Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class DeletedItem
{

	use Model;

	protected $table = 'deleteditems';
	protected $primaryKey = 'id'; 

	protected $allowedColumns = [
		'record_deleted',
		'from_table',
		'deleted_by',
		'date_deleted',
	];

}
