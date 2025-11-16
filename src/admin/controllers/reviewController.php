<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/models/admin_product_review.php';

class ReviewController {
    private $conn;
    private $db;
    private $model;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->db = $conn;
        $this->model = new ProductReviewModel($conn);
    }

    public function index() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        // Lấy danh sách review với filter
        $filters = [];
        if (isset($_GET['product_name']) && !empty($_GET['product_name'])) {
            $filters['product_name'] = trim($_GET['product_name']);
        }
        if (isset($_GET['customer']) && !empty($_GET['customer'])) {
            $filters['customer'] = trim($_GET['customer']);
        }
        if (isset($_GET['rating']) && !empty($_GET['rating'])) {
            $filters['rating'] = intval($_GET['rating']);
        }

        $reviews = $this->model->getAllReviews($filters);
        
        // Hiển thị view
        include __DIR__ . '/../views/reviews.php';
    }

    public function delete() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $review_id = $_GET['review_id'] ?? '';

        if (empty($review_id)) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID review']);
            exit;
        }

        try {
            // Xóa review
            if ($this->model->deleteReview($review_id)) {
                echo json_encode(['success' => true, 'message' => 'Xóa review thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa review']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    private function checkAdminAuth() {
        if (!isset($_SESSION['admin'])) {
            header("Location: login.php");
            exit;
        }
    }
}

// Chỉ khởi tạo và chạy controller khi được gọi từ file chính
// Không tự động chạy trong controller nữa
?>
