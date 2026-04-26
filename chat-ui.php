<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AthleteHub AI Assistant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #chat-box { height: 450px; overflow-y: auto; background: #f4f7f6; border: 1px solid #eee; padding: 20px; }
        .msg-bubble { max-width: 85%; padding: 12px 16px; border-radius: 15px; margin-bottom: 15px; line-height: 1.5; }
        .bot-msg { background: white; color: #333; border: 1px solid #ddd; border-bottom-left-radius: 2px; }
        .user-msg { background: #007bff; color: white; border-bottom-right-radius: 2px; align-self: flex-end; }
        #btn-send:disabled { opacity: 0.6; cursor: not-allowed; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0">Trợ lý ảo AthleteHub</h5>
            </div>
            <div id="chat-box" class="card-body d-flex flex-column">
                <div class="text-start mb-3">
                    <span class="msg-bubble d-inline-block bot-msg">Chào Đạt! Shop AthleteHub có thể giúp gì cho bạn?</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                <div class="input-group">
                    <input type="text" id="user-input" class="form-control" placeholder="Hỏi về sản phẩm thể thao...">
                    <button class="btn btn-dark px-4" id="btn-send" onclick="sendMsg()">Gửi</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function sendMsg() {
            const input = document.getElementById('user-input');
            const msg = input.value.trim();
            if (!msg) return;

            appendMessage('user', msg);
            input.value = '';
            toggleLoading(true);

            let formData = new FormData();
            formData.append('message', msg);

            try {
                const response = await fetch('api-test.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.status === 'success') {
                    appendMessage('bot', result.message);
                } else {
                    alert("Có lỗi: " + result.message);
                }
            } catch (err) {
                console.error("Lỗi:", err);
            } finally {
                toggleLoading(false);
            }
        }

        function appendMessage(sender, message) {
            const box = document.getElementById('chat-box');
            const wrapper = document.createElement('div');
            wrapper.className = sender === 'bot' ? 'text-start' : 'text-end d-flex flex-column';
            const bubbleClass = sender === 'bot' ? 'bot-msg' : 'user-msg';
            wrapper.innerHTML = `<span class="msg-bubble d-inline-block ${bubbleClass}">${message}</span>`;
            box.appendChild(wrapper);
            box.scrollTop = box.scrollHeight;
        }

        function toggleLoading(isLoading) {
            const btn = document.getElementById('btn-send');
            const input = document.getElementById('user-input');
            const box = document.getElementById('chat-box');
            btn.disabled = isLoading;
            input.disabled = isLoading;
            btn.innerText = isLoading ? "..." : "Gửi";

            if (isLoading) {
                const temp = document.createElement('div');
                temp.id = 'loading-bubble';
                temp.className = 'text-start';
                temp.innerHTML = '<span class="msg-bubble d-inline-block bot-msg">AthleteHub đang trả lời...</span>';
                box.appendChild(temp);
                box.scrollTop = box.scrollHeight;
            } else {
                const temp = document.getElementById('loading-bubble');
                if (temp) temp.remove();
            }
        }

        document.getElementById("user-input").addEventListener("keypress", (e) => {
            if (e.key === "Enter" && !e.target.disabled) sendMsg();
        });
    </script>
</body>
</html>