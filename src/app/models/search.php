<?php
class SearchModel {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Tìm kiếm sản phẩm theo từ khóa
     * Tìm theo: tên sản phẩm, mô tả, tags, tên danh mục
     */
    public function searchProducts($keyword, $sort = 'newest') {
        if (empty($keyword)) {
            return [];
        }
        
        $searchTerm = $this->removeAccents($keyword);
        $searchTerm = "%" . $searchTerm . "%";
        
        // Base SQL query - sửa tên cột chính xác theo database của bạn
        $sql = "SELECT DISTINCT 
                    p.pro_id,
                    p.name,
                    p.description,
                    p.category_id,
                    p.status,
                    p.created_at,
                    c.name as category_name,
                    (SELECT MIN(pv.price) FROM product_variant pv WHERE pv.product_id = p.pro_id) as min_price,
                    (SELECT COUNT(DISTINCT pv.size) FROM product_variant pv WHERE pv.product_id = p.pro_id) > 0 as has_size,
                    (SELECT pi.image_url FROM product_image pi WHERE pi.product_id = p.pro_id AND pi.sort_order = 0 LIMIT 1) as image_url
                FROM product p
                LEFT JOIN category c ON p.category_id = c.cate_id
                LEFT JOIN product_tags pt ON p.pro_id = pt.product_id
                WHERE p.status = 1 
                AND (
                    p.name LIKE ? 
                    OR p.description LIKE ? 
                    OR c.name LIKE ?
                    OR pt.tag_name LIKE ?
                )";
        
        // Thêm điều kiện sắp xếp
        switch ($sort) {
            case 'price_low':
                $sql .= " ORDER BY min_price ASC";
                break;
            case 'price_high':
                $sql .= " ORDER BY min_price DESC";
                break;
            case 'name':
                $sql .= " ORDER BY p.name ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY p.created_at DESC";
                break;
        }
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Search query error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Hàm loại bỏ dấu tiếng Việt (tìm kiếm không dấu)
     */
    private function removeAccents($str) {
        if (empty($str)) return '';
        
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        return $str;
    }
}
?>