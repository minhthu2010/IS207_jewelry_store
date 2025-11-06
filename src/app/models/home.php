<?php
class HomeModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy tất cả categories
    public function getAllCategories() {
        $query = "SELECT * FROM category WHERE name IS NOT NULL ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy sản phẩm bán chạy nhất (dựa trên số lượng đã bán)
    public function getBestSellingProducts($limit = 6) {
    $query = "
        SELECT 
            p.pro_id,
            p.name,
            p.description,
            c.name as category_name,
            pi.image_url,
            MIN(pv.price) as min_price,
            MAX(pv.price) as max_price,
            COALESCE(SUM(od.quantity), 0) as total_sold
        FROM product p
        LEFT JOIN category c ON p.category_id = c.cate_id
        LEFT JOIN product_image pi ON p.pro_id = pi.product_id AND pi.sort_order = 0
        LEFT JOIN product_variant pv ON p.pro_id = pv.product_id
        LEFT JOIN order_detail od ON pv.variant_id = od.variant_id
        LEFT JOIN orders o ON od.order_id = o.order_id 
            AND o.status = 3  -- CHỈ đơn hàng đã hoàn thành
            AND o.payment_status = 'success'  -- CHỈ thanh toán thành công
        WHERE pv.stock_quantity > 0
        GROUP BY p.pro_id
        HAVING total_sold > 0
        ORDER BY total_sold DESC, p.created_at DESC
        LIMIT ?
    ";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $this->processProductImages($products);
}
    // Lấy sản phẩm trending (nếu không có đơn hàng thì lấy sản phẩm mới nhất)
    public function getTrendingProducts($limit = 6) {
    // Lấy sản phẩm bán chạy (đã sửa query)
    $bestSelling = $this->getBestSellingProducts($limit);
    
    // Nếu có đủ sản phẩm bán chạy, trả về
    if (count($bestSelling) >= $limit) {
        return $bestSelling;
    }
    
    // Nếu thiếu, lấy thêm sản phẩm mới nhất
    $latestProducts = $this->getLatestProducts($limit);
    
    // Kết hợp và loại bỏ trùng lặp
    $combined = $bestSelling;
    $existingIds = array_column($bestSelling, 'pro_id');
    
    foreach ($latestProducts as $product) {
        if (!in_array($product['pro_id'], $existingIds) && count($combined) < $limit) {
            $combined[] = $product;
            $existingIds[] = $product['pro_id'];
        }
    }
    
    return $combined;
}

    // Lấy sản phẩm mới nhất
    public function getLatestProducts($limit = 6) {
    $query = "
        SELECT DISTINCT
            p.pro_id,
            p.name,
            p.description,
            c.name as category_name,
            pi.image_url,
            MIN(pv.price) as min_price,
            MAX(pv.price) as max_price,
            0 as total_sold
        FROM product p
        LEFT JOIN category c ON p.category_id = c.cate_id
        LEFT JOIN product_image pi ON p.pro_id = pi.product_id AND pi.sort_order = 0
        LEFT JOIN product_variant pv ON p.pro_id = pv.product_id
        WHERE pv.stock_quantity > 0
        GROUP BY p.pro_id
        ORDER BY p.created_at DESC
        LIMIT ?
    ";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $this->processProductImages($products);
}

    // Xử lý đường dẫn ảnh sản phẩm
    private function processProductImages($products) {
    foreach ($products as &$product) {
        if (!empty($product['image_url'])) {
            // Thêm đường dẫn đầy đủ nếu ảnh chỉ có tên file
            if (strpos($product['image_url'], '/') === false && 
                strpos($product['image_url'], 'assets/') === false) {
                $product['image_url'] = 'assets/images/products/' . $product['image_url'];
            }
        } else {
            // Ảnh mặc định theo category
            $categoryImages = [
                'Nhẫn' => 'assets/images/rings_home.jpg',
                'Bông tai' => 'assets/images/earrings_home.jpg',
                'Dây chuyền' => 'assets/images/necklaces_home.jpg',
                'Vòng tay' => 'assets/images/bracelets_home.jpg'
            ];
            $categoryName = $product['category_name'] ?? 'Nhẫn';
            $product['image_url'] = $categoryImages[$categoryName] ?? 'assets/images/products/no-image.jpg';
        }
        
        // QUAN TRỌNG: Đảm bảo price được set đúng
        if (isset($product['min_price'])) {
            $product['price'] = $product['min_price'];
        } elseif (!isset($product['price'])) {
            $product['price'] = 0;
        }
    }
    return $products;
}

    // Phương thức dự phòng: Lấy tất cả sản phẩm có ảnh
    public function getAllProductsWithImages($limit = 6) {
        $query = "
            SELECT DISTINCT
                p.pro_id,
                p.name,
                p.description,
                c.name as category_name,
                pi.image_url,
                pv.price,
                pv.variant_id,
                0 as total_sold
            FROM product p
            LEFT JOIN category c ON p.category_id = c.cate_id
            LEFT JOIN product_image pi ON p.pro_id = pi.product_id AND pi.sort_order = 0
            LEFT JOIN product_variant pv ON p.pro_id = pv.product_id
            WHERE pv.stock_quantity > 0
            ORDER BY p.created_at DESC
            LIMIT ?
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$limit]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $this->processProductImages($products);
    }
}
?>
