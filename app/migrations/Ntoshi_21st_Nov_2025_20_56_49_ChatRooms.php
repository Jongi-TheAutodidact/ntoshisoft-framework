<?php

/**
 * ChatRooms Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class ChatRooms extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(11) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('room_name varchar(100) NOT NULL');
		$this->addColumn('created_by int(11) UNSIGNED NOT NULL');
		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('is_active tinyint(1) DEFAULT 1');
		
		// Primary Key 
		$this->addPrimaryKey('id');
		
		# Indexing
		$this->addKey('room_name');
		$this->addKey('created_by');
		$this->addKey('is_active');

		// Create Table
		$this->createTable('chat_rooms');
    }

    public function omega()
    {
        $this->dropTable('chat_rooms');
    }
}