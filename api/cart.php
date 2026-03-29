<?php
/**
 * API for Shopping Cart
 * Endpoint: /api/cart.php?action=add|remove|update|get|clear
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Database.php';

try {
    $db = new Database();
    $conn = $db->connect();
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    $input = file_get_contents('php://input');
    $data = json_decode($input, true) ?? [];
    
    // Initialize cart session
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    switch ($action) {
        
        // ========== THÊM VÀO GIỎ ==========
        case 'add':
            $productId = (int)($data['product_id'] ?? 0);
            $qty = (int)($data['quantity'] ?? 1);
            $qty = max(1, $qty);
            
            if ($productId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
                exit;
            }
            
            // Lấy thông tin sản phẩm từ DB (ĐẢM BẢO GIÁ ĐÓ ĐÚNG)
            $stmt = $conn->prepare("SELECT id, ten, gia, hinh_anh_chinh, danh_muc_id FROM san_pham WHERE id = :id AND trang_thai = 1");
            $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
                exit;
            }
            
            // Kiểm tra tồn kho
            $stmtStock = $conn->prepare("
                SELECT COALESCE(SUM(so_luong_ton), 0) as total 
                FROM bien_the_san_pham 
                WHERE san_pham_id = :id AND trang_thai = 1
            ");
            $stmtStock->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmtStock->execute();
            $stock = (int)$stmtStock->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($stock <= 0) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm đã hết hàng']);
                exit;
            }
            
            $qty = min($qty, $stock);
            
            // Kiểm tra sản phẩm đã có trong giỏ chưa
            $itemExists = false;
            foreach ($_SESSION['cart'] as $item) {
                if ($item['id'] === $productId) {
                    $itemExists = true;
                    break;
                }
            }
            
            // Nếu có rồi thì tăng qty, không thì thêm mới
            if ($itemExists) {
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] === $productId) {
                        $oldQty = $item['quantity'];
                        $item['quantity'] += $qty;
                        // Không được vượt quá tồn kho
                        $item['quantity'] = min($item['quantity'], $stock);
                        echo json_encode([
                            'success' => true,
                            'message' => $product['ten'] . ' - Số lượng cập nhật: ' . $item['quantity'],
                            'quantity' => $item['quantity'],
                            'cart_count' => countCart()
                        ]);
                        exit;
                    }
                }
            } else {
                // Thêm mới
                $_SESSION['cart'][] = [
                    'id' => $product['id'],
                    'name' => $product['ten'],
                    'price' => (float)$product['gia'],
                    'image' => $product['hinh_anh_chinh'],
                    'quantity' => $qty
                ];
                
                echo json_encode([
                    'success' => true,
                    'message' => '"' . $product['ten'] . '" đã được thêm vào giỏ hàng!',
                    'cart_count' => countCart()
                ]);
                exit;
            }
            break;
            
        // ========== CẬP NHẬT SỐ LƯỢNG ==========
        case 'update':
            $productId = (int)($data['product_id'] ?? 0);
            $qty = (int)($data['quantity'] ?? 1);
            
            if ($productId <= 0 || $qty < 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
                exit;
            }
            
            if ($qty === 0) {
                // Xóa sản phẩm nếu qty = 0
                $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($productId) {
                    return $item['id'] !== $productId;
                });
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Sản phẩm đã được xóa khỏi giỏ',
                    'cart_count' => countCart()
                ]);
                exit;
            }
            
            // Cập nhật số lượng
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] === $productId) {
                    $item['quantity'] = $qty;
                    echo json_encode([
                        'success' => true,
                        'message' => 'Số lượng đã cập nhật',
                        'quantity' => $item['quantity'],
                        'cart_count' => countCart()
                    ]);
                    exit;
                }
            }
            
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tìm thấy trong giỏ']);
            break;
            
        // ========== XÓA KHỎI GIỎ ==========
        case 'remove':
            $productId = (int)($data['product_id'] ?? 0);
            
            if ($productId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
                exit;
            }
            
            $productName = '';
            foreach ($_SESSION['cart'] as $item) {
                if ($item['id'] === $productId) {
                    $productName = $item['name'];
                    break;
                }
            }
            
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($productId) {
                return $item['id'] !== $productId;
            });
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            
            echo json_encode([
                'success' => true,
                'message' => '"' . $productName . '" đã được xóa khỏi giỏ hàng',
                'cart_count' => countCart()
            ]);
            break;
            
        // ========== LẤY THÔNG TIN GIỎ ==========
        case 'get':
            echo json_encode([
                'success' => true,
                'cart' => $_SESSION['cart'],
                'cart_count' => countCart(),
                'total' => getCartTotal()
            ]);
            break;
            
        // ========== XÓA TOÀN BỘ GIỎ ==========
        case 'clear':
            $_SESSION['cart'] = [];
            echo json_encode(['success' => true, 'message' => 'Giỏ hàng đã được xóa trống']);
            break;
            
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

// ============ HELPER FUNCTIONS ============

function countCart() {
    if (!isset($_SESSION['cart'])) return 0;
    // Đếm tổng số lượng sản phẩm, không phải tổng items
    $totalQuantity = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalQuantity += (int)($item['quantity'] ?? 1);
    }
    return $totalQuantity;
}

function getCartTotal() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) return 0;
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
    }
    return $total;
}

// Hàm array_find (PHP 8 không có sẵn, cần thêm vào)
if (!function_exists('array_find')) {
    function array_find(array $array, callable $callback) {
        foreach ($array as $item) {
            if ($callback($item)) {
                return $item;
            }
        }
        return null;
    }
}
