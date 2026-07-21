<?php

/**
 * ContactForm Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class ContactForm extends Migration
{

    public function alpha()
    {
       
		$this->addColumn('id int(11) NOT NULL AUTO_INCREMENT');
		$this->addColumn('contactName varchar(20) NOT NULL');
		$this->addColumn('contactEmail varchar(50) NOT NULL');
		$this->addColumn('contactSubject varchar(50) NOT NULL');
		$this->addColumn('contactMessage text NOT NULL');		
		
		$this->addColumn('date_created datetime default current_timestamp()');
	
		// Primary Key
		$this->addPrimaryKey('id');

		// Create Table
		$this->createTable('contact_form');
    }

    public function omega()
    {
        $this->dropTable('contact_form');
    }
}
