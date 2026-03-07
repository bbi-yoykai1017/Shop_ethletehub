<?php
function getallproduct($conn)
{
 $sql = "SELECT * FROM san_pham";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Map tên cột database sang tên cột JavaScript đang dùng
    $mappedProducts = array_map(function($p) {
        return [
            'id' => $p['id'],
            'name' => $p['ten'],
            'description' => $p['mo_ta'],
            'price' => $p['gia'],
            'originalPrice' => $p['gia_goc'],
            'rating' => $p['trung_binh_sao'],
            'image' => $p['hinh_anh_chinh'], // Sử dụng trực tiếp tên file từ database
            'category' => getCategoryKey($p['danh_muc_id']),
            'trung_binh_sao' => $p['trung_binh_sao'],
            'so_luong_danh_gia' => $p['so_luong_danh_gia'],
            'hinh_anh_chinh' => $p['hinh_anh_chinh'],
            'ten' => $p['ten'],
            'mo_ta' => $p['mo_ta'],
            'gia' => $p['gia'],
            'gia_goc' => $p['gia_goc']
        ];
    }, $products);
    
    return $mappedProducts;
}

// Chuyển đổi danh_muc_id sang key category
function getCategoryKey($danhMucId) {
    $categoryMap = [
        1 => 'quan-ao',
        2 => 'giay',
        3 => 'thiet-bi',
        4 => 'phu-kien'
    ];
    return isset($categoryMap[$danhMucId]) ? $categoryMap[$danhMucId] : 'quan-ao';
}

// Hàm hiển thị danh mục (label tiếng Việt)
function getCategoryLabel($category) {
    $labels = [
        'quan-ao' => 'Quần áo',
        'giay' => 'Giày',
        'thiet-bi' => 'Thiết bị',
        'phu-kien' => 'Phụ kiện'
    ];
    return $labels[$category] ?? 'Quần áo';
}

// Hàm hiển thị sao đánh giá
function getStarRating($rating) {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= floor($rating)) {
            $html .= '<i class="fas fa-star"></i>';
        } elseif ($i - 0.5 <= $rating) {
            $html .= '<i class="fas fa-star-half"></i>';
        } else {
            $html .= '<i class="far fa-star"></i>';
        }
    }
    return $html;
}

// Hàm định dạng giá tiền
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . '₫';
}

// Hàm xử lý sản phẩm - thêm các trường tính toán
function processProducts($products) {
    return array_map(function($p) {
        $p['categoryLabel'] = getCategoryLabel($p['category']);
        $p['rating'] = floatval($p['rating']);
        $p['originalPrice'] = floatval($p['originalPrice']);
        $p['price'] = floatval($p['price']);
        
        // Tính discount
        $p['discount'] = 0;
        if ($p['originalPrice'] > $p['price'] && $p['originalPrice'] > 0) {
            $p['discount'] = round((($p['originalPrice'] - $p['price']) / $p['originalPrice']) * 100);
        }
        
        return $p;
    }, $products);
}

// ham lay chi tiet san pham theo id
function getProductById($conn,$id) {
     // truy van lay chi tiet san pham
 $sql = "SELECT * FROM san_pham WHERE id = :id";
 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':id', $id, PDO::PARAM_INT);
 $stmt->execute();
 return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ============================================
// HÀM MỚI CHO TRANG CHI TIẾT SẢN PHẨM
// ============================================

/**
 * Lấy chi tiết sản phẩm đầy đủ bao gồm thông tin danh mục
 * @param PDO $conn Kết nối database
 * @param int $id ID sản phẩm
 * @return array|mixed Thông tin sản phẩm hoặc false
 */
function getProductDetail($conn, $id) {
    $sql = "SELECT sp.*, dm.ten_danh_muc, dm.mo_ta AS mo_ta_danh_muc
            FROM san_pham sp 
            LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
            WHERE sp.id = :id AND sp.trang_thai = 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        return false;
    }
    
    // Xử lý các trường tính toán
    $product['gia_formatted'] = formatPrice($product['gia']);
    $product['gia_goc_formatted'] = formatPrice($product['gia_goc']);
    
    // Tính phần trăm giảm giá
    $product['discount_percent'] = 0;
    if ($product['gia_goc'] > $product['gia']) {
        $product['discount_percent'] = round((($product['gia_goc'] - $product['gia']) / $product['gia_goc']) * 100);
    }
    
    // Tính tiết kiệm
    $product['savings'] = $product['gia_goc'] - $product['gia'];
    $product['savings_formatted'] = formatPrice($product['savings']);
    
    // Lấy category key
    $product['category_key'] = getCategoryKey($product['danh_muc_id']);
    
    // Lấy số lượng tồn kho tổng
    $product['total_stock'] = getTotalStock($conn, $id);
    
    // Lấy các thông tin bổ sung
    $product['images'] = getProductImages($conn, $id);
    $product['variants'] = getProductVariants($conn, $id);
    $product['specifications'] = getProductSpecifications($conn, $id);
    $product['rating_summary'] = getProductRatingSummary($conn, $id);
    
    return $product;
}

/**
 * Lấy tổng số lượng tồn kho của sản phẩm
 * @param PDO $conn Kết nối database
 * @param int $productId ID sản phẩm
 * @return int Tổng số lượng tồn kho
 */
function getTotalStock($conn, $productId) {
    $sql = "SELECT COALESCE(SUM(so_luong_ton), 0) as total 
            FROM bien_the_san_pham 
            WHERE san_pham_id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int) $result['total'];
}

/**
 * Lấy danh sách hình ảnh sản phẩm
 * @param PDO $conn Kết nối database
 * @param int $productId ID sản phẩm
 * @return array Danh sách hình ảnh
 */
function getProductImages($conn, $productId) {
    $sql = "SELECT id, duong_dan, thu_tu, la_chinh 
            FROM hinh_anh_san_pham 
            WHERE san_pham_id = :id 
            ORDER BY thu_tu ASC, la_chinh DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy danh sách biến thể sản phẩm (size, màu, tồn kho)
 * @param PDO $conn Kết nối database
 * @param int $productId ID sản phẩm
 * @return array Danh sách biến thể
 */
function getProductVariants($conn, $productId) {
    $sql = "SELECT bt.id, bt.san_pham_id, bt.so_luong_ton, bt.hinh_anh, bt.gia_them,
                   kt.id AS kich_thuoc_id, kt.ten AS kich_thuoc_ten,
                   ms.id AS mau_sac_id, ms.ten AS mau_sac_ten, ms.ma_hex
            FROM bien_the_san_pham bt
            LEFT JOIN kich_thuoc kt ON bt.kich_thuoc_id = kt.id
            LEFT JOIN mau_sac ms ON bt.mau_sac_id = ms.id
            WHERE bt.san_pham_id = :id AND bt.trang_thai = 1
            ORDER BY kt.id ASC, ms.id ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy danh sách size có sẵn của sản phẩm
 * @param PDO $conn Kết nối database
 * @param int $productId ID sản phẩm
 * @return array Danh sách size
 */
function getProductSizes($conn, $productId) {
    $sql = "SELECT DISTINCT kt.id, kt.ten, kt.mo_ta
            FROM bien_the_san_pham bt
            JOIN kich_thuoc kt ON bt.kich_thuoc_id = kt.id
            WHERE bt.san_pham_id = :id 
              AND bt.trang_thai = 1 
              AND bt.so_luong_ton > 0
            ORDER BY kt.id ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy danh sách màu có sẵn của sản phẩm
 * @param PDO $conn Kết nối database
 * @param int $productId ID sản phẩm
 * @return array Danh sách màu
 */
function getProductColors($conn, $productId) {
    $sql = "SELECT DISTINCT ms.id, ms.ten, ms.ma_hex
            FROM bien_the_san_pham bt
            JOIN mau_sac ms ON bt.mau_sac_id = ms.id
            WHERE bt.san_pham_id = :id 
              AND bt.trang_thai = 1 
              AND bt.so_luong_ton > 0
            ORDER BY ms.id ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy thông số kỹ thuật của sản phẩm
 * @param PDO $conn Kết nối database
 * @param int $productId ID sản phẩm
 * @return array Danh sách thông số
 */
function getProductSpecifications($conn, $productId) {
    $sql = "SELECT ts.ten_thong_so, gts.gia_tri
            FROM gia_tri_thong_so gts
            JOIN thong_so ts ON gts.thong_so_id = ts.id
            WHERE gts.san_pham_id = :id
            ORDER BY ts.id ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy tổng hợp đánh giá sản phẩm
 * @param PDO $conn Kết nối database
 * @param int $productId ID sản phẩm
 * @return array Thông tin đánh giá tổng hợp
 */
function getProductRatingSummary($conn, $productId) {
    // Lấy tổng số đánh giá và điểm trung bình từ bảng sản phẩm
    $sql = "SELECT trung_binh_sao, so_luong_danh_gia 
            FROM san_pham 
            WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $summary = [
        'average_rating' => $product ? floatval($product['trung_binh_sao']) : 0,
        'total_reviews' => $product ? (int) $product['so_luong_danh_gia'] : 0,
        'rating_distribution' => [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0
        ]
    ];
    
    // Lấy phân bố đánh giá
    $sql = "SELECT so_sao, COUNT(*) as count 
            FROM danh_gia 
            WHERE san_pham_id = :id AND trang_thai = 1
            GROUP BY so_sao";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    $distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($distribution as $row) {
        $rating = (int) $row['so_sao'];
        $count = (int) $row['count'];
        if (isset($summary['rating_distribution'][$rating])) {
            $summary['rating_distribution'][$rating] = $count;
        }
    }
    
    // Tính phần trăm cho mỗi mức đánh giá
    $total = $summary['total_reviews'];
    if ($total > 0) {
        foreach ($summary['rating_distribution'] as $rating => $count) {
            $summary['rating_distribution'][$rating] = [
                'count' => $count,
                'percentage' => round(($count / $total) * 100)
            ];
        }
    }
    
    return $summary;
}

/**
 * Lấy sản phẩm liên quan (cùng danh mục)
 * @param PDO $conn Kết nối database
 * @param int $categoryId ID danh mục
 * @param int $productId ID sản phẩm hiện tại (để loại trừ)
 * @param int $limit Số lượng sản phẩm lấy
 * @return array Danh sách sản phẩm liên quan
 */
function getRelatedProducts($conn, $categoryId, $productId, $limit = 4) {
    $sql = "SELECT sp.*, dm.ten_danh_muc
            FROM san_pham sp
            LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id
            WHERE sp.danh_muc_id = :category_id 
              AND sp.id != :product_id 
              AND sp.trang_thai = 1
            ORDER BY sp.la_noi_bat DESC, sp.ngay_cap_nhat DESC
            LIMIT :limit";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Xử lý thông tin mỗi sản phẩm
    return array_map(function($p) {
        $p['gia_formatted'] = formatPrice($p['gia']);
        $p['gia_goc_formatted'] = formatPrice($p['gia_goc']);
        $p['discount_percent'] = 0;
        if ($p['gia_goc'] > $p['gia']) {
            $p['discount_percent'] = round((($p['gia_goc'] - $p['gia']) / $p['gia_goc']) * 100);
        }
        $p['category_key'] = getCategoryKey($p['danh_muc_id']);
        $p['star_rating'] = getStarRating($p['trung_binh_sao']);
        return $p;
    }, $products);
}

/**
 * Lấy tồn kho của một biến thể cụ thể (size + màu)
 * @param PDO $conn Kết nối database
 * @param int $productId ID sản phẩm
 * @param int $sizeId ID kích thước
 * @param int $colorId ID màu sắc
 * @return int Số lượng tồn kho
 */
function getVariantStock($conn, $productId, $sizeId, $colorId) {
    $sql = "SELECT so_luong_ton 
            FROM bien_the_san_pham 
            WHERE san_pham_id = :product_id 
              AND kich_thuoc_id = :size_id 
              AND mau_sac_id = :color_id 
              AND trang_thai = 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $stmt->bindParam(':size_id', $sizeId, PDO::PARAM_INT);
    $stmt->bindParam(':color_id', $colorId, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? (int) $result['so_luong_ton'] : 0;
}

// ham lay danh sach danh gia cua mot san pham theo id cua san pham do
function getReviewsByProductId($conn, $id, $limit = 5) {
    try {
        $sql = "SELECT dg.*, nd.ten AS ten_nguoi_dung, nd.anh_dai_dien
                FROM danh_gia dg 
                JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id 
                WHERE dg.san_pham_id = :id AND dg.trang_thai = 1
                ORDER BY dg.ngay_danh_gia DESC LIMIT :limit";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // tra ve danh sach danh gia
    } catch (Exception $e) {
        return []; // neu bang chua ton tai thi tra ve mang rong
    }
}

 //Lấy danh sách sản phẩm theo ID danh mục
 
function getProductsByCategory($conn, $categoryId) {
    $sql = "SELECT * FROM san_pham WHERE danh_muc_id = :cat_id AND trang_thai = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cat_id', $categoryId, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sử dụng lại logic map dữ liệu bạn đã viết ở hàm getallproduct
    return array_map(function($p) {
        return [
            'id' => $p['id'],
            'name' => $p['ten'],
            'description' => $p['mo_ta'],
            'price' => $p['gia'],
            'originalPrice' => $p['gia_goc'],
            'rating' => $p['trung_binh_sao'],
            'image' => $p['hinh_anh_chinh'],
            'category' => getCategoryKey($p['danh_muc_id']),
            // ... thêm các trường khác nếu cần
        ];
    }, $products);
}
?>
