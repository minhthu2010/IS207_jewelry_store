<?php
class ProductModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllProducts() {
        try {
            $query = "
                SELECT 
                    p.pro_id,
                    p.name,
                    p.description,
                    c.name as category_name,
                    c.has_size,
                    MIN(pv.price) as min_price,
                    MAX(pv.price) as max_price,
                    pi.image_url,
                    COUNT(DISTINCT pv.variant_id) as variant_count
                FROM product p
                LEFT JOIN category c ON p.category_id = c.cate_id
                LEFT JOIN product_variant pv ON p.pro_id = pv.product_id
                LEFT JOIN product_image pi ON p.pro_id = pi.product_id AND pi.sort_order = 0
                GROUP BY p.pro_id, p.name, p.description, c.name, c.has_size, pi.image_url
                ORDER BY p.created_at DESC
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProductModel Error: " . $e->getMessage());
            return [];
        }
    }

    public function getProductsByCategory($categoryId) {
        try {
            $query = "
                SELECT 
                    p.pro_id,
                    p.name,
                    p.description,
                    c.name as category_name,
                    c.has_size,
                    MIN(pv.price) as min_price,
                    MAX(pv.price) as max_price,
                    pi.image_url,
                    COUNT(DISTINCT pv.variant_id) as variant_count
                FROM product p
                LEFT JOIN category c ON p.category_id = c.cate_id
                LEFT JOIN product_variant pv ON p.pro_id = pv.product_id
                LEFT JOIN product_image pi ON p.pro_id = pi.product_id AND pi.sort_order = 0
                WHERE p.category_id = :category_id
                GROUP BY p.pro_id, p.name, p.description, c.name, c.has_size, pi.image_url
                ORDER BY p.created_at DESC
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProductModel Error: " . $e->getMessage());
            return [];
        }
    }

    public function getCategories() {
        try {
            $query = "SELECT cate_id, name, has_size FROM category ORDER BY name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProductModel Error: " . $e->getMessage());
            return [];
        }
    }

    public function getProductById($productId) {
        try {
            $query = "
                SELECT 
                    p.*,
                    c.name as category_name,
                    c.has_size,
                    w.period as warranty_period,
                    w.description as warranty_description
                FROM product p
                LEFT JOIN category c ON p.category_id = c.cate_id
                LEFT JOIN warranty w ON p.warranty_id = w.w_id
                WHERE p.pro_id = :product_id
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProductModel Error: " . $e->getMessage());
            return null;
        }
    }

    public function getVariantsByProduct($productId) {
        try {
            $query = "
                SELECT 
                    variant_id,
                    sku,
                    size,
                    price,
                    stock_quantity
                FROM product_variant 
                WHERE product_id = :product_id 
                ORDER BY price ASC
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProductModel Error: " . $e->getMessage());
            return [];
        }
    }

    public function getProductImages($productId) {
        try {
            $query = "
                SELECT 
                    image_id,
                    image_url,
                    sort_order
                FROM product_image 
                WHERE product_id = :product_id 
                ORDER BY sort_order ASC, image_id ASC
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProductModel Error: " . $e->getMessage());
            return [];
        }
    }

    // THÊM METHOD LẤY REVIEWS
    public function getProductReviews($productId) {
        try {
            $query = "
                SELECT 
                    pr.*,
                    c.fullname,
                    c.email
                FROM product_review pr
                LEFT JOIN customer c ON pr.customer_id = c.cus_id
                WHERE pr.product_id = :product_id
                ORDER BY pr.created_at DESC
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProductModel getReviews Error: " . $e->getMessage());
            return [];
        }
    }




    // THÊM METHOD LẤY FILTER OPTIONS TỪ TAGS
    public function getFilterOptions() {
        try {
            $options = [];

            // Lấy các category (loại trang sức)
            $options['categories'] = $this->getCategories();

            // Lấy các tags phổ biến (loại bỏ trùng lặp)
            $options['popular_tags'] = $this->getPopularTags();

            // Lấy price range từ giá thấp nhất của sản phẩm
            $options['price_range'] = $this->getPriceRange();

            return $options;
        } catch (PDOException $e) {
            error_log("ProductModel getFilterOptions Error: " . $e->getMessage());
            return [];
        }
    }

    // THÊM METHOD LẤY TAGS PHỔ BIẾN
    private function getPopularTags() {
        try {
            $query = "
                SELECT 
                    tag_name,
                    COUNT(*) as tag_count
                FROM product_tags 
                GROUP BY tag_name 
                HAVING tag_count >= 1
                ORDER BY tag_count DESC, tag_name ASC
                LIMIT 20
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProductModel getPopularTags Error: " . $e->getMessage());
            return [];
        }
    }

    // CẬP NHẬT METHOD LẤY PRICE RANGE (từ giá thấp nhất)
    private function getPriceRange() {
        try {
            $query = "
                SELECT 
                    MIN(min_price) as min_price,
                    MAX(min_price) as max_price
                FROM (
                    SELECT 
                        p.pro_id,
                        MIN(pv.price) as min_price
                    FROM product p
                    LEFT JOIN product_variant pv ON p.pro_id = pv.product_id
                    WHERE pv.price > 0
                    GROUP BY p.pro_id
                ) as product_min_prices
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'min_price' => $result['min_price'] ?? 0,
                'max_price' => $result['max_price'] ?? 1000
            ];
        } catch (PDOException $e) {
            error_log("ProductModel getPriceRange Error: " . $e->getMessage());
            return ['min_price' => 0, 'max_price' => 1000];
        }
    }

    // CẬP NHẬT METHOD LỌC SẢN PHẨM VỚI TAGS
    public function getFilteredProducts($filters = []) {
    try {
        $whereConditions = [];
        $params = [];
        $havingConditions = [];

        // Base query - LẤY GIÁ THẤP NHẤT
        $query = "
            SELECT 
                p.pro_id,
                p.name,
                p.description,
                c.name as category_name,
                c.has_size,
                MIN(pv.price) as min_price,
                MAX(pv.price) as max_price,
                pi.image_url,
                COUNT(DISTINCT pv.variant_id) as variant_count,
                GROUP_CONCAT(DISTINCT pt.tag_name) as tags
            FROM product p
            LEFT JOIN category c ON p.category_id = c.cate_id
            LEFT JOIN product_variant pv ON p.pro_id = pv.product_id
            LEFT JOIN product_image pi ON p.pro_id = pi.product_id AND pi.sort_order = 0
            LEFT JOIN product_tags pt ON p.pro_id = pt.product_id
        ";

        // Áp dụng category filter
        if (!empty($filters['category'])) {
            $whereConditions[] = "p.category_id = :category";
            $params[':category'] = $filters['category'];
        }

        // Áp dụng tag filter
        if (!empty($filters['tags'])) {
            $tagConditions = [];
            foreach ($filters['tags'] as $index => $tag) {
                $tagConditions[] = "pt.tag_name = :tag_{$index}";
                $params[":tag_{$index}"] = $tag;
            }
            $whereConditions[] = "(" . implode(" OR ", $tagConditions) . ")";
        }

        // Price filter - Áp dụng trên giá thấp nhất
        if (!empty($filters['min_price']) && !empty($filters['max_price'])) {
            $havingConditions[] = "MIN(pv.price) BETWEEN :min_price AND :max_price";
            $params[':min_price'] = $filters['min_price'];
            $params[':max_price'] = $filters['max_price'];
        }

        // Thêm điều kiện WHERE nếu có
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }


        // Group
        $query .= " GROUP BY p.pro_id, p.name, p.description, c.name, c.has_size, pi.image_url";

        // Thêm điều kiện HAVING nếu có
        if (!empty($havingConditions)) {
            $query .= " HAVING " . implode(" AND ", $havingConditions);
        }

        // Xử lý sorting - CẬP NHẬT THEO TIẾNG VIỆT
        $sort = $filters['sort'] ?? 'newest'; // Mặc định là sản phẩm mới nhất
        switch ($sort) {
            case 'price_low':
                $query .= " ORDER BY min_price ASC";
                break;
            case 'price_high':
                $query .= " ORDER BY min_price DESC";
                break;
            case 'name':
                $query .= " ORDER BY p.name ASC";
                break;
            case 'bestseller':
                // Tạm thời sử dụng pro_id, sau này sẽ thay bằng số lượng bán
                $query .= " ORDER BY p.pro_id DESC";
                break;
            case 'newest':
            default:
                $query .= " ORDER BY p.pro_id DESC"; // Sản phẩm mới nhất theo ID
                break;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("ProductModel getFilteredProducts Error: " . $e->getMessage());
        return [];
    }
}

    // THÊM METHOD ĐỂ LẤY TAGS CHO PRODUCT
    public function getProductTags($productId) {
        try {
            $query = "SELECT tag_name FROM product_tags WHERE product_id = :product_id ORDER BY tag_name";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("ProductModel getProductTags Error: " . $e->getMessage());
            return [];
        }
    }
}
?>
