<?php
class User {
    private $conn;
    private $table_name = "customer";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách khách hàng (có tìm kiếm + lọc số đơn hàng)
    public function getAllCustomers($search = '', $orderCount = '') {
        $sql = "SELECT c.*, COUNT(o.order_id) AS order_count
                FROM customer c
                LEFT JOIN orders o ON c.cus_id = o.customer_id
                WHERE 1";

        // Nếu có từ khóa tìm kiếm
        if (!empty($search)) {
            $sql .= " AND (c.fullname LIKE :search 
                        OR c.phone LIKE :search 
                        OR c.address LIKE :search)";
        }

        $sql .= " GROUP BY c.cus_id";

        // Nếu có lọc theo số đơn hàng
        if (!empty($orderCount)) {
            $sql .= " HAVING order_count = :orderCount";
        }

        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        // Gán giá trị tìm kiếm
        if (!empty($search)) {
            $searchParam = "%$search%";
            $stmt->bindParam(':search', $searchParam);
        }

        // Gán giá trị lọc theo số đơn hàng
        if (!empty($orderCount)) {
            $stmt->bindParam(':orderCount', $orderCount, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
