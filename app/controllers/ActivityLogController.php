<?php
defined('ROOTPATH') or exit('Access Denied!');

class ActivityLogController
{
    use Controller;

    private $log;
    private $userModel;

    public function __construct()
    {
        $this->log = new ActivityLog();
        $this->userModel = new User();

        if (!$this->userModel->logged_in()) {
            redirect('auth/login');
        }
    }

    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $total = $this->log->query(
            "SELECT COUNT(*) as cnt FROM activity_logs"
        );
        $totalCount = $total ? (int)$total[0]->cnt : 0;

        $data['rows'] = $this->log->query(
            "SELECT a.*
             FROM activity_logs a
             ORDER BY a.date_created DESC
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        ) ?: [];

        $data['total'] = $totalCount;
        $data['page'] = $page;
        $data['per_page'] = $perPage;
        $data['total_pages'] = max(1, (int)ceil($totalCount / $perPage));
        $data['page_title'] = 'Activity Logs';

        $this->view('admin/sentinel/activity/index', $data);
    }
}
