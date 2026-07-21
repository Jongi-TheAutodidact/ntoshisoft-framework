<?php
defined('ROOTPATH') or exit('Access Denied!');

class AlertController
{
    use Controller;

    private $alert;
    private $userModel;

    public function __construct()
    {
        $this->alert = new Alert();
        $this->userModel = new User();

        if (!$this->userModel->logged_in()) {
            redirect('login');
        }
    }

    public function index(): void
    {
        $data['rows'] = $this->alert->getActiveAlerts();
        $data['unread_count'] = $this->alert->getUnreadCount();
        $data['page_title'] = 'Alerts';
        $this->view('admin/sentinel/alerts/index', $data);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Validator::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token!');
            }
            if ($this->alert->validate($_POST)) {
                $_POST['alert_number'] = $this->alert->generateAlertNumber();
                $_POST['triggered_by'] = user('user_id');
                $this->alert->insert($_POST);
                log_activity('CREATE', 'alert', $this->alert->lastInsertId(), 'Created alert: ' . ($_POST['title'] ?? ''));
                Util::setFlash('success', 'Alert created successfully');
                redirect('admin/sentinel/alerts');
            }
            $data['errors'] = $this->alert->errors;
        }

        $data['page_title'] = 'Create Alert';
        $this->view('admin/sentinel/alerts/create', $data);
    }

    public function edit(?string $id = null): void
    {
        $data['row'] = $this->alert->first(['id' => $id]);
        if (!$data['row']) {
            Util::setFlash('error', 'Alert not found');
            redirect('admin/sentinel/alerts');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Validator::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token!');
            }
            if ($this->alert->validate($_POST, $id)) {
                $this->alert->update((int)$id, $_POST);
                log_activity('UPDATE', 'alert', $id, 'Updated alert: ' . ($data['row']->title ?? ''));
                Util::setFlash('success', 'Alert updated successfully');
                redirect('admin/sentinel/alerts');
            }
            $data['errors'] = $this->alert->errors;
        }

        $data['page_title'] = 'Edit Alert';
        $this->view('admin/sentinel/alerts/edit', $data);
    }

    public function delete(?string $id = null): void
    {
        $data['row'] = $this->alert->first(['id' => $id]);
        if (!$data['row']) {
            Util::setFlash('error', 'Alert not found');
            redirect('admin/sentinel/alerts');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Validator::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('Invalid CSRF Token!');
            }
            $title = $data['row']->title ?? '';
            $this->alert->delete((int)$id);
            log_activity('DELETE', 'alert', $id, 'Deleted alert: ' . $title);
            Util::setFlash('success', 'Alert deleted successfully');
            redirect('admin/sentinel/alerts');
        }

        $data['page_title'] = 'Delete Alert';
        $this->view('admin/sentinel/alerts/delete', $data);
    }

    public function markRead(?string $id = null): void
    {
        $this->alert->update((int)$id, ['is_read' => 1, 'date_updated' => date('Y-m-d H:i:s'), 'updated_by' => user('user_id')]);
        log_activity('READ', 'alert', $id, 'Marked alert as read');

        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'unread_count' => $this->alert->getUnreadCount()]);
            exit;
        }

        Util::setFlash('success', 'Alert marked as read');
        redirect('admin/sentinel/alerts');
    }

    public function dismiss(?string $id = null): void
    {
        $this->alert->update((int)$id, ['is_dismissed' => 1, 'date_updated' => date('Y-m-d H:i:s'), 'updated_by' => user('user_id')]);
        log_activity('DISMISS', 'alert', $id, 'Dismissed alert');

        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'unread_count' => $this->alert->getUnreadCount()]);
            exit;
        }

        Util::setFlash('success', 'Alert dismissed');
        redirect('admin/sentinel/alerts');
    }

    public function markAllRead(): void
    {
        $userId = user('user_id');
        $this->alert->query(
            "UPDATE alerts SET is_read = 1, date_updated = NOW(), updated_by = ? WHERE is_read = 0 AND is_dismissed = 0",
            [$userId]
        );

        log_activity('READ_ALL', 'alert', 0, 'All alerts marked as read');
        Util::setFlash('success', 'All alerts marked as read');
        redirect('admin/sentinel/alerts');
    }

    public function getAlertsJson(): void
    {
        $rows = $this->alert->getActiveAlerts();

        header('Content-Type: application/json');
        echo json_encode([
            'rows' => $rows,
            'unread_count' => $this->alert->getUnreadCount(),
        ]);
        exit;
    }

    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
