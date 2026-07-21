<?php 
defined('ROOTPATH') OR exit('Access Denied!');
/**
 * 404 Controller Class
 */

class _404
{
	use Controller;
	
	public function index(): void
	{
		// User
		$user = new User();
		$data['users'] = $user->findAll();
		$data['page_title'] = 'Page Not Found';
		$data['logged_in_user'] = $user->logged_in();

		// Social Links
		$contact = new SocialLink();
		$data['social_link'] = $contact->first(['id' => 1]);

		// Company Details
		$detail = new CompanyDetail();
		$data['comp_detail'] = $detail->first(['id' => 1]);
		
	
		$this->view('front/pages/404', $data);
	}
}
