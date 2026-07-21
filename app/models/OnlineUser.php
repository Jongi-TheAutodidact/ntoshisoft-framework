<?php

defined('ROOTPATH') or exit('Access Denied!');

class OnlineUser

{
    use Model;
    protected $table = 'online_users';
    protected $sessionKey = 'session_id';

    protected $allowedColumns = [
        'session_id',
        'user_id',
        'ip_address',
        'last_active'
    ];

    public function trackVisitor(): void
    {
        $session_id = session_id();
        $ip         = get_ip();
        $currentUserId = user('id') ?: 0;   // 0 = guest

        // See if we already have a row for this session
        $existing = $this->first(['session_id' => $session_id]);

        if ($existing) {
            // Build an update payload
            $data = [
                'last_active' => date('Y-m-d H:i:s'),
            ];

            // If they’ve just logged in (or out), refresh user_id too
            if ((int)$existing->user_id !== (int)$currentUserId) {
                $data['user_id'] = $currentUserId;
            }

            $this->session_update($session_id, $data);
        } else {
            // First‐time visitor this session
            $this->insert([
                'session_id'  => $session_id,
                'user_id'     => $currentUserId,
                'ip_address'  => $ip,
                'last_active' => date('Y-m-d H:i:s')
            ]);
        }

        // Purge stale sessions
        $this->query("DELETE FROM online_users WHERE last_active < NOW() - INTERVAL 30 MINUTE");
    }


    public function getOnlineUsers(): array|false
    {
        $sql = "SELECT o.*, u.firstname, u.surname 
                FROM online_users o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.last_active >= NOW() - INTERVAL 5 MINUTE";
        return $this->query($sql);
    }

    public function numOnlineUsers(): int
    {
        $sql = "SELECT COUNT(*) FROM online_users";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function session_update(string $sessionKeyValue, array $data): array|false
    {
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key = ?";
        }

        $sql = "UPDATE $this->table SET ";
        $sql .= implode(', ', $setClauses);
        $sql .= " WHERE $this->sessionKey = ?";

        $values = array_values($data);
        $values[] = $sessionKeyValue;

        return $this->query($sql, $values);
    }
}
