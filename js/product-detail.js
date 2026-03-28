// ===========================
// PRODUCT DETAIL PAGE JS
// ===========================

// ===========================
// CHANGE PRODUCT IMAGE
// ===========================

function changeImage(element) {
    // Remove active from all thumbnails
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active to clicked thumbnail
    element.classList.add('active');
    
    // Get the image src from thumbnail
    const imageSrc = element.querySelector('img').src;
    
    // Update main image
    const mainImage = document.getElementById('mainImage');
    mainImage.src = imageSrc;
    
    // Add animation
    mainImage.style.animation = 'fadeOut 0.3s ease-out';
    setTimeout(() => {
        mainImage.style.animation = 'fadeIn 0.3s ease-in';
    }, 150);
}

// ===========================
// QUANTITY SELECTOR
// ===========================

function increaseQty() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max) || 99;
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

// ===========================
// SIZE BUTTON SELECTION
// ===========================

document.querySelectorAll('.size-btn').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('.size-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        this.classList.add('active');
        
        const sizeName = this.dataset.sizeName || this.textContent;
        showNotification('Đã chọn size: ' + sizeName, 'info');
        
        // Check stock for selected combination
        checkStock();
    });
});

// ===========================
// COLOR BUTTON SELECTION
// ===========================

document.querySelectorAll('.color-btn').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('.color-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        this.classList.add('active');
        
        const colorName = this.dataset.colorName || this.title;
        showNotification('Đã chọn màu: ' + colorName, 'info');
        
        // Check stock for selected combination
        checkStock();
    });
});

// ===========================
// CHECK STOCK
// ===========================

function checkStock() {
    const sizeBtn = document.querySelector('.size-btn.active');
    const colorBtn = document.querySelector('.color-btn.active');
    
    if (!sizeBtn || !colorBtn) {
        return;
    }
    
    // Update quantity max based on stock
    const maxStock = parseInt(document.getElementById('quantity').max) || 99;
    const currentQty = parseInt(document.getElementById('quantity').value);
    
    if (currentQty > maxStock) {
        document.getElementById('quantity').value = maxStock;
    }
}

// ===========================
// ADD TO CART
// ===========================

const addToCartBtn = document.querySelector('.btn-add-to-cart-detail');
if (addToCartBtn) {
    addToCartBtn.addEventListener('click', function() {
        const productId = parseInt(this.dataset.productId);
        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        const sizeBtn = document.querySelector('.size-btn.active');
        const colorBtn = document.querySelector('.color-btn.active');
        
        // Check if product has variants (size or color buttons exist)
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
        
        // Gọi API để thêm vào giỏ
        fetch('api/cart.php?action=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const size = sizeBtn ? (sizeBtn.dataset.sizeName || sizeBtn.textContent) : '';
                const color = colorBtn ? (colorBtn.dataset.colorName || colorBtn.title) : '';
                
                let variantText = '';
                if (size && color) {
                    variantText = ' (' + size + '/' + color + ')';
                } else if (size) {
                    variantText = ' (Size: ' + size + ')';
                } else if (color) {
                    variantText = ' (Màu: ' + color + ')';
                }
                
                showNotification(data.message + variantText + ' (' + quantity + ' sản phẩm)', 'success');
                updateCartCount();
                document.getElementById('quantity').value = 1;
            } else {
                showNotification(data.message || 'Lỗi khi thêm sản phẩm', 'danger');
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            showNotification('Lỗi kết nối server', 'danger');
        });
    });
}

// ===========================
// BUY NOW
// ===========================

const buyNowBtn = document.querySelector('.btn-buy-now-detail');
if (buyNowBtn) {
    buyNowBtn.addEventListener('click', function() {
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
        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        
        // Thêm vào giỏ rồi chuyển sang thanh toán
        fetch('api/cart.php?action=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
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
    });
}

// ===========================
// WISHLIST
// ===========================

const wishlistBtn = document.getElementById('wishlistBtn');
if (wishlistBtn) {
    wishlistBtn.addEventListener('click', function() {
        this.classList.toggle('active');
        
        const icon = this.querySelector('i');
        if (this.classList.contains('active')) {
            icon.classList.remove('far');
            icon.classList.add('fas');
            showNotification('Đã thêm vào danh sách yêu thích!', 'success');
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
            showNotification('Đã xóa khỏi danh sách yêu thích', 'info');
        }
    });
}

// ===========================
// TABS
// ===========================

document.querySelectorAll('[role="presentation"] button').forEach(button => {
    button.addEventListener('click', function() {
        const tabsSection = document.querySelector('.product-tabs-section');
        if (tabsSection) {
            tabsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// ===========================
// RATING STARS
// ===========================

const ratingStars = document.querySelectorAll('.rating-input i');

ratingStars.forEach((star, index) => {
    star.addEventListener('click', function() {
        ratingStars.forEach((s, i) => {
            if (i <= index) {
                s.classList.remove('far');
                s.classList.add('fas', 'active');
            } else {
                s.classList.remove('fas', 'active');
                s.classList.add('far');
            }
        });
    });
    
    star.addEventListener('mouseover', function() {
        ratingStars.forEach((s, i) => {
            if (i <= index) {
                s.classList.add('active');
            } else {
                s.classList.remove('active');
            }
        });
    });
});

const ratingInput = document.querySelector('.rating-input');
if (ratingInput) {
    ratingInput.addEventListener('mouseleave', function() {
        const active = document.querySelectorAll('.rating-input i.fas');
        ratingStars.forEach(star => star.classList.remove('active'));
        active.forEach(star => star.classList.add('active'));
    });
}

// ===========================
// REVIEW FORM
// ===========================

const reviewForm = document.querySelector('.review-form');
if (reviewForm) {
    reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const titleInput = this.querySelector('input[placeholder="Viết tiêu đề đánh giá..."]');
        const contentInput = this.querySelector('textarea');
        
        if (!titleInput || !titleInput.value || !contentInput || !contentInput.value) {
            showNotification('Vui lòng điền đầy đủ thông tin!', 'danger');
            return;
        }
        
        const ratingCount = document.querySelectorAll('.rating-input i.fas').length;
        if (ratingCount === 0) {
            showNotification('Vui lòng chọn số sao đánh giá!', 'danger');
            return;
        }
        
        showNotification('Cảm ơn bạn đã đánh giá! Đánh giá sẽ được xem xét và công bố trong 24h.', 'success');
        
        // Reset form
        this.reset();
        document.querySelectorAll('.rating-input i').forEach(star => {
            star.classList.remove('fas', 'active');
            star.classList.add('far');
        });
    });
}

// ===========================
// HELPFUL BUTTONS
// ===========================

document.querySelectorAll('.helpful-btn').forEach(button => {
    button.addEventListener('click', function() {
        this.style.color = 'var(--primary-color)';
        this.disabled = true;
        showNotification('Cảm ơn phản hồi của bạn!', 'success');
    });
});

// ===========================
// SHARE BUTTONS
// ===========================

document.querySelectorAll('.share-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        const productName = document.querySelector('.detail-title').textContent;
        const productPrice = document.querySelector('.price-current').textContent;
        
        const shareText = 'Tôi đang xem ' + productName + ' (' + productPrice + ') trên AthleteHub';
        const currentUrl = window.location.href;
        
        const icon = this.querySelector('i');
        
        if (icon.classList.contains('fa-facebook-f')) {
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(currentUrl), '_blank');
        } else if (icon.classList.contains('fa-twitter')) {
            window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareText) + '&url=' + encodeURIComponent(currentUrl), '_blank');
        } else if (icon.classList.contains('fa-pinterest')) {
            window.open('https://pinterest.com/pin/create/button/?url=' + encodeURIComponent(currentUrl) + '&description=' + encodeURIComponent(shareText), '_blank');
        } else if (icon.classList.contains('fa-linkedin-in')) {
            window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(currentUrl), '_blank');
        }
        
        showNotification('Đang chia sẻ...', 'info');
    });
});

// ===========================
// RELATED PRODUCTS - Quick Add
// ===========================

document.querySelectorAll('.product-card-small .btn-add-quick').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        const productCard = this.closest('.product-card-small');
        const productName = productCard.querySelector('h4').textContent;
        
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        const product = {
            id: Date.now(),
            name: productName,
            quantity: 1
        };
        
        cart.push(product);
        localStorage.setItem('cart', JSON.stringify(cart));
        
        updateCartCount();
        showNotification(productName + ' đã được thêm vào giỏ hàng!', 'success');
    });
});

// ===========================
// UTILITIES
// ===========================

function showNotification(message, type) {
    type = type || 'info';
    
    // Remove existing notifications
    const existing = document.querySelector('.alert-custom');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = 'alert-custom alert-' + type;
    notification.style.position = 'fixed';
    notification.style.top = '100px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.style.padding = '15px 20px';
    notification.style.borderRadius = '8px';
    notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    notification.style.animation = 'slideInRight 0.3s ease-out';
    
    if (type === 'success') {
        notification.style.backgroundColor = '#28a745';
    } else if (type === 'danger') {
        notification.style.backgroundColor = '#dc3545';
    } else if (type === 'warning') {
        notification.style.backgroundColor = '#ffc107';
        notification.style.color = '#000';
    } else {
        notification.style.backgroundColor = '#17a2b8';
    }
    
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function updateCartCount() {
    fetch('api/cart.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(function(el) {
                    el.textContent = data.cart_count || 0;
                });
            }
        })
        .catch(error => console.error('Lỗi cập nhật cart count:', error));
}

// ===========================
// ANIMATIONS
// ===========================

if (!document.getElementById('product-detail-styles')) {
    const style = document.createElement('style');
    style.id = 'product-detail-styles';
    style.textContent = '' +
        '@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } } ' +
        '@keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } } ' +
        '@keyframes slideInRight { from { opacity: 0; transform: translateX(100px); } to { opacity: 1; transform: translateX(0); } } ' +
        '@keyframes slideOutRight { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100px); } } ' +
        '.rating-input i { cursor: pointer; transition: transform 0.2s; } ' +
        '.rating-input i:hover { transform: scale(1.2); }';
    document.head.appendChild(style);
}

// ===========================
// INITIALIZE
// ===========================

document.addEventListener('DOMContentLoaded', function() {
    // Clear old localStorage data
    localStorage.removeItem('cart');
    
    // Set first size and color as active if only one option
    const sizeBtns = document.querySelectorAll('.size-btn');
    const colorBtns = document.querySelectorAll('.color-btn');
    
    if (sizeBtns.length === 1) {
        sizeBtns[0].classList.add('active');
    }
    
    if (colorBtns.length === 1) {
        colorBtns[0].classList.add('active');
    }
    
    // Update cart count from backend
    updateCartCount();
    
    console.log('Trang chi tiet san pham da duoc khoi tao!');
});

// Update cart count on page load (keep for backup)
updateCartCount();

