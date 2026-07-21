<?php
defined('ROOTPATH') or exit('Access Denied!');

class Task
{
    use Model;

    protected $table = 'tasks';
    protected $allowedColumns = [
        'task_number',
        'title',
        'description',
        'task_type',
        'priority',
        'status',
        'assigned_to',
        'assigned_by',
        'incident_id',
        'case_id',
        'due_date',
        'completed_at',
        'completion_notes',
        'created_by',
        'updated_by',
        'date_updated'
    ];

    public function validate(array $data, int|string|null $id = null): bool
    {
        $this->errors = [];

        if (empty($data['title'])) {
            $this->errors['title'] = 'Title is required';
        }

        if (empty($data['assigned_to'])) {
            $this->errors['assigned_to'] = 'Assignee is required';
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    public function getAllWithDetails(): array
    {
        $sql = "SELECT t.*,
                CONCAT(au.firstname, ' ', au.surname) as assigned_to_name,
                CONCAT(ab.firstname, ' ', ab.surname) as assigned_by_name,
                i.reference_number as incident_reference,
                c.case_number
                FROM tasks t
                LEFT JOIN users au ON t.assigned_to = au.user_id
                LEFT JOIN users ab ON t.assigned_by = ab.user_id
                LEFT JOIN incidents i ON t.incident_id = i.id
                LEFT JOIN cases c ON t.case_id = c.id
                ORDER BY t.due_date ASC, t.priority DESC";

        return $this->query($sql) ?: [];
    }

    public function getByAssignee(int|string $userId): array
    {
        $sql = "SELECT t.*,
                CONCAT(au.firstname, ' ', au.surname) as assigned_to_name,
                CONCAT(ab.firstname, ' ', ab.surname) as assigned_by_name,
                i.reference_number as incident_reference,
                c.case_number
                FROM tasks t
                LEFT JOIN users au ON t.assigned_to = au.user_id
                LEFT JOIN users ab ON t.assigned_by = ab.user_id
                LEFT JOIN incidents i ON t.incident_id = i.id
                LEFT JOIN cases c ON t.case_id = c.id
                WHERE t.assigned_to = ?
                ORDER BY t.status ASC, t.due_date ASC";

        return $this->query($sql, [$userId]) ?: [];
    }

    public function getPendingTasks(): array
    {
        $sql = "SELECT t.*,
                CONCAT(au.firstname, ' ', au.surname) as assigned_to_name,
                CONCAT(ab.firstname, ' ', ab.surname) as assigned_by_name
                FROM tasks t
                LEFT JOIN users au ON t.assigned_to = au.user_id
                LEFT JOIN users ab ON t.assigned_by = ab.user_id
                WHERE t.status NOT IN ('completed', 'cancelled')
                ORDER BY t.due_date ASC, t.priority DESC";

        return $this->query($sql) ?: [];
    }

    public function getTaskStats(): object|false
    {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN due_date < CURDATE() AND status NOT IN ('completed', 'cancelled') THEN 1 ELSE 0 END) as overdue
                FROM tasks";

        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ) ?: false;
    }

    public function generateTaskNumber(): string
    {
        $prefix = 'TASK';
        $year = date('Y');

        $sql = "SELECT MAX(id) as max_id FROM tasks WHERE YEAR(date_created) = ?";
        $result = $this->query($sql, [$year]);
        $serial = $result ? ((int)$result[0]->max_id % 10000) + 1 : 1;

        return "{$prefix}-{$year}-" . str_pad($serial, 4, '0', STR_PAD_LEFT);
    }
}
