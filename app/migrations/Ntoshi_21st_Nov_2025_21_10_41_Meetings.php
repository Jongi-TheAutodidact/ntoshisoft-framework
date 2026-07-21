<?php

/**
 * Meetings Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Meetings extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(11) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('meeting_title varchar(30) NOT NULL');
		$this->addColumn('meeting_id varchar(64) NOT NULL');
		$this->addColumn('user_id int(11) UNSIGNED NOT NULL');
		$this->addColumn('scheduled_for datetime NULL');
		$this->addColumn('notes text NULL');
		
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		
		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		
		// Primary Key 
		$this->addPrimaryKey('id');
		
		# Indexing
		$this->addKey('meeting_title');
		$this->addKey('meeting_id');
		$this->addKey('user_id');
		$this->addKey('scheduled_for');
		$this->addUniqueKey('meeting_id');

		// Create Table
		$this->createTable('meetings');
    }

    public function omega()
    {
        $this->dropTable('meetings');
    }
}