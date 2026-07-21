<?php

/**
 * Tasks Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Tasks extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(10) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('task_number varchar(255) NOT NULL');
		$this->addColumn('title varchar(255) NOT NULL');
		$this->addColumn('description text NULL');
		$this->addColumn('task_type varchar(100) NULL');
		$this->addColumn('priority varchar(50) DEFAULT \'medium\'');
		$this->addColumn('status varchar(50) DEFAULT \'pending\'');
		$this->addColumn('assigned_to varchar(255) NULL');
		$this->addColumn('assigned_by varchar(255) NULL');
		$this->addColumn('incident_id int(11) UNSIGNED NULL');
		$this->addColumn('case_id int(11) UNSIGNED NULL');
		$this->addColumn('due_date datetime NULL');
		$this->addColumn('completed_at datetime NULL');
		$this->addColumn('completion_notes text NULL');

		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		$this->addColumn('deleted_by varchar(30) NULL');

		$this->addPrimaryKey('id');

		$this->addUniqueKey('task_number');

		$this->addKey('incident_id');
		$this->addKey('case_id');
		$this->addKey('task_type');
		$this->addKey('priority');
		$this->addKey('status');
		$this->addKey('assigned_to');
		$this->addKey('date_created');

		$this->createTable('tasks');

		$this->addForeignKey('incident_id', 'incidents', 'id');
		$this->addForeignKey('case_id', 'cases', 'id');
    }

    public function omega()
    {
        $this->dropTable('tasks');
    }
}
