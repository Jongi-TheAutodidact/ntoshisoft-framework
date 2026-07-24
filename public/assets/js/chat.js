/* =============================================
   NtoshiSoft Chat - Core JavaScript Module
   ============================================= */

let chatConfig = {};
let chatPolling = null;
let typingTimeout = null;
let isRecording = false;
let mediaRecorder = null;
let audioChunks = [];
let lastMessageId = 0;
let recordingTimer = null;
let recordingStartTime = 0;

function initChat(config) {
    chatConfig = config;
    getLastMessageId();
    startPolling();
    setupInputListeners();
    loadEmojiGrid('smileys');
}

function getLastMessageId() {
    const messages = document.querySelectorAll('#chatMessages .chat-message');
    if (messages.length > 0) {
        const last = messages[messages.length - 1];
        lastMessageId = parseInt(last.dataset.id) || 0;
    }
}

function startPolling() {
    if (chatPolling) clearInterval(chatPolling);
    chatPolling = setInterval(fetchNewMessages, chatConfig.pollingInterval || 2000);
}

function stopPolling() {
    if (chatPolling) {
        clearInterval(chatPolling);
        chatPolling = null;
    }
}

async function fetchNewMessages() {
    try {
        const params = chatConfig.type === 'room'
            ? `room_id=${chatConfig.id}&last_id=${lastMessageId}`
            : `conversation_id=${chatConfig.id}&last_id=${lastMessageId}`;

        const response = await fetch(`${chatConfig.messagesUrl}?${params}`);
        if (!response.ok) return;

        const messages = await response.json();
        if (Array.isArray(messages) && messages.length > 0) {
            messages.forEach(msg => {
                if (parseInt(msg.user_id) !== parseInt(chatConfig.userId)) {
                    appendMessage(msg, 'received');
                }
            });
            markAsRead();
        }
    } catch (err) {
        // Silent fail on polling errors
    }
}

function appendMessage(msg, type) {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;

    const msgId = parseInt(msg.id);
    if (chatMessages.querySelector(`.chat-message[data-id="${msgId}"]`)) return;

    const div = document.createElement('div');
    div.className = `chat-message ${type}`;
    div.dataset.id = msgId;

    const time = msg.date_sent
        ? new Date(msg.date_sent).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        : new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    const msgType = msg.message_type || 'text';
    const isGroup = chatConfig.type === 'room';
    const senderName = msg.firstname || '';

    let bubbleContent = '';

    if (type === 'received' && isGroup) {
        bubbleContent += `<div class="msg-sender">${escapeHtml(senderName)}</div>`;
    }

    if (msgType === 'voice' && msg.media_url) {
        bubbleContent += `
            <div class="voice-msg">
                <button class="voice-play-btn" onclick="playVoice(this, '${chatConfig.root}/${msg.media_url}')">
                    <i class="fas fa-play"></i>
                </button>
                <div class="voice-waveform">
                    ${Array.from({length: 25}, () => `<div class="bar" style="height:${Math.floor(Math.random() * 18 + 4)}px"></div>`).join('')}
                </div>
                <span class="voice-duration">0:00</span>
            </div>`;
    } else if (msgType === 'image' && msg.media_url) {
        bubbleContent += `
            <div class="msg-image">
                <img src="${chatConfig.root}/${msg.media_url}" alt="Photo" loading="lazy">
            </div>`;
        if (msg.message && msg.message !== '[image]') {
            bubbleContent += `<div class="msg-text">${escapeHtml(msg.message).replace(/\n/g, '<br>')}</div>`;
        }
    } else {
        bubbleContent += `<div class="msg-text">${escapeHtml(msg.message || '').replace(/\n/g, '<br>')}</div>`;
    }

    let ticks = '';
    if (type === 'sent') {
        const readClass = msg.is_read ? 'read' : (msg.is_delivered ? 'delivered' : 'sent-tick');
        const icon = (msg.is_read || msg.is_delivered) ? 'fas fa-check-double' : 'fas fa-check';
        ticks = `<span class="msg-ticks ${readClass}"><i class="${icon}"></i></span>`;
    }

    div.innerHTML = `
        <div class="msg-bubble">
            ${bubbleContent}
            <div class="msg-footer">
                <span class="msg-time">${time}</span>
                ${ticks}
            </div>
        </div>`;

    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    if (msgId > lastMessageId) lastMessageId = msgId;
}

function sendChatMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    if (!message) return;

    const payload = {
        message: message,
        message_type: 'text',
    };

    if (chatConfig.type === 'room') {
        payload.room_id = chatConfig.id;
    } else {
        payload.conversation_id = chatConfig.id;
    }

    appendMessage({
        id: Date.now(),
        user_id: chatConfig.userId,
        firstname: chatConfig.userName || '',
        message: message,
        date_sent: new Date().toISOString(),
        message_type: 'text',
    }, 'sent');

    input.value = '';
    input.style.height = 'auto';
    toggleSendVoiceBtn();
    clearTyping();

    fetch(chatConfig.sendUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    }).then(r => r.json()).then(data => {
        if (data.status === 'success' && data.message_id) {
            const sentMsgs = document.querySelectorAll('#chatMessages .chat-message.sent');
            const lastSent = sentMsgs[sentMsgs.length - 1];
            if (lastSent) {
                lastSent.dataset.id = data.message_id;
                if (data.message_id > lastMessageId) lastMessageId = data.message_id;
            }
        }
    }).catch(() => {});
}

function sendVoiceMessage(audioBlob, duration) {
    const formData = new FormData();
    formData.append('voice', audioBlob, 'voice.webm');

    fetch(chatConfig.voiceUrl, {
        method: 'POST',
        body: formData,
    }).then(r => r.json()).then(data => {
        if (data.status === 'success') {
            const payload = {
                message: `Voice message (${formatDuration(duration)})`,
                message_type: 'voice',
                media_url: data.path,
            };

            if (chatConfig.type === 'room') {
                payload.room_id = chatConfig.id;
            } else {
                payload.conversation_id = chatConfig.id;
            }

            appendMessage({
                id: Date.now(),
                user_id: chatConfig.userId,
                message: payload.message,
                date_sent: new Date().toISOString(),
                message_type: 'voice',
                media_url: data.path,
            }, 'sent');

            fetch(chatConfig.sendUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            }).then(r => r.json()).then(d => {
                if (d.status === 'success' && d.message_id) {
                    const sentMsgs = document.querySelectorAll('#chatMessages .chat-message.sent');
                    const lastSent = sentMsgs[sentMsgs.length - 1];
                    if (lastSent) {
                        lastSent.dataset.id = d.message_id;
                        if (d.message_id > lastMessageId) lastMessageId = d.message_id;
                    }
                }
            });
        }
        resetRecordingUI();
    }).catch(() => {
        resetRecordingUI();
    });
}

// ─── VOICE RECORDING ───
async function startRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm;codecs=opus' });
        audioChunks = [];

        mediaRecorder.ondataavailable = (e) => {
            if (e.data.size > 0) audioChunks.push(e.data);
        };

        mediaRecorder.onstop = () => {
            const duration = (Date.now() - recordingStartTime) / 1000;
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            if (duration >= 1) {
                sendVoiceMessage(audioBlob, duration);
            } else {
                resetRecordingUI();
            }
            stream.getTracks().forEach(t => t.stop());
        };

        mediaRecorder.start();
        isRecording = true;
        recordingStartTime = Date.now();

        const voiceBtn = document.getElementById('voiceBtn');
        voiceBtn.classList.add('recording');
        voiceBtn.innerHTML = '<i class="fas fa-stop"></i>';

        document.getElementById('chatStatus').innerHTML = '<em>Recording audio...</em>';
        document.getElementById('chatStatus').classList.add('typing');
        sendTypingIndicator(true, true);

    } catch (err) {
        console.error('Microphone access denied:', err);
        alert('Please allow microphone access to record voice messages.');
        resetRecordingUI();
    }
}

function stopRecording() {
    if (mediaRecorder && isRecording) {
        mediaRecorder.stop();
        isRecording = false;
    }
}

function resetRecordingUI() {
    isRecording = false;
    mediaRecorder = null;

    const voiceBtn = document.getElementById('voiceBtn');
    if (voiceBtn) {
        voiceBtn.classList.remove('recording');
        voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
    }

    const chatStatus = document.getElementById('chatStatus');
    if (chatStatus) {
        chatStatus.classList.remove('typing');
        const headerName = document.querySelector('.chat-header-name');
        if (chatConfig.type === 'room') {
            chatStatus.textContent = headerName?.dataset?.status || '';
        } else {
            chatStatus.textContent = 'online';
        }
    }
    sendTypingIndicator(false, false);
}

function toggleRecording() {
    if (isRecording) {
        stopRecording();
    } else {
        startRecording();
    }
}

function playVoice(btn, src) {
    const audio = btn.closest('.voice-msg')?.querySelector('audio');

    if (audio && !audio.paused) {
        audio.pause();
        btn.innerHTML = '<i class="fas fa-play"></i>';
        return;
    }

    let existingAudio = audio;
    if (!existingAudio) {
        existingAudio = document.createElement('audio');
        existingAudio.src = src;
        btn.closest('.voice-msg').appendChild(existingAudio);

        existingAudio.addEventListener('ended', () => {
            btn.innerHTML = '<i class="fas fa-play"></i>';
            const bars = btn.closest('.voice-msg').querySelectorAll('.voice-waveform .bar');
            bars.forEach(b => b.style.height = Math.floor(Math.random() * 18 + 4) + 'px');
        });

        existingAudio.addEventListener('timeupdate', () => {
            const durationEl = btn.closest('.voice-msg').querySelector('.voice-duration');
            if (durationEl) {
                durationEl.textContent = formatDuration(existingAudio.currentTime);
            }
            const bars = btn.closest('.voice-msg').querySelectorAll('.voice-waveform .bar');
            const progress = existingAudio.currentTime / (existingAudio.duration || 1);
            const activeBar = Math.floor(progress * bars.length);
            bars.forEach((b, i) => {
                if (i <= activeBar) {
                    b.style.height = Math.floor(Math.random() * 18 + 4) + 'px';
                    b.style.opacity = '1';
                } else {
                    b.style.opacity = '0.3';
                }
            });
        });
    }

    existingAudio.play();
    btn.innerHTML = '<i class="fas fa-pause"></i>';
}

// ─── TYPING INDICATOR ───
function handleTyping() {
    toggleSendVoiceBtn();
    sendTypingIndicator(true, false);
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => sendTypingIndicator(false, false), 2000);
}

function clearTyping() {
    clearTimeout(typingTimeout);
    sendTypingIndicator(false, false);
}

function sendTypingIndicator(isTyping, isRecording) {
    const payload = {
        is_typing: isTyping,
        is_recording: isRecording,
    };
    if (chatConfig.type === 'room') {
        payload.room_id = chatConfig.id;
    } else {
        payload.conversation_id = chatConfig.id;
    }

    fetch(chatConfig.typingUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    }).catch(() => {});
}

function showTypingIndicator(name, isRecording) {
    const indicator = document.getElementById('typingIndicator');
    const text = document.getElementById('typingText');
    if (!indicator || !text) return;

    indicator.style.display = 'flex';
    if (isRecording) {
        text.textContent = `${name} is recording...`;
    } else {
        text.textContent = `${name} is typing...`;
    }
}

function hideTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    if (indicator) indicator.style.display = 'none';
}

// ─── MARK AS READ ───
function markAsRead() {
    fetch(chatConfig.markReadUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(chatConfig.type === 'room'
            ? { room_id: chatConfig.id }
            : { conversation_id: chatConfig.id }
        ),
    }).catch(() => {});
}

// ─── INPUT LISTENERS ───
function setupInputListeners() {
    const input = document.getElementById('messageInput');
    if (!input) return;

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendChatMessage();
        }
    });

    input.addEventListener('input', () => {
        autoResize(input);
        toggleSendVoiceBtn();
    });
}

function handleInputKeydown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendChatMessage();
    }
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
}

function toggleSendVoiceBtn() {
    const input = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const voiceBtn = document.getElementById('voiceBtn');

    if (input && sendBtn && voiceBtn) {
        const hasText = input.value.trim().length > 0;
        sendBtn.style.display = hasText ? 'flex' : 'none';
        voiceBtn.style.display = hasText ? 'none' : 'flex';
    }
}

// ─── EMOJI PICKER ───
const emojiData = {
    smileys: ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','🙃','😉','😊','😇','🥰','😍','🤩','😘','😗','😚','😙','🥲','😋','😛','😜','🤪','😝','🤑','🤗','🤭','🫢','🤫','🤔','🫡','🤐','🤨','😐','😑','😶','🫥','😏','😒','🙄','😬','🤥','😌','😔','😪','🤤','😴','😷','🤒','🤕','🤢','🤮','🥵','🥶','🥴','😵','🤯','🤠','🥳','🥸','😎','🤓','🧐','😕','🫤','😟','🙁','😮','😯','😲','😳','🥺','🥹','😦','😧','😨','😰','😥','😢','😭','😱','😖','😣','😞','😓','😩','😫','🥱','😤','😡','😠','🤬','😈','👿','💀','☠️','💩','🤡','👹','👺','👻','👽','👾','🤖'],
    gestures: ['👋','🤚','🖐️','✋','🖖','🫱','🫲','🫳','🫴','👌','🤌','🤏','✌️','🤞','🫰','🤟','🤘','🤙','👈','👉','👆','🖕','👇','☝️','🫵','👍','👎','✊','👊','🤛','🤜','👏','🙌','🫶','👐','🤲','🤝','🙏'],
    people: ['👶','🧒','👦','👧','🧑','👱','👨','🧔','👩','🧓','👴','👵','🙍','🙎','🙅','🙆','💁','🙋','🧏','🙇','🤦','🤷','👮','🕵️','💂','🥷','👷','🫅','🤴','👸','👳','👲','🧕','🤵','👰','🤰','🫃','🤱','👼','🎅','🤶','🦸','🦹','🧙','🧚','🧛','🧜','新华','🧝','🧞','🧟','🧌','💏','💑','👪','👨‍👩‍👦','👨‍👩‍👧','👨‍👩‍👧‍👦','👨‍👩‍👦‍👦','👨‍👩‍👧‍👧','👨‍👦','👨‍👧','👩‍👦','👩‍👧'],
    animals: ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐻‍❄️','🐨','🐯','🦁','🐮','🐷','🐽','🐸','🐵','🙈','🙉','🙊','🐒','🐔','🐧','🐦','🐤','🐣','🐥','🦆','🦅','🦉','🦇','🐺','🐗','🐴','🦄','🐝','🪱','🐛','🦋','🐌','🐞','🐜','🪰','🪲','🪳','🦟','🦗','🕷️','🦂','🐢','🐍','🦎','🦖','🦕','🐙','🦑','🦐','🦞','🦀','🐡','🐠','🐟','🐬','🐳','🐋','🦈','🐊','🐅','🐆','🦓','🦍','🦧','🐘','🦣','🦛','🦏','🐪','🐫','🦒','🦘','🦬','🐃','🐂','🐄','🐎','🐖','🐏','🐑','🦙','🐐','🦌','🐕','🐩','🦮','🐈','🐈‍⬛','🪶','🐓','🦃','🦤','🦚','🦜','🦢','🦩','🕊️','🐇','🦝','🦨','🦡','🦫','🦦','🦥','🐁','🐀','🐿️','🦔'],
    food: ['🍏','🍎','🍐','🍊','🍋','🍌','🍉','🍇','🍓','🫐','🍈','🍒','🍑','🥭','🍍','🥥','🥝','🍅','🥑','🍆','🥔','🥕','🌽','🌶️','🫑','🥒','🥬','🥦','🧄','🧅','🍄','🥜','🫘','🌰','🫚','🫛','🍞','🥐','🥖','🫓','🥨','🥯','🥞','🧇','🧀','🍖','🍗','🥩','🥓','🍔','🍟','🍕','🌭','🥪','🌮','🌯','🫔','🥙','🧆','🥚','🍳','🥘','🍲','🫕','🥣','🥗','🍿','🧈','🧂','🥫','🍱','🍘','🍙','🍚','🍛','🍜','🍝','🍠','🍢','🍣','🍤','🍥','🥮','🍡','🥟','🥠','🥡','🦀','🦞','🦐','🦑','🦪','🍦','🍧','🍨','🍩','🍪','🎂','🍰','🧁','🥧','🍫','🍬','🍭','🍮','🍯','🍼','🥛','☕','🫖','🍵','🍶','🍾','🍷','🍸','🍹','🍺','🍻','🥂','🥃','🫗','🥤','🧋','🧃','🧉','🧊'],
    travel: ['🚗','🚕','🚙','🚌','🚎','🏎️','🚓','🚑','🚒','🚐','🛻','🚚','🚛','🚜','🛵','🏍️','🛺','🚲','🛴','🛹','🛼','🚏','🛣️','🛤️','⛽','🛞','🚨','🚥','🚦','🛑','🚧','⚓','🛟','⛵','🛶','🚤','🛳️','⛴️','🛥️','🚢','✈️','🛩️','🛫','🛬','🪂','💺','🚁','🚟','🚠','🚡','🛰️','🚀','🛸','🌍','🌎','🌏','🗺️','🧭','🏔️','⛰️','🌋','🗻','🏕️','🏖️','🏜️','🏝️','🏞️','🏟️','🏛️','🏗️','🧱','🪨','🪵','🛖','🏘️','🏚️','🏗️','🏭','🏢','🏬','🏣','🏤','🏥','🏦','🏨','🏪','🏫','🏩','💒','🏛️','⛪','🕌','🕍','🛕','🕋','⛩️','🛤️'],
    objects: ['⌚','📱','📲','💻','⌨️','🖥️','🖨️','🖱️','🖲️','💽','💾','💿','📀','📼','📷','📸','📹','🎥','📽️','🎞️','📞','☎️','📟','📠','📺','📻','🎙️','🎚️','🎛️','🧭','⏱️','⏲️','⏰','🕰️','📡','🔋','🪫','🔌','💡','🔦','🕯️','🪔','🧯','🛢️','💸','💵','💴','🇪🇺','💷','🪙','💰','💳','🪪','🧾','📧','📨','📩','📤','📥','📦','🏷️','🪧','📪','📫','📬','📭','📮','🗳️','✏️','✒️','🖋️','🖊️','🖌️','🖍️','📝','💼','📁','📂','🗂️','📅','📆','🗒️','🗓️','📇','📈','📉','📊','📋','📌','📍','📎','🖇️','📏','📐','✂️','🗃️','🗄️','🗑️','🔒','🔓','🔏','🔐','🗝️','❤️'],
    symbols: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❤️‍🔥','❤️‍🩹','❣️','💕','💞','💓','💗','💖','💘','💝','💟','☮️','✝️','☪️','🕉️','☸️','✡️','🔯','🕎','☯️','☦️','🛐','⛎','♈','♉','♊','♋','♌','♍','♎','♏','♐','♑','♒','♓','🆔','⚛️','🉑','☢️','☣️','📴','📳','🈶','🈚','🈸','🈺','🈷️','✴️','🆚','💮','🉐','㊙️','㊗️','🈴','🈵','🈹','🈲','🅰️','🅱️','🆎','🆑','🅾️','🆘','❌','⭕','🛑','⛔','📛','🚫','💯','💢','♨️','🚷','🚯','🚳','🚱','🔞','📵','🚭','❗','❕','❓','❔','‼️','⁉️','🔅','🔆','〽️','⚠️','🚸','🔱','⚜️','🔰','♻️','✅','🈯','💹','❇️','✳️','❎','🌐','💠','Ⓜ️','🌀','💤','🏧','🚾','♿','🅿️','🛗','🈳','🈂️','🛂','🛃','🛄','🛅','🚹','🚺','🚼','⚧️','🚻','🚮','🎦','📶','🈁','🔣','ℹ️','🔤','🔡','🔠','🆖','🆗','🆙','🆒','🆕','🆓','0️⃣','1️⃣','2️⃣','3️⃣','4️⃣','5️⃣','6️⃣','7️⃣','8️⃣','9️⃣','🔟','🔢','#️⃣','*️⃣','⏏️','▶️','⏸️','⏯️','⏹️','⏺️','⏭️','⏮️','⏩','⏪','⏫','⏬','◀️','🔼','🔽','➡️','⬅️','⬆️','⬇️','↗️','↘️','↙️','↖️','↕️','↔️','↪️','↩️','⤴️','⤵️','🔀','🔁','🔂','🔄','🔃','🎵','🎶','➕','➖','➗','✖️','🟰','♾️','💲','💱','™️','©️','®️','〰️','➰','➿','🔚','🔙','🔛','🔝','🔜','✔️','☑️','🔘','🔴','🟠','🟡','🟢','🔵','🟣','⚫','⚪','🟤','🔺','🔻','🔸','🔹','🔶','🔷','🔳','🔲','▪️','▫️','◾','◽','◼️','◻️','🟫','🟧','🟨','🟩','🟦','🟪','⬛','⬜','🟫'],
    flags: ['🏁','🚩','🎌','🏴','🏳️','🏳️‍🌈','🏳️‍⚧️','🏴‍☠️','🇺🇸','🇬🇧','🇿🇦','🇳🇬','🇰🇪','🇪🇬','🇬🇭','🇪🇹','🇹🇿','🇺🇬','🇷🇼','🇧🇼','🇳🇦','🇿🇲','🇿🇼','🇲🇿','🇦🇴','🇸🇳','🇨🇮','🇲🇱','🇧🇫','🇳🇪','🇹🇩','🇨🇲','🇬🇦','🇨🇬','🇨🇩','🇦🇩','🇦🇪','🇦🇫','🇦🇬','🇦🇱','🇦🇲','🇦🇴','🇦🇷','🇦🇹','🇦🇺','🇦🇿','🇧🇦','🇧🇧','🇧🇩','🇧🇪','🇧🇬','🇧🇭','🇧🇮','🇧🇯','🇧🇳','🇧🇴','🇧🇷','🇧🇸','🇧🇹','🇧🇾','🇧🇿','🇨🇦','🇨🇭','🇨🇱','🇨🇳','🇨🇴','🇨🇷','🇨🇺','🇨🇿','🇩🇪','🇩🇯','🇩🇰','🇩🇲','🇩🇴','🇩🇿','🇪🇨','🇪🇪','🇪🇷','🇪🇸','🇪🇹','🇫🇮','🇫🇯','🇫🇲','🇫🇴','🇫🇷','🇬🇦','🇬🇧','🇬🇩','🇬🇪','🇬🇭','🇬🇲','🇬🇳','🇬🇶','🇬🇷','🇬🇹','🇬🇼','🇬🇾','🇭🇰','🇭🇳','🇭🇷','🇭🇹','🇭🇺','🇮🇩','🇮🇪','🇮🇱','🇮🇳','🇮🇶','🇮🇷','🇮🇸','🇮🇹','🇯🇲','🇯🇴','🇯🇵','🇰🇪','🇰🇬','🇰🇭','🇰🇮','🇰🇲','🇰🇳','🇰🇵','🇰🇷','🇰🇼','🇰🇿','🇱🇦','🇱🇧','🇱🇨','🇱🇮','🇱🇰','🇱🇷','🇱🇸','🇱🇹','🇱🇺','🇱🇻','🇱🇾','🇲🇦','🇲🇨','🇲🇩','🇲🇪','🇲🇬','🇲🇰','🇲🇱','🇲🇲','🇲🇳','🇲🇴','🇲🇷','🇲🇹','🇲🇺','🇲🇻','🇲🇼','🇲🇽','🇲🇾','🇲🇿','🇳🇦','🇳🇨','🇳🇪','🇳🇬','🇳🇮','🇳🇱','🇳🇴','🇳🇵','🇳🇿','🇴🇲','🇵🇦','🇵🇪','🇵🇫','🇵🇬','🇵🇭','🇵🇰','🇵🇱','🇵🇹','🇵🇼','🇶🇦','🇷🇪','🇷🇴','🇷🇸','🇷🇺','🇷🇼','🇸🇦','🇸🇧','🇸🇨','🇸🇩','🇸🇪','🇸🇬','🇸🇮','🇸🇰','🇸🇱','🇸🇲','🇸🇳','🇸🇴','🇸🇷','🇸🇸','🇸🇻','🇸🇾','🇸🇿','🇹🇨','🇹🇩','🇹🇬','🇹🇭','🇹🇯','🇹🇱','🇹🇲','🇹🇳','🇹🇴','🇹🇷','🇹🇹','🇹🇻','🇹🇼','🇹🇿','🇺🇦','🇺🇬','🇺🇾','🇺🇿','🇻🇪','🇻🇳','🇻🇺','🇼🇸','🇽🇰','🇾🇪','🇿🇦','🇿🇲','🇿🇼']
};

function loadEmojiGrid(category) {
    const grid = document.getElementById('emojiGrid');
    if (!grid) return;

    const emojis = emojiData[category] || emojiData.smileys;
    grid.innerHTML = emojis.map(e =>
        `<button onclick="insertEmoji('${e}')" title="${e}">${e}</button>`
    ).join('');

    document.querySelectorAll('.emoji-category-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.category === category);
        btn.onclick = () => loadEmojiGrid(btn.dataset.category);
    });
}

function toggleEmojiPicker() {
    const picker = document.getElementById('emojiPicker');
    if (picker) {
        picker.classList.toggle('show');
    }
}

function insertEmoji(emoji) {
    const input = document.getElementById('messageInput');
    if (input) {
        const start = input.selectionStart;
        const end = input.selectionEnd;
        input.value = input.value.substring(0, start) + emoji + input.value.substring(end);
        input.selectionStart = input.selectionEnd = start + emoji.length;
        input.focus();
        toggleSendVoiceBtn();
    }
}

// Close emoji picker on outside click
document.addEventListener('click', (e) => {
    const picker = document.getElementById('emojiPicker');
    const btn = document.getElementById('emojiBtn');
    if (picker && !picker.contains(e.target) && btn && !btn.contains(e.target)) {
        picker.classList.remove('show');
    }
});

// ─── FILE HANDLING ───
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('Only image files are supported');
        return;
    }

    const formData = new FormData();
    formData.append('image', file);

    fetch(chatConfig.root + '/admin/chat/upload-image', {
        method: 'POST',
        body: formData,
    }).then(r => r.json()).then(data => {
        if (data.status === 'success' && data.path) {
            const payload = {
                message: '[image]',
                message_type: 'image',
                media_url: data.path,
            };

            if (chatConfig.type === 'room') {
                payload.room_id = chatConfig.id;
            } else {
                payload.conversation_id = chatConfig.id;
            }

            appendMessage({
                id: Date.now(),
                user_id: chatConfig.userId,
                message: '[image]',
                date_sent: new Date().toISOString(),
                message_type: 'image',
                media_url: data.path,
            }, 'sent');

            fetch(chatConfig.sendUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            }).then(r => r.json()).then(d => {
                if (d.status === 'success' && d.message_id) {
                    const sentMsgs = document.querySelectorAll('#chatMessages .chat-message.sent');
                    const lastSent = sentMsgs[sentMsgs.length - 1];
                    if (lastSent) {
                        lastSent.dataset.id = d.message_id;
                        if (d.message_id > lastMessageId) lastMessageId = d.message_id;
                    }
                }
            });
        } else {
            alert('Image upload failed: ' + (data.message || 'Unknown error'));
        }
    }).catch(() => {
        alert('Image upload failed. Please try again.');
    });

    event.target.value = '';
}

// ─── UTILITIES ───
function formatDuration(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function toggleGroupInfo() {
    // Placeholder for group info panel
}

function toggleMsgSearch() {
    // Placeholder for message search
}
