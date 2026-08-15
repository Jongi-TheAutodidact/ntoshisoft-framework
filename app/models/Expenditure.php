<?php

defined('ROOTPATH') or exit('Access Denied!');

class Expenditure
{
    use Model;

    protected $table = 'expenditures';
    protected $primaryKey = 'id'; 

    // Participates in the NtoshiSoft offline-first / PWA sync engine.
    public bool $offlineEnabled = true;

    protected $allowedColumns = [
        'expenditure_date',
        'description',
        'amount',
        'expense_type',
        'paid_via',
        'notes',
        'created_by',
        'updated_by',
        'date_updated',
    ];

	public function validate(array $post_data, int|string|null $id = null): bool
    {
        $this->errors = [];

        if (empty($post_data['expenditure_date'])) {
            $this->errors['expenditure_date'] = "** Expenditure date is required **";
        }

        if (empty($post_data['description'])) {
            $this->errors['description'] = "** Description is required **";
        } elseif (strlen($post_data['description']) > 255) {
            $this->errors['description'] = "** Description cannot exceed 255 characters **";
        }

        if (empty($post_data['amount'])) {
            $this->errors['amount'] = "** Amount is required **";
        } elseif (!is_numeric($post_data['amount']) || $post_data['amount'] <= 0) {
            $this->errors['amount'] = "** Amount must be a positive number **";
        }

        if (empty($post_data['paid_via'])) {
            $this->errors['paid_via'] = "** Payment method is required **";
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

	public function getAllExpenditures(): array
    {
        $sql = "SELECT * FROM expenditures ORDER BY expenditure_date DESC";
        $result = $this->query($sql);
        return $result ? $result : [];
    }

	public function getSingleExpenditure(int|string $id): object|false
    {
        return $this->first(['id' => $id]);
    }

	public function sumAllExpenditures(): mixed
    {
        $sql = "SELECT SUM(amount) AS total_expenditures FROM expenditures";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

	public function sumExpendituresByType(string $type): mixed
    {
        $sql = "SELECT SUM(amount) AS total FROM expenditures WHERE expense_type = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$type]);
        return $stmt->fetchColumn();
    }
    
    // Get net balance (Payments - Expenditures)
	public function getNetBalance(): mixed
    {
        $payment = new Payment();
        $totalPayments = $payment->sumAllPayments();
        $totalExpenditures = $this->sumAllExpenditures();
        return $totalPayments - $totalExpenditures;
    }
}