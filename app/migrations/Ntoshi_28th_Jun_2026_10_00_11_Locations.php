<?php

/**
 * Locations Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Locations extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(10) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('location_code varchar(255) NOT NULL');
		$this->addColumn('name varchar(255) NULL');
		$this->addColumn('description text NULL');
		$this->addColumn('address text NULL');
		$this->addColumn('city varchar(100) NULL');
		$this->addColumn('province varchar(100) NULL');
		$this->addColumn('country varchar(100) DEFAULT \'South Africa\'');
		$this->addColumn('latitude decimal(10,7) NULL');
		$this->addColumn('longitude decimal(10,7) NULL');
		$this->addColumn('location_type varchar(100) NULL');
		$this->addColumn('risk_level varchar(50) DEFAULT \'LOW\'');
		$this->addColumn('is_active tinyint(1) DEFAULT 1');

		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		$this->addColumn('deleted_by varchar(30) NULL');

		$this->addPrimaryKey('id');

		$this->addUniqueKey('location_code');

		$this->addKey('city');
		$this->addKey('province');
		$this->addKey('location_type');
		$this->addKey('risk_level');
		$this->addKey('date_created');

		$this->createTable('locations');
    }

    public function omega()
    {
        $this->dropTable('locations');
    }
}
