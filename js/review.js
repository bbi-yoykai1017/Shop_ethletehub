// ===========================
// REVIEW/COMMENT JAVASCRIPT
// ===========================

let selectedRating = 0;

// Initialize rating stars
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded - Initializing review form');
    setTimeout(() => {
        initializeReviewForm();
    }, 300);
    setTimeout(() => {
        loadAllReviews();
    }, 500);
});

// Also handle if DOM already loaded
if (document.readyState === 'loading') {
    // Document still loading
} else {
    // Document already loaded
    console.log('Document already loaded - reinitializing review');
    setTimeout(() => {
        initializeReviewForm();
        loadAllReviews();
    }, 100);
}

function initializeReviewForm() {
    const reviewForm = document.querySelector('.review-form');
    if (!reviewForm) {
        console.log('Review form not found - will retry');
        setTimeout(() => initializeReviewForm(), 500);
        return;
    }
    
    console.log(' Review form found, initializing...');
    
    const ratingStars = document.querySelectorAll('.rating-input i');
    console.log('Rating stars found:', ratingStars.length);
    
    if (ratingStars.length === 0) {
        console.log('Rating stars not found - retrying');
        setTimeout(() => initializeReviewForm(), 500);
        return;
    }
    
    ratingStars.forEach((star, index) => {
        star.addEventListener('click', function() {
            selectedRating = index + 1;
            console.log('✓ Selected rating:', selectedRating);
            updateStarDisplay(selectedRating);
        });
        
        star.addEventListener('mouseover', function() {
            updateStarDisplay(index + 1);
        });
    });
    
    document.querySelector('.rating-input').addEventListener('mouseleave', function() {
        updateStarDisplay(selectedRating);
    });
    
    // Submit form
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            console.log(' Form submitted');
            e.preventDefault();
            submitReview();
        });
        console.log('✓ Submit handler attached to form');
        
        // Also attach to button as backup
        const submitBtn = reviewForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                console.log(' Submit button clicked');
                e.preventDefault();
                submitReview();
            });
            console.log(' Submit button handler attached');
        }
    }
}

function updateStarDisplay(rating) {
    const stars = document.querySelectorAll('.rating-input i');
    if (stars.length === 0) {
        console.log('Rating stars not found in updateStarDisplay');
        return;
    }
    
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas');
            star.style.color = '#ffc107';
        } else {
            star.classList.remove('fas');
            star.classList.add('far');
            star.style.color = '#ddd';
        }
    });
}

function submitReview() {
    // Get form data
    const form = document.querySelector('.review-form');
    if (!form) {
        console.log('Review form not found');
        return;
    }
    
    const tieuDeInput = form.querySelector('input[name="title"]');
    const noiDungInput = form.querySelector('textarea[name="content"]');
    
    if (!tieuDeInput || !noiDungInput) {
        console.log('Form inputs not found');
        return;
    }
    
    const tieuDe = tieuDeInput.value;
    const noiDung = noiDungInput.value;
    
    // Get product ID from form or URL
    const sanPhamIdHidden = form.querySelector('input[name="product_id"]');
    let sanPhamId = sanPhamIdHidden ? sanPhamIdHidden.value : new URLSearchParams(window.location.search).get('id');
    sanPhamId = parseInt(sanPhamId);
    console.log('San pham ID:', sanPhamId);
    
    if (!sanPhamId) {
        showNotification('Lỗi: Không tìm thấy ID sản phẩm', 'danger');
        return;
    }
    
    if (selectedRating === 0) {
        showNotification('Vui lòng chọn số sao', 'warning');
        return;
    }
    
    if (tieuDe.trim().length < 2) {
        showNotification('Tiêu đề phải tối thiểu 2 ký tự', 'warning');
        return;
    }
    
    if (noiDung.trim().length < 2) {
        showNotification('Nội dung phải tối thiểu 2 ký tự', 'warning');
        return;
    }
    
    // Combine title and content
    const binhLuan = tieuDe + '\n\n' + noiDung;
    
    console.log('Submitting review:', {
        san_pham_id: sanPhamId,
        so_sao: selectedRating,
        binh_luan: binhLuan
    });
    
    // Send to API
    fetch('api/review.php?action=add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            san_pham_id: sanPhamId,
            so_sao: selectedRating,
            binh_luan: binhLuan
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('API Response:', data);
        if (data.success) {
            showNotification(data.message, 'success');
            form.reset();
            selectedRating = 0;
            updateStarDisplay(0);
            loadAllReviews();
        } else {
            showNotification(data.message || 'Lỗi khi gửi bình luận', 'danger');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        showNotification('Lỗi kết nối server', 'danger');
    });
}

function loadAllReviews() {
    const sanPhamId = new URLSearchParams(window.location.search).get('id');

    if (!sanPhamId) {
        console.log('San pham ID not found');
        return;
    }

    console.log('Loading reviews for product:', sanPhamId);

    fetch(`api/review.php?action=getAll&san_pham_id=${sanPhamId}&limit=100`)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Reviews data:', data);
            if (data.success && data.reviews && data.reviews.length > 0) {
                const reviewsList = document.querySelector('.reviews-list');
                if (reviewsList) {
                    const reviewsContent = data.reviews.map(review => {
                        // Tạo avatar - chỉ dùng chữ cái đầu của tên
                        const initial = review.ten_nguoi_dung ? review.ten_nguoi_dung.charAt(0).toUpperCase() : 'U';
                        const bgColor = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE'][review.id % 7];

                        // Lấy current user ID từ data attribute hoặc window object
                        const currentUserId = window.currentUserId || null;
                        const userRole = window.userRole || 'user';

                        // Kiểm tra quyền xóa: user owner hoặc admin
                        const canDelete = currentUserId && (currentUserId == review.nguoi_dung_id || userRole === 'admin');

                        return `
                        <div class="review-item" data-review-id="${review.id}">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar-initial" style="background-color: ${bgColor};">
                                        ${initial}
                                    </div>
                                    <div>
                                        <h5>${escapeHtml(review.ten_nguoi_dung)}</h5>
                                        <p class="review-date">
                                            ${getStarHtml(review.so_sao)} - ${formatDate(review.ngay_danh_gia)}
                                        </p>
                                    </div>
                                </div>
                                <span class="verified-badge">
                                    <i class="fas fa-check-circle"></i> Đã xác minh
                                </span>
                            </div>
                            <p class="review-content">${escapeHtml(review.binh_luan).replace(/\n/g, '<br>')}</p>
                            <div class="review-actions">
                                <button class="helpful-btn" style="border: none; background: none; color: #666; cursor: pointer;">
                                    <i class="fas fa-thumbs-up"></i> Hữu ích (0)
                                </button>
                                ${canDelete ? `
                                    <button class="delete-btn"
                                        onclick="deleteReview(${review.id})"
                                        style="border: none; background: none; color: #dc3545; cursor: pointer; margin-left: 10px;">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    }).join('');

                    reviewsList.innerHTML = '<h4>Đánh giá từ khách hàng (' + data.total + ')</h4>' + reviewsContent;
                } else {
                    console.log('Reviews list container not found');
                }
            } else {
                console.log('No reviews found or error:', data);
            }
        })
        .catch(error => {
            console.error('Lỗi tải bình luận:', error);
        });
}

function deleteReview(reviewId) {
    if (!confirm('Bạn chắc chắn muốn xóa bình luận này?')) {
        return;
    }
    
    fetch('api/review.php?action=delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            review_id: reviewId
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Delete response:', data);
        if (data.success) {
            // Xóa item khỏi HTML ngay lập tức với animation
            const reviewElement = document.querySelector(`[data-review-id="${reviewId}"]`);
            if (reviewElement) {
                reviewElement.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                reviewElement.style.opacity = '0';
                reviewElement.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    reviewElement.remove();
                    
                    // Cập nhật số lượng đánh giá
                    const reviewsList = document.querySelector('.reviews-list h4');
                    if (reviewsList) {
                        const currentText = reviewsList.textContent;
                        const currentCount = parseInt(currentText.match(/\d+/)?.[0] || 0);
                        if (currentCount > 0) {
                            reviewsList.textContent = `Đánh giá từ khách hàng (${currentCount - 1})`;
                        }
                    }
                }, 300);
            }
            
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Lỗi khi xóa bình luận', 'danger');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        showNotification('Lỗi kết nối server', 'danger');
    });
}

function getStarHtml(rating) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            html += '<i class="fas fa-star" style="color: #ffc107;"></i>';
        } else {
            html += '<i class="far fa-star"></i>';
        }
    }
    return html;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN', { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'info') {
    // Tạo notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove sau 5 giây
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
