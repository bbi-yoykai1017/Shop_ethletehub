<?php
class Cart
{
    private string $sessionKey  = 'athleteHubCart';
    private string $discountKey = 'athleteHubDiscount';

    private array $validCodes = [
        'SAVE10'  => 50000,
        'SAVE20'  => 100000,
        'SHIP'    => 25000,
        'WELCOME' => 75000,
    ];

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION[$this->sessionKey])) $_SESSION[$this->sessionKey] = [];
    }

    // ── CRUD ──────────────────────────────────────────────

    public function getItems(): array
    {
        return $_SESSION[$this->sessionKey];
    }

    public function addItem(array $product): void
    {
        $id = (int)($product['id'] ?? 0);
        if ($id <= 0) return;

        foreach ($_SESSION[$this->sessionKey] as &$item) {
            if ($item['id'] === $id) {
                $item['quantity'] += (int)($product['quantity'] ?? 1);
                return;
            }
        }
        unset($item);

        $_SESSION[$this->sessionKey][] = [
            'id'             => $id,
            'ten'            => htmlspecialchars(trim($product['ten']    ?? 'Sản phẩm')),
            'price'          => max(0, (float)($product['price']         ?? 0)),
            'hinh_anh_chinh' => $product['hinh_anh_chinh']               ?? 'images/placeholder.jpg',
            'danh_muc'       => htmlspecialchars(trim($product['danh_muc'] ?? 'Sản phẩm')),
            'quantity'       => max(1, (int)($product['quantity']         ?? 1)),
        ];
    }

    public function updateQuantity(int $productId, int $qty): void
    {
        if ($qty <= 0) { $this->removeItem($productId); return; }
        foreach ($_SESSION[$this->sessionKey] as &$item) {
            if ($item['id'] === $productId) { $item['quantity'] = $qty; return; }
        }
    }

    public function removeItem(int $productId): void
    {
        $_SESSION[$this->sessionKey] = array_values(
            array_filter($_SESSION[$this->sessionKey], fn($i) => $i['id'] !== $productId)
        );
    }

    public function clearCart(): void
    {
        $_SESSION[$this->sessionKey] = [];
        $this->clearDiscount();
    }

    // ── TÍNH TOÁN ─────────────────────────────────────────

    public function getTotalQuantity(): int
    {
        return array_sum(array_column($this->getItems(), 'quantity'));
    }

    public function getSubtotal(): float
    {
        return array_reduce($this->getItems(),
            fn($carry, $item) => $carry + $item['price'] * $item['quantity'], 0.0);
    }

    public function getShippingFee(): float
    {
        return $this->getSubtotal() >= 500000 ? 0.0 : 25000.0;
    }

    public function getDiscount(): float
    {
        return (float)($_SESSION[$this->discountKey] ?? 0);
    }

    public function getTotal(): float
    {
        return max(0, $this->getSubtotal() + $this->getShippingFee() - $this->getDiscount());
    }

    public function isEmpty(): bool
    {
        return empty($_SESSION[$this->sessionKey]);
    }

    // ── MÃ GIẢM GIÁ ───────────────────────────────────────

    public function applyPromoCode(string $code): array
    {
        $code = strtoupper(trim($code));
        if (isset($this->validCodes[$code])) {
            $_SESSION[$this->discountKey] = $this->validCodes[$code];
            return ['success' => true, 'message' => "Mã \"{$code}\" đã được áp dụng!", 'amount' => $this->validCodes[$code]];
        }
        return ['success' => false, 'message' => 'Mã giảm giá không hợp lệ', 'amount' => 0];
    }

    public function clearDiscount(): void { unset($_SESSION[$this->discountKey]); }

    // ── TIỆN ÍCH ──────────────────────────────────────────

    public static function formatPrice(float $price): string
    {
        return number_format($price, 0, ',', '.') . '₫';
    }

    public function getSummary(): array
    {
        return [
            'totalQuantity' => $this->getTotalQuantity(),
            'subtotal'      => $this->getSubtotal(),
            'shippingFee'   => $this->getShippingFee(),
            'discount'      => $this->getDiscount(),
            'total'         => $this->getTotal(),
        ];
    }
}