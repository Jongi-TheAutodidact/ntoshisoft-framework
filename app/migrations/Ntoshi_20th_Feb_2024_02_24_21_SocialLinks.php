<?php

/**
 * SocialLinks Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class SocialLinks extends Migration
{

    public function alpha()
    {
        /** Add table columns (default columns hereunder) **/

		$this->addColumn('id int(11) NOT NULL AUTO_INCREMENT');
		$this->addColumn('youtube_link varchar(1024) DEFAULT NULL');
		$this->addColumn('twitter_link varchar(1024) DEFAULT NULL');
		$this->addColumn('facebook_link varchar(1024) DEFAULT NULL');
		$this->addColumn('tiktok_link varchar(1024) DEFAULT NULL');
		$this->addColumn('instagram_link varchar(1024) DEFAULT NULL');
		$this->addColumn('linkedin varchar(1024) DEFAULT NULL');
		
		$this->addColumn('created_by varchar(30) NULL');
		$this->addColumn('updated_by varchar(30) NULL');
		

		$this->addColumn('date_created datetime default current_timestamp()');
		$this->addColumn('date_updated datetime NULL');
		$this->addPrimaryKey('id');
		
		 # You may add/list your table's keys and unique keys below

		$this->addKey('id');
		// $this->addUniqueKey('key/column');
		
		$this->createTable('social_links');

		/** insert data **/
		# if you wish to immediately add data to your table from here, you can do that by listing repeating the addData function, passing the relevant key and value.
		$this->addData('youtube_link','https://twitter.com/#');
		$this->addData('twitter_link','https://twitter.com/#');
		$this->addData('facebook_link','https://facebook.com/#');
		$this->addData('tiktok_link','https://tiktok.com/#');
		$this->addData('instagram_link','https://instagram.com/#');
		$this->addData('linkedin','https://linkedin.com/#');

		$this->insertData('social_links');
    }

    public function omega()
    {
        $this->dropTable('social_links');
    }
}
