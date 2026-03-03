# 🏃 AthleteHub - Website Bán Đồ Thể Thao Online

Một website bán đồ thể thao hiện đại, chuyên nghiệp được xây dựng với Bootstrap 5 và CSS3 tách biệt.

## 📁 Cấu Trúc Thư Mục

```
athletehub/
├── index.html                    # 🏠 Trang chủ
├── products.html                 # 🛍️ Danh sách sản phẩm với filter
├── product-detail.html           # 📝 Chi tiết sản phẩm
├── cart.html                     # 🛒 Giỏ hàng
│
├── css/
│   ├── variables.css            # Biến CSS & cài đặt chung
│   ├── navbar.css               # Navbar styles
│   ├── hero.css                 # Hero section styles
│   ├── categories.css           # Danh mục styles
│   ├── products.css             # Sản phẩm styles
│   ├── product-detail.css       # Chi tiết sản phẩm styles
│   ├── products-page.css        # Danh sách sản phẩm styles
│   ├── cart.css                 # Giỏ hàng styles
│   ├── footer.css               # Footer styles
│   └── utilities.css            # Utility classes
│
├── js/
│   ├── script.js                # JavaScript chính (trang chủ)
│   ├── product-detail.js        # JS chi tiết sản phẩm
│   ├── products-page.js         # JS danh sách sản phẩm
│   └── cart.js                  # JS giỏ hàng
│
└── README.md                     # Tài liệu này
```

## 📄 Chi Tiết Các Trang

### 1️⃣ **index.html** - Trang Chủ
✅ Hero banner với animation
✅ Danh mục sản phẩm (4 loại)
✅ Sản phẩm nổi bật (6 sản phẩm)
✅ Newsletter subscription
✅ Footer đầy đủ

### 2️⃣ **products.html** - Danh Sách Sản Phẩm
🔍 **Sidebar Filter**:
- Tìm kiếm theo tên
- Lọc theo danh mục
- Lọc theo giá (range slider)
- Lọc theo kích thước
- Lọc theo đánh giá

📊 **Sorting Options**:
- Phổ biến nhất
- Mới nhất
- Giá: thấp → cao
- Giá: cao → thấp
- Đánh giá cao nhất
- Bán chạy nhất

✨ Grid sản phẩm responsive với 12 sản phẩm mẫu
📄 Pagination

### 3️⃣ **product-detail.html** - Chi Tiết Sản Phẩm
🖼️ **Gallery**:
- Main image với zoom
- 4 ảnh thumbnail
- Click để thay đổi

📋 **Thông Tin Sản Phẩm**:
- Tên, mô tả đầy đủ
- Giá gốc & giá chiết khấu
- Tồn kho & cảnh báo
- Đánh giá sao (4.8/5)

🎯 **Lựa Chọn**:
- Size (XS, S, M, L, XL, XXL)
- Màu sắc (4 lựa chọn)
- Số lượng (increments)

🛒 **Action Buttons**:
- Thêm vào giỏ hàng
- Mua ngay
- Thêm vào yêu thích

📑 **Tabs**:
- Mô tả sản phẩm
- Thông số kỹ thuật
- Đánh giá & bình luận

🔗 **Khác**:
- Sản phẩm liên quan
- Share trên mạng xã hội
- Thông tin vận chuyển

### 4️⃣ **cart.html** - Giỏ Hàng
📦 **Danh Sách Sản Phẩm**:
- Hình ảnh, tên, giá
- Thay đổi số lượng
- Xóa sản phẩm

💰 **Tóm Tắt Đơn Hàng**:
- Tổng tiền hàng
- Phí vận chuyển (miễn phí trên 500k)
- Mã giảm giá:
  - SAVE10 → 50.000₫
  - SAVE20 → 100.000₫
  - SHIP → 25.000₫ (vận chuyển)
  - WELCOME → 75.000₫
- Tổng cộng

✅ **Lợi Ích**:
- Giao hàng miễn phí
- Hoàn lại 30 ngày
- Bảo mật thanh toán

🎁 **Sản Phẩm Có Thể Quan Tâm**

## 🎨 Thiết Kế & Màu Sắc

### Palette Màu
```
Primary (Chính):     #ff6b35 - Cam sống động
Secondary (Phụ):     #004e89 - Xanh đậm
Accent (Nhấn):       #f7b801 - Vàng
Success (Thành công): #06d6a0 - Xanh lá
Warning (Cảnh báo):  #ffa500 - Cam nhạt
Danger (Nguy hiểm):  #ef476f - Đỏ
Dark (Tối):          #1a1a1a - Đen
Light (Sáng):        #f8f9fa - Trắng xám
```

### Typography
- **Display Font**: Trebuchet MS (Heading)
- **Body Font**: Segoe UI (Nội dung)
- **Font Weights**: 400, 600, 700, 800, 900

### Spacing System
- xs: 0.25rem  | sm: 0.5rem  | md: 1rem
- lg: 1.5rem  | xl: 2rem    | 2xl: 3rem | 3xl: 4rem

## 🚀 Tính Năng Chính

### ✨ Navbar
- Menu sticky với animation
- Biểu tượng giỏ hàng (với số lượng)
- Thông báo, tài khoản
- Responsive toggler menu
- Search bar

### 🎯 Hero Section
- Banner gradient background
- Animation floating elements
- Multiple CTA buttons
- Statistics (50K+ customers, 1000+ products, 4.8★ rating)

### 📂 Categories
- 4 danh mục (Quần áo, Giày, Thiết bị, Phụ kiện)
- Hover card animation
- Badge đếm sản phẩm

### 🛍️ Products
- Grid layout responsive
- Product badges (Sale, New, Hot)
- Rating stars
- Quick view modal
- Add to cart & wishlist buttons
- Discount badges

### 🔍 Advanced Filtering
- Real-time search
- Multi-select filters
- Price range slider
- Multiple sorting options
- Clear all filters button

### 🛒 Shopping Cart
- LocalStorage persistence
- Quantity controls
- Auto-calculate totals
- Promo code system
- Free shipping threshold (500k)
- Related products

### 👥 User Experience
- Smooth animations (0.3s transitions)
- Hover effects
- Loading states
- Toast notifications
- Responsive design (mobile-first)

## 📊 Số Liệu

| Trang | Sản phẩm | Filter | CTA |
|-------|----------|--------|-----|
| Trang chủ | 6 | — | 2 |
| Danh sách | 12 | 5 loại | 12 |
| Chi tiết | 1 | — | 3 |
| Giỏ hàng | Dynamic | — | 2 |

## 💾 Lưu Trữ Dữ Liệu

**LocalStorage Keys**:
- `cart` - Danh sách sản phẩm trong giỏ
- `cartDiscount` - Giảm giá từ mã promo
- Wishlist (nếu mở rộng)

**Format Cart Item**:
```javascript
{
    id: 1,
    name: "Áo tập Pro Performance",
    price: 299000,
    quantity: 1,
    image: "url",
    category: "quan-ao"
}
```

## 🔧 Cài Đặt & Sử Dụng

### 1️⃣ Cài Đặt Ban Đầu
```bash
1. Clone hoặc tải project
2. Mở index.html trong trình duyệt
3. Hoặc sử dụng Live Server
```

### 2️⃣ Tùy Chỉnh Màu
File: `css/variables.css`
```css
:root {
    --primary: #ff6b35;
    --secondary: #004e89;
    --accent: #f7b801;
}
```

### 3️⃣ Thêm Sản Phẩm
File: `js/products-page.js`
```javascript
const allProducts = [
    {
        id: 13,
        name: "Tên sản phẩm",
        category: "quan-ao",
        price: 299000,
        originalPrice: 399000,
        rating: 4.7,
        reviews: 85
    }
];
```

### 4️⃣ Thêm Mã Giảm Giá
File: `js/cart.js`
```javascript
const promoCodes = {
    'MYCODE': 100000,  // Giảm 100k
    'WELCOME': 50000
};
```

## 📱 Responsive Breakpoints

- **Mobile**: < 576px
- **Tablet**: 576px - 992px
- **Desktop**: > 992px

## 🎯 Tối Ưu Hóa

✅ **Performance**:
- CSS tách biệt (9 file)
- Minify code (có thể)
- Lazy loading images
- Smooth animations

✅ **SEO**:
- Semantic HTML
- Meta tags
- Structured data ready
- Mobile-friendly

✅ **UX**:
- Consistent styling
- Intuitive navigation
- Fast interactions
- Error handling

## 🚀 Mở Rộng (Tương Lai)

### Backend
```
[ ] Database (MySQL/MongoDB)
[ ] User authentication
[ ] Payment gateway (VNPay)
[ ] Email notifications
[ ] Order management
```

### Frontend
```
[ ] Wishlist management
[ ] User reviews
[ ] Live chat support
[ ] Push notifications
[ ] PWA features
```

### Analytics
```
[ ] Google Analytics
[ ] Conversion tracking
[ ] User behavior
[ ] Sales reports
```

## 🛠️ Công Nghệ

| Công Nghệ | Phiên Bản | Mục Đích |
|-----------|----------|---------|
| Bootstrap | 5.3.2 | Framework CSS |
| Font Awesome | 6.4.0 | Icons |
| Vanilla JS | ES6+ | Interactivity |
| LocalStorage | Native | Data persistence |
| CSS3 | Latest | Styling & Animations |
| HTML5 | latest | Markup |

## 📚 Hướng Dẫn Nhanh

| Tác vụ | Vị trí | Cách làm |
|-------|-------|---------|
| Thay đổi logo | navbar.css | Chỉnh icon/text |
| Thêm category | index.html | Sao chép card |
| Sửa giá | products-page.js | Thay đổi giá trị |
| Thay font | variables.css | Chỉnh --font-primary |
| Thêm page | — | Tạo file HTML mới |

## 💡 Tips & Tricks

1. **Tối ưu ảnh**: Sử dụng placeholder CDN
2. **Testing**: Dùng DevTools để test responsive
3. **Backup**: Lưu version cũ trước khi sửa
4. **Minify**: Compress CSS/JS cho production
5. **Cache**: Dùng service workers

## 🐛 Troubleshooting

| Vấn đề | Giải pháp |
|-------|----------|
| Style không load | Kiểm tra đường dẫn CSS |
| Icon không hiện | Cập nhật FontAwesome CDN |
| Giỏ hàng trống | Xóa localStorage |
| Responsive lỗi | Kiểm tra meta viewport |
| Animation lag | Giảm animation duration |

## 📞 Support

**Email**: support@athletehub.vn
**Phone**: +84 (0) 123 456 789
**Website**: www.athletehub.vn

## 📄 License

MIT License - Tự do sử dụng cho dự án cá nhân & thương mại

## 👨‍💻 Author

**AthleteHub Development Team**
- Design & Frontend
- Responsive & Optimized
- Modern & Professional

---

**⭐ Nếu bạn thích project này, vui lòng cho sao!**

**Latest Update**: March 2024
**Version**: 2.0
**Status**: Production Ready ✅

Made with ❤️ for fitness enthusiasts worldwide
