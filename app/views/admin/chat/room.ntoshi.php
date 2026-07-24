<?php
/**
 * Chat Room / Conversation View - WhatsApp-Style Interface
 * @var object|null $room
 * @var object|null $conversation
 * @var object|null $otherUser
 * @var array|false $messages
 * @var array $participants
 * @var array $rooms
 * @var array $data
 * @var array $conversations
 * @var array $allUsers
 * @var int|string $current_user_id
 * @var string $chatType ('room' or 'conversation')
 */
$this->view('inc/header', $data);

$chatName = '';
$chatAvatar = '';
$chatStatus = '';
$isGroup = ($chatType === 'room');

if ($isGroup && !empty($room)) {
    $chatName = $room->room_name;
    $chatAvatar = get_image($room->avatar ?? $room->image, 'user');
    $participantCount = is_array($participants) ? count($participants) : 0;
    $chatStatus = $participantCount . ' participants';
} elseif (!$isGroup && !empty($otherUser)) {
    $chatName = $otherUser->firstname . ' ' . $otherUser->surname;
    $chatAvatar = get_image($otherUser->image, 'user');
    $chatStatus = 'online';
}

$chatId = $isGroup ? ($room->id ?? 0) : ($conversation->id ?? 0);
$chatIdParam = $isGroup ? 'room_id' : 'conversation_id';
?>

<link rel="stylesheet" href="<?= ROOT ?>/assets/css/chat.css">

<div class="container-fluid px-3 mt-3">
    <div class="chat-app-wrapper">

        <!-- SIDEBAR -->
        <div class="chat-sidebar" id="chatSidebar">
            <div class="chat-sidebar-header">
                <img src="<?= get_image(user('image'), 'user') ?>" class="user-avatar" alt="">
                <div class="header-actions">
                    <button title="Back to Chat List" onclick="window.location='<?= ROOT ?>/admin/chat'">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button title="New Group" onclick="document.getElementById('createRoomModal').classList.add('show')">
                        <i class="fas fa-users"></i>
                    </button>
                    <button title="New Chat" onclick="document.getElementById('newChatModal').classList.add('show')">
                        <i class="fas fa-comment-dots"></i>
                    </button>
                </div>
            </div>

            <div class="chat-search">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="chatSearch" placeholder="Search or start new chat" oninput="searchChats(this.value)">
                </div>
            </div>

            <div class="chat-tabs">
                <div class="chat-tab" data-tab="rooms" onclick="switchTab('rooms')">
                    <i class="fas fa-users"></i> Groups
                </div>
                <div class="chat-tab active" data-tab="conversations" onclick="switchTab('conversations')">
                    <i class="fas fa-comments"></i> Chats
                </div>
            </div>

            <!-- Group Rooms List -->
            <div class="chat-conversation-list" id="roomsList" style="display:none">
                <?php if (!empty($rooms)): ?>
                    <?php foreach ($rooms as $rm): ?>
                        <a href="<?= ROOT ?>/admin/chat/room/<?= $rm->id ?>" class="chat-conversation-item text-decoration-none <?= ($isGroup && ($room->id ?? 0) == $rm->id) ? 'active' : '' ?>">
                            <img src="<?= get_image($rm->avatar ?? $rm->image, 'user') ?>" class="conv-avatar group-avatar" alt="">
                            <div class="conv-info">
                                <div class="conv-top">
                                    <span class="conv-name"><?= esc($rm->room_name) ?></span>
                                    <span class="conv-time"><?= !empty($rm->last_message_time) ? date('H:i', strtotime($rm->last_message_time)) : '' ?></span>
                                </div>
                                <div class="conv-bottom">
                                    <span class="conv-preview">
                                        <?php if (!empty($rm->last_message)): ?>
                                            <?= $rm->last_message_sender == user('id') ? 'You: ' : '' ?>
                                            <?= mb_strimwidth(esc($rm->last_message), 0, 40, '...') ?>
                                        <?php else: ?>
                                            <?= esc($rm->description ?? 'No messages') ?>
                                        <?php endif; ?>
                                    </span>
                                    <?php if (($rm->unread_count ?? 0) > 0): ?>
                                        <span class="unread-badge"><?= $rm->unread_count ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Conversations List -->
            <div class="chat-conversation-list" id="conversationsList">
                <?php if (!empty($conversations)): ?>
                    <?php foreach ($conversations as $conv): ?>
                        <a href="<?= ROOT ?>/admin/chat/conversation/<?= $conv->id ?>" class="chat-conversation-item text-decoration-none <?= (!$isGroup && ($conversation->id ?? 0) == $conv->id) ? 'active' : '' ?>">
                            <img src="<?= get_image($conv->other_image ?? '', 'user') ?>" class="conv-avatar" alt="">
                            <div class="conv-info">
                                <div class="conv-top">
                                    <span class="conv-name"><?= esc($conv->other_firstname . ' ' . $conv->other_surname) ?></span>
                                    <span class="conv-time"><?= !empty($conv->last_message_time) ? date('H:i', strtotime($conv->last_message_time)) : '' ?></span>
                                </div>
                                <div class="conv-bottom">
                                    <span class="conv-preview">
                                        <?php if (!empty($conv->last_message)): ?>
                                            <?= $conv->last_message_sender == user('id') ? 'You: ' : '' ?>
                                            <?= mb_strimwidth(esc($conv->last_message), 0, 40, '...') ?>
                                        <?php else: ?>
                                            Start a conversation
                                        <?php endif; ?>
                                    </span>
                                    <?php if (($conv->unread_count ?? 0) > 0): ?>
                                        <span class="unread-badge"><?= $conv->unread_count ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- MAIN CHAT AREA -->
        <div class="chat-main" id="chatMain">
            <div class="chat-main-bg"></div>

            <!-- Chat Header -->
            <div class="chat-header">
                <img src="<?= $chatAvatar ?>" class="chat-header-avatar <?= $isGroup ? 'group-avatar' : '' ?>" alt="">
                <div class="chat-header-info">
                    <div class="chat-header-name"><?= esc($chatName) ?></div>
                    <div class="chat-header-status" id="chatStatus"><?= esc($chatStatus) ?></div>
                </div>
                <div class="chat-header-actions">
                    <?php if ($isGroup): ?>
                        <button title="Group Info" onclick="toggleGroupInfo()">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    <?php endif; ?>
                    <button title="Search Messages" onclick="toggleMsgSearch()">
                        <i class="fas fa-search"></i>
                    </button>
                    <button title="More Options">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>

            <!-- Messages -->
            <div class="chat-messages" id="chatMessages">
                <?php
                $lastDate = '';
                if (!empty($messages)):
                    foreach ($messages as $msg):
                        $msgDate = date('d M Y', strtotime($msg->date_sent));
                        if ($msgDate !== $lastDate):
                            $lastDate = $msgDate;
                            $today = date('d M Y');
                            $yesterday = date('d M Y', strtotime('-1 day'));
                            $dateLabel = $msgDate === $today ? 'Today' : ($msgDate === $yesterday ? 'Yesterday' : $msgDate);
                            ?>
                            <div class="chat-date-divider"><span><?= $dateLabel ?></span></div>
                        <?php endif;

                        $isSent = ($msg->user_id == $current_user_id);
                        $msgType = $msg->message_type ?? 'text';
                        ?>
                        <div class="chat-message <?= $isSent ? 'sent' : 'received' ?>" data-id="<?= $msg->id ?>">
                            <div class="msg-bubble">
                                <?php if (!$isSent && $isGroup): ?>
                                    <div class="msg-sender"><?= esc($msg->firstname) ?></div>
                                <?php endif; ?>

                                <?php if ($msgType === 'voice' && !empty($msg->media_url)): ?>
                                    <div class="voice-msg">
                                        <button class="voice-play-btn" onclick="playVoice(this, '<?= ROOT ?>/<?= $msg->media_url ?>')">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <div class="voice-waveform" id="waveform-<?= $msg->id ?>">
                                            <?php for ($i = 0; $i < 25; $i++): ?>
                                                <div class="bar" style="height:<?= rand(4, 22) ?>px"></div>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="voice-duration">0:00</span>
                                    </div>
                                <?php elseif ($msgType === 'image' && !empty($msg->media_url)): ?>
                                    <div class="msg-image">
                                        <img src="<?= ROOT ?>/<?= $msg->media_url ?>" alt="Photo" loading="lazy">
                                    </div>
                                    <?php if (!empty($msg->message) && $msg->message !== '[image]'): ?>
                                        <div class="msg-text"><?= nl2br(esc($msg->message)) ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="msg-text"><?= nl2br(esc($msg->message)) ?></div>
                                <?php endif; ?>

                                <div class="msg-footer">
                                    <span class="msg-time"><?= date('H:i', strtotime($msg->date_sent)) ?></span>
                                    <?php if ($isSent): ?>
                                        <span class="msg-ticks <?= $msg->is_read ? 'read' : ($msg->is_delivered ? 'delivered' : 'sent-tick') ?>">
                                            <?php if ($msg->is_read): ?>
                                                <i class="fas fa-check-double"></i>
                                            <?php elseif ($msg->is_delivered): ?>
                                                <i class="fas fa-check-double"></i>
                                            <?php else: ?>
                                                <i class="fas fa-check"></i>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Typing Indicator -->
            <div class="typing-indicator" id="typingIndicator" style="display:none">
                <div class="typing-dots">
                    <span></span><span></span><span></span>
                </div>
                <span id="typingText"></span>
            </div>

            <!-- Emoji Picker -->
            <div class="emoji-picker" id="emojiPicker">
                <div class="emoji-picker-header">
                    <button class="emoji-category-btn active" data-category="smileys">😀</button>
                    <button class="emoji-category-btn" data-category="gestures">👋</button>
                    <button class="emoji-category-btn" data-category="people">👨</button>
                    <button class="emoji-category-btn" data-category="animals">🐶</button>
                    <button class="emoji-category-btn" data-category="food">🍕</button>
                    <button class="emoji-category-btn" data-category="travel">✈️</button>
                    <button class="emoji-category-btn" data-category="objects">💡</button>
                    <button class="emoji-category-btn" data-category="symbols">❤️</button>
                    <button class="emoji-category-btn" data-category="flags">🏳️</button>
                </div>
                <div class="emoji-grid" id="emojiGrid"></div>
            </div>

            <!-- Input Area -->
            <div class="chat-input-area">
                <button class="emoji-btn" id="emojiBtn" title="Emoji" onclick="toggleEmojiPicker()">
                    <i class="fas fa-smile"></i>
                </button>
                <button class="attach-btn" title="Attach file" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="file" id="fileInput" style="display:none" accept="image/*" onchange="handleFileSelect(event)">
                <div class="chat-input-wrapper">
                    <textarea id="messageInput" rows="1" placeholder="Type a message" oninput="autoResize(this);handleTyping()" onkeydown="handleInputKeydown(event)"></textarea>
                </div>
                <button class="voice-btn" id="voiceBtn" title="Record voice message" onclick="toggleRecording()">
                    <i class="fas fa-microphone"></i>
                </button>
                <button class="send-btn" id="sendBtn" title="Send" onclick="sendChatMessage()" style="display:none">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Hidden inputs for metadata -->
<input type="hidden" id="chatType" value="<?= $chatType ?>">
<input type="hidden" id="chatId" value="<?= $chatId ?>">
<input type="hidden" id="userId" value="<?= $current_user_id ?>">
<input type="hidden" id="userName" value="<?= user('firstname') ?>">
<input type="hidden" id="userSurname" value="<?= user('surname') ?>">
<input type="hidden" id="userImage" value="<?= ROOT ?>/<?= user('image') ?>">

<!-- New Chat Modal -->
<div class="modal-overlay" id="newChatModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center">
    <div style="background:var(--chat-header-bg);border:1px solid var(--chat-border);border-radius:12px;width:420px;max-height:80vh;overflow:hidden;display:flex;flex-direction:column">
        <div style="padding:16px;border-bottom:1px solid var(--chat-border);display:flex;align-items:center;justify-content:space-between">
            <h5 class="mb-0" style="color:var(--chat-text);font-size:1rem">New Chat</h5>
            <button onclick="document.getElementById('newChatModal').style.display='none'" style="background:none;border:none;color:var(--chat-text-secondary);font-size:1.2rem;cursor:pointer"><i class="fas fa-times"></i></button>
        </div>
        <div style="padding:8px 12px;border-bottom:1px solid var(--chat-border)">
            <input type="text" placeholder="Search contacts..." style="width:100%;background:var(--chat-search-bg);border:none;border-radius:8px;padding:10px 12px;color:var(--chat-text);outline:none;font-size:.9rem" id="contactSearch" oninput="filterContacts(this.value)">
        </div>
        <div style="overflow-y:auto;flex:1;max-height:60vh" id="contactsList">
            <?php if (!empty($allUsers)): ?>
                <?php foreach ($allUsers as $u): ?>
                    <a href="<?= ROOT ?>/admin/chat/start/<?= $u->id ?>" class="chat-conversation-item text-decoration-none contact-item" data-name="<?= strtolower($u->firstname . ' ' . $u->surname) ?>">
                        <img src="<?= get_image($u->image ?? '', 'user') ?>" class="conv-avatar" alt="">
                        <div class="conv-info">
                            <span class="conv-name"><?= esc($u->firstname . ' ' . $u->surname) ?></span>
                            <span class="conv-preview" style="display:block;font-size:.8rem"><?= esc($u->user_role ?? '') ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Room Modal -->
<div class="modal-overlay" id="createRoomModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center">
    <div style="background:var(--chat-header-bg);border:1px solid var(--chat-border);border-radius:12px;width:480px;max-height:85vh;overflow:hidden;display:flex;flex-direction:column">
        <div style="padding:16px;border-bottom:1px solid var(--chat-border);display:flex;align-items:center;justify-content:space-between">
            <h5 class="mb-0" style="color:var(--chat-text);font-size:1rem">Create Group Chat</h5>
            <button onclick="document.getElementById('createRoomModal').style.display='none'" style="background:none;border:none;color:var(--chat-text-secondary);font-size:1.2rem;cursor:pointer"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="<?= ROOT ?>/admin/chat/create-room" style="overflow-y:auto;flex:1">
            <div style="padding:16px">
                <div class="mb-3">
                    <label style="color:var(--chat-text-secondary);font-size:.85rem;margin-bottom:6px;display:block">Group Name</label>
                    <input type="text" name="room_name" required placeholder="Enter group name" style="width:100%;background:var(--chat-input-bg);border:none;border-radius:8px;padding:10px 12px;color:var(--chat-text);outline:none;font-size:.9rem">
                </div>
                <div class="mb-3">
                    <label style="color:var(--chat-text-secondary);font-size:.85rem;margin-bottom:6px;display:block">Description (optional)</label>
                    <input type="text" name="description" placeholder="What's this group about?" style="width:100%;background:var(--chat-input-bg);border:none;border-radius:8px;padding:10px 12px;color:var(--chat-text);outline:none;font-size:.9rem">
                </div>
                <input type="hidden" name="room_type" value="group">
                <div class="mb-3">
                    <label style="color:var(--chat-text-secondary);font-size:.85rem;margin-bottom:6px;display:block">Add Members</label>
                    <div style="max-height:200px;overflow-y:auto">
                        <?php if (!empty($allUsers)): ?>
                            <?php foreach ($allUsers as $u): ?>
                                <label style="display:flex;align-items:center;gap:10px;padding:8px;cursor:pointer;border-radius:8px;color:var(--chat-text)">
                                    <input type="checkbox" name="participants[]" value="<?= $u->id ?>" style="accent-color:var(--chat-unread)">
                                    <img src="<?= get_image($u->image ?? '', 'user') ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover" alt="">
                                    <span style="font-size:.9rem"><?= esc($u->firstname . ' ' . $u->surname) ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div style="padding:12px 16px;border-top:1px solid var(--chat-border);display:flex;justify-content:flex-end;gap:8px">
                <button type="button" onclick="document.getElementById('createRoomModal').style.display='none'" style="background:var(--chat-input-bg);border:none;color:var(--chat-text);padding:8px 16px;border-radius:8px;cursor:pointer;font-size:.85rem">Cancel</button>
                <button type="submit" style="background:var(--chat-unread);border:none;color:#111b21;padding:8px 20px;border-radius:8px;cursor:pointer;font-size:.85rem;font-weight:500">Create Group</button>
            </div>
        </form>
    </div>
</div>

<script src="<?= ROOT ?>/assets/js/chat.js"></script>

<script>
// Pass PHP data to JS
const CHAT_CONFIG = {
    type: '<?= $chatType ?>',
    id: <?= $chatId ?>,
    userId: <?= $current_user_id ?>,
    sendUrl: '<?= ROOT ?>/admin/chat/send',
    messagesUrl: '<?= ROOT ?>/admin/chat/messages',
    voiceUrl: '<?= ROOT ?>/admin/chat/upload-voice',
    markReadUrl: '<?= ROOT ?>/admin/chat/mark-read',
    typingUrl: '<?= ROOT ?>/admin/chat/typing',
    root: '<?= ROOT ?>',
    pollingInterval: 2000,
};

initChat(CHAT_CONFIG);
</script>

<script>
function switchTab(tab) {
    document.querySelectorAll('.chat-tab').forEach(t => t.classList.remove('active'));
    document.querySelector(`.chat-tab[data-tab="${tab}"]`).classList.add('active');
    document.getElementById('roomsList').style.display = tab === 'rooms' ? '' : 'none';
    document.getElementById('conversationsList').style.display = tab === 'conversations' ? '' : 'none';
}

function searchChats(query) {
    const items = document.querySelectorAll('.chat-conversation-item');
    const q = query.toLowerCase();
    items.forEach(item => {
        const name = item.querySelector('.conv-name')?.textContent.toLowerCase() || '';
        item.style.display = name.includes(q) ? '' : 'none';
    });
}

function filterContacts(query) {
    const items = document.querySelectorAll('.contact-item');
    const q = query.toLowerCase();
    items.forEach(item => {
        const name = item.dataset.name || '';
        item.style.display = name.includes(q) ? '' : 'none';
    });
}

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});

// Scroll to bottom on load
const chatMessages = document.getElementById('chatMessages');
if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
</script>

<?php $this->view('inc/footer', $data) ?>
