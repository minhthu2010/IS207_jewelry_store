<?php

class CheckoutModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Lấy thông tin khách hàng
    public function getCustomerInfo($customer_id) {
        $query = "SELECT cus_id, fullname, phone, email, address FROM customer WHERE cus_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$customer_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin sản phẩm từ cart_item
    public function getCartItems($customer_id, $selected_items = []) {
        $query = "SELECT 
                    ci.id as cart_item_id,
                    ci.variant_id,
                    ci.quantity,
                    pv.price,
                    p.name as product_name,
                    pv.size,
                    pv.sku,
                    pv.stock_quantity,
                    p.pro_id,
                    p.warranty_id,
                    w.period as warranty_period,
                    w.description as warranty_description,
                    (SELECT image_url FROM product_image WHERE product_id = p.pro_id AND sort_order = 0 LIMIT 1) as image_url
                  FROM cart_item ci
                  JOIN cart c ON ci.cart_id = c.cart_id
                  JOIN product_variant pv ON ci.variant_id = pv.variant_id
                  JOIN product p ON pv.product_id = p.pro_id
                  LEFT JOIN warranty w ON p.warranty_id = w.w_id
                  WHERE c.customer_id = ?";
        
        if (!empty($selected_items)) {
            $placeholders = str_repeat('?,', count($selected_items) - 1) . '?';
            $query .= " AND ci.id IN ($placeholders)";
            $stmt = $this->db->prepare($query);
            $params = array_merge([$customer_id], $selected_items);
            $stmt->execute($params);
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->execute([$customer_id]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tạo đơn hàng mới
    public function createOrder($order_data) {
        $query = "INSERT INTO orders (
                    customer_id, total, payment_method, payment_status, 
                    shipping_address, shipping_fullname, shipping_phone, 
                    shipping_fee, notes
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([
            $order_data['customer_id'],
            $order_data['total'],
            $order_data['payment_method'],
            $order_data['payment_status'],
            $order_data['shipping_address'],
            $order_data['shipping_fullname'],
            $order_data['shipping_phone'],
            $order_data['shipping_fee'],
            $order_data['notes']
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Thêm chi tiết đơn hàng với thông tin bảo hành
    public function createOrderDetail($order_detail) {
        $query = "INSERT INTO order_detail (
                    order_id, variant_id, quantity, price_at_purchase,
                    start_warranty_date, end_warranty_date
                  ) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $order_detail['order_id'],
            $order_detail['variant_id'],
            $order_detail['quantity'],
            $order_detail['price_at_purchase'],
            $order_detail['start_warranty_date'],
            $order_detail['end_warranty_date']
        ]);
    }

    // Lấy thông tin bảo hành từ product
    public function getProductWarranty($product_id) {
        $query = "SELECT w.period, w.description 
                  FROM product p 
                  LEFT JOIN warranty w ON p.warranty_id = w.w_id 
                  WHERE p.pro_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tính ngày kết thúc bảo hành
    public function calculateWarrantyEndDate($product_id, $start_date) {
        $warranty_info = $this->getProductWarranty($product_id);
        
        if ($warranty_info && $warranty_info['period'] > 0) {
            return date('Y-m-d', strtotime($start_date . " + {$warranty_info['period']} months"));
        }
        
        // Mặc định 12 tháng nếu không có thông tin bảo hành
        return date('Y-m-d', strtotime($start_date . " + 12 months"));
    }

    // Tạo transaction thanh toán
    public function createPaymentTransaction($transaction_data) {
        $query = "INSERT INTO payment_transaction (
                    order_id, amount, request_id, trans_id, response_data
                  ) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $transaction_data['order_id'],
            $transaction_data['amount'],
            $transaction_data['request_id'],
            $transaction_data['trans_id'],
            $transaction_data['response_data']
        ]);
    }

    // Xóa items đã order khỏi giỏ hàng
    public function removeCartItems($customer_id, $cart_item_ids) {
        if (empty($cart_item_ids)) return true;
        
        $placeholders = str_repeat('?,', count($cart_item_ids) - 1) . '?';
        $query = "DELETE ci FROM cart_item ci
                  JOIN cart c ON ci.cart_id = c.cart_id
                  WHERE c.customer_id = ? AND ci.id IN ($placeholders)";
        
        $stmt = $this->db->prepare($query);
        $params = array_merge([$customer_id], $cart_item_ids);
        return $stmt->execute($params);
    }

    // Tính phí vận chuyển - LUÔN TRẢ VỀ 0 (MIỄN PHÍ)
    public function calculateShippingFee($address, $total_amount) {
        return 0; // Luôn miễn phí vận chuyển
    }

    // Kiểm tra số lượng tồn kho
    public function checkStock($variant_id, $quantity) {
        $query = "SELECT stock_quantity FROM product_variant WHERE variant_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$variant_id]);
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $stock && $stock['stock_quantity'] >= $quantity;
    }

    // Cập nhật số lượng tồn kho
    public function updateStock($variant_id, $quantity) {
        $query = "UPDATE product_variant SET stock_quantity = stock_quantity - ? WHERE variant_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$quantity, $variant_id]);
    }
}
?>
