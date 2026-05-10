<?php
/**
 * API cho giỏ hàng
 * Endpoint: /api/cart.php?action=add|remove|update|get
 */

// Ngăn chặn output trước JSON
ob_start();

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Database.php';

ob_clean();

try {
    $db = new Database();
    $conn = $db->connect();

    if (!$conn) {
        throw new Exception('Không thể kết nối database');
    }

    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    $input = file_get_contents('php://input');
    $data = json_decode($input, true) ?? [];

    // Khởi tạo session giỏ hàng nếu chưa có
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    function getOrCreateCartInDB($conn, $userId = null) {
        if ($userId) {
            // Nếu user đăng nhập, lấy giỏ của user
            $stmt = $conn->prepare("SELECT id FROM gio_hang WHERE nguoi_dung_id = :user_id LIMIT 1");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cart) {
                return $cart['id'];
            } else {
                // Tạo giỏ mới
                $stmt = $conn->prepare("INSERT INTO gio_hang (nguoi_dung_id) VALUES (:user_id)");
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
                return $conn->lastInsertId();
            }
        } else {
            // Khách vãng lai: dùng session ID
            $sessionId = session_id();
            $stmt = $conn->prepare("SELECT id FROM gio_hang WHERE id_phien_lam_viec = :session_id LIMIT 1");
            $stmt->bindParam(':session_id', $sessionId);
            $stmt->execute();
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cart) {
                return $cart['id'];
            } else {
                $stmt = $conn->prepare("INSERT INTO gio_hang (id_phien_lam_viec) VALUES (:session_id)");
                $stmt->bindParam(':session_id', $sessionId);
                $stmt->execute();
                return $conn->lastInsertId();
            }
        }
    }

    function saveCartItemToDB($conn, $cartId, $productId, $sizeId, $colorId, $qty, $price) {
        // Kiểm tra xem item này đã tồn tại chưa
        $stmt = $conn->prepare("
            SELECT id FROM chi_tiet_gio_hang 
            WHERE gio_hang_id = :cart_id 
              AND san_pham_id = :product_id 
              AND (kich_thuoc_id <=> :size_id) 
              AND (mau_sac_id <=> :color_id)
        ");
        $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':size_id', $sizeId);
        $stmt->bindParam(':color_id', $colorId);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Cập nhật số lượng
            $stmt = $conn->prepare("UPDATE chi_tiet_gio_hang SET so_luong = so_luong + :qty WHERE id = :id");
            $stmt->bindParam(':qty', $qty, PDO::PARAM_INT);
            $stmt->bindParam(':id', $existing['id'], PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Thêm mới
            $stmt = $conn->prepare("
                INSERT INTO chi_tiet_gio_hang (gio_hang_id, san_pham_id, kich_thuoc_id, mau_sac_id, so_luong, gia)
                VALUES (:cart_id, :product_id, :size_id, :color_id, :qty, :gia)
            ");
            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':size_id', $sizeId);
            $stmt->bindParam(':color_id', $colorId);
            $stmt->bindParam(':qty', $qty, PDO::PARAM_INT);
            $stmt->bindParam(':gia', $price);
            $stmt->execute();
        }
    }

    function removeCartItemFromDB($conn, $cartId, $productId, $sizeId, $colorId) {
        $stmt = $conn->prepare("
            DELETE FROM chi_tiet_gio_hang 
            WHERE gio_hang_id = :cart_id 
              AND san_pham_id = :product_id 
              AND (kich_thuoc_id <=> :size_id) 
              AND (mau_sac_id <=> :color_id)
        ");
        $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':size_id', $sizeId);
        $stmt->bindParam(':color_id', $colorId);
        $stmt->execute();
    }

    // Lấy user ID nếu đã đăng nhập
    $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    $gioHangId = null;

    switch ($action) {

        // ========== THÊM VÀO GIỎ ==========
        case 'add':
            $productId = (int) ($data['product_id'] ?? 0);
            $qty = (int) ($data['quantity'] ?? 1);
            $sizeId = $data['size_id'] ?? null;
            $colorId = $data['color_id'] ?? null;
            $sizeName = $data['size_name'] ?? null;
            $colorName = $data['color_name'] ?? null;
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

            // KIỂM TRA SẢN PHẨM CÓ YÊU CẦU SIZE/COLOR KHÔNG
            // Nếu sản phẩm có biến thể (size hoặc color), thì chúng là bắt buộc
            $stmt = $conn->prepare("
                SELECT COUNT(DISTINCT kich_thuoc_id) as size_count, 
                       COUNT(DISTINCT mau_sac_id) as color_count
                FROM bien_the_san_pham 
                WHERE san_pham_id = :id AND trang_thai = 1
            ");
            $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $variantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $hasSize = (int)$variantInfo['size_count'] > 0;
            $hasColor = (int)$variantInfo['color_count'] > 0;
            
            // KIỂM TRA SIZE/COLOR BẮT BUỘC
            if ($hasSize && empty($sizeId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Vui lòng chọn size!']);
                exit;
            }
            
            if ($hasColor && empty($colorId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Vui lòng chọn màu sắc!']);
                exit;
            }

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
            $stock = (int) $stmtStock->fetch(PDO::FETCH_ASSOC)['total'];

            if ($stock <= 0) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm đã hết hàng']);
                exit;
            }

            $qty = min($qty, $stock);

            // Helper: Tạo khóa duy nhất cho item dựa trên product + size + color
            function generateCartKey($productId, $sizeId, $colorId) {
                $size = $sizeId ?? 'NULL';
                $color = $colorId ?? 'NULL';
                return $productId . '_' . $size . '_' . $color;
            }

            $cartKey = generateCartKey($productId, $sizeId, $colorId);

            // Kiểm tra sản phẩm với cùng size/color đã có trong giỏ
            $itemExists = false;
            foreach ($_SESSION['cart'] as $item) {
                $existingKey = generateCartKey($item['id'], $item['size_id'] ?? null, $item['color_id'] ?? null);
                if ($existingKey === $cartKey) {
                    $itemExists = true;
                    break;
                }
            }

            // Nếu có rồi thì tăng qty, không thì thêm mới
            if ($itemExists) {
                foreach ($_SESSION['cart'] as &$item) {
                    $existingKey = generateCartKey($item['id'], $item['size_id'] ?? null, $item['color_id'] ?? null);
                    if ($existingKey === $cartKey) {
                        $oldQty = $item['quantity'];
                        $item['quantity'] += $qty;
                        // Không được vượt quá tồn kho
                        $item['quantity'] = min($item['quantity'], $stock);
                        
                        // LƯU VÀO DATABASE
                        if ($userId) {
                            $gioHangId = getOrCreateCartInDB($conn, $userId);
                            saveCartItemToDB($conn, $gioHangId, $productId, $sizeId, $colorId, $qty, $product['gia']);
                        }
                        
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
                    'ten' => $product['ten'],
                    'price' => (float) $product['gia'],
                    'gia' => (float) $product['gia'],
                    'image' => $product['hinh_anh_chinh'],
                    'hinh_anh_chinh' => $product['hinh_anh_chinh'],
                    'quantity' => $qty,
                    'size_id' => $sizeId,
                    'size' => $sizeName,
                    'color_id' => $colorId,
                    'color' => $colorName
                ];

                // LƯU VÀO DATABASE
                if ($userId) {
                    $gioHangId = getOrCreateCartInDB($conn, $userId);
                    saveCartItemToDB($conn, $gioHangId, $productId, $sizeId, $colorId, $qty, $product['gia']);
                }

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
            $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
            $productId = (int) ($data['product_id'] ?? 0);
            $qty = (int) ($data['quantity'] ?? 1);
            $sizeId = $data['size_id'] ?? null;
            $colorId = $data['color_id'] ?? null;

            if ($productId <= 0 || $qty < 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
                exit;
            }

            // Helper để tạo cartKey
            function generateCartKeyForUpdate($productId, $sizeId, $colorId) {
                $size = $sizeId ?? 'NULL';
                $color = $colorId ?? 'NULL';
                return $productId . '_' . $size . '_' . $color;
            }

            $targetKey = generateCartKeyForUpdate($productId, $sizeId, $colorId);

            if ($qty === 0) {
                // Xóa sản phẩm nếu qty = 0
                $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($item) use ($targetKey) {
                    $existingKey = generateCartKeyForUpdate($item['id'], $item['size_id'] ?? null, $item['color_id'] ?? null);
                    return $existingKey !== $targetKey;
                });
                $_SESSION['cart'] = array_values($_SESSION['cart']);

                // Xóa khỏi database nếu user đăng nhập
                if ($userId) {
                    $gioHangId = getOrCreateCartInDB($conn, $userId);
                    $stmt = $conn->prepare("
                        DELETE FROM chi_tiet_gio_hang 
                        WHERE gio_hang_id = :cart_id 
                          AND san_pham_id = :product_id 
                          AND (kich_thuoc_id <=> :size_id) 
                          AND (mau_sac_id <=> :color_id)
                    ");
                    $stmt->bindParam(':cart_id', $gioHangId, PDO::PARAM_INT);
                    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                    $stmt->bindParam(':size_id', $sizeId);
                    $stmt->bindParam(':color_id', $colorId);
                    $stmt->execute();
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Sản phẩm đã được xóa khỏi giỏ',
                    'cart_count' => countCart()
                ]);
                exit;
            }

            // Cập nhật số lượng
            foreach ($_SESSION['cart'] as &$item) {
                $existingKey = generateCartKeyForUpdate($item['id'], $item['size_id'] ?? null, $item['color_id'] ?? null);
                if ($existingKey === $targetKey) {
                    $item['quantity'] = $qty;
                    
                    // Cập nhật database nếu user đăng nhập
                    if ($userId) {
                        $gioHangId = getOrCreateCartInDB($conn, $userId);
                        $stmt = $conn->prepare("
                            UPDATE chi_tiet_gio_hang 
                            SET so_luong = :qty
                            WHERE gio_hang_id = :cart_id 
                              AND san_pham_id = :product_id 
                              AND (kich_thuoc_id <=> :size_id) 
                              AND (mau_sac_id <=> :color_id)
                        ");
                        $stmt->bindParam(':qty', $qty, PDO::PARAM_INT);
                        $stmt->bindParam(':cart_id', $gioHangId, PDO::PARAM_INT);
                        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                        $stmt->bindParam(':size_id', $sizeId);
                        $stmt->bindParam(':color_id', $colorId);
                        $stmt->execute();
                    }
                    
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
            $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
            $productId = (int) ($data['product_id'] ?? 0);
            $sizeId = $data['size_id'] ?? null;
            $colorId = $data['color_id'] ?? null;

            if ($productId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
                exit;
            }

            // Helper để tạo cartKey
            function generateCartKeyForRemove($productId, $sizeId, $colorId) {
                $size = $sizeId ?? 'NULL';
                $color = $colorId ?? 'NULL';
                return $productId . '_' . $size . '_' . $color;
            }

            $targetKey = generateCartKeyForRemove($productId, $sizeId, $colorId);
            $productName = '';

            foreach ($_SESSION['cart'] as $item) {
                $existingKey = generateCartKeyForRemove($item['id'], $item['size_id'] ?? null, $item['color_id'] ?? null);
                if ($existingKey === $targetKey) {
                    $productName = $item['name'] ?? $item['ten'] ?? 'Sản phẩm';
                    break;
                }
            }

            $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($item) use ($targetKey) {
                $existingKey = generateCartKeyForRemove($item['id'], $item['size_id'] ?? null, $item['color_id'] ?? null);
                return $existingKey !== $targetKey;
            });
            $_SESSION['cart'] = array_values($_SESSION['cart']);

            // Xóa khỏi database nếu user đăng nhập
            if ($userId) {
                $gioHangId = getOrCreateCartInDB($conn, $userId);
                $stmt = $conn->prepare("
                    DELETE FROM chi_tiet_gio_hang 
                    WHERE gio_hang_id = :cart_id 
                      AND san_pham_id = :product_id 
                      AND (kich_thuoc_id <=> :size_id) 
                      AND (mau_sac_id <=> :color_id)
                ");
                $stmt->bindParam(':cart_id', $gioHangId, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $stmt->bindParam(':size_id', $sizeId);
                $stmt->bindParam(':color_id', $colorId);
                $stmt->execute();
            }

            echo json_encode([
                'success' => true,
                'message' => '"' . $productName . '" đã được xóa khỏi giỏ hàng',
                'cart_count' => countCart()
            ]);
            exit;

        // ========== LẤY THÔNG TIN GIỎ ==========
        case 'get':
            $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
            
            // Nếu user đăng nhập, load từ database
            if ($userId) {
                $gioHangId = null;
                
                // Tìm giỏ hàng của user
                $stmt = $conn->prepare("SELECT id FROM gio_hang WHERE nguoi_dung_id = :user_id LIMIT 1");
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
                $cart = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($cart) {
                    $gioHangId = $cart['id'];
                    
                    // Lấy tất cả items trong giỏ từ DB, kèm thông tin sản phẩm
                    $stmt = $conn->prepare("
                        SELECT 
                            ctgh.id as chi_tiet_id,
                            ctgh.san_pham_id as product_id,
                            sp.ten as name,
                            sp.gia as price,
                            sp.hinh_anh_chinh as image,
                            ctgh.kich_thuoc_id as size_id,
                            ks.ten as size,
                            ctgh.mau_sac_id as color_id,
                            ms.ten as color,
                            ctgh.so_luong as quantity
                        FROM chi_tiet_gio_hang ctgh
                        JOIN san_pham sp ON ctgh.san_pham_id = sp.id
                        LEFT JOIN kich_thuoc ks ON ctgh.kich_thuoc_id = ks.id
                        LEFT JOIN mau_sac ms ON ctgh.mau_sac_id = ms.id
                        WHERE ctgh.gio_hang_id = :cart_id
                    ");
                    $stmt->bindParam(':cart_id', $gioHangId, PDO::PARAM_INT);
                    $stmt->execute();
                    $dbItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Chuẩn bị dữ liệu theo định dạng session
                    $cartItems = [];
                    $totalQty = 0;
                    $totalPrice = 0;
                    
                    foreach ($dbItems as $item) {
                        $cartItems[] = [
                            'id' => (int)$item['product_id'],
                            'name' => $item['name'],
                            'ten' => $item['name'],
                            'price' => (float)$item['price'],
                            'gia' => (float)$item['price'],
                            'image' => $item['image'],
                            'hinh_anh_chinh' => $item['image'],
                            'quantity' => (int)$item['quantity'],
                            'size_id' => $item['size_id'],
                            'size' => $item['size'],
                            'color_id' => $item['color_id'],
                            'color' => $item['color']
                        ];
                        
                        $totalQty += (int)$item['quantity'];
                        $totalPrice += (float)$item['price'] * (int)$item['quantity'];
                    }
                    
                    echo json_encode([
                        'success' => true,
                        'cart' => $cartItems,
                        'cart_count' => $totalQty,
                        'total' => $totalPrice
                    ]);
                } else {
                    // User chưa có giỏ hàng
                    echo json_encode([
                        'success' => true,
                        'cart' => [],
                        'cart_count' => 0,
                        'total' => 0
                    ]);
                }
            } else {
                // Khách vãng lai: dùng session
                echo json_encode([
                    'success' => true,
                    'cart' => $_SESSION['cart'] ?? [],
                    'cart_count' => countCart(),
                    'total' => getCartTotal()
                ]);
            }
            break;

        // ========== XÓA TOÀN BỘ GIỎ ==========
        case 'clear':
            $_SESSION['cart'] = [];
            
            if ($userId) {
                $cartId = getOrCreateCartInDB($conn, $userId);
                $stmt = $conn->prepare("DELETE FROM chi_tiet_gio_hang WHERE gio_hang_id = :cart_id");
                $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
                $stmt->execute();
            }
            
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

function countCart()
{
    if (!isset($_SESSION['cart']))
        return 0;
    // Đếm tổng số lượng sản phẩm, không phải tổng items
    $totalQuantity = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalQuantity += (int) ($item['quantity'] ?? 1);
    }
    return $totalQuantity;
}

function getCartTotal()
{
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart']))
        return 0;
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
    }
    return $total;
}

if (!function_exists('array_find')) {
    function array_find(array $array, callable $callback)
    {
        foreach ($array as $item) {
            if ($callback($item)) {
                return $item;
            }
        }
        return null;
    }
}