<?php
class Dashboard {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Tổng doanh thu theo tháng/năm
    public function getRevenue($month = null, $year = null) {
        $sql = "SELECT COALESCE(SUM(total), 0) as total_revenue 
                FROM orders 
                WHERE status = 1";
        
        $params = [];
        
        if ($month && $year) {
            $sql .= " AND MONTH(order_date) = ? AND YEAR(order_date) = ?";
            $params[] = $month;
            $params[] = $year;
        } elseif ($year) {
            $sql .= " AND YEAR(order_date) = ?";
            $params[] = $year;
        } else {
            // Mặc định tháng hiện tại
            $sql .= " AND MONTH(order_date) = MONTH(CURRENT_DATE()) 
                     AND YEAR(order_date) = YEAR(CURRENT_DATE())";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total_revenue'] ?? 0;
    }

    // Doanh thu theo ngày trong tháng
    public function getRevenueByDay($month, $year) {
        $sql = "SELECT 
                    DAY(order_date) as day,
                    COALESCE(SUM(total), 0) as revenue
                FROM orders 
                WHERE status = 1
                AND MONTH(order_date) = ?
                AND YEAR(order_date) = ?
                GROUP BY DAY(order_date)
                ORDER BY day";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$month, $year]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Tạo mảng đầy đủ các ngày trong tháng
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $revenueData = [];
        
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $revenueData[$i] = 0;
        }
        
        foreach ($results as $row) {
            $revenueData[$row['day']] = (float)$row['revenue'];
        }
        
        return $revenueData;
    }
    
    // Doanh thu theo năm (tất cả các năm)
    public function getRevenueByYear() {
        $sql = "SELECT 
                    YEAR(order_date) as year,
                    COALESCE(SUM(total), 0) as revenue
                FROM orders 
                WHERE status = 1
                GROUP BY YEAR(order_date)
                ORDER BY year";
        
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $revenueData = [];
        foreach ($results as $row) {
            $revenueData[$row['year']] = (float)$row['revenue'];
        }
        
        return $revenueData;
    }

    // Doanh thu của một tháng cụ thể qua tất cả các năm
    public function getRevenueByMonthForSpecificMonth($month) {
        $sql = "SELECT 
                    YEAR(order_date) as year,
                    COALESCE(SUM(total), 0) as revenue
                FROM orders 
                WHERE status = 1
                AND MONTH(order_date) = ?
                GROUP BY YEAR(order_date)
                ORDER BY year(order_date)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$month]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $revenueData = [];
        foreach ($results as $row) {
            $revenueData[$row['year']] = (float)$row['revenue'];
        }
        
        return $revenueData;
    }
    
    // Tổng số đơn hàng
    public function getTotalOrders($month = null, $year = null) {
        $sql = "SELECT COUNT(*) as total_orders FROM orders WHERE status = 1";
        $params = [];
        
        if ($month && $year) {
            $sql .= " AND MONTH(order_date) = ? AND YEAR(order_date) = ?";
            $params[] = $month;
            $params[] = $year;
        } elseif ($year) {
            $sql .= " AND YEAR(order_date) = ?";
            $params[] = $year;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total_orders'] ?? 0;
    }

    public function getTotalOrdersByMonthAllYears($month) {
        $sql = "SELECT COUNT(*) as total_orders 
                FROM orders 
                WHERE MONTH(order_date) = ? 
                AND status = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$month]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total_orders'] ?? 0;
    }
    
    // Tổng số sản phẩm
    public function getTotalProducts() {
        $sql = "SELECT COUNT(*) as total_products FROM product";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total_products'] ?? 0;
    }
    
    // Tổng số khách hàng
    public function getTotalCustomers() {
        $sql = "SELECT COUNT(*) as total_customers FROM customer";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total_customers'] ?? 0;
    }
    
    // Doanh thu theo tháng cho biểu đồ
    public function getRevenueByMonth($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $sql = "SELECT 
                    MONTH(order_date) as month,
                    COALESCE(SUM(total), 0) as revenue
                FROM orders 
                WHERE status = 1
                AND YEAR(order_date) = ?
                GROUP BY MONTH(order_date)
                ORDER BY month";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$year]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Tạo mảng đầy đủ 12 tháng
        $revenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueData[$i] = 0;
        }
        
        foreach ($results as $row) {
            $revenueData[$row['month']] = (float)$row['revenue'];
        }
        
        return $revenueData;
    }
    
    // Phương thức lấy đơn hàng theo loại lọc (KHÔNG giới hạn số lượng)
    public function getRecentOrdersByFilter($filterType = '', $filterMonth = '', $filterYear = '') {
        $sql = "SELECT o.*, c.fullname as customer_name 
                FROM orders o 
                LEFT JOIN customer c ON o.customer_id = c.cus_id 
                WHERE o.status = 1";
        
        $params = [];
        
        switch($filterType) {
            case 'month':
                // Lọc theo tháng (tất cả năm)
                $sql .= " AND MONTH(o.order_date) = ?";
                $params[] = $filterMonth;
                break;
                
            case 'year':
                // Lọc theo năm
                $sql .= " AND YEAR(o.order_date) = ?";
                $params[] = $filterYear;
                break;
                
            case 'month_year':
                // Lọc theo tháng và năm
                $sql .= " AND MONTH(o.order_date) = ? AND YEAR(o.order_date) = ?";
                $params[] = $filterMonth;
                $params[] = $filterYear;
                break;
                
            default:
                // Mặc định: tháng năm hiện tại
                $sql .= " AND MONTH(o.order_date) = ? AND YEAR(o.order_date) = ?";
                $params[] = date('m');
                $params[] = date('Y');
                break;
        }
        
        $sql .= " ORDER BY o.order_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy danh sách năm có đơn hàng
    public function getOrderYears() {
        $sql = "SELECT DISTINCT YEAR(order_date) as year 
                FROM orders 
                WHERE status = 1
                ORDER BY year DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
