<?php

/**
 * MeetingsController class 
 */

defined('ROOTPATH') or exit('Access Denied!');

class MeetingsController
{
	use Controller;

	public function __construct()
	{
		/*** INSTANTIATE RELEVANT INSTANCES (OBJECTS) ***/
		$user = new User();
		/*** CHECK IF USER IS LOGGED IN ***/
		if (!$user->logged_in()) {
			redirect('auth/login');
		}
	}

	public function boardroom(): void
	{
		$meeting = new Meeting();
		$user = new User();

		// Get latest scheduled meeting
		$latestMeeting = $meeting->getLatestMeeting();

		// Prepare data for view
		$data['page_title'] = 'Board Room';
		$data['meeting_id'] = $latestMeeting ? $latestMeeting->meeting_id : 'default_room';
		$data['meeting_title'] = $latestMeeting ? $latestMeeting->meeting_title : 'Board Meeting';

		// Meetings Status Indicator
		$data['meeting_status'] = $latestMeeting && strtotime($latestMeeting->scheduled_for) <= time() ? 'live' : 'upcoming';

		$this->view('admin/executive/boardroom', $data);
	}
	public function meetings(): void
	{
		$meeting = new Meeting();

		$data['meetings'] = $meeting->getAllMeetings();

		/*** EXPORT THE (OBJECTS) VARIABLES ***/
		$data['page_title'] = 'Board Room';


		/*** DISPLAY THE VIEW PAGE ***/
		$this->view('admin/executive/meetings', $data);
	}
	public function create_meeting(): void
	{
		$meeting = new Meeting();


		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($meeting->validate($_POST)) {
				if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
					die('Invalid CSRF Token!');
				} else {
					// Insert New Meeting details into DB
					$meeting->insert($_POST);
					Util::setFlash('meeting_register_success', 'Meeting Created Successfully!!');
					redirect('admin/meetings');
				}
			}
		}
		/*** EXPORT THE (OBJECTS) VARIABLES ***/
		$data['page_title'] = 'Create Meeting';
		$data['errors'] = $meeting->errors;


		/*** DISPLAY THE VIEW PAGE ***/
		$this->view('admin/executive/meeting-create', $data);
	}
	public function edit_meeting(?string $id = null): void
	{
		$meeting = new Meeting();

		$data['row'] = $meeting->first(['id' => $id]);


		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($meeting->validate($_POST)) {
				if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
					die('Invalid CSRF Token!');
				} else {
					// Insert Data into DB
					$meeting->update($id,$_POST);
					Util::setFlash('meeting_update_success', 'Meeting Updated Successfully!!');
					redirect('admin/meetings');
				}
			}
		}
		/*** EXPORT THE (OBJECTS) VARIABLES ***/
		$data['page_title'] = 'Edit Meeting';
		$data['errors'] = $meeting->errors;


		/*** DISPLAY THE VIEW PAGE ***/
		$this->view('admin/executive/meeting-edit', $data);
	}
}
