<?php

if (!function_exists('calculatePoints')) {
    function calculatePoints($orderTotal) {
        // Tỷ lệ: 100.000đ = 1 điểm
        return floor($orderTotal / 100000);
    }
}

if (!function_exists('getRankBySpending')) {
    function getRankBySpending($totalSpending) {
        // Định nghĩa hạng theo tổng chi tiêu
        if ($totalSpending >= 25000000) return 4; // Kim cương
        if ($totalSpending >= 10000000) return 3; // Vàng
        if ($totalSpending >= 3000000)  return 2; // Bạc
        return 1; // Đồng (Mặc định)
    }
}

if (!function_exists('syncMembershipForCompletedOrder')) {
    function syncMembershipForCompletedOrder($conn, $orderId) {
        // 1. Lấy thông tin đơn hàng (Chỉ lấy đơn đã giao/hoàn thành)
        $stmt = $conn->prepare("SELECT id, nguoi_dung_id, thanh_tien FROM don_hang WHERE id = ? AND trang_thai IN ('da_giao', 'hoan_thanh')");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            return false;
        }

        $userId = $order['nguoi_dung_id'];
        $thanhTien = (float) $order['thanh_tien'];

        // 2. Kiểm tra tránh cộng điểm trùng (Idempotency)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM lich_su_diem WHERE don_hang_id = ? AND loai = 'cong_diem'");
        $stmt->execute([$orderId]);
        if ($stmt->fetchColumn() > 0) {
            return false;
        }

        // 3. Tính điểm
        $points = calculatePoints($thanhTien);

        // 4. Kiểm tra xem người dùng đã có bản ghi trong bảng thành viên chưa
        $stmt = $conn->prepare("SELECT nguoi_dung_id FROM thanh_vien_nguoi_dung WHERE nguoi_dung_id = ?");
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            // Nếu chưa có, tạo mới với hạng mặc định là 1
            $stmt = $conn->prepare("INSERT INTO thanh_vien_nguoi_dung (nguoi_dung_id, hang_id, tong_diem, tong_chi_tieu) VALUES (?, 1, 0, 0)");
            $stmt->execute([$userId]);
        }

        // 5. Lưu lịch sử điểm
        $stmt = $conn->prepare("INSERT INTO lich_su_diem (nguoi_dung_id, don_hang_id, loai, so_diem, mo_ta, ngay_tao) VALUES (?, ?, 'cong_diem', ?, ?, NOW())");
        $stmt->execute([$userId, $orderId, $points, 'Cộng điểm từ đơn hàng #' . $orderId]);

        // 6. Cập nhật Tổng điểm và Tổng chi tiêu
        $stmt = $conn->prepare("UPDATE thanh_vien_nguoi_dung SET tong_diem = tong_diem + ?, tong_chi_tieu = tong_chi_tieu + ? WHERE nguoi_dung_id = ?");
        $stmt->execute([$points, $thanhTien, $userId]);

        // 7. Tính toán lại hạng
        $stmt = $conn->prepare("SELECT tong_chi_tieu FROM thanh_vien_nguoi_dung WHERE nguoi_dung_id = ?");
        $stmt->execute([$userId]);
        $totalSpending = (float) $stmt->fetchColumn();
        
        $newRankId = getRankBySpending($totalSpending);

        // 8. Cập nhật hạng mới
        $stmt = $conn->prepare("UPDATE thanh_vien_nguoi_dung SET hang_id = ? WHERE nguoi_dung_id = ?");
        $stmt->execute([$newRankId, $userId]);

        return true;
    }
}