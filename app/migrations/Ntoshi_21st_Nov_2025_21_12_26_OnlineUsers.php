<?php

/**
 * OnlineUsers Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class OnlineUsers extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(11) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('session_id varchar(255) NOT NULL');
		$this->addColumn('user_id int(11) UNSIGNED NULL');
		$this->addColumn('ip_address varchar(45) NOT NULL');
		$this->addColumn('last_active datetime default current_timestamp() ON UPDATE current_timestamp()');
		
		// Primary Key 
		$this->addPrimaryKey('id');
		
		# Indexing
		$this->addKey('session_id');
		$this->addKey('user_id');
		$this->addKey('ip_address');
		$this->addKey('last_active');
		$this->addUniqueKey('session_id');

		// Create Table
		$this->createTable('online_users');
    }

    public function omega()
    {
        $this->dropTable('online_users');
    }
}