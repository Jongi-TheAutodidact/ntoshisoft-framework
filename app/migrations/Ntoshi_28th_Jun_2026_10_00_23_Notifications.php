<?php

/**
 * Notifications Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Notifications extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(10) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('user_id int(11) UNSIGNED NOT NULL');
		$this->addColumn('user_name varchar(255) NULL');
		$this->addColumn('title varchar(255) NOT NULL');
		$this->addColumn('message text NULL');
		$this->addColumn('notification_type varchar(100) NULL');
		$this->addColumn('icon varchar(255) NULL');
		$this->addColumn('link varchar(255) NULL');
		$this->addColumn('is_read tinyint(1) DEFAULT 0');
		$this->addColumn('sent_at datetime NULL');
		$this->addColumn('read_at datetime NULL');

		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		$this->addColumn('deleted_by varchar(30) NULL');

		$this->addPrimaryKey('id');

		$this->addKey('user_id');
		$this->addKey('notification_type');
		$this->addKey('is_read');
		$this->addKey('date_created');

		$this->createTable('notifications');

		$this->addForeignKey('user_id', 'users', 'id');
    }

    public function omega()
    {
        $this->dropTable('notifications');
    }
}
