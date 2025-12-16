<?php
class WarrantyModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả bảo hành với filter
    public function getAllWarranties($filters = []) {
        $sql = "SELECT 
                    w_id,
                    period,
                    description
                FROM warranty 
                WHERE 1=1";

        $params = [];

        // Filter theo period
        if (!empty($filters['period'])) {
            $sql .= " AND period = ?";
            $params[] = intval($filters['period']);
        }

        // Filter theo description
        if (!empty($filters['description'])) {
            $sql .= " AND description LIKE ?";
            $params[] = "%" . trim($filters['description']) . "%";
        }

        $sql .= " ORDER BY period ASC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy warranties: " . $e->getMessage());
            return [];
        }
    }

    // Lấy thông tin bảo hành theo ID
    public function getWarrantyById($w_id) {
        $sql = "SELECT * FROM warranty WHERE w_id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$w_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy warranty: " . $e->getMessage());
            return null;
        }
    }

    // Tạo mới bảo hành
    public function createWarranty($data) {
        $sql = "INSERT INTO warranty (period, description) VALUES (?, ?)";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                intval($data['period']), 
                trim($data['description'])
            ]);
            
            if ($result) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Lỗi khi tạo warranty: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật bảo hành
    public function updateWarranty($w_id, $data) {
        $sql = "UPDATE warranty SET period = ?, description = ? WHERE w_id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                intval($data['period']), 
                trim($data['description']), 
                $w_id
            ]);
        } catch (PDOException $e) {
            error_log("Lỗi khi cập nhật warranty: " . $e->getMessage());
            return false;
        }
    }

    // Xóa bảo hành
    public function deleteWarranty($w_id) {
        // Kiểm tra xem có sản phẩm nào đang sử dụng bảo hành này không
        $check_sql = "SELECT COUNT(*) as product_count FROM product WHERE warranty_id = ?";
        $stmt = $this->conn->prepare($check_sql);
        $stmt->execute([$w_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['product_count'] > 0) {
            throw new Exception("Không thể xóa bảo hành vì có sản phẩm đang sử dụng");
        }

        $sql = "DELETE FROM warranty WHERE w_id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$w_id]);
        } catch (PDOException $e) {
            error_log("Lỗi khi xóa warranty: " . $e->getMessage());
            return false;
        }
    }

    // Kiểm tra period đã tồn tại chưa (trừ bản ghi hiện tại khi edit)
    public function periodExists($period, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM warranty WHERE period = ?";
        $params = [intval($period)];
        
        if ($exclude_id) {
            $sql .= " AND w_id != ?";
            $params[] = $exclude_id;
        }
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Lỗi khi kiểm tra period: " . $e->getMessage());
            return false;
        }
    }
}
?>