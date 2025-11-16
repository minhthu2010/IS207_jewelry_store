<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/models/category.php';

class CategoryFormController {
    private $conn;
    private $model;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->model = new CategoryModel($conn);
    }

    public function handleForm() {
        // Kiểm tra đăng nhập admin
        if (!isset($_SESSION['admin'])) {
            header("Location: login.php");
            exit;
        }

        // Xử lý form thêm/sửa danh mục
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cate_id = $_POST['cate_id'] ?? '';
            $name = trim($_POST['name'] ?? '');
            $has_size = isset($_POST['has_size']) ? 1 : 0;
            
            // Validate
            if (empty($name)) {
                $_SESSION['form_message'] = [
                    'type' => 'error',
                    'text' => 'Tên danh mục không được để trống'
                ];
                header("Location: categories.php");
                exit;
            }
            
            try {
                if (empty($cate_id)) {
                    // Thêm mới
                    if ($this->model->createCategory($name, $has_size)) {
                        $_SESSION['form_message'] = [
                            'type' => 'success',
                            'text' => 'Thêm danh mục thành công'
                        ];
                    } else {
                        throw new Exception("Không thể thêm danh mục");
                    }
                } else {
                    // Cập nhật
                    if ($this->model->updateCategory($cate_id, $name, $has_size)) {
                        $_SESSION['form_message'] = [
                            'type' => 'success',
                            'text' => 'Cập nhật danh mục thành công'
                        ];
                    } else {
                        throw new Exception("Không thể cập nhật danh mục");
                    }
                }
            } catch (Exception $e) {
                $_SESSION['form_message'] = [
                    'type' => 'error',
                    'text' => $e->getMessage()
                ];
            }
            
            header("Location: categories.php");
            exit;
        }
    }
}
?>