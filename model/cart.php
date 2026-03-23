<?php 
    // class quan ly gio hang and luu gio hang trong session 
    class Cart {
        private $sessionKey = 'athleteHubCart';

        // contructor - khoi tao session gio hang
        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            // neu chua co gio hang -> tao moi
            if (!isset($_SESSION[$this->sessionKey])) {
                $_SESSION[$this->sessionKey] = [];
            }
        }

        // them san pham vao gio hang
        public function addCart($productId, $productName, $price, $image, $quantity = 1, $size = '', $color = '', ) {
            $carKey = $this->generaCarKey($productId,$size,$color);
        }

        // tao key duy nhat cho san pham 
        public function generaCarKey($id,$size,$color) {
            return 'product_' . $id . '_' .md5($size,$color);
        }
    }
?>