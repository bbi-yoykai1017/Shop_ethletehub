
let filterState = {
    category: 'all',
    search: '',
    maxPrice: 2000000,
    sizes: [],
    ratings: [],
    sortBy: 'popular'
};

// ===========================
// RENDER PRODUCTS
// ===========================

function renderProducts() {
    const grid = document.getElementById('productsGrid');
    // Quan trọng: Lấy dữ liệu từ window.allProducts mà PHP đã đổ vào
    const data = window.allProducts || [];

    let filteredProducts = filterProducts(data);

    if (!grid) return; // Tránh lỗi nếu không tìm thấy thẻ grid

    if (filteredProducts.length === 0) {
        grid.innerHTML = '<div class="empty-state" style="grid-column: 1/-1; text-align: center; padding: 50px;"><i class="fas fa-search fa-3x"></i><h3>Không tìm thấy sản phẩm</h3><p>Vui lòng thay đổi bộ lọc của bạn</p></div>';
        document.getElementById('showingCount').textContent = 0;
        return;
    }

    grid.innerHTML = filteredProducts.map(product => `
        <div class="product-card-page">
            <div class="product-image-page">
                <img src="./public/${product.hinh_anh_chinh}" 
     alt="${product.ten_san_pham}" 
     onerror="this.src='./public/images/aoamnu.jpg'">
                ${getDiscountBadge(product)}
            </div>
            <div class="product-info-page">
                <div class="product-category-page">${getCategoryLabel(product.ten)}</div>
                <h3 class="product-name-page">${product.mo_ta}</h3>
                <div class="rating-page">
                    ${getStarRating(Number(product.trung_binh_sao))} 
                    <span>(${product.so_luong_danh_gia || 0})</span>
                </div>
                <div class="product-price-page">
                    <span class="price-current-page">${formatPrice(product.gia)}</span>
                    ${(product.gia_goc && Number(product.gia_goc) > Number(product.gia))
            ? `<span class="price-original-page">${formatPrice(product.gia_goc)}</span>`
            : ''}
                </div>
                <button class="btn-page-add" onclick="addToCart(${product.id})">
                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                </button>
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
        // Ép kiểu số để so sánh chính xác
        const pPrice = Number(product.price);
        const pRating = Math.round(Number(product.rating));

        // Category filter
        if (filterState.category !== 'all' && product.category !== filterState.category) {
            return false;
        }

        // Search filter
        if (filterState.search && !product.name.toLowerCase().includes(filterState.search.toLowerCase())) {
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
        const ratingA = Number(a.rating);
        const ratingB = Number(b.rating);

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
        category: 'all',
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

    document.querySelectorAll('.category-filter').forEach(cb => cb.checked = cb.value === 'all');
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
    if (product.originalPrice > product.price) {
        const discount = Math.round(((product.originalPrice - product.price) / product.originalPrice) * 100);
        return `<span style="position: absolute; top: 10px; right: 10px; background: var(--primary); color: white; padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 0.85rem;">-${discount}%</span>`;
    }
    return '';
}

function addToCart(productId) {
    const product = allProducts.find(p => p.id === productId);
    if (!product) return;

    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    const cartItem = {
        id: product.id,
        name: product.name,
        price: product.price,
        quantity: 1,
        image: product.image
    };

    cart.push(cartItem);
    localStorage.setItem('cart', JSON.stringify(cart));

    updateCartCount();
    showNotification(`${product.name} đã được thêm vào giỏ hàng!`, 'success');
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

console.log(allProducts);

// ===========================
// INITIALIZE
// ===========================

document.addEventListener('DOMContentLoaded', function () {
    renderProducts();
    updateCartCount();
    console.log('✅ Trang danh sách sản phẩm đã được khởi tạo!');
});
