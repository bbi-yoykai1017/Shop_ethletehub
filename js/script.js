// ===========================
// NAVBAR SCROLL EFFECT
// ===========================
 
window.addEventListener('scroll', function () {
    const navbar = document.querySelector('.navbar');
    if (navbar) navbar.classList.toggle('navbar-scrolled', window.scrollY > 50);
});
 
// ===========================
// BACK TO TOP
// ===========================
 
const backToTopBtn = document.getElementById('backToTop');
if (backToTopBtn) {
    window.addEventListener('scroll', () => {
        backToTopBtn.classList.toggle('show', window.scrollY > 300);
    });
    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
 
// ===========================
// EVENT DELEGATION — XỬ LÝ CLICK SẢN PHẨM
// ===========================
 
document.addEventListener('click', function (e) {
 
    // 1. NÚT THÊM VÀO GIỎ
    const btnAddCart = e.target.closest('.btn-add-cart');
    if (btnAddCart) {
        e.preventDefault();
        const card = btnAddCart.closest('.product-card');
        if (!card) return;
 
        // Đọc thông tin sản phẩm từ data-* attributes của .product-card
        // HTML cần có: data-id, data-ten, data-price, data-hinh-anh, data-danh-muc
        const product = {
            id:             parseInt(card.dataset.productId || card.dataset.id || 0),
            ten:            card.dataset.ten  || card.querySelector('.product-name')?.textContent?.trim() || 'Sản phẩm',
            price:          parseFloat(card.dataset.price || 0),
            hinh_anh_chinh: card.dataset.hinhAnh || card.querySelector('img')?.src || 'images/placeholder.jpg',
            danh_muc:       card.dataset.danhMuc  || card.querySelector('.product-category')?.textContent?.trim() || 'Sản phẩm',
        };
 
        if (!product.id) {
            console.warn('Sản phẩm thiếu ID:', card);
            return;
        }
 
        // addToCart được định nghĩa trong cart.js (gọi PHP session)
        if (typeof addToCart === 'function') {
            addToCart(product);
        }
    }
 
    // 2. NÚT YÊU THÍCH (WISHLIST)
    const btnWishlist = e.target.closest('.btn-wishlist');
    if (btnWishlist) {
        e.preventDefault();
        btnWishlist.classList.toggle('active');
        const name = btnWishlist.closest('.product-card')?.querySelector('.product-name')?.textContent?.trim();
        if (name) {
            const added = btnWishlist.classList.contains('active');
            showNotification(
                added ? `"${name}" đã được thêm vào yêu thích!` : `"${name}" đã bị xóa khỏi yêu thích`,
                added ? 'success' : 'info'
            );
        }
    }
 
    // 3. NÚT XEM NHANH (QUICK VIEW)
    const btnQuickView = e.target.closest('.btn-quick-view');
    if (btnQuickView) {
        e.preventDefault();
        const card = btnQuickView.closest('.product-card');
        const id = card?.dataset.productId || card?.dataset.id;
        window.location.href = id ? `product-detail.php?id=${id}` : 'product-detail.php';
    }
});
 
// ===========================
// FILTER SẢN PHẨM
// ===========================
 
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
 
        const cat = this.textContent.trim();
        document.querySelectorAll('.product-card').forEach((card, i) => {
            const cardCat = card.querySelector('.product-category')?.textContent?.trim();
            const show    = cat === 'Tất cả' || cardCat === cat;
            card.style.display = show ? 'block' : 'none';
            if (show) {
                card.style.animation = 'none';
                setTimeout(() => {
                    card.style.animation = `slideUp 0.4s ease-out`;
                    card.style.animationDelay = `${i * 0.04}s`;
                }, 10);
            }
        });
    });
});
 
// ===========================
// NEWSLETTER FORM
// ===========================
 
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.newsletter-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const emailInput = this.querySelector('.newsletter-input');
            const email = emailInput?.value?.trim();
            if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showNotification(`Cảm ơn! Đã đăng ký với email: ${email}`, 'success');
                emailInput.value = '';
            } else {
                showNotification('Vui lòng nhập email hợp lệ', 'danger');
            }
        });
    }
});
 
// ===========================
// SMOOTH SCROLL
// ===========================
 
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        const target = href !== '#' && document.querySelector(href);
        if (target) {
            e.preventDefault();
            window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
            document.querySelector('.navbar-collapse.show') &&
                document.querySelector('.navbar-toggler')?.click();
        }
    });
});
 
// ===========================
// NOTIFICATION SYSTEM
// ===========================
 
function showNotification(message, type = 'info') {
    const n = document.createElement('div');
    n.className = `alert-custom alert-${type}`;
    Object.assign(n.style, {
        position: 'fixed', top: '100px', right: '20px',
        zIndex: '9999', minWidth: '300px',
        animation: 'slideInRight 0.3s ease-out',
    });
    n.textContent = message;
    document.body.appendChild(n);
    setTimeout(() => {
        n.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => n.remove(), 300);
    }, 3000);
}
 
// ===========================
// ANIMATIONS
// ===========================
 
const _style = document.createElement('style');
_style.textContent = `
    @keyframes slideUp {
        from { opacity:0; transform:translateY(30px); }
        to   { opacity:1; transform:translateY(0); }
    }
    @keyframes slideInRight {
        from { opacity:0; transform:translateX(100px); }
        to   { opacity:1; transform:translateX(0); }
    }
    @keyframes slideOutRight {
        from { opacity:1; transform:translateX(0); }
        to   { opacity:0; transform:translateX(100px); }
    }
    @keyframes pulse {
        0%,100% { transform:scale(1); }
        50%      { transform:scale(1.2); }
    }
`;
document.head.appendChild(_style);
 
console.log('✅ AthleteHub script.js đã khởi tạo!');