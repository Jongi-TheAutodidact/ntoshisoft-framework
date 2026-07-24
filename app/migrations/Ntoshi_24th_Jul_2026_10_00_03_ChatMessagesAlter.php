<?php

/**
 * ChatMessages Alter Migration - Add message_type, media_url, conversation_id
 */

defined('ROOTPATH') or exit('Access Denied!');

class ChatMessagesAlter extends Migration
{
    public function alpha()
    {
        // Add message_type column (text, voice, image, file)
        $this->query("ALTER TABLE chat_messages ADD COLUMN message_type varchar(20) DEFAULT 'text' AFTER message");

        // Add media_url column for voice/image/file messages
        $this->query("ALTER TABLE chat_messages ADD COLUMN media_url varchar(1024) DEFAULT NULL AFTER message_type");

        // Add conversation_id for private DMs (NULL = group message)
        $this->query("ALTER TABLE chat_messages ADD COLUMN conversation_id int(11) UNSIGNED DEFAULT NULL AFTER room_id");

        // Add indexes
        $this->query("ALTER TABLE chat_messages ADD INDEX idx_conversation_id (conversation_id)");
        $this->query("ALTER TABLE chat_messages ADD INDEX idx_message_type (message_type)");
    }

    public function omega()
    {
        $this->query("ALTER TABLE chat_messages DROP COLUMN message_type");
        $this->query("ALTER TABLE chat_messages DROP COLUMN media_url");
        $this->query("ALTER TABLE chat_messages DROP COLUMN conversation_id");
        $this->query("ALTER TABLE chat_messages DROP INDEX idx_conversation_id");
        $this->query("ALTER TABLE chat_messages DROP INDEX idx_message_type");
    }
}
