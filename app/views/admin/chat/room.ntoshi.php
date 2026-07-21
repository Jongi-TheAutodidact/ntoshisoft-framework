<?php
    /**
     * @var object $room
     * @var array $messages
     * @var int|string $current_user_id
     * @var array $data
     */
    $this->view('inc/header', $data); ?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>
<style>
    .chat-container {
        max-width: 100%;
        height: calc(100vh - 150px);
        display: flex;
        flex-direction: column;
        background-color: #f0f2f5;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
    }

    .chat-header {
        background-color: <?= THEME_COLOR == 'primary' ? '#128C7E' : '#6c757d' ?>;
        color: white;
        padding: 15px;
        display: flex;
        align-items: center;
    }

    .chat-header img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 15px;
    }

    .chat-messages {
        padding: 20px;
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        background: url('https://wallpapercave.com/wp/wp4410743.png') center/cover no-repeat;
    }

    .message {
        display: flex;
        flex-direction: column;
        margin-bottom: 15px;
        max-width: 70%;
    }

    .sent {
        align-self: flex-end;
        text-align: right;
    }

    .received {
        align-self: flex-start;
        text-align: left;
    }

    .message-content {
        padding: 10px 15px;
        border-radius: 18px;
        word-wrap: break-word;
        color: #000;
    }

    .sent .message-content {
        background-color: #dcf8c6;
        border-bottom-right-radius: 4px;
    }

    .received .message-content {
        background-color: #ffffff;
        border-bottom-left-radius: 4px;
    }

    .message-info {
        font-size: 0.75rem;
        color: #666;
        margin-top: 5px;
    }

    .sender-info {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .sender-info img {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .chat-input {
        padding: 15px;
        background-color: #fff;
        border-top: 1px solid #e0e0e0;
        display: flex;
        gap: 10px;
        z-index: 100;
    }

    .chat-input input {
        flex: 1;
        border-radius: 20px;
        padding: 10px 20px;
        border: 1px solid #ccc;
        outline: none;
    }

    .chat-input button {
        border-radius: 50%;
        width: 45px;
        height: 45px;
        background: <?= THEME_COLOR == 'primary' ? '#128C7E' : '#6c757d' ?>;
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-12">
            <div class="chat-container">
                <div class="chat-header">
                    <img src="<?= get_image($room->image, 'user') ?>" alt="<?= $room->firstname ?>">
                    <div>
                        <h5 class="mb-0"><?= $room->room_name ?? 'Chat Room' ?></h5>
                        <small class="opacity-75"><?= $room->firstname ?? 'Admin' ?> (Room Creator)</small>
                    </div>
                    <a href="<?= ROOT ?>/admin/chat" class="ms-auto btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Rooms
                    </a>
                </div>

                <div class="chat-messages" id="chatMessages">
                    <?php if (!empty($messages)): foreach ($messages as $msg): ?>
                            <div class="message <?= $msg->user_id == $current_user_id ? 'sent' : 'received' ?>" data-id="<?= $msg->id ?>">

                                <?php if ($msg->user_id != $current_user_id): ?>
                                    <div class="sender-info d-flex align-items-center mb-1">
                                        <img src="<?= get_image($msg->image, 'user') ?>" alt="<?= $msg->firstname ?>" width="25" height="25" class="rounded-circle me-2">
                                        <strong><?= esc($msg->firstname . ' ' . $msg->surname) ?></strong>

                                    </div>


                                <?php endif; ?>
                                <div class="message-content"><?= nl2br(esc($msg->message)) ?></div>
                                <div class="message-info">
                                    <?= date('H:i', strtotime($msg->date_sent)) ?>
                                    <?php if ($msg->user_id == $current_user_id): ?>
                                        <?= $msg->is_read ? '<i class="bi bi-check2-all text-primary"></i>' : '<i class="bi bi-check2"></i>' ?>
                                    <?php endif; ?>

                                </div>
                            </div>
                            <div class="message sent" data-id="<?= $msg->id ?>"></div>
                    <?php endforeach;
                    
                    endif; ?>
                    
                </div>

                <div class="chat-input">
                    <input type="text" class="form-control" id="messageInput" placeholder="Type a message...">
                    <button type="button" id="sendMessageBtn" class="btn btn-<?= THEME_COLOR ?>">
                        <i class="bi bi-send"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden inputs for metadata -->
<input type="hidden" id="roomId" value="<?= $room->id ?>">
<input type="hidden" id="userId" value="<?= $current_user_id ?>">
<input type="hidden" id="userName" value="<?= user('firstname') ?>">
<input type="hidden" id="userImage" value="<?= ROOT ?>/<?= user('image') ?>">

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chatMessages = document.getElementById('chatMessages');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendMessageBtn');

        const roomId = document.getElementById('roomId').value;
        const userId = document.getElementById('userId').value;
        const userName = document.getElementById('userName').value;
        const userImage = document.getElementById('userImage').value;

        // Scroll to bottom initially
        chatMessages.scrollTop = chatMessages.scrollHeight;

        sendBtn.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', e => {
            if (e.key === 'Enter') sendMessage();
        });

        function sendMessage() {
            const message = messageInput.value.trim();
            if (!message) return;

            // Optimistically add to UI
            appendMessage({
                user_id: userId,
                firstname: userName,
                image: userImage,
                message: message,
                time: new Date().toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                }),
                id: Date.now()
            }, 'sent');

            // Send to backend
            fetch("<?= ROOT ?>/admin/chat/send-message", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `room_id=${roomId}&message=${encodeURIComponent(message)}`
            });

            messageInput.value = '';
        }

        function appendMessage(msg, type) {
            const div = document.createElement('div');
            div.className = `message ${type}`;
            div.dataset.id = msg.id || Date.now(); // fallback for sender

            div.innerHTML = `
        ${type === 'received' ? `
        <div class="sender-info d-flex align-items-center mb-1">
            <img src="${msg.image}" width="25" height="25" class="rounded-circle me-2">
            <strong>${msg.firstname}</strong>
        </div>` : ''}
        <div class="message-content">${msg.message.replace(/\n/g, '<br>')}</div>
        <div class="message-info">${msg.time || ''}</div>
    `;
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }


        function getLastMessageId() {
            const last = chatMessages.querySelector('.message:last-child');
            return last ? parseInt(last.dataset.id) : 0;
        }


        // Poll every 3 seconds
        setInterval(async () => {
            try {
                const lastId = getLastMessageId();
                const response = await fetch(`<?= ROOT ?>/admin/chat/get-messages?room_id=${roomId}&last_id=${lastId}`);
                const messages = await response.json();

                if (Array.isArray(messages)) {
                    messages.forEach(msg => {
                        if (msg.user_id != userId) {
                            appendMessage(msg, 'received');
                        }
                    });
                }
            } catch (err) {
                console.error('Polling error:', err);
            }
        }, 3000);
    });
</script>
<script>
    function pollNewMessages() {
        const lastId = getLastMessageId();
        fetch(`<?= ROOT ?>/admin/chat/get-messages?room_id=${roomId}&last_id=${lastId}`)
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    data.forEach(msg => {
                        // Don't double display own message (you already see it optimistically)
                        if (msg.user_id != userId) {
                            appendMessage(msg, 'received');
                        }
                    });
                }
            })
            .catch(error => {
                console.error("Polling error:", error);
            });
    }

    // Start polling every 3 seconds
    setInterval(pollNewMessages, 3000);

    // Helper function 
    function getLastMessageId() {
        const lastMsg = chatMessages.querySelector('.message:last-child');
        return lastMsg?.dataset.id || 0;
    }
</script>

<?php $this->view('inc/footer') ?>