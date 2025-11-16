<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/models/category.php';

class CategoryController {
    private $conn;
    private $db;
    private $model;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->db = $conn;
        $this->model = new CategoryModel($conn);
    }

    public function index() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        $search = $_GET['search'] ?? '';
        $categories = $this->model->getCategories($search);
        
        // Hiển thị view
        include __DIR__ . '/../views/categories.php'; // Đổi tên file view để tránh conflict
    }

    public function delete() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');

        // Debug: kiểm tra transaction
    try {
        $inTransaction = $this->conn->inTransaction();
        error_log("In transaction: " . ($inTransaction ? 'true' : 'false'));
    } catch (Exception $e) {
        error_log("Transaction check error: " . $e->getMessage());
    }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $cate_id = $_GET['cate_id'] ?? '';

        if (empty($cate_id)) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID danh mục']);
            exit;
        }

        try {
            // Kiểm tra xem danh mục có sản phẩm nào không
            if ($this->model->hasProducts($cate_id)) {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa danh mục vì có sản phẩm thuộc danh mục này']);
                exit;
            }

            // Xóa danh mục
            if ($this->model->deleteCategory($cate_id)) {
                echo json_encode(['success' => true, 'message' => 'Xóa danh mục thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa danh mục']);
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
?>