<?php

defined('ROOTPATH') OR exit('Access Denied!');

class ChatRoom
{
    use Model;

    protected $table = 'chat_rooms';
    protected $primaryKey = 'id';

    protected $allowedColumns = [
        'room_name',
        'created_by',
        'is_active'
    ];

	public function getActiveRooms(): array|false
    {
        $sql = "SELECT r.*, u.firstname, u.surname, u.image
                FROM chat_rooms r
                JOIN users u ON r.created_by = u.id
                WHERE r.is_active = 1
                ORDER BY r.date_created DESC";
        return $this->query($sql);
    }

	public function createRoom(int $userId, string $roomName): false
    {
        $data = [
            'room_name' => $roomName,
            'created_by' => $userId,
            'is_active' => 1
        ];
        return $this->insert($data);
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
}