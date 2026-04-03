

/* Clear Old LocalStorage */
localStorage.removeItem('cart');
localStorage.removeItem('cartDiscount');

/* Navbar Scroll Effect */
window.addEventListener('scroll', () => {
    document.querySelector('.navbar')?.classList.toggle('navbar-scrolled', window.scrollY > 50);
});

/* Back to Top Button */
const backToTopBtn = document.getElementById('backToTop');
if (backToTopBtn) {
    window.addEventListener('scroll', () => backToTopBtn.classList.toggle('show', window.scrollY > 300));
    backToTopBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

/* Event Delegation - Product Actions */
// Used for: products.php, Category_products.php, index.php, product-detail.php
document.addEventListener('click', function (e) {

    // Add to Cart button (btn-add-cart only)
    const btnAdd = e.target.closest('.btn-add-cart');
    if (btnAdd && !btnAdd.closest('.btn-add-to-cart-detail')) {
        e.preventDefault();

        // Get product ID - prioritize data-product-id on button, then on card
        const productId = parseInt(
            btnAdd.dataset.productId ||
            btnAdd.closest('.product-card, .product-card-small')?.dataset?.productId ||
            0
        );

        if (!productId) { console.warn('Product ID not found'); return; }

        const qty = parseInt(document.getElementById('quantity')?.value || 1);

        // Call addToCart() from cart.js
        if (typeof addToCart === 'function') {
            addToCart(productId, qty).then(() => {
                // Navigate to product detail after successful add
                setTimeout(() => {
                    window.location.href = `product-detail.php?id=${productId}`;
                }, 500);
            });
        }
    }

    // Wishlist button
    const btnWishlist = e.target.closest('.btn-wishlist, .btn-wishlist-detail');
    if (btnWishlist) {
        e.preventDefault();
        btnWishlist.classList.toggle('active');
        const name = btnWishlist.closest('.product-card, .product-card-small, .product-detail-info')
            ?.querySelector('.product-name, .detail-title')?.textContent?.trim();
        if (name) {
            const added = btnWishlist.classList.contains('active');
            showNotification(added ? `"${name}" đã thêm vào yêu thích!` : `"${name}" đã xóa khỏi yêu thích`, added ? 'success' : 'info');
        }
    }

    // Quick View and Buy Now buttons
    const btnQuickView = e.target.closest('.btn-quick-view');
    if (btnQuickView) {
        e.preventDefault();
        const card = btnQuickView.closest('.product-card');
        const id   = card?.dataset?.productId;
        window.location.href = id ? `ThanhToan.php?id=${id}` : 'ThanhToan.php';
    }

    const btnBuyNow = e.target.closest('.btn-buy-now-detail');
    if (btnBuyNow) {
        e.preventDefault();
        
        // Check if on product-detail page
        const isDetailPage = !!document.querySelector('.btn-add-to-cart-detail');
        
        if (isDetailPage) {
            // Validation for product detail page
            const sizeBtn = document.querySelector('.size-btn.active');
            const colorBtn = document.querySelector('.color-btn.active');
            
            // Check if product has variants
            const hasSizeOptions = document.querySelectorAll('.size-btn').length > 0;
            const hasColorOptions = document.querySelectorAll('.color-btn').length > 0;
            
            if (hasSizeOptions && !sizeBtn) {
                showNotification('Vui lòng chọn size!', 'danger');
                return;
            }
            
            if (hasColorOptions && !colorBtn) {
                showNotification('Vui lòng chọn màu sắc!', 'danger');
                return;
            }
            
            const productId = parseInt(document.querySelector('.btn-add-to-cart-detail').dataset.productId);
            const qty = parseInt(document.getElementById('quantity')?.value || 1);
            
            // Add to cart with variants data
            const sizeId = sizeBtn ? sizeBtn.dataset.sizeId : null;
            const colorId = colorBtn ? colorBtn.dataset.colorId : null;
            const sizeName = sizeBtn ? sizeBtn.dataset.sizeName : null;
            const colorName = colorBtn ? colorBtn.dataset.colorName : null;
            
            fetch('api/cart.php?action=add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: qty,
                    size_id: sizeId,
                    color_id: colorId,
                    size_name: sizeName,
                    color_name: colorName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Chuyển hướng tới trang thanh toán...', 'info');
                    setTimeout(() => {
                        window.location.href = 'ThanhToan.php';
                    }, 1000);
                } else {
                    showNotification(data.message || 'Lỗi khi thêm sản phẩm', 'danger');
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                showNotification('Lỗi kết nối server', 'danger');
            });
        } else {
            // For other pages (index, products), just redirect to detail page
            const productId = parseInt(
                btnBuyNow.dataset.productId ||
                btnBuyNow.closest('.product-card, .product-card-small')?.dataset?.productId ||
                0
            );
            if (productId) {
                window.location.href = `product-detail.php?id=${productId}`;
            }
        }
    }
});

// ══════════════════════════════════════════
// FILTER SẢN PHẨM (products.php)
// ══════════════════════════════════════════
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const cat = this.textContent.trim();
        document.querySelectorAll('.product-card').forEach((card, i) => {
            const cardCat = card.querySelector('.product-category')?.textContent?.trim();
            const show    = cat === 'Tất cả' || cardCat === cat;
            card.style.display = show ? 'block' : 'none';
            if (show) setTimeout(() => { card.style.animation = 'slideUp .4s ease-out'; }, 10);
        });
    });
});

// ══════════════════════════════════════════
// QUANTITY CONTROLS (product-detail.php)
// ══════════════════════════════════════════
function increaseQty() {
    const input = document.getElementById('quantity');
    if (!input) return;
    const max = parseInt(input.max) || 100;
    if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    if (!input) return;
    if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
}

// ══════════════════════════════════════════
// GALLERY (product-detail.php)
// ══════════════════════════════════════════
function changeImage(el) {
    const src = el.dataset.src;
    if (!src) return;
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

// ══════════════════════════════════════════
// NEWSLETTER
// ══════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.newsletter-form')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const emailInput = this.querySelector('.newsletter-input');
        if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput?.value)) {
            showNotification(`Đã đăng ký thành công với: ${emailInput.value}`, 'success');
            emailInput.value = '';
        } else {
            showNotification('Email không hợp lệ', 'danger');
        }
    });
});

// ══════════════════════════════════════════
// SMOOTH SCROLL
// ══════════════════════════════════════════
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', function (e) {
        const href   = this.getAttribute('href');
        const target = href !== '#' && document.querySelector(href);
        if (target) {
            e.preventDefault();
            window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
            document.querySelector('.navbar-collapse.show') &&
                document.querySelector('.navbar-toggler')?.click();
        }
    });
});

// ══════════════════════════════════════════
// NOTIFICATION (dùng chung với cart.js)
// ══════════════════════════════════════════
function showNotification(message, type = 'info') {
    const n = document.createElement('div');
    n.className = `alert-custom alert-${type}`;
    Object.assign(n.style, {
        position: 'fixed', top: '100px', right: '20px',
        zIndex: '9999', minWidth: '300px',
        animation: 'slideInRight .3s ease-out',
    });
    n.textContent = message;
    document.body.appendChild(n);
    setTimeout(() => {
        n.style.animation = 'slideOutRight .3s ease-out';
        setTimeout(() => { n.remove(); }, 300);
    }, 3000);
}

// ══════════════════════════════════════════
// ANIMATIONS CSS
// ══════════════════════════════════════════
if (!document.getElementById('scriptAnimStyle')) {
    const s = document.createElement('style');
    s.id = 'scriptAnimStyle';
    s.textContent = `
        @keyframes slideUp       { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
        @keyframes slideInRight  { from{opacity:0;transform:translateX(100px)} to{opacity:1;transform:translateX(0)} }
        @keyframes slideOutRight { from{opacity:1;transform:translateX(0)} to{opacity:0;transform:translateX(100px)} }
        @keyframes pulse         { 0%,100%{transform:scale(1)} 50%{transform:scale(1.2)} }
    `;
    document.head.appendChild(s);
}

console.log('✅ AthleteHub script.js đã khởi tạo!');