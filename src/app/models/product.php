<?php
// app/models/Product.php

require_once __DIR__ . '/../../config/config.php';

class Product {

    // 🟢 Lấy tất cả sản phẩm (kèm ảnh chính và giá thấp nhất)
    public static function getAllProducts() {
        global $conn;

        $sql = "
            SELECT 
                p.pro_id,
                p.name,
                p.description,
                MIN(v.price) AS price,          -- giá thấp nhất nếu có nhiều variant
                i.image_url
            FROM product p
            LEFT JOIN product_variant v ON p.pro_id = v.product_id
            LEFT JOIN product_image i ON p.pro_id = i.product_id AND i.sort_order = 0
            GROUP BY p.pro_id
        ";

        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🟢 Lấy chi tiết 1 sản phẩm theo ID (chỉ 1 dòng)
    public static function getProductById($id) {
        global $conn;

        $sql = "
            SELECT 
                p.pro_id,
                p.name,
                p.description,
                v.price,
                i.image_url
            FROM product p
            LEFT JOIN product_variant v ON p.pro_id = v.product_id
            LEFT JOIN product_image i ON p.pro_id = i.product_id AND i.sort_order = 0
            WHERE p.pro_id = :id
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // chỉ trả về 1 dòng
    }

    // 🟢 Lấy tất cả ảnh của sản phẩm (nếu muốn hiển thị nhiều ảnh)
    public static function getProductImages($id) {
        global $conn;

        $sql = "
            SELECT image_url 
            FROM product_image 
            WHERE product_id = :id
            ORDER BY sort_order ASC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🟢 Lấy tất cả các biến thể (variant) theo size, giá,...
    public static function getVariantsByProduct($id) {
        global $conn;

        $sql = "
            SELECT variant_id, size, price, stock_quantity
            FROM product_variant
            WHERE product_id = :id
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
