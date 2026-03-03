// ===========================
// SHOPPING CART JAVASCRIPT
// ===========================

// ===========================
// LOAD CART
// ===========================

function loadCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartItemsList = document.getElementById('cartItemsList');
    
    if (cart.length === 0) {
        cartItemsList.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Giỏ hàng của bạn trống</h3>
                <p>Hãy thêm một số sản phẩm vào giỏ hàng của bạn để tiếp tục mua sắm.</p>
                <a href="products.html" class="btn btn-primary">
                    <i class="fas fa-arrow-right"></i>
                    Tiếp tục mua sắm
                </a>
            </div>
        `;
        document.querySelector('.cart-summary').style.display = 'none';
        return;
    }
    
    cartItemsList.innerHTML = cart.map((item, index) => `
        <div class="cart-item">
            <div class="cart-item-image">
                <img src="${item.image || 'https://via.placeholder.com/80?text=Product'}" alt="${item.name}">
            </div>
            <div class="cart-item-info">
                <h4>${item.name}</h4>
                <p>${item.category || 'Sản phẩm'}</p>
                <div class="cart-item-price">${formatPrice(item.price)}</div>
            </div>
            <div class="cart-item-details">
                <div class="quantity-control">
                    <button class="qty-control-btn" onclick="decreaseQuantity(${index})">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" class="qty-control-input" value="${item.quantity || 1}" min="1" max="100" onchange="changeQuantity(${index}, this.value)">
                    <button class="qty-control-btn" onclick="increaseQuantity(${index})">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="cart-item-subtotal">
                <strong>${formatPrice((item.price || 0) * (item.quantity || 1))}</strong>
            </div>
            <button class="btn-remove-item" onclick="removeFromCart(${index})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `).join('');
    
    updateCartSummary();
}

// ===========================
// QUANTITY FUNCTIONS
// ===========================

function increaseQuantity(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart[index]) {
        cart[index].quantity = (cart[index].quantity || 1) + 1;
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    }
}

function decreaseQuantity(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart[index] && cart[index].quantity > 1) {
        cart[index].quantity--;
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    }
}

function changeQuantity(index, value) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const qty = parseInt(value);
    if (cart[index] && qty > 0) {
        cart[index].quantity = qty;
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    }
}

// ===========================
// REMOVE FROM CART
// ===========================

function removeFromCart(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const itemName = cart[index].name;
    
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    
    updateCartCount();
    loadCart();
    showNotification(`${itemName} đã được xóa khỏi giỏ hàng`, 'info');
}

// ===========================
// UPDATE CART SUMMARY
// ===========================

function updateCartSummary() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Calculate totals
    const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
    const subtotal = cart.reduce((sum, item) => sum + ((item.price || 0) * (item.quantity || 1)), 0);
    
    // Shipping fee (free if over 500k)
    const shippingFee = subtotal >= 500000 ? 0 : 25000;
    
    // Discount (placeholder for promo codes)
    const discount = parseFloat(localStorage.getItem('cartDiscount') || 0);
    
    // Calculate total
    const total = subtotal + shippingFee - discount;
    
    // Update display
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('subtotal').textContent = formatPrice(subtotal);
    
    if (shippingFee === 0) {
        document.getElementById('shipping').textContent = 'Miễn phí';
        document.getElementById('shipping').className = 'shipping-fee';
    } else {
        document.getElementById('shipping').textContent = formatPrice(shippingFee);
        document.getElementById('shipping').className = '';
    }
    
    document.getElementById('total').textContent = formatPrice(total);
    
    // Show/hide discount
    const discountItem = document.getElementById('discountItem');
    if (discount > 0) {
        discountItem.style.display = 'grid';
        document.getElementById('discount').textContent = '-' + formatPrice(discount);
    } else {
        discountItem.style.display = 'none';
    }
    
    updateCartCount();
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
    
    // Sample promo codes
    const promoCodes = {
        'SAVE10': 50000,
        'SAVE20': 100000,
        'SHIP': 25000,
        'WELCOME': 75000
    };
    
    if (promoCodes[promoCode]) {
        localStorage.setItem('cartDiscount', promoCodes[promoCode]);
        updateCartSummary();
        showNotification(`Mã giảm giá "${promoCode}" đã được áp dụng!`, 'success');
        document.getElementById('promoCode').value = '';
    } else {
        showNotification('Mã giảm giá không hợp lệ', 'danger');
    }
});

// ===========================
// CHECKOUT
// ===========================

document.getElementById('checkoutBtn')?.addEventListener('click', function() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    if (cart.length === 0) {
        showNotification('Giỏ hàng của bạn trống!', 'danger');
        return;
    }
    
    showNotification('Chuyển hướng tới trang thanh toán...', 'info');
    setTimeout(() => {
        // window.location.href = 'checkout.html';
    }, 1000);
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
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = cart.length;
    }
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
// INITIALIZE
// ===========================

document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    updateCartCount();
    
    // Add animation styles
    const style = document.createElement('style');
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
    
    console.log('✅ Trang giỏ hàng đã được khởi tạo!');
});
