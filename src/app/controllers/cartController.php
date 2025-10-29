<?php
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

// ========== CLASS DEFINITION ==========
class CartController {
    private $cartModel;
    private $customer_id;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->cartModel = new CartModel($db);  // CartModel class đã được require
        $this->customer_id = $_SESSION['customer']['cus_id'] ?? null;
    }

    public function addToCart() {
    if (!$this->customer_id) {
        return ['success' => false, 'message' => 'Please login to add to cart'];
    }

    $variant_id = $_POST['variant_id'] ?? null;
    $quantity = $_POST['quantity'] ?? 1;

    // DEBUG CHI TIẾT
    error_log("=== ADD TO CART ===");
    error_log("Customer ID: " . $this->customer_id);
    error_log("Variant ID from POST: " . $variant_id);
    error_log("Quantity: " . $quantity);

    if (!$variant_id || $variant_id == 0) {
        error_log("❌ ERROR: Missing variant_id");
        return ['success' => false, 'message' => 'Missing product information (variant_id: ' . $variant_id . ')'];
    }

    try {
        // Kiểm tra variant tồn tại và lấy thông tin
        $stmt = $this->conn->prepare("
            SELECT pv.*, p.name as product_name 
            FROM product_variant pv 
            JOIN product p ON pv.product_id = p.pro_id 
            WHERE pv.variant_id = ?
        ");
        $stmt->execute([$variant_id]);
        $variant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$variant) {
            error_log("❌ ERROR: Variant not found - ID: " . $variant_id);
            return ['success' => false, 'message' => 'Product variant not found (ID: ' . $variant_id . ')'];
        }

        error_log("✅ Found variant: " . $variant['product_name'] . " (Size: " . ($variant['size'] ?? 'Default') . ")");

        // Lấy hoặc tạo giỏ hàng
        $cart = $this->cartModel->getCartByCustomerId($this->customer_id);
        if (!$cart) {
            error_log("Creating new cart for customer: " . $this->customer_id);
            $cart_id = $this->cartModel->createCart($this->customer_id);
            if (!$cart_id) {
                error_log("❌ ERROR: Cannot create cart");
                return ['success' => false, 'message' => 'Cannot create cart'];
            }
            error_log("✅ New cart created: " . $cart_id);
        } else {
            $cart_id = $cart['cart_id'];
            error_log("Using existing cart: " . $cart_id);
        }

        // Thêm vào giỏ hàng
        error_log("Adding to cart - Cart: $cart_id, Variant: $variant_id, Quantity: $quantity");
        $result = $this->cartModel->addToCart($cart_id, $variant_id, $quantity);

        if ($result) {
            $itemCount = $this->cartModel->getCartItemCount($cart_id);
            error_log("✅ Successfully added to cart. Item count: " . $itemCount);
            
            return [
                'success' => true,
                'message' => 'Added ' . $variant['product_name'] . ' to cart!',
                'itemCount' => $itemCount
            ];
        } else {
            error_log("❌ ERROR: Failed to add to cart");
            return ['success' => false, 'message' => 'Failed to add product to cart'];
        }

    } catch (Exception $e) {
        error_log("❌ EXCEPTION: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

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

    public function updateCart() {
    if ($_POST['action'] == 'checkout') {
        $selectedItems = $_POST['selected_items'] ?? [];
        
        if (empty($selectedItems)) {
            $_SESSION['error'] = "Please select at least one item to checkout";
            header("Location: /cart");
            exit;
        }
        
        // Lưu selected items vào session để xử lý thanh toán
        $_SESSION['checkout_items'] = $selectedItems;
        
        // Chuyển hướng đến trang thanh toán
        header("Location: checkout.php");
        exit;
    }
}
}
?>
