<?php

/**
 * Alerts Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Alerts extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(10) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('alert_number varchar(255) NOT NULL');
		$this->addColumn('title varchar(255) NOT NULL');
		$this->addColumn('description text NULL');
		$this->addColumn('alert_type varchar(100) NULL');
		$this->addColumn('severity varchar(50) DEFAULT \'medium\'');
		$this->addColumn('source_entity varchar(100) NULL');
		$this->addColumn('source_entity_id int(11) NULL');
		$this->addColumn('icon varchar(255) NULL');
		$this->addColumn('color varchar(50) NULL');
		$this->addColumn('link varchar(255) NULL');
		$this->addColumn('is_read tinyint(1) DEFAULT 0');
		$this->addColumn('is_dismissed tinyint(1) DEFAULT 0');
		$this->addColumn('triggered_by varchar(255) NULL');
		$this->addColumn('triggered_at datetime NULL');
		$this->addColumn('expires_at datetime NULL');

		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		$this->addColumn('deleted_by varchar(30) NULL');

		$this->addPrimaryKey('id');

		$this->addUniqueKey('alert_number');

		$this->addKey('alert_type');
		$this->addKey('severity');
		$this->addKey('is_read');
		$this->addKey('is_dismissed');
		$this->addKey('date_created');

		$this->createTable('alerts');
    }

    public function omega()
    {
        $this->dropTable('alerts');
    }
}
