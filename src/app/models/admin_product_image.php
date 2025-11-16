<?php
class ProductImageModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getImagesByProduct($product_id) {
        $sql = "SELECT * FROM product_image WHERE product_id = ? ORDER BY sort_order, created_at";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMainImage($product_id) {
        $sql = "SELECT * FROM product_image WHERE product_id = ? AND sort_order = 0 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addImage($product_id, $image_url, $sort_order = 1) {
        // Đảm bảo chỉ có 1 ảnh chính (sort_order = 0)
        // Mặc định ảnh mới thêm vào sẽ là sort_order = 1 (không phải ảnh chính)
        $sql = "INSERT INTO product_image (product_id, image_url, sort_order) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$product_id, $image_url, $sort_order]);
    }

    public function updateImageSortOrder($image_id, $sort_order) {
        $sql = "UPDATE product_image SET sort_order = ? WHERE image_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$sort_order, $image_id]);
    }

    public function setMainImage($product_id, $image_id) {
        try {
            // Bắt đầu transaction để đảm bảo tính nhất quán
            $this->conn->beginTransaction();

            // Reset all images to sort_order > 0
            $sql = "UPDATE product_image SET sort_order = 1 WHERE product_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$product_id]);

            // Set the selected image as main (sort_order = 0)
            $sql = "UPDATE product_image SET sort_order = 0 WHERE image_id = ? AND product_id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$image_id, $product_id]);

            $this->conn->commit();
            return $result;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function deleteImage($image_id) {
        $sql = "DELETE FROM product_image WHERE image_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$image_id]);
    }

    public function deleteImagesByProduct($product_id) {
        $sql = "DELETE FROM product_image WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$product_id]);
    }

    // Thêm vào class ProductImageModel
    public function updateMultipleSortOrders($sort_orders) {
        try {
            $this->conn->beginTransaction();
            
            $sql = "UPDATE product_image SET sort_order = ? WHERE image_id = ?";
            $stmt = $this->conn->prepare($sql);
            
            foreach ($sort_orders as $image_id => $sort_order) {
                $stmt->execute([$sort_order, $image_id]);
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
?>