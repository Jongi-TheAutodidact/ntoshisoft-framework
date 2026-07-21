<?php

/**
 * Geofences Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Geofences extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(10) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('name varchar(255) NOT NULL');
		$this->addColumn('description text NULL');
		$this->addColumn('geofence_type varchar(100) NULL');
		$this->addColumn('latitude decimal(10,7) NULL');
		$this->addColumn('longitude decimal(10,7) NULL');
		$this->addColumn('radius_meters int(11) DEFAULT 500');
		$this->addColumn('boundary_points text NULL');
		$this->addColumn('color varchar(50) NULL');
		$this->addColumn('is_active tinyint(1) DEFAULT 1');
		$this->addColumn('risk_level varchar(50) DEFAULT \'LOW\'');
		$this->addColumn('assigned_to varchar(255) NULL');

		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		$this->addColumn('deleted_by varchar(30) NULL');

		$this->addPrimaryKey('id');

		$this->addKey('geofence_type');
		$this->addKey('is_active');
		$this->addKey('risk_level');
		$this->addKey('date_created');

		$this->createTable('geofences');
    }

    public function omega()
    {
        $this->dropTable('geofences');
    }
}
