<?php

/**
 * Clients Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Clients extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(11) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('user_id int(11) UNSIGNED NOT NULL');
		$this->addColumn('identity_number varchar(13) NULL');
		$this->addColumn('marital_status enum(\'Single\',\'Married\',\'Divorced\',\'Other\') DEFAULT \'Single\'');
		$this->addColumn('address varchar(50) NOT NULL');
		$this->addColumn('city varchar(20) NOT NULL');
		$this->addColumn('province varchar(20) NOT NULL');
		$this->addColumn('postal_code int(11) NULL');
		$this->addColumn('country varchar(20) DEFAULT \'RSA\'');
		$this->addColumn('nationality varchar(20) DEFAULT \'South African\'');
		$this->addColumn('status enum(\'Active\',\'Inactive\',\'Terminated\',\'Other\') DEFAULT \'Active\'');
		$this->addColumn('source_of_funds enum(\'Salary/Wages\',\'Pension\',\'Support Grant\',\'Self Employed\',\'Other\') DEFAULT \'Salary/Wages\'');
		$this->addColumn('prem_col_date varchar(5) NULL');
		$this->addColumn('notes text NULL');
		
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		
		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		
		// Primary Key 
		$this->addPrimaryKey('id');
		
		# Indexing
		$this->addKey('user_id');
		$this->addKey('identity_number');
		$this->addKey('city');
		$this->addKey('province');
		$this->addKey('status');

		// Create Table
		$this->createTable('clients');
    }

    public function omega()
    {
        $this->dropTable('clients');
    }
}