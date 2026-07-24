<?php
/**
 * Chat - WhatsApp-Style Sidebar with Rooms & Conversations
 * @var array $rooms
 * @var array $conversations
 * @var array $allUsers
 * @var string $activeTab
 */
$this->view('inc/header', $data);
?>

<link rel="stylesheet" href="<?= ROOT ?>/assets/css/chat.css">

<div class="container-fluid px-3 mt-3">
    <div class="chat-app-wrapper">

        <!-- SIDEBAR -->
        <div class="chat-sidebar" id="chatSidebar">
            <div class="chat-sidebar-header">
                <img src="<?= get_image(user('image'), 'user') ?>" class="user-avatar" alt="">
                <div class="header-actions">
                    <button title="New Group" onclick="document.getElementById('createRoomModal').style.display='flex'">
                        <i class="fas fa-users"></i>
                    </button>
                    <button title="New Chat" onclick="document.getElementById('newChatModal').style.display='flex'">
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
                <div class="chat-tab <?= $activeTab === 'rooms' ? 'active' : '' ?>" data-tab="rooms" onclick="switchTab('rooms')">
                    <i class="fas fa-users"></i> Groups
                </div>
                <div class="chat-tab <?= $activeTab === 'conversations' ? 'active' : '' ?>" data-tab="conversations" onclick="switchTab('conversations')">
                    <i class="fas fa-comments"></i> Chats
                </div>
            </div>

            <!-- Group Rooms List -->
            <div class="chat-conversation-list" id="roomsList" style="<?= $activeTab !== 'rooms' ? 'display:none' : '' ?>">
                <?php if (!empty($rooms)): ?>
                    <?php foreach ($rooms as $room): ?>
                        <a href="<?= ROOT ?>/admin/chat/room/<?= $room->id ?>" class="chat-conversation-item text-decoration-none" data-id="room-<?= $room->id ?>">
                            <img src="<?= get_image($room->avatar ?? $room->image, 'user') ?>" class="conv-avatar group-avatar" alt="">
                            <div class="conv-info">
                                <div class="conv-top">
                                    <span class="conv-name"><?= esc($room->room_name) ?></span>
                                    <span class="conv-time <?= ($room->unread_count ?? 0) > 0 ? 'unread' : '' ?>">
                                        <?= !empty($room->last_message_time) ? date('H:i', strtotime($room->last_message_time)) : '' ?>
                                    </span>
                                </div>
                                <div class="conv-bottom">
                                    <span class="conv-preview">
                                        <?php if (!empty($room->last_message)): ?>
                                            <?= $room->last_message_sender == user('id') ? 'You: ' : '' ?>
                                            <?= mb_strimwidth(esc($room->last_message), 0, 45, '...') ?>
                                        <?php else: ?>
                                            <?= esc($room->description ?? 'No messages yet') ?>
                                        <?php endif; ?>
                                    </span>
                                    <?php if (($room->unread_count ?? 0) > 0): ?>
                                        <span class="unread-badge"><?= $room->unread_count ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5 px-3">
                        <i class="fas fa-users" style="font-size:2.5rem;color:var(--chat-text-secondary);opacity:.3"></i>
                        <p class="mt-2 mb-3" style="color:var(--chat-text-secondary);font-size:.85rem">No group chats yet</p>
                        <button onclick="document.getElementById('createRoomModal').style.display='flex'" style="background:var(--chat-unread);color:#111b21;border:none;border-radius:20px;padding:8px 20px;font-size:.85rem;cursor:pointer">
                            <i class="fas fa-plus me-1"></i>Create Group
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Conversations List -->
            <div class="chat-conversation-list" id="conversationsList" style="<?= $activeTab !== 'conversations' ? 'display:none' : '' ?>">
                <?php if (!empty($conversations)): ?>
                    <?php foreach ($conversations as $conv): ?>
                        <a href="<?= ROOT ?>/admin/chat/conversation/<?= $conv->id ?>" class="chat-conversation-item text-decoration-none" data-id="conv-<?= $conv->id ?>">
                            <img src="<?= get_image($conv->other_image ?? '', 'user') ?>" class="conv-avatar" alt="">
                            <div class="conv-info">
                                <div class="conv-top">
                                    <span class="conv-name"><?= esc($conv->other_firstname . ' ' . $conv->other_surname) ?></span>
                                    <span class="conv-time <?= ($conv->unread_count ?? 0) > 0 ? 'unread' : '' ?>">
                                        <?= !empty($conv->last_message_time) ? date('H:i', strtotime($conv->last_message_time)) : '' ?>
                                    </span>
                                </div>
                                <div class="conv-bottom">
                                    <span class="conv-preview">
                                        <?php if (!empty($conv->last_message)): ?>
                                            <?php if ($conv->last_message_type === 'voice'): ?>
                                                <i class="fas fa-microphone"></i> Voice message
                                            <?php elseif ($conv->last_message_type === 'image'): ?>
                                                <i class="fas fa-image"></i> Photo
                                            <?php else: ?>
                                                <?= $conv->last_message_sender == user('id') ? 'You: ' : '' ?>
                                                <?= mb_strimwidth(esc($conv->last_message), 0, 45, '...') ?>
                                            <?php endif; ?>
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
                <?php else: ?>
                    <div class="text-center py-5 px-3">
                        <i class="fas fa-comments" style="font-size:2.5rem;color:var(--chat-text-secondary);opacity:.3"></i>
                        <p class="mt-2 mb-3" style="color:var(--chat-text-secondary);font-size:.85rem">No conversations yet</p>
                        <button onclick="document.getElementById('newChatModal').style.display='flex'" style="background:var(--chat-unread);color:#111b21;border:none;border-radius:20px;padding:8px 20px;font-size:.85rem;cursor:pointer">
                            <i class="fas fa-comment me-1"></i>Start a Chat
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- MAIN CHAT AREA - Empty State -->
        <div class="chat-main" id="chatMain">
            <div class="chat-main-bg"></div>
            <div class="chat-empty-state">
                <i class="fas fa-comment"></i>
                <h3>NtoshiSoft Chat</h3>
                <p>Send and receive messages. Select a group chat or start a new conversation.</p>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm" style="background:var(--chat-unread);color:#111b21;border-radius:20px;padding:8px 20px" onclick="document.getElementById('newChatModal').style.display='flex'">
                        <i class="fas fa-comment me-2"></i>New Chat
                    </button>
                    <button class="btn btn-sm" style="background:var(--chat-input-bg);color:var(--chat-text);border:1px solid var(--chat-border);border-radius:20px;padding:8px 20px" onclick="document.getElementById('createRoomModal').style.display='flex'">
                        <i class="fas fa-users me-2"></i>New Group
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

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
                    <div style="max-height:200px;overflow-y:auto" id="participantList">
                        <?php if (!empty($allUsers)): ?>
                            <?php foreach ($allUsers as $u): ?>
                                <label style="display:flex;align-items:center;gap:10px;padding:8px;cursor:pointer;border-radius:8px;transition:background .15s;color:var(--chat-text)" class="participant-option">
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
        const preview = item.querySelector('.conv-preview')?.textContent.toLowerCase() || '';
        item.style.display = (name.includes(q) || preview.includes(q)) ? '' : 'none';
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

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});
</script>

<?php $this->view('inc/footer', $data) ?>
