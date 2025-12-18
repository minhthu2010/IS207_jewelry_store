<?php
class Order {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAllOrders($filters = []) {
        $sql = "SELECT o.*, c.fullname as customer_name, c.email as customer_email 
                FROM orders o 
                LEFT JOIN customer c ON o.customer_id = c.cus_id 
                WHERE 1=1";
        $params = [];
        
        // Lọc theo ngày bắt đầu
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(o.order_date) >= ?";
            $params[] = $filters['start_date'];
        }
        
        // Lọc theo ngày kết thúc
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(o.order_date) <= ?";
            $params[] = $filters['end_date'];
        }
        
        // Lọc theo trạng thái đơn hàng
        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND o.status = ?";
            $params[] = $filters['status'];
        }
        
        // Lọc theo phương thức thanh toán
        if (!empty($filters['payment_method'])) {
            $sql .= " AND o.payment_method = ?";
            $params[] = $filters['payment_method'];
        }
        
        // Lọc theo tổng tiền (từ)
        if (!empty($filters['min_total'])) {
            $sql .= " AND o.total >= ?";
            $params[] = $filters['min_total'];
        }
        
        // Lọc theo tổng tiền (đến)
        if (!empty($filters['max_total'])) {
            $sql .= " AND o.total <= ?";
            $params[] = $filters['max_total'];
        }
        
        $sql .= " ORDER BY o.order_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy thông tin chi tiết 1 đơn hàng
    public function getOrderById($order_id) {
        $sql = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Cập nhật chỉ trạng thái đơn hàng
    public function updateOrderStatus($order_id, $status) {
        $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $order_id]);
    }
    
    // Cập nhật chỉ trạng thái thanh toán
    public function updatePaymentStatus($order_id, $payment_status) {
        $sql = "UPDATE orders SET payment_status = ?, updated_at = NOW() WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$payment_status, $order_id]);
    }
    
    public function updateOnlyOrderStatus($order_id, $status) {
        $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $order_id]);
    }
    // Cập nhật cả trạng thái đơn hàng và thanh toán
    public function updateOrderAndPaymentStatus($order_id, $order_status, $payment_status) {
        $sql = "UPDATE orders SET status = ?, payment_status = ?, updated_at = NOW() WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$order_status, $payment_status, $order_id]);
    }
    
    public function getYears() {
        $sql = "SELECT DISTINCT YEAR(order_date) as year FROM orders ORDER BY year DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPaymentMethods() {
        $sql = "SELECT DISTINCT payment_method FROM orders WHERE payment_method IS NOT NULL";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getStatusLabels() {
        return [
            0 => 'CHỜ XÁC NHẬN',
            1 => 'ĐÃ XÁC NHẬN',
            2 => 'ĐÃ HỦY'
        ];
    }
    
    public function getPaymentStatusLabels() {
        return [
            'pending' => 'CHỜ THANH TOÁN',
            'success' => 'ĐÃ THANH TOÁN', 
            'failed' => 'THẤT BẠI',
            'refund_required' => 'CẦN HOÀN TIỀN'
        ];
    }

    // Lấy tất cả đơn hàng của khách hàng
    public function getOrdersByCustomer($customerId) {
        $sql = "SELECT 
                    o.order_id,
                    o.order_date,
                    o.total,
                    o.status,
                    o.payment_method,
                    o.payment_status
                FROM orders o
                WHERE o.customer_id = :cid
                ORDER BY o.order_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getOrderDetail($orderId) {
        // Lấy thông tin đơn hàng
        $stmt = $this->db->prepare("
            SELECT 
                o.order_id,
                o.order_date,
                o.total,
                o.status,
                o.payment_method,
                o.payment_status,
                o.shipping_address,
                o.shipping_fullname,
                o.shipping_phone,
                o.shipping_fee,
                o.notes
            FROM orders o
            WHERE o.order_id = :oid
        ");
        $stmt->bindParam(':oid', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        // Lấy chi tiết sản phẩm trong đơn
        $stmt2 = $this->db->prepare("
            SELECT 
                p.name AS product_name,
                pv.size,
                od.quantity,
                od.price_at_purchase,
                (od.quantity * od.price_at_purchase) AS total_item
            FROM order_detail od
            JOIN product_variant pv ON od.variant_id = pv.variant_id
            JOIN product p ON pv.product_id = p.pro_id
            WHERE od.order_id = :oid
        ");
        $stmt2->bindParam(':oid', $orderId, PDO::PARAM_INT);
        $stmt2->execute();
        $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return ['order' => $order, 'items' => $items];
    }

    public function cancelOrderByCustomer($orderId, $customerId) {
    // 1. Kiểm tra đơn hàng tồn tại + thuộc về khách + trạng thái cho phép hủy
        $stmt = $this->db->prepare("
            SELECT status 
            FROM orders 
            WHERE order_id = :order_id 
            AND customer_id = :customer_id
        ");
        $stmt->execute([
            ':order_id' => $orderId,
            ':customer_id' => $customerId
        ]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return [
                'success' => false,
                'message' => 'Đơn hàng không tồn tại'
            ];
        }

        if ((int)$order['status'] !== 0) {
            return [
                'success' => false,
                'message' => 'Đơn hàng không thể hủy'
            ];
        }

        // 2. Cập nhật trạng thái = 2 (ĐÃ HỦY)
        $update = $this->db->prepare("
            UPDATE orders 
            SET status = 2, updated_at = NOW() 
            WHERE order_id = :order_id
        ");

        $update->execute([':order_id' => $orderId]);

        return [
            'success' => true
        ];
    }
}
?>


