<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Widget Interface</title>
    <style>
        /* ============================= */
        /* 1. NÚT CỐ ĐỊNH (KHÔNG RESPONSIVE) */
        /* ============================= */

        .back-to-top,
        .chat-launcher {
            position: fixed;
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

        /* Nút mũi tên */
        .back-to-top {
            bottom: 20px !important;
        }

        /* Nút chat */
        .chat-launcher {
            bottom: 85px !important;
            background-color: #ff4d4f;
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: none;
        }

        /* ============================= */
        /* 2. KHUNG CHAT */
        /* ============================= */

        .chat-box {
            position: fixed;
            bottom: 145px;
            right: 20px;
            width: 320px;
            height: 420px;

            background: #fff;
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

        /* ============================= */
        /* 3. HEADER */
        /* ============================= */

        .chat-header {
            background: #fff;
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        /* ============================= */
        /* 4. CONTENT */
        /* ============================= */

        .chat-content {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background: #f9f9f9;
        }

        /* ============================= */
        /* 5. TIN NHẮN */
        /* ============================= */

        .bot-msg,
        .user-msg {
            max-width: 75%;
            padding: 10px;
            border-radius: 12px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        /* Bot */
        .bot-msg {
            background: #f1f0f0;
            align-self: flex-start;
        }

        /* User */
        .user-msg {
            background: #0084ff;
            color: white;
            align-self: flex-end;
            text-align: right;
        }

        /* ============================= */
        /* 6. FAQ BUTTON */
        /* ============================= */

        .reply-btn {
            background: white;
            border: 1px solid #0084ff;
            color: #0084ff;
            padding: 6px 12px;
            border-radius: 15px;
            margin-bottom: 6px;
            cursor: pointer;
            display: inline-block;
            font-size: 13px;
            transition: 0.2s;
        }

        .reply-btn:hover {
            background: #0084ff;
            color: white;
        }

        .quick-replies {
            max-height: 90px;
            overflow-y: auto;
        }

        .chat-content::-webkit-scrollbar {
            width: 4px;
        }

        .chat-content::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 10px;
        }

        .chat-box {
            transform: scale(0.8);
            opacity: 0;
            transition: 0.2s ease;
        }

        .chat-box.active {
            transform: scale(1);
            opacity: 1;
        }

        /* ============================= */
        /* 7. FOOTER */
        /* ============================= */

        .chat-footer {
            padding: 10px;
            border-top: 1px solid #eee;
        }

        .chat-footer input {
            width: 100%;
            border: none;
            outline: none;
            font-size: 14px;
        }

        /* ============================= */
        /* 8. RESPONSIVE (CHỈ THU NHỎ) */
        /* ============================= */

        @media (max-width: 480px) {

            .chat-box {
                width: 260px;
                height: 360px;
                right: 15px;
                bottom: 140px;
                border-radius: 10px;
            }

            .chat-header {
                padding: 10px;
                font-size: 13px;
            }

            .chat-content {
                padding: 10px;
            }

            .bot-msg,
            .user-msg {
                font-size: 12px;
                padding: 7px 9px;
                margin-bottom: 6px;
            }

            /* FAQ gọn lại */
            .quick-replies {
                gap: 4px;
            }

            .reply-btn {
                font-size: 11px;
                padding: 4px 8px;
                border-radius: 14px;
            }

            /* Input + nút gửi */
            .chat-footer {
                padding: 6px;
            }

            .chat-footer input {
                font-size: 12px;
                padding: 6px 8px;
            }

            .chat-footer button {
                padding: 6px 9px;
                font-size: 12px;
            }
        }

        /* Header nổi bật hơn */
        .chat-header {
            background: linear-gradient(135deg, #ff4d4f, #ff7a45);
            color: white;
            padding: 12px;
            font-weight: bold;
        }

        /* Content nền nhẹ */
        .chat-content {
            background: #f9fafb;
        }

        /* Bot message */
        .bot-msg {
            background: #ffffff;
            border: 1px solid #eee;
        }

        /* User message */
        .user-msg {
            background: #ff4d4f;
            color: white;
        }

        /* FAQ dạng grid đẹp hơn */
        .quick-replies {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .reply-btn {
            background: #fff;
            border: 1px solid #ff4d4f;
            color: #ff4d4f;
            padding: 6px 10px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            transition: 0.2s;
        }

        .reply-btn:hover {
            background: #ff4d4f;
            color: white;
        }

        /* Footer đẹp hơn */
        .chat-footer {
            display: flex;
            gap: 5px;
        }

        .chat-footer input {
            flex: 1;
            padding: 8px;
            border-radius: 20px;
            background: #f1f1f1;
        }

        .chat-footer button {
            background: #ff4d4f;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <!-- NÚT CHAT -->
    <div class="chat-launcher" onclick="toggleChat()">
        <span>💬</span>
    </div>
    <!-- KHUNG CHAT -->
    <div class="chat-box" id="chatBox">

        <!-- HEADER -->
        <div class="chat-header">
            Shop_Athletehub
            <span onclick="toggleChat()" style="cursor:pointer;">✕</span>
        </div>

        <!-- CONTENT -->
        <div class="chat-content" id="chatContent">

            <div class="bot-msg">
                👋 Xin chào! Bạn cần hỗ trợ gì?
            </div>

            <p style="font-size:12px;color:#888;">Chọn nhanh câu hỏi:</p>

            <div class="quick-replies">
                <button class="reply-btn" onclick="sendFAQ(this)">Còn hàng không?</button>
                <button class="reply-btn" onclick="sendFAQ(this)">Có COD không?</button>
                <button class="reply-btn" onclick="sendFAQ(this)">Giao hàng bao lâu?</button>
                <button class="reply-btn" onclick="sendFAQ(this)">Đổi trả thế nào?</button>
                <button class="reply-btn" onclick="sendFAQ(this)">Có giảm giá không?</button>
                <button class="reply-btn" onclick="sendFAQ(this)">Có tư vấn chọn size không?</button>
                <button class="reply-btn" onclick="sendFAQ(this)">Shop bán những gì?</button>
            </div>

        </div>

        <!-- FOOTER -->
        <div class="chat-footer">
            <input type="text" id="chatInput" placeholder="Nhập câu hỏi...">
            <button onclick="sendMessage()">➤</button>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            function toggleChat() {
                document.getElementById('chatBox').classList.toggle('active');
            }

            window.toggleChat = toggleChat;

            function sendFAQ(el) {
                const text = el.innerText;
                addMessage(text, 'user');

                setTimeout(() => {
                    addMessage(getReply(text), 'bot');
                }, 500);
            }

            window.sendFAQ = sendFAQ;

            function sendMessage() {
                const input = document.getElementById("chatInput");
                const text = input.value.trim();
                if (!text) return;

                addMessage(text, 'user');
                input.value = "";

                setTimeout(() => {
                    addMessage("⏳ Vui lòng chờ trong giây lát...", "bot");
                }, 500);
            }

            window.sendMessage = sendMessage;

            document.getElementById("chatInput").addEventListener("keypress", function(e) {
                if (e.key === "Enter") sendMessage();
            });

            function addMessage(text, type) {
                const chat = document.getElementById("chatContent");

                const msg = document.createElement("div");
                msg.className = type === "bot" ? "bot-msg" : "user-msg";
                msg.innerText = text;

                chat.appendChild(msg);
                chat.scrollTop = chat.scrollHeight;
            }

            function getReply(q) {
                const faq = {
                    "Còn hàng không?": "✅ Sản phẩm vẫn còn hàng.",
                    "Có COD không?": "💰 Có hỗ trợ COD.",
                    "Giao hàng bao lâu?": "🚚 2-5 ngày.",
                    "Đổi trả thế nào?": "🔄 Đổi trong 7 ngày.",
                    "Có giảm giá không?": "🎁 Giảm đến 15%.",
                    "Có tư vấn chọn size không?": "📏 Shop hỗ trợ chọn size miễn phí.",
                    "Shop bán những gì?": "🏋️ Shop bán dụng cụ gym, giày, phụ kiện thể thao."
                };

                return faq[q] || "🤖 Shop sẽ phản hồi bạn sớm!";
            }

        });
    </script>
</body>

</html>