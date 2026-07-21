<?php

/**
 * CompanyDetails Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class CompanyDetails extends Migration
{

	public function alpha()
	{
		$this->addColumn('id int(11) NOT NULL AUTO_INCREMENT');
		$this->addColumn('image varchar(1024)');
		$this->addColumn('name varchar(30)');
		$this->addColumn('about text');
		$this->addColumn('email varchar(50)');
		$this->addColumn('phone varchar(15)');
		$this->addColumn('address varchar(1024)');

		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');


		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		$this->addPrimaryKey('id');

		# You may add/list your table's keys and unique keys below

		$this->addKey('name');
		$this->addKey('phone');
		$this->addUniqueKey('email');

		$this->createTable('company_details');

		$this->addData('name', 'Ntoshi MVC Framework');
		$this->addData('about', 'With a user-friendly interface and intuitive design patterns, Ntoshi MVC Framework takes you from the absolute beginning of your project to about 70% completion in no time. Our framework is not only easy to use but also highly customizable to fit the needs of your unique project.');
		$this->addData('email', 'jongim@jongibrandz.co.za');
		$this->addData('phone', '27 65 858 5833');
		$this->addData('address', 'Gqeberha (Port Elizabeth), South Africa');

		/** insert data **/
		$this->insertData('company_details');
	}

	public function omega()
	{
		$this->dropTable('company_details');
	}
}
