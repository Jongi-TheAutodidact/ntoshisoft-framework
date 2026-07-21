<?php

defined('ROOTPATH') OR exit('Access Denied!');

class Visitor 
{
	use Model;
    protected $table = 'visitors';
    protected $primaryKey = 'id'; 

    protected $allowedColumns = [
        'ip_address',
        'user_agent',
        'referrer',
        'location',
        'device',
        'country',
        'city',
        'visited_from',
        'visited_to',
        'visited_at'
    ];

    public function logVisit(array $data): void
    {
        $this->insert($data);
    }

    public function getRecentVisits(int $limit = 50): array|false
    {
        $sql = "SELECT * FROM visitors ORDER BY visited_at DESC LIMIT $limit";
        return $this->query($sql);
    }

    public function getUniqueVisitsToday(): int
    {
        $today = date('Y-m-d');
        $sql = "SELECT COUNT(DISTINCT ip_address) as count FROM visitors WHERE DATE(visited_at) = '$today'";
        return $this->query($sql)[0]->count ?? 0;
    }

    public function getTotalVisits(): int
    {
        $sql = "SELECT COUNT(*) as count FROM visitors";
        return $this->query($sql)[0]->count ?? 0;
    }

    public function getVisitsByCountry(): array|false
    {
        $sql = "SELECT country, COUNT(*) as count FROM visitors GROUP BY country";
        return $this->query($sql);
    }

    public function getVisitsByCity(): array|false
    {
        $sql = "SELECT city, country, COUNT(*) as count FROM visitors GROUP BY city, country";
        return $this->query($sql);
    }
}