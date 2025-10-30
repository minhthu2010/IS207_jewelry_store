<?php
class CartModel {
    private $conn;
    private $cart_table = "cart";
    private $cart_item_table = "cart_item";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCartByCustomerId($customer_id) {
        $query = "SELECT * FROM " . $this->cart_table . " WHERE customer_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$customer_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCart($customer_id) {
        $query = "INSERT INTO " . $this->cart_table . " (customer_id) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$customer_id]) ? $this->conn->lastInsertId() : false;
    }

    public function addToCart($cart_id, $variant_id, $quantity = 1) {
        $existing = $this->getCartItem($cart_id, $variant_id);
        
        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            return $this->updateCartItem($existing['id'], $newQuantity);
        } else {
            $query = "INSERT INTO " . $this->cart_item_table . " (cart_id, variant_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$cart_id, $variant_id, $quantity]);
        }
    }

    private function getCartItem($cart_id, $variant_id) {
        $query = "SELECT * FROM " . $this->cart_item_table . " WHERE cart_id = ? AND variant_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cart_id, $variant_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function updateCartItem($item_id, $quantity) {
        $query = "UPDATE " . $this->cart_item_table . " SET quantity = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$quantity, $item_id]);
    }

    public function getCartItemCount($cart_id) {
        $query = "SELECT SUM(quantity) as total_items FROM " . $this->cart_item_table . " WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cart_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_items'] ? $result['total_items'] : 0;
    }

    public function getCartItems($cart_id) {
        $query = "SELECT 
                    ci.id,
                    ci.cart_id,
                    ci.variant_id,
                    ci.quantity,
                    ci.created_at,
                    pv.size,
                    pv.price,
                    pv.stock_quantity,
                    pv.sku,
                    p.pro_id as product_id,
                    p.name as product_name,
                    p.description,
                    -- Lấy ảnh đầu tiên của sản phẩm
                    (SELECT pi.image_url 
                     FROM product_image pi 
                     WHERE pi.product_id = p.pro_id 
                     ORDER BY pi.sort_order ASC, pi.image_id ASC 
                     LIMIT 1) as product_image
                  FROM " . $this->cart_item_table . " ci 
                  JOIN product_variant pv ON ci.variant_id = pv.variant_id 
                  JOIN product p ON pv.product_id = p.pro_id 
                  WHERE ci.cart_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cart_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Cart items found: " . count($items) . " for cart_id: " . $cart_id);
        
        return $items;
    }

    public function removeCartItem($item_id) {
        $query = "DELETE FROM " . $this->cart_item_table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$item_id]);
    }

    public function updateCartItemQuantity($item_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeCartItem($item_id);
        }
        
        $query = "UPDATE " . $this->cart_item_table . " SET quantity = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$quantity, $item_id]);
    }

    // THÊM: Kiểm tra tồn kho
    public function checkStock($variant_id, $requested_quantity) {
        $query = "SELECT stock_quantity FROM product_variant WHERE variant_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$variant_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['stock_quantity'] >= $requested_quantity) {
            return ['available' => true, 'current_stock' => $result['stock_quantity']];
        } else {
            return [
                'available' => false, 
                'current_stock' => $result ? $result['stock_quantity'] : 0
            ];
        }
    }

    // THÊM: Lấy thông tin tồn kho
    public function getStockInfo($variant_id) {
        $query = "SELECT stock_quantity FROM product_variant WHERE variant_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$variant_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['stock_quantity'] : 0;
    }
}
?>
