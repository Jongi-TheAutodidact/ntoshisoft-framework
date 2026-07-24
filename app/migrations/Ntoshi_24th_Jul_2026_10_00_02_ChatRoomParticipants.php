<?php

/**
 * ChatRoomParticipants Migration - Track who belongs to which group chat
 */

defined('ROOTPATH') or exit('Access Denied!');

class ChatRoomParticipants extends Migration
{
    public function alpha()
    {
        $this->addColumn('id int(11) UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->addColumn('room_id int(11) UNSIGNED NOT NULL');
        $this->addColumn('user_id int(11) UNSIGNED NOT NULL');
        $this->addColumn('role varchar(20) DEFAULT "member"');
        $this->addColumn('is_muted tinyint(1) DEFAULT 0');
        $this->addColumn('last_read_message_id int(11) UNSIGNED DEFAULT 0');
        $this->addColumn('joined_at datetime DEFAULT current_timestamp()');

        $this->addPrimaryKey('id');
        $this->addKey('room_id');
        $this->addKey('user_id');

        $this->createTable('chat_room_participants');
    }

    public function omega()
    {
        $this->dropTable('chat_room_participants');
    }
}
