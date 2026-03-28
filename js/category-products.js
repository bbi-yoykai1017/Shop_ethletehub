/**
 * Category Products Page - Xử lý lọc và hiển thị sản phẩm theo danh mục
 */

// Filter state
let filterState = {
    category: 'all',
    search: '',
    maxPrice: 2000000,
    sizes: [],
    ratings: [],
    sortBy: 'popular'
};

// Current category from PHP
let currentCategory = '';

// ===========================
// RENDER PRODUCTS
// ===========================

function renderProducts() {
    const grid = document.getElementById('productsGrid');
    // Lấy dữ liệu từ window.allProducts mà PHP đã đổ vào
    const data = window.allProducts || [];

    let filteredProducts = filterProducts(data);

    if (!grid) return;

    if (filteredProducts.length === 0) {
        grid.innerHTML = '<div class="empty-state" style="grid-column: 1/-1; text-align: center; padding: 50px;"><i class="fas fa-search fa-3x"></i><h3>Không tìm thấy sản phẩm</h3><p>Vui lòng thay đổi bộ lọc của bạn</p></div>';
        document.getElementById('showingCount').textContent = 0;
        return;
    }

    // Hiển thị sản phẩm đầy đủ giống index.php
    grid.innerHTML = filteredProducts.map(product => `
        <div class="product-card" data-product-id="${product.id}">
            <div class="product-image">
                <a href="product-detail.php?id=${product.id}" style="display: block;">
                    ${product.hinh_anh_chinh ? 
                        `<img src="./public/${product.hinh_anh_chinh}" alt="${product.ten}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                         <i class="fas fa-shirt" style="display:none;"></i>` : 
                        `<i class="fas fa-shirt"></i>`
                    }
                </a>
                
                ${getDiscountBadge(product)}
                <span class="product-rating">
                    <i class="fas fa-star"></i>
                    ${Number(product.trung_binh_sao || 0).toFixed(1)}
                </span>
                <button class="btn-quick-view">Xem nhanh</button>
            </div>

            <div class="product-info">
                <div class="product-category">${getCategoryLabel(product.category)}</div>

                <h3 class="product-name">
                    <a href="product-detail.php?id=${product.id}" style="text-decoration: none; color: inherit;">
                        ${product.ten}
                    </a>
                </h3>

                <div class="rating-stars">
                    ${getStarRating(Number(product.trung_binh_sao || 0))}
                    <span class="rating-text">(${product.so_luong_danh_gia || 0})</span>
                </div>
                <div class="product-price">
                    <span class="price-current">${formatPrice(product.gia)}</span>
                </div>
                <p class="product-description">${product.mo_ta || ''}</p>
                <span class="stock-status in-stock">Còn hàng</span>

                <div class="product-actions">
                    <button class="product-btn btn-add-cart" data-product-id="${product.id}">
                        <i class="fas fa-shopping-cart"></i> Thêm
                    </button>
                    <button class="btn-buy-now-detail" onclick="window.location.href='ThanhToan.php?id=${product.id}'">
                        <i class="fas fa-bolt"></i>
                        Mua Ngay
                    </button>
                </div>
            </div>
        </div>
    `).join('');

    document.getElementById('showingCount').textContent = filteredProducts.length;
}

// ===========================
// FILTER PRODUCTS
// ===========================

function filterProducts(data) {
    let filtered = data.filter(product => {
        const pPrice = Number(product.price);
        const pRating = Math.round(Number(product.rating || 0));

        // Category filter - chỉ lọc nếu không phải "all" và không phải danh mục hiện tại
        if (filterState.category !== 'all' && product.category !== filterState.category) {
            return false;
        }

        // Search filter
        if (filterState.search && !product.ten.toLowerCase().includes(filterState.search.toLowerCase())) {
            return false;
        }

        // Price filter
        if (pPrice > filterState.maxPrice) {
            return false;
        }

        // Rating filter
        if (filterState.ratings.length > 0) {
            if (!filterState.ratings.includes(pRating)) {
                return false;
            }
        }

        return true;
    });

    // Sort
    filtered.sort((a, b) => {
        const priceA = Number(a.price);
        const priceB = Number(b.price);
        const ratingA = Number(a.rating || 0);
        const ratingB = Number(b.rating || 0);

        switch (filterState.sortBy) {
            case 'price-low': return priceA - priceB;
            case 'price-high': return priceB - priceA;
            case 'rating': return ratingB - ratingA;
            case 'newest': return b.id - a.id;
            default: return 0;
        }
    });

    return filtered;
}

// ===========================
// EVENT LISTENERS
// ===========================

// Category Filter
document.querySelectorAll('.category-filter').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        if (this.value === 'all') {
            filterState.category = 'all';
        } else {
            filterState.category = this.value;
        }
        renderProducts();
    });
});

// Search Filter
document.getElementById('searchInput')?.addEventListener('input', function () {
    filterState.search = this.value;
    renderProducts();
});

// Price Range
document.getElementById('priceRange')?.addEventListener('input', function () {
    filterState.maxPrice = parseInt(this.value);
    document.getElementById('maxPrice').textContent = formatPrice(this.value);
    renderProducts();
});

// Rating Filter
document.querySelectorAll('.rating-filter').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        if (this.checked) {
            filterState.ratings.push(parseInt(this.value));
        } else {
            filterState.ratings = filterState.ratings.filter(r => r !== parseInt(this.value));
        }
        renderProducts();
    });
});

// Sort
document.getElementById('sortBy')?.addEventListener('change', function () {
    filterState.sortBy = this.value;
    renderProducts();
});

// Clear Filters
document.getElementById('clearFilters')?.addEventListener('click', function () {
    filterState = {
        category: currentCategory || 'all',
        search: '',
        maxPrice: 2000000,
        sizes: [],
        ratings: [],
        sortBy: 'popular'
    };

    document.getElementById('searchInput').value = '';
    document.getElementById('priceRange').value = 2000000;
    document.getElementById('maxPrice').textContent = formatPrice(2000000);
    document.getElementById('sortBy').value = 'popular';

    document.querySelectorAll('.category-filter').forEach(cb => {
        cb.checked = cb.value === (currentCategory || 'all');
    });
    document.querySelectorAll('.rating-filter').forEach(cb => cb.checked = false);

    renderProducts();
    showNotification('Đã xóa tất cả bộ lọc', 'info');
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

function getCategoryLabel(category) {
    const labels = {
        'quan-ao': 'Quần áo',
        'giay': 'Giày',
        'thiet-bi': 'Thiết bị',
        'phu-kien': 'Phụ kiện'
    };
    return labels[category] || category;
}

function getStarRating(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    let html = '';

    for (let i = 0; i < fullStars; i++) {
        html += '<i class="fas fa-star"></i>';
    }

    if (hasHalfStar) {
        html += '<i class="fas fa-star-half"></i>';
    }

    for (let i = fullStars + (hasHalfStar ? 1 : 0); i < 5; i++) {
        html += '<i class="far fa-star"></i>';
    }

    return html;
}

function getDiscountBadge(product) {
    if (product.gia_goc > product.gia) {
        const discount = Math.round(((product.gia_goc - product.gia) / product.gia_goc) * 100);
        return `<span class="product-badge sale">-${discount}%</span>`;
    }
    return '';
}

function addToCart(productId) {
    if (typeof addToCart_API === 'function') {
        // Gọi hàm từ cart.js nếu có
        addToCart_API(productId, 1);
    } else {
        // Fallback: gọi API cart trực tiếp
        fetch('api/cart.php?action=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Lỗi khi thêm sản phẩm', 'danger');
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            showNotification('Lỗi kết nối server', 'danger');
        });
    }
}

function updateCartCount() {
    fetch('api/cart.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count || 0;
                }
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
// INITIALIZE
// ===========================

document.addEventListener('DOMContentLoaded', function () {
    // Clear old localStorage data
    localStorage.removeItem('cart');
    
    // Set current category
    const urlParams = new URLSearchParams(window.location.search);
    const danhMucId = urlParams.get('danh_muc_id');
    
    const categoryMap = {
        '1': 'quan-ao',
        '2': 'giay',
        '3': 'thiet-bi',
        '4': 'phu-kien'
    };
    
    currentCategory = categoryMap[danhMucId] || 'all';
    filterState.category = currentCategory;
    
    // Update checkbox UI
    document.querySelectorAll('.category-filter').forEach(cb => {
        cb.checked = cb.value === currentCategory;
    });

    renderProducts();
    updateCartCount();
    console.log('✅ Trang danh mục sản phẩm đã được khởi tạo!');
});

