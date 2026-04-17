<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Widget Interface</title>
    <style>
        /* 1. CẤU HÌNH CỐ ĐỊNH (KHÔNG THAY ĐỔI DÙ LÀ MOBILE HAY PC) */
        /* Giả sử nút mũi tên của bạn có class là .back-to-top */
        .back-to-top,
        .chat-launcher {
            position: fixed;
            /* Giữ nguyên vị trí và kích thước trên mọi màn hình */
            right: 20px !important;
            width: 45px !important;
            height: 45px !important;

            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            z-index: 1000;
            transition: transform 0.2s ease;
        }

        /* Vị trí đứng của nút Mũi tên (Dưới cùng) */
        .back-to-top {
            bottom: 20px !important;
        }

        /* Vị trí đứng của nút Chat (Ở trên mũi tên) */
        .chat-launcher {
            bottom: 85px !important;
            /* Khoảng cách cố định để luôn thẳng hàng dọc */
            background-color: #ff4d4f;
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: none;
        }

        /* 2. KHUNG CỬA SỔ CHAT */
        .chat-box {
            position: fixed;
            bottom: 145px;
            /* Luôn cách Launcher một khoảng cố định */
            right: 20px;
            width: 320px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 1001;
        }

        .chat-box.active {
            display: flex;
        }

        /* 3. TỐI ƯU RESPONSIVE (CHỈ THAY ĐỔI ĐỘ RỘNG KHUNG CHAT) */
        @media (max-width: 480px) {
            /* Nút vẫn giữ nguyên vị trí right: 20px và size: 45px nhờ !important ở trên */

            .chat-box {
                /* Trên mobile cực nhỏ, khung chat sẽ mở rộng ra để dễ nhìn hơn */
                width: calc(100% - 40px);
                right: 20px;
            }
        }

        /* --- CÁC STYLE PHỤ CHO NỘI DUNG CHAT --- */
        .chat-header {
            background: #fff;
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        .chat-content {
            padding: 15px;
            max-height: 350px;
            overflow-y: auto;
        }

        .bot-msg {
            background: #f1f0f0;
            padding: 10px;
            border-radius: 12px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .reply-btn {
            background: white;
            border: 1px solid #0084ff;
            color: #0084ff;
            padding: 6px 12px;
            border-radius: 15px;
            margin-bottom: 5px;
            cursor: pointer;
            display: block;
            width: fit-content;
        }

        .chat-footer {
            padding: 10px;
            border-top: 1px solid #eee;
        }

        .chat-footer input {
            width: 100%;
            border: none;
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