<?php

/**
 * Payments Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Payments extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(10) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('payment_date date NULL');
		$this->addColumn('client int(10) UNSIGNED NULL');
		$this->addColumn('pay_type varchar(30) NOT NULL');
		$this->addColumn('amount decimal(10,2) NULL');
		$this->addColumn('paid_via enum(\'Cash\',\'EFT\',\'Send Cash\',\'Bitcoin\',\'Credit/Debit Card\',\'Other\') DEFAULT \'EFT\'');
		$this->addColumn('notes text NULL');
		
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		
		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL ON UPDATE current_timestamp()');
		
		// Primary Key 
		$this->addPrimaryKey('id');
		
		# Indexing
		$this->addKey('payment_date');
		$this->addKey('client');
		$this->addKey('pay_type');
		$this->addKey('paid_via');

		// Create Table
		$this->createTable('payments');
    }

    public function omega()
    {
        $this->dropTable('payments');
    }
}