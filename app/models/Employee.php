<?php

/**
 * Employee Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Employee
{
    use Model;

    protected $table = 'employees';  
    protected $primaryKey = 'id';
    protected $allowedColumns = [
        'user_id',
        'employee_number',
        'position',
        'department',
        'hire_date',
        'termination_date',
        'emergency_contact',
        'emergency_phone',
        'qualifications',
        'notes',
        'created_by',
        'updated_by',
        'date_updated'
    ];

    public function validate(array $data, int|string|null $id = null): bool
    {
        $this->errors = [];

        if (empty($data['employee_number'])) {
            $this->errors['employee_number'] = "Employee number is required";
        }

        if (empty($data['position'])) {
            $this->errors['position'] = "Position is required";
        }

        if (empty($data['hire_date'])) {
            $this->errors['hire_date'] = "Hire date is required";
        } elseif (strtotime($data['hire_date']) > time()) {
            $this->errors['hire_date'] = "Hire date cannot be in the future";
        }

        if (!empty($data['termination_date']) && strtotime($data['termination_date']) < strtotime($data['hire_date'])) {
            $this->errors['termination_date'] = "Termination date cannot be before hire date";
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    public function getEmployeesWithUserDetails(): array
    {
        $sql = "SELECT e.*, u.firstname, u.surname, u.email, u.phone, u.image, u.user_role,
                CONCAT(u.firstname, ' ', u.surname) as full_name
                FROM employees e
                LEFT JOIN users u ON e.user_id = u.user_id
                ORDER BY e.position, u.surname";

        return $this->query($sql) ?: [];
    }

    public function getTeachers(): array
    {
        $sql = "SELECT e.*, u.firstname, u.surname, u.email, u.phone, u.image, u.user_role,
                CONCAT(u.firstname, ' ', u.surname) as full_name
                FROM employees e
                LEFT JOIN users u ON e.user_id = u.user_id
                WHERE e.position IN ('Principal', 'ECD Practitioner', 'Assistant Practitioner')
                ORDER BY e.position, u.surname";

        return $this->query($sql) ?: [];
    }

    public function getSingleEmployeeWithUserDetails(int $id): object|false
    {
        $sql = "SELECT e.*, u.firstname, u.surname, u.user_id, u.email, u.phone, u.image, u.user_role,
                CONCAT(u.firstname, ' ', u.surname) as full_name
                FROM employees e
                LEFT JOIN users u ON e.user_id = u.user_id
                WHERE e.id = ?
                ORDER BY e.position, u.surname";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if($result)
        {
            return $result;
        }
        return false;
    }

    public function getDrivers(): array|false
    {
        return $this->query("SELECT e.*, u.firstname, u.surname, u.email, u.image 
                           FROM employees e
                           LEFT JOIN users u ON e.user_id = u.user_id
                           WHERE e.position = 'Driver'
                           ORDER BY u.surname");
    }

    public function getByPosition(string $position): array|false
    {
        return $this->query("SELECT e.*, u.firstname, u.surname, u.email, u.image 
                           FROM employees e
                           JOIN users u ON e.user_id = u.id
                           WHERE e.position = ?
                           ORDER BY u.surname", [$position]);
    }

    public function uploadDocument(int $employee_id, array $file_data): string|false
    {
        $folder = "uploads/employees/$employee_id/docs/";
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $filename = time() . '_' . basename($file_data['name']);
        $destination = $folder . $filename;

        if (move_uploaded_file($file_data['tmp_name'], $destination)) {
            return $destination;
        }
        return false;
    }

    public function getDocuments(int $employee_id): array
    {
        $folder = "uploads/employees/$employee_id/docs/";
        if (!file_exists($folder)) return [];

        $files = scandir($folder);
        return array_filter($files, fn($f) => !in_array($f, ['.', '..']));
    }

    public function updatePerformance(int $employee_id, mixed $score, string $notes): bool
    {
        return $this->update($employee_id, [
            'performance_score' => $score,
            'last_evaluation_date' => date('Y-m-d'),
            'notes' => $notes
        ]);
    }

    public function getSchedule(int $employee_id): array
    {
        $employee = $this->first(['id' => $employee_id]);
        return $employee ? json_decode($employee->schedule, true) : [];
    }

    public function generateEmployeeNumber(?string $date = null): string
	{
		$date = $date ? new DateTime($date) : new DateTime();
        $prefix = 'FH';
        $month = $date->format('m');
        $year = $date->format('Y');
        
        // Get last case for this month
        $sql = "SELECT MAX(id) as max_id FROM employees 
                WHERE YEAR(date_created) = ? AND MONTH(date_created) = ?";
        
        $result = $this->query($sql, [$year, $month]);
        $serial = $result ? ((int)$result[0]->max_id % 10000) + 1 : 1;
        
        return "{$prefix}-{$year}{$month}" . str_pad($serial, 2, '0', STR_PAD_LEFT);
	}
}
