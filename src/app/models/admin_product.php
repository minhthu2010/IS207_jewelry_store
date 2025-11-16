<?php
class ProductModel_Admin {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProducts($filters = []) {
        try {
            $sql = "SELECT p.*, c.name as category_name, c.has_size as category_has_size,
                        (SELECT image_url FROM product_image WHERE product_id = p.pro_id ORDER BY sort_order LIMIT 1) as main_image,
                        (SELECT COUNT(*) FROM product_variant WHERE product_id = p.pro_id) as variant_count,
                        (SELECT SUM(stock_quantity) FROM product_variant WHERE product_id = p.pro_id) as total_stock,
                        (SELECT MIN(price) FROM product_variant WHERE product_id = p.pro_id) as min_price,
                        (SELECT MAX(price) FROM product_variant WHERE product_id = p.pro_id) as max_price
                    FROM product p 
                    LEFT JOIN category c ON p.category_id = c.cate_id 
                    WHERE 1=1";

            $params = [];

            // Lọc theo tên
            if (!empty($filters['search'])) {
                $sql .= " AND p.name LIKE ?";
                $params[] = "%" . $filters['search'] . "%";
            }

            // Lọc theo danh mục
            if (!empty($filters['category_id'])) {
                $sql .= " AND p.category_id = ?";
                $params[] = $filters['category_id'];
            }

            // Lọc theo size
            if (isset($filters['has_size']) && $filters['has_size'] !== '') {
                if ($filters['has_size'] == '1') {
                    $sql .= " AND c.has_size = 1";
                } else {
                    $sql .= " AND (c.has_size = 0 OR c.has_size IS NULL)";
                }
            }

            // Lọc theo tình trạng stock
            if (!empty($filters['stock_status'])) {
                switch ($filters['stock_status']) {
                    case 'in_stock':
                        $sql .= " AND (SELECT SUM(stock_quantity) FROM product_variant WHERE product_id = p.pro_id) > 10";
                        break;
                    case 'low_stock':
                        $sql .= " AND (SELECT SUM(stock_quantity) FROM product_variant WHERE product_id = p.pro_id) BETWEEN 1 AND 10";
                        break;
                    case 'out_of_stock':
                        $sql .= " AND (SELECT SUM(stock_quantity) FROM product_variant WHERE product_id = p.pro_id) = 0";
                        break;
                }
            }

            // Lọc theo khoảng giá
            if (!empty($filters['min_price'])) {
                $sql .= " AND (SELECT MIN(price) FROM product_variant WHERE product_id = p.pro_id) >= ?";
                $params[] = $filters['min_price'];
            }

            if (!empty($filters['max_price'])) {
                $sql .= " AND (SELECT MAX(price) FROM product_variant WHERE product_id = p.pro_id) <= ?";
                $params[] = $filters['max_price'];
            }

            // Sắp xếp
            switch ($filters['sort_by']) {
                case 'oldest':
                    $sql .= " ORDER BY p.created_at ASC";
                    break;
                case 'name_asc':
                    $sql .= " ORDER BY p.name ASC";
                    break;
                case 'name_desc':
                    $sql .= " ORDER BY p.name DESC";
                    break;
                case 'price_asc':
                    $sql .= " ORDER BY (SELECT MIN(price) FROM product_variant WHERE product_id = p.pro_id) ASC";
                    break;
                case 'price_desc':
                    $sql .= " ORDER BY (SELECT MAX(price) FROM product_variant WHERE product_id = p.pro_id) DESC";
                    break;
                case 'stock_asc':
                    $sql .= " ORDER BY (SELECT SUM(stock_quantity) FROM product_variant WHERE product_id = p.pro_id) ASC";
                    break;
                case 'stock_desc':
                    $sql .= " ORDER BY (SELECT SUM(stock_quantity) FROM product_variant WHERE product_id = p.pro_id) DESC";
                    break;
                default: // newest
                    $sql .= " ORDER BY p.created_at DESC";
                    break;
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database error in getAllProducts: " . $e->getMessage());
        }
    }

    public function getProductById($product_id) {
        $sql = "SELECT p.*, c.has_size as category_has_size
                FROM product p 
                LEFT JOIN category c ON p.category_id = c.cate_id 
                WHERE p.pro_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProduct($data) {
        $sql = "INSERT INTO product (name, description, category_id, warranty_id) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['description'],
            $data['category_id'],
            $data['warranty_id']
        ]);
        return $this->conn->lastInsertId();
    }

    public function updateProduct($product_id, $data) {
        try {
            $sql = "UPDATE product SET name = ?, description = ?, category_id = ?, warranty_id = ?, updated_at = NOW() 
                    WHERE pro_id = ?";
            $stmt = $this->conn->prepare($sql);
            
            $params = [
                $data['name'],
                $data['description'],
                $data['category_id'],
                $data['warranty_id'],
                $product_id
            ];
            
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            throw new Exception("Database error in updateProduct: " . $e->getMessage());
        }
    }

    public function deleteProduct($product_id) {
        $sql = "DELETE FROM product WHERE pro_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$product_id]);
    }

    public function getCategories() {
        try {
            $sql = "SELECT cate_id, name, has_size FROM category ORDER BY name";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database error in getCategories: " . $e->getMessage());
        }
    }

    public function getWarranties() {
        try {
            $sql = "SELECT w_id, period, description FROM warranty ORDER BY w_id";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database error in getWarranties: " . $e->getMessage());
        }
    }

    public function getCategoryById($category_id) {
        try {
            $sql = "SELECT has_size FROM category WHERE cate_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$category_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database error in getCategoryById: " . $e->getMessage());
        }
    }
}
?>