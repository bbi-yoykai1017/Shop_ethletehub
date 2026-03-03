# 🏃 AthleteHub - Website Bán Đồ Thể Thao

Đây là một website bán đồ thể thao online hiện đại, được xây dựng với Bootstrap 5 và CSS3.

## 📁 Cấu Trúc Thư Mục

```
project/
├── index.html                 # File HTML chính
├── css/
│   ├── variables.css         # Biến CSS và cài đặt chung
│   ├── navbar.css            # Style cho Navbar
│   ├── hero.css              # Style cho Hero Section
│   ├── categories.css        # Style cho mục Danh mục
│   ├── products.css          # Style cho mục Sản phẩm
│   ├── footer.css            # Style cho Footer
│   └── utilities.css         # Utility classes và helpers
├── js/
│   └── script.js             # JavaScript cho chức năng tương tác
└── README.md                 # File hướng dẫn này
```

## 🎨 Màu Sắc Chính

- **Primary**: #ff6b35 (Cam)
- **Secondary**: #004e89 (Xanh đậm)
- **Accent**: #f7b801 (Vàng)
- **Dark**: #1a1a1a (Đen)
- **Light**: #f8f9fa (Trắng xám nhạt)

## 🚀 Các Tính Năng

### 1. **Navbar (Thanh điều hướng)**
- Menu điều hướng responsive
- Biểu tượng giỏ hàng với đếm sản phẩm
- Thông báo
- Tài khoản người dùng
- Effect cuộn động

### 2. **Hero Section (Phần nổi bật)**
- Banner chào mừng với animation
- Nút Call-to-Action
- Thống kê (Khách hàng, Sản phẩm, Đánh giá)
- Responsive design

### 3. **Danh Mục Sản Phẩm**
- 4 danh mục chính: Quần áo, Giày, Thiết bị, Phụ kiện
- Card hover effect
- Bộ lọc sản phẩm

### 4. **Sản Phẩm**
- Grid layout responsive
- Thẻ sản phẩm với thông tin chi tiết
- Giá gốc & giá chiết khấu
- Đánh giá sao
- Nút "Thêm vào giỏ" & "Yêu thích"
- Quick view modal
- Badge "Sale", "New", "Hot"

### 5. **Footer (Chân trang)**
- Thông tin công ty
- Liên kết nhanh
- Hỗ trợ khách hàng
- Thông tin liên lạc
- Newsletter đăng ký
- Social media links
- Phương thức thanh toán

### 6. **JavaScript Tương Tác**
- Thêm sản phẩm vào giỏ hàng
- Quản lý wishlist
- Quick view sản phẩm
- Lọc sản phẩm theo danh mục
- Newsletter form
- Back to top button
- Thông báo (notifications)

## 🛠️ Công Nghệ Sử Dụng

- **HTML5**: Cấu trúc trang
- **CSS3**: Styling & animations
- **Bootstrap 5**: Framework responsive
- **JavaScript (Vanilla)**: Chức năng tương tác
- **Font Awesome 6**: Icons
- **CSS Grid & Flexbox**: Layout

## 📱 Responsive Design

Website được tối ưu hóa cho:
- 📱 Mobile (< 576px)
- 📱 Tablet (576px - 992px)
- 🖥️ Desktop (> 992px)

## 🎯 Cách Sử Dụng

### 1. **Cài đặt & Mở**
```bash
1. Tải tất cả các file vào một thư mục
2. Mở file `index.html` trong trình duyệt
```

### 2. **Tùy chỉnh Màu Sắc**
Mở file `css/variables.css` và thay đổi các biến CSS:
```css
:root {
    --primary: #ff6b35;        /* Thay đổi màu chính */
    --secondary: #004e89;      /* Thay đổi màu phụ */
    --accent: #f7b801;         /* Thay đổi màu nhấn */
}
```

### 3. **Thêm Sản Phẩm Mới**
Thêm code này vào phần `products-grid` trong `index.html`:
```html
<div class="product-card">
    <div class="product-image">
        <i class="fas fa-icon-name"></i>
        <span class="product-badge">New</span>
    </div>
    <div class="product-info">
        <div class="product-category">Danh mục</div>
        <h3 class="product-name">Tên sản phẩm</h3>
        <!-- ... thêm chi tiết khác ... -->
    </div>
</div>
```

### 4. **Thay Đổi Text & Nội Dung**
Tất cả text có thể chỉnh sửa trực tiếp trong file HTML.

## 🎨 Tùy Chỉnh Style

### Thay đổi Font
Mở `css/variables.css`:
```css
--font-primary: 'Tên font mới', sans-serif;
--font-display: 'Tên font display mới', sans-serif;
```

### Thay đổi Spacing (Khoảng cách)
```css
--spacing-lg: 2rem;    /* Tăng khoảng cách lớn */
--spacing-xl: 3rem;    /* Tăng khoảng cách rất lớn */
```

### Thay đổi Border Radius
```css
--border-radius: 16px;      /* Góc tròn hơn */
--border-radius-lg: 20px;   /* Góc tròn rất lớn */
```

## 📊 Tính Năng JavaScript Chi Tiết

### Thêm vào giỏ hàng
```javascript
// Tự động cập nhật số lượng giỏ hàng
// Hiển thị thông báo thành công
```

### Wishlist
```javascript
// Thêm/xóa khỏi danh sách yêu thích
// Lưu trạng thái nút
```

### Filter sản phẩm
```javascript
// Lọc theo danh mục
// Animate hiển thị/ẩn
```

## 🔧 Mở Rộng Chức Năng

### Thêm Backend (PHP/Node.js)
1. Kết nối database
2. Xử lý đơn đặt hàng
3. Quản lý người dùng

### Tích Hợp Payment Gateway
- Thêm VNPay, Stripe, PayPal
- Xử lý thanh toán

### Tối Ưu SEO
- Thêm meta tags
- Structured data (Schema.org)
- Sitemap & robots.txt

## 📝 License

Tự do sử dụng cho dự án cá nhân & thương mại

## 👨‍💻 Hỗ Trợ

Nếu gặp vấn đề hoặc cần tùy chỉnh, vui lòng liên hệ qua email hoặc điện thoại.

---

**Made with ❤️ for AthleteHub**
