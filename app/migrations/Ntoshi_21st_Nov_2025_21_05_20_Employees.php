<?php

/**
 * Employees Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Employees extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(11) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('user_id int(11) UNSIGNED NOT NULL');
		$this->addColumn('employee_number varchar(20) NOT NULL');
		$this->addColumn('position varchar(100) NOT NULL');
		$this->addColumn('department varchar(50) NULL');
		$this->addColumn('hire_date date NOT NULL');
		$this->addColumn('termination_date date NULL');
		$this->addColumn('emergency_contact varchar(100) NULL');
		$this->addColumn('emergency_phone varchar(15) NULL');
		$this->addColumn('qualifications text NULL');
		$this->addColumn('notes text NULL');
		$this->addColumn('schedule longtext NULL');
		$this->addColumn('document_folder varchar(255) NULL');
		$this->addColumn('performance_score decimal(3,1) NULL');
		$this->addColumn('last_evaluation_date date NULL');
		$this->addColumn('skills longtext NULL');
		
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		
		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL ON UPDATE current_timestamp()');
		
		// Primary Key 
		$this->addPrimaryKey('id');
		
		# Indexing
		$this->addKey('user_id');
		$this->addKey('employee_number');
		$this->addKey('position');
		$this->addKey('department');
		$this->addKey('hire_date');
		$this->addUniqueKey('employee_number');

		// Create Table
		$this->createTable('employees');
    }

    public function omega()
    {
        $this->dropTable('employees');
    }
}