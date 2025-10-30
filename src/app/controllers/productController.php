<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../models/cart.php';

class productController {
    private $productModel;
    private $cartModel;

    public function __construct($db) {
        $this->productModel = new Product($db);
        $this->cartModel = new CartModel($db);
    }

    public function detail($id) {
    $product = $this->productModel->getProductById($id);
    $cartItemCount = $this->getCartItemCount();
    
    if ($product) {
        // Lấy variants
        $product['variants'] = $this->productModel->getVariantsByProduct($id);
        
        // DEBUG
        error_log("Product Detail - ID: $id");
        error_log("Product Name: " . ($product['name'] ?? 'N/A'));
        error_log("Variants Count: " . count($product['variants']));
        foreach ($product['variants'] as $v) {
            error_log("Variant: ID=" . $v['variant_id'] . ", Size=" . ($v['size'] ?? 'N/A'));
        }
        
        include __DIR__ . '/../views/product_detail.php';
    } else {
        include __DIR__ . '/../views/404.php';
    }
}

    public function list() {
        $products = $this->productModel->getAllProducts();
        $cartItemCount = $this->getCartItemCount();
        include __DIR__ . '/../views/product.php';
    }

   

    // Lấy số lượng sản phẩm trong giỏ hàng
    private function getCartItemCount() {
        if (isset($_SESSION['customer']['cus_id'])) {
            $cart = $this->cartModel->getCartByCustomerId($_SESSION['customer']['cus_id']);
            if ($cart) {
                return $this->cartModel->getCartItemCount($cart['cart_id']);
            }
        }
        return 0;
    }
}
?>
