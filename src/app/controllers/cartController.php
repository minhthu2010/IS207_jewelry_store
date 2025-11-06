<?php
// CartController.php - FIXED VERSION

// BẬT HIỂN THỊ LỖI
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// BẮT ĐẦU OUTPUT BUFFERING
ob_start();

// REQUIRE FILES TRƯỚC KHI XỬ LÝ
try {
    // REQUIRE CONFIG FIRST
    $config_path = __DIR__ . '/../../config/config.php';
    if (!file_exists($config_path)) {
        throw new Exception('Config file not found');
    }
    
    require_once $config_path;

    // START SESSION
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // REQUIRE MODEL SECOND
    $model_path = __DIR__ . '/../models/cart.php';
    if (!file_exists($model_path)) {
        throw new Exception('Cart model file not found at: ' . $model_path);
    }
    
    require_once $model_path;

} catch (Exception $e) {
    // Nếu có lỗi khi require files, trả về JSON error
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Initialization error: ' . $e->getMessage()
    ]);
    exit;
}

// XỬ LÝ CHECKOUT (FORM SUBMIT)
if (isset($_POST['action']) && $_POST['action'] === 'checkout') {
    try {
        $controller = new CartController($conn);
        $controller->processCheckout();
    } catch (Exception $e) {
        $_SESSION['error'] = 'Checkout error: ' . $e->getMessage();
        header("Location: /jewelry_website/index.php?action=cart");
        exit;
    }
}

// CHỈ XỬ LÝ AJAX KHI CÓ ACTION
if (isset($_POST['action'])) {
    try {
        $controller = new CartController($conn);
        $response = [];

        switch ($_POST['action']) {
            case 'add_to_cart':
                $response = $controller->addToCart();
                break;
                
            case 'get_cart_count':
                $itemCount = $controller->getCartItemCount();
                $response = ['success' => true, 'itemCount' => $itemCount];
                break;
                
            case 'update_quantity':
                $item_id = $_POST['item_id'] ?? null;
                $quantity = $_POST['quantity'] ?? 1;
                if ($item_id) {
                    $result = $controller->updateCartItem($item_id, $quantity);
                    $response = ['success' => $result];
                }
                break;
                
            case 'remove_item':
                $item_id = $_POST['item_id'] ?? null;
                if ($item_id) {
                    $result = $controller->removeCartItem($item_id);
                    $response = ['success' => $result];
                }
                break;

            case 'check_stock':
                $variant_id = $_POST['variant_id'] ?? null;
                $quantity = $_POST['quantity'] ?? 1;
                if ($variant_id) {
                    $response = $controller->checkStock($variant_id, $quantity);
                }
                break;
                
            default:
                throw new Exception('Unknown action: ' . $_POST['action']);
        }

    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }

    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// CLASS DEFINITION 
class CartController {
    private $cartModel;
    private $customer_id;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->cartModel = new CartModel($db);
        $this->customer_id = $_SESSION['customer']['cus_id'] ?? null;
    }

    public function addToCart() {
        if (!$this->customer_id) {
            return ['success' => false, 'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng'];
        }

        $variant_id = $_POST['variant_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;

        // DEBUG CHI TIẾT
        error_log("=== ADD TO CART ===");
        error_log("Customer ID: " . $this->customer_id);
        error_log("Variant ID from POST: " . $variant_id);
        error_log("Quantity: " . $quantity);

        if (!$variant_id || $variant_id == 0) {
            error_log("ERROR: Missing variant_id");
            return ['success' => false, 'message' => 'Thiếu thông tin sản phẩm'];
        }

        try {
            // SỬ DỤNG MODEL để lấy thông tin variant - ĐÃ SỬA
            $variant = $this->getVariantWithProduct($variant_id);
            
            if (!$variant) {
                error_log("ERROR: Variant not found - ID: " . $variant_id);
                return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];
            }

            error_log("Found variant: " . $variant['product_name'] . " (Size: " . ($variant['size'] ?? 'Default') . ")");

            // KIỂM TRA TỒN KHO TRƯỚC KHI THÊM - SỬ DỤNG MODEL
            $stockCheck = $this->cartModel->checkStock($variant_id, $quantity);
            if (!$stockCheck['available']) {
                return [
                    'success' => false, 
                    'message' => 'Số lượng tồn kho không đủ. Chỉ còn ' . $stockCheck['current_stock'] . ' sản phẩm'
                ];
            }

            // Lấy hoặc tạo giỏ hàng - SỬ DỤNG MODEL
            $cart = $this->cartModel->getCartByCustomerId($this->customer_id);
            if (!$cart) {
                error_log("Creating new cart for customer: " . $this->customer_id);
                $cart_id = $this->cartModel->createCart($this->customer_id);
                if (!$cart_id) {
                    error_log("ERROR: Cannot create cart");
                    return ['success' => false, 'message' => 'Không thể tạo giỏ hàng'];
                }
                error_log("New cart created: " . $cart_id);
            } else {
                $cart_id = $cart['cart_id'];
                error_log("Using existing cart: " . $cart_id);
            }

            // Thêm vào giỏ hàng - SỬ DỤNG MODEL
            error_log("Adding to cart - Cart: $cart_id, Variant: $variant_id, Quantity: $quantity");
            $result = $this->cartModel->addToCart($cart_id, $variant_id, $quantity);

            if ($result) {
                $itemCount = $this->cartModel->getCartItemCount($cart_id);
                error_log("Successfully added to cart. Item count: " . $itemCount);
                
                return [
                    'success' => true,
                    'message' => 'Đã thêm ' . $variant['product_name'] . ' vào giỏ hàng!',
                    'itemCount' => $itemCount
                ];
            } else {
                error_log("ERROR: Failed to add to cart");
                return ['success' => false, 'message' => 'Không thể thêm sản phẩm vào giỏ hàng'];
            }

        } catch (Exception $e) {
            error_log("EXCEPTION: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }
    }

    // Phương thức helper để lấy thông tin variant 
    private function getVariantWithProduct($variant_id) {
        return $this->cartModel->getVariantWithProduct($variant_id);
    }

    // Phương thức kiểm tra tồn kho - SỬ DỤNG MODEL
    public function checkStock($variant_id, $quantity) {
        try {
            $stockCheck = $this->cartModel->checkStock($variant_id, $quantity);
            return [
                'success' => true,
                'available' => $stockCheck['available'],
                'current_stock' => $stockCheck['current_stock'],
                'requested_quantity' => $quantity
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'available' => false,
                'message' => 'Lỗi kiểm tra tồn kho'
            ];
        }
    }

    // Các phương thức khác - ĐÃ SỬA ĐỂ SỬ DỤNG MODEL
    public function getCartItemCount() {
        if (!$this->customer_id) return 0;
        
        $cart = $this->cartModel->getCartByCustomerId($this->customer_id);
        return $cart ? $this->cartModel->getCartItemCount($cart['cart_id']) : 0;
    }

    public function getCart() {
        if (!$this->customer_id) {
            return [];
        }

        $cart = $this->cartModel->getCartByCustomerId($this->customer_id);
        if ($cart) {
            return $this->cartModel->getCartItems($cart['cart_id']);
        }
        
        return [];
    }

    public function updateCartItem($item_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeCartItem($item_id);
        }

        return $this->cartModel->updateCartItemQuantity($item_id, $quantity);
    }

    public function removeCartItem($item_id) {
        return $this->cartModel->removeCartItem($item_id);
    }

    public function viewCart() {
        $cartItems = $this->getCart();
        include __DIR__ . '/../views/cart.php';
    }

    public function handleUpdateQuantity($item_id, $quantity) {
        if (!$this->customer_id) {
            return ['success' => false, 'message' => 'Vui lòng đăng nhập'];
        }

        try {
            $result = $this->updateCartItem($item_id, $quantity);
            
            if ($result) {
                // Lấy số lượng mới cho icon giỏ hàng
                $itemCount = $this->getCartItemCount();
                return [
                    'success' => true,
                    'itemCount' => $itemCount,
                    'message' => 'Cập nhật số lượng thành công'
                ];
            } else {
                return ['success' => false, 'message' => 'Cập nhật thất bại'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function handleRemoveItem($item_id) {
        if (!$this->customer_id) {
            return ['success' => false, 'message' => 'Vui lòng đăng nhập'];
        }

        try {
            $result = $this->removeCartItem($item_id);
            
            if ($result) {
                // Lấy số lượng mới cho icon giỏ hàng
                $itemCount = $this->getCartItemCount();
                return [
                    'success' => true,
                    'itemCount' => $itemCount,
                    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
                ];
            } else {
                return ['success' => false, 'message' => 'Xóa sản phẩm thất bại'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function processCheckout() {
        if (!$this->customer_id) {
            $_SESSION['error'] = "Vui lòng đăng nhập để thanh toán";
            header("Location: index.php?action=login");
            exit;
        }

        $selectedItems = $_POST['selected_items'] ?? [];
        
        if (empty($selectedItems)) {
            $_SESSION['error'] = "Vui lòng chọn ít nhất một sản phẩm để thanh toán";
            header("Location: index.php?action=cart");
            exit;
        }
        
        // Lưu selected items vào session để xử lý thanh toán
        $_SESSION['checkout_items'] = $selectedItems;
        
        header("Location: index.php?action=checkout");
        exit;
    }
}
?>
