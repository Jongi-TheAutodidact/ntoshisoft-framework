<?php

defined('ROOTPATH') or exit('Access Denied!');

class ChatConversation
{
    use Model;

    protected $table = 'chat_conversations';
    protected $primaryKey = 'id';

    protected $allowedColumns = [
        'user_one_id',
        'user_two_id',
        'last_message_id',
        'last_message_at',
        'is_active',
    ];

    public function getOrCreate(int $userId1, int $userId2): int|false
    {
        $minId = min($userId1, $userId2);
        $maxId = max($userId1, $userId2);

        $existing = $this->query(
            "SELECT id FROM chat_conversations WHERE user_one_id = ? AND user_two_id = ? AND is_active = 1",
            [$minId, $maxId]
        );

        if (!empty($existing)) {
            return (int) $existing[0]->id;
        }

        $this->insert([
            'user_one_id' => $minId,
            'user_two_id' => $maxId,
        ]);

        $new = $this->query(
            "SELECT id FROM chat_conversations WHERE user_one_id = ? AND user_two_id = ?",
            [$minId, $maxId]
        );

        return !empty($new) ? (int) $new[0]->id : false;
    }

    public function getConversation(int $conversationId): object|false
    {
        $result = $this->query(
            "SELECT * FROM chat_conversations WHERE id = ?",
            [$conversationId]
        );
        return $result ? $result[0] : false;
    }

    public function findConversationBetween(int $userId1, int $userId2): object|false
    {
        $minId = min($userId1, $userId2);
        $maxId = max($userId1, $userId2);

        $result = $this->query(
            "SELECT * FROM chat_conversations WHERE user_one_id = ? AND user_two_id = ? AND is_active = 1",
            [$minId, $maxId]
        );
        return $result ? $result[0] : false;
    }

    public function getUserConversations(int $userId): array|false
    {
        return $this->query("
            SELECT c.*,
                CASE 
                    WHEN c.user_one_id = ? THEN c.user_two_id
                    ELSE c.user_one_id
                END as other_user_id,
                u.firstname as other_firstname,
                u.surname as other_surname,
                u.image as other_image,
                m.message as last_message,
                m.message_type as last_message_type,
                m.date_sent as last_message_time,
                m.user_id as last_message_sender,
                (SELECT COUNT(*) FROM chat_messages cm 
                 WHERE cm.conversation_id = c.id 
                 AND cm.user_id != ? 
                 AND cm.is_read = 0 
                 AND cm.deleted_at IS NULL
                ) as unread_count
            FROM chat_conversations c
            JOIN users u ON u.id = CASE WHEN c.user_one_id = ? THEN c.user_two_id ELSE c.user_one_id END
            LEFT JOIN chat_messages m ON m.id = c.last_message_id
            WHERE (c.user_one_id = ? OR c.user_two_id = ?)
            AND c.is_active = 1
            ORDER BY c.last_message_at DESC
        ", [$userId, $userId, $userId, $userId, $userId]);
    }

    public function updateLastMessage(int $conversationId, int $messageId): bool
    {
        return $this->update($conversationId, [
            'last_message_id' => $messageId,
            'last_message_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getOtherUserId(int $conversationId, int $currentUserId): int
    {
        $conv = $this->getConversation($conversationId);
        if (!$conv) return 0;
        return $conv->user_one_id == $currentUserId ? $conv->user_two_id : $conv->user_one_id;
    }
}
