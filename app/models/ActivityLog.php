<?php
defined('ROOTPATH') or exit('Access Denied!');

class ActivityLog
{
    use Model;

    protected $table = 'activity_logs';
    protected $allowedColumns = [
        'user_id',
        'user_name',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    public function validate(array $data, int|string|null $id = null): bool
    {
        return true;
    }
}
