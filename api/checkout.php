<?php
/**
 * API Checkout - Xử lý thanh toán
 * Endpoint: /api/checkout.php?action=check_coupon|place_order
 */

ob_start();
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '../Database.php';
require_once __DIR__ . '../model/Mailer.php';

$phpmailerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($phpmailerAutoload)) {
    require_once $phpmailerAutoload;
}
ob_clean();

try {
    $db = new Database();
    $conn = $db->connect();

    if (!$conn) {
        throw new Exception('Không thể kết nối database');
    }

    // Kiểm tra user đã đăng nhập
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
        exit;
    }

    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    $input = file_get_contents('php://input');
    $data = json_decode($input, true) ?? [];

    switch ($action) {

        // ========== KIỂM TRA MÃ GIẢM GIÁ ==========
        case 'check_coupon':
            $code = trim($data['coupon_code'] ?? '');
            $totalAmount = floatval($data['total_amount'] ?? 0);
            $now = date('Y-m-d H:i:s');

            if (empty($code)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá']);
                exit;
            }

            $stmt = $conn->prepare("SELECT * FROM ma_giam_gia WHERE ma_code = ? AND trang_thai = 1 LIMIT 1");
            $stmt->execute([$code]);
            $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$coupon) {
                echo json_encode(['success' => false, 'message' => 'Mã giảm giá không tồn tại!']);
                exit;
            }

            if ($now < $coupon['ngay_bat_dau'] || $now > $coupon['ngay_ket_thuc']) {
                echo json_encode(['success' => false, 'message' => 'Mã giảm giá đã hết hạn!']);
                exit;
            }

            if ($totalAmount < $coupon['don_hang_toi_thieu']) {
                echo json_encode(['success' => false, 'message' => 'Đơn hàng tối thiểu ' . number_format($coupon['don_hang_toi_thieu'], 0, '', '.') . 'đ để dùng mã này!']);
                exit;
            }

            $discount = 0;
            if (!empty($coupon['phan_tram_giam'])) {
                $discount = ($totalAmount * $coupon['phan_tram_giam']) / 100;
                if ($discount > $coupon['giam_toi_da']) {
                    $discount = $coupon['giam_toi_da'];
                }
            } else {
                $discount = $coupon['so_tien_giam'];
            }

            echo json_encode([
                'success' => true,
                'message' => 'Áp dụng thành công!',
                'discount' => $discount,
                'coupon_id' => $coupon['id']
            ]);
            exit;

        // ========== ĐẶT HÀNG ==========
        case 'place_order':
            $userId = (int) $_SESSION['user_id'];

            // Lấy thông tin user (cần email để gửi thông báo)
            $stmt = $conn->prepare("SELECT id, ten, email, so_dien_thoai FROM nguoi_dung WHERE id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Validate dữ liệu
            $tenNguoiNhan = trim($data['ten_nguoi_nhan'] ?? '');
            $soDienThoai = trim($data['so_dien_thoai_nhan'] ?? '');
            $diaChiGiao = trim($data['dia_chi_giao_hang'] ?? '');
            $phuongThuc = trim($data['phuong_thuc_thanh_toan'] ?? 'tien_mat');
            $tongTien = floatval($data['tong_tien'] ?? 0);
            $tienGiam = floatval($data['tien_giam_gia'] ?? 0);
            $maGiamGiaId = !empty($data['ma_giam_gia_id']) ? (int) $data['ma_giam_gia_id'] : null;
            $lat = floatval($data['lat'] ?? 0);
            $lng = floatval($data['lng'] ?? 0);

            // Kiểm tra validate
            if (empty($tenNguoiNhan) || strlen($tenNguoiNhan) < 3) {
                echo json_encode(['success' => false, 'message' => 'Tên người nhận không hợp lệ']);
                exit;
            }

            if (empty($soDienThoai) || !preg_match('/^0[0-9]{9}$/', $soDienThoai)) {
                echo json_encode(['success' => false, 'message' => 'Số điện thoại không hợp lệ']);
                exit;
            }

            if (empty($diaChiGiao)) {
                echo json_encode(['success' => false, 'message' => 'Địa chỉ giao hàng không được để trống']);
                exit;
            }

            if ($tongTien <= 0) {
                echo json_encode(['success' => false, 'message' => 'Số tiền không hợp lệ']);
                exit;
            }

            // Kiểm tra giỏ hàng
            $stmt = $conn->prepare("SELECT id FROM gio_hang WHERE nguoi_dung_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $giohang = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$giohang) {
                echo json_encode(['success' => false, 'message' => 'Giỏ hàng không tồn tại']);
                exit;
            }

            $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM chi_tiet_gio_hang WHERE gio_hang_id = ?");
            $stmt->execute([$giohang['id']]);
            $cartCount = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];

            if ($cartCount == 0) {
                echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
                exit;
            }

            try {
                $conn->beginTransaction();

                // Tạo đơn hàng
                $maDonHang = 'ATH-' . strtoupper(uniqid());
                $thanhTien = $tongTien - $tienGiam;

                $stmt = $conn->prepare("
                    INSERT INTO don_hang
                    (nguoi_dung_id, ten_nguoi_nhan, so_dien_thoai_nhan, dia_chi_giao_hang,
                     ma_don_hang, tong_tien, tien_giam, thanh_tien, ma_giam_gia_id,
                     phuong_thuc_thanh_toan, trang_thai, ngay_dat, lat, lng)
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'cho_xac_nhan', NOW(), ?, ?)
                ");

                $stmt->execute([
                    $userId,
                    $tenNguoiNhan,
                    $soDienThoai,
                    $diaChiGiao,
                    $maDonHang,
                    $tongTien,
                    $tienGiam,
                    $thanhTien,
                    $maGiamGiaId,
                    $phuongThuc,
                    $lat,
                    $lng
                ]);

                $donHangId = $conn->lastInsertId();

                // Sao chép chi tiết giỏ hàng sang đơn hàng
                $stmt = $conn->prepare("
                    SELECT
                        ctgh.san_pham_id,
                        ctgh.so_luong,
                        sp.ten,
                        sp.gia,
                        ctgh.kich_thuoc_id,
                        ks.ten  AS kich_thuoc,
                        ctgh.mau_sac_id,
                        ms.ten  AS mau_sac
                    FROM chi_tiet_gio_hang ctgh
                    JOIN san_pham sp ON ctgh.san_pham_id = sp.id
                    LEFT JOIN kich_thuoc ks ON ctgh.kich_thuoc_id = ks.id
                    LEFT JOIN mau_sac    ms ON ctgh.mau_sac_id    = ms.id
                    WHERE ctgh.gio_hang_id = ?
                ");
                $stmt->execute([$giohang['id']]);
                $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($cartItems as $item) {
                    $thanhTienItem = $item['so_luong'] * $item['gia'];

                    $stmt = $conn->prepare("
                        INSERT INTO chi_tiet_don_hang
                        (don_hang_id, san_pham_id, so_luong, gia, thanh_tien, kich_thuoc_id, mau_sac_id)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");

                    $stmt->execute([
                        $donHangId,
                        $item['san_pham_id'],
                        $item['so_luong'],
                        $item['gia'],
                        $thanhTienItem,
                        $item['kich_thuoc_id'],
                        $item['mau_sac_id']
                    ]);
                }

                // Xóa giỏ hàng chi tiết & giỏ hàng
                $stmt = $conn->prepare("DELETE FROM chi_tiet_gio_hang WHERE gio_hang_id = ?");
                $stmt->execute([$giohang['id']]);

                $stmt = $conn->prepare("DELETE FROM gio_hang WHERE id = ?");
                $stmt->execute([$giohang['id']]);

                $conn->commit();

                // ================================================
                // GỬI EMAIL XÁC NHẬN ĐƠN HÀNG
                // ================================================
                $orderForMail = [
                    'ma_don_hang' => $maDonHang,
                    'ngay_dat' => date('Y-m-d H:i:s'),
                    'ten_nguoi_nhan' => $tenNguoiNhan,
                    'so_dien_thoai_nhan' => $soDienThoai,
                    'dia_chi_giao_hang' => $diaChiGiao,
                    'phuong_thuc_thanh_toan' => $phuongThuc,
                    'tong_tien' => $tongTien,
                    'tien_giam' => $tienGiam,
                    'thanh_tien' => $thanhTien,
                ];

                // Chuẩn hóa items để đưa vào template email
                $itemsForMail = array_map(fn($i) => [
                    'ten' => $i['ten'],
                    'so_luong' => $i['so_luong'],
                    'gia' => $i['gia'],
                    'kich_thuoc' => $i['kich_thuoc'] ?? '',
                    'mau_sac' => $i['mau_sac'] ?? '',
                ], $cartItems);

                try {
                    $mailer = new Mailer();
                    $emailSent = $mailer->sendOrderConfirmation($orderForMail, $itemsForMail, $user);
                    if (!$emailSent) {
                        error_log("Email không gửi được cho đơn hàng #$maDonHang - user: " . ($user['email'] ?? 'N/A'));
                    }
                } catch (Exception $mailEx) {
                    // Gửi email thất bại KHÔNG rollback đơn hàng — chỉ log lỗi
                    error_log("Mailer exception: " . $mailEx->getMessage());
                }
                // ================================================

                echo json_encode([
                    'success' => true,
                    'message' => 'Đơn hàng đã được tạo thành công',
                    'order_id' => $donHangId,
                    'order_code' => $maDonHang,
                    'redirect' => 'orders.php?id=' . $donHangId,
                    'email_sent' => $emailSent ?? false,
                ]);

            } catch (Exception $e) {
                $conn->rollBack();
                error_log("Checkout error: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Lỗi tạo đơn hàng: ' . $e->getMessage()
                ]);
            }
            exit;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
?>