<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/models/warranty.php';

class WarrantyController {
    private $conn;
    private $db;
    private $model;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->db = $conn;
        $this->model = new WarrantyModel($conn);
    }

    public function index() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        // Lấy danh sách bảo hành với filter
        $filters = [];
        if (isset($_GET['period']) && !empty($_GET['period'])) {
            $filters['period'] = intval($_GET['period']);
        }
        if (isset($_GET['description']) && !empty($_GET['description'])) {
            $filters['description'] = trim($_GET['description']);
        }

        $warranties = $this->model->getAllWarranties($filters);
        
        // Hiển thị view
        include __DIR__ . '/../views/warranties.php';
    }

    public function create() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        if ($_POST['action'] == 'create_warranty') {
            try {
                $period = intval($_POST['period']);
                $description = trim($_POST['description']);

                // Validate
                if (empty($period) || $period <= 0) {
                    throw new Exception("Thời hạn bảo hành phải là số dương");
                }

                if (empty($description)) {
                    throw new Exception("Mô tả bảo hành không được để trống");
                }

                // Kiểm tra period đã tồn tại chưa
                if ($this->model->periodExists($period)) {
                    throw new Exception("Thời hạn bảo hành đã tồn tại");
                }

                // Tạo bảo hành
                $result = $this->model->createWarranty([
                    'period' => $period,
                    'description' => $description
                ]);
                
                if ($result) {
                    $_SESSION['success'] = 'Thêm bảo hành thành công!';
                } else {
                    throw new Exception("Không thể thêm bảo hành");
                }

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            
            header('Location: warranties.php');
            exit;
        }
    }

    public function update($w_id) {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        if ($_POST['action'] == 'update_warranty') {
            try {
                $period = intval($_POST['period']);
                $description = trim($_POST['description']);

                // Validate
                if (empty($period) || $period <= 0) {
                    throw new Exception("Thời hạn bảo hành phải là số dương");
                }

                if (empty($description)) {
                    throw new Exception("Mô tả bảo hành không được để trống");
                }

                // Kiểm tra period đã tồn tại chưa (trừ bản ghi hiện tại)
                if ($this->model->periodExists($period, $w_id)) {
                    throw new Exception("Thời hạn bảo hành đã tồn tại");
                }

                // Cập nhật bảo hành
                if ($this->model->updateWarranty($w_id, [
                    'period' => $period,
                    'description' => $description
                ])) {
                    $_SESSION['success'] = 'Cập nhật bảo hành thành công!';
                } else {
                    throw new Exception("Không thể cập nhật bảo hành");
                }

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            
            header('Location: warranties.php');
            exit;
        }
    }

    public function delete() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $w_id = $_GET['w_id'] ?? '';

        if (empty($w_id)) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID bảo hành']);
            exit;
        }

        try {
            // Xóa bảo hành
            if ($this->model->deleteWarranty($w_id)) {
                echo json_encode(['success' => true, 'message' => 'Xóa bảo hành thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa bảo hành']);
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