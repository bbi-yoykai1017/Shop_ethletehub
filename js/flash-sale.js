/**
 * Flash Sale Countdown Timer
 * Quản lý đếm ngược thời gian cho flash sale
 */

class FlashSaleTimer {
    constructor() {
        this.timers = new Map();
        this.loadFlashSales();
    }

    /**
     * Tải flash sale từ API
     */
    async loadFlashSales() {
        try {
            const response = await fetch('api/flash-sale.php?action=get-upcoming&days_before=1');
            const result = await response.json();
            
            if (result.success && result.data) {
                this.renderFlashSales(result.data);
                result.data.forEach(sale => {
                    this.startCountdown(sale.id, new Date(sale.ngay_ket_thuc));
                });
            }
        } catch (error) {
            console.error('Lỗi tải flash sale:', error);
        }
    }

    /**
     * Bắt đầu đếm ngược cho một flash sale
     */
    startCountdown(saleId, endDate) {
        // Dừng timer cũ nếu có
        if (this.timers.has(saleId)) {
            clearInterval(this.timers.get(saleId));
        }

        const updateCountdown = () => {
            const now = new Date().getTime();
            const distance = endDate.getTime() - now;

            if (distance < 0) {
                // Flash sale kết thúc
                this.updateDisplay(saleId, {
                    days: 0,
                    hours: 0,
                    minutes: 0,
                    seconds: 0,
                    ended: true
                });
                clearInterval(this.timers.get(saleId));
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            this.updateDisplay(saleId, {
                days,
                hours,
                minutes,
                seconds,
                ended: false
            });
        };

        // Cập nhật lần đầu ngay
        updateCountdown();

        // Cập nhật mỗi giây
        const intervalId = setInterval(updateCountdown, 1000);
        this.timers.set(saleId, intervalId);
    }

    /**
     * Cập nhật hiển thị countdown
     */
    updateDisplay(saleId, time) {
        const timerElements = document.querySelectorAll(`[data-flash-sale-id="${saleId}"]`);
        
        timerElements.forEach(element => {
            const daysEl = element.querySelector('.flash-days');
            const hoursEl = element.querySelector('.flash-hours');
            const minutesEl = element.querySelector('.flash-minutes');
            const secondsEl = element.querySelector('.flash-seconds');

            if (daysEl) daysEl.textContent = String(time.days).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(time.hours).padStart(2, '0');
            if (minutesEl) minutesEl.textContent = String(time.minutes).padStart(2, '0');
            if (secondsEl) secondsEl.textContent = String(time.seconds).padStart(2, '0');

            // Thêm class ended nếu kết thúc
            if (time.ended) {
                element.classList.add('flash-sale-ended');
                element.querySelector('.flash-status').textContent = 'Đã kết thúc';
            }
        });
    }

    /**
     * Render flash sales lên trang
     */
    renderFlashSales(flashSales) {
        const container = document.getElementById('flash-sale-container');
        if (!container) return;

        if (flashSales.length === 0) {
            container.innerHTML = '<p class="text-center text-muted">Chưa có flash sale nào sắp tới</p>';
            return;
        }

        const html = flashSales.map(sale => `
            <div class="flash-sale-card" data-flash-sale-id="${sale.id}">
                <div class="flash-sale-header">
                    <h3 class="flash-sale-title">${this.escapeHtml(sale.ten_chuong_trinh)}</h3>
                    <div class="flash-countdown">
                        <div class="countdown-item">
                            <span class="flash-days">00</span>
                            <label>Ngày</label>
                        </div>
                        <span class="separator">:</span>
                        <div class="countdown-item">
                            <span class="flash-hours">00</span>
                            <label>Giờ</label>
                        </div>
                        <span class="separator">:</span>
                        <div class="countdown-item">
                            <span class="flash-minutes">00</span>
                            <label>Phút</label>
                        </div>
                        <span class="separator">:</span>
                        <div class="countdown-item">
                            <span class="flash-seconds">00</span>
                            <label>Giây</label>
                        </div>
                    </div>
                    <span class="flash-status badge bg-danger">Sắp bắt đầu</span>
                </div>
                
                <div class="flash-products-slider">
                    ${sale.products.slice(0, 6).map(product => `
                        <div class="flash-product-item">
                            <div class="product-image-wrapper">
                                <img src="${this.escapeHtml(product.image)}" alt="${this.escapeHtml(product.name)}" class="product-image">
                                <span class="discount-badge">-${product.discount}%</span>
                            </div>
                            <div class="product-info">
                                <p class="product-name">${this.escapeHtml(product.name)}</p>

                               

                                <div class="product-price">
                                    <span class="price-new">${this.formatPrice(product.price)}</span>
                                    <span class="price-old">${this.formatPrice(product.originalPrice)}</span>
                                </div>
                                ${product.so_luong_gioi_han ? `
                                    <div class="stock-info">
                                        <small>Còn: ${product.so_luong_gioi_han - product.so_luong_da_ban}</small>
                                        <div class="stock-bar">
                                            <div class="stock-fill" style="width: ${(product.so_luong_da_ban / product.so_luong_gioi_han) * 100}%"></div>
                                        </div>
                                    </div>
                                ` : ''}
                                <a href="product-detail.php?id=${product.id}" class="btn btn-sm btn-primary w-100">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    `).join('')}
                </div>
                
                <div class="flash-sale-footer">
                    <a href="#" class="btn btn-outline-primary btn-sm" onclick="window.location.href='products.php?flash_sale=${sale.id}'; return false;">
                        Xem tất cả Flash Sale →
                    </a>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    /**
     * Render sao rating
     */
    renderStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= Math.floor(rating)) {
                stars += '<i class="fas fa-star"></i>';
            } else if (i - 0.5 <= rating) {
                stars += '<i class="fas fa-star-half-alt"></i>';
            } else {
                stars += '<i class="far fa-star"></i>';
            }
        }
        return stars;
    }

    /**
     * Format giá tiền VND
     */
    formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
            minimumFractionDigits: 0
        }).format(price);
    }

    /**
     * Escape HTML để tránh XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Dừng tất cả timers
     */
    destroy() {
        this.timers.forEach(intervalId => clearInterval(intervalId));
        this.timers.clear();
    }
}

// Khởi tạo khi DOM sẵn sàng
document.addEventListener('DOMContentLoaded', () => {
    window.flashSaleTimer = new FlashSaleTimer();
});

// Cleanup khi rời trang
window.addEventListener('beforeunload', () => {
    if (window.flashSaleTimer) {
        window.flashSaleTimer.destroy();
    }
});
