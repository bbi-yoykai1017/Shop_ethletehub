// ===========================
// GENERAL FUNCTIONS
// ===========================

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    }
});

// Back to top button
const backToTopBtn = document.getElementById('backToTop');
if (backToTopBtn) {
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });

    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ===========================
// CART FUNCTIONS
// ===========================

// Update cart count from localStorage
function updateCartCount() {
    const cartIcon = document.querySelector('.cart-count');
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cartIcon) {
        cartIcon.textContent = cart.length;
    }
}

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});

// ===========================
// EVENT DELEGATION (XỬ LÝ CLICK CHO TẤT CẢ CÁC NÚT TRONG SẢN PHẨM)
// ===========================
document.addEventListener('click', function(e) {
    
    // 1. XỬ LÝ NÚT THÊM VÀO GIỎ HÀNG
    const btnAddCart = e.target.closest('.btn-add-cart');
    if (btnAddCart) {
        e.preventDefault();
        const productCard = btnAddCart.closest('.product-card');
        if (productCard) {
            const productName = productCard.querySelector('.product-name').textContent;
            const productPrice = productCard.querySelector('.price-current').textContent;
            
            // Add to cart (localStorage)
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const product = {
                id: Date.now(),
                name: productName,
                price: parseFloat(productPrice.replace(/\D/g, '')),
                quantity: 1
            };
            cart.push(product);
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Update cart count
            updateCartCount();
            
            // Show notification
            showNotification(`${productName} đã được thêm vào giỏ hàng!`, 'success');
            
            // Add animation
            const cartIcon = document.querySelector('.cart-count');
            if (cartIcon) {
                cartIcon.parentElement.style.animation = 'none';
                setTimeout(() => {
                    cartIcon.parentElement.style.animation = 'pulse 0.5s ease-in-out';
                }, 10);
            }
        }
    }

    // 2. XỬ LÝ NÚT YÊU THÍCH (WISHLIST)
    const btnWishlist = e.target.closest('.btn-wishlist');
    if (btnWishlist) {
        e.preventDefault();
        btnWishlist.classList.toggle('active');
        
        const productCard = btnWishlist.closest('.product-card');
        if (productCard) {
            const productName = productCard.querySelector('.product-name').textContent;
            
            if (btnWishlist.classList.contains('active')) {
                showNotification(`${productName} đã được thêm vào danh sách yêu thích!`, 'success');
            } else {
                showNotification(`${productName} đã bị xóa khỏi danh sách yêu thích`, 'info');
            }
        }
    }

    // 3. XỬ LÝ NÚT XEM NHANH (QUICK VIEW)
    const btnQuickView = e.target.closest('.btn-quick-view');
    if (btnQuickView) {
        e.preventDefault();
        const productCard = btnQuickView.closest('.product-card');
        if (productCard) {
            const productId = productCard.getAttribute('data-product-id');
            
            // Chuyển hướng sang trang chi tiết với ID
            if (productId) {
                window.location.href = 'product-detail.php?id=' + productId;
            } else {
                window.location.href = 'product-detail.php';
            }
        }
    }
});

// ===========================
// FILTER FUNCTIONALITY
// ===========================

document.querySelectorAll('.filter-btn').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        this.classList.add('active');
        const category = this.textContent.trim();
        
        if (category === 'Tất cả') {
            showAllProducts();
        } else {
            filterProducts(category);
        }
    });
});

function filterProducts(category) {
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach((card, index) => {
        const productCategory = card.querySelector('.product-category').textContent.trim();
        
        if (productCategory === category) {
            card.style.animation = 'none';
            setTimeout(() => {
                card.style.display = 'block';
                card.style.animation = 'slideUp 0.5s ease-out';
            }, 10);
        } else {
            card.style.display = 'none';
        }
    });
}

function showAllProducts() {
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach((card, index) => {
        card.style.display = 'block';
        card.style.animation = 'none';
        setTimeout(() => {
            card.style.animation = `slideUp 0.5s ease-out`;
            card.style.animationDelay = `${index * 0.05}s`;
        }, 10);
    });
}

// ===========================
// NOTIFICATION SYSTEM
// ===========================

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert-custom alert-${type}`;
    notification.style.position = 'fixed';
    notification.style.top = '100px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.style.animation = 'slideInRight 0.3s ease-out';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// ===========================
// NEWSLETTER FORM
// ===========================

document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.querySelector('.newsletter-form');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('.newsletter-input');
            const email = emailInput.value;
            
            if (isValidEmail(email)) {
                showNotification(`Cảm ơn! Bạn đã đăng ký nhận tin với email: ${email}`, 'success');
                emailInput.value = '';
            } else {
                showNotification('Vui lòng nhập một địa chỉ email hợp lệ', 'danger');
            }
        });
    }
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// ===========================
// SMOOTH SCROLL FOR NAVIGATION LINKS
// ===========================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        if (href !== '#' && document.querySelector(href)) {
            e.preventDefault();
            
            const target = document.querySelector(href);
            const offsetTop = target.offsetTop - 80;
            
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
            
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                const toggleBtn = document.querySelector('.navbar-toggler');
                if(toggleBtn) toggleBtn.click();
            }
        }
    });
});

// ===========================
// ANIMATIONS
// ===========================

const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(100px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOutRight {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100px); }
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
`;
document.head.appendChild(style);

console.log('✅ Website bán đồ thể thao đã được khởi tạo thành công!');