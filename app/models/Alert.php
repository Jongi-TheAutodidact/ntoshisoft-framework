<?php
defined('ROOTPATH') or exit('Access Denied!');

class Alert
{
    use Model;

    protected $table = 'alerts';
    protected $allowedColumns = [
        'alert_number',
        'title',
        'description',
        'alert_type',
        'severity',
        'source_entity',
        'source_entity_id',
        'icon',
        'color',
        'link',
        'is_read',
        'is_dismissed',
        'triggered_by',
        'triggered_at',
        'expires_at'
    ];

    public function validate(array $data, int|string|null $id = null): bool
    {
        $this->errors = [];

        if (empty($data['title'])) {
            $this->errors['title'] = 'Title is required';
        }

        if (empty($data['alert_type'])) {
            $this->errors['alert_type'] = 'Alert type is required';
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    public function getActiveAlerts(): array
    {
        $sql = "SELECT a.*
                FROM alerts a
                WHERE a.is_dismissed = 0
                AND (a.expires_at IS NULL OR a.expires_at >= CURDATE())
                ORDER BY a.severity DESC, a.triggered_at DESC";

        return $this->query($sql) ?: [];
    }

    public function getUnreadCount(): int
    {
        $sql = "SELECT COUNT(*) as count FROM alerts WHERE is_read = 0 AND is_dismissed = 0";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function generateAlertNumber(): string
    {
        $prefix = 'ALRT';
        $year = date('Y');

        $sql = "SELECT MAX(id) as max_id FROM alerts WHERE YEAR(date_created) = ?";
        $result = $this->query($sql, [$year]);
        $serial = $result ? ((int)$result[0]->max_id % 10000) + 1 : 1;

        return "{$prefix}-{$year}-" . str_pad($serial, 4, '0', STR_PAD_LEFT);
    }
}
