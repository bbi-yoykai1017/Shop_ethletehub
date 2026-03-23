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
            'image' => $p['hinh_anh_chinh'],
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

function getProductsByCategory($conn, $categoryId) {
    $sql = "SELECT * FROM san_pham WHERE danh_muc_id = :cat_id AND trang_thai = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cat_id', $categoryId, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            'trung_binh_sao' => $p['trung_binh_sao'],
            'so_luong_danh_gia' => $p['so_luong_danh_gia'],
            'hinh_anh_chinh' => $p['hinh_anh_chinh'],
            'ten' => $p['ten'],
            'mo_ta' => $p['mo_ta'],
            'gia' => $p['gia'],
            'gia_goc' => $p['gia_goc']
        ];
    }, $products);
}

function getAllUsers($conn) {
    $sql = "SELECT id, ten, email, so_dien_thoai, vai_tro FROM nguoi_dung";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserById($conn, $id) {
    $sql = "SELECT id, ten, email, so_dien_thoai, dia_chi, anh_dai_dien, vai_tro, trang_thai, ngay_tao 
            FROM nguoi_dung 
            WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * ========================================
 * HÀM XỬ LÝ NGƯỜI DÙNG (ĐĂNG NHẬP / ĐĂNG KÝ)
 * ========================================
 */

function registerUser($conn, $ten, $email, $mat_khau) {
    try {
        // 1️ SANITIZE INPUT
        $ten = trim($ten);
        $email = strtolower(trim($email));
 
        // 2️ VALIDATE INPUT
        //  Kiểm tra tên
        if (empty($ten)) {
            return ['success' => false, 'message' => 'Vui lòng nhập tên!'];
        }
        if (strlen($ten) < 3) {
            return ['success' => false, 'message' => 'Tên phải >= 3 ký tự'];
        }
 
        //  Kiểm tra email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
 
        //  Kiểm tra mật khẩu
        if (empty($mat_khau)) {
            return ['success' => false, 'message' => 'Vui lòng nhập mật khẩu!'];
        }
        if (strlen($mat_khau) < 6) {
            return ['success' => false, 'message' => 'Mật khẩu phải >= 6 ký tự'];
        }
 
        // 3️ KIỂM TRA EMAIL ĐÃ TỒN TẠI
        $sql_check = "SELECT id FROM nguoi_dung WHERE email = :email LIMIT 1";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt_check->execute();
 
        if ($stmt_check->rowCount() > 0) {
            return ['success' => false, 'message' => 'Email đã tồn tại!'];
        }
 
        // 4️ HASH PASSWORD
        $hashed_password = password_hash($mat_khau, PASSWORD_DEFAULT);
 
        // 5️ INSERT USER
        $sql = "INSERT INTO nguoi_dung (ten, email, mat_khau, vai_tro, trang_thai) 
                VALUES (:ten, :email, :mat_khau, 'khach_hang', 'hoat_dong')";
        
        $stmt = $conn->prepare($sql);
        
        //  Thêm PDO::PARAM_STR
        $stmt->bindParam(':ten', $ten, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':mat_khau', $hashed_password, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            //  Log thành công
            error_log("New user registered: $email");
            return ['success' => true, 'message' => 'Đăng ký thành công!'];
        } else {
            return ['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại.'];
        }
 
    } catch (PDOException $e) {
        //  LỖI DATABASE
        error_log("Register error: " . $e->getMessage() . " - Code: " . $e->getCode());
        
        // Kiểm tra lỗi duplicate
        if ($e->getCode() == 23000) {
            return ['success' => false, 'message' => 'Email đã tồn tại!'];
        }
        
        // Lỗi khác
        return ['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại sau.'];
    }
}
 
/**
 *  Hàm đăng nhập
 */
function loginUser($conn, $email, $mat_khau) {
    try {
        // Kiểm tra input
        if (empty($email) || empty($mat_khau)) {
            return ['success' => false, 'message' => 'Vui lòng nhập email và mật khẩu!'];
        }
 
        // Lấy user từ database
        $sql = "SELECT * FROM nguoi_dung WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
        //  Kiểm tra user tồn tại VÀ password đúng
        if ($user && password_verify($mat_khau, $user['mat_khau'])) {
            
            // Kiểm tra tài khoản bị khóa
            if ($user['trang_thai'] === 'bi_khoa') {
                return ['success' => false, 'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.'];
            }
 
            // Cập nhật lần đăng nhập cuối
            $sql_update = "UPDATE nguoi_dung SET lan_dang_nhap_cuoi = NOW() WHERE id = :id";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $stmt_update->execute();
 
            // Log thành công
            error_log("User logged in: " . $user['email']);
 
            // Xóa mật khẩu trước khi trả về
            unset($user['mat_khau']);
 
            return [
                'success' => true, 
                'message' => 'Đăng nhập thành công',
                'user' => $user
            ];
        }
 
        // Email không tồn tại hoặc password sai
        error_log("Login failed for email: $email");
        return ['success' => false, 'message' => 'Email hoặc mật khẩu không chính xác!'];
        
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Lỗi cơ sở dữ liệu!'];
    }
}
// lay danh sach san pham
function getAllProducts($conn) {
    $sql = "SELECT * FROM san_pham WHERE trang_thai = 1 ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} 
// lay danh sach giam gia
function getAllDiscounts($conn) {
    $sql = "SELECT * FROM ma_giam_gia WHERE trang_thai = 1 ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// lay bien the san pham
function getAllVariants($conn) {
    $sql = "SELECT * FROM bien_the_san_pham WHERE trang_thai = 1 ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// lay danh sach don hang
function getAllOrders($conn) {
    $sql = "SELECT * FROM don_hang WHERE trang_thai = 1 ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>