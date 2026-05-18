<?php
// Nhận thông tin sản phẩm từ trang (nếu có)
// $sp được truyền từ product-detail.php trước khi include file này
$co_san_pham = isset($sp) && $sp;
?>

<style>
    #chat-widget-btn {
        position: fixed;
        bottom: 28px;
        right: 28px;
        width: 58px;
        height: 58px;
        background: #f97316;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(249, 115, 22, 0.5);
        z-index: 99999;
        border: none;
        font-size: 26px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    #chat-widget-btn:hover { transform: scale(1.08); }

    #chat-widget-box {
        position: fixed;
        z-index: 99999;
        display: none;
        flex-direction: column;
        overflow: hidden;
        background: white;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
    }

    @media (min-width: 769px) {
        #chat-widget-box {
            bottom: 28px; right: 28px;
            width: 380px; height: 580px;
            max-height: calc(100vh - 120px);
            border-radius: 18px;
            top: auto; left: auto;
        }
    }

    @media (min-width: 481px) and (max-width: 768px) {
        #chat-widget-btn { bottom: 16px; right: 16px; width: 52px; height: 52px; }
        #chat-widget-box {
            top: 70px; right: 0; left: 0;
            width: 100vw; max-width: 100vw;
            height: calc(100vh - 70px);
            max-height: calc(100vh - 70px);
            border-radius: 20px 20px 0 0;
            box-sizing: border-box;
        }
    }

    @media (max-width: 480px) {
        #chat-widget-btn { bottom: 14px; right: 14px; width: 50px; height: 50px; font-size: 20px; }
        #chat-widget-box {
            top: 70px; left: 0; right: 0;
            width: 100vw; max-width: 100vw;
            height: calc(100vh - 70px);
            max-height: calc(100vh - 70px);
            border-radius: 0;
            box-sizing: border-box;
        }
    }

    #chat-widget-header {
        background: linear-gradient(135deg, #1e3a5f, #2d5a8e);
        color: white; padding: 13px 16px;
        display: flex; justify-content: space-between; align-items: center;
        flex-shrink: 0;
    }

    .cw-header-left { display: flex; align-items: center; gap: 10px; }
    .cw-avatar {
        width: 36px; height: 36px; background: #f97316;
        border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-size: 18px;
    }
    .cw-name { font-weight: bold; font-size: 14px; }
    .cw-status { font-size: 11px; opacity: 0.8; }

    #chat-close-btn {
        cursor: pointer; font-size: 18px;
        background: rgba(255, 255, 255, 0.15);
        border: none; color: white;
        width: 30px; height: 30px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
    #chat-close-btn:hover { background: rgba(255, 255, 255, 0.3); }

    #cw-product-card {
        margin: 12px; border: 1px solid #e5e7eb;
        border-radius: 12px; overflow: hidden;
        display: flex; gap: 10px; padding: 10px;
        background: #fffbf7; flex-shrink: 0;
        cursor: pointer; transition: box-shadow 0.2s;
    }
    #cw-product-card:hover { box-shadow: 0 2px 12px rgba(249, 115, 22, 0.15); }
    #cw-product-card img { width: 64px; height: 64px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }

    .cw-card-info { flex: 1; min-width: 0; }
    .cw-card-name { font-size: 13px; font-weight: 600; color: #1e3a5f; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cw-card-price { font-size: 14px; font-weight: bold; color: #f97316; margin-top: 3px; }
    .cw-card-old { font-size: 11px; color: #999; text-decoration: line-through; }
    .cw-card-badge { display: inline-block; background: #ef4444; color: white; font-size: 10px; padding: 1px 6px; border-radius: 10px; margin-left: 4px; }
    .cw-card-desc { font-size: 11px; color: #666; margin-top: 3px; line-height: 1.3; }

    #chat-widget-messages {
        flex: 1; overflow-y: auto; padding: 12px;
        background: #f4f7f6; display: flex;
        flex-direction: column; gap: 8px;
        -webkit-overflow-scrolling: touch;
    }
    #chat-widget-messages::-webkit-scrollbar { width: 4px; }
    #chat-widget-messages::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }

    .cw-bubble {
        max-width: 85%; padding: 10px 14px;
        border-radius: 16px; font-size: 13.5px;
        line-height: 1.55; word-break: break-word;
        animation: cwFade 0.2s ease;
    }
    @keyframes cwFade {
        from { opacity: 0; transform: translateY(5px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .cw-bot { background: white; border: 1px solid #e5e7eb; align-self: flex-start; border-bottom-left-radius: 4px; }
    .cw-user { background: #f97316; color: white; align-self: flex-end; border-bottom-right-radius: 4px; }

    .cw-loading { display: flex; gap: 4px; align-items: center; padding: 12px 16px; }
    .cw-loading span { width: 7px; height: 7px; background: #bbb; border-radius: 50%; animation: cwDot 1.2s infinite; }
    .cw-loading span:nth-child(2) { animation-delay: 0.2s; }
    .cw-loading span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes cwDot {
        0%, 80%, 100% { transform: scale(0.7); opacity: 0.5; }
        40%            { transform: scale(1);   opacity: 1;   }
    }

    .cw-product-suggestions { align-self: flex-start; width: 100%; max-width: 100%; animation: cwFade 0.2s ease; }
    .cw-suggest-label { font-size: 12px; color: #888; margin-bottom: 6px; padding-left: 2px; }
    .cw-product-cards-row { display: flex; flex-direction: column; gap: 8px; }

    .cw-sp-card {
        display: flex; align-items: center; gap: 10px;
        background: white; border: 1.5px solid #e5e7eb;
        border-radius: 12px; padding: 8px 10px;
        cursor: pointer; text-decoration: none; color: inherit;
        transition: border-color 0.18s, box-shadow 0.18s, transform 0.15s;
    }
    .cw-sp-card:hover { border-color: #f97316; box-shadow: 0 3px 14px rgba(249, 115, 22, 0.18); transform: translateY(-1px); }
    .cw-sp-card img { width: 58px; height: 58px; object-fit: cover; border-radius: 8px; flex-shrink: 0; background: #f3f4f6; }
    .cw-sp-card-info { flex: 1; min-width: 0; }
    .cw-sp-card-name { font-size: 12.5px; font-weight: 600; color: #1e3a5f; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.4; }
    .cw-sp-card-price { font-size: 13px; font-weight: bold; color: #f97316; margin-top: 3px; }
    .cw-sp-card-old { font-size: 11px; color: #aaa; text-decoration: line-through; margin-left: 4px; }
    .cw-sp-card-badge { display: inline-block; background: #ef4444; color: white; font-size: 10px; padding: 1px 5px; border-radius: 8px; margin-left: 4px; }
    .cw-sp-card-arrow { color: #f97316; font-size: 16px; flex-shrink: 0; opacity: 0.7; }
    .cw-sp-card:hover .cw-sp-card-arrow { opacity: 1; }

    #cw-suggestions {
        padding: 8px 10px; display: flex; flex-wrap: wrap; gap: 5px;
        border-top: 1px solid #f0f0f0; background: white; flex-shrink: 0;
    }
    .cw-suggest-btn {
        background: white; border: 1px solid #f97316; color: #f97316;
        border-radius: 20px; padding: 4px 11px; font-size: 12px;
        cursor: pointer; white-space: nowrap; transition: all 0.15s;
    }
    .cw-suggest-btn:hover { background: #f97316; color: white; }

    #chat-widget-footer {
        padding: 10px 12px; border-top: 1px solid #f0f0f0;
        display: flex; gap: 8px; align-items: center;
        background: white; flex-shrink: 0;
    }
    #cw-input {
        flex: 1; border: 1.5px solid #e5e7eb;
        border-radius: 22px; padding: 9px 15px;
        font-size: 14px; outline: none; min-width: 0;
        transition: border-color 0.2s;
    }
    #cw-input:focus { border-color: #f97316; }
    #cw-send {
        background: #f97316; color: white; border: none;
        border-radius: 50%; width: 40px; height: 40px; min-width: 40px;
        cursor: pointer; font-size: 16px;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.2s, transform 0.15s;
    }
    #cw-send:hover { background: #ea6c0a; transform: scale(1.05); }
    #cw-send:disabled { opacity: 0.45; cursor: not-allowed; transform: none; }
</style>

<button id="chat-widget-btn" onclick="toggleChat()" aria-label="Chat với AthleteHub">💬</button>

<div id="chat-widget-box">
    <div id="chat-widget-header">
        <div class="cw-header-left">
            <div class="cw-avatar">🏋️</div>
            <div>
                <div class="cw-name">AthleteHub Support</div>
                <div class="cw-status">🟢 Đang hoạt động</div>
            </div>
        </div>
        <button id="chat-close-btn" onclick="toggleChat()">✕</button>
    </div>
   <!-- $hinh = '/public/' . $sp['hinh_anh_chinh']; -->
    <?php if ($co_san_pham):
        $hinh    = './public/' . $sp['hinh_anh_chinh'];
        $gia     = number_format($sp['gia'],     0, ',', '.');
        $gia_goc = number_format($sp['gia_goc'], 0, ',', '.');
    ?>
        <!-- Card hiển thị sản phẩm khách đang xem — chỉ xuất hiện trên trang product-detail -->
         <!-- <div id="cw-product-card" onclick="window.location='/product/<?= $sp['id'] ?>'">-->
        <div id="cw-product-card" onclick="window.location='./product-detail.php?id=<?= $sp['id'] ?>'">
            <img src="<?= $hinh ?>"
                 alt="<?= htmlspecialchars($sp['ten']) ?>"
                 onerror="this.onerror=null; this.src='./public/placeholder.svg'">
                 <!--   onerror="this.onerror=null; this.src='/public/placeholder.svg'">-->
            <div class="cw-card-info">
                <div class="cw-card-name"><?= htmlspecialchars($sp['ten']) ?></div>
                <div class="cw-card-price">
                    <?= $gia ?>đ
                    <span class="cw-card-old"><?= $gia_goc ?>đ</span>
                    <span class="cw-card-badge">-<?= $sp['phan_tram_giam'] ?>%</span>
                </div>
                <div class="cw-card-desc"><?= htmlspecialchars(mb_substr($sp['mo_ta'], 0, 60)) ?>...</div>
            </div>
        </div>
    <?php endif; ?>

    <div id="chat-widget-messages">
        <!-- Tin nhắn chào tự động khác nhau tùy có sản phẩm hay không -->
        <?php if ($co_san_pham): ?>
            <div class="cw-bubble cw-bot">
                Bạn đang xem <strong><?= htmlspecialchars($sp['ten']) ?></strong>. Mình có thể tư vấn size, màu sắc hoặc so sánh với sản phẩm khác cho bạn! 😊
            </div>
        <?php else: ?>
            <div class="cw-bubble cw-bot">Chào bạn! AthleteHub có thể giúp gì cho hành trình tập luyện của bạn hôm nay? 💪</div>
        <?php endif; ?>
    </div>

    <div id="cw-suggestions">
        <!-- Gợi ý khác nhau: trang sản phẩm → hỏi về SP đó | trang khác → hỏi chung -->
        <?php if ($co_san_pham): ?>
            <button class="cw-suggest-btn" onclick="cwQuick('Size <?= htmlspecialchars($sp['ten']) ?> như thế nào?', this)">Tư vấn size</button>
            <button class="cw-suggest-btn" onclick="cwQuick('<?= htmlspecialchars($sp['ten']) ?> có bền không?', this)">Chất lượng thế nào?</button>
            <button class="cw-suggest-btn" onclick="cwQuick('Còn hàng <?= htmlspecialchars($sp['ten']) ?> không?', this)">Còn hàng không?</button>
            <button class="cw-suggest-btn" onclick="cwQuick('Có giảm thêm không?', this)">Có giảm thêm không?</button>
              <button class="cw-suggest-btn" onclick="cwQuick('Thanh toán như thế nào?', this)">Thanh toán như thế nào?</button>
        <?php else: ?>
            <button class="cw-suggest-btn" onclick="cwQuick('Sản phẩm nào đang nổi bật nhất?', this)">🔥 Nổi bật nhất</button>
            <button class="cw-suggest-btn" onclick="cwQuick('Sản phẩm nào bán chạy nhất shop?', this)">📦 Bán chạy nhất</button>
            <button class="cw-suggest-btn" onclick="cwQuick('Sản phẩm nào đang giảm giá nhiều nhất?', this)">💰 Giảm giá nhiều nhất</button>
            <button class="cw-suggest-btn" onclick="cwQuick('Tư vấn giúp tôi bộ đồ tập gym phù hợp', this)">🏋️ Bộ đồ tập gym</button>
            <button class="cw-suggest-btn" onclick="cwQuick('Có sản phẩm nào dưới 200 nghìn không?', this)">🎯 Dưới 200k</button>
            <button class="cw-suggest-btn" onclick="cwQuick('Sản phẩm nào phù hợp cho người mới tập?', this)">🌱 Mới bắt đầu tập</button>
        <?php endif; ?>
    </div>

    <div id="chat-widget-footer">
        <input type="text" id="cw-input" placeholder="Hỏi Shop về sản phẩm, size...">
        <button id="cw-send" onclick="cwSend()">➤</button>
    </div>
</div>

<script>
    // =========================================================
    // DATA SẢN PHẨM
    // =========================================================
    const CW_PRODUCT = <?= $co_san_pham
        ? json_encode([
            'ten'            => $sp['ten'],
            'gia'            => $sp['gia'],
            'gia_goc'        => $sp['gia_goc'],
            'phan_tram_giam' => $sp['phan_tram_giam'],
            'mo_ta'          => $sp['mo_ta'],
            'sizes'          => array_column($sizes ?? [], 'ten'),
            'colors'         => array_column($colors ?? [], 'ten'),
        ], JSON_UNESCAPED_UNICODE)
        : 'null' ?>;

    let CW_HISTORY = [];

    // =========================================================
    // TOGGLE CHAT
    // =========================================================
    function toggleChat() {
        const box = document.getElementById('chat-widget-box');
        const btn = document.getElementById('chat-widget-btn');

        const isOpen = box.style.display === 'flex';

        box.style.display = isOpen ? 'none' : 'flex';
        btn.style.display = isOpen ? 'flex' : 'none';

        if (!isOpen) {
            document.getElementById('cw-input').focus();
        }
    }

    // =========================================================
    // QUICK BUTTON
    // =========================================================
    function cwQuick(text, btn) {

        if (btn) {
            btn.style.display = 'none';

            const allBtns = document.querySelectorAll('#cw-suggestions .cw-suggest-btn');

            const stillVisible = [...allBtns].some(
                b => b.style.display !== 'none'
            );

            if (!stillVisible) {
                document.getElementById('cw-suggestions').style.display = 'none';
            }
        }

        document.getElementById('cw-input').value = text;

        cwSend();
    }

    // =========================================================
    // ESCAPE HTML
    // =========================================================
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // =========================================================
    // PARSE MARKDOWN
    // =========================================================
    function cwParseMarkdown(text) {

        if (!text) return '';

        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/^- (.+)/gm, '<div style="margin:3px 0;padding-left:10px">• $1</div>')
            .replace(/\n/g, '<br>');
    }

    // =========================================================
    // APPEND MESSAGE
    // =========================================================
    function cwAppend(sender, message) {

        const msgs = document.getElementById('chat-widget-messages');

        const div = document.createElement('div');

        div.className = 'cw-bubble ' +
            (sender === 'bot' ? 'cw-bot' : 'cw-user');

        if (sender === 'bot') {
            div.innerHTML = cwParseMarkdown(message);
        } else {
            div.textContent = message;
        }

        msgs.appendChild(div);

        msgs.scrollTop = msgs.scrollHeight;
    }

    // =========================================================
    // PRODUCT CARD
    // =========================================================
    function cwRenderProductCards(products, intro = null) {

        if (!products || !products.length) return;

        const msgs = document.getElementById('chat-widget-messages');

        const wrapper = document.createElement('div');

        wrapper.className = 'cw-product-suggestions';

        if (intro) {
            const label = document.createElement('div');

            label.className = 'cw-suggest-label';

            label.textContent = intro;

            wrapper.appendChild(label);
        }

        const row = document.createElement('div');

        row.className = 'cw-product-cards-row';

        products.forEach(sp => {

            const card = document.createElement('a');

            card.className = 'cw-sp-card';

            card.href = './product-detail.php?id=' + encodeURIComponent(sp.id);

            const gia = parseInt(sp.gia || 0).toLocaleString('vi-VN');

            const giaGoc = parseInt(sp.gia_goc || 0).toLocaleString('vi-VN');

    
            const hinh = sp.hinh_anh_chinh
                ? './public/' + sp.hinh_anh_chinh
                : './public/placeholder.svg';

            card.innerHTML = `
                <img src="${hinh}"
                     alt="${escHtml(sp.ten)}"
                     onerror="this.src='https://via.placeholder.com/60'">

                <div class="cw-sp-card-info">
                    <div class="cw-sp-card-name">
                        ${escHtml(sp.ten)}
                    </div>

                    <div class="cw-sp-card-price">
                        ${gia}đ

                        <span class="cw-sp-card-old">
                            ${giaGoc}đ
                        </span>

                        <span class="cw-sp-card-badge">
                            -${sp.phan_tram_giam || 0}%
                        </span>
                    </div>
                </div>

                <div class="cw-sp-card-arrow">›</div>
            `;

            row.appendChild(card);
        });

        wrapper.appendChild(row);

        msgs.appendChild(wrapper);

        msgs.scrollTop = msgs.scrollHeight;
    }

    // =========================================================
    // SEND MESSAGE
    // =========================================================
    async function cwSend() {

        const input = document.getElementById('cw-input');

        const msg = input.value.trim();

        if (!msg) return;

        cwAppend('user', msg);

        input.value = '';

        input.disabled = true;

        const sendBtn = document.getElementById('cw-send');

        sendBtn.disabled = true;

        // LOADING
        const msgs = document.getElementById('chat-widget-messages');

        const loading = document.createElement('div');

        loading.className = 'cw-bubble cw-bot';

        loading.id = 'cw-loading';

        loading.innerHTML = `
            <div class="cw-loading">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;

        msgs.appendChild(loading);

        msgs.scrollTop = msgs.scrollHeight;

        // =====================================================
        // FORM DATA
        // =====================================================
        const formData = new FormData();

        formData.append('message', msg);

        formData.append(
            'history',
            JSON.stringify(CW_HISTORY)
        );

        if (CW_PRODUCT) {
            formData.append(
                'product_context',
                JSON.stringify(CW_PRODUCT)
            );
        }

        try {

            // =================================================
            // FIX FETCH URL
            // =================================================
            const response = await fetch('./api/ai.php', {
                method: 'POST',
                body: formData
            });

            // DEBUG RESPONSE
            const rawText = await response.text();

            console.log(rawText);

            // XÓA LOADING
            document.getElementById('cw-loading')?.remove();

            let data;

            try {
                data = JSON.parse(rawText);
            } catch (jsonErr) {

                cwAppend(
                    'bot',
                    'Server trả về dữ liệu không hợp lệ!'
                );

                console.error(rawText);

                return;
            }

            // =================================================
            // SUCCESS
            // =================================================
            if (data.status === 'success') {

                CW_HISTORY.push({
                    role: 'user',
                    content: msg
                });

                CW_HISTORY.push({
                    role: 'assistant',
                    content: data.message
                });

                if (CW_HISTORY.length > 20) {
                    CW_HISTORY = CW_HISTORY.slice(-20);
                }

                if (data.message) {
                    cwAppend('bot', data.message);
                }

                if (data.products && data.products.length > 0) {
                    cwRenderProductCards(
                        data.products,
                        data.products_label || null
                    );
                }

            } else {

                cwAppend(
                    'bot',
                    data.message || 'Có lỗi xảy ra!'
                );
            }

        } catch (err) {

            document.getElementById('cw-loading')?.remove();

            console.error(err);

            cwAppend(
                'bot',
                'Không thể kết nối server!'
            );

        } finally {

            input.disabled = false;

            sendBtn.disabled = false;

            input.focus();
        }
    }

    // =========================================================
    // ENTER TO SEND
    // =========================================================
    document.getElementById('cw-input')
        .addEventListener('keypress', function (e) {

            if (
                e.key === 'Enter' &&
                !this.disabled
            ) {
                cwSend();
            }
        });
</script>
