<?php
defined('ROOTPATH') or exit('Access Denied!');

class MediaFile
{
    use Model;

    protected $table = 'media_files';
    protected $allowedColumns = [
        'incident_id',
        'observation_id',
        'evidence_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'uploaded_by',
        'description',
        'is_public',
        'media_metadata'
    ];

    public function validate(array $data, int|string|null $id = null): bool
    {
        $this->errors = [];

        if (empty($data['file_name'])) {
            $this->errors['file_name'] = 'File name is required';
        }

        if (empty($data['file_path'])) {
            $this->errors['file_path'] = 'File path is required';
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    public function getAllWithUserDetails(): array
    {
        $sql = "SELECT mf.*,
                CONCAT(u.firstname, ' ', u.surname) as uploaded_by_name
                FROM media_files mf
                LEFT JOIN users u ON mf.uploaded_by = u.user_id
                ORDER BY mf.id DESC";

        return $this->query($sql) ?: [];
    }

    public function getByIncident(int|string $incidentId): array
    {
        $sql = "SELECT mf.*,
                CONCAT(u.firstname, ' ', u.surname) as uploaded_by_name
                FROM media_files mf
                LEFT JOIN users u ON mf.uploaded_by = u.user_id
                WHERE mf.incident_id = ?
                ORDER BY mf.id DESC";

        return $this->query($sql, [$incidentId]) ?: [];
    }

    public function getByObservation(int|string $observationId): array
    {
        $sql = "SELECT mf.*,
                CONCAT(u.firstname, ' ', u.surname) as uploaded_by_name
                FROM media_files mf
                LEFT JOIN users u ON mf.uploaded_by = u.user_id
                WHERE mf.observation_id = ?
                ORDER BY mf.id DESC";

        return $this->query($sql, [$observationId]) ?: [];
    }
}
