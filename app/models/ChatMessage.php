<?php

defined('ROOTPATH') or exit('Access Denied!');

class ChatMessage
{
    use Model;

    protected $table = 'chat_messages';
    protected $primaryKey = 'id';

    protected $allowedColumns = [
        'room_id',
        'conversation_id',
        'user_id',
        'message',
        'message_type',
        'media_url',
        'is_delivered',
        'is_read',
        'deleted_at',
        'date_sent',
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

    public function getDirectMessages(int $conversationId, int $limit = 50, int $before = 0): array|false
    {
        $sql = "SELECT m.*, u.firstname, u.surname, u.image
            FROM chat_messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.conversation_id = ?
            AND m.deleted_at IS NULL";

        $params = [$conversationId];

        if ($before > 0) {
            $sql .= " AND m.id < ?";
            $params[] = $before;
        }

        $sql .= " ORDER BY m.date_sent DESC LIMIT ?";
        $params[] = $limit;

        $messages = $this->query($sql, $params);
        return $messages ? array_reverse($messages) : false;
    }

    public function sendMessage(int $roomId, int $senderId, string $message, string $messageType = 'text', ?string $mediaUrl = null, ?int $conversationId = null): int|false
    {
        $data = [
            'room_id' => $roomId,
            'conversation_id' => $conversationId,
            'user_id' => $senderId,
            'message' => $message,
            'message_type' => $messageType,
            'media_url' => $mediaUrl,
        ];

        $this->insert($data);

        $newMsg = $this->query(
            "SELECT id FROM chat_messages WHERE user_id = ? ORDER BY id DESC LIMIT 1",
            [$senderId]
        );

        return !empty($newMsg) ? (int) $newMsg[0]->id : false;
    }

    public function sendDirectMessage(int $conversationId, int $senderId, string $message, string $messageType = 'text', ?string $mediaUrl = null): int|false
    {
        $data = [
            'conversation_id' => $conversationId,
            'user_id' => $senderId,
            'message' => $message,
            'message_type' => $messageType,
            'media_url' => $mediaUrl,
        ];

        $this->insert($data);

        $newMsg = $this->query(
            "SELECT id FROM chat_messages WHERE user_id = ? ORDER BY id DESC LIMIT 1",
            [$senderId]
        );

        return !empty($newMsg) ? (int) $newMsg[0]->id : false;
    }

    public function markAsRead(int $messageId): bool
    {
        return $this->update($messageId, ['is_read' => 1]);
    }

    public function markAsDelivered(int $messageId): bool
    {
        return $this->update($messageId, ['is_delivered' => 1]);
    }

    public function markConversationAsRead(int $conversationId, int $userId): bool
    {
        $sql = "UPDATE chat_messages SET is_read = 1 
                WHERE conversation_id = ? AND user_id != ? AND is_read = 0";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([$conversationId, $userId]);
    }

    public function markRoomAsRead(int $roomId, int $userId): bool
    {
        $sql = "UPDATE chat_messages SET is_read = 1 
                WHERE room_id = ? AND user_id != ? AND is_read = 0 AND conversation_id IS NULL";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([$roomId, $userId]);
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

    public function getUnreadDirectCount(int $conversationId, int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM chat_messages 
                WHERE conversation_id = ? 
                AND user_id != ? 
                AND is_read = 0
                AND deleted_at IS NULL";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$conversationId, $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function getNewMessages(int $roomId, int $lastId): array|false
    {
        $sql = "SELECT m.*, u.firstname, u.surname, u.image
            FROM chat_messages m
            JOIN users u ON u.id = m.user_id
            WHERE m.room_id = ? AND m.id > ? AND m.conversation_id IS NULL
            ORDER BY m.date_sent ASC";
        return $this->query($sql, [$roomId, $lastId]);
    }

    public function getNewDirectMessages(int $conversationId, int $lastId): array|false
    {
        $sql = "SELECT m.*, u.firstname, u.surname, u.image
            FROM chat_messages m
            JOIN users u ON u.id = m.user_id
            WHERE m.conversation_id = ? AND m.id > ?
            ORDER BY m.date_sent ASC";
        return $this->query($sql, [$conversationId, $lastId]);
    }

    public function softDelete(int $messageId): bool
    {
        return $this->update($messageId, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    public function getRoomMessages(int $roomId, int $limit = 50, int $before = 0): array|false
    {
        $sql = "SELECT m.*, u.firstname, u.surname, u.image
            FROM chat_messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.room_id = ? AND m.conversation_id IS NULL
            AND m.deleted_at IS NULL";

        $params = [$roomId];

        if ($before > 0) {
            $sql .= " AND m.id < ?";
            $params[] = $before;
        }

        $sql .= " ORDER BY m.date_sent DESC LIMIT ?";
        $params[] = $limit;

        $messages = $this->query($sql, $params);
        return $messages ? array_reverse($messages) : false;
    }
}
