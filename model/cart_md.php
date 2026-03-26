<?php
class Cart
{
    private $sessionKey = 'athleteHubCart';
    private $disscountKey = 'athleteHubCount';

    private array $vavilCodes = [
        'SAVE10' => 50000,
        'SAVE20' => 100000,
        'SHIP' => 25000,
        'WELCOME' => 75000
    ];

    public function __construct()
    {
        if (session_start() === PHP_SESSION_NONE)
            session_start(); // neu chua co session thi tao
        if (!isset($_SESSION[$this->sessionKey]))
            $_SESSION[$this->sessionKey] = []; // neu chua co gio hang thi tao mang rong
    }

    // _________________CRUD_________________

    public function getItem()
    { // lay danh sach san pham
        return $_SESSION[$this->sessionKey];
    }

    public function addItem(array $product)
    {
        $id = (int) ($product['id'] ?? 0); // lay id
        if ($id <= 0)
            return false;

        foreach ($_SESSION[$this->sessionKey] as $item) { // san pham ton tai tang so luong
            if ($item['id'] === $id) {
                $item['quantity'] += (int)($product['quantity'] ?? 1);
                return;
            }
        }

        unset($item);

        $_SESSION[$this->sessionKey][] = [ // chua ton tai -> them moi
            'id' => $id,
            'ten' => htmlspecialchars(trim($product['ten'] ?? 'Sản phẩm')),
            'price' => max(0, (float) ($product['price'] ?? 0)),
            'hinh_anh_chinh' => $product['hinh_anh_chinh'] ?? 'public/placeholder.jpg',
            'danh_muc' => htmlspecialchars(trim($product['danh_muc'] ?? 'Sản phẩm')),
            'quantity' => max(1, (int) ($product['quantity'] ?? 1)),
        ];
    }

    // cap nhat so luong
    public function updateQuantity(int $productId, int $qty) {
        if ($qty < 0) {
            $this->removeItem($productId);
            return;
        }
        foreach ($_SESSION[$this->sessionKey] as $item) {
            if ($item['id'] === $productId) {
                $item['quantity'] = $qty;
                return;
            }
        }
    }

    public function removeItem( int $productId) {
        $_SESSION[$this->sessionKey] = array_values( // reset lai key cua mang thanh 0,1,2...
            array_filter($_SESSION[$this->sessionKey],fn($i) => $i['id'] !== $productId) // loc bo phan tu co id = idproduct 
        );
    }

    public function clearCart() {
        $_SESSION[$this->sessionKey] = [];
        $this->clearDiscount();
    }

    // logic tinh toan

    public function getTotalQuantity() {
        return array_sum(array_column($this->getItem(), 'quantity'));
    }
}
?>