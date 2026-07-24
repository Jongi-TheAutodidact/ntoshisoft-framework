<?php

/**
 * ChatRooms Alter Migration - Add room_type, description, avatar
 */

defined('ROOTPATH') or exit('Access Denied!');

class ChatRoomsAlter extends Migration
{
    public function alpha()
    {
        // Add room_type column (group, channel, direct)
        $this->query("ALTER TABLE chat_rooms ADD COLUMN room_type varchar(20) DEFAULT 'group' AFTER room_name");

        // Add description column
        $this->query("ALTER TABLE chat_rooms ADD COLUMN description varchar(255) DEFAULT NULL AFTER room_type");

        // Add avatar column
        $this->query("ALTER TABLE chat_rooms ADD COLUMN avatar varchar(1024) DEFAULT NULL AFTER description");

        // Add indexes
        $this->query("ALTER TABLE chat_rooms ADD INDEX idx_room_type (room_type)");
    }

    public function omega()
    {
        $this->query("ALTER TABLE chat_rooms DROP COLUMN room_type");
        $this->query("ALTER TABLE chat_rooms DROP COLUMN description");
        $this->query("ALTER TABLE chat_rooms DROP COLUMN avatar");
        $this->query("ALTER TABLE chat_rooms DROP INDEX idx_room_type");
    }
}
