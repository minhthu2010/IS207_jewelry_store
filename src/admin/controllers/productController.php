<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/models/admin_product.php';
require_once __DIR__ . '/../../app/models/admin_product_variant.php';
require_once __DIR__ . '/../../app/models/admin_product_image.php';
require_once __DIR__ . '/../../app/models/admin_product_tag.php';

class ProductController_Admin {
    private $productModel;
    private $variantModel;
    private $imageModel;
    private $tagModel;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->productModel = new ProductModel_Admin($conn);
        $this->variantModel = new ProductVariantModel($conn);
        $this->imageModel = new ProductImageModel($conn);
        $this->tagModel = new ProductTagModel($conn);
    }

    public function index() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        $filters = [
            'search' => $_GET['search'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'has_size' => $_GET['has_size'] ?? '',
            'stock_status' => $_GET['stock_status'] ?? '',
            'custom_stock' => $_GET['custom_stock'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'sort_by' => $_GET['sort_by'] ?? 'newest'
        ];

        $products = $this->productModel->getAllProducts($filters);
        $categories = $this->productModel->getCategories();

        include __DIR__ . '/../views/product_list.php';
    }

    public function create() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        $categories = $this->productModel->getCategories();
        $warranties = $this->productModel->getWarranties();
        
        include __DIR__ . '/../views/product_create.php';
    }

    public function edit($product_id) {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            header('Location: products.php');
            exit;
        }

        $categories = $this->productModel->getCategories();
        $warranties = $this->productModel->getWarranties();
        $variants = $this->variantModel->getVariantsByProduct($product_id);
        $images = $this->imageModel->getImagesByProduct($product_id);
        $tags = $this->tagModel->getTagsByProduct($product_id);

        include __DIR__ . '/../views/product_edit.php';
    }

    public function store() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        if ($_POST['action'] == 'create_product') {
            try {
                $category_id = $_POST['category_id'];
                $category = $this->productModel->getCategoryById($category_id);
                $has_size = $category['has_size'] ?? false;

                $product_id = $this->productModel->createProduct([
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'category_id' => $category_id,
                    'warranty_id' => $_POST['warranty_id'] ?: null
                ]);

                if (!empty($_POST['tags'])) {
                    $tags = explode(',', $_POST['tags']);
                    foreach ($tags as $tag) {
                        $tag = trim($tag);
                        if (!empty($tag)) {
                            $this->tagModel->addTag($product_id, $tag);
                        }
                    }
                }

                if (!$has_size) {
                    $this->variantModel->createVariant([
                        'product_id' => $product_id,
                        'sku' => $_POST['sku'] ?? 'SKU_' . $product_id,
                        'size' => null,
                        'price' => $_POST['price'] ?? 0,
                        'stock_quantity' => $_POST['stock_quantity'] ?? 0
                    ]);
                } else {
                    if (!empty($_POST['sku']) && !empty($_POST['price'])) {
                        $this->variantModel->createVariant([
                            'product_id' => $product_id,
                            'sku' => $_POST['sku'],
                            'size' => $_POST['size'] ?? null,
                            'price' => $_POST['price'],
                            'stock_quantity' => $_POST['stock_quantity'] ?? 0
                        ]);
                    }
                }

                $_SESSION['success'] = 'Sản phẩm đã được tạo thành công!';
                header('Location: products.php?action=edit&id=' . $product_id);
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi khi tạo sản phẩm: ' . $e->getMessage();
                header('Location: products.php?action=create');
                exit;
            }
        }
    }

    public function update($product_id) {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        if ($_POST['action'] == 'update_product') {
            try {
                $result = $this->productModel->updateProduct($product_id, [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'category_id' => $_POST['category_id'],
                    'warranty_id' => !empty($_POST['warranty_id']) ? $_POST['warranty_id'] : null
                ]);

                $this->tagModel->deleteTagsByProduct($product_id);
                if (!empty($_POST['tags'])) {
                    $tags = explode(',', $_POST['tags']);
                    foreach ($tags as $tag) {
                        $tag = trim($tag);
                        if (!empty($tag)) {
                            $this->tagModel->addTag($product_id, $tag);
                        }
                    }
                }

                $_SESSION['success'] = 'Sản phẩm đã được cập nhật thành công!';
                header('Location: products.php?action=edit&id=' . $product_id);
                exit;
                
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi khi cập nhật sản phẩm: ' . $e->getMessage();
                header('Location: products.php?action=edit&id=' . $product_id);
                exit;
            }
        } else {
            header('Location: products.php?action=edit&id=' . $product_id);
            exit;
        }
    }

    public function delete($product_id) {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        $this->productModel->deleteProduct($product_id);
        $_SESSION['success'] = 'Sản phẩm đã được xóa thành công!';
        header('Location: products.php');
        exit;
    }

    public function addVariant() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        if ($_POST['action'] == 'add_variant') {
            $product_id = $_POST['product_id'];
            
            if ($this->variantModel->variantExists($_POST['sku'])) {
                $_SESSION['error'] = 'SKU đã tồn tại!';
                header('Location: products.php?action=edit&id=' . $product_id . '#variants');
                exit;
            }

            $this->variantModel->createVariant([
                'product_id' => $product_id,
                'sku' => $_POST['sku'],
                'size' => $_POST['size'] ?: null,
                'price' => $_POST['price'],
                'stock_quantity' => $_POST['stock_quantity']
            ]);

            $_SESSION['success'] = 'Phiên bản sản phẩm mới đã được thêm thành công!';
            header('Location: products.php?action=edit&id=' . $product_id . '#variants');
            exit;
        }
    }

    public function updateVariant($variant_id) {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        if ($_POST['action'] == 'update_variant') {
            $product_id = $_POST['product_id'];
            
            if ($this->variantModel->variantExists($_POST['sku'], $variant_id)) {
                $_SESSION['error'] = 'SKU đã tồn tại!';
                header('Location: products.php?action=edit&id=' . $product_id . '#variants');
                exit;
            }

            $this->variantModel->updateVariant($variant_id, [
                'sku' => $_POST['sku'],
                'size' => $_POST['size'] ?: null,
                'price' => $_POST['price'],
                'stock_quantity' => $_POST['stock_quantity']
            ]);

            $_SESSION['success'] = 'Phiên bản sản phẩm đã được cập nhật thành công!';
            header('Location: products.php?action=edit&id=' . $product_id . '#variants');
            exit;
        }
    }

    public function deleteVariant($variant_id) {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        if ($_POST['action'] == 'delete_variant') {
            $product_id = $_POST['product_id'];
            $this->variantModel->deleteVariant($variant_id);
            $_SESSION['success'] = 'Phiên bản sản phẩm đã được xóa thành công!';
            header('Location: products.php?action=edit&id=' . $product_id . '#variants');
            exit;
        }
    }

    public function addImage() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        if ($_POST['action'] == 'add_image') {
            try {
                $product_id = $_POST['product_id'];
                
                if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("Vui lòng chọn file ảnh để tải lên");
                }

                $upload_dir = __DIR__ . '/../../public/assets/images/products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $file_type = $_FILES['image']['type'];
                if (!in_array($file_type, $allowed_types)) {
                    throw new Exception("Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP)");
                }

                $file_name = time() . '_' . uniqid() . '_' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $this->imageModel->addImage($product_id, $file_name, 1);
                    $_SESSION['success'] = 'Ảnh đã được tải lên thành công!';
                } else {
                    throw new Exception("Lỗi khi tải lên ảnh!");
                }

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            
            header('Location: products.php?action=edit&id=' . $product_id . '#images');
            exit;
        }
    }

    public function setMainImage($image_id) {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        if ($_POST['action'] == 'set_main_image') {
            $product_id = $_POST['product_id'];
            $this->imageModel->setMainImage($product_id, $image_id);
            $_SESSION['success'] = 'Đã đặt làm ảnh chính!';
            header('Location: products.php?action=edit&id=' . $product_id . '#images');
            exit;
        }
    }

    public function deleteImage($image_id) {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        if ($_POST['action'] == 'delete_image') {
            $product_id = $_POST['product_id'];
            $this->imageModel->deleteImage($image_id);
            $_SESSION['success'] = 'Ảnh đã được xóa!';
            header('Location: products.php?action=edit&id=' . $product_id . '#images');
            exit;
        }
    }

    public function updateImageSortOrders() {
        // Kiểm tra session
        $this->checkAdminAuth();
        
        if ($_POST['action'] == 'update_image_sort_orders') {
            $product_id = $_POST['product_id'];
            $sort_orders = $_POST['sort_orders'];
            
            try {
                $this->imageModel->updateMultipleSortOrders($sort_orders);
                $_SESSION['success'] = 'Thứ tự ảnh đã được cập nhật thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi khi cập nhật thứ tự ảnh: ' . $e->getMessage();
            }
            
            header('Location: products.php?action=edit&id=' . $product_id . '#images');
            exit;
        }
    }

    private function checkAdminAuth() {
        if (!isset($_SESSION['admin'])) {
            header("Location: login.php");
            exit;
        }
    }
}

// Không tự động chạy controller, chỉ khởi tạo khi được gọi từ file chính
?>
