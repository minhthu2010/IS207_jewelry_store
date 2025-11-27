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

    // Hàm kiểm tra xem variant đã tồn tại trong giỏ hàng chưa (dựa trên product_id và size)
    public function checkExistingCartItem($cart_id, $product_id, $size) {
        $query = "SELECT ci.*, pv.size 
                  FROM " . $this->cart_item_table . " ci 
                  JOIN product_variant pv ON ci.variant_id = pv.variant_id 
                  WHERE ci.cart_id = ? AND pv.product_id = ? AND pv.size = ? 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cart_id, $product_id, $size]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Hàm lấy product_id từ variant_id
    public function getProductIdFromVariant($variant_id) {
        $query = "SELECT product_id FROM product_variant WHERE variant_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$variant_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['product_id'] : null;
    }

    //Hàm lấy size từ variant_id
    public function getSizeFromVariant($variant_id) {
        $query = "SELECT size FROM product_variant WHERE variant_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$variant_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['size'] : null;
    }

    public function addToCart($cart_id, $variant_id, $quantity = 1) {
        // Lấy thông tin product_id và size từ variant
        $product_id = $this->getProductIdFromVariant($variant_id);
        $size = $this->getSizeFromVariant($variant_id);
        
        if (!$product_id) {
            return false; // Variant không tồn tại
        }

        // Kiểm tra xem đã có sản phẩm cùng product_id và size trong giỏ hàng chưa
        $existingItem = $this->checkExistingCartItem($cart_id, $product_id, $size);
        
        if ($existingItem) {
            // Nếu đã tồn tại: cập nhật số lượng
            $newQuantity = $existingItem['quantity'] + $quantity;
            return [
                'success' => $this->updateCartItem($existingItem['id'], $newQuantity),
                'isNewItem' => false // Không phải item mới
            ];
        } else {
            // Nếu chưa tồn tại: thêm mới
            $query = "INSERT INTO " . $this->cart_item_table . " (cart_id, variant_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $success = $stmt->execute([$cart_id, $variant_id, $quantity]);
            
            return [
                'success' => $success,
                'isNewItem' => true // Là item mới
            ];
        }
    }

    // THÊM: Hàm đếm số lượng sản phẩm phân biệt (distinct items)
    public function getDistinctCartItemCount($cart_id) {
        $query = "SELECT COUNT(DISTINCT id) as total_items FROM " . $this->cart_item_table . " WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cart_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_items'] ? $result['total_items'] : 0;
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
        $query = "SELECT COUNT(*) as total_items FROM " . $this->cart_item_table . " WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cart_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_items'] ? $result['total_items'] : 0;
    }

    public function getVariantWithProduct($variant_id) {
        $query = "SELECT pv.*, p.name as product_name, p.description,
                        pv.price as base_price, p.category_id
                FROM product_variant pv 
                JOIN product p ON pv.product_id = p.pro_id 
                WHERE pv.variant_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$variant_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
