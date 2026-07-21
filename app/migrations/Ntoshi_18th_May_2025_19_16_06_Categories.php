<?php

/**
 * Categories Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Categories extends Migration
{

    public function alpha()
    {
        /** Add table columns (default columns hereunder) **/

		$this->addColumn('id int(11) NOT NULL AUTO_INCREMENT');
		$this->addColumn('sn int(11) NOT NULL');
		$this->addColumn('cat_name varchar(20) NOT NULL');
		
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		
		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		$this->addPrimaryKey('id');
		
		 # You may add/list your table's keys and unique keys below

		// $this->addKey('key/column');
		$this->addUniqueKey('cat_name');
		
		$this->createTable('categories');
    }

    public function omega()
    {
        $this->dropTable('categories');
    }
}
