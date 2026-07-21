<?php

/**
 * PDF class 
 */

defined('ROOTPATH') or exit('Access Denied!');

use Dompdf\Dompdf;

class PDF
{
	use Controller;

	public function index(?string $id = null): void {}

	public function userProfilePDF(?string $id = null): void
	{

		$user = new User();

		$data['row'] = $user->first(['id' => $id]);

		$url = ROOT . "/admin/users/pdf/$id";
		$id = extract_id_from_url($url);
		$data['id'] = $id;


		// /*** INSTANTIATE RELEVANT INSTANCES (OBJECTS) ***/
		$dompdf = new Dompdf();

		// /*** RENDER VIEW ***/
		$html = $this->renderView('admin/users/user-profile-pdf');

		$dompdf->loadHtml($html);
		$dompdf->set_option('isRemoteEnabled', true);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$dompdf->stream($data['row']->firstname . '_' . $data['row']->surname . '_Profile.pdf', [
			'Attachment' => 0
		]);
	}

	public function debtor_profile_pdf(?string $id = null): void
	{
		$debtor = new Debtor();

		$data['debtor_profile'] = $debtor->debtorProfile($id);

		$url = ROOT . "/admin/debtors/pdf/$id";
		$id = extract_id_from_url($url);
		$data['id'] = $id;


		// /*** INSTANTIATE RELEVANT INSTANCES (OBJECTS) ***/
		$dompdf = new Dompdf();

		// /*** RENDER VIEW ***/
		$html = $this->renderView('admin/case-management/debtors/debtor-profile-pdf');

		$dompdf->loadHtml($html);
		$dompdf->set_option('isRemoteEnabled', true);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$dompdf->stream($data['debtor_profile']->debtor_name .'_Debtor_Profile.pdf', [
			'Attachment' => 0
		]);
	}

	public function clients_list_pdf(): void
	{
		// /*** INSTANTIATE RELEVANT INSTANCES (OBJECTS) ***/
		$dompdf = new Dompdf();

		// /*** RENDER VIEW ***/
		$html = $this->renderView('admin/clients/clients-pdf');

		$dompdf->loadHtml($html);
		$dompdf->set_option('isRemoteEnabled', true);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$dompdf->stream('clients_list.pdf', [
			'Attachment' => 0
		]);
	}
	
	public function user_list_pdf(): void
	{
		$user = new User();

		$data['users_list'] = $user->findAll();


		// /*** INSTANTIATE RELEVANT INSTANCES (OBJECTS) ***/
		$dompdf = new Dompdf();

		// /*** RENDER VIEW ***/
		$html = $this->renderView('admin/users/users-pdf');

		$dompdf->loadHtml($html);
		$dompdf->set_option('isRemoteEnabled', true);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$dompdf->stream('mmf_users_list.pdf', [
			'Attachment' => 0
		]);
	}

	// Helper function to 'payments' function
	private function renderView(string $viewName): string
	{
		ob_start();
		$this->view($viewName);
		$content = ob_get_clean();
		return $content;
	}
}
