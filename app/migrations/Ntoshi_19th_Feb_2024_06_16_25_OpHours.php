<?php

/**
 * OpHours Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class OpHours extends Migration
{

    public function alpha()
    {
        /** Add table columns (default columns hereunder) **/

		$this->addColumn('id int(11) NOT NULL AUTO_INCREMENT');
		$this->addColumn('sun varchar(20) DEFAULT NULL');
		$this->addColumn('mon varchar(20) DEFAULT NULL');
		$this->addColumn('tue varchar(20) DEFAULT NULL');
		$this->addColumn('wed varchar(20) DEFAULT NULL');
		$this->addColumn('thu varchar(20) DEFAULT NULL');
		$this->addColumn('fri varchar(20) DEFAULT NULL');
		$this->addColumn('sat varchar(20) DEFAULT NULL');
		
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		

		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		$this->addPrimaryKey('id');
		
		 # You may add/list your table's keys and unique keys below

		// $this->addKey('key/column');
		// $this->addUniqueKey('key/column');
		
		$this->createTable('op_hours');

		/** insert data **/
		# if you wish tp immediately add data to your table from here, you can do that by listing repeating the addData function, passing the relevant key and value.
		$this->addData('sun','08:00 - 14:00');
		$this->addData('mon','08:00 - 18:00');
		$this->addData('tue','08:00 - 18:00');
		$this->addData('wed','08:00 - 18:00');
		$this->addData('thu','08:00 - 18:00');
		$this->addData('fri','08:00 - 18:00');
		$this->addData('sat','08:00 - 15:00');
		
		$this->insertData('op_hours');
    }

    public function omega()
    {
        $this->dropTable('op_hours');
    }
}
