<?php
defined('ROOTPATH') or exit('Access Denied!');

class Geofence
{
    use Model;

    protected $table = 'geofences';
    protected $allowedColumns = [
        'name',
        'description',
        'geofence_type',
        'latitude',
        'longitude',
        'radius_meters',
        'boundary_points',
        'color',
        'is_active',
        'risk_level',
        'assigned_to',
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

        if (empty($data['geofence_type'])) {
            $this->errors['geofence_type'] = 'Geofence type is required';
        }

        if (empty($data['latitude']) || !is_numeric($data['latitude'])) {
            $this->errors['latitude'] = 'Valid latitude is required';
        }

        if (empty($data['longitude']) || !is_numeric($data['longitude'])) {
            $this->errors['longitude'] = 'Valid longitude is required';
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    public function getAllWithDetails(): array
    {
        $sql = "SELECT g.*,
                CONCAT(u.firstname, ' ', u.surname) as created_by_name
                FROM geofences g
                LEFT JOIN users u ON g.created_by = u.user_id
                ORDER BY g.name ASC";

        return $this->query($sql) ?: [];
    }

    public function getActiveGeofences(): array
    {
        $sql = "SELECT g.*
                FROM geofences g
                WHERE g.is_active = 1
                ORDER BY g.risk_level DESC, g.name ASC";

        return $this->query($sql) ?: [];
    }
}
