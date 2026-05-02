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

    /**
 * ========================================
 * HÀM QUÊN MẬT KHẨU
 * ========================================
 */

/**
 * Tạo token ngẫu nhiên
 */
function generateResetToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Tìm user bằng email và tạo token đặt lại mật khẩu
 */
function createPasswordResetToken($conn, $email) {
    try {
        $email = strtolower(trim($email));
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
        
        // Kiểm tra user tồn tại
        $sql = "SELECT id, ten, email FROM nguoi_dung WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            // Không tiết lộ user có tồn tại hay không
            return ['success' => true, 'message' => 'Nếu email tồn tại, chúng tôi đã gửi link đặt lại mật khẩu'];
        }
        
        // Tạo token
        $token = generateResetToken();
        $expire = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token hết hạn sau 1 giờ
        
        // Lưu token vào database
        $sql_update = "UPDATE nguoi_dung 
                      SET reset_token = :token, reset_token_expire = :expire 
                      WHERE id = :id";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt_update->bindParam(':expire', $expire, PDO::PARAM_STR);
        $stmt_update->bindParam(':id', $user['id'], PDO::PARAM_INT);
        $stmt_update->execute();
        
        error_log("Password reset token created for: $email");
        
        return [
            'success' => true, 
            'message' => 'Nếu email tồn tại, chúng tôi đã gửi link đặt lại mật khẩu',
            'token' => $token,
            'email' => $email,
            'user' => $user
        ];
        
    } catch (PDOException $e) {
        error_log("Password reset token error: " . $e->getMessage());
        return ['success' => true, 'message' => 'Nếu email tồn tại, chúng tôi đã gửi link đặt lại mật khẩu'];
    }
}

/**
 * Xác minh token đặt lại mật khẩu
 */
function verifyPasswordResetToken($conn, $token) {
    try {
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token không hợp lệ'];
        }
        
        $sql = "SELECT id, ten, email FROM nguoi_dung 
                WHERE reset_token = :token AND reset_token_expire > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Token đã hết hạn hoặc không hợp lệ'];
        }
        
        return ['success' => true, 'user' => $user];
        
    } catch (PDOException $e) {
        error_log("Verify token error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Lỗi xác minh token'];
    }
}

/**
 * Cập nhật mật khẩu mới
 */
function updatePassword($conn, $userId, $newPassword) {
    try {
        if (empty($newPassword) || strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự'];
        }
        
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Cập nhật mật khẩu và xóa token
        $sql = "UPDATE nguoi_dung 
                SET mat_khau = :mat_khau, reset_token = NULL, reset_token_expire = NULL 
                WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':mat_khau', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        error_log("Password updated for user ID: $userId");
        
        return ['success' => true, 'message' => 'Đặt lại mật khẩu thành công'];
        
    } catch (PDOException $e) {
        error_log("Update password error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Lỗi cập nhật mật khẩu'];
    }
}
}
?>