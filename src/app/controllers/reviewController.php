<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/product.php';

header('Content-Type: application/json');

class ReviewController {
    private $productModel;
    
    public function __construct($db) {
        $this->productModel = new ProductModel($db);
    }
    
    public function checkReviewEligibility() {
        if (!isset($_SESSION['customer'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để đánh giá']);
            return;
        }
        
        $customerId = $_SESSION['customer']['cus_id'];
        $productId = $_GET['product_id'] ?? null;
        
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin sản phẩm']);
            return;
        }
        
        try {
            // DEBUG: Log thông tin kiểm tra
            error_log("Checking review eligibility - Customer: $customerId, Product: $productId");
            
            // Kiểm tra xem đã review chưa
            $userReview = $this->productModel->getUserReviewForProduct($customerId, $productId);
            
            if (!empty($userReview)) {
                echo json_encode([
                    'success' => true,
                    'canReview' => false,
                    'hasReviewed' => true,
                    'userReview' => $userReview,
                    'message' => 'Bạn đã đánh giá sản phẩm này rồi'
                ]);
                return;
            }
            
            // Kiểm tra có thể review không
            $canReview = $this->productModel->canCustomerReviewProduct($customerId, $productId);
            
            // DEBUG: Log kết quả
            error_log("Can Review: " . ($canReview ? 'Yes' : 'No'));
            error_log("Has Reviewed: " . (!empty($userReview) ? 'Yes' : 'No'));
            
            echo json_encode([
                'success' => true,
                'canReview' => $canReview,
                'hasReviewed' => !empty($userReview),
                'userReview' => $userReview,
                'message' => $canReview ? 'Bạn có thể đánh giá sản phẩm này' : 'Bạn cần mua hàng thành công và đơn hàng hoàn thành để đánh giá'
            ]);
            
        } catch (Exception $e) {
            error_log("Review eligibility error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }
    
    public function submitReview() {
        if (!isset($_SESSION['customer'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để đánh giá']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $customerId = $_SESSION['customer']['cus_id'];
        $productId = $input['product_id'] ?? null;
        $rating = $input['rating'] ?? null;
        $comment = $input['comment'] ?? '';
        
        if (!$productId || !$rating) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
            return;
        }
        
        if ($rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Đánh giá phải từ 1-5 sao']);
            return;
        }
        
        try {
            // Kiểm tra đã đánh giá chưa
            $existingReview = $this->productModel->getUserReviewForProduct($customerId, $productId);
            if ($existingReview) {
                echo json_encode(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi']);
                return;
            }
            
            // Kiểm tra quyền đánh giá và lấy order_detail_id
            $orderDetail = $this->productModel->getOrderDetailForReview($customerId, $productId);
            if (!$orderDetail) {
                echo json_encode(['success' => false, 'message' => 'Bạn cần mua hàng thành công và đơn hàng hoàn thành để đánh giá sản phẩm này']);
                return;
            }
            
            // Thêm đánh giá
            $success = $this->productModel->addProductReview([
                ':product_id' => $productId,
                ':customer_id' => $customerId,
                ':order_detail_id' => $orderDetail['id'],
                ':rating' => $rating,
                ':comment' => $comment
            ]);
            
            if ($success) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Đánh giá thành công! Cảm ơn bạn đã chia sẻ cảm nhận.'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi gửi đánh giá. Có thể bạn không đủ điều kiện để đánh giá sản phẩm này.']);
            }
            
        } catch (Exception $e) {
            error_log("Submit review error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống khi gửi đánh giá: ' . $e->getMessage()]);
        }
    }
    
    public function getProductReviews() {
        $productId = $_GET['product_id'] ?? null;
        $limit = $_GET['limit'] ?? null;
        
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin sản phẩm']);
            return;
        }
        
        try {
            $reviews = $this->productModel->getProductReviews($productId, $limit);
            echo json_encode(['success' => true, 'reviews' => $reviews]);
            
        } catch (Exception $e) {
            error_log("Get reviews error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi khi tải đánh giá']);
        }
    }
}

// Xử lý request
$reviewController = new ReviewController($conn);

$action = $_GET['action'] ?? '';
switch ($action) {
    case 'check_eligibility':
        $reviewController->checkReviewEligibility();
        break;
    case 'submit':
        $reviewController->submitReview();
        break;
    case 'get_reviews':
        $reviewController->getProductReviews();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
}
?>
