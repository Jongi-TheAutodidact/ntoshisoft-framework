<?php

/**
 * OfflineSync Migration class
 *
 * Creates the sync_log table used by the NtoshiSoft offline-first sync engine.
 * Each pushed offline mutation is recorded once against a unique client UUID so
 * replays from the client (service worker + IndexedDB queue) are idempotent.
 */

defined('ROOTPATH') or exit('Access Denied!');

class OfflineSync extends Migration
{

	public function alpha()
	{
		$this->addColumn('id int(11) NOT NULL AUTO_INCREMENT');
		$this->addColumn('client_uuid varchar(40) NOT NULL');
		$this->addColumn('user_id varchar(30) DEFAULT NULL');
		$this->addColumn('table_name varchar(64) DEFAULT NULL');
		$this->addColumn('action varchar(10) DEFAULT NULL');
		$this->addColumn('record_id varchar(64) DEFAULT NULL');
		$this->addColumn('payload text');
		$this->addColumn('status varchar(20) DEFAULT \'applied\'');
		$this->addColumn('error_message text');
		$this->addColumn('date_created datetime DEFAULT NULL');

		$this->addPrimaryKey('id');
		$this->addUniqueKey('client_uuid');
		$this->addKey('user_id');
		$this->addKey('table_name');
		$this->addKey('date_created');

		$this->createTable('sync_log');
	}

	public function omega()
	{
		$this->dropTable('sync_log');
	}
}
