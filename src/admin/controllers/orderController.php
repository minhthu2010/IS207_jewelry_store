<?php
// Kiểm tra xem file config có tồn tại không
$configPath = __DIR__ . '/../../config/config.php';
if (!file_exists($configPath)) {
    die("File config không tồn tại: " . $configPath);
}
require_once $configPath;

$modelPath = __DIR__ . '/../../app/models/order.php';
if (!file_exists($modelPath)) {
    die("File model không tồn tại: " . $modelPath);
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
        // Kiểm tra đăng nhập admin
        // if (!isset($_SESSION['admin_logged_in'])) {
        //     // Nếu chưa đăng nhập, chuyển hướng về trang đăng nhập admin
        //     header("Location: index.php");
        //     exit();
        // }
        
        // Lấy các tham số lọc
        $filters = [
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'status' => $_GET['status'] ?? '',
            'payment_method' => $_GET['payment_method'] ?? '',
            'payment_status' => $_GET['payment_status'] ?? '',
            'min_total' => $_GET['min_total'] ?? '',
            'max_total' => $_GET['max_total'] ?? ''
        ];
        
        // Lấy dữ liệu
        $orders = $this->orderModel->getAllOrders($filters);
        $years = $this->orderModel->getYears();
        $paymentMethods = $this->orderModel->getPaymentMethods();
        $statusLabels = $this->orderModel->getStatusLabels();
        $paymentStatusLabels = $this->orderModel->getPaymentStatusLabels();
        
        // Hiển thị view - SỬA ĐƯỜNG DẪN
        $viewPath = __DIR__ . '/../views/orders.php';
        if (!file_exists($viewPath)) {
            die("File view không tồn tại: " . $viewPath);
        }
        require_once $viewPath;
    }
    
    public function updateStatus() {
        // Kiểm tra đăng nhập admin
        // if (!isset($_SESSION['admin_logged_in'])) {
        //     header("Location: index.php");
        //     exit();
        // }
        
        if (isset($_POST['action'])) {
            if ($_POST['action'] == 'update_order_status') {
                $order_id = $_POST['order_id'];
                $status = $_POST['status'];
                
                if ($this->orderModel->updateOrderStatus($order_id, $status)) {
                    $_SESSION['success'] = "Cập nhật trạng thái đơn hàng thành công!";
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật trạng thái đơn hàng!";
                }
                
            } elseif ($_POST['action'] == 'update_payment_status') {
                $order_id = $_POST['order_id'];
                $payment_status = $_POST['payment_status'];
                
                if ($this->updatePaymentStatus($order_id, $payment_status)) {
                    $_SESSION['success'] = "Cập nhật trạng thái thanh toán thành công!";
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật trạng thái thanh toán!";
                }
            }
            
            header("Location: orders.php");
            exit();
        }
    }

    private function updatePaymentStatus($order_id, $payment_status) {
        $sql = "UPDATE orders SET payment_status = ?, updated_at = NOW() WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$payment_status, $order_id]);
    }
}

// Xử lý request
$controller = new OrderController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller->updateStatus();
} else {
    $controller->index();
}
?>