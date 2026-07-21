<?php 
declare(strict_types=1);
defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Main Core Controller Class
 */

Trait Controller
{

	public function view(string $name, array $data = []): void
	{
		if(!empty($data))
			extract($data);
		
		$filename = "../app/views/".$name.".ntoshi.php";
		if(file_exists($filename))
		{
			require $filename;
		}else{

			$filename = "../app/views/404.ntoshi.php";
			require $filename;
		}
	}
}