<?php

defined('ROOTPATH') or exit('Access Denied!');

class ChatController
{
    use Controller;

    private ChatRoom $chatRoom;
    private ChatMessage $chatMessage;
    private ChatConversation $chatConversation;
    private ChatRoomParticipant $participant;
    private User $user;

    public function __construct()
    {
        $this->user = new User();
        if (!$this->user->logged_in()) {
            redirect('auth/login');
        }

        $this->chatRoom = new ChatRoom();
        $this->chatMessage = new ChatMessage();
        $this->chatConversation = new ChatConversation();
        $this->participant = new ChatRoomParticipant();
    }

    public function index(): void
    {
        $userId = user('id');

        $data['rooms'] = $this->chatRoom->getUserRooms($userId);
        $data['conversations'] = $this->chatConversation->getUserConversations($userId);
        $data['allUsers'] = $this->getAllOtherUsers();
        $data['activeTab'] = 'rooms';
        $data['page_title'] = 'Chat';

        $this->view('admin/chat/index', $data);
    }

    public function room(?string $roomId = null): void
    {
        if (!$roomId) {
            redirect('admin/chat');
        }

        $userId = user('id');
        $room = $this->chatRoom->getRoom((int) $roomId);

        if (!$room) {
            redirect('admin/chat');
        }

        $data['room'] = $room;
        $data['messages'] = $this->chatMessage->getRoomMessages((int) $roomId);
        $data['participants'] = $this->chatRoom->getParticipants((int) $roomId);
        $data['allUsers'] = $this->getAllOtherUsers();
        $data['rooms'] = $this->chatRoom->getUserRooms($userId);
        $data['conversations'] = $this->chatConversation->getUserConversations($userId);
        $data['current_user_id'] = $userId;
        $data['chatType'] = 'room';
        $data['page_title'] = $room->room_name ?? 'Chat Room';

        $this->chatMessage->markRoomAsRead((int) $roomId, $userId);

        $this->view('admin/chat/room', $data);
    }

    public function conversation(?string $conversationId = null): void
    {
        if (!$conversationId) {
            redirect('admin/chat');
        }

        $userId = user('id');
        $conv = $this->chatConversation->getConversation((int) $conversationId);

        if (!$conv || ($conv->user_one_id != $userId && $conv->user_two_id != $userId)) {
            redirect('admin/chat');
        }

        $otherUserId = $this->chatConversation->getOtherUserId((int) $conversationId, $userId);
        $otherUser = $this->user->first(['id' => $otherUserId]);

        $data['conversation'] = $conv;
        $data['otherUser'] = $otherUser;
        $data['messages'] = $this->chatMessage->getDirectMessages((int) $conversationId);
        $data['rooms'] = $this->chatRoom->getUserRooms($userId);
        $data['conversations'] = $this->chatConversation->getUserConversations($userId);
        $data['allUsers'] = $this->getAllOtherUsers();
        $data['current_user_id'] = $userId;
        $data['chatType'] = 'conversation';
        $data['page_title'] = $otherUser ? $otherUser->firstname . ' ' . $otherUser->surname : 'Chat';

        $this->chatMessage->markConversationAsRead((int) $conversationId, $userId);

        $this->view('admin/chat/room', $data);
    }

    public function startConversation(?string $userId = null): void
    {
        if (!$userId) {
            redirect('admin/chat');
        }

        $currentUserId = user('id');
        $otherUser = $this->user->first(['id' => (int) $userId]);

        if (!$otherUser || $currentUserId == (int) $userId) {
            redirect('admin/chat');
        }

        $convId = $this->chatConversation->getOrCreate($currentUserId, (int) $userId);

        if ($convId) {
            redirect('admin/chat/conversation/' . $convId);
        }

        redirect('admin/chat');
    }

    public function sendMessage(): never
    {
        $this->jsonResponse(['status' => 'error', 'message' => 'Invalid request'], 400);
    }

    public function apiSendMessage(): never
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Method not allowed'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid input'], 400);
        }

        $userId = user('id');
        $message = trim($input['message'] ?? '');
        $messageType = $input['message_type'] ?? 'text';
        $mediaUrl = $input['media_url'] ?? null;

        if (empty($message) && $messageType === 'text') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Message cannot be empty'], 400);
        }

        if (isset($input['room_id']) && !empty($input['room_id'])) {
            $roomId = (int) $input['room_id'];

            if (!$this->chatRoom->isParticipant($roomId, $userId)) {
                $this->jsonResponse(['status' => 'error', 'message' => 'Not a participant'], 403);
            }

            $messageId = $this->chatMessage->sendMessage($roomId, $userId, $message, $messageType, $mediaUrl);

            if ($messageId) {
                $msgData = $this->chatMessage->query(
                    "SELECT m.*, u.firstname, u.surname, u.image FROM chat_messages m JOIN users u ON m.user_id = u.id WHERE m.id = ?",
                    [$messageId]
                );

                $this->jsonResponse([
                    'status' => 'success',
                    'message_id' => $messageId,
                    'message' => $msgData ? $msgData[0] : null,
                ]);
            }
        } elseif (isset($input['conversation_id']) && !empty($input['conversation_id'])) {
            $convId = (int) $input['conversation_id'];
            $conv = $this->chatConversation->getConversation($convId);

            if (!$conv || ($conv->user_one_id != $userId && $conv->user_two_id != $userId)) {
                $this->jsonResponse(['status' => 'error', 'message' => 'Invalid conversation'], 403);
            }

            $messageId = $this->chatMessage->sendDirectMessage($convId, $userId, $message, $messageType, $mediaUrl);

            if ($messageId) {
                $this->chatConversation->updateLastMessage($convId, $messageId);

                $msgData = $this->chatMessage->query(
                    "SELECT m.*, u.firstname, u.surname, u.image FROM chat_messages m JOIN users u ON m.user_id = u.id WHERE m.id = ?",
                    [$messageId]
                );

                $this->jsonResponse([
                    'status' => 'success',
                    'message_id' => $messageId,
                    'message' => $msgData ? $msgData[0] : null,
                ]);
            }
        }

        $this->jsonResponse(['status' => 'error', 'message' => 'Failed to send message'], 500);
    }

    public function getMessages(): void
    {
        header('Content-Type: application/json');

        if (isset($_GET['room_id'])) {
            $roomId = (int) $_GET['room_id'];
            $lastId = (int) ($_GET['last_id'] ?? 0);
            $messages = $this->chatMessage->getNewMessages($roomId, $lastId);
            echo json_encode($messages ?: []);
            exit;
        }

        if (isset($_GET['conversation_id'])) {
            $convId = (int) $_GET['conversation_id'];
            $lastId = (int) ($_GET['last_id'] ?? 0);
            $messages = $this->chatMessage->getNewDirectMessages($convId, $lastId);
            echo json_encode($messages ?: []);
            exit;
        }

        echo json_encode([]);
    }

    public function uploadVoice(): never
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error'], 405);
        }

        if (empty($_FILES['voice'])) {
            $this->jsonResponse(['status' => 'error', 'message' => 'No audio file provided'], 400);
        }

        $file = $_FILES['voice'];
        $allowedTypes = ['audio/webm', 'audio/ogg', 'audio/mpeg', 'audio/wav', 'audio/mp4'];
        $allowedExts = ['webm', 'ogg', 'mp3', 'wav', 'm4a', 'mp4'];

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid audio format'], 400);
        }

        $targetDir = ROOTPATH . 'uploads/voice/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filename = 'voice_' . user('id') . '_' . time() . '.' . $ext;
        $targetPath = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $relativePath = 'uploads/voice/' . $filename;
            $this->jsonResponse([
                'status' => 'success',
                'url' => ROOT . '/' . $relativePath,
                'path' => $relativePath,
            ]);
        }

        $this->jsonResponse(['status' => 'error', 'message' => 'Upload failed'], 500);
    }

    public function uploadImage(): never
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error'], 405);
        }

        if (empty($_FILES['image'])) {
            $this->jsonResponse(['status' => 'error', 'message' => 'No image file provided'], 400);
        }

        $file = $_FILES['image'];
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid image format'], 400);
        }

        $targetDir = ROOTPATH . 'uploads/chat/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filename = 'img_' . user('id') . '_' . time() . '.' . $ext;
        $targetPath = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $relativePath = 'uploads/chat/' . $filename;
            $this->jsonResponse([
                'status' => 'success',
                'url' => ROOT . '/' . $relativePath,
                'path' => $relativePath,
            ]);
        }

        $this->jsonResponse(['status' => 'error', 'message' => 'Upload failed'], 500);
    }

    public function createRoom(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/chat');
        }

        $userId = user('id');
        $roomName = trim($_POST['room_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $roomType = $_POST['room_type'] ?? 'group';
        $selectedUsers = $_POST['participants'] ?? [];

        if (empty($roomName)) {
            redirect('admin/chat');
        }

        $roomId = $this->chatRoom->createRoom($userId, $roomName, $roomType, $description);

        if ($roomId && !empty($selectedUsers)) {
            foreach ($selectedUsers as $participantId) {
                $this->chatRoom->addParticipant((int) $roomId, (int) $participantId);
            }
        }

        redirect('admin/chat/room/' . $roomId);
    }

    public function joinRoom(?string $roomId = null): void
    {
        if (!$roomId) {
            redirect('admin/chat');
        }

        $userId = user('id');
        $this->chatRoom->addParticipant((int) $roomId, $userId);
        redirect('admin/chat/room/' . $roomId);
    }

    public function leaveRoom(?string $roomId = null): void
    {
        if (!$roomId) {
            redirect('admin/chat');
        }

        $userId = user('id');
        $this->chatRoom->removeParticipant((int) $roomId, $userId);
        redirect('admin/chat');
    }

    public function addParticipant(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/chat');
        }

        $roomId = (int) ($_POST['room_id'] ?? 0);
        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($roomId && $userId) {
            $this->chatRoom->addParticipant($roomId, $userId);
        }

        redirect('admin/chat/room/' . $roomId);
    }

    public function removeParticipant(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/chat');
        }

        $roomId = (int) ($_POST['room_id'] ?? 0);
        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($roomId && $userId) {
            $this->chatRoom->removeParticipant($roomId, $userId);
        }

        redirect('admin/chat/room/' . $roomId);
    }

    public function markRead(): never
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $userId = user('id');

        if (isset($input['conversation_id'])) {
            $this->chatMessage->markConversationAsRead((int) $input['conversation_id'], $userId);
        } elseif (isset($input['room_id'])) {
            $this->chatMessage->markRoomAsRead((int) $input['room_id'], $userId);
        }

        $this->jsonResponse(['status' => 'success']);
    }

    public function typing(): never
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $userId = user('id');
        $isTyping = $input['is_typing'] ?? false;
        $isRecording = $input['is_recording'] ?? false;

        $userData = $this->user->first(['id' => $userId]);

        $payload = [
            'user_id' => $userId,
            'firstname' => $userData->firstname ?? '',
            'is_typing' => $isTyping,
            'is_recording' => $isRecording,
        ];

        if (isset($input['room_id'])) {
            $payload['room_id'] = (int) $input['room_id'];
        } elseif (isset($input['conversation_id'])) {
            $payload['conversation_id'] = (int) $input['conversation_id'];
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'typing' => $payload]);
        exit;
    }

    public function getTyping(): void
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'typing_users' => []]);
        exit;
    }

    public function getContacts(): void
    {
        header('Content-Type: application/json');
        $users = $this->getAllOtherUsers();
        echo json_encode($users ?: []);
    }

    public function getOnlineUsers(): void
    {
        header('Content-Type: application/json');
        $onlineModel = new OnlineUser();
        $online = $onlineModel->getOnlineUsers();
        echo json_encode($online ?: []);
    }

    public function search(): void
    {
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';

        if (strlen($query) < 2) {
            echo json_encode([]);
            return;
        }

        $roomResults = $this->chatRoom->searchRooms($query);

        $userResults = $this->user->query(
            "SELECT id, firstname, surname, image, user_id FROM users 
             WHERE (firstname LIKE ? OR surname LIKE ? OR email LIKE ?) 
             AND id != ? LIMIT 10",
            ["%$query%", "%$query%", "%$query%", user('id')]
        );

        echo json_encode([
            'rooms' => $roomResults ?: [],
            'users' => $userResults ?: [],
        ]);
    }

    public function deleteMessage(): never
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $messageId = (int) ($input['message_id'] ?? 0);

        if ($messageId) {
            $this->chatMessage->softDelete($messageId);
        }

        $this->jsonResponse(['status' => 'success']);
    }

    private function getAllOtherUsers(): array
    {
        $users = $this->user->query(
            "SELECT id, firstname, surname, image, user_id, user_role FROM users WHERE id != ? ORDER BY firstname ASC",
            [user('id')]
        );
        return $users ?: [];
    }

    private function jsonResponse(mixed $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
