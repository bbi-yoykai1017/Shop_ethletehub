# 🏃 AthleteHub - Hướng Dẫn Cài Đặt

## 📋 Yêu Cầu Hệ Thống

Trước khi cài đặt, đảm bảo máy tính của bạn đáp ứng các yêu cầu sau:

| Thành phần | Yêu cầu tối thiểu | Khuyến nghị |
|------------|-------------------|--------------|
| Hệ điều hành | Windows 10/11, macOS, Linux | Windows 11 |
| Web Server | Apache/Nginx | Apache (XAMPP) |
| PHP | 7.4+ | 8.0+ |
| MySQL/MariaDB | 5.7+ | 10.4+ |
| RAM | 2GB | 4GB+ |
| Dung lượng đĩa | 500MB | 1GB+ |

---

## 📥 Bước 1: Tải Và Cài Đặt XAMPP

### Windows
1. Truy cập trang chủ XAMPP: https://www.apachefriends.org/
2. Tải phiên bản XAMPP cho Windows (PHP 8.0+)
3. Chạy file cài đặt `xampp-windows-x64-x.x.x.exe`
4. Làm theo các bước hướng dẫn:
   - Chọn Next > Next
   - Chọn đường dẫn cài đặt (mặc định: `C:\xampp`)
   - Đợi quá trình cài đặt hoàn tất

### Sau khi cài đặt XAMPP
1. Mở **XAMPP Control Panel**
2. Bấm nút **Start** cho:
   - **Apache** (Web Server)
   - **MySQL** (Database Server)

---

## 🗄️ Bước 2: Thiết Lập Database

### Cách 1: Sử dụng phpMyAdmin (Khuyến nghị)

1. Mở trình duyệt, truy cập: `http://localhost/phpmyadmin/`
2. Đăng nhập với:
   - **Username**: `root`
   - **Password**: (để trống)
3. Tạo database mới:
   - Click vào tab **New** (Mới)
   - Nhập tên database: `athletehub`
   - Chọn **Collation**: `utf8mb4_unicode_ci`
   - Click **Create** (Tạo)

4. Import database:
   - Click vào database `athletehub` vừa tạo
   - Click tab **Import** (Nhập)
   - Click **Choose File** và chọn file `athletehub.sql` trong thư mục project
   - Click **Go** (Thực hiện)

### Cách 2: Sử dụng Command Line

```cmd
# Mở MySQL Command Line từ XAMPP
cd C:\xampp\mysql\bin
mysql -u root

# Tạo database
CREATE DATABASE athletehub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE athletehub;

# Import database
SOURCE C:/xampp/htdocs/Shop_ethletehub/athletehub.sql;
```

---

## 📂 Bước 3: Cấu Hình Database

### Kiểm tra file Database.php

Mở file `Database.php` và kiểm tra các thông số:

```php
<?php
class Database {
    private $host = "localhost";
    private $dbname = "athletehub";
    private $user = "root";
    private $pass = "";  // Mật khẩu MySQL (để trống nếu dùng XAMPP mặc định)
    // ...
}
?>
```

**Lưu ý**: Nếu bạn đã đặt mật khẩu cho MySQL, hãy thay đổi giá trị `$pass`.

---

## 🚀 Bướchạch 4: Chạy Website

### Phương pháp 1: Sử dụng XAMPP (Khuyến nghị)

1. Copy thư mục project vào thư mục htdocs:
   ```
   C:\xampp\htdocs\Shop_ethletehub\
   ```

2. Mở trình duyệt và truy cập:
   ```
   http://localhost/Shop_ethletehub/
   ```

### Phương pháp 2: Sử dụng PHP Built-in Server

```cmd
# Mở Terminal tại thư mục project
cd C:\xampp\htdocs\Shop_ethletehub

# Chạy PHP Server
php -S localhost:8000
```

Sau đó mở trình duyệt: `http://localhost:8000/`

---

## ⚙️ Cấu Hình Bổ Sung (Tùy chọn)

### Thay đổi thông tin website

Truy cập phpMyAdmin > Database `athletehub` > Bảng `cai_dat`:

| Khóa | Mô tả | Giá trị mặc định |
|------|-------|-------------------|
| ten_website | Tên website | AthleteHub - Cửa Hàng Đồ Thể Thao |
| email_admin | Email admin | admin@athletehub.com |
| so_dien_thoai | Số điện thoại | 0912345678 |
| dia_chi | Địa chỉ | 123 Đường Thể Thao, Q1, TP.HCM |
| phi_ship_mac_dinh | Phí ship mặc định | 25000 VNĐ |
| mien_phi_ship_tu | Miễn phí ship từ | 500000 VNĐ |

### Thêm mã giảm giá

Vào phpMyAdmin > Bảng `ma_giam_gia` > Thêm mới:

```sql
INSERT INTO ma_giam_gia (ma_code, mo_ta, phan_tram_giam, so_tien_giam, don_hang_toi_thieu, giam_toi_da, ngay_bat_dau, ngay_ket_thuc, trang_thai)
VALUES ('TENMA', 'Mô tả', 10, NULL, 200000, 100000, '2026-01-01', '2026-12-31', 1);
```

---

## 🔧 Khắc Phục Sự Cố Thường Gặp

### ❌ Lỗi "Cannot connect to database"

**Nguyên nhân**: MySQL chưa được khởi động hoặc sai thông tin đăng nhập

**Giải pháp**:
1. Mở XAMPP Control Panel > Start MySQL
2. Kiểm tra lại username/password trong `Database.php`
3. Đảm bảo database `athletehub` đã được tạo

### ❌ Lỗi "404 Not Found"

**Nguyên nhân**: URL không đúng hoặc Apache chưa chạy

**Giải pháp**:
1. Kiểm tra Apache đang chạy trong XAMPP Control Panel
2. Đảm bảo đường dẫn: `http://localhost/Shop_ethletehub/`

### ❌ Lỗi hình ảnh không hiển thị

**Nguyên nhân**: Đường dẫn ảnh sai

**Giải pháp**:
1. Kiểm tra thư mục `public/` chứa đầy đủ hình ảnh
2. Đảm bảo đường dẫn trong code đúng với cấu trúc thư mục

### ❌ Lỗi PHP không hoạt động

**Nguyên nhân**: PHP chưa được cài đặt đúng cách

**Giải pháp**:
1. Kiểm tra PHP đã được thêm vào PATH
2. Chạy lệnh `php -v` trong CMD để kiểm tra

---

## 📱 Cài Đặt Trên Hosting (Production)

### Yêu cầu Hosting
- PHP 8.0+
- MySQL 5.7+
- Hỗ trợ .htaccess (nếu dùng Apache)

### Các bước:
1. **Upload files**: Upload toàn bộ file lên hosting (thường là thư mục `public_html`)
2. **Import Database**: Import file `athletehub.sql` lên MySQL của hosting
3. **Cấu hình Database**: Sửa file `Database.php` với thông tin database của hosting:
   ```php
   private $host = "localhost"; // Hoặc địa chỉ MySQL của hosting
   private $dbname = "ten_database";
   private $user = "ten_nguoi_dung";
   private $pass = "mat_khau";
   ```
4. **Cấu quyền**: Đảm bảo thư mục `uploads/` (nếu có) có quyền ghi

---

## ✅ Kiểm Tra Sau Cài Đặt

Sau khi cài đặt hoàn tất, hãy kiểm tra:

- [ ] Truy cập được trang chủ `http://localhost/Shop_ethletehub/`
- [ ] Hiển thị danh sách sản phẩm
- [ ] Click vào sản phẩm xem chi tiết
- [ ] Thêm sản phẩm vào giỏ hàng
- [ ] Kiểm tra giỏ hàng
- [ ] Áp dụng mã giảm giá (SAVE10, WELCOME,...)
- [ ] Đăng nhập/đăng ký tài khoản (nếu có chức năng)

---

## 📞 Hỗ Trợ

Nếu gặp vấn đề trong quá trình cài đặt:

- **Email**: admin@athletehub.com
- **Điện thoại**: 0912 345 678
- **Website**: www.athletehub.vn

---

**Phiên bản**: 1.0
**Cập nhật**: Tháng 3/2026

