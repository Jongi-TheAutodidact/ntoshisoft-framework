<?php

/**
 * DeletedItems Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class DeletedItems extends Migration
{

    public function alpha()
    {
		$this->addColumn('id int(11) NOT NULL AUTO_INCREMENT');
		$this->addColumn('record_deleted varchar(1024) DEFAULT NULL');
		$this->addColumn('from_table varchar(50) DEFAULT NULL');		
		$this->addColumn('deleted_by varchar(30) NULL');
		$this->addColumn('date_deleted datetime default current_timestamp()');
		
		// Primary Key
		$this->addPrimaryKey('id');
		
		# You may add/list your table's keys and unique keys below
		// Indexing
		// $this->addKey('key/column');
		// Unique Keys
		// $this->addUniqueKey('key/column');
		// Create Table
		$this->createTable('deleteditems');
    }

    public function omega()
    {
        $this->dropTable('deleteditems');
    }
}
