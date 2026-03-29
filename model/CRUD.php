<?php 
require_once __DIR__ . '/functions.php';

// lay danh sach giam gia
function getAllDiscounts($conn) {
    $sql = "SELECT * FROM ma_giam_gia ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// lay bien the san pham
function getAllVariants($conn) {
    $sql = "SELECT * FROM bien_the_san_pham ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// lay danh sach don hang
function getAllOrders($conn) {
    $sql = "SELECT * FROM don_hang  ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllUsers($conn) {
    $sql = "SELECT id, ten, email, so_dien_thoai, vai_tro FROM nguoi_dung";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

