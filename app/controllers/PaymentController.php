<?php

/**
 * PaymentController class
 */

defined('ROOTPATH') OR exit('Access Denied!');

class PaymentController
{
    use Controller;

    public function __construct()
	{
		$user = new User();

		// Check if current user is logged in 
		if (!$user->logged_in())
			redirect('auth/login');
	}

    public function index(): void
    {
        $payment = new Payment();
        $user = new User();

       

        $data['payments'] = $payment->getAllPayments(); 
        $data['sum_all_payments'] = $payment->sumAllPayments(); 
        $data['sum_eft_pay'] = $payment->sumEFTPayments(); 
        $data['sum_cash_pay'] = $payment->sumCashPayments(); 
        $data['errors'] = $payment->errors;
        $data['page_title'] = 'Payments';

        $this->view('admin/payments/payments', $data);
    }

    public function create_payment(?string $id = null): void
    {
        $payment = new Payment();
        $client = new Client();

        // Get Clients For Dropdown
        $data['clients'] = $client->clientsWithUsersDetails('Client');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($payment->validate($_POST)) {
                if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    die('Invalid CSRF Token!');
                } else {
                    // Insert into DB
                    $payment->insert($_POST);
                    Util::setFlash('payment_register_success', 'Payment Registered Successfully!!');
                    redirect('admin/payments');
                }
            }
        }

        $preselect_client_id = $id ?? null;
        $data['preselect_client_id'] = $preselect_client_id;

        $data['errors'] = $payment->errors;
        $data['page_title'] = 'Add New Payment';

        $this->view('admin/payments/payment-create', $data);
    }

    public function edit_payment(?string $id = null): void
    {
        $payment = new Payment();
        $user = new User();
        $client = new Client();

        if (!$user->logged_in()) {
            redirect('auth/login');
        }

        if (!$id) {
            Util::setFlash('payment_error', 'Payment not found.');
            redirect('admin/payments');
        }

        $data['payment'] = $payment->first(['id' => $id]);
        // Get Members For Dropdown
        $data['clients'] = $client->clientsWithUsersDetails('Client');

        if (!$data['payment']) {
            Util::setFlash('payment_error', 'Payment not found.');
            redirect('admin/payments');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($payment->validate($_POST)) {
                if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    die('Invalid CSRF Token!');
                } else {
                    // Update record
                    $payment->update($id, $_POST);
                    Util::setFlash('payment_update_success', 'Payment Updated Successfully!!');
                    redirect('admin/payments');
                }
            }
        }

        $preselect_member_id = $id ?? null;
        $data['preselect_member_id'] = $preselect_member_id;
        
        $data['errors'] = $payment->errors;
        $data['page_title'] = 'Edit Payment';
        $this->view('admin/payments/payment-edit', $data);
    }

    public function delete_payment(?string $id = null): void
    {
        $payment = new Payment();
        $user = new User();

        if (!$user->logged_in()) {
            redirect('auth/login');
        }

        if (!$id) {
            Util::setFlash('payment_error', 'Payment not found.');
            redirect('admin/payments');
        }

        $data['payment'] = $payment->getSinglePayment($id);

        if (!$data['payment']) {
            Util::setFlash('payment_error', 'Payment not found.');
            redirect('admin/payments');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Invalid CSRF Token!');
            } else {
                $payment->delete($id);
                Util::setFlash('payment_delete_success', 'Payment Deleted Successfully!!');
                redirect('admin/payments');
            }
        }

        $data['page_title'] = 'Delete Payment';
        $this->view('admin/payments/payment-delete', $data);
    }
}