<?php

/**
 * Payment Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Payment
{
    use Model;

    protected $table = 'payments';
    protected $primaryKey = 'id';

    protected $allowedColumns = [
        'payment_date',
        'client',
        'amount',
        'pay_type',
        'paid_via',
        'notes',
        'created_by',
        'updated_by',
        'date_updated',
    ];

    public function validate(array $post_data, int|string|null $id = null): bool
    {
        $this->errors = [];

        if (empty($post_data['payment_date'])) {
            $this->errors['payment_date'] = "** Payment date is required **";
        }

        if (empty($post_data['client'])) {
            $this->errors['client'] = "** Client name is required **";
        }

        if (empty($post_data['amount'])) {
            $this->errors['amount'] = "** Amount is required **";
        } elseif (!is_numeric($post_data['amount']) || $post_data['amount'] <= 0) {
            $this->errors['amount'] = "** Amount must be a positive number **";
        }

        if (empty($post_data['paid_via'])) {
            $this->errors['paid_via'] = "** Payment method is required **";
        }

        if (!empty($post_data['notes']) && strlen($post_data['notes']) > 65535) {
            $this->errors['notes'] = "** Notes cannot exceed 65,535 characters **";
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    public function getAllPayments(): array
    {
        $sql = "SELECT p.*, u.firstname, u.surname
                FROM payments p 
                LEFT JOIN clients c ON p.client = c.id
                LEFT JOIN users u ON u.user_id = c.user_id
                ORDER BY payment_date DESC";
        $result = $this->query($sql);
        if ($result) {
            return $result;
        }
        return [];
    }

    public function getSinglePayment(int $id): object|array
    {
        $sql = "SELECT p.*, c.user_id, u.firstname, u.surname
                FROM payments p
                LEFT JOIN clients m ON p.client = c.id
                LEFT JOIN users u ON m.user_id = u.user_id
                WHERE p.id = ?
               ";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if ($result) {
            return $result;
        }
        return [];
    }

    public function getPaymentsByClient(string $client_user_id): array
    {
        $sql = "SELECT p.*, c.user_id, u.firstname, u.surname
                FROM payments p
                LEFT JOIN clients c ON p.client = c.id
                LEFT JOIN users u ON c.user_id = u.user_id
                WHERE c.user_id = ?
               ";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$client_user_id]);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        if ($result) {
            return $result;
        }
        return [];
    }

    // Optional: Count total payments
    public function numPayments(): int
    {
        $sql = "SELECT COUNT(*) FROM payments";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function sumAllPayments(): mixed
    {
        $sql = "SELECT SUM(amount) AS total_payments FROM payments;";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function sumCashPayments(): mixed
    {
        $sql = "SELECT SUM(amount) AS total_payments FROM payments WHERE paid_via = 'Cash';";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function sumSingleClientPayments(int $client_id): mixed
    {
        $sql = "SELECT SUM(amount) AS total_premiums FROM payments WHERE client = ?;";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$client_id]);
        return $stmt->fetchColumn();
    }

    public function sumEFTPayments(): mixed
    {
        $sql = "SELECT SUM(amount) AS total_payments FROM payments WHERE paid_via = 'EFT';";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // In app/models/Payment.php
    public function sumPaymentsByMethod(): array
    {
        $sql = "SELECT paid_via AS method, SUM(amount) AS total 
            FROM payments 
            GROUP BY paid_via";

        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function sumPaymentsByType(): array
    {
        $sql = "SELECT pay_type AS type, SUM(amount) AS total 
            FROM payments 
            GROUP BY pay_type";

        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getPaymentMethodsData(): array
    {
        $sql = "SELECT paid_via AS label, SUM(amount) AS value 
            FROM payments 
            GROUP BY paid_via";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyPaymentsTrend(int $months = 6): array
    {
        $sql = "SELECT 
                DATE_FORMAT(payment_date, '%b %Y') AS month,
                SUM(amount) AS total
            FROM payments
            WHERE payment_date >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
            ORDER BY payment_date";

        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$months]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
