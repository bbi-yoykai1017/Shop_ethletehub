# TODO - Fix Related Products Section in product-detail.php

## Task Details
Fix the related products section in product-detail.php:
1. Fix quick view button positioning (should appear on hover, not covering image)
2. Replace "Xem chi tiết" button with "Mua ngay" and "Thêm vào giỏ hàng" buttons
3. Add full data fields like in index.php (stock status, category label, description)
4. Remove incorrectly placed JavaScript in the foreach loop
5. Add proper CSS styling

## Steps to Complete:
- [x] 1. Update product-detail.php - Related Products Section with complete data and proper buttons
- [x] 2. Update css/product-detail.css - Add styles for related products section
- [x] 3. Test the changes

## Files to Edit:
- product-detail.php - COMPLETED
- css/product-detail.css - COMPLETED

## Summary of Changes:
### product-detail.php:
- Changed from `.row` to `.products-grid` class for proper grid layout
- Added complete product card structure matching index.php style:
  - Image with discount badge, rating badge, and quick view button
  - Category label
  - Product name (link to detail)
  - Star rating with review count
  - Price with original price and discount percentage
  - Product description
  - Stock status (Còn hàng / Hết hàng)
  - Action buttons: "Thêm" (add to cart) and wishlist button
- Removed incorrectly placed JavaScript code from the foreach loop
- Added proper data fields: stock status, category label, description

### css/product-detail.css:
- Added comprehensive CSS for related products section matching products.css styles
- Added responsive grid (4 columns on desktop, 3 on large tablet, 2 on tablet, 1 on mobile)
- Added proper hover effects for cards and quick view button
- Added all styling for badges, ratings, prices, descriptions, stock status, and action buttons

