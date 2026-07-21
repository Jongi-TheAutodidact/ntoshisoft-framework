<?php

/**
 * Expenditures Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Expenditures extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(10) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('expenditure_date date NULL');
		$this->addColumn('description varchar(255) NULL');
		$this->addColumn('amount decimal(10,2) NULL');
		$this->addColumn('expense_type enum(\'Office Supplies\',\'Salaries\',\'Utilities\',\'Maintenance\',\'Marketing\',\'Other\') DEFAULT \'Other\'');
		$this->addColumn('paid_via enum(\'Cash\',\'EFT\',\'Credit/Debit Card\',\'Other\') DEFAULT \'EFT\'');
		$this->addColumn('notes text NULL');
		
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		
		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL ON UPDATE current_timestamp()');
		
		// Primary Key 
		$this->addPrimaryKey('id');
		
		# Indexing
		$this->addKey('expenditure_date');
		$this->addKey('expense_type');
		$this->addKey('paid_via');

		// Create Table
		$this->createTable('expenditures');
    }

    public function omega()
    {
        $this->dropTable('expenditures');
    }
}