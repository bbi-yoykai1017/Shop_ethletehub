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
    input.value = parseInt(input.value) + 1;
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
        showNotification(`Đã chọn size: ${this.textContent}`, 'info');
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
        showNotification('Đã chọn màu sắc', 'info');
    });
});

// ===========================
// ADD TO CART
// ===========================

document.querySelector('.btn-add-to-cart-detail')?.addEventListener('click', function() {
    const quantity = document.getElementById('quantity').value;
    const size = document.querySelector('.size-btn.active')?.textContent;
    const productName = document.querySelector('.detail-title').textContent;
    
    if (!size) {
        showNotification('Vui lòng chọn size!', 'danger');
        return;
    }
    
    // Add to cart logic
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    const product = {
        id: Date.now(),
        name: productName,
        price: 299000,
        quantity: quantity,
        size: size,
        image: document.getElementById('mainImage').src
    };
    
    cart.push(product);
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart count
    updateCartCount();
    
    showNotification(`${productName} đã được thêm vào giỏ hàng! (${quantity} sản phẩm)`, 'success');
    
    // Reset quantity
    document.getElementById('quantity').value = 1;
});

// ===========================
// BUY NOW
// ===========================

document.querySelector('.btn-buy-now-detail')?.addEventListener('click', function() {
    const size = document.querySelector('.size-btn.active')?.textContent;
    
    if (!size) {
        showNotification('Vui lòng chọn size!', 'danger');
        return;
    }
    
    showNotification('Chuyển hướng tới trang thanh toán...', 'info');
    setTimeout(() => {
        // window.location.href = 'checkout.html';
    }, 1000);
});

// ===========================
// WISHLIST
// ===========================

document.getElementById('wishlistBtn')?.addEventListener('click', function() {
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

// ===========================
// TABS
// ===========================

document.querySelectorAll('[role="presentation"] button').forEach(button => {
    button.addEventListener('click', function() {
        // Smooth scroll to tabs
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

document.querySelector('.rating-input')?.addEventListener('mouseleave', function() {
    const active = document.querySelectorAll('.rating-input i.fas');
    ratingStars.forEach(star => star.classList.remove('active'));
    active.forEach(star => star.classList.add('active'));
});

// ===========================
// REVIEW FORM
// ===========================

document.querySelector('.review-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const title = this.querySelector('input[placeholder="Viết tiêu đề đánh giá..."]').value;
    const content = this.querySelector('textarea').value;
    const ratingCount = document.querySelectorAll('.rating-input i.fas').length;
    
    if (!title || !content || ratingCount === 0) {
        showNotification('Vui lòng điền đầy đủ thông tin!', 'danger');
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

// ===========================
// HELPFUL BUTTONS
// ===========================

document.querySelectorAll('.helpful-btn').forEach(button => {
    button.addEventListener('click', function() {
        const count = parseInt(this.textContent.match(/\d+/)[0]);
        this.textContent = `\n\t\t\t\t\t\t\t\t\t<i class="fas fa-thumbs-up"></i> Hữu ích (${count + 1})`;
        this.style.color = 'var(--primary)';
        this.disabled = true;
    });
});

document.querySelectorAll('.unhelpful-btn').forEach(button => {
    button.addEventListener('click', function() {
        const count = parseInt(this.textContent.match(/\d+/)[0]);
        this.textContent = `<i class="fas fa-thumbs-down"></i> Không hữu ích (${count + 1})`;
        this.style.color = 'var(--danger)';
        this.disabled = true;
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
        
        const shareText = `Tôi đang xem ${productName} (${productPrice}) trên AthleteHub - Cửa hàng đồ thể thao online`;
        const currentUrl = window.location.href;
        
        const icon = this.querySelector('i');
        
        if (icon.classList.contains('fa-facebook-f')) {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${currentUrl}`, '_blank');
        } else if (icon.classList.contains('fa-twitter')) {
            window.open(`https://twitter.com/intent/tweet?text=${shareText}&url=${currentUrl}`, '_blank');
        } else if (icon.classList.contains('fa-pinterest')) {
            window.open(`https://pinterest.com/pin/create/button/?url=${currentUrl}&description=${shareText}`, '_blank');
        } else if (icon.classList.contains('fa-linkedin-in')) {
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${currentUrl}`, '_blank');
        }
        
        showNotification('Đang chia sẻ...', 'info');
    });
});

// ===========================
// RELATED PRODUCTS
// ===========================

document.querySelectorAll('.btn-add-quick').forEach(button => {
    button.addEventListener('click', function() {
        const productName = this.parentElement.querySelector('h4').textContent;
        const quantity = 1;
        
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        const product = {
            id: Date.now(),
            name: productName,
            price: parseFloat(this.parentElement.querySelector('.price').textContent),
            quantity: quantity
        };
        
        cart.push(product);
        localStorage.setItem('cart', JSON.stringify(cart));
        
        updateCartCount();
        showNotification(`${productName} đã được thêm vào giỏ hàng!`, 'success');
    });
});

// ===========================
// UTILITIES
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

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = cart.length;
    }
}

// ===========================
// ANIMATIONS
// ===========================

const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
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
`;
document.head.appendChild(style);

// ===========================
// INITIALIZE
// ===========================

// Update cart count on page load
updateCartCount();

console.log('✅ Trang chi tiết sản phẩm đã được khởi tạo!');
