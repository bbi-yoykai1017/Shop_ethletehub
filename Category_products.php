<?php
require_once 'functions.php';
require_once 'Database.php';

$db = new Database();
$conn = $db->connect();

// Lấy ID từ URL
$id_danhmuc = isset($_GET['danh_muc_id']) ? (int)$_GET['danh_muc_id'] : 0;

if ($id_danhmuc > 0) {
    // Gọi hàm lấy sản phẩm theo danh mục (hàm mình đã thảo luận ở trên)
    $products = getProductsByCategory($conn, $id_danhmuc);
} else {
    header("Location: index.php"); // Nếu không có ID thì quay về trang chủ
}
?>
