<?php
session_start();
require_once "Database.php";

$order_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if ($order_id && $user_id) {
    $db = new Database();
    $conn = $db->connect();

    // Kiểm tra lại lần nữa ở phía Server cho chắc chắn
    $sql = "SELECT ngay_dat FROM don_hang WHERE id = :id AND nguoi_dung_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $order_id, 'user_id' => $user_id]);
    $order = $stmt->fetch();

    if ($order) {
        $diff_hours = (time() - strtotime($order['ngay_dat'])) / 3600;
        if ($diff_hours < 24) {
            // Cập nhật trạng thái thành 'da_huy'
            $update = $conn->prepare("UPDATE don_hang SET trang_thai = 'da_huy' WHERE id = :id");
            $update->execute(['id' => $order_id]);
            header("Location: orders.php?id=$order_id&msg=success");
            exit;
        }
    }
}

header("Location: orders.php?id=$order_id&msg=error");
exit;