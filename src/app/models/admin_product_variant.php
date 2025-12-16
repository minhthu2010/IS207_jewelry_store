<?php
class ProductVariantModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Thêm phương thức toggleStatus
    public function toggleVariantStatus($variant_id, $status) {
        try {
            $sql = "UPDATE product_variant SET status = ? WHERE variant_id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$status, $variant_id]);
        } catch (PDOException $e) {
            throw new Exception("Database error in toggleVariantStatus: " . $e->getMessage());
        }
    }

    // Cập nhật getVariantsByProduct để chỉ lấy variants có status = 1
    public function getVariantsByProduct($product_id) {
        $sql = "SELECT * FROM product_variant WHERE product_id = ? AND status = 1 ORDER BY size";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm phương thức để lấy tất cả variants (kể cả status = 0)
    public function getAllVariantsByProduct($product_id) {
        $sql = "SELECT * FROM product_variant WHERE product_id = ? ORDER BY size";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVariantById($variant_id) {
        $sql = "SELECT * FROM product_variant WHERE variant_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$variant_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createVariant($data) {
        $sql = "INSERT INTO product_variant (product_id, sku, size, price, stock_quantity) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['product_id'],
            $data['sku'],
            $data['size'],
            $data['price'],
            $data['stock_quantity']
        ]);
    }

    public function updateVariant($variant_id, $data) {
        try {
            $sql = "UPDATE product_variant SET sku = ?, size = ?, price = ?, stock_quantity = ?, status = ? 
                    WHERE variant_id = ?";
            $stmt = $this->conn->prepare($sql);
            
            $params = [
                $data['sku'],
                $data['size'] ?? null,
                $data['price'],
                $data['stock_quantity'],
                $data['status'] ?? 1,
                $variant_id
            ];
            
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            throw new Exception("Database error in updateVariant: " . $e->getMessage());
        }
    }

    public function variantExists($sku, $exclude_variant_id = null) {
        $sql = "SELECT COUNT(*) FROM product_variant WHERE sku = ?";
        $params = [$sku];
        
        if ($exclude_variant_id) {
            $sql .= " AND variant_id != ?";
            $params[] = $exclude_variant_id;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
?>
