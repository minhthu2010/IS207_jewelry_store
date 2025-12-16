<?php
require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../models/cart.php';

class productController {
    private $productModel;
    private $cartModel;

    public function __construct($db) {
        $this->productModel = new ProductModel($db);
        $this->cartModel = new CartModel($db);
    }

    public function list() {
        try {
            // Lấy tham số category nếu có
            $categoryId = isset($_GET['category']) ? $_GET['category'] : null;
            
            if ($categoryId) {
                $products = $this->productModel->getProductsByCategory($categoryId);
            } else {
                $products = $this->productModel->getAllProducts();
            }
            
            // Lấy các filter parameters
            $filters = $this->getFiltersFromRequest();
            
            // Lấy sản phẩm đã lọc
            $products = $this->productModel->getFilteredProducts($filters);
            
            // Lấy filter options cho sidebar
            $filterOptions = $this->productModel->getFilterOptions();
            
            // Lấy danh sách categories cho sidebar
            $categories = $this->productModel->getCategories();
            
            // Lấy số lượng cart items
            $cartItemCount = $this->getCartItemCount();
            
            // Include view
            include __DIR__ . '/../views/product.php';
            
        } catch (Exception $e) {
            error_log("ProductController List Error: " . $e->getMessage());
            // Fallback data
            $products = [];
            $categories = [];
            $filterOptions = [];
            $cartItemCount = 0;
            include __DIR__ . '/../views/product.php';
        }
    }

    private function getFiltersFromRequest() {
        $filters = [];

        // Category filter - chỉ lấy nếu có trong URL
        if (isset($_GET['category']) && $_GET['category'] !== '') {
            $filters['category'] = $_GET['category'];
        }

        // Tags filter (có thể chọn nhiều tags) - chỉ lấy nếu có trong URL
        if (!empty($_GET['tags'])) {
            if (is_array($_GET['tags'])) {
                $filters['tags'] = $_GET['tags'];
            } else {
                $filters['tags'] = [$_GET['tags']];
            }
        }

        // Price filter - CHỈ lấy khi có cả min và max VÀ khi form price được submit
        // Thêm một hidden input để xác định khi nào price filter được áp dụng
        if (!empty($_GET['price_filter_applied']) && $_GET['price_filter_applied'] === '1' 
            && !empty($_GET['min_price']) && !empty($_GET['max_price'])) {
            $filters['min_price'] = floatval($_GET['min_price']);
            $filters['max_price'] = floatval($_GET['max_price']);
            $filters['price_filter_applied'] = true;
        }

        // Sort filter
        if (!empty($_GET['sort'])) {
            $filters['sort'] = $_GET['sort'];
        } else {
            $filters['sort'] = 'newest';
        }

        return $filters;
    }

    public function detail($id) {
        try {
            $product = $this->productModel->getProductById($id);
            $cartItemCount = $this->getCartItemCount();
            
            if ($product) {
                // Lấy variants, images và reviews
                $product['variants'] = $this->productModel->getVariantsByProduct($id);
                $product['images'] = $this->productModel->getProductImages($id);
                $product['reviews'] = $this->productModel->getProductReviews($id);
                
                // Lấy rating summary
                $ratingSummary = $this->productModel->getProductRatingSummary($id);
                $product = array_merge($product, $ratingSummary);
                
                // Kiểm tra xem user đã login có thể review không
                if (isset($_SESSION['customer'])) {
                    $customerId = $_SESSION['customer']['cus_id'];
                    $product['can_review'] = $this->productModel->canCustomerReviewProduct($customerId, $id);
                    $product['user_review'] = $this->productModel->getUserReviewForProduct($customerId, $id);
                } else {
                    $product['can_review'] = false;
                    $product['user_review'] = null;
                }
                
                include __DIR__ . '/../views/product_detail.php';
            } else {
                include __DIR__ . '/../views/404.php';
            }
        } catch (Exception $e) {
            error_log("ProductController Detail Error: " . $e->getMessage());
            include __DIR__ . '/../views/404.php';
        }
    }

    private function calculateAverageRating($reviews) {
        if (empty($reviews)) return 0;
        
        $totalRating = 0;
        foreach ($reviews as $review) {
            $totalRating += $review['rating'];
        }
        
        return round($totalRating / count($reviews), 1);
    }

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
