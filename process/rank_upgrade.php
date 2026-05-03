<?php
// Lấy thông tin user
$stmt = $conn->prepare("
    SELECT tong_chi_tieu, hang_id 
    FROM thanh_vien_nguoi_dung
    WHERE nguoi_dung_id = :user_id
");
$stmt->execute([':user_id' => $user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    throw new Exception("Không tìm thấy người dùng");
}

// Tính hạng mới
$newRank = getRankBySpending($user['tong_chi_tieu']);

// Nếu lên hạng
if ($newRank > $user['hang_id']) {

    // Update hạng
    $stmt = $conn->prepare("
        UPDATE thanh_vien_nguoi_dung
        SET hang_id = :hang_id
        WHERE nguoi_dung_id = :user_id
    ");
    $stmt->execute([
        ':hang_id' => $newRank,
        ':user_id' => $user_id
    ]);

    // Thưởng voucher
    require_once 'voucher_reward.php';
}
?>