<?php
// app/controllers/productController.php
require_once __DIR__ . '/../models/product.php';

class ProductController {
    public function list() {
        $products = Product::getAllProducts();
        include __DIR__ . '/../views/product.php';
    }

    public function detail($id) {
        $product = Product::getProductById($id);
        include __DIR__ . '/../views/product_detail.php';
    }
}
?>
