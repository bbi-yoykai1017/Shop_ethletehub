/* Shopping Cart Functions */

/* ============================== */
/* Navigate to Product Detail */
/* ============================== */

function goProductDetail(event, productId) {
    console.log('🔗 goProductDetail called with:', productId);
    window.location.href = `product-detail.php?id=${productId}`;
}

/* ============================== */
/* Update Total for Selected Items */
/* ============================== */

function parsePrice(priceText) {
    // Convert "1.000.000₫" → 1000000
    return parseInt(priceText.replace(/[^\d]/g, ''));
}

function updateTotal() {
    const allItems = document.querySelectorAll('.cart-item');

    let selectedTotal = 0;
    let selectedCount = 0;

    allItems.forEach(item => {
        const checkbox = item.querySelector('.item-select-checkbox');

        if (checkbox && checkbox.checked) {
            const priceEl = item.querySelector('.cart-item-price');
            const qtyInput = item.querySelector('.qty-control-input');

            if (priceEl && qtyInput) {
                const price = parsePrice(priceEl.textContent);
                const qty = parseInt(qtyInput.value) || 1;

                selectedTotal += price * qty;
                selectedCount += qty;
            }
        }
    });

    // Update summary
    const totalQuantityEl = document.getElementById('totalQuantity');
    const subtotalEl = document.getElementById('subtotalDisplay');
    const shippingEl = document.getElementById('shippingDisplay');
    const totalEl = document.getElementById('totalDisplay');

    if (totalQuantityEl) totalQuantityEl.textContent = selectedCount;

    const subtotal = selectedTotal;
    const shipping = subtotal >= 500000 ? 0 : 25000;
    const total = subtotal + shipping;

    if (subtotalEl) subtotalEl.textContent = formatPrice(subtotal);

    if (shippingEl) {
        if (shipping === 0) {
            shippingEl.textContent = 'Miễn phí';
            shippingEl.className = 'shipping-fee';
        } else {
            shippingEl.textContent = formatPrice(shipping);
            shippingEl.className = '';
        }
    }

    if (totalEl) totalEl.textContent = formatPrice(total);
}

/* Add to Cart - Send to Backend API */

function addToCart(productId, qty = 1, sizeId = null, colorId = null, sizeName = null, colorName = null) {
    return new Promise((resolve) => {
        // Send request to API backend
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

/* Update Cart Quantity */

function updateCartQuantity(productId, qty, sizeId = null, colorId = null) {
    // Convert empty strings to null
    sizeId = sizeId === '' ? null : sizeId;
    colorId = colorId === '' ? null : colorId;
    
    fetch('api/cart.php?action=update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: qty,
            size_id: sizeId,
            color_id: colorId
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

function removeFromCartAPI(productId, sizeId = null, colorId = null) {
    return new Promise((resolve) => {
        // Convert empty strings to null
        sizeId = sizeId === '' ? null : sizeId;
        colorId = colorId === '' ? null : colorId;
        
        fetch('api/cart.php?action=remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                size_id: sizeId,
                color_id: colorId
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
    console.log('📦 Loading cart...');
    fetch('api/cart.php?action=get')
        .then(response => {
            console.log('📡 API Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📊 API Response data:', data);
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

                let imagePath = item.image || 'public/placeholder.svg';
                if (!imagePath.startsWith('./') && !imagePath.startsWith('/') && !imagePath.startsWith('http') && !imagePath.includes('data:')) {
                    imagePath = 'public/' + imagePath;
                }

                // Build HTML for size/color info
                let variantInfo = '';
                if (item.size || item.color) {
                    variantInfo = '<div class="cart-item-variants">';
                    if (item.size) {
                        variantInfo += `<span class="variant-badge">Size: ${item.size}</span>`;
                    }
                    if (item.color) {
                        variantInfo += `<span class="variant-badge">Màu: ${item.color}</span>`;
                    }
                    variantInfo += '</div>';
                }

                return `
                <div class="cart-item" id="item-${item.id}" data-product-id="${item.id}"
                     data-size-id="${item.size_id || ''}" data-color-id="${item.color_id || ''}"
                     style="cursor: pointer;">
                    <input type="checkbox" class="item-select-checkbox"
                           data-product-id="${item.id}"
                           onchange="updateTotal()"
                           onclick="event.stopPropagation()">
                    <div class="cart-item-image">
                        <img src="${imagePath}" alt="${item.name}"
                             onerror="this.src='public/placeholder.svg'"
                             onclick="event.stopPropagation()">
                    </div>
                    <div class="cart-item-info">
                        <h4>${item.name}</h4>
                        ${variantInfo}
                        <div class="cart-item-price">${formatPrice(item.price)}</div>
                    </div>
                    <div class="cart-item-details">
                        <div class="quantity-control">
                            <button class="qty-control-btn" onclick="event.stopPropagation(); changeQty(${item.id}, ${item.quantity - 1}, '${item.size_id || ''}', '${item.color_id || ''}')">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="qty-control-input" value="${item.quantity}" min="1" max="100"
                                   onclick="event.stopPropagation()"
                                   onchange="changeQty(${item.id}, this.value, '${item.size_id || ''}', '${item.color_id || ''}')">
                            <button class="qty-control-btn" onclick="event.stopPropagation(); changeQty(${item.id}, ${item.quantity + 1}, '${item.size_id || ''}', '${item.color_id || ''}')">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="cart-item-subtotal">
                        <strong>${formatPrice((item.price || 0) * (item.quantity || 1))}</strong>
                    </div>
                     <div class="cart-item-actions">
                        <button class="btn-edit-item" data-product-id="${item.id}" data-size-name="${(item.size || '').replace(/'/g, '&#39;')}" data-color-name="${(item.color || '').replace(/'/g, '&#39;')}" data-size-id="${item.size_id || ''}" data-color-id="${item.color_id || ''}" title="Chỉnh sửa sản phẩm">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-remove-item" data-product-id="${item.id}" data-size-id="${item.size_id || ''}" data-color-id="${item.color_id || ''}" title="Xóa sản phẩm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                `;
            }).join('');

            updateCartSummary();
            updateTotal();  // Calculate for selected items
            document.querySelector('.cart-summary').style.display = 'block';
        })
        .catch(error => console.error('Lỗi tải giỏ hàng:', error));
}

// ===========================
// CHANGE QUANTITY
// ===========================

function changeQty(productId, newQty, sizeId = null, colorId = null) {
    newQty = parseInt(newQty);
    if (newQty < 0) newQty = 0;
    if (newQty > 100) newQty = 100;
    
    if (newQty === 0) {
        if (confirm('Xóa sản phẩm này khỏi giỏ hàng?')) {
            removeFromCartAPI(productId, sizeId, colorId);
        }
        return;
    }
    
    updateCartQuantity(productId, newQty, sizeId, colorId);
}

// ===========================
/* Update Cart Summary */
// ===========================

function updateCartSummary() {
    fetch('api/cart.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.cart) return;
            
            const cart = data.cart;
            
            // Calculate totals
            const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
            const subtotal = cart.reduce((sum, item) => sum + ((item.price || 0) * (item.quantity || 1)), 0);
            
            // Shipping fee (free if >= 500k)
            const shippingFee = subtotal >= 500000 ? 0 : 25000;
            
            // Discount
            const discount = 0; // Can add discount logic later
            
            // Total amount
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
            
            // Show discount if exists
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
        .catch(error => console.error('Error updating summary:', error));
}

/* Promo Code */

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

/* Checkout */

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

/* Utility Functions */
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
        .then(response => response.json()) // json file
        .then(data => {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount && data.success) {
                cartCount.textContent = data.cart_count || 0;
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
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

/* Checkout Function */
function goCheckout() {
    // Lấy danh sách items được chọn
    const checkedCheckboxes = document.querySelectorAll('.item-select-checkbox:checked');

    if (checkedCheckboxes.length === 0) {
        showNotification('Vui lòng chọn ít nhất 1 sản phẩm!', 'warning');
        return;
    }

    const selectedProductIds = Array.from(checkedCheckboxes).map(cb =>
        parseInt(cb.dataset.productId)
    );

    // Lưu selected items vào sessionStorage + URL params
    sessionStorage.setItem('selectedItems', JSON.stringify(selectedProductIds));

    showNotification('Chuyển hướng tới trang thanh toán...', 'info');
    setTimeout(() => {
        // Gửi selected items qua GET parameter
        const selectedItemsParam = selectedProductIds.join(',');
        window.location.href = `ThanhToan.php?selected_items=${encodeURIComponent(selectedItemsParam)}`;
    }, 1000);
}

/* Apply Promo Code */

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

/* Button Handlers */

function editCartItemBtn(btn) {
    const productId = parseInt(btn.dataset.productId);
    const sizeName = btn.dataset.sizeName || '';
    const colorName = btn.dataset.colorName || '';
    const sizeId = btn.dataset.sizeId || null;
    const colorId = btn.dataset.colorId || null;
    
    editCartItem(productId, sizeName, colorName, sizeId, colorId);
}

function removeFromCartBtn(btn) {
    const productId = parseInt(btn.dataset.productId);
    const sizeId = btn.dataset.sizeId || null;
    const colorId = btn.dataset.colorId || null;
    
    removeFromCartAPI(productId, sizeId, colorId);
}

/* Edit Cart Item */

function editCartItem(productId, size, color, sizeId = null, colorId = null) {
    // Fetch product info from session and variants from database
    Promise.all([
        fetch('api/cart.php?action=get').then(r => r.json()),
        fetch(`api/product-variants.php?product_id=${productId}`).then(r => r.json())
    ])
    .then(([cartData, variantsData]) => {
        if (!cartData.cart || !variantsData.success) {
            showNotification('Error loading product data', 'danger');
            return;
        }
        
        // Find item in cart - match with size/color
        const item = cartData.cart.find(i => 
            i.id == productId && 
            (i.size_id == sizeId || (i.size_id === null && sizeId === null)) &&
            (i.color_id == colorId || (i.color_id === null && colorId === null))
        );
        if (!item) {
            showNotification('Product not found in cart', 'danger');
            return;
        }
        
        const sizes = variantsData.sizes || [];
        const colors = variantsData.colors || [];
        
        // Render size buttons
        let sizesHTML = '';
        if (sizes.length > 0) {
            sizesHTML = '<div class="form-group mt-3"><label class="form-label">Size:</label><div id="editSizeOptions" class="d-flex flex-wrap gap-2">';
            sizes.forEach(s => {
                const isActive = sizeId == s.id ? 'active' : '';
                sizesHTML += `<button type="button" class="btn btn-outline-primary size-btn-edit ${isActive}" data-size-id="${s.id}" data-size-name="${s.ten}">${s.ten}</button>`;
            });
            sizesHTML += '</div>';
        }
        
        // Render color buttons
        let colorsHTML = '';
        if (colors.length > 0) {
            colorsHTML = '<div class="form-group mt-3"><label class="form-label">Color:</label><div id="editColorOptions" class="d-flex flex-wrap gap-2">';
            colors.forEach(c => {
                const isActive = colorId == c.id ? 'active' : '';
                const colorHex = c.ma_hex || '#999999';
                colorsHTML += `<button type="button" class="btn btn-outline-secondary color-btn-edit ${isActive}" data-color-id="${c.id}" data-color-name="${c.ten}" style="position: relative;">
                    <span style="display: inline-block; width: 16px; height: 16px; background: ${colorHex}; border: 2px solid #ccc; border-radius: 4px; margin-right: 5px;"></span>${c.ten}
                </button>`;
            });
            colorsHTML += '</div>';
        }
        
        // Create modal for editing
        const modalHTML = `
            <div class="modal fade" id="editCartModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Chỉnh sửa sản phẩm</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img src="${item.image.includes('public/') ? item.image : 'public/' + item.image}" 
                                         alt="${item.name}" style="max-width: 100%; border-radius: 8px;">
                                </div>
                                <div class="col-md-8">
                                    <h6>${item.name}</h6>
                                    <p class="text-muted">Giá: ${formatPrice(item.price)}</p>
                                    
                                    ${sizesHTML}
                                    ${colorsHTML}
                                    
                                    <div class="form-group mt-3">
                                        <label for="editQuantity" class="form-label">Số lượng:</label>
                                        <div class="input-group" style="max-width: 150px;">
                                            <button class="btn btn-outline-secondary" type="button" onclick="decreaseQtyEdit()">-</button>
                                            <input type="number" id="editQuantity" class="form-control text-center" 
                                                   value="${item.quantity}" min="1" max="100">
                                            <button class="btn btn-outline-secondary" type="button" onclick="increaseQtyEdit()">+</button>
                                        </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-primary" onclick="saveEditCartItem(${productId})">Lưu thay đổi</button>
                        </div>
                </div>
        `;
        
        // Xóa modal cũ nếu có
        const oldModal = document.getElementById('editCartModal');
        if (oldModal) {
            oldModal.remove();
        }
        
        // Thêm modal vào body
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Setup size button event listeners
        document.querySelectorAll('.size-btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.size-btn-edit').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                window.editItemNewSizeId = this.dataset.sizeId;
                window.editItemNewSizeName = this.dataset.sizeName;
            });
        });
        
        // Setup color button event listeners
        document.querySelectorAll('.color-btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.color-btn-edit').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                window.editItemNewColorId = this.dataset.colorId;
                window.editItemNewColorName = this.dataset.colorName;
            });
        });
        
        // Store original values
        window.editItemProductId = productId;
        window.editItemOldSizeId = sizeId;
        window.editItemOldColorId = colorId;
        window.editItemNewSizeId = sizeId;
        window.editItemNewColorId = colorId;
        window.editItemNewSizeName = size;
        window.editItemNewColorName = color;
        
        // Hiển thị modal
        const modal = new bootstrap.Modal(document.getElementById('editCartModal'));
        modal.show();
        
        // Xóa modal khi đóng
        document.getElementById('editCartModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    })
    .catch(error => {
        console.error('Lỗi tải thông tin sản phẩm:', error);
        showNotification('Lỗi tải dữ liệu', 'danger');
    });
}

function increaseQtyEdit() {
    const input = document.getElementById('editQuantity');
    let value = parseInt(input.value) || 1;
    if (value < 100) {
        input.value = value + 1;
    }
}

function decreaseQtyEdit() {
    const input = document.getElementById('editQuantity');
    let value = parseInt(input.value) || 1;
    if (value > 1) {
        input.value = value - 1;
    }
}

function saveEditCartItem(productId) {
    const newQty = parseInt(document.getElementById('editQuantity').value);
    
    if (isNaN(newQty) || newQty < 1) {
        showNotification('Số lượng không hợp lệ', 'danger');
        return;
    }
    
    const oldSizeId = window.editItemOldSizeId;
    const oldColorId = window.editItemOldColorId;
    const newSizeId = window.editItemNewSizeId;
    const newColorId = window.editItemNewColorId;
    const newSizeName = window.editItemNewSizeName;
    const newColorName = window.editItemNewColorName;
    
    // Check if size or color changed
    const sizeChanged = oldSizeId != newSizeId;
    const colorChanged = oldColorId != newColorId;
    
    if (sizeChanged || colorChanged) {
        // Size/color changed: remove old item and add new item
        removeFromCartAPI(productId, oldSizeId, oldColorId)
            .then(removed => {
                if (!removed) {
                    showNotification('Lỗi khi xóa sản phẩm cũ', 'danger');
                    return;
                }
                
                // Now add new item with new size/color
                return fetch('api/cart.php?action=add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: newQty,
                        size_id: newSizeId || null,
                        color_id: newColorId || null,
                        size_name: newSizeName || null,
                        color_name: newColorName || null
                    })
                }).then(r => r.json());
            })
            .then(data => {
                if (data && data.success) {
                    showNotification('Cập nhật sản phẩm thành công', 'success');
                    loadCart();
                    updateCartCount();
                    
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editCartModal'));
                    if (modal) {
                        modal.hide();
                    }
                } else {
                    showNotification('Lỗi cập nhật sản phẩm', 'danger');
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                showNotification('Lỗi kết nối server', 'danger');
            });
    } else {
        // Only quantity changed: simple update
        updateCartQuantity(productId, newQty, newSizeId, newColorId);
        
        // Đóng modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editCartModal'));
        if (modal) {
            modal.hide();
        }
        
        showNotification('Cập nhật sản phẩm thành công', 'success');
    }
}

// ===========================
// ===========================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 DOMContentLoaded - Initializing cart...');
    loadCart();
    updateCartCount();
    updateTotal();

    // Event delegation for cart actions
    document.addEventListener('click', function(e) {
        console.log('✅ Click detected - target:', e.target, 'classList:', e.target.className);

        // Check for Edit button
        const editBtn = e.target.closest('.btn-edit-item');
        if (editBtn) {
            console.log('🔧 Edit button clicked!', editBtn);
            e.preventDefault();
            e.stopPropagation();
            editCartItemBtn(editBtn);
            return;
        }

        // Check for Remove button
        const removeBtn = e.target.closest('.btn-remove-item');
        if (removeBtn) {
            console.log('🗑️ Remove button clicked!', removeBtn);
            e.preventDefault();
            e.stopPropagation();
            removeFromCartBtn(removeBtn);
            return;
        }

        // Check for cart item click (navigate to detail)
        const cartItem = e.target.closest('.cart-item');
        if (cartItem && !e.target.closest('input') && !e.target.closest('button')) {
            const productId = cartItem.dataset.productId;
            console.log('🛍️ Cart item clicked, navigating to product:', productId);
            goProductDetail(e, productId);
            return;
        }
    });
    
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
    
    console.log('✅ Trang giỏ hàng đã được khởi tạo!');
});
