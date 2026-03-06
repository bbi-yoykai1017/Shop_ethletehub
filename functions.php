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

// ham lay chi tiet san pham theo id 
function getProductById($conn,$id) {
     // truy van lay chi tiet san pham
 $sql = "SELECT * FROM san_pham WHERE id = :id";
 $stmt = $conn->prepare($sql);
 $stmt->bindParam(':id', $id, PDO::PARAM_INT);
 $stmt->execute();
 return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ham lay danh sach danh gia cua mot san pham theo id cua san pham do
function getReviewsByProductId($conn, $id, $limit = 5) {
    try {
        $sql = "SELECT dg.*, nd.ten AS ten_nguoi_dung 
                FROM danh_gia dg 
                JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id 
                WHERE dg.san_pham_id = :id 
                ORDER BY dg.ngay_tao DESC LIMIT :limit";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // tra ve danh sach danh gia
    } catch (Exception $e) {
        return []; // neu bang chua ton tai thi tra ve mang rong
    }
}

?>
