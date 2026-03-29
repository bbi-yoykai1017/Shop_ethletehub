<?php

// ========== HELPER FUNCTION ==========
/**
 * Convert category ID to category key (slug)
 */
function getCategoryKey($categoryId) {
    $categoryMap = [
        1 => 'quan-ao',
        2 => 'giay',
        3 => 'thiet-bi',
        4 => 'phu-kien'
    ];
    return $categoryMap[$categoryId] ?? 'quan-ao';
}

// ========== PRODUCT FUNCTIONS ==========
function getAllProducts($conn) {
    $sql = "SELECT * FROM san_pham WHERE trang_thai = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return array_map(function($p) {
        return [
            'id' => (int)$p['id'],
            'ten' => $p['ten'],
            'mo_ta' => $p['mo_ta'],
            'gia' => (float)$p['gia'],
            'gia_goc' => (float)$p['gia_goc'],
            'hinh_anh_chinh' => $p['hinh_anh_chinh'],
            'danh_muc_id' => (int)$p['danh_muc_id'],
            'trung_binh_sao' => (float)$p['trung_binh_sao'],
            'so_luong_danh_gia' => (int)$p['so_luong_danh_gia'],
        ];
    }, $products);
}

function getProductsByCategory($conn, $categoryId) {
    $sql = "SELECT * FROM san_pham WHERE danh_muc_id = :cat_id AND trang_thai = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cat_id', $categoryId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    return $result ? (int)$result['so_luong_ton'] : 0;
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

// ========== USER AUTHENTICATION ==========


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
?>