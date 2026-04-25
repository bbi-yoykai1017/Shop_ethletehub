<?php
/**
 * Flash Sale Functions
 * Xử lý các chức năng liên quan đến flash sale
 */

/**
 * Lấy flash sale sắp tới (cập nhật trước X ngày)
 */
function getUpcomingFlashSales($conn, $daysBeforeStart = 1) {
    $sql = "
        SELECT 
            id,
            ten_chuong_trinh,
            ngay_bat_dau,
            ngay_ket_thuc,
            ngay_cap_nhat_truoc,
            ghi_chu,
            trang_thai
        FROM flash_sale
        WHERE trang_thai = 1
        AND ngay_bat_dau <= DATE_ADD(NOW(), INTERVAL ? DAY)
        AND ngay_ket_thuc >= NOW()
        ORDER BY ngay_bat_dau ASC
        LIMIT 5
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$daysBeforeStart]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy flash sale hiện tại đang diễn ra
 */
function getCurrentFlashSale($conn) {
    $sql = "
        SELECT 
            id,
            ten_chuong_trinh,
            ngay_bat_dau,
            ngay_ket_thuc,
            ghi_chu
        FROM flash_sale
        WHERE trang_thai = 1
        AND NOW() BETWEEN ngay_bat_dau AND ngay_ket_thuc
        ORDER BY ngay_bat_dau DESC
        LIMIT 1
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Lấy sản phẩm flash sale theo flash_sale_id
 */
function getFlashSaleProducts($conn, $flashSaleId) {
    $sql = "
        SELECT 
            fsp.id,
            fsp.san_pham_id,
            fsp.gia_giam_gia,
            fsp.so_luong_gioi_han,
            fsp.so_luong_da_ban,
            sp.ten,
            sp.mo_ta,
            sp.gia as gia_goc,
            sp.hinh_anh_chinh,
            sp.danh_muc_id,
            sp.trung_binh_sao,
            sp.so_luong_danh_gia,
            ROUND((1 - fsp.gia_giam_gia / sp.gia) * 100) as phan_tram_giam
        FROM flash_sale_products fsp
        JOIN san_pham sp ON fsp.san_pham_id = sp.id
        WHERE fsp.flash_sale_id = ?
        AND fsp.trang_thai = 1
        AND sp.trang_thai = 1
        ORDER BY fsp.so_luong_da_ban DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$flashSaleId]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Map về format chuẩn
    return array_map(function($p) {
        return [
            'id' => $p['san_pham_id'],
            'name' => $p['ten'],
            'description' => $p['mo_ta'],
            'price' => floatval($p['gia_giam_gia']),
            'originalPrice' => floatval($p['gia_goc']),
            'image' => $p['hinh_anh_chinh'] ? 'public/' . $p['hinh_anh_chinh'] : 'public/placeholder.svg',
            'rating' => floatval($p['trung_binh_sao']),
            'so_luong_danh_gia' => $p['so_luong_danh_gia'],
            'discount' => intval($p['phan_tram_giam']),
            'so_luong_gioi_han' => $p['so_luong_gioi_han'],
            'so_luong_da_ban' => $p['so_luong_da_ban'],
            'flash_sale_id' => $p['san_pham_id']
        ];
    }, $products);
}

/**
 * Lấy tất cả flash sale cùng với sản phẩm
 */
function getFlashSalesWithProducts($conn, $daysBeforeStart = 1) {
    $flashSales = getUpcomingFlashSales($conn, $daysBeforeStart);
    
    return array_map(function($sale) use ($conn) {
        $sale['products'] = getFlashSaleProducts($conn, $sale['id']);
        $sale['product_count'] = count($sale['products']);
        
        // Tính toán thời gian countdown
        $startTime = strtotime($sale['ngay_bat_dau']);
        $endTime = strtotime($sale['ngay_ket_thuc']);
        $currentTime = time();
        
        $sale['is_active'] = ($currentTime >= $startTime && $currentTime <= $endTime);
        $sale['time_remaining'] = max(0, $endTime - $currentTime); // seconds
        $sale['start_in'] = max(0, $startTime - $currentTime); // seconds
        
        return $sale;
    }, $flashSales);
}

/**
 * Cập nhật số lượng đã bán flash sale product
 */
function incrementFlashSaleProductSold($conn, $flashSaleProductId, $quantity = 1) {
    $sql = "
        UPDATE flash_sale_products 
        SET so_luong_da_ban = so_luong_da_ban + ?
        WHERE id = ?
    ";
    
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$quantity, $flashSaleProductId]);
}

/**
 * Kiểm tra sản phẩm có trong flash sale hiện tại không
 */
function isProductInCurrentFlashSale($conn, $productId) {
    $sql = "
        SELECT fsp.* 
        FROM flash_sale_products fsp
        JOIN flash_sale fs ON fsp.flash_sale_id = fs.id
        WHERE fsp.san_pham_id = ?
        AND fsp.trang_thai = 1
        AND fs.trang_thai = 1
        AND NOW() BETWEEN fs.ngay_bat_dau AND fs.ngay_ket_thuc
        LIMIT 1
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$productId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
