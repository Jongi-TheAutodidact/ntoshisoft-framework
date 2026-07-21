<?php
defined('ROOTPATH') or exit('Access Denied!');

class NotificationController
{
    use Controller;

    private $notification;
    private $userModel;

    public function __construct()
    {
        $this->notification = new Notification();
        $this->userModel = new User();

        if (!$this->userModel->logged_in()) {
            redirect('auth/login');
        }
    }

    public function index(): void
    {
        $userId = user('user_id');
        $data['rows'] = $this->notification->getRecentByUser($userId);
        $data['page_title'] = 'Notifications';
        $this->view('admin/sentinel/notifications/index', $data);
    }

    public function markRead(?string $id = null): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Validator::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token!');
            }
            $this->notification->markAsRead((int)$id);
            Util::setFlash('success', 'Notification marked as read');
        }
        redirect('admin/sentinel/notifications');
    }
}
