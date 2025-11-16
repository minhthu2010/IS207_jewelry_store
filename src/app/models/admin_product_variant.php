<?php
class ProductVariantModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function getVariantsByProduct($product_id) {
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
        $sql = "UPDATE product_variant SET sku = ?, size = ?, price = ?, stock_quantity = ? 
                WHERE variant_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['sku'],
            $data['size'],
            $data['price'],
            $data['stock_quantity'],
            $variant_id
        ]);
    }

    public function deleteVariant($variant_id) {
        $sql = "DELETE FROM product_variant WHERE variant_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$variant_id]);
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