<?php 
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
// Hàm hiển thị sao đánh giá
function getStarRating($rating) {
    // Đảm bảo $rating là số hợp lệ
    $rating = (float)($rating ?? 0);
    if ($rating < 0) $rating = 0;
    if ($rating > 5) $rating = 5;
    
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
function getProductRatingSummary($conn, $productId) {
    $sql = "SELECT 
                COUNT(*) as total_reviews,
                AVG(so_sao) as average_rating,
                SUM(CASE WHEN so_sao = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN so_sao = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN so_sao = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN so_sao = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN so_sao = 1 THEN 1 ELSE 0 END) as one_star
            FROM danh_gia
            WHERE san_pham_id = :id AND trang_thai = 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}
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
// Hàm định dạng giá tiền
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . '₫';
}
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
?>