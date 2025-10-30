<?php

require_once __DIR__ . '/../models/checkout.php';

class CheckoutController {
    private $model;
    private $customer_id;

    public function __construct($database) {
        error_log("=== CHECKOUTCONTROLLER CONSTRUCT ===");
        $this->model = new CheckoutModel($database);
        $this->customer_id = $_SESSION['customer_id'] ?? $_SESSION['customer']['cus_id'] ?? null;
        error_log("Customer ID: " . $this->customer_id);
    }

    public function index() {
        if (!$this->customer_id) {
            header('Location: index.php?action=login');
            exit;
        }

        // Lấy selected items từ session
        $selected_items = $_SESSION['checkout_items'] ?? [];
        
        if (empty($selected_items)) {
            $_SESSION['error'] = "No items selected for checkout";
            header('Location: index.php?action=cart');
            exit;
        }

        $customer_info = $this->model->getCustomerInfo($this->customer_id);
        $cart_items = $this->model->getCartItems($this->customer_id, $selected_items);
        
        if (empty($cart_items)) {
            $_SESSION['error'] = "Selected items not found in cart";
            header('Location: index.php?action=cart');
            exit;
        }
        
        // Tính tổng tiền
        $subtotal = 0;
        foreach ($cart_items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // Phí vận chuyển luôn = 0
        $shipping_fee = $this->model->calculateShippingFee('', $subtotal);
        $total = $subtotal + $shipping_fee;

        include __DIR__ . '/../views/checkout.php';
    }

    public function processOrder() {
        error_log("=== PROCESS ORDER START ===");
        
        // Ghi log toàn bộ input
        error_log("RAW INPUT: " . file_get_contents('php://input'));
        error_log("SESSION: " . print_r($_SESSION, true));
        
        if (!$this->customer_id) {
            error_log("ERROR: No customer ID");
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("ERROR: Wrong method - " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        error_log("Decoded data: " . print_r($data, true));

        // Kiểm tra nếu JSON decode lỗi
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg());
            echo json_encode(['success' => false, 'message' => 'Invalid JSON data: ' . json_last_error_msg()]);
            exit;
        }

        try {
            // Lấy thông tin từ request
            $shipping_info = $data['shipping_info'] ?? null;
            $payment_method = $data['payment_method'] ?? null;
            $selected_cart_items = $_SESSION['checkout_items'] ?? [];
            $notes = $data['notes'] ?? '';

            error_log("Shipping info: " . print_r($shipping_info, true));
            error_log("Payment method: " . $payment_method);
            error_log("Selected items: " . print_r($selected_cart_items, true));

            if (!$shipping_info || !$payment_method) {
                throw new Exception('Thiếu thông tin bắt buộc');
            }

            // Lấy thông tin giỏ hàng
            $cart_items = $this->model->getCartItems($this->customer_id, $selected_cart_items);
            error_log("Cart items count: " . count($cart_items));
            
            if (empty($cart_items)) {
                throw new Exception('Không có sản phẩm nào được chọn');
            }

            // Tính tổng tiền
            $subtotal = 0;
            foreach ($cart_items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            error_log("Subtotal: " . $subtotal);

            // Phí vận chuyển luôn = 0
            $shipping_fee = $this->model->calculateShippingFee($shipping_info['address'], $subtotal);
            $total_amount = $subtotal + $shipping_fee;

            error_log("Total amount: " . $total_amount);

            // Tạo đơn hàng
            $order_data = [
                'customer_id' => $this->customer_id,
                'total' => $total_amount,
                'payment_method' => $payment_method,
                'payment_status' => $payment_method === 'cod' ? 'pending' : 'pending',
                'shipping_address' => $shipping_info['address'],
                'shipping_fullname' => $shipping_info['fullname'],
                'shipping_phone' => $shipping_info['phone'],
                'shipping_fee' => $shipping_fee,
                'notes' => $notes
            ];

            error_log("Order data: " . print_r($order_data, true));

            $order_id = $this->model->createOrder($order_data);
            error_log("Order ID created: " . $order_id);
            
            if (!$order_id) {
                throw new Exception('Không thể tạo đơn hàng');
            }

            // Thêm chi tiết đơn hàng
            $today = date('Y-m-d');
            foreach ($cart_items as $item) {
                $end_warranty = $this->model->calculateWarrantyEndDate($item['pro_id'], $today);
                
                $order_detail = [
                    'order_id' => $order_id,
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                    'start_warranty_date' => $today,
                    'end_warranty_date' => $end_warranty
                ];

                if (!$this->model->createOrderDetail($order_detail)) {
                    throw new Exception('Không thể thêm chi tiết đơn hàng');
                }

                // Cập nhật số lượng tồn kho
                if (!$this->model->updateStock($item['variant_id'], $item['quantity'])) {
                    throw new Exception('Không thể cập nhật tồn kho');
                }
            }

            // Xử lý thanh toán ngân hàng
            $transaction_data = null;
            if ($payment_method === 'bank') {
                $transaction_data = $this->processBankPayment($order_id, $total_amount);
                error_log("Transaction data: " . print_r($transaction_data, true));
            }

            // Xóa items đã order khỏi giỏ hàng
            $cart_item_ids = array_column($cart_items, 'cart_item_id');
            $this->model->removeCartItems($this->customer_id, $cart_item_ids);

            // Xóa session checkout items
            unset($_SESSION['checkout_items']);

            error_log("=== PROCESS ORDER SUCCESS ===");
            
            echo json_encode([
                'success' => true,
                'order_id' => $order_id,
                'transaction_data' => $transaction_data,
                'message' => 'Đặt hàng thành công!'
            ]);

        } catch (Exception $e) {
            error_log("=== PROCESS ORDER ERROR: " . $e->getMessage() . " ===");
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function processBankPayment($order_id, $amount) {
        // Tạo QR code với VietQR
        $qr_data = $this->generateVietQR($amount, $order_id);
        
        $transaction_data = [
            'order_id' => $order_id,
            'amount' => $amount,
            'request_id' => uniqid('REQ_'),
            'trans_id' => null,
            'response_data' => json_encode(['qr_data' => $qr_data])
        ];

        // Lưu transaction
        $this->model->createPaymentTransaction($transaction_data);

        return [
            'qr_code' => $qr_data,
            'amount' => number_format($amount, 0, ',', '.') . '₫',
            'order_id' => $order_id
        ];
    }

    private function generateVietQR($amount, $order_id) {
    // Sử dụng thông tin DEMO - KHÔNG PHẢI TÀI KHOẢN THẬT
        $bank_info = [
        'bank_id' => 'VCB', // Mã ngân hàng demo
        'account_number' => '2352147415421331', // Số tài khoản demo
        'account_name' => 'Jewelry_store', // Tên demo
        'amount' => $amount,
        'description' => "Thanh toan don hang #$order_id"
        ];

        $qr_content = "https://img.vietqr.io/image/{$bank_info['bank_id']}-{$bank_info['account_number']}-compact2.jpg?amount={$bank_info['amount']}&addInfo={$bank_info['description']}&accountName=" . urlencode($bank_info['account_name']);
    
        return $qr_content;
    }
    private function generateBankTransferInfo($amount, $order_id) {
    return [
        'bank_name' => 'Ngân hàng TMCP Ngoại thương Việt Nam (Vietcombank)',
        'account_number' => '***Xem trong email xác nhận***',
        'account_name' => 'Jewelry Store',
        'amount' => number_format($amount, 0, ',', '.') . '₫',
        'content' => "THANHTOAN {$order_id}",
        'note' => 'Quý khách vui lòng chuyển khoản và ghi nội dung theo hướng dẫn. Thông tin tài khoản thật sẽ được gửi qua email xác nhận.'
    ];
    }
}
?>
