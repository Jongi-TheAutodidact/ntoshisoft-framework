<?php
defined('ROOTPATH') or exit('Access Denied!');

class MediaFileController
{
    use Controller;

    private $media;
    private $userModel;

    public function __construct()
    {
        $this->media = new MediaFile();
        $this->userModel = new User();

        if (!$this->userModel->logged_in()) {
            redirect('login');
        }
    }

    public function index(): void
    {
        $data['rows'] = $this->media->getAllWithUserDetails();
        $data['page_title'] = 'Media Files';
        $this->view('admin/sentinel/media/index', $data);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Validator::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token!');
            }

            $targetDir = 'uploads/media/';
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            if (!empty($_FILES['file']['name'])) {
                $filename = time() . '_' . basename($_FILES['file']['name']);
                $targetPath = $targetDir . $filename;

                if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                    $_POST['file_path'] = $targetPath;
                    $_POST['file_size'] = $_FILES['file']['size'];
                    $_POST['mime_type'] = mime_content_type($targetPath);
                    $_POST['file_name'] = $_FILES['file']['name'];
                    $_POST['file_type'] = mime_content_type($targetPath);
                }
            }

            if ($this->media->validate($_POST)) {
                $_POST['uploaded_by'] = user('user_id');
                $this->media->insert($_POST);
                Util::setFlash('success', 'Media file uploaded successfully');
                redirect('admin/sentinel/media');
            }
            $data['errors'] = $this->media->errors;
        }

        $data['page_title'] = 'Upload Media File';
        $this->view('admin/sentinel/media/create', $data);
    }

    public function edit(?string $id = null): void
    {
        $data['row'] = $this->media->first(['id' => $id]);
        if (!$data['row']) {
            Util::setFlash('error', 'Media file not found');
            redirect('admin/sentinel/media');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Validator::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token!');
            }
            if ($this->media->validate($_POST, $id)) {
                $this->media->update((int)$id, $_POST);
                Util::setFlash('success', 'Media file updated successfully');
                redirect('admin/sentinel/media');
            }
            $data['errors'] = $this->media->errors;
        }

        $data['page_title'] = 'Edit Media File';
        $this->view('admin/sentinel/media/edit', $data);
    }

    public function delete(?string $id = null): void
    {
        $data['row'] = $this->media->first(['id' => $id]);
        if (!$data['row']) {
            Util::setFlash('error', 'Media file not found');
            redirect('admin/sentinel/media');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Validator::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token!');
            }
            if (!empty($data['row']->file_path) && file_exists($data['row']->file_path)) {
                unlink($data['row']->file_path);
            }
            $this->media->delete((int)$id);
            Util::setFlash('success', 'Media file deleted successfully');
            redirect('admin/sentinel/media');
        }

        $data['page_title'] = 'Delete Media File';
        $this->view('admin/sentinel/media/delete', $data);
    }
}
