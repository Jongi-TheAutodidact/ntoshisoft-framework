<?php

/**
 * ActivityLogs Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class ActivityLogs extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(10) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('user_id int(11) UNSIGNED NOT NULL');
		$this->addColumn('user_name varchar(255) NULL');
		$this->addColumn('action varchar(100) NOT NULL');
		$this->addColumn('entity_type varchar(100) NULL');
		$this->addColumn('entity_id int(11) NULL');
		$this->addColumn('description text NULL');
		$this->addColumn('ip_address varchar(45) NULL');
		$this->addColumn('user_agent text NULL');
		$this->addColumn('metadata text NULL');

		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		$this->addColumn('deleted_by varchar(30) NULL');

		$this->addPrimaryKey('id');

		$this->addKey('user_id');
		$this->addKey('action');
		$this->addKey('entity_type');
		$this->addKey('date_created');

		$this->createTable('activity_logs');

		$this->addForeignKey('user_id', 'users', 'id');
    }

    public function omega()
    {
        $this->dropTable('activity_logs');
    }
}
