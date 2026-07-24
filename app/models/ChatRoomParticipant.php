<?php

defined('ROOTPATH') or exit('Access Denied!');

class ChatRoomParticipant
{
    use Model;

    protected $table = 'chat_room_participants';
    protected $primaryKey = 'id';

    protected $allowedColumns = [
        'room_id',
        'user_id',
        'role',
        'is_muted',
        'last_read_message_id',
        'joined_at',
    ];

    public function getRoomParticipants(int $roomId): array|false
    {
        return $this->query("
            SELECT rp.*, u.firstname, u.surname, u.image, u.user_id as uid
            FROM chat_room_participants rp
            JOIN users u ON rp.user_id = u.id
            WHERE rp.room_id = ?
            ORDER BY rp.joined_at ASC
        ", [$roomId]);
    }

    public function addParticipant(int $roomId, int $userId, string $role = 'member'): false
    {
        $existing = $this->query(
            "SELECT id FROM chat_room_participants WHERE room_id = ? AND user_id = ?",
            [$roomId, $userId]
        );

        if (!empty($existing)) {
            return false;
        }

        return $this->insert([
            'room_id' => $roomId,
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    public function removeParticipant(int $roomId, int $userId): bool
    {
        $record = $this->query(
            "SELECT id FROM chat_room_participants WHERE room_id = ? AND user_id = ?",
            [$roomId, $userId]
        );

        if (!empty($record)) {
            return $this->delete($record[0]->id);
        }
        return false;
    }

    public function isParticipant(int $roomId, int $userId): bool
    {
        $sql = "SELECT COUNT(*) FROM chat_room_participants WHERE room_id = ? AND user_id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$roomId, $userId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function updateLastRead(int $roomId, int $userId, int $messageId): bool
    {
        $record = $this->query(
            "SELECT id FROM chat_room_participants WHERE room_id = ? AND user_id = ?",
            [$roomId, $userId]
        );

        if (!empty($record)) {
            return $this->update($record[0]->id, [
                'last_read_message_id' => $messageId,
            ]);
        }
        return false;
    }

    public function getParticipantCount(int $roomId): int
    {
        $sql = "SELECT COUNT(*) FROM chat_room_participants WHERE room_id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$roomId]);
        return (int) $stmt->fetchColumn();
    }
}
