<?php
class ProductTagModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTagsByProduct($product_id) {
        $sql = "SELECT * FROM product_tags WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addTag($product_id, $tag_name) {
        $sql = "INSERT INTO product_tags (product_id, tag_name) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$product_id, $tag_name]);
    }

    public function deleteTag($tag_id) {
        $sql = "DELETE FROM product_tags WHERE tag_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$tag_id]);
    }

    public function deleteTagsByProduct($product_id) {
        $sql = "DELETE FROM product_tags WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$product_id]);
    }
}
?>