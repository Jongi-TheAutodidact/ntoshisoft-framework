<?php

/**
 * MediaFiles Migration class
 */

defined('ROOTPATH') or exit('Access Denied!');

class MediaFiles extends Migration
{

    public function alpha()
    {
        /** Add table columns **/

		$this->addColumn('id int(10) UNSIGNED NOT NULL AUTO_INCREMENT');
		$this->addColumn('incident_id int(11) UNSIGNED NULL');
		$this->addColumn('observation_id int(11) UNSIGNED NULL');
		$this->addColumn('evidence_id int(11) UNSIGNED NULL');
		$this->addColumn('file_name varchar(255) NOT NULL');
		$this->addColumn('file_path varchar(255) NOT NULL');
		$this->addColumn('file_type varchar(100) NULL');
		$this->addColumn('file_size int(11) NULL');
		$this->addColumn('mime_type varchar(100) NULL');
		$this->addColumn('uploaded_by varchar(255) NULL');
		$this->addColumn('description text NULL');
		$this->addColumn('is_public tinyint(1) DEFAULT 0');
		$this->addColumn('media_metadata text NULL');

		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		$this->addColumn('deleted_by varchar(30) NULL');

		$this->addPrimaryKey('id');

		$this->addKey('incident_id');
		$this->addKey('observation_id');
		$this->addKey('evidence_id');
		$this->addKey('file_type');
		$this->addKey('date_created');

		$this->createTable('media_files');

		$this->addForeignKey('incident_id', 'incidents', 'id');
		$this->addForeignKey('observation_id', 'observations', 'id');
		$this->addForeignKey('evidence_id', 'evidence', 'id');
    }

    public function omega()
    {
        $this->dropTable('media_files');
    }
}
