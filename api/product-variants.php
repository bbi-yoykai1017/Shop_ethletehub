<?php
/**
 * API để lấy danh sách kích thước và màu sắc của sản phẩm
 */

require_once __DIR__ . '/../Database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = new Database();
    $conn = $db->connect();

    if (!$conn) {
        throw new Exception('Không thể kết nối database');
    }

    $productId = (int) ($_GET['product_id'] ?? 0);

    if ($productId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
        exit;
    }

    // Lấy danh sách sizes
    $stmtSizes = $conn->prepare("
        SELECT DISTINCT kt.id, kt.ten
        FROM kich_thuoc kt
        INNER JOIN bien_the_san_pham btsp ON kt.id = btsp.kich_thuoc_id
        WHERE btsp.san_pham_id = :product_id AND btsp.trang_thai = 1
        ORDER BY kt.id ASC
    ");
    $stmtSizes->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $stmtSizes->execute();
    $sizes = $stmtSizes->fetchAll(PDO::FETCH_ASSOC);

    // Lấy danh sách colors
    $stmtColors = $conn->prepare("
        SELECT DISTINCT ms.id, ms.ten, ms.ma_hex
        FROM mau_sac ms
        INNER JOIN bien_the_san_pham btsp ON ms.id = btsp.mau_sac_id
        WHERE btsp.san_pham_id = :product_id AND btsp.trang_thai = 1
        ORDER BY ms.id ASC
    ");
    $stmtColors->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $stmtColors->execute();
    $colors = $stmtColors->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'sizes' => $sizes,
        'colors' => $colors
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
?>
