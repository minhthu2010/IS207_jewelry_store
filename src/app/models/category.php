<?php
class CategoryModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCategories($search = '') {
        $categories = [];
        
        try {
            if (!empty($search)) {
                $searchTerm = "%{$search}%";
                $stmt = $this->conn->prepare("SELECT * FROM category WHERE name LIKE ? ORDER BY created_at DESC");
                $stmt->execute([$searchTerm]);
            } else {
                $stmt = $this->conn->prepare("SELECT * FROM category ORDER BY created_at DESC");
                $stmt->execute();
            }
            
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Lỗi khi lấy danh mục: " . $e->getMessage());
        }
        
        return $categories;
    }

    public function hasProducts($cate_id) {
        $checkStmt = $this->conn->prepare("SELECT COUNT(*) as product_count FROM product WHERE category_id = ?");
        $checkStmt->execute([$cate_id]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        return $result['product_count'] > 0;
    }

    public function deleteCategory($cate_id) {
        $stmt = $this->conn->prepare("DELETE FROM category WHERE cate_id = ?");
        return $stmt->execute([$cate_id]);
    }


    public function createCategory($name, $has_size) {
        $stmt = $this->conn->prepare("INSERT INTO category (name, has_size) VALUES (?, ?)");
        return $stmt->execute([$name, $has_size]);
    }

    public function updateCategory($cate_id, $name, $has_size) {
        $stmt = $this->conn->prepare("UPDATE category SET name = ?, has_size = ? WHERE cate_id = ?");
        return $stmt->execute([$name, $has_size, $cate_id]);
    }
}
?>