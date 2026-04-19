<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AthleteHub Chat Support</title>
    <style>
        /* GIỮ NGUYÊN CSS GỐC VÀ CẢI TIẾN NHẸ */
        .back-to-top,
        .chat-launcher {
            position: fixed;
            right: 20px !important;
            width: 50px !important;
            height: 50px !important;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .chat-launcher {
            bottom: 85px !important;
            background: linear-gradient(135deg, #ff4d4f, #ff7a45);
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 77, 79, 0.4);
            border: none;
            font-size: 24px;
        }

        .chat-launcher:hover {
            transform: scale(1.1);
        }

        .chat-box {
            position: fixed;
            /* Căn chỉnh lại khoảng cách so với đáy màn hình */
            bottom: 70px;
            right: 20px;

            /* Sử dụng max-width/height để linh hoạt */
            width: 350px;
            max-height: calc(100% - 110px);
            /* Đảm bảo không bao giờ vượt quá chiều cao màn hình */
            height: 500px;

            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);

            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 1001;

            /* Hiệu ứng mượt mà */
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .chat-box.active {
            display: flex;
            transform: translateY(0);
            opacity: 1;
        }

        /* Căn chỉnh lại Header */
        .chat-header {
            background: linear-gradient(135deg, #ff4d4f, #ff7a45);
            color: white;
            padding: 10px 15px;
            font-size: 15px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            /* Ngăn header bị co lại */
        }

        /* Căn chỉnh lại Content để tự động co giãn */
        .chat-content {
            flex: 1 1 auto;
            padding: 15px;
            overflow-y: auto;
            background: #f9fafb;
        }



        /* Tối ưu tin nhắn để tiết kiệm diện tích */
        .bot-msg,
        .user-msg {
            max-width: 85%;
            font-size: 13px;
            padding: 8px 12px;
            margin-bottom: 8px;
        }

        .bot-msg {
            background: #ffffff;
            align-self: flex-start;
            border: 1px solid #eee;
            color: #333;
            border-bottom-left-radius: 2px;
        }

        .user-msg {
            background: #ff4d4f;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 2px;
        }

        .typing {
            font-style: italic;
            font-size: 12px;
            color: #888;
            margin-bottom: 10px;
            display: none;
        }


        .quick-replies {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }

        .reply-btn {
            background: #fff;
            border: 1px solid #ff4d4f;
            color: #ff4d4f;
            padding: 6px 12px;
            border-radius: 150px;
            cursor: pointer;
            font-size: 12px;
            transition: 0.2s;
        }

        .reply-btn:hover {
         
            color: white;
        }

        /* Căn chỉnh lại Footer */
        .chat-footer {
            padding: 8px;
            border-top: 1px solid #eee;
            background: #fff;
            flex-shrink: 0;
            /* Ngăn footer bị co lại */
            display: flex;
            gap: 8px;
        }

        .chat-footer input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 13px;
            background: #f1f1f1;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .chat-footer button {
            background: #ff4d4f;
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .chat-footer button:hover {
            background: #d4380d;
        }

        /* ============================= */
        /* RESPONSIVE CHO ĐIỆN THOẠI */
        /* ============================= */

       @media (max-width: 480px) {
    /* Thu nhỏ vùng chứa câu hỏi nhanh */
    .quick-replies {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* Chia 2 cột đều nhau */
        gap: 6px;
        margin-bottom: 10px;
    }

    /* Thu nhỏ nút bấm */
    .reply-btn {
        font-size: 11px !important; /* Giảm cỡ chữ */
        padding: 5px 8px !important; /* Giảm khoảng cách đệm */
        border-radius: 12px !important;
        line-height: 1.2;
        min-height: 32px; /* Đảm bảo nút vẫn dễ bấm nhưng không quá to */
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    /* Điều chỉnh khung chat để không bị quá thô */
    .chat-box {
        width: 85vw;
        right: 15px;
        bottom: 80px;
        height: 65vh;
    }
}
    </style>
</head>

<body>

    <div class="chat-launcher" onclick="toggleChat()">💬</div>

    <div class="chat-box" id="chatBox">
        <div class="chat-header">
            <span>🔥 AthleteHub Support</span>
            <span onclick="toggleChat()" style="cursor:pointer; font-size: 20px;">×</span>
        </div>

        <div class="chat-content" id="chatContent">
            <div class="bot-msg">👋 Chào bạn! AthleteHub có thể giúp gì cho hành trình tập luyện của bạn hôm nay?</div>
            <div class="quick-replies" id="quickReplies">
            </div>
            <div id="typingIndicator" class="typing">Bot đang nhập...</div>
        </div>

        <div class="chat-footer">
            <input type="text" id="chatInput" placeholder="Hỏi Shop về sản phẩm, size..." autocomplete="off">
            <button onclick="handleUserInput()">➤</button>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chatBox');
        const chatContent = document.getElementById('chatContent');
        const chatInput = document.getElementById('chatInput');
        const typingIndicator = document.getElementById('typingIndicator');

        // BỘ DỮ LIỆU FAQ MỞ RỘNG
        const faqData = [{
                q: "Còn hàng không?",
                keywords: ["còn hàng", "có sẵn", "item"],
                a: "✅ Hầu hết sản phẩm trên website đều sẵn hàng. Bạn cho Shop xin tên sản phẩm bạn đang quan tâm nhé!"
            },
            {
                q: "Có COD không?",
                keywords: ["cod", "thanh toán khi nhận", "nhận hàng trả tiền"],
                a: "💰 AthleteHub có ship COD toàn quốc. Bạn được kiểm tra hàng trước khi thanh toán nhé!"
            },
            {
                q: "Giao hàng bao lâu?",
                keywords: ["bao lâu", "thời gian ship", "giao hàng"],
                a: "🚚 Nội thành HCM nhận trong 1-2 ngày, tỉnh thành khác khoảng 3-5 ngày làm việc ạ."
            },
            {
                q: "Phí ship bao nhiêu?",
                keywords: ["phí ship", "tiền ship", "vận chuyển"],
                a: "📦 Phí ship đồng giá 30k toàn quốc. Đơn hàng trên 500k sẽ được Miễn Phí Giao Hàng!"
            },
            {
                q: "Đổi trả thế nào?",
                keywords: ["đổi trả", "trả hàng", "lỗi"],
                a: "🔄 Shop hỗ trợ đổi trả trong vòng 7 ngày nếu có lỗi sản xuất hoặc không vừa size (yêu cầu còn tem mác)."
            },
            {
                q: "Tư vấn chọn size",
                keywords: ["size", "kích cỡ", "vừa không"],
                a: "📏 Bạn vui lòng để lại Chiều cao & Cân nặng, nhân viên sẽ tư vấn size chuẩn nhất cho bạn ngay!"
            },
            {
                q: "Địa chỉ shop?",
                keywords: ["địa chỉ", "cửa hàng", "ở đâu"],
                a: "📍 Shop có chi nhánh tại Quận Thủ Đức, TP.HCM. Mời bạn ghé chơi để trải nghiệm sản phẩm trực tiếp!"
            },
            {
                q: "Có giảm giá không?",
                keywords: ["giảm giá", "khuyến mãi", "voucher"],
                a: "🎁 Bạn có thể sử dụng mã 'HLV2026' để được giảm 10% cho đơn hàng đầu tiên đấy!"
            }
        ];

        function toggleChat() {
            chatBox.classList.toggle('active');
            if (chatBox.classList.contains('active') && chatContent.children.length <= 3) {
                renderQuickReplies();
            }
        }

        function renderQuickReplies() {
            const container = document.getElementById('quickReplies');
            container.innerHTML = "";
            faqData.forEach(item => {
                const btn = document.createElement('button');
                btn.className = 'reply-btn';
                btn.innerText = item.q;
                btn.onclick = () => sendMsg(item.q, item.a);
                container.appendChild(btn);
            });
        }

        function sendMsg(userText, botReply) {
            addMessage(userText, 'user');
            showTyping(true);

            setTimeout(() => {
                showTyping(false);
                addMessage(botReply, 'bot');
            }, 800);
        }

        function handleUserInput() {
            const text = chatInput.value.trim();
            if (!text) return;

            addMessage(text, 'user');
            chatInput.value = "";
            showTyping(true);

            setTimeout(() => {
                showTyping(false);
                let response = "🤖 Hiện tại các tư vấn viên đang bận một chút, bạn để lại SĐT để Shop gọi lại hỗ trợ ngay nhé!";

                // Logic tìm kiếm thông minh hơn
                const lowerText = text.toLowerCase();
                for (let item of faqData) {
                    if (item.keywords.some(key => lowerText.includes(key))) {
                        response = item.a;
                        break;
                    }
                }
                addMessage(response, 'bot');
            }, 1000);
        }

        function addMessage(text, type) {
            const msg = document.createElement("div");
            msg.className = type === "bot" ? "bot-msg" : "user-msg";
            msg.innerText = text;

            // Chèn tin nhắn vào trước typing indicator
            chatContent.insertBefore(msg, typingIndicator);
            chatContent.scrollTop = chatContent.scrollHeight;
        }

        function showTyping(status) {
            typingIndicator.style.display = status ? "block" : "none";
            chatContent.scrollTop = chatContent.scrollHeight;
        }

        chatInput.addEventListener("keypress", (e) => {
            if (e.key === "Enter") handleUserInput();
        });
    </script>
</body>

</html>