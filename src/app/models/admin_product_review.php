<?php
class ProductReviewModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả review với filter
    public function getAllReviews($filters = []) {
        $sql = "SELECT 
                    pr.review_id,
                    pr.rating,
                    pr.comment,
                    pr.created_at,
                    pr.product_id,
                    p.name as product_name,
                    c.cus_id,
                    c.fullname as customer_name,
                    c.email as customer_email
                FROM product_review pr
                INNER JOIN product p ON pr.product_id = p.pro_id
                INNER JOIN customer c ON pr.customer_id = c.cus_id
                WHERE 1=1";

        $params = [];

        // Filter theo sản phẩm
        if (!empty($filters['product_name'])) {
            $sql .= " AND p.name LIKE ?";
            $params[] = "%{$filters['product_name']}%";
        }

        // Filter theo khách hàng
        if (!empty($filters['customer'])) {
            $sql .= " AND (c.fullname LIKE ? OR c.email LIKE ?)";
            $params[] = "%{$filters['customer']}%";
            $params[] = "%{$filters['customer']}%";
        }

        // Filter theo rating
        if (!empty($filters['rating']) && $filters['rating'] > 0) {
            $sql .= " AND pr.rating = ?";
            $params[] = $filters['rating'];
        }

        $sql .= " ORDER BY pr.created_at DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy reviews: " . $e->getMessage());
            return [];
        }
    }

    // Xóa review
    public function deleteReview($review_id) {
        $sql = "DELETE FROM product_review WHERE review_id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$review_id]);
        } catch (PDOException $e) {
            error_log("Lỗi khi xóa review: " . $e->getMessage());
            return false;
        }
    }

    // Lấy thông tin review theo ID
    public function getReviewById($review_id) {
        $sql = "SELECT * FROM product_review WHERE review_id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$review_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy review: " . $e->getMessage());
            return null;
        }
    }
}
?>