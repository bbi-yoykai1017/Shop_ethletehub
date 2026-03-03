// ===========================
// CATEGORIES DROPDOWN & SEARCH
// ===========================

document.addEventListener('DOMContentLoaded', function() {
    
    // ===========================
    // DROPDOWN FUNCTIONALITY
    // ===========================
    
    const dropdownBtn = document.getElementById('categoriesDropdownBtn');
    const dropdownMenu = document.getElementById('categoriesDropdownMenu');
    
    if (dropdownBtn && dropdownMenu) {
        // Toggle dropdown on button click
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
            dropdownBtn.classList.toggle('active');
        });
        
        // Close dropdown when clicking a menu item
        document.querySelectorAll('.dropdown-cat-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                dropdownMenu.classList.remove('show');
                dropdownBtn.classList.remove('active');
                
                // Smooth scroll to category
                const categoryId = this.getAttribute('href');
                const categoryElement = document.querySelector(categoryId);
                if (categoryElement) {
                    categoryElement.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.categories-dropdown-wrapper')) {
                dropdownMenu.classList.remove('show');
                dropdownBtn.classList.remove('active');
            }
        });
        
        // Close dropdown on window resize (mobile to desktop)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                dropdownMenu.classList.remove('show');
                dropdownBtn.classList.remove('active');
            }
        });
    }
    
    // ===========================
    // SEARCH FUNCTIONALITY
    // ===========================
    
    const categorySearch = document.getElementById('categorySearch');
    const categoriesGrid = document.getElementById('categoriesGrid');
    const categoryCards = categoriesGrid.querySelectorAll('.category-card');
    
    if (categorySearch) {
        categorySearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            categoryCards.forEach(card => {
                const categoryName = card.querySelector('h3').textContent.toLowerCase();
                const categoryDesc = card.querySelector('p').textContent.toLowerCase();
                
                if (categoryName.includes(searchTerm) || categoryDesc.includes(searchTerm)) {
                    card.style.display = 'block';
                    card.style.animation = 'slideUp 0.5s ease-out';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show "no results" message if needed
            const visibleCards = Array.from(categoryCards).filter(card => card.style.display !== 'none');
            if (visibleCards.length === 0 && searchTerm !== '') {
                if (!document.querySelector('.no-results-message')) {
                    const noResults = document.createElement('div');
                    noResults.className = 'no-results-message';
                    noResults.innerHTML = `
                        <div style="grid-column: 1/-1; text-align: center; padding: 40px 20px;">
                            <i class="fas fa-search" style="font-size: 3rem; color: var(--gray-light); margin-bottom: 20px; display: block;"></i>
                            <h3 style="color: var(--dark); margin-bottom: 10px;">Không tìm thấy danh mục</h3>
                            <p style="color: var(--gray);">Vui lòng thử tìm kiếm khác</p>
                        </div>
                    `;
                    categoriesGrid.appendChild(noResults);
                }
            } else {
                const noResultsMsg = document.querySelector('.no-results-message');
                if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            }
        });
        
        // Clear search on focus (optional - for better UX)
        categorySearch.addEventListener('focus', function() {
            if (this.value === '') {
                // Focus state styling
                this.style.borderColor = 'var(--primary)';
            }
        });
        
        categorySearch.addEventListener('blur', function() {
            this.style.borderColor = '';
        });
    }
    
    // ===========================
    // NAVBAR SEARCH
    // ===========================
    
    const navbarSearchInput = document.querySelector('.search-input');
    const searchBtn = document.querySelector('.search-btn');
    
    if (searchBtn) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const searchTerm = navbarSearchInput.value.trim();
            
            if (searchTerm) {
                // Redirect to products page with search term
                // window.location.href = `products.html?search=${encodeURIComponent(searchTerm)}`;
                console.log('Search for:', searchTerm);
                showNotification(`Tìm kiếm: "${searchTerm}"`, 'info');
            } else {
                showNotification('Vui lòng nhập từ khóa tìm kiếm', 'warning');
            }
        });
        
        // Search on Enter key
        navbarSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchBtn.click();
            }
        });
    }
    
    // ===========================
    // CATEGORY CARD CLICK
    // ===========================
    
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', function() {
            const categoryName = this.querySelector('h3').textContent;
            console.log('Clicked category:', categoryName);
            // Can redirect to filtered products page
            // window.location.href = `products.html?category=${categoryName.toLowerCase()}`;
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
    
    console.log('✅ Categories dropdown & search initialized!');
});