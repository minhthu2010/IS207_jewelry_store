<?php
require_once __DIR__ . '/../models/home.php';

class HomeController {
    private $model;

    public function __construct($db) {
        $this->model = new HomeModel($db);
    }

    public function index() {
    try {
        $categories = $this->model->getAllCategories();
        $trendingProducts = $this->model->getTrendingProducts(6);
        
        // DEBUG CHI TIẾT SẢN PHẨM TRENDING
        echo "<!-- DEBUG TRENDING PRODUCTS WITH SALES -->";
        foreach ($trendingProducts as $index => $product) {
            echo "<!-- Product {$index}: {$product['name']} | Sold: {$product['total_sold']} | Price: {$product['min_price']} - {$product['max_price']} -->";
        }
        
        return [
            'categories' => $categories,
            'trendingProducts' => $trendingProducts
        ];
        
    } catch (Exception $e) {
        echo "<!-- ERROR: " . $e->getMessage() . " -->";
        return $this->getFallbackData();
    }
}

    private function getFallbackData() {
        // Dữ liệu mẫu khi có lỗi - SỬA ĐƯỜNG DẪN ẢNH
        return [
            'categories' => [
                ['cate_id' => 1, 'name' => 'Nhẫn'],
                ['cate_id' => 2, 'name' => 'Bông tai'],
                ['cate_id' => 3, 'name' => 'Dây chuyền'],
                ['cate_id' => 4, 'name' => 'Vòng tay']
            ],
            'trendingProducts' => [
                [
                    'name' => 'Nhẫn Kim Cương Nữ Crown',
                    'price' => '18500000',
                    'image_url' => 'assets/images/rings_home.jpg',
                    'total_sold' => 2
                ],
                [
                    'name' => 'Dây Chuyền Ngọc Trai Classic', 
                    'price' => '12500000',
                    'image_url' => 'assets/images/necklaces_home.jpg',
                    'total_sold' => 1
                ],
                [
                    'name' => 'Bông Tai Vàng 24K Giọt Nước',
                    'price' => '8900000',
                    'image_url' => 'assets/images/earrings_home.jpg',
                    'total_sold' => 0
                ],
                [
                    'name' => 'Vòng Tay Kim Cương Infinity',
                    'price' => '15900000',
                    'image_url' => 'assets/images/bracelets_home.jpg',
                    'total_sold' => 0
                ]
            ]
        ];
    }
}
?>
