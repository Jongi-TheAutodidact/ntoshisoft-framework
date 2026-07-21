<?php
/** @var array $data */
$this->view('inc/front-header', $data) ?>

<main id="main" class="main vh-100 d-flex flex-column">
    <section class="section flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="container p-0" style="max-width: 720px; width: 100%;">
            <div class="card shadow-sm border-0 h-100 d-flex flex-column">
                <div class="card-body d-flex flex-column p-3" style="overflow: hidden;">

                    <!-- Chat History -->
                    <div id="chat-history" class="flex-grow-1 overflow-auto mb-3 px-2" style="max-height: 60vh;">
                        <!-- Example message -->
                        <!-- <div class="mb-2"><strong>You:</strong> Hello!</div>
            <div class="mb-2"><strong>Bot:</strong> Hi there!</div> -->
                    </div>

                    <!-- Chat Input -->
                    <div class="d-flex gap-2 mb-3">
                        <input type="text" id="user-input" class="form-control rounded-pill" placeholder="Ask something...">
                        <button class="btn btn-success rounded-pill" id="send-btn">Send</button>
                    </div>

                    <!-- PDF Upload -->
                    <form id="pdf-upload">
                        <div class="input-group">
                            <input type="file" class="form-control" name="pdf" accept=".pdf">
                            <button class="btn btn-outline-primary" type="submit">Upload PDF</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
</main>

<?php $this->view('inc/front-footer', $data) ?>


<script>
    const BASE_URL = '<?= ROOT ?>';
    let chatHistory = [];

    // Initialize event listeners
    document.getElementById('send-btn').addEventListener('click', sendMessage);
    document.getElementById('user-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });
    document.getElementById('pdf-upload').addEventListener('submit', uploadPdf);

    // Main chat function
    async function sendMessage() {
        const input = document.getElementById('user-input');
        const message = input.value.trim();

        if (!message) return;

        addMessage('user', message);
        input.value = '';

        try {
            const response = await fetch(`${BASE_URL}/chat/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    message: message,
                    history: chatHistory
                })
            });

            if (!response.ok) throw new Error(await response.text());

            const data = await response.json();
            addMessage('assistant', data.response);
        } catch (error) {
            console.error('Error:', error);
            addMessage('assistant', "Sorry, I encountered an error. Please try again.");
        }
    }

    // PDF upload handler
    async function uploadPdf(e) {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const response = await fetch(`${BASE_URL}/chat/pdf`, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error(await response.text());

            const result = await response.json();
            alert(result.success ? 'PDF processed successfully!' : 'PDF processing failed');
        } catch (error) {
            console.error('Upload error:', error);
            alert('Error uploading PDF');
        }
    }

    // UI helper
    function addMessage(role, content) {
        chatHistory.push({
            role,
            content
        });
        const historyDiv = document.getElementById('chat-history');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${role}`;
        messageDiv.textContent = content;
        historyDiv.appendChild(messageDiv);
        historyDiv.scrollTop = historyDiv.scrollHeight;
    }
</script>