<?php

defined('ROOTPATH') OR exit('Access Denied!');

class ChatRoom
{
    use Model;

    protected $table = 'chat_rooms';
    protected $primaryKey = 'id';

    protected $allowedColumns = [
        'room_name',
        'room_type',
        'description',
        'avatar',
        'created_by',
        'is_active',
    ];

    public function getActiveRooms(): array|false
    {
        $sql = "SELECT r.*, u.firstname, u.surname, u.image,
                (SELECT COUNT(*) FROM chat_messages m WHERE m.room_id = r.id AND m.conversation_id IS NULL) as message_count,
                (SELECT COUNT(*) FROM chat_room_participants rp WHERE rp.room_id = r.id) as participant_count
                FROM chat_rooms r
                JOIN users u ON r.created_by = u.id
                WHERE r.is_active = 1
                ORDER BY r.date_created DESC";
        return $this->query($sql);
    }

    public function createRoom(int $userId, string $roomName, string $roomType = 'group', ?string $description = null): int|false
    {
        $data = [
            'room_name' => $roomName,
            'room_type' => $roomType,
            'description' => $description,
            'created_by' => $userId,
            'is_active' => 1,
        ];

        $this->insert($data);

        $newRoom = $this->query(
            "SELECT id FROM chat_rooms WHERE created_by = ? ORDER BY id DESC LIMIT 1",
            [$userId]
        );

        if (!empty($newRoom)) {
            $roomId = (int) $newRoom[0]->id;
            $participant = new ChatRoomParticipant();
            $participant->addParticipant($roomId, $userId, 'admin');
            return $roomId;
        }

        return false;
    }

    public function getRoom(int $roomId): object|false
    {
        $sql = "SELECT r.*, u.firstname, u.surname, u.image 
                FROM chat_rooms r
                JOIN users u ON r.created_by = u.id
                WHERE r.id = ?";
        $result = $this->query($sql, [$roomId]);
        return $result[0] ?? false;
    }

    public function isParticipant(int $roomId, int $userId): bool
    {
        $sql = "SELECT COUNT(*) FROM chat_room_participants WHERE room_id = ? AND user_id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$roomId, $userId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function addParticipant(int $roomId, int $userId, string $role = 'member'): bool
    {
        $existing = $this->query(
            "SELECT id FROM chat_room_participants WHERE room_id = ? AND user_id = ?",
            [$roomId, $userId]
        );

        if (!empty($existing)) {
            return true;
        }

        $participant = new ChatRoomParticipant();
        return (bool) $participant->insert([
            'room_id' => $roomId,
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    public function removeParticipant(int $roomId, int $userId): bool
    {
        $participant = new ChatRoomParticipant();
        $record = $participant->query(
            "SELECT id FROM chat_room_participants WHERE room_id = ? AND user_id = ?",
            [$roomId, $userId]
        );

        if (!empty($record)) {
            return $participant->delete($record[0]->id);
        }
        return false;
    }

    public function getParticipants(int $roomId): array|false
    {
        $sql = "SELECT rp.*, u.firstname, u.surname, u.image, u.user_id as uid
                FROM chat_room_participants rp
                JOIN users u ON rp.user_id = u.id
                WHERE rp.room_id = ?
                ORDER BY rp.joined_at ASC";
        return $this->query($sql, [$roomId]);
    }

    public function getUserRooms(int $userId): array|false
    {
        return $this->query("
            SELECT r.*, u.firstname, u.surname, u.image,
                (SELECT COUNT(*) FROM chat_messages m WHERE m.room_id = r.id AND m.conversation_id IS NULL) as message_count,
                (SELECT m.message FROM chat_messages m WHERE m.room_id = r.id AND m.conversation_id IS NULL ORDER BY m.date_sent DESC LIMIT 1) as last_message,
                (SELECT m.date_sent FROM chat_messages m WHERE m.room_id = r.id AND m.conversation_id IS NULL ORDER BY m.date_sent DESC LIMIT 1) as last_message_time,
                (SELECT m.user_id FROM chat_messages m WHERE m.room_id = r.id AND m.conversation_id IS NULL ORDER BY m.date_sent DESC LIMIT 1) as last_message_sender,
                (SELECT rp2.last_read_message_id FROM chat_room_participants rp2 WHERE rp2.room_id = r.id AND rp2.user_id = ?) as my_last_read,
                (SELECT COUNT(*) FROM chat_messages cm WHERE cm.room_id = r.id AND cm.conversation_id IS NULL AND cm.user_id != ? AND cm.is_read = 0) as unread_count
            FROM chat_rooms r
            JOIN users u ON r.created_by = u.id
            JOIN chat_room_participants rp ON rp.room_id = r.id AND rp.user_id = ?
            WHERE r.is_active = 1
            ORDER BY last_message_time DESC
        ", [$userId, $userId, $userId]);
    }

    public function searchRooms(string $query): array|false
    {
        return $this->query("
            SELECT r.*, u.firstname, u.surname, u.image
            FROM chat_rooms r
            JOIN users u ON r.created_by = u.id
            WHERE r.is_active = 1 
            AND (r.room_name LIKE ? OR r.description LIKE ?)
            ORDER BY r.room_name ASC
            LIMIT 20
        ", ["%$query%", "%$query%"]);
    }
}
