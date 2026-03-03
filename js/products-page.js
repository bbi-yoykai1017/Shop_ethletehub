// ===========================
// PRODUCTS PAGE JAVASCRIPT
// ===========================

// Sample Products Data
const allProducts = [
    {
        id: 1,
        name: 'Áo tập Pro Performance',
        category: 'quan-ao',
        price: 299000,
        originalPrice: 429000,
        rating: 4.8,
        reviews: 120,
        image: 'https://via.placeholder.com/200?text=Product+1'
    },
    {
        id: 2,
        name: 'Giày chạy Elite Runner',
        category: 'giay',
        price: 1299000,
        originalPrice: 1599000,
        rating: 4.9,
        reviews: 95,
        image: 'https://via.placeholder.com/200?text=Product+2'
    },
    {
        id: 3,
        name: 'Tạ tay đôi 10KG',
        category: 'thiet-bi',
        price: 499000,
        originalPrice: 499000,
        rating: 4.7,
        reviews: 78,
        image: 'https://via.placeholder.com/200?text=Product+3'
    },
    {
        id: 4,
        name: 'Kính bảo vệ UV',
        category: 'phu-kien',
        price: 299000,
        originalPrice: 349000,
        rating: 4.6,
        reviews: 64,
        image: 'https://via.placeholder.com/200?text=Product+4'
    },
    {
        id: 5,
        name: 'Bình nước thể thao 1L',
        category: 'phu-kien',
        price: 149000,
        originalPrice: 149000,
        rating: 4.9,
        reviews: 112,
        image: 'https://via.placeholder.com/200?text=Product+5'
    },
    {
        id: 6,
        name: 'Quần tập thể thao',
        category: 'quan-ao',
        price: 349000,
        originalPrice: 499000,
        rating: 4.8,
        reviews: 88,
        image: 'https://via.placeholder.com/200?text=Product+6'
    },
    {
        id: 7,
        name: 'Áo tập Casual',
        category: 'quan-ao',
        price: 199000,
        originalPrice: 249000,
        rating: 4.7,
        reviews: 85,
        image: 'https://via.placeholder.com/200?text=Product+7'
    },
    {
        id: 8,
        name: 'Áo tập Marathon',
        category: 'quan-ao',
        price: 249000,
        originalPrice: 299000,
        rating: 4.6,
        reviews: 56,
        image: 'https://via.placeholder.com/200?text=Product+8'
    },
    {
        id: 9,
        name: 'Áo tập Mesh',
        category: 'quan-ao',
        price: 279000,
        originalPrice: 329000,
        rating: 4.9,
        reviews: 102,
        image: 'https://via.placeholder.com/200?text=Product+9'
    },
    {
        id: 10,
        name: 'Áo tập Premium',
        category: 'quan-ao',
        price: 349000,
        originalPrice: 449000,
        rating: 4.8,
        reviews: 78,
        image: 'https://via.placeholder.com/200?text=Product+10'
    },
    {
        id: 11,
        name: 'Giày chạy Marathon',
        category: 'giay',
        price: 999000,
        originalPrice: 1199000,
        rating: 4.8,
        reviews: 92,
        image: 'https://via.placeholder.com/200?text=Product+11'
    },
    {
        id: 12,
        name: 'Giày bóng rổ Pro',
        category: 'giay',
        price: 899000,
        originalPrice: 999000,
        rating: 4.7,
        reviews: 67,
        image: 'https://via.placeholder.com/200?text=Product+12'
    }
];

// ===========================
// FILTER STATE
// ===========================

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
    let filteredProducts = filterProducts();
    
    if (filteredProducts.length === 0) {
        grid.innerHTML = '<div class="empty-state"><i class="fas fa-search"></i><h3>Không tìm thấy sản phẩm</h3><p>Vui lòng thay đổi bộ lọc của bạn</p></div>';
        return;
    }
    
    grid.innerHTML = filteredProducts.map(product => `
        <div class="product-card-page">
            <div class="product-image-page">
                <img src="${product.image}" alt="${product.name}" style="width: 100%; height: 100%; object-fit: cover;">
                ${getDiscountBadge(product)}
            </div>
            <div class="product-info-page">
                <div class="product-category-page">${getCategoryLabel(product.category)}</div>
                <h3 class="product-name-page">${product.name}</h3>
                <div class="rating-page">
                    ${getStarRating(product.rating)}
                    <span>(${product.reviews})</span>
                </div>
                <div class="product-price-page">
                    <span class="price-current-page">${formatPrice(product.price)}</span>
                    ${product.originalPrice !== product.price ? `<span class="price-original-page">${formatPrice(product.originalPrice)}</span>` : ''}
                </div>
                <button class="btn-page-add" onclick="addToCart(${product.id})">
                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                </button>
            </div>
        </div>
    `).join('');
    
    // Update showing count
    document.getElementById('showingCount').textContent = filteredProducts.length;
}

// ===========================
// FILTER PRODUCTS
// ===========================

function filterProducts() {
    let filtered = allProducts.filter(product => {
        // Category filter
        if (filterState.category !== 'all' && product.category !== filterState.category) {
            return false;
        }
        
        // Search filter
        if (filterState.search && !product.name.toLowerCase().includes(filterState.search.toLowerCase())) {
            return false;
        }
        
        // Price filter
        if (product.price > filterState.maxPrice) {
            return false;
        }
        
        // Rating filter
        if (filterState.ratings.length > 0) {
            const productRating = Math.round(product.rating);
            if (!filterState.ratings.includes(productRating)) {
                return false;
            }
        }
        
        return true;
    });
    
    // Sort
    filtered.sort((a, b) => {
        switch (filterState.sortBy) {
            case 'price-low':
                return a.price - b.price;
            case 'price-high':
                return b.price - a.price;
            case 'rating':
                return b.rating - a.rating;
            case 'newest':
                return b.id - a.id;
            default:
                return 0;
        }
    });
    
    return filtered;
}

// ===========================
// EVENT LISTENERS
// ===========================

// Category Filter
document.querySelectorAll('.category-filter').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (this.value === 'all') {
            filterState.category = 'all';
        } else {
            filterState.category = this.value;
        }
        renderProducts();
    });
});

// Search Filter
document.getElementById('searchInput')?.addEventListener('input', function() {
    filterState.search = this.value;
    renderProducts();
});

// Price Range
document.getElementById('priceRange')?.addEventListener('input', function() {
    filterState.maxPrice = parseInt(this.value);
    document.getElementById('maxPrice').textContent = formatPrice(this.value);
    renderProducts();
});

// Rating Filter
document.querySelectorAll('.rating-filter').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            filterState.ratings.push(parseInt(this.value));
        } else {
            filterState.ratings = filterState.ratings.filter(r => r !== parseInt(this.value));
        }
        renderProducts();
    });
});

// Sort
document.getElementById('sortBy')?.addEventListener('change', function() {
    filterState.sortBy = this.value;
    renderProducts();
});

// Clear Filters
document.getElementById('clearFilters')?.addEventListener('click', function() {
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

// ===========================
// INITIALIZE
// ===========================

document.addEventListener('DOMContentLoaded', function() {
    renderProducts();
    updateCartCount();
    console.log('✅ Trang danh sách sản phẩm đã được khởi tạo!');
});
