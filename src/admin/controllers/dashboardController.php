<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/models/dashboard.php';

class DashboardController {
    private $dashboardModel;
    
    public function __construct() {
        global $conn;
        $this->dashboardModel = new Dashboard($conn);
    }
    
    public function index() {
        // Lấy tham số lọc
        $filterMonth = $_GET['month'] ?? '';
        $filterYear = $_GET['year'] ?? '';

        $currentMonth = date('m');
        $currentYear = date('Y');

        // Nếu không có tham số, hiển thị tháng năm hiện tại
        if (empty($filterMonth) && empty($filterYear)) {
            $filterMonth = $currentMonth;
            $filterYear = $currentYear;
        }
        
        // Xác định dữ liệu biểu đồ dựa trên bộ lọc
        if (!empty($filterMonth) && !empty($filterYear)) {
            // Nếu có chọn tháng + năm: hiển thị theo ngày trong tháng
            $revenueChart = $this->dashboardModel->getRevenueByDay($filterMonth, $filterYear);
            $chartType = 'daily';
            $chartTitle = "Doanh thu tháng $filterMonth/$filterYear";
            $chartLabels = array_map(function($day) {
                return "Ngày $day";
            }, array_keys($revenueChart));
            
        } elseif (!empty($filterYear)) {
            // Nếu chỉ chọn năm: hiển thị theo tháng trong năm
            $revenueChart = $this->dashboardModel->getRevenueByMonth($filterYear);
            $chartType = 'monthly';
            $chartTitle = "Doanh thu năm $filterYear";
            $chartLabels = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 
                        'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
            
        } else {
            // Nếu không chọn gì: hiển thị tháng năm hiện tại
            $revenueChart = $this->dashboardModel->getRevenueByDay($currentMonth, $currentYear);
            $chartType = 'daily';
            $chartTitle = "Doanh thu tháng $currentMonth/$currentYear";
            $chartLabels = array_map(function($day) {
                return "Ngày $day";
            }, array_keys($revenueChart));
        }
        
        // Lấy dữ liệu thống kê
        $data = [
            'revenue' => $this->dashboardModel->getRevenue($filterMonth, $filterYear),
            'totalOrders' => $this->dashboardModel->getTotalOrders($filterMonth, $filterYear),
            'totalProducts' => $this->dashboardModel->getTotalProducts(),
            'totalCustomers' => $this->dashboardModel->getTotalCustomers(),
            'revenueChart' => $revenueChart,
            'recentOrders' => $this->dashboardModel->getRecentOrders(10, $filterMonth, $filterYear),
            'years' => $this->dashboardModel->getOrderYears(),
            'filterMonth' => $filterMonth,
            'filterYear' => $filterYear,
            'chartType' => $chartType,
            'chartTitle' => $chartTitle,
            'chartLabels' => $chartLabels
        ];
        
        // Extract data to variables
        extract($data);
        
        // Hiển thị view
        require_once __DIR__ . '/../views/dashboard.php';
    }
}

// Xử lý request
$controller = new DashboardController();
$controller->index();
?>