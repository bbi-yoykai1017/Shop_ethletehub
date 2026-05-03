<?php
require_once 'database.php';

$db = new Database();
$conn = $db->connect();

// Lấy danh sách user có sinh nhật hôm nay
$stmt = $conn->prepare("
    SELECT id
    FROM nguoi_dung
    WHERE DATE_FORMAT(ngay_sinh, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')
");
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Chuẩn bị insert
$insertStmt = $conn->prepare("
    INSERT INTO voucher_thanh_vien (nguoi_dung_id, ma_giam_gia_id, loai)
    VALUES (:user_id, 4, 'birthday')
");

foreach ($users as $user) {
    $user_id = $user['id'];

    // ❗ Check tránh tặng trùng trong năm
    $checkStmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM voucher_thanh_vien
        WHERE nguoi_dung_id = :user_id 
        AND ma_giam_gia_id = 4
        AND YEAR(ngay_tao) = YEAR(CURDATE())
    ");
    $checkStmt->execute([':user_id' => $user_id]);

    if ($checkStmt->fetchColumn() == 0) {
        $insertStmt->execute([':user_id' => $user_id]);
    }
}
?>