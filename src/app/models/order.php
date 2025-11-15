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
        
        // Lọc theo trạng thái thanh toán
        if (!empty($filters['payment_status'])) {
            $sql .= " AND o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }
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
    
    public function updateOrderStatus($order_id, $status) {
        $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $order_id]);
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
            'success' => 'THÀNH CÔNG', 
            'failed' => 'THẤT BẠI'
        ];
    }
}
?>