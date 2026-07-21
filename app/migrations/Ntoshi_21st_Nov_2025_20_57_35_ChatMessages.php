<?php

/**
 * ChatMessages Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class ChatMessages extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(11) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('room_id int(11) UNSIGNED NOT NULL');
		$this->addColumn('user_id int(11) UNSIGNED NOT NULL');
		$this->addColumn('message text NOT NULL');
		$this->addColumn('date_sent datetime default current_timestamp()');
		$this->addColumn('is_read tinyint(1) DEFAULT 0');
		$this->addColumn('is_delivered tinyint(1) DEFAULT 0');
		$this->addColumn('deleted_at datetime NULL');
		
		// Primary Key 
		$this->addPrimaryKey('id');
		
		# Indexing
		$this->addKey('room_id');
		$this->addKey('user_id');
		$this->addKey('date_sent');
		$this->addKey('is_read');
		$this->addKey('is_delivered');

		// Create Table
		$this->createTable('chat_messages');
    }

    public function omega()
    {
        $this->dropTable('chat_messages');
    }
}