// ===========================
// GENERAL FUNCTIONS
// ===========================

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
    } else {
        navbar.classList.remove('navbar-scrolled');
    }
});

// Back to top button
const backToTopBtn = document.getElementById('backToTop');

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

// ===========================
// PRODUCT CART FUNCTIONALITY
// ===========================

const cartIcon = document.querySelector('.cart-count');

// Add to cart buttons
document.querySelectorAll('.btn-add-cart').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const productCard = this.closest('.product-card');
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
        cartIcon.parentElement.style.animation = 'none';
        setTimeout(() => {
            cartIcon.parentElement.style.animation = 'pulse 0.5s ease-in-out';
        }, 10);
    });
});

// Update cart count from localStorage
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cartIcon) {
        cartIcon.textContent = cart.length;
    }
}

// Initialize cart count on page load
updateCartCount();

// ===========================
// WISHLIST FUNCTIONALITY
// ===========================

document.querySelectorAll('.btn-wishlist').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        this.classList.toggle('active');
        
        const productName = this.closest('.product-card').querySelector('.product-name').textContent;
        
        if (this.classList.contains('active')) {
            showNotification(`${productName} đã được thêm vào danh sách yêu thích!`, 'success');
        } else {
            showNotification(`${productName} đã bị xóa khỏi danh sách yêu thích`, 'info');
        }
    });
});

// ===========================
// QUICK VIEW FUNCTIONALITY
// ===========================

document.querySelectorAll('.btn-quick-view').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const productCard = this.closest('.product-card');
        const productName = productCard.querySelector('.product-name').textContent;
        const productPrice = productCard.querySelector('.price-current').textContent;
        const productCategory = productCard.querySelector('.product-category').textContent;
        
        showQuickViewModal(productName, productPrice, productCategory);
    });
});

// ===========================
// FILTER FUNCTIONALITY
// ===========================

document.querySelectorAll('.filter-btn').forEach(button => {
    button.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Get filter category
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
    // Create notification element
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
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// ===========================
// QUICK VIEW MODAL
// ===========================

function showQuickViewModal(productName, productPrice, productCategory) {
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'quickViewModal';
    modal.tabIndex = '-1';
    modal.style.display = 'block';
    
    modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: none; border-radius: var(--border-radius-lg);">
                <div class="modal-header" style="border: none; padding: 20px;">
                    <h5 class="modal-title" style="color: var(--dark);">Xem nhanh sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div style="background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%); 
                                height: 300px; 
                                border-radius: var(--border-radius); 
                                display: flex; 
                                align-items: center; 
                                justify-content: center; 
                                margin-bottom: 20px;
                                font-size: 5rem;
                                color: #999;">
                        <i class="fas fa-image"></i>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <span style="color: var(--primary); font-weight: bold; text-transform: uppercase; font-size: 0.9rem;">${productCategory}</span>
                        <h3 style="color: var(--dark); margin: 10px 0; font-weight: 800;">${productName}</h3>
                        <div style="display: flex; align-items: center; gap: 10px; margin: 15px 0;">
                            <span style="font-size: 1.5rem; color: var(--primary); font-weight: 800;">${productPrice}</span>
                            <span style="color: #999; text-decoration: line-through;">1.499.000₫</span>
                        </div>
                        <div style="display: flex; gap: 5px; margin: 15px 0;">
                            <i class="fas fa-star" style="color: var(--accent);"></i>
                            <i class="fas fa-star" style="color: var(--accent);"></i>
                            <i class="fas fa-star" style="color: var(--accent);"></i>
                            <i class="fas fa-star" style="color: var(--accent);"></i>
                            <i class="fas fa-star-half" style="color: var(--accent);"></i>
                            <span style="color: var(--gray); margin-left: 10px;">(120 đánh giá)</span>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <p style="color: var(--gray); line-height: 1.6;">Sản phẩm chất lượng cao được chế tạo với những vật liệu tốt nhất và công nghệ hiện đại nhất. Phù hợp cho mọi loại hoạt động thể thao.</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button style="flex: 1; padding: 12px; background: var(--primary); color: white; border: none; border-radius: var(--border-radius); font-weight: bold; cursor: pointer; text-transform: uppercase;">
                            <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                        </button>
                        <button style="padding: 12px 20px; background: transparent; color: var(--primary); border: 2px solid var(--primary); border-radius: var(--border-radius); font-weight: bold; cursor: pointer;">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Remove modal from DOM when hidden
    modal.addEventListener('hidden.bs.modal', function() {
        modal.remove();
    });
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
            
            // Simple email validation
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
            const offsetTop = target.offsetTop - 80; // Offset for navbar
            
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
            
            // Close mobile menu if open
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse.classList.contains('show')) {
                const toggleBtn = document.querySelector('.navbar-toggler');
                toggleBtn.click();
            }
        }
    });
});

// ===========================
// ANIMATIONS
// ===========================

// Define slideUp animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
`;
document.head.appendChild(style);

// ===========================
// INITIALIZE
// ===========================

console.log('✅ Website bán đồ thể thao đã được khởi tạo thành công!');
