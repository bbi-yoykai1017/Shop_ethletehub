// ===========================
// REVIEW/COMMENT JAVASCRIPT
// ===========================

let selectedRating = 0;

// Initialize rating stars and attach listeners to existing review elements
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded - Initializing review form and attaching listeners');
    setTimeout(() => {
        initializeReviewForm();
        attachReviewEventListeners();
        loadRepliesForAllReviews();
    }, 300);
});

// Also handle if DOM already loaded
if (document.readyState === 'loading') {
    // Document still loading
} else {
    // Document already loaded
    console.log('Document already loaded - reinitializing review');
    setTimeout(() => {
        initializeReviewForm();
        attachReviewEventListeners();
        loadRepliesForAllReviews();
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
        star.addEventListener('click', function () {
            selectedRating = index + 1;
            console.log('✓ Selected rating:', selectedRating);
            updateStarDisplay(selectedRating);
        });

        star.addEventListener('mouseover', function () {
            updateStarDisplay(index + 1);
        });
    });

    document.querySelector('.rating-input').addEventListener('mouseleave', function () {
        updateStarDisplay(selectedRating);
    });

    // Submit form
    if (reviewForm) {
        reviewForm.addEventListener('submit', function (e) {
            console.log(' Form submitted');
            e.preventDefault();
            submitReview();
        });
        console.log('✓ Submit handler attached to form');

        // Also attach to button as backup
        const submitBtn = reviewForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.addEventListener('click', function (e) {
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
                // Reload page to show new review
                setTimeout(() => {
                    location.reload();
                }, 1500);
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
    // This function is no longer used - reviews are rendered server-side
    console.log('loadAllReviews is deprecated - reviews are server-rendered');
    return;
}

// Load replies for all visible review items
function loadRepliesForAllReviews() {
    const reviewItems = document.querySelectorAll('.review-item');
    console.log('Loading replies for', reviewItems.length, 'reviews');
    reviewItems.forEach(item => {
        const reviewId = item.dataset.reviewId;
        if (reviewId) {
            loadReplies(reviewId);
        }
    });
}

function deleteReview(reviewId) {
    if (!confirm('Bạn chắc chắn muốn xóa bình luận này?')) {
        return;
    }

    fetch('api/review.php?action=delete', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            review_id: reviewId
        })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Reload page to show updated reviews
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showNotification(data.message || 'Lỗi khi xóa bình luận', 'danger');
            }
        })
        .catch(error => {
            console.error('Lỗi xóa:', error);
            showNotification('Lỗi: ' + error.message, 'danger');
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

// ========== REPLY & LIKE FUNCTIONS ==========

function attachReviewEventListeners() {
    console.log('🔍 Looking for buttons...');
    // Like review buttons
    const likeButtons = document.querySelectorAll('.like-review-btn');
    console.log('Found like buttons:', likeButtons.length);

    likeButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const reviewId = this.dataset.reviewId;
            console.log('Like button clicked for review:', reviewId);
            likeReview(reviewId);
        });
    });

    // Reply buttons
    const replyButtons = document.querySelectorAll('.reply-btn');
    console.log('Found reply buttons:', replyButtons.length);

    replyButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const reviewId = this.dataset.reviewId;
            const formContainer = document.getElementById(`reply-form-${reviewId}`);
            console.log('Reply button clicked for review:', reviewId);
            if (formContainer.style.display === 'none') {
                formContainer.style.display = 'block';
                formContainer.querySelector('.reply-textarea').focus();
            } else {
                formContainer.style.display = 'none';
            }
        });
    });

    // Submit reply buttons
    document.querySelectorAll('.submit-reply-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const reviewId = this.dataset.reviewId;
            const textarea = document.getElementById(`reply-form-${reviewId}`).querySelector('.reply-textarea');
            const content = textarea.value.trim();

            if (content.length < 3) {
                showNotification('Trả lời phải tối thiểu 3 ký tự', 'warning');
                return;
            }

            submitReply(reviewId, content);
        });
    });

    // Cancel reply buttons
    document.querySelectorAll('.cancel-reply-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const reviewId = this.dataset.reviewId;
            const formContainer = document.getElementById(`reply-form-${reviewId}`);
            formContainer.style.display = 'none';
            formContainer.querySelector('.reply-textarea').value = '';
        });
    });
}

function loadReplies(reviewId) {
    fetch(`api/review.php?action=get_replies&danh_gia_id=${reviewId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Replies for review', reviewId, ':', data);
            if (data.success && data.replies && data.replies.length > 0) {
                const repliesContainer = document.getElementById(`replies-${reviewId}`);
                if (repliesContainer) {
                    const repliesHtml = data.replies.map(reply => {
                        const initial = reply.ten_nguoi_dung ? reply.ten_nguoi_dung.charAt(0).toUpperCase() : 'U';
                        const bgColor = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE'][reply.nguoi_dung_id % 7];
                        const likeIcon = reply.liked_by_user ? 'fas' : 'far';
                        const likeColor = reply.liked_by_user ? '#FF6B6B' : '#666';

                        return `
                        <div class="reply-item" data-reply-id="${reply.id}" style="padding: 12px; border-left: 3px solid #ddd; margin-bottom: 10px; background: #fff;">
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <div style="background-color: ${bgColor}; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">
                                    ${initial}
                                </div>
                                <div style="flex-grow: 1;">
                                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 5px;">
                                        <strong>${escapeHtml(reply.ten_nguoi_dung)}</strong>
                                        <small style="color: #999;">${formatDate(reply.ngay_tao)}</small>
                                    </div>
                                    <p style="margin: 8px 0; color: #333;">${escapeHtml(reply.noi_dung).replace(/\n/g, '<br>')}</p>
                                    <button class="like-reply-btn" data-reply-id="${reply.id}" style="border: none; background: none; color: ${likeColor}; cursor: pointer; font-size: 12px; display: flex; align-items: center; gap: 5px;">
                                        <i class="${likeIcon} fa-heart"></i> <span class="reply-like-count">${reply.so_like}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        `;
                    }).join('');

                    repliesContainer.innerHTML = '<div style="margin-bottom: 10px; border-top: 1px solid #eee; padding-top: 10px;"><h6 style="color: #666; font-size: 12px;">Trả lời:</h6>' + repliesHtml + '</div>';

                    // Attach like reply buttons
                    document.querySelectorAll('.like-reply-btn').forEach(btn => {
                        btn.addEventListener('click', function (e) {
                            e.preventDefault();
                            const replyId = this.dataset.replyId;
                            likeReply(replyId);
                        });
                    });
                }
            }
        })
        .catch(error => {
            console.error('Lỗi tải trả lời:', error);
        });
}

function submitReply(reviewId, content) {
    fetch('api/review.php?action=add_reply', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            danh_gia_id: reviewId,
            noi_dung: content
        })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Reply submission response:', data);
            if (data.success) {
                showNotification('Trả lời đã được thêm', 'success');
                const formContainer = document.getElementById(`reply-form-${reviewId}`);
                formContainer.style.display = 'none';
                formContainer.querySelector('.reply-textarea').value = '';
                loadReplies(reviewId);
            } else {
                showNotification(data.message || 'Lỗi khi gửi trả lời', 'danger');
            }
        })
        .catch(error => {
            console.error('Lỗi gửi trả lời:', error);
            showNotification('Lỗi: ' + error.message, 'danger');
        });
}

function likeReview(reviewId) {
    console.log('❤️ Liking review:', reviewId);
    fetch('api/review.php?action=like_review', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            danh_gia_id: reviewId
        })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('❤️ Like response:', data);
            if (data.success) {
                const btn = document.querySelector(`.like-review-btn[data-review-id="${reviewId}"]`);
                console.log('Button found:', btn);
                if (btn) {
                    const countSpan = btn.querySelector('.like-count');

                    if (data.action === 'like') {
                        btn.style.color = '#FF6B6B';
                        btn.querySelector('i').classList.remove('far');
                        btn.querySelector('i').classList.add('fas');
                    } else {
                        btn.style.color = '#666';
                        btn.querySelector('i').classList.remove('fas');
                        btn.querySelector('i').classList.add('far');
                    }

                    countSpan.textContent = data.so_like;
                    console.log('✅ Updated like count to:', data.so_like);
                } else {
                    console.log('❌ Like button not found');
                }
            } else {
                showNotification(data.message || 'Lỗi khi thích', 'danger');
            }
        })
        .catch(error => {
            console.error('Lỗi thích:', error);
            showNotification('Lỗi: ' + error.message, 'danger');
        });
    }

function likeReply(replyId) {
    fetch('api/review.php?action=like_reply', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            phan_hoi_id: replyId
        })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Like reply response:', data);
            if (data.success) {
                const btn = document.querySelector(`.like-reply-btn[data-reply-id="${replyId}"]`);
                const countSpan = btn.querySelector('.reply-like-count');

                if (data.action === 'like') {
                    btn.style.color = '#FF6B6B';
                    btn.querySelector('i').classList.remove('far');
                    btn.querySelector('i').classList.add('fas');
                } else {
                    btn.style.color = '#666';
                    btn.querySelector('i').classList.remove('fas');
                    btn.querySelector('i').classList.add('far');
                }

                countSpan.textContent = data.so_like;
            } else {
                showNotification(data.message || 'Lỗi khi thích', 'danger');
            }
        })
        .catch(error => {
            console.error('Lỗi thích reply:', error);
            showNotification('Lỗi: ' + error.message, 'danger');
        });
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
