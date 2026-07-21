<?php

defined('ROOTPATH') OR exit('Access Denied!');

class ExpenditureController
{
    use Controller;

    public function __construct()
    {
        $user = new User();
        if (!$user->logged_in()) redirect('auth/login');
    }

    public function index(): void
    {
        $expenditure = new Expenditure();
        $payment = new Payment();
        $data = [
            'expenditures' => $expenditure->getAllExpenditures(),
            'sum_all_expenditures' => $expenditure->sumAllExpenditures(),
            'sum_all_payments' => $payment->sumAllPayments(),
            'net_balance' => $expenditure->getNetBalance(),
            'expense_types' => [
                'Office Supplies' => $expenditure->sumExpendituresByType('Office Supplies'),
                'Salaries' => $expenditure->sumExpendituresByType('Salaries'),
                'Utilities' => $expenditure->sumExpendituresByType('Utilities'),
                'Maintenance' => $expenditure->sumExpendituresByType('Maintenance'),
                'Marketing' => $expenditure->sumExpendituresByType('Marketing'),
                'Other' => $expenditure->sumExpendituresByType('Other')
            ],
            'errors' => [],
            'page_title' => 'Expenditure'
        ];

        $this->view('admin/expenditure/expenditures', $data);
    }

    public function create_expenditure(): void
    {
        $expenditure = new Expenditure();
        $data = [
            'errors' => $expenditure->errors,
            'page_title' => 'Add New Expenditure'
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($expenditure->validate($_POST)) {
                if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    die('Invalid CSRF Token!');
                } else {
                    $expenditure->insert($_POST);
                    Util::setFlash('expenditure_register_success', 'Expenditure Recorded Successfully!');
                    redirect('admin/expenditure');
                }
            }
        }

        $this->view('admin/expenditure/expenditure-create', $data);
    }

    public function edit_expenditure(?string $id = null): void
    {
        $expenditure = new Expenditure();
        $data = [
            'expenditure' => $expenditure->getSingleExpenditure($id),
            'errors' => $expenditure->errors,
            'page_title' => 'Edit Expenditure'
        ];

        if (!$data['expenditure']) {
            Util::setFlash('expenditure_error', 'Expenditure not found.');
            redirect('admin/expenditure');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($expenditure->validate($_POST)) {
                if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    die('Invalid CSRF Token!');
                } else {
                    $expenditure->update($id, $_POST);
                    Util::setFlash('expenditure_update_success', 'Expenditure Updated Successfully!');
                    redirect('admin/expenditure');
                }
            }
        }

        $this->view('admin/expenditure/expenditure-edit', $data);
    }

    public function delete_expenditure(?string $id = null): void
    {
        $expenditure = new Expenditure();
        $data = [
            'expenditure' => $expenditure->getSingleExpenditure($id),
            'page_title' => 'Delete Expenditure'
        ];

        if (!$data['expenditure']) {
            Util::setFlash('expenditure_error', 'Expenditure not found.');
            redirect('admin/expenditure');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Invalid CSRF Token!');
            } else {
                $expenditure->delete($id);
                Util::setFlash('expenditure_delete_success', 'Expenditure Deleted Successfully!');
                redirect('admin/expenditure');
            }
        }

        $this->view('admin/expenditure/expenditure-delete', $data);
    }
}