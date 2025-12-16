<?php
class SearchController {
    private $conn;
    private $searchModel;
    private $cartModel;
    
    public function __construct($db) {
        $this->conn = $db;
        
        // Include và khởi tạo models
        require_once __DIR__ . '/../models/search.php';
        require_once __DIR__ . '/../models/cart.php';
        //require_once __DIR__ . '/../models/CustomerModel.php'; // Nếu cần
        
        $this->searchModel = new SearchModel($db);
        $this->cartModel = new CartModel($db);
    }
    
    /**
     * Hiển thị trang tìm kiếm chính
     */
    public function index() {
        try {
            // Lấy từ khóa tìm kiếm
            $searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
            
            // Lấy tham số sắp xếp
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
            
            // Nếu có từ khóa tìm kiếm
            if (!empty($searchQuery)) {
                // Thực hiện tìm kiếm
                $searchResults = $this->searchModel->searchProducts($searchQuery, $sort);
            } else {
                $searchResults = [];
            }
            
            // Lấy số lượng cart items
            $cartItemCount = $this->getCartItemCount();
            
            // Xác định base URL
            $base_url = "http://" . $_SERVER['HTTP_HOST'] . "/jewelry_website/public/";
            
            // Hiển thị view
            include __DIR__ . '/../views/search.php';
            
        } catch (Exception $e) {
            error_log("SearchController Error: " . $e->getMessage());
            
            // Fallback data
            $searchResults = [];
            $cartItemCount = 0;
            $base_url = "http://" . $_SERVER['HTTP_HOST'] . "/jewelry_website/public/";
            $searchQuery = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
            
            include __DIR__ . '/../views/search.php';
        }
    }
    
    /**
     * Lấy số lượng sản phẩm trong giỏ hàng
     */
    private function getCartItemCount() {
        if (isset($_SESSION['customer']['cus_id'])) {
            try {
                $cart = $this->cartModel->getCartByCustomerId($_SESSION['customer']['cus_id']);
                if ($cart) {
                    return $this->cartModel->getCartItemCount($cart['cart_id']);
                }
            } catch (Exception $e) {
                error_log("Get cart count error: " . $e->getMessage());
            }
        }
        return 0;
    }
}
?>