<?php

defined('ROOTPATH') or exit('Access Denied!');

class ChatMessage
{
    use Model;

    protected $table = 'chat_messages';
    protected $primaryKey = 'id';

    // Update column names to match migration
    protected $allowedColumns = [
        'room_id',         // was room_id
        'user_id',         // was user_id
        'message',
        'is_delivered',
        'is_read',
        'deleted_at',
        'date_sent'
    ];

	public function getConversationMessages(int $conversationId): array|false
    {
        $sql = "SELECT m.*, u.firstname, u.surname, u.image
            FROM chat_messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.room_id = ?
            AND m.deleted_at IS NULL
            ORDER BY m.date_sent ASC";

        return $this->query($sql, [$conversationId]);
    }

	public function sendMessage(int $roomId, int $senderId, string $message): false
    {
        $data = [
            'room_id' => $roomId,
            'user_id' => $senderId,
            'message' => $message
        ];

        return $this->insert($data);
    }

	public function markAsRead(int $messageId): bool
    {
        return $this->update($messageId, ['is_read' => 1]);
    }

	public function getUnreadMessages(int $userId, int $roomId): array|false
    {
        $sql = "SELECT * FROM chat_messages 
                WHERE room_id = ? 
                AND user_id != ? 
                AND is_read = 0
                AND deleted_at IS NULL";
        return $this->query($sql, [$roomId, $userId]);
    }

	public function getNewMessages(int $roomId, int $lastId): array|false
    {
        $sql = "SELECT m.*, u.firstname, u.image
            FROM chat_messages m
            JOIN users u ON u.id = m.user_id
            WHERE m.room_id = ? AND m.id > ?
            ORDER BY m.date_sent ASC";
        return $this->query($sql, [$roomId, $lastId]);
    }
}
