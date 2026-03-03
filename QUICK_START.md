╔════════════════════════════════════════════════════════════════════════════╗
║        🏃 AthleteHub - Website Bán Đồ Thể Thao Online - QUICK START      ║
║                    Hướng Dẫn Nhanh Bắt Đầu                               ║
╚════════════════════════════════════════════════════════════════════════════╝

📋 DANH SÁCH FILE
════════════════════════════════════════════════════════════════════════════

📄 HTML (4 trang):
  ✅ index.html               - Trang chủ
  ✅ products.html            - Danh sách sản phẩm
  ✅ product-detail.html      - Chi tiết sản phẩm
  ✅ cart.html                - Giỏ hàng

🎨 CSS (9 file):
  ✅ css/variables.css        - Biến & màu sắc
  ✅ css/navbar.css           - Navbar
  ✅ css/hero.css             - Hero section
  ✅ css/categories.css       - Danh mục
  ✅ css/products.css         - Sản phẩm (trang chủ)
  ✅ css/product-detail.css   - Chi tiết sản phẩm
  ✅ css/products-page.css    - Danh sách sản phẩm
  ✅ css/cart.css             - Giỏ hàng
  ✅ css/utilities.css        - Utilities

⚙️ JavaScript (4 file):
  ✅ js/script.js             - Script chính
  ✅ js/product-detail.js     - Chi tiết sản phẩm
  ✅ js/products-page.js      - Danh sách sản phẩm
  ✅ js/cart.js               - Giỏ hàng

📚 Documentation:
  ✅ README.md                - README gốc
  ✅ README_FULL.md           - Tài liệu chi tiết
  ✅ QUICK_START.md           - File này

════════════════════════════════════════════════════════════════════════════

🚀 CÁC BƯỚC SETUP BAN ĐẦU
════════════════════════════════════════════════════════════════════════════

1️⃣ CHUẨN BỊ
   └─ Tải tất cả file về máy
   └─ Giữ nguyên cấu trúc thư mục
   └─ Không đổi tên file HTML hoặc folder

2️⃣ CHẠY WEBSITE
   Option A - Mở trực tiếp:
     └─ Double-click vào index.html
   
   Option B - Dùng Live Server (VS Code):
     └─ Cài extension "Live Server"
     └─ Click chuột phải → "Open with Live Server"

   Option C - Dùng Python:
     └─ cd /đường/dẫn/folder
     └─ python -m http.server 8000
     └─ Vào http://localhost:8000

3️⃣ KIỂM TRA
   ✓ Index.html load bình thường
   ✓ CSS có màu đúng (xanh, cam, vàng)
   ✓ Navbar sticky khi scroll
   ✓ Click vào "Sản phẩm" → đến products.html
   ✓ Click vào sản phẩm → đến product-detail.html
   ✓ Thêm vào giỏ → counter tăng
   ✓ Click vào giỏ → đến cart.html

════════════════════════════════════════════════════════════════════════════

🎨 TÙY CHỈNH CƠ BẢN
════════════════════════════════════════════════════════════════════════════

▶ THAY ĐỔI MÀU SẮC
   File: css/variables.css
   
   Dòng 2-18:
   :root {
       --primary: #ff6b35;     ← Thay màu cam
       --secondary: #004e89;   ← Thay màu xanh
       --accent: #f7b801;      ← Thay màu vàng
   }
   
   Lưu → Refresh browser → Màu đổi ngay

▶ THAY ĐỔI LOGO
   File: index.html (dòng 50)
   
   Từ:  <i class="fas fa-dumbbell"></i>
   Thành: <img src="logo.png" alt="Logo">
   
   Tìm icon tại: fontawesome.com

▶ THAY ĐỔI TÊN WEBSITE
   File: Tất cả .html files
   
   <title>AthleteHub</title> → <title>Tên mới</title>
   <h1>AthleteHub</h1> → <h1>Tên mới</h1>

▶ THÊM/SỬA SẢN PHẨM
   File: js/products-page.js
   
   Dòng ~15 - Thêm vào mảng allProducts:
   {
       id: 13,
       name: 'Sản phẩm mới',
       category: 'quan-ao',
       price: 299000,
       originalPrice: 429000,
       rating: 4.8,
       reviews: 120,
       image: 'url-hình'
   }

▶ THÊM MÃ GIẢM GIÁ
   File: js/cart.js
   
   Dòng ~120 - Thêm vào promoCodes:
   const promoCodes = {
       'SAVE10': 50000,
       'MYCODE': 150000  ← Mã mới
   };

════════════════════════════════════════════════════════════════════════════

📊 TÍNH NĂNG CÓ SẵN
════════════════════════════════════════════════════════════════════════════

✅ Trang chủ (index.html)
   • Hero banner với animation
   • 4 danh mục sản phẩm
   • 6 sản phẩm nổi bật
   • Newsletter subscription
   • Footer đầy đủ

✅ Danh sách sản phẩm (products.html)
   • 5 loại filter (tên, danh mục, giá, size, đánh giá)
   • 6 tùy chọn sorting
   • Grid 12 sản phẩm responsive
   • Pagination
   • Xóa tất cả filter

✅ Chi tiết sản phẩm (product-detail.html)
   • Gallery 4 ảnh
   • Chọn size, màu, số lượng
   • 3 nút action (giỏ, mua, yêu thích)
   • 3 tab (mô tả, thông số, đánh giá)
   • Sản phẩm liên quan
   • Share mạng xã hội

✅ Giỏ hàng (cart.html)
   • Danh sách sản phẩm
   • Thay đổi số lượng
   • Xóa sản phẩm
   • 4 mã giảm giá (SAVE10, SAVE20, SHIP, WELCOME)
   • Tính phí vận chuyển tự động
   • Sản phẩm gợi ý

════════════════════════════════════════════════════════════════════════════

🔑 PHÍM TẮT PHÁT TRIỂN
════════════════════════════════════════════════════════════════════════════

VS Code:
   Ctrl+Shift+P   → Command Palette
   Ctrl+`         → Terminal
   F12 / Ctrl+Shift+I → DevTools
   Ctrl+K Ctrl+F → Format code

Browser DevTools:
   F12 / Ctrl+Shift+I → Mở DevTools
   Ctrl+Shift+M       → Toggle Mobile View
   Ctrl+Shift+C       → Select Element
   Ctrl+Shift+P       → Run command

════════════════════════════════════════════════════════════════════════════

❓ TROUBLESHOOTING
════════════════════════════════════════════════════════════════════════════

❌ CSS không load (văn bản trắng)
   ✓ Kiểm tra đường dẫn CSS trong HTML
   ✓ Đảm bảo folder css/ tồn tại
   ✓ Kiểm tra console F12 xem error gì

❌ Icon không hiện
   ✓ Refresh page (Ctrl+R)
   ✓ Clear cache (Ctrl+Shift+Delete)
   ✓ Kiểm tra CDN FontAwesome có load không

❌ Giỏ hàng luôn trống
   ✓ Xóa localStorage: F12 → Application → Local Storage → Delete
   ✓ Refresh page
   ✓ Thêm sản phẩm lại

❌ Responsive không hoạt động
   ✓ Thêm meta viewport vào <head>:
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
   ✓ Dùng Ctrl+Shift+M để test mobile view

❌ JS errors trong console
   ✓ Kiểm tra đường dẫn file JS
   ✓ Tìm dòng lỗi trong DevTools
   ✓ Kiểm tra syntax lỗi

════════════════════════════════════════════════════════════════════════════

💡 MẸOVÀTRICKS
════════════════════════════════════════════════════════════════════════════

1. Dùng DevTools để check responsive
   - F12 → Ctrl+Shift+M
   - Test trên iPhone, iPad, Android

2. Dùng Lighthouse để kiểm tra performance
   - F12 → Lighthouse tab
   - Generate report

3. Dùng placeholder images
   - https://via.placeholder.com/300x200?text=Product

4. Test promo codes
   - SAVE10, SAVE20, SHIP, WELCOME

5. Check cart persistence
   - F12 → Application → Local Storage

════════════════════════════════════════════════════════════════════════════

📞 CÓ VẤN ĐỀ?
════════════════════════════════════════════════════════════════════════════

1. Kiểm tra DevTools (F12)
2. Xem console có error không
3. Kiểm tra Network tab xem có fail request nào
4. Thử xóa cache & refresh (Ctrl+Shift+Delete)
5. Mở file lại trong browser khác

════════════════════════════════════════════════════════════════════════════

📖 TÀI LIỆU THAM KHẢO
════════════════════════════════════════════════════════════════════════════

Bootstrap Docs:     https://getbootstrap.com/docs/5.3/
Font Awesome:       https://fontawesome.com/icons
CSS Grid:           https://css-tricks.com/snippets/css/complete-guide-grid/
Flexbox:            https://css-tricks.com/snippets/css/a-guide-to-flexbox/
JS Tips:            https://developer.mozilla.org/en-US/docs/Web/JavaScript/

════════════════════════════════════════════════════════════════════════════

🎯 NEXT STEPS
════════════════════════════════════════════════════════════════════════════

1. ✅ Setup và chạy website
2. ✅ Tùy chỉnh màu sắc & logo
3. ✅ Thêm sản phẩm của bạn
4. ✅ Thêm mã giảm giá
5. ✅ Test trên mobile
6. ✅ Deploy lên server/hosting

════════════════════════════════════════════════════════════════════════════

Version: 2.0
Last Updated: March 2024
Status: Production Ready ✅

Made with ❤️ by AthleteHub Development Team
