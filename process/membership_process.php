<?php
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../model/membership_functions.php';

$db = new Database();
$conn = $db->connect();

// Kiểm tra order_id
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Order không hợp lệ");
}

$order_id = (int)$_GET['order_id'];

try {
    // Bắt đầu transaction
    $conn->beginTransaction();

    // 1. Lấy đơn hàng đã hoàn thành
    $stmt = $conn->prepare("
        SELECT * FROM don_hang 
            WHERE id = :order_id AND trang_thai IN ('da_giao', 'hoan_thanh')
    ");
    $stmt->execute([':order_id' => $order_id]);

    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception("Không tìm thấy đơn hàng hợp lệ");
    }

    $user_id = $order['nguoi_dung_id'];
    $total = $order['thanh_tien'];

    // 2. Tính điểm
    $points = calculatePoints($total);

    // 3. Update điểm + chi tiêu
    $stmt = $conn->prepare("
        UPDATE thanh_vien_nguoi_dung
        SET tong_diem = tong_diem + :points,
            tong_chi_tieu = tong_chi_tieu + :total
        WHERE nguoi_dung_id = :user_id
    ");
    $stmt->execute([
        ':points' => $points,
        ':total' => $total,
        ':user_id' => $user_id
    ]);

    // 4. Lưu lịch sử điểm
    $stmt = $conn->prepare("
        INSERT INTO lich_su_diem (nguoi_dung_id, don_hang_id, loai, so_diem, mo_ta)
        VALUES (:user_id, :order_id, 'cong_diem', :points, :mo_ta)
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':order_id' => $order_id,
        ':points' => $points,
        ':mo_ta' => 'Cộng điểm từ đơn hàng'
    ]);

    // 5. Commit
    $conn->commit();

    // 6. Gọi nâng hạng
    require_once 'rank_upgrade.php';

    echo "Cập nhật điểm thành công!";
    
} catch (Exception $e) {
    $conn->rollBack();
    echo "Lỗi: " . $e->getMessage();
}
?>