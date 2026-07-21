<?php

/**
 * MediaController class 
 */

defined('ROOTPATH') or exit('Access Denied!');

class MediaController
{
	use Controller;

	public function index(): void
	{
		$data = [
			'page_title' => APP_NAME . ' Media Player',
		];

		$this->view('admin/media/index', $data);
	}

}