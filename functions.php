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
?>

