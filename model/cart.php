<?php 
// khoi tao class gio hang
class Cart {
    private $sessionKey = 'athleteHubCart';

    // Constructor - khoi tao session gio hang
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // neu chua co gio hang thi tao moi
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }
    }

    /**
     * Thêm/Cập nhật sản phẩm vào giỏ hàng
     */
    public function addCart($productId, $productName, $price, $image, $quantity = 1, $size = '', $color = '') {
        $cartKey = $this->generateCartKey($productId, $size, $color);

        if (isset($_SESSION[$this->sessionKey][$cartKey])) {
            // Tăng quantity nếu đã tồn tại
            $_SESSION[$this->sessionKey][$cartKey]['quantity'] += $quantity;
        } else {
            // Tạo sản phẩm mới
            $_SESSION[$this->sessionKey][$cartKey] = [
                'product_id' => $productId,
                'name' => $productName,
                'price' => floatval($price),
                'image' => $image,
                'quantity' => (int)$quantity,
                'size' => $size,
                'color' => $color
            ];
        }
        return true;
    }

    /**
     * Tạo key unique cho sản phẩm (productId_size_color)
     */
    private function generateCartKey($id, $size, $color) {
        return 'product_' . $id . '_' . md5($size . '_' . $color);
    }

    /**
     * lay tat ca san pham trong gio hang
     */
    public function getAllCart() {
        return isset($_SESSION[$this->sessionKey]) ? $_SESSION[$this->sessionKey] : [];
    }

    /**
     * Cập nhật số lượng sản phẩm
     */
    public function updateQuantity($cartKey, $quantity) {
        if (isset($_SESSION[$this->sessionKey][$cartKey])) {
            $quantity = (int)$quantity;
            if ($quantity <= 0) {
                $this->removeItem($cartKey);
            } else {
                $_SESSION[$this->sessionKey][$cartKey]['quantity'] = $quantity;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Xóa 1 sản phẩm khỏi giỏ hàng
     */
    public function removeItem($cartKey) {
        if (isset($_SESSION[$this->sessionKey][$cartKey])) {
            unset($_SESSION[$this->sessionKey][$cartKey]);
            return true;
        }
        return false;
    }



}
?>

