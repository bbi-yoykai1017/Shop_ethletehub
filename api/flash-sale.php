<?php
/**
 * API Flash Sale
 * GET /api/flash-sale.php
 * 
 * Querystring:
 * - action=get-upcoming (lấy flash sale sắp tới)
 * - action=get-current (lấy flash sale hiện tại)
 * - days_before=1 (cập nhật trước bao nhiêu ngày)
 */

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../model/flash_sale.php';

try {
    $db = new Database();
    $conn = $db->connect();
    
    if (!$conn) {
        throw new Exception('Không thể kết nối database');
    }
    
    $action = $_GET['action'] ?? 'get-upcoming';
    $daysBeforeStart = (int) ($_GET['days_before'] ?? 1);
    
    switch ($action) {
        case 'get-upcoming':
            // Lấy flash sale sắp tới (cập nhật trước X ngày)
            $flashSales = getFlashSalesWithProducts($conn, $daysBeforeStart);
            
            if (empty($flashSales)) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Không có flash sale sắp tới'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => $flashSales
                ]);
            }
            break;
            
        case 'get-current':
            // Lấy flash sale hiện tại đang diễn ra
            $currentSale = getCurrentFlashSale($conn);
            
            if (!$currentSale) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Không có flash sale nào đang diễn ra'
                ]);
            } else {
                $currentSale['products'] = getFlashSaleProducts($conn, $currentSale['id']);
                $currentSale['product_count'] = count($currentSale['products']);
                
                // Tính thời gian còn lại
                $endTime = strtotime($currentSale['ngay_ket_thuc']);
                $currentTime = time();
                $currentSale['time_remaining'] = max(0, $endTime - $currentTime);
                
                echo json_encode([
                    'success' => true,
                    'data' => $currentSale
                ]);
            }
            break;
            
        case 'get-product-details':
            // Lấy chi tiết sản phẩm flash sale
            $flashSaleId = (int) ($_GET['flash_sale_id'] ?? 0);
            
            if (!$flashSaleId) {
                throw new Exception('flash_sale_id không được cung cấp');
            }
            
            $sql = "SELECT * FROM flash_sale WHERE id = ? AND trang_thai = 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$flashSaleId]);
            $sale = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$sale) {
                throw new Exception('Flash sale không tìm thấy');
            }
            
            $sale['products'] = getFlashSaleProducts($conn, $flashSaleId);
            
            echo json_encode([
                'success' => true,
                'data' => $sale
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Action không hợp lệ'
            ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
