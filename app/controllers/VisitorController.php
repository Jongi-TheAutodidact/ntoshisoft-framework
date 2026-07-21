<?php

defined('ROOTPATH') OR exit('Access Denied!');

class VisitorController
{
    use Controller;

    public function visitors(): void
    {
        $visitor = new Visitor();
        $onlineUser = new OnlineUser();

        $data['recent_visits'] = $visitor->getRecentVisits(50);
        $data['total_visits'] = $visitor->getTotalVisits();
        $data['unique_visits_today'] = $visitor->getUniqueVisitsToday();
        $data['visits_by_country'] = $visitor->getVisitsByCountry();
        $data['visits_by_city'] = $visitor->getVisitsByCity();
        $data['online_users'] = $onlineUser->getOnlineUsers();
        $data['num_online_users'] = $onlineUser->numOnlineUsers();

        $data['page_title'] = 'Visitor Analytics';
        $this->view('admin/visitors/visitors', $data);
    }

    public function single_view(?string $id = null): void
    {
        $visitor = new Visitor();

        if (!$id) {
            redirect('admin/visitors');
        }

        $data['visit'] = $visitor->first(['id' => $id]);

        if (!$data['visit']) {
            redirect('admin/visitors');
        }

        $data['page_title'] = 'View Visit Details';
        $this->view('admin/visitors/view', $data);
    }
}