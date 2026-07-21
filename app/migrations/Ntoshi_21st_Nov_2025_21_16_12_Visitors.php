<?php

/**
 * Visitors Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Visitors extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(11) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('ip_address varchar(45) NOT NULL');
		$this->addColumn('user_agent text NOT NULL');
		$this->addColumn('referrer varchar(255) NULL');
		$this->addColumn('location varchar(255) NULL');
		$this->addColumn('device varchar(255) NULL');
		$this->addColumn('country varchar(100) NULL');
		$this->addColumn('city varchar(100) NULL');
		$this->addColumn('visited_from varchar(255) NULL');
		$this->addColumn('visited_to varchar(255) NOT NULL');
		$this->addColumn('visited_at datetime default current_timestamp()');
		
		// Primary Key 
		$this->addPrimaryKey('id');
		
		# Indexing
		$this->addKey('ip_address');
		$this->addKey('country');
		$this->addKey('city');
		$this->addKey('visited_at');

		// Create Table
		$this->createTable('visitors');
    }

    public function omega()
    {
        $this->dropTable('visitors');
    }
}