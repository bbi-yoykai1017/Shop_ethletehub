<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Widget Interface</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
        }

        /* Nút Icon Chat nổi */
        .chat-launcher {
            position: fixed;
            bottom: 80px;
            /* Tăng từ 20px lên 80px để tránh bị đè */
            right: 20px;
            /* Đổi từ right sang left */
            width: 60px;
            height: 60px;
            background-color: #ff4d4f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        /* Khung cửa sổ Chat */
        .chat-box {
            position: fixed;
            bottom: 150px;
            /* Phải cao hơn bottom của chat-launcher (80 + 60 + một khoảng hở) */
            right: 20px;
            /* Đổi sang left để đồng bộ với nút bấm */
            width: 350px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 1000;
        }

        .chat-box.active {
            display: flex;
        }

        /* Header */
        .chat-header {
            background: white;
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        /* Nội dung tin nhắn */
        .chat-content {
            padding: 15px;
            max-height: 400px;
            overflow-y: auto;
            background: #fff;
        }

        .bot-msg {
            background: #f1f0f0;
            padding: 10px 15px;
            border-radius: 15px;
            margin-bottom: 10px;
            font-size: 14px;
            max-width: 85%;
        }

        /* Khu vực câu hỏi gợi ý (Quan trọng nhất) */
        .quick-replies {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 10px;
        }

        .reply-btn {
            background: white;
            border: 1px solid #0084ff;
            color: #0084ff;
            padding: 8px 12px;
            border-radius: 18px;
            font-size: 13px;
            cursor: pointer;
            text-align: left;
            transition: 0.3s;
        }

        .reply-btn:hover {
            background: #e6f4ff;
        }

        /* Input bên dưới */
        .chat-footer {
            padding: 10px;
            border-top: 1px solid #eee;
        }

        .chat-footer input {
            width: 100%;
            border: none;
            padding: 8px;
            outline: none;
        }
    </style>
</head>

<body>

    <div class="chat-launcher" onclick="toggleChat()">
        <span>💬</span>
    </div>

    <div class="chat-box" id="chatBox">
        <div class="chat-header">
            Shop_Athletehub
            <span style="cursor:pointer" onclick="toggleChat()">✕</span>
        </div>

        <div class="chat-content">
            <div class="bot-msg">
                🎁 MUA CÀNG NHIỀU - GIẢM CÀNG SÂU! ƯU ĐÃI LÊN TỚI 15%
            </div>

            <p style="font-size: 12px; color: #888;">Vấn đề khác:</p>

            <div class="quick-replies">
                <button class="reply-btn" onclick="sendReply(this)">Sản phẩm này có sẵn không?</button>
                <button class="reply-btn" onclick="sendReply(this)">Có thể thanh toán bằng COD không?</button>
                <button class="reply-btn" onclick="sendReply(this)">Tôi có thể được giảm giá không?</button>
            </div>
        </div>

        <div class="chat-footer">
            <input type="text" placeholder="Nhập nội dung tin nhắn...">
        </div>
    </div>

    <script>
        function toggleChat() {
            const chatBox = document.getElementById('chatBox');
            chatBox.classList.toggle('active');
        }

        function sendReply(element) {
            alert("Bạn đã chọn: " + element.innerText + "\n(Tính năng này sẽ gửi tin nhắn đến hệ thống)");
        }
    </script>
</body>

</html>