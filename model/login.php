<?php 
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