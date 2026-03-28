<?php
/**
 * cart-handler.php
 * Đặt tại: /cart-handler.php (thư mục gốc project)
 *
 * ✅ BẢO MẬT: Giá LUÔN lấy từ DB, không bao giờ tin client gửi lên.
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Cart.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && ($_GET['action'] ?? '') !== 'get') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$db    = new Database();
$conn  = $db->connect();
$cart  = new Cart();
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? ($_GET['action'] ?? '');

switch ($action) {

    // ── Lấy trạng thái giỏ ─────────────────────────────────
    case 'get':
        echo json_encode(['success' => true, 'items' => $cart->getItems(), 'summary' => $cart->getSummary()]);
        break;

    // ── Thêm sản phẩm ──────────────────────────────────────
    case 'add':
        $productId = (int)(($input['product'] ?? [])['id'] ?? 0);
        if ($productId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
            break;
        }

        // ✅ LẤY GIÁ TỪ DATABASE - không dùng giá từ client
        $stmt = $conn->prepare("
            SELECT sp.id, sp.ten, sp.gia, sp.hinh_anh_chinh, dm.ten_danh_muc
            FROM san_pham sp
            LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id
            WHERE sp.id = :id AND sp.trang_thai = 1
        ");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $dbProduct = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dbProduct) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã ngừng bán']);
            break;
        }

        // Kiểm tra tồn kho
        $stmtStock = $conn->prepare("SELECT COALESCE(SUM(so_luong_ton),0) as total FROM bien_the_san_pham WHERE san_pham_id = :id AND trang_thai = 1");
        $stmtStock->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmtStock->execute();
        $stock = (int)$stmtStock->fetch(PDO::FETCH_ASSOC)['total'];

        if ($stock <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm đã hết hàng']);
            break;
        }

        $qty = min(max(1, (int)(($input['product'] ?? [])['quantity'] ?? 1)), $stock);

        $cart->addItem([
            'id'             => $dbProduct['id'],
            'ten'            => $dbProduct['ten'],
            'price'          => (float)$dbProduct['gia'],       // ← GIÁ TỪ DB
            'hinh_anh_chinh' => $dbProduct['hinh_anh_chinh'],
            'danh_muc'       => $dbProduct['ten_danh_muc'] ?? 'Sản phẩm',
            'quantity'       => $qty,
        ]);

        echo json_encode([
            'success'       => true,
            'message'       => '"' . htmlspecialchars($dbProduct['ten']) . '" đã được thêm vào giỏ!',
            'totalQuantity' => $cart->getTotalQuantity(),
            'summary'       => $cart->getSummary(),
        ]);
        break;

    // ── Cập nhật số lượng ──────────────────────────────────
    case 'update':
        $productId = (int)($input['id']       ?? 0);
        $quantity  = (int)($input['quantity'] ?? 0);

        if ($productId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
            break;
        }

        if ($quantity > 0) {
            $stmtStock = $conn->prepare("SELECT COALESCE(SUM(so_luong_ton),0) as total FROM bien_the_san_pham WHERE san_pham_id = :id AND trang_thai = 1");
            $stmtStock->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmtStock->execute();
            $stock    = (int)$stmtStock->fetch(PDO::FETCH_ASSOC)['total'];
            $quantity = min($quantity, $stock);
        }

        $cart->updateQuantity($productId, $quantity);
        echo json_encode(['success' => true, 'summary' => $cart->getSummary()]);
        break;

    // ── Xóa sản phẩm ───────────────────────────────────────
    case 'remove':
        $productId = (int)($input['id'] ?? 0);
        if ($productId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
            break;
        }
        $cart->removeItem($productId);
        echo json_encode(['success' => true, 'message' => 'Đã xóa sản phẩm', 'totalQuantity' => $cart->getTotalQuantity(), 'summary' => $cart->getSummary()]);
        break;

    // ── Xóa toàn bộ ────────────────────────────────────────
    case 'clear':
        $cart->clearCart();
        echo json_encode(['success' => true]);
        break;

    // ── Mã giảm giá ────────────────────────────────────────
    case 'apply_promo':
        $result = $cart->applyPromoCode($input['code'] ?? '');
        echo json_encode(array_merge($result, ['summary' => $cart->getSummary()]));
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
}