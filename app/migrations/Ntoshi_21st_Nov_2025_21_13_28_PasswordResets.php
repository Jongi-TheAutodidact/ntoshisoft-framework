<?php

/**
 * PasswordResets Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class PasswordResets extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(11) NOT NULL AUTO_INCREMENT');
		$this->addColumn('email varchar(255) NOT NULL');
		$this->addColumn('token varchar(255) NOT NULL');
		
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		
		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		
		// Primary Key 
		$this->addPrimaryKey('id');
		
		# Indexing
		$this->addKey('email');
		$this->addKey('token');

		// Create Table
		$this->createTable('password_resets');
    }

    public function omega()
    {
        $this->dropTable('password_resets');
    }
}