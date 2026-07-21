<?php 
defined('ROOTPATH') OR exit('Access Denied!');

/**
 * The SocialLink Model Class
 */

class SocialLink
{
	
	use Model;

	protected $table = 'social_links';
	

	protected $allowedColumns = [
		'twitter_link',
		'youtube_link',
		'facebook_link',
		'tiktok_link',
		'instagram_link',
		'linkedin',
	];

	public function validate(array $data, int|string|null $id = null): bool
	{
		$this->errors = [];
		
		if(empty($this->errors))
		{
			return true;
		}

		return false;
	}

	public function create_table(): void
	{
        $sql = "CREATE TABLE IF NOT EXISTS social_links (
            id int(11) NOT NULL AUTO_INCREMENT,
            twitter_link varchar(1024) NULL,
            facebook_link varchar(1024) NULL,
            tiktok_link varchar(1024) NULL,
            instagram_link varchar(1024) NULL,
            linkedin varchar(1024) NULL,
            
            PRIMARY KEY (`id`)
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

		$this->query($sql);
		
        // Insert at least one row (even if its empty)
        if(empty($this->table))
		{
			$sql = "INSERT INTO $this->table (twitter_link) VALUES ('')";
		}

		$this->query($sql);
	}
}
