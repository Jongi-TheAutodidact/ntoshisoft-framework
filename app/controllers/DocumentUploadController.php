<?php

/**
 * DocumentUploadController class 
 */

defined('ROOTPATH') or exit('Access Denied!');

class DocumentUploadController
{
    use Controller;

    public function __construct()
    {
        $user = new User();
        if (!$user->logged_in()) redirect('auth/login');
    }

    public function index(): void
    {
        $document = new DocumentUpload();
        $data = [
            'documents' => $document->getAllDocuments(),
            'categories' => ['General', 'Contracts', 'Reports', 'Financials', 'Personnel'],
            'errors' => [],
            'page_title' => 'Document Uploads'
        ];

        $this->view('admin/clients/documents', $data);
    }

    public function upload(): void
    {
        $document = new DocumentUpload();
        $client = new Client();

        $data = [
            'clients'       => $client->clientsWithUsersDetails('Client'),
            'errors'        => $document->errors,
            'page_title'    => 'Upload New Document'
        ];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_data = $_POST;
            $file_data = $_FILES['file'] ?? [];

            if ($document->validate($post_data, $file_data)) {
                if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    die('Invalid CSRF Token!');
                }

                // Handle file upload
                $upload_result = $this->handleFileUpload($file_data);
                if ($upload_result['success']) {
                    $post_data['file_path'] = $upload_result['path'];
                    $post_data['file_type'] = $upload_result['type'];
                    $post_data['file_size'] = $upload_result['size'];
                    $post_data['created_by'] = user('firstname') . ' ' . user('surname');

                    $document->insert($post_data);
                    Util::setFlash('document_upload_success', 'Document uploaded successfully!');
                    redirect('admin/documents');
                } else {
                    $document->errors['file'] = $upload_result['error'];
                }
            }
        }

        $this->view('admin/clients/document-upload', $data);
    }

    private function handleFileUpload(array $file): array
    {
        $upload_dir = 'uploads/documents/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $allowed_types = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
        $max_size = 5 * 1024 * 1024; // 5MB

        $file_name = basename($file['name']);
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_types)) {
            return ['success' => false, 'error' => 'File type not allowed'];
        }

        if ($file_size > $max_size) {
            return ['success' => false, 'error' => 'File size exceeds 5MB limit'];
        }

        $new_name = uniqid('doc_', true) . '.' . $file_ext;
        $target_path = $upload_dir . $new_name;

        if (move_uploaded_file($file_tmp, $target_path)) {
            return [
                'success' => true,
                'path' => $target_path,
                'type' => $file_ext,
                'size' => $file_size
            ];
        }

        return ['success' => false, 'error' => 'Error uploading file'];
    }

    public function edit_document(?string $id = null): void
    {
        $document = new DocumentUpload();
        $data = [
            'document' => $document->getDocument($id),
            'errors' => $document->errors,
            'page_title' => 'Edit Document'
        ];

        if (!$data['document']) {
            Util::setFlash('document_error', 'Document not found.');
            redirect('admin/document_uploads');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_data = $_POST;

            if ($document->validate($post_data)) {
                if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    die('Invalid CSRF Token!');
                }

                $post_data['updated_by'] = user('firstname') . ' ' . user('surname');
                $post_data['date_updated'] = date('Y-m-d H:i:s');

                $document->update($id, $post_data);
                Util::setFlash('document_update_success', 'Document updated successfully!');
                redirect('admin/documents');
            }
        }

        $this->view('admin/clients/document-edit', $data);
    }

    public function delete_document(?string $id = null): void
    {
        $document = new DocumentUpload();
        $doc = $document->getDocument($id);

        if (!$doc) {
            Util::setFlash('document_error', 'Document not found.');
            redirect('admin/document_uploads');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Invalid CSRF Token!');
            }

            // Delete physical file
            if (file_exists($doc->file_path)) {
                unlink($doc->file_path);
            }

            $document->delete($id);
            Util::setFlash('document_delete_success', 'Document deleted successfully!');
            redirect('admin/documents');
        }

        $data = [
            'document' => $doc,
            'page_title' => 'Delete Document'
        ];

        $this->view('admin/clients/document-delete', $data);
    }
}
