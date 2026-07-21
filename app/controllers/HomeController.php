<?php

defined('ROOTPATH') or exit('Access Denied!');

class HomeController
{
	use Controller;

	public function __construct()
	{
		// redirect('admin');
	}

	

	public function index(?string $id = null): void
	{
		$user = new User();
		$setting = new Settings();

		$settings = $setting->loadSettings();

		$data = [
			'page_title'		=> $settings['site_name'] ?? 'NtoshiSoft',
			'logged_in_user'	=> $user->logged_in(),
			'siteName'			=> $settings['site_name'] ?? 'NtoshiSoft',
			'primaryColor'		=> $settings['primary_color'] ?? '#b4a33f',
			'meta_description'	=> $settings['meta_description'] ?? 'Business management platform built with NtoshiSoft Framework.',
		];

		$this->view('front/home', $data);
	}

	public function popia(): void
	{
		$user = new User();
		$contact = new SocialLink();
		$detail = new CompanyDetail();
		$op_hour = new OperatingHour();

		$data['users'] = $user->findAll();
		$data['page_title'] = 'Home';
		$data['logged_in_user'] = $user->logged_in();
		$data['social_link'] = $contact->first(['id' => 1]);
		$data['comp_detail'] = $detail->first(['id' => 1]);
		$data['op_hours'] = $op_hour->first(['id' => 1]);

		$data['page_title'] = 'Popia Compliance';
		$this->view('front/pages/popia', $data);
	}

	public function privacy(): void
	{
		$user = new User();
		$contact = new SocialLink();
		$detail = new CompanyDetail();
		$op_hour = new OperatingHour();

		$data['users'] = $user->findAll();
		$data['page_title'] = 'Home';
		$data['logged_in_user'] = $user->logged_in();
		$data['social_link'] = $contact->first(['id' => 1]);
		$data['comp_detail'] = $detail->first(['id' => 1]);
		$data['op_hours'] = $op_hour->first(['id' => 1]);

		$data['page_title'] = 'Privacy Policy';
		$this->view('front/pages/privacy', $data);
	}

	public function about(): void
	{
		$user = new User();
		$contact = new SocialLink();
		$detail = new CompanyDetail();
		$op_hour = new OperatingHour();

		$data['executive'] = $user->findAll();
		$data['page_title'] = 'About us';
		$data['logged_in_user'] = $user->logged_in();
		$data['social_link'] = $contact->first(['id' => 1]);
		$data['comp_detail'] = $detail->first(['id' => 1]);
		$data['op_hours'] = $op_hour->first(['id' => 1]);

		$this->view('front/pages/about', $data);
	}

	public function services(): void
	{
		$user = new User();
		$contact = new SocialLink();
		$detail = new CompanyDetail();
		$op_hour = new OperatingHour();

		$data['users'] = $user->findAll();
		$data['page_title'] = 'Services';
		$data['logged_in_user'] = $user->logged_in();
		$data['social_link'] = $contact->first(['id' => 1]);
		$data['comp_detail'] = $detail->first(['id' => 1]);
		$data['op_hours'] = $op_hour->first(['id' => 1]);

		$this->view('front/pages/services', $data);
	}
}
