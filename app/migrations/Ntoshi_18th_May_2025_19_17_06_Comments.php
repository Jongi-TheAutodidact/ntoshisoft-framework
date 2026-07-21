<?php

/**
 * Comments Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Comments extends Migration
{

    public function alpha()
    {
        /** Add table columns (default columns hereunder) **/

		$this->addColumn('id int(11) NOT NULL AUTO_INCREMENT');
		$this->addColumn('post tinyint(11) NOT NULL');
		$this->addColumn('fullname varchar(50) NOT NULL');
		$this->addColumn('email varchar(128) NOT NULL');
		$this->addColumn('comment text NOT NULL');
		$this->addColumn('com_date datetime NOT NULL DEFAULT current_timestamp()');

		$this->addPrimaryKey('id');
		
		 # You may add/list your table's keys and unique keys below

		$this->addKey('fullname');
		$this->addKey('email');
		$this->addKey('com_date');
		// $this->addUniqueKey('key/column');
		
		$this->createTable('comments');
    }

    public function omega()
    {
        $this->dropTable('comments');
    }
}
