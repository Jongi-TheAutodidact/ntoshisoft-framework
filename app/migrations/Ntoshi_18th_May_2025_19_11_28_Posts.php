<?php

/**
 * Posts Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Posts extends Migration
{

    public function alpha()
    {
         /** Add table columns (default columns hereunder) **/
		$this->addColumn('post_id int(11) NOT NULL AUTO_INCREMENT');
		$this->addColumn('image varchar(1024) DEFAULT NULL');
		$this->addColumn('title varchar(1024) NOT NULL');
		$this->addColumn('content text NOT NULL');
		$this->addColumn('author varchar(60) NOT NULL');
		$this->addColumn('category varchar(20) NOT NULL');
		$this->addColumn('date_posted datetime NOT NULL DEFAULT current_timestamp()');
		
		$this->addColumn('updated_by varchar(30) NULL');
		
		$this->addColumn('date_updated datetime NULL');
		$this->addPrimaryKey('post_id');
		
		# You may add/list your table's keys and unique keys below
		$this->addKey('image');
		$this->addKey('title');
		$this->addKey('author');
		$this->addKey('category');
		$this->addKey('date_posted');
		// $this->addUniqueKey('key/column');
		
		$this->createTable('posts');
    }

    public function omega()
    {
        $this->dropTable('posts');
    }
}
