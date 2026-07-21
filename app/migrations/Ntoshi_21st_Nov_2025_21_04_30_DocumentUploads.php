<?php

/**
 * DocumentUploads Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class DocumentUploads extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(11) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('client_id int(11) UNSIGNED NOT NULL');
		$this->addColumn('title varchar(255) NOT NULL');
		$this->addColumn('description text NULL');
		$this->addColumn('file_path varchar(1024) NOT NULL');
		$this->addColumn('file_type varchar(50) NOT NULL');
		$this->addColumn('file_size int(11) NOT NULL');
		$this->addColumn('category varchar(50) DEFAULT \'General\'');
		$this->addColumn('status enum(\'Active\',\'Archived\') DEFAULT \'Active\'');
		
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		
		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL ON UPDATE current_timestamp()');
		
		// Primary Key 
		$this->addPrimaryKey('id');
		
		# Indexing
		$this->addKey('client_id');
		$this->addKey('title');
		$this->addKey('category');
		$this->addKey('status');

		// Create Table
		$this->createTable('document_uploads');
    }

    public function omega()
    {
        $this->dropTable('document_uploads');
    }
}