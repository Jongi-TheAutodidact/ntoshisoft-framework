<?php

/**
 * ChatConversations Migration - Private DM support between two users
 */

defined('ROOTPATH') or exit('Access Denied!');

class ChatConversations extends Migration
{
    public function alpha()
    {
        $this->addColumn('id int(11) UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->addColumn('user_one_id int(11) UNSIGNED NOT NULL');
        $this->addColumn('user_two_id int(11) UNSIGNED NOT NULL');
        $this->addColumn('last_message_id int(11) UNSIGNED DEFAULT NULL');
        $this->addColumn('last_message_at datetime DEFAULT NULL');
        $this->addColumn('is_active tinyint(1) DEFAULT 1');
        $this->addColumn('date_created datetime DEFAULT current_timestamp()');
        $this->addColumn('date_updated datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()');

        $this->addPrimaryKey('id');
        $this->addKey('user_one_id');
        $this->addKey('user_two_id');
        $this->addKey('last_message_at');

        $this->createTable('chat_conversations');

        // Add composite unique index via raw query
        $this->query("ALTER TABLE chat_conversations ADD UNIQUE INDEX idx_unique_conv (user_one_id, user_two_id)");
    }

    public function omega()
    {
        $this->dropTable('chat_conversations');
    }
}
