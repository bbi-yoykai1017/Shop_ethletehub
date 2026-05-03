<!DOCTYPE html>
<html lang="vi">
<head>
    <!-- ===================================================== -->
    <!-- THIẾT LẬP CƠ BẢN -->
    <!-- ===================================================== -->

    <!-- Bộ mã UTF-8 -->
    <meta charset="UTF-8">

    <!-- Responsive trên mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tiêu đề tab -->
    <title>AthleteHub AI Assistant</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ===================================================== */
        /* KHUNG CHAT CHÍNH */
        /* ===================================================== */
        #chat-box {
            height: 450px;                 /* Chiều cao cố định */
            overflow-y: auto;              /* Scroll dọc */
            background: #f4f7f6;          /* Màu nền */
            border: 1px solid #eee;       /* Viền nhẹ */
            padding: 20px;                /* Khoảng cách trong */
        }

        /* ===================================================== */
        /* BONG BÓNG TIN NHẮN CHUNG */
        /* ===================================================== */
        .msg-bubble {
            max-width: 85%;               /* Không quá rộng */
            padding: 12px 16px;
            border-radius: 15px;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        /* ===================================================== */
        /* TIN NHẮN BOT */
        /* ===================================================== */
        .bot-msg {
            background: white;
            color: #333;
            border: 1px solid #ddd;
            border-bottom-left-radius: 2px;
        }

        /* ===================================================== */
        /* TIN NHẮN USER */
        /* ===================================================== */
        .user-msg {
            background: #007bff;
            color: white;
            border-bottom-right-radius: 2px;
            align-self: flex-end;
        }

        /* ===================================================== */
        /* NÚT GỬI KHI DISABLED */
        /* ===================================================== */
        #btn-send:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="bg-light">

    <!-- ===================================================== -->
    <!-- KHUNG CHÍNH -->
    <!-- ===================================================== -->
    <div class="container py-5">

        <!-- Card chatbot -->
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">

            <!-- ===================================================== -->
            <!-- HEADER -->
            <!-- ===================================================== -->
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0">Trợ lý ảo AthleteHub</h5>
            </div>


            <!-- ===================================================== -->
            <!-- KHUNG CHAT -->
            <!-- ===================================================== -->
            <div id="chat-box" class="card-body d-flex flex-column">

                <!-- Tin nhắn mặc định ban đầu -->
                <div class="text-start mb-3">
                    <span class="msg-bubble d-inline-block bot-msg">
                        Chào bạn! Shop AthleteHub có thể giúp gì cho bạn?
                    </span>
                </div>

            </div>


            <!-- ===================================================== -->
            <!-- FOOTER NHẬP TIN NHẮN -->
            <!-- ===================================================== -->
            <div class="card-footer bg-white border-0 py-3">

                <div class="input-group">

                    <!-- Ô nhập -->
                    <input
                        type="text"
                        id="user-input"
                        class="form-control"
                        placeholder="Hỏi về sản phẩm thể thao..."
                    >

                    <!-- Nút gửi -->
                    <button
                        class="btn btn-dark px-4"
                        id="btn-send"
                        onclick="sendMsg()"
                    >
                        Gửi
                    </button>

                </div>
            </div>
        </div>
    </div>


    <!-- ===================================================== -->
    <!-- JAVASCRIPT -->
    <!-- ===================================================== -->
    <script>

        // =====================================================
        // GỬI TIN NHẮN LÊN SERVER
        // =====================================================
        async function sendMsg() {

            // Lấy input
            const input = document.getElementById('user-input');

            // Lấy nội dung
            const msg = input.value.trim();

            // Nếu rỗng -> dừng
            if (!msg) return;


            // =====================================================
            // HIỂN THỊ TIN NHẮN USER
            // =====================================================
            appendMessage('user', msg);

            // Reset input
            input.value = '';

            // Bật loading
            toggleLoading(true);


            // =====================================================
            // TẠO FORM DATA GỬI API
            // =====================================================
            let formData = new FormData();

            // Nội dung chat
            formData.append('message', msg);

            // Context sản phẩm hiện tại
            // CW_PRODUCT phải được khai báo ở trang sản phẩm
            formData.append(
                'product_context',
                JSON.stringify(CW_PRODUCT)
            );


            try {

                // =====================================================
                // GỌI API BACKEND
                // =====================================================
                const response = await fetch('api/ai.php', {
                    method: 'POST',
                    body: formData
                });

                // Parse JSON
                const result = await response.json();


                // =====================================================
                // XỬ LÝ RESPONSE
                // =====================================================
                if (result.status === 'success') {

                    // Hiển thị bot
                    appendMessage('bot', result.message);

                } else {

                    // Báo lỗi
                    alert("Có lỗi: " + result.message);
                }

            } catch (err) {

                // Lỗi network / server
                console.error("Lỗi:", err);

            } finally {

                // Tắt loading
                toggleLoading(false);
            }
        }



        // =====================================================
        // HIỂN THỊ TIN NHẮN TRÊN GIAO DIỆN
        // =====================================================
        function appendMessage(sender, message) {

            // Box chat
            const box = document.getElementById('chat-box');

            // Tạo wrapper
            const wrapper = document.createElement('div');


            // Căn trái/phải
            wrapper.className =
                sender === 'bot'
                    ? 'text-start'
                    : 'text-end d-flex flex-column';


            // Chọn style bong bóng
            const bubbleClass =
                sender === 'bot'
                    ? 'bot-msg'
                    : 'user-msg';


            // Render HTML
            wrapper.innerHTML = `
                <span class="msg-bubble d-inline-block ${bubbleClass}">
                    ${message}
                </span>
            `;

            // Append vào chat box
            box.appendChild(wrapper);

            // Auto scroll xuống cuối
            box.scrollTop = box.scrollHeight;
        }



        // =====================================================
        // BẬT / TẮT TRẠNG THÁI LOADING
        // =====================================================
        function toggleLoading(isLoading) {

            const btn = document.getElementById('btn-send');
            const input = document.getElementById('user-input');
            const box = document.getElementById('chat-box');

            // Disable nút và input
            btn.disabled = isLoading;
            input.disabled = isLoading;

            // Đổi text nút
            btn.innerText = isLoading ? "..." : "Gửi";


            // =====================================================
            // HIỂN THỊ BONG BÓNG "ĐANG TRẢ LỜI"
            // =====================================================
            if (isLoading) {

                const temp = document.createElement('div');

                temp.id = 'loading-bubble';
                temp.className = 'text-start';

                temp.innerHTML = `
                    <span class="msg-bubble d-inline-block bot-msg">
                        AthleteHub đang trả lời...
                    </span>
                `;

                box.appendChild(temp);

                // Scroll xuống cuối
                box.scrollTop = box.scrollHeight;

            } else {

                // Xóa loading bubble
                const temp = document.getElementById('loading-bubble');

                if (temp) temp.remove();
            }
        }



        // =====================================================
        // NHẤN ENTER ĐỂ GỬI
        // =====================================================
        document.getElementById("user-input")
            .addEventListener("keypress", (e) => {

                if (
                    e.key === "Enter" &&
                    !e.target.disabled
                ) {
                    sendMsg();
                }
            });

    </script>
</body>
</html>