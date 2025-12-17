<?php
// Thêm session_start() nếu chưa có
session_start();

// Kiểm tra xem file config có tồn tại không
$configPath = __DIR__ . '/../../config/config.php';
if (!file_exists($configPath)) {
    die(json_encode(['success' => false, 'message' => 'File config không tồn tại']));
}
require_once $configPath;

$modelPath = __DIR__ . '/../../app/models/order.php';
if (!file_exists($modelPath)) {
    die(json_encode(['success' => false, 'message' => 'File model không tồn tại']));
}
require_once $modelPath;

class OrderController {
    private $orderModel;
    private $db;
    
    public function __construct() {
        global $conn;
        $this->db = $conn;
        $this->orderModel = new Order($this->db);
    }
    
    public function index() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['admin'])) {
            header("Location: login.php");
            exit();
        }
        
        // Lấy các tham số lọc
        $filters = [
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'status' => $_GET['status'] ?? '',
            'payment_method' => $_GET['payment_method'] ?? '',
            'min_total' => $_GET['min_total'] ?? '',
            'max_total' => $_GET['max_total'] ?? ''
        ];
        
        // Lấy dữ liệu
        $orders = $this->orderModel->getAllOrders($filters);
        $paymentMethods = $this->orderModel->getPaymentMethods();
        $statusLabels = $this->orderModel->getStatusLabels();
        
        // Hiển thị view
        require_once __DIR__ . '/../views/orders.php';
    }
    
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handlePost();
        } else {
            $this->index();
        }
    }
    
    private function handlePost() {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'update_order_status':
                $this->updateOrderStatus();
                break;
                
            case 'confirm_cod_payment':
                $this->confirmCodPayment();
                break;
                
            default:
                // Nếu không phải AJAX, redirect về trang trước
                if (!$this->isAjaxRequest()) {
                    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'orders.php'));
                    exit();
                }
                echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
                break;
        }
    }
    
    private function updateOrderStatus() {
        $order_id = $_POST['order_id'];
        $new_status = $_POST['order_status'];
        
        // Lấy trạng thái hiện tại
        $current_order = $this->orderModel->getOrderById($order_id);
        $current_status = $current_order['status'];
        
        // RÀNG BUỘC NGHIÊM NGẶT
        $errors = [];
        
        // 1. Đã hủy → không thể thay đổi nữa
        if ($current_status == 2) {
            $_SESSION['error'] = "Đơn hàng đã hủy, không thể thay đổi trạng thái!";
            header("Location: orders.php");
            exit;
        }
        
        // 2. Đã xác nhận → không thể quay về chờ xác nhận
        if ($current_status == 1 && $new_status == 0) {
            $_SESSION['error'] = "Không thể chuyển từ 'Đã xác nhận' về 'Chờ xác nhận'!";
            header("Location: orders.php");
            exit;
        }
        
        // 3. Chỉ cho phép chuyển đúng luồng
        $allowed_transitions = [
            0 => [1, 2], // Chờ xác nhận → Đã xác nhận hoặc Hủy
            1 => [2],     // Đã xác nhận → Hủy
            2 => []       // Đã hủy → không được chuyển đi đâu
        ];
        
        if (!in_array($new_status, $allowed_transitions[$current_status])) {
            $_SESSION['error'] = "Chuyển trạng thái không hợp lệ!";
            header("Location: orders.php");
            exit;
        }
        
        // 4. Nếu hủy đơn đã thanh toán → đánh dấu cần hoàn tiền
        if ($new_status == 2) {
            // CHỈ cập nhật order_status, KHÔNG đụng đến payment_status
            if ($this->orderModel->updateOnlyOrderStatus($order_id, $new_status)) {
                
                // Thông báo tùy theo payment_status hiện tại
                $order = $this->orderModel->getOrderById($order_id);
                
                if ($order['payment_status'] == 'success') {
                    $_SESSION['warning'] = "Đã hủy đơn hàng #$order_id. ĐƠN ĐÃ THANH TOÁN - CẦN XỬ LÝ HOÀN TIỀN!";
                } else {
                    $_SESSION['success'] = "Đã hủy đơn hàng #$order_id thành công.";
                }
                
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi hủy đơn!";
            }
            
        } else {
            // Cập nhật trạng thái bình thường
            if ($this->orderModel->updateOrderStatus($order_id, $new_status)) {
                $_SESSION['success'] = "Cập nhật trạng thái thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra!";
            }
        }
        header("Location: orders.php");
        exit;
    }
    
    private function confirmCodPayment() {
        // Đảm bảo trả về JSON cho AJAX request
        header('Content-Type: application/json');
        
        $order_id = $_POST['order_id'] ?? '';
        
        if (empty($order_id)) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
            exit;
        }
        
        // Lấy thông tin đơn hàng
        $order = $this->orderModel->getOrderById($order_id);
        
        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Đơn hàng không tồn tại']);
            exit;
        }
        
        // Kiểm tra điều kiện: COD, chưa thanh toán, đã xác nhận
        if ($order['payment_method'] != 'cod') {
            echo json_encode(['success' => false, 'message' => 'Đơn hàng không phải COD']);
            exit;
        }
        
        if ($order['payment_status'] != 'pending') {
            echo json_encode(['success' => false, 'message' => 'Đơn hàng đã được thanh toán']);
            exit;
        }
        
        if ($order['status'] != 1) {
            echo json_encode(['success' => false, 'message' => 'Đơn hàng chưa được xác nhận']);
            exit;
        }
        
        // Cập nhật trạng thái thanh toán
        if ($this->orderModel->updatePaymentStatus($order_id, 'success')) {
            echo json_encode([
                'success' => true, 
                'message' => 'Đã xác nhận thu tiền COD thành công!',
                'order_id' => $order_id
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Có lỗi xảy ra khi cập nhật cơ sở dữ liệu'
            ]);
        }
        exit;
    }
    
    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

// Khởi tạo và xử lý request
try {
    $controller = new OrderController();
    $controller->handleRequest();
} catch (Exception $e) {
    // Xử lý lỗi
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
    } else {
        die("Lỗi: " . $e->getMessage());
    }
}
?>
