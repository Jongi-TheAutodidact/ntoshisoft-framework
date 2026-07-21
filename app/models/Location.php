<?php
defined('ROOTPATH') or exit('Access Denied!');

class Location
{
    use Model;

    protected $table = 'locations';
    protected $allowedColumns = [
        'location_code',
        'name',
        'description',
        'address',
        'city',
        'province',
        'country',
        'latitude',
        'longitude',
        'location_type',
        'risk_level',
        'is_active',
        'created_by',
        'updated_by',
        'date_updated'
    ];

    public function validate(array $data, int|string|null $id = null): bool
    {
        $this->errors = [];

        if (empty($data['name'])) {
            $this->errors['name'] = 'Name is required';
        }

        if (empty($data['location_type'])) {
            $this->errors['location_type'] = 'Location type is required';
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    public function getAllWithDetails(): array
    {
        $sql = "SELECT l.*,
                CONCAT(u.firstname, ' ', u.surname) as created_by_name
                FROM locations l
                LEFT JOIN users u ON l.created_by = u.user_id
                ORDER BY l.name ASC";

        return $this->query($sql) ?: [];
    }

    public function getActiveLocations(): array
    {
        $sql = "SELECT l.*
                FROM locations l
                WHERE l.is_active = 1
                ORDER BY l.name ASC";

        return $this->query($sql) ?: [];
    }

    public function generateLocationCode(): string
    {
        $prefix = 'LOC';
        $year = date('Y');

        $sql = "SELECT MAX(id) as max_id FROM locations WHERE YEAR(date_created) = ?";
        $result = $this->query($sql, [$year]);
        $serial = $result ? ((int)$result[0]->max_id % 10000) + 1 : 1;

        return "{$prefix}-{$year}-" . str_pad($serial, 4, '0', STR_PAD_LEFT);
    }
}
