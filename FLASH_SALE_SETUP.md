# 🔥 Flash Sale Feature - Hướng Dẫn Setup

## 📋 Danh Sách File Được Tạo

### 1. **Database Migration**
- `migrations/001_create_flash_sale_tables.sql` - File SQL tạo bảng

### 2. **Backend (PHP)**
- `model/flash_sale.php` - Các hàm xử lý flash sale
- `api/flash-sale.php` - API endpoint lấy dữ liệu flash sale
- `CRUDflashsale.php` - Trang admin quản lý flash sale
- `flash-sale-products-admin.php` - Trang quản lý sản phẩm flash sale

### 3. **Frontend**
- `js/flash-sale.js` - JavaScript countdown timer
- `css/flash-sale.css` - CSS styling
- `index.php` - Cập nhật để thêm flash sale section

## 🚀 Các Bước Setup

### Step 1: Tạo Bảng Database

Chạy SQL script để tạo bảng:

```sql
-- Bảng Flash Sale (chương trình khuyến mãi cho các ngày lễ)
CREATE TABLE IF NOT EXISTS flash_sale (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ten_chuong_trinh VARCHAR(255) NOT NULL COMMENT 'Tên chương trình (Tết, 8/3, Black Friday...)',
    ngay_bat_dau DATETIME NOT NULL COMMENT 'Ngày bắt đầu flash sale',
    ngay_ket_thuc DATETIME NOT NULL COMMENT 'Ngày kết thúc flash sale',
    ngay_cap_nhat_truoc INT DEFAULT 1 COMMENT 'Cập nhật trước bao nhiêu ngày (mặc định 1 ngày)',
    ghi_chu TEXT COMMENT 'Mô tả chương trình',
    trang_thai TINYINT DEFAULT 1 COMMENT '1=Active, 0=Inactive',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng liên kết Flash Sale với Sản phẩm
CREATE TABLE IF NOT EXISTS flash_sale_products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    flash_sale_id INT NOT NULL,
    san_pham_id INT NOT NULL,
    gia_giam_gia DECIMAL(10, 2) NOT NULL COMMENT 'Giá mới trong flash sale',
    so_luong_gioi_han INT COMMENT 'Số lượng giới hạn (NULL = không giới hạn)',
    so_luong_da_ban INT DEFAULT 0 COMMENT 'Số lượng đã bán',
    trang_thai TINYINT DEFAULT 1,
    FOREIGN KEY (flash_sale_id) REFERENCES flash_sale(id) ON DELETE CASCADE,
    FOREIGN KEY (san_pham_id) REFERENCES san_pham(id) ON DELETE CASCADE,
    UNIQUE KEY unique_flash_product (flash_sale_id, san_pham_id)
);

CREATE INDEX idx_flash_sale_status ON flash_sale(trang_thai);
CREATE INDEX idx_flash_sale_dates ON flash_sale(ngay_bat_dau, ngay_ket_thuc);
CREATE INDEX idx_flash_sale_products_status ON flash_sale_products(trang_thai);
```

### Step 2: Thêm Dữ Liệu Test

```sql
-- Thêm flash sale cho Tết
INSERT INTO flash_sale (ten_chuong_trinh, ngay_bat_dau, ngay_ket_thuc, ngay_cap_nhat_truoc, ghi_chu, trang_thai)
VALUES (
    'Khuyến Mãi Tết',
    '2026-01-27 00:00:00',
    '2026-02-02 23:59:59',
    1,
    'Khuyến mãi đặc biệt dịp Tết Nguyên Đán',
    1
);

-- Hoặc flash sale khác
INSERT INTO flash_sale (ten_chuong_trinh, ngay_bat_dau, ngay_ket_thuc, ghi_chu, trang_thai)
VALUES (
    'Black Friday 2026',
    '2026-11-27 00:00:00',
    '2026-11-29 23:59:59',
    'Giảm giá khủng dịp Black Friday',
    1
);
```

### Step 3: Thêm Sản Phẩm vào Flash Sale

```sql
-- Thêm sản phẩm vào flash sale (ID flash sale là 1)
INSERT INTO flash_sale_products (flash_sale_id, san_pham_id, gia_giam_gia, so_luong_gioi_han, trang_thai)
VALUES 
    (1, 1, 299000, 50, 1),   -- Sản phẩm 1, giá mới 299k, giới hạn 50 cái
    (1, 5, 450000, 100, 1),  -- Sản phẩm 5, giá mới 450k, giới hạn 100 cái
    (1, 10, 699000, NULL, 1); -- Sản phẩm 10, giá mới 699k, không giới hạn
```

## 📱 Cách Sử Dụng

### Cho Admin:
1. Đăng nhập vào admin panel
2. Vào **Quản Lý > Flash Sale**
3. Thêm flash sale mới (nhập tên, ngày bắt đầu, kết thúc)
4. Cấu hình "Cập nhật trước X ngày" (mặc định 1 ngày)
5. Nhấp **Lưu**
6. Click vào icon sản phẩm để thêm sản phẩm vào flash sale
7. Chọn sản phẩm, nhập giá mới, (tùy chọn) số lượng giới hạn

### Cho Khách Hàng:
1. Vào trang chủ (index.php)
2. Xem section **Flash Sale** với countdown timer
3. Xem sản phẩm flash sale với % giảm
4. Bấm **Xem chi tiết** để mua hoặc xem tất cả

## ⏰ Cách Hoạt Động của Countdown Timer

**JavaScript (`js/flash-sale.js`) thực hiện:**
1. ✅ Tải danh sách flash sale sắp tới từ API
2. ✅ Tính toán thời gian còn lại
3. ✅ Cập nhật countdown mỗi giây
4. ✅ Hiển thị sản phẩm Flash Sale
5. ✅ Tự động dừng timer khi kết thúc

**API (`api/flash-sale.php`) cung cấp:**
- `GET /api/flash-sale.php?action=get-upcoming` - Lấy flash sale sắp tới
- `GET /api/flash-sale.php?action=get-current` - Lấy flash sale đang diễn ra
- `GET /api/flash-sale.php?action=get-product-details&flash_sale_id=1` - Chi tiết sản phẩm

**Cập nhật tự động:**
- Cập nhật sẽ xuất hiện **1 ngày trước** khi flash sale bắt đầu (có thể thay đổi)
- Nếu muốn thay đổi, sửa giá trị `days_before` trong index.php

## 🎨 Tùy Chỉnh Styling

Chỉnh sửa file `css/flash-sale.css`:
- **Màu sắc**: Thay đổi CSS variables `--flash-primary`, `--flash-secondary`
- **Kích thước**: Chỉnh sửa `font-size`, `padding`
- **Animation**: Sửa trong `@keyframes`

## 📊 Theo Dõi Hiệu Suất

Thống kê sản phẩm flash sale trong bảng `flash_sale_products`:
- `so_luong_da_ban` - Số lượng đã bán
- `so_luong_gioi_han` - Số lượng giới hạn
- Tính % bán hết = `so_luong_da_ban / so_luong_gioi_han * 100`

## 🔧 Troubleshooting

| Vấn đề | Giải pháp |
|-------|---------|
| Flash sale không hiển thị | Kiểm tra `trang_thai = 1` và thời gian hợp lệ |
| Countdown không chạy | Kiểm tra trình duyệt có support JavaScript không |
| API trả về lỗi 404 | Kiểm tra file `api/flash-sale.php` tồn tại |
| Sản phẩm không hiển thị | Kiểm tra `flash_sale_products.trang_thai = 1` |

## 📝 Ghi Chú

- Flash sale sẽ **tự động ẩn** khi hết thời gian kết thúc
- Countdown timer **cập nhật real-time** cho mỗi khách hàng
- Hỗ trợ **vô hạn sản phẩm** trong 1 flash sale
- Có thể giới hạn số lượng bán từng sản phẩm
