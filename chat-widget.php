<?php
// Nhận thông tin sản phẩm từ trang (nếu có)
$co_san_pham = isset($sp) && $sp;
?>

<style>
#chat-widget-btn {
    position: fixed;
    bottom: 28px; right: 28px;
    width: 58px; height: 58px;
    background: #f97316;
    border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 20px rgba(249,115,22,0.5);
    z-index: 99999; border: none; font-size: 26px;
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
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
}

/* Desktop */
@media (min-width: 769px) {
    #chat-widget-box {
        bottom: 95px; right: 28px;
        width: 360px; height: 560px;
        border-radius: 18px;
    }
}
/* Tablet */
@media (min-width: 481px) and (max-width: 768px) {
    #chat-widget-btn { bottom: 20px; right: 20px; width: 52px; height: 52px; }
    #chat-widget-box {
        bottom: 0; right: 0; left: 0;
        width: 100%; height: 65vh;
        border-radius: 20px 20px 0 0;
    }
}
/* Mobile */
@media (max-width: 480px) {
    #chat-widget-btn { bottom: 18px; right: 18px; width: 50px; height: 50px; font-size: 20px; }
    #chat-widget-box {
        top: 0; left: 0; right: 0; bottom: 0;
        width: 100%; height: 100%; border-radius: 0;
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
    background: rgba(255,255,255,0.15);
    border: none; color: white;
    width: 30px; height: 30px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
}
#chat-close-btn:hover { background: rgba(255,255,255,0.3); }

/* Card sản phẩm */
#cw-product-card {
    margin: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    gap: 10px;
    padding: 10px;
    background: #fffbf7;
    flex-shrink: 0;
    cursor: pointer;
    transition: box-shadow 0.2s;
}
#cw-product-card:hover { box-shadow: 0 2px 12px rgba(249,115,22,0.15); }
#cw-product-card img {
    width: 64px; height: 64px;
    object-fit: cover; border-radius: 8px;
    flex-shrink: 0;
}
.cw-card-info { flex: 1; min-width: 0; }
.cw-card-name {
    font-size: 13px; font-weight: 600;
    color: #1e3a5f;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.cw-card-price { font-size: 14px; font-weight: bold; color: #f97316; margin-top: 3px; }
.cw-card-old { font-size: 11px; color: #999; text-decoration: line-through; }
.cw-card-badge {
    display: inline-block; background: #ef4444; color: white;
    font-size: 10px; padding: 1px 6px; border-radius: 10px; margin-left: 4px;
}
.cw-card-desc { font-size: 11px; color: #666; margin-top: 3px; line-height: 1.3; }

#chat-widget-messages {
    flex: 1; overflow-y: auto;
    padding: 12px; background: #f4f7f6;
    display: flex; flex-direction: column; gap: 8px;
    -webkit-overflow-scrolling: touch;
}
#chat-widget-messages::-webkit-scrollbar { width: 4px; }
#chat-widget-messages::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }

.cw-bubble {
    max-width: 80%; padding: 10px 14px;
    border-radius: 16px; font-size: 13.5px;
    line-height: 1.55; word-break: break-word;
    animation: cwFade 0.2s ease;
}
@keyframes cwFade {
    from { opacity: 0; transform: translateY(5px); }
    to   { opacity: 1; transform: translateY(0); }
}
.cw-bot {
    background: white; border: 1px solid #e5e7eb;
    align-self: flex-start; border-bottom-left-radius: 4px;
}
.cw-user {
    background: #f97316; color: white;
    align-self: flex-end; border-bottom-right-radius: 4px;
}
.cw-loading { display: flex; gap: 4px; align-items: center; padding: 12px 16px; }
.cw-loading span {
    width: 7px; height: 7px; background: #bbb;
    border-radius: 50%; animation: cwDot 1.2s infinite;
}
.cw-loading span:nth-child(2) { animation-delay: 0.2s; }
.cw-loading span:nth-child(3) { animation-delay: 0.4s; }
@keyframes cwDot {
    0%,80%,100% { transform: scale(0.7); opacity: 0.5; }
    40% { transform: scale(1); opacity: 1; }
}

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

<!-- NÚT MỞ CHAT -->
<button id="chat-widget-btn" onclick="toggleChat()" aria-label="Chat với AthleteHub">💬</button>

<div id="chat-widget-box">
    <!-- HEADER -->
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

    <?php if ($co_san_pham): 
 $hinh = '/Shop_ethletehub/public/' . $sp['hinh_anh_chinh'];
        $gia = number_format($sp['gia'], 0, ',', '.');
        $gia_goc = number_format($sp['gia_goc'], 0, ',', '.');
    ?>
    <!-- CARD SẢN PHẨM -->
    <div id="cw-product-card">
  <img src="<?= $hinh ?>" 
     alt="<?= htmlspecialchars($sp['ten']) ?>"
     onerror="this.onerror=null; this.src='/Shop_ethletehub/public/placeholder.svg'">
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

    <!-- MESSAGES -->
    <div id="chat-widget-messages">
        <?php if ($co_san_pham): ?>
        <div class="cw-bubble cw-bot">
            Bạn đang xem <strong><?= htmlspecialchars($sp['ten']) ?></strong>. Mình có thể tư vấn size, màu sắc hoặc so sánh với sản phẩm khác cho bạn! 😊
        </div>
        <?php else: ?>
        <div class="cw-bubble cw-bot">Chào bạn! AthleteHub có thể giúp gì cho hành trình tập luyện của bạn hôm nay? 💪</div>
        <?php endif; ?>
    </div>

    <!-- GỢI Ý NHANH -->
    <div id="cw-suggestions">
        <?php if ($co_san_pham): ?>
        <button class="cw-suggest-btn" onclick="cwQuick('Size <?= htmlspecialchars($sp['ten']) ?> như thế nào?')">Tư vấn size</button>
        <button class="cw-suggest-btn" onclick="cwQuick('<?= htmlspecialchars($sp['ten']) ?> có bền không?')">Chất lượng thế nào?</button>
        <button class="cw-suggest-btn" onclick="cwQuick('Còn hàng <?= htmlspecialchars($sp['ten']) ?> không?')">Còn hàng không?</button>
        <button class="cw-suggest-btn" onclick="cwQuick('Có giảm thêm không?')">Có giảm thêm không?</button>
        <?php else: ?>
        <button class="cw-suggest-btn" onclick="cwQuick('Còn hàng không?')">Còn hàng không?</button>
        <button class="cw-suggest-btn" onclick="cwQuick('Có COD không?')">Có COD không?</button>
        <button class="cw-suggest-btn" onclick="cwQuick('Giao hàng bao lâu?')">Giao hàng bao lâu?</button>
        <button class="cw-suggest-btn" onclick="cwQuick('Tư vấn chọn size')">Tư vấn size</button>
        <button class="cw-suggest-btn" onclick="cwQuick('Có giảm giá không?')">Có giảm giá không?</button>
        <?php endif; ?>
    </div>

    <!-- INPUT -->
    <div id="chat-widget-footer">
        <input type="text" id="cw-input" placeholder="Hỏi Shop về sản phẩm, size...">
        <button id="cw-send" onclick="cwSend()">➤</button>
    </div>
</div>

<script>
// Truyền thông tin sản phẩm vào JS
const CW_PRODUCT = <?= $co_san_pham 
    ? json_encode(['ten' => $sp['ten'], 'gia' => $sp['gia'], 'mo_ta' => $sp['mo_ta']]) 
    : 'null' ?>;

function toggleChat() {
    const box = document.getElementById('chat-widget-box');
    const btn = document.getElementById('chat-widget-btn');
    const isOpen = box.style.display === 'flex';
    box.style.display = isOpen ? 'none' : 'flex';
    btn.style.display = isOpen ? 'flex' : 'none';
    if (!isOpen) document.getElementById('cw-input').focus();
}

function cwQuick(text) {
    document.getElementById('cw-suggestions').style.display = 'none';
    document.getElementById('cw-input').value = text;
    cwSend();
}

async function cwSend() {
    const input = document.getElementById('cw-input');
    const msg = input.value.trim();
    if (!msg) return;

    cwAppend('user', msg);
    input.value = '';
    input.disabled = true;
    document.getElementById('cw-send').disabled = true;

    // Loading
    const msgs = document.getElementById('chat-widget-messages');
    const lid = 'cwl' + Date.now();
    const ld = document.createElement('div');
    ld.className = 'cw-bubble cw-bot cw-loading';
    ld.id = lid;
    ld.innerHTML = '<span></span><span></span><span></span>';
    msgs.appendChild(ld);
    msgs.scrollTop = msgs.scrollHeight;

    const formData = new FormData();
    formData.append('message', msg);
    // Truyền thêm context sản phẩm nếu có
    if (CW_PRODUCT) formData.append('product_context', JSON.stringify(CW_PRODUCT));

    try {
        const res = await fetch('/Shop_ethletehub/api-test.php', { method: 'POST', body: formData });
        const data = await res.json();
        document.getElementById(lid)?.remove();
        cwAppend('bot', data.status === 'success' ? data.message : 'Xin lỗi, có lỗi xảy ra!');
    } catch(e) {
        document.getElementById(lid)?.remove();
        cwAppend('bot', 'Không thể kết nối. Vui lòng thử lại!');
    }

    input.disabled = false;
    document.getElementById('cw-send').disabled = false;
    input.focus();
}

function cwAppend(sender, message) {
    const msgs = document.getElementById('chat-widget-messages');
    const div = document.createElement('div');
    div.className = 'cw-bubble ' + (sender === 'bot' ? 'cw-bot' : 'cw-user');
    div.textContent = message;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

document.getElementById('cw-input').addEventListener('keypress', e => {
    if (e.key === 'Enter' && !e.target.disabled) cwSend();
});
</script>