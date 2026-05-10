<!DOCTYPE html>
<html lang="vi">

<head>

    <!-- ===================================================== -->
    <!-- THIẾT LẬP CƠ BẢN -->
    <!-- ===================================================== -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AthleteHub AI Assistant</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ===================================================== */
        /* KHUNG CHAT */
        /* ===================================================== */
        #chat-box {
            height: 450px;
            overflow-y: auto;
            background: #f4f7f6;
            border: 1px solid #eee;
            padding: 20px;
        }

        /* ===================================================== */
        /* BONG BÓNG TIN NHẮN */
        /* ===================================================== */
        .msg-bubble {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 15px;
            margin-bottom: 15px;
            line-height: 1.5;
            word-wrap: break-word;
            white-space: pre-wrap;
        }

        /* ===================================================== */
        /* BOT */
        /* ===================================================== */
        .bot-msg {
            background: white;
            color: #333;
            border: 1px solid #ddd;
            border-bottom-left-radius: 2px;
        }

        /* ===================================================== */
        /* USER */
        /* ===================================================== */
        .user-msg {
            background: #007bff;
            color: white;
            border-bottom-right-radius: 2px;
        }

        /* ===================================================== */
        /* NÚT DISABLED */
        /* ===================================================== */
        #btn-send:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* ===================================================== */
        /* LOADING DOT */
        /* ===================================================== */
        .typing {
            display: inline-flex;
            gap: 5px;
        }

        .typing span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #666;
            animation: bounce 1.2s infinite;
        }

        .typing span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0.7);
                opacity: 0.5;
            }

            40% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="bg-light">

    <!-- ===================================================== -->
    <!-- KHUNG CHÍNH -->
    <!-- ===================================================== -->
    <div class="container py-5">

        <div class="card shadow-sm mx-auto" style="max-width: 600px;">

            <!-- ===================================================== -->
            <!-- HEADER -->
            <!-- ===================================================== -->
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0">
                    AthleteHub AI Assistant
                </h5>
            </div>


            <!-- ===================================================== -->
            <!-- CHAT BOX -->
            <!-- ===================================================== -->
            <div id="chat-box" class="card-body d-flex flex-column">

                <div class="text-start mb-3">

                    <span class="msg-bubble d-inline-block bot-msg">
                        👋 Chào bạn! AthleteHub có thể giúp gì cho bạn?
                    </span>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- INPUT -->
            <!-- ===================================================== -->
            <div class="card-footer bg-white border-0 py-3">

                <div class="input-group">

                    <input type="text" id="user-input" class="form-control"
                        placeholder="Hỏi về sản phẩm thể thao...">

                    <button class="btn btn-dark px-4"
                        id="btn-send"
                        onclick="sendMsg()">

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
        // GỬI TIN NHẮN
        // =====================================================
        async function sendMsg() {

            const input = document.getElementById('user-input');

            const msg = input.value.trim();

            // Không gửi rỗng
            if (!msg) return;


            // =====================================================
            // HIỂN THỊ USER MESSAGE
            // =====================================================
            appendMessage('user', msg);

            input.value = '';

            toggleLoading(true);


            // =====================================================
            // FORM DATA
            // =====================================================
            let formData = new FormData();

            formData.append('message', msg);


            // =====================================================
            // CONTEXT SẢN PHẨM
            // =====================================================
            let productContext = '';

            if (typeof CW_PRODUCT !== 'undefined') {

                try {

                    productContext = JSON.stringify(CW_PRODUCT);

                } catch (e) {

                    console.error('Lỗi stringify CW_PRODUCT:', e);
                }
            }

            formData.append('product_context', productContext);


            try {

                // =====================================================
                // GỌI API
                // =====================================================
                const response = await fetch('api/ai.php', {
                    method: 'POST',
                    body: formData
                });


                // =====================================================
                // CHECK HTTP STATUS
                // =====================================================
                if (!response.ok) {

                    throw new Error(
                        'HTTP Error: ' + response.status
                    );
                }


                // =====================================================
                // LẤY TEXT ĐỂ DEBUG
                // =====================================================
                const text = await response.text();

                console.log('RAW RESPONSE:', text);


                // =====================================================
                // PARSE JSON
                // =====================================================
                let result;

                try {

                    result = JSON.parse(text);

                } catch (jsonError) {

                    console.error('JSON Parse Error:', jsonError);

                    appendMessage(
                        'bot',
                        '❌ Server trả dữ liệu không hợp lệ.'
                    );

                    return;
                }


                // =====================================================
                // XỬ LÝ RESPONSE
                // =====================================================
                if (result.status === 'success') {

                    appendMessage(
                        'bot',
                        result.message
                    );

                } else {

                    appendMessage(
                        'bot',
                        '❌ ' + (
                            result.message ||
                            'Có lỗi xảy ra.'
                        )
                    );
                }

            } catch (err) {

                console.error('Fetch Error:', err);

                appendMessage(
                    'bot',
                    '❌ Không thể kết nối AI. Vui lòng thử lại.'
                );

            } finally {

                toggleLoading(false);
            }
        }



        // =====================================================
        // HIỂN THỊ TIN NHẮN
        // =====================================================
        function appendMessage(sender, message) {

            const box = document.getElementById('chat-box');

            const wrapper = document.createElement('div');

            wrapper.className =
                sender === 'bot'
                    ? 'text-start'
                    : 'text-end d-flex flex-column';


            // =====================================================
            // TẠO BUBBLE
            // =====================================================
            const bubble = document.createElement('span');

            bubble.className =
                `msg-bubble d-inline-block ${sender === 'bot'
                    ? 'bot-msg'
                    : 'user-msg'
                }`;


            // =====================================================
            // CHỐNG XSS
            // =====================================================
            bubble.innerText = message;


            wrapper.appendChild(bubble);

            box.appendChild(wrapper);


            // =====================================================
            // AUTO SCROLL
            // =====================================================
            box.scrollTop = box.scrollHeight;
        }



        // =====================================================
        // LOADING
        // =====================================================
        function toggleLoading(isLoading) {

            const btn = document.getElementById('btn-send');

            const input = document.getElementById('user-input');

            const box = document.getElementById('chat-box');


            // =====================================================
            // DISABLE INPUT
            // =====================================================
            btn.disabled = isLoading;

            input.disabled = isLoading;


            // =====================================================
            // TEXT BUTTON
            // =====================================================
            btn.innerText = isLoading
                ? '...'
                : 'Gửi';


            // =====================================================
            // SHOW LOADING
            // =====================================================
            if (isLoading) {

                const temp = document.createElement('div');

                temp.id = 'loading-bubble';

                temp.className = 'text-start';

                temp.innerHTML = `
                    <span class="msg-bubble d-inline-block bot-msg">
                        <div class="typing">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </span>
                `;

                box.appendChild(temp);

                box.scrollTop = box.scrollHeight;

            } else {

                const temp =
                    document.getElementById(
                        'loading-bubble'
                    );

                if (temp) temp.remove();
            }
        }



        // =====================================================
        // ENTER ĐỂ GỬI
        // =====================================================
        document.getElementById('user-input')
            .addEventListener('keypress', function (e) {

                if (
                    e.key === 'Enter' &&
                    !this.disabled
                ) {

                    sendMsg();
                }
            });

    </script>

</body>

</html>