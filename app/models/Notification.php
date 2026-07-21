<?php
defined('ROOTPATH') or exit('Access Denied!');

class Notification
{
    use Model;

    protected $table = 'notifications';
    protected $allowedColumns = [
        'user_id',
        'user_name',
        'title',
        'message',
        'notification_type',
        'icon',
        'link',
        'is_read',
        'sent_at',
        'read_at'
    ];

    public function getUnreadByUser(int|string $userId): array
    {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = ? AND is_read = 0
                ORDER BY sent_at DESC";

        return $this->query($sql, [$userId]) ?: [];
    }

    public function markAsRead(int|string $id): bool
    {
        return $this->update($id, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getRecentByUser(int|string $userId, int $limit = 10): array
    {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = ?
                ORDER BY sent_at DESC
                LIMIT ?";

        return $this->query($sql, [$userId, $limit]) ?: [];
    }
}
