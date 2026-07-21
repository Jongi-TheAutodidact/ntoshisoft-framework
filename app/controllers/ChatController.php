<?php

defined('ROOTPATH') or exit('Access Denied!');

class ChatController
{
    use Controller;

    public function __construct()
    {
        $user = new User();
        if (!$user->logged_in()) {
            redirect('login');
        }
    }

    public function index(): void
    {
        $chatRoom = new ChatRoom();
        $data['rooms'] = $chatRoom->getActiveRooms();
        $data['page_title'] = 'Chat Rooms';

        $this->view('admin/chat/rooms', $data);
    }

    public function room(?string $roomId = null): void
    {
        $roomId = $roomId ?? 1; // Default to first room
        $chatRoom = new ChatRoom();
        $chatMessage = new ChatMessage();

        $data['room'] = $chatRoom->getRoom($roomId);
        $data['messages'] = $chatMessage->getConversationMessages($roomId);
        $data['page_title'] = $data['room']->room_name ?? 'Chat Room';
        $data['current_user_id'] = user('id');

        // Mark messages as delivered
        $this->markDeliveredMessages($roomId);

        $this->view('admin/chat/room', $data);
    }

    private function markDeliveredMessages(string $roomId): void
    {
        $chatMessage = new ChatMessage();
        $unreadMessages = $chatMessage->getUnreadMessages(user('id'), $roomId);

        if (!empty($unreadMessages)) {
            foreach ($unreadMessages as $msg) {
                $chatMessage->markAsRead($msg->id); // Changed from markAsDelivered to markAsRead
            }
        }
    }

    public function sendMessage(): never
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chatMessage = new ChatMessage();
            $roomId = (int)$_POST['room_id']; // Cast to integer
            $message = trim($_POST['message']);

            if (!empty($message)) {
                // Use correct parameter order and names
                $chatMessage->sendMessage($roomId, user('id'), $message);
                echo json_encode(['status' => 'success']);
                exit;
            }
        }
        echo json_encode(['status' => 'error']);
        exit;
    }

    public function createRoom(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chatRoom = new ChatRoom();
            $roomName = trim($_POST['room_name']);

            if (!empty($roomName)) {
                $roomId = $chatRoom->createRoom(user('id'), $roomName);
                redirect('admin/chat');
            }
        }

        redirect('admin/chat');
    }

    public function getMessages(): void
    {
        $room_id = $_GET['room_id'] ?? null;
        $last_id = $_GET['last_id'] ?? 0;

        $chat = new ChatMessage();
        $messages = $chat->getNewMessages($room_id, $last_id);

        echo json_encode($messages);
    }
}
