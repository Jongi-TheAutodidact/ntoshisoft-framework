<?php

/**
 * DocumentUpload Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class DocumentUpload
{
    use Model;

    protected $table = 'document_uploads';
    protected $primaryKey = 'id';

    protected $allowedColumns = [
        'title',
        'client_id',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'category',
        'status',
        'updated_by',
        'date_updated',
    ];

	public function validate(array $post_data, array $file_data = []): bool
    {
        $this->errors = [];

        if (empty($post_data['title'])) {
            $this->errors['title'] = "** Title is required **";
        } elseif (strlen($post_data['title']) > 255) {
            $this->errors['title'] = "** Title cannot exceed 255 characters **";
        }

        if (empty($file_data['name'])) {
            $this->errors['file'] = "** File is required **";
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

	public function getAllDocuments(): array|false
    {
        $sql = "SELECT * FROM document_uploads ORDER BY date_created DESC";
        return $this->query($sql) ?: [];
    }

	public function getDocument(int|string $id): object|false
    {
        return $this->first(['id' => $id]);
    }

	public function getDocumentsByCategory(string $category): array|false
    {
        return $this->where(['category' => $category]);
    }

	public function getDocumentsByClient(string $client_user_id): array
    {
        $sql = "SELECT du.*, c.user_id, u.firstname, u.surname
                FROM document_uploads du
                LEFT JOIN clients c ON du.client_id = c.id
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
}
