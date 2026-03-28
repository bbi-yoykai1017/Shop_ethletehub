// ===========================
// SHOPPING CART JAVASCRIPT
// ===========================

// ===========================
// ADD TO CART (Gửi tới Backend API)
// ===========================

function addToCart(productId, qty = 1) {
    return new Promise((resolve) => {
        // Gửi request tới API backend
        fetch('api/cart.php?action=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: qty
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                updateCartCount();
                resolve(true);
            } else {
                showNotification(data.message || 'Lỗi khi thêm sản phẩm', 'danger');
                resolve(false);
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            showNotification('Lỗi kết nối server', 'danger');
            resolve(false);
        });
    });
}

// ===========================
// UPDATE CART QUANTITY
// ===========================

function updateCartQuantity(productId, qty) {
    fetch('api/cart.php?action=update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: qty
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadCart();
            updateCartCount();
        }
    })
    .catch(error => console.error('Lỗi:', error));
}

// ===========================
// REMOVE FROM CART
// ===========================

function removeFromCartAPI(productId) {
    return new Promise((resolve) => {
        fetch('api/cart.php?action=remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'info');
                loadCart();
                updateCartCount();
                resolve(true);
            } else {
                showNotification(data.message || 'Lỗi khi xóa', 'danger');
                resolve(false);
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            resolve(false);
        });
    });
}

// ===========================
// LOAD CART FROM BACKEND
// ===========================

function loadCart() {
    fetch('api/cart.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.cart || data.cart.length === 0) {
                const cartItemsList = document.getElementById('cartItemsList');
                if (cartItemsList) {
                    cartItemsList.innerHTML = `
                        <div class="empty-cart text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x mb-3 text-muted"></i>
                            <h3>Giỏ hàng của bạn trống</h3>
                            <p class="text-muted">Hãy thêm sản phẩm vào giỏ để tiếp tục mua sắm.</p>
                            <a href="products.php" class="btn btn-primary mt-2">
                                <i class="fas fa-arrow-right me-1"></i>Tiếp tục mua sắm
                            </a>
                        </div>
                    `;
                }
                const cartSummary = document.querySelector('.cart-summary');
                if (cartSummary) cartSummary.style.display = 'none';
                return;
            }
            
            const cartItemsList = document.getElementById('cartItemsList');
            if (!cartItemsList) return;
            
            const cart = data.cart;
            cartItemsList.innerHTML = cart.map((item, index) => {
                // Fix image path - thêm public/ nếu cần
                let imagePath = item.image || 'images/placeholder.svg';
                if (!imagePath.startsWith('./') && !imagePath.startsWith('/') && !imagePath.includes('data:')) {
                    imagePath = './public/' + imagePath;
                }
                return `
                <div class="cart-item" id="item-${item.id}">
                    <div class="cart-item-image">
                        <img src="${imagePath}" alt="${item.name}" onerror="this.src='images/placeholder.svg'">
                    </div>
                    <div class="cart-item-info">
                        <h4>${item.name}</h4>
                        <div class="cart-item-price">${formatPrice(item.price)}</div>
                    </div>
                    <div class="cart-item-details">
                        <div class="quantity-control">
                            <button class="qty-control-btn" onclick="changeQty(${item.id}, ${item.quantity - 1})">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="qty-control-input" value="${item.quantity}" min="1" max="100" 
                                   onchange="changeQty(${item.id}, this.value)">
                            <button class="qty-control-btn" onclick="changeQty(${item.id}, ${item.quantity + 1})">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="cart-item-subtotal">
                        <strong>${formatPrice((item.price || 0) * (item.quantity || 1))}</strong>
                    </div>
                    <button class="btn-remove-item" onclick="removeFromCartAPI(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                `;
            }).join('');
            
            updateCartSummary();
            document.querySelector('.cart-summary').style.display = 'block';
        })
        .catch(error => console.error('Lỗi tải giỏ hàng:', error));
}

// ===========================
// CHANGE QUANTITY
// ===========================

function changeQty(productId, newQty) {
    newQty = parseInt(newQty);
    if (newQty < 0) newQty = 0;
    if (newQty > 100) newQty = 100;
    
    if (newQty === 0) {
        if (confirm('Xóa sản phẩm này khỏi giỏ hàng?')) {
            removeFromCartAPI(productId);
        }
        return;
    }
    
    updateCartQuantity(productId, newQty);
}

// ===========================
// UPDATE CART SUMMARY
// ===========================

function updateCartSummary() {
    fetch('api/cart.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.cart) return;
            
            const cart = data.cart;
            
            // Tính toán
            const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
            const subtotal = cart.reduce((sum, item) => sum + ((item.price || 0) * (item.quantity || 1)), 0);
            
            // Phí vận chuyển (miễn phí nếu >= 500k)
            const shippingFee = subtotal >= 500000 ? 0 : 25000;
            
            // Giảm giá
            const discount = 0; // Có thể thêm logic giảm giá sau
            
            // Tổng cộng
            const total = subtotal + shippingFee - discount;
            
            // Cập nhật hiển thị - SỬA LỖI ID
            const totalItemsEl = document.getElementById('totalQuantity');
            const subtotalEl = document.getElementById('subtotalDisplay');
            const shippingEl = document.getElementById('shippingDisplay');
            const totalEl = document.getElementById('totalDisplay');
            const discountItemEl = document.getElementById('discountItem');
            const discountEl = document.getElementById('discountDisplay');
            
            if (totalItemsEl) totalItemsEl.textContent = totalItems;
            if (subtotalEl) subtotalEl.textContent = formatPrice(subtotal);
            
            if (shippingEl) {
                if (shippingFee === 0) {
                    shippingEl.textContent = 'Miễn phí';
                    shippingEl.className = 'shipping-fee';
                } else {
                    shippingEl.textContent = formatPrice(shippingFee);
                    shippingEl.className = '';
                }
            }
            
            if (totalEl) totalEl.textContent = formatPrice(total);
            
            // Hiển thị giảm giá nếu có
            if (discountItemEl) {
                if (discount > 0) {
                    discountItemEl.style.display = 'grid';
                    if (discountEl) discountEl.textContent = '-' + formatPrice(discount);
                } else {
                    discountItemEl.style.display = 'none';
                }
            }
            
            updateCartCount();
        })
        .catch(error => console.error('Lỗi cập nhật summary:', error));
}

// ===========================
// PROMO CODE
// ===========================

document.getElementById('applyPromo')?.addEventListener('click', function() {
    const promoCode = document.getElementById('promoCode').value.toUpperCase().trim();
    
    if (!promoCode) {
        showNotification('Vui lòng nhập mã giảm giá', 'warning');
        return;
    }
    
    // Định nghĩa các mã giảm giá hợp lệ
    const promoCodes = {
        'SAVE10': 50000,
        'SAVE20': 100000,
        'SHIP': 25000,
        'WELCOME': 75000
    };
    
    if (promoCodes[promoCode]) {
        showNotification(`Mã giảm giá "${promoCode}" đã được áp dụng!`, 'success');
        document.getElementById('promoCode').value = '';
        // Lưu giảm giá và tính lại
        updateCartSummary();
    } else {
        showNotification('Mã giảm giá không hợp lệ', 'danger');
    }
});

// ===========================
// CHECKOUT
// ===========================

document.getElementById('checkoutBtn')?.addEventListener('click', function() {
    fetch('api/cart.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (!data.cart || data.cart.length === 0) {
                showNotification('Giỏ hàng của bạn trống!', 'danger');
                return;
            }
            
            showNotification('Chuyển hướng tới trang thanh toán...', 'info');
            setTimeout(() => {
                window.location.href = 'ThanhToan.php';
            }, 1000);
        });
});

// ===========================
// UTILITIES
// ===========================

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
}

function updateCartCount() {
    fetch('api/cart.php?action=get')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount && data.success) {
                cartCount.textContent = data.cart_count || 0;
            }
        })
        .catch(error => console.error('Lỗi cập nhật cart count:', error));
}

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
// CHECKOUT FUNCTION
// ===========================

function goCheckout() {
    fetch('api/cart.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (!data.cart || data.cart.length === 0) {
                showNotification('Giỏ hàng của bạn trống!', 'danger');
                return;
            }
            
            showNotification('Chuyển hướng tới trang thanh toán...', 'info');
            setTimeout(() => {
                window.location.href = 'ThanhToan.php';
            }, 1000);
        });
}

// ===========================
// APPLY PROMO CODE
// ===========================

function applyPromo() {
    const promoCode = document.getElementById('promoCode').value.toUpperCase().trim();
    
    if (!promoCode) {
        showNotification('Vui lòng nhập mã giảm giá', 'warning');
        return;
    }
    
    // Định nghĩa các mã giảm giá hợp lệ
    const promoCodes = {
        'SAVE10': 50000,
        'SAVE20': 100000,
        'SHIP': 25000,
        'WELCOME': 75000
    };
    
    if (promoCodes[promoCode]) {
        showNotification(`Mã giảm giá "${promoCode}" đã được áp dụng!`, 'success');
        document.getElementById('promoCode').value = '';
        // Lưu giảm giá và tính lại
        updateCartSummary();
    } else {
        showNotification('Mã giảm giá không hợp lệ', 'danger');
    }
}

// ===========================
// ===========================

document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    updateCartCount();
    
    // Add animation styles (avoid duplicates)
    if (!document.getElementById('cartAnimStyle')) {
        const style = document.createElement('style');
        style.id = 'cartAnimStyle';
        style.textContent = `
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
    }
    
    console.log('✅ Trang giỏ hàng đã được khởi tạo (Backend API)!');
});
