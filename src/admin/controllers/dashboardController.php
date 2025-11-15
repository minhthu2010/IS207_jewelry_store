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
        $filterType = $_GET['filter_type'] ?? '';
        $filterMonth = $_GET['month'] ?? '';
        $filterYear = $_GET['year'] ?? '';

        $currentMonth = date('m');
        $currentYear = date('Y');

        // Xác định dữ liệu thống kê theo bộ lọc
        $revenue = 0;
        $totalOrders = 0;
        $statisticsTitle = '';

        switch($filterType) {
            case 'month':
                // Lọc theo tháng (tất cả các năm)
                $revenueData = $this->dashboardModel->getRevenueByMonthForSpecificMonth($filterMonth);
                $revenue = array_sum($revenueData); 
                $totalOrders = $this->dashboardModel->getTotalOrdersByMonthAllYears($filterMonth);
                $revenueChart = $revenueData;
                $chartType = 'yearly_by_month';
                $chartTitle = "Doanh thu tháng $filterMonth qua các năm";
                $chartLabels = array_map(function($year) {
                    return "Năm $year";
                }, array_keys($revenueChart));
                $recentOrdersTitle = "Đơn hàng gần đây - Tháng $filterMonth (Tất cả năm)";
                $statisticsTitle = "Tháng $filterMonth (Tất cả năm)";
                break;
                
            case 'year':
                // Lọc theo năm
                $revenue = $this->dashboardModel->getRevenue('', $filterYear);
                $totalOrders = $this->dashboardModel->getTotalOrders('', $filterYear);
                $revenueChart = $this->dashboardModel->getRevenueByMonth($filterYear);
                $chartType = 'monthly';
                $chartTitle = "Doanh thu năm $filterYear";
                $chartLabels = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 
                            'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
                $recentOrdersTitle = "Đơn hàng gần đây - Năm $filterYear";
                $statisticsTitle = "Năm $filterYear";
                break;
                
            case 'month_year':
                // Lọc theo tháng và năm
                $revenue = $this->dashboardModel->getRevenue($filterMonth, $filterYear);
                $totalOrders = $this->dashboardModel->getTotalOrders($filterMonth, $filterYear);
                $revenueChart = $this->dashboardModel->getRevenueByDay($filterMonth, $filterYear);
                $chartType = 'daily';
                $chartTitle = "Doanh thu tháng $filterMonth/$filterYear";
                $chartLabels = array_map(function($day) {
                    return "Ngày $day";
                }, array_keys($revenueChart));
                $recentOrdersTitle = "Đơn hàng gần đây - Tháng $filterMonth/$filterYear";
                $statisticsTitle = "Tháng $filterMonth/$filterYear";
                break;
                
            default:
                // Mặc định: tháng năm hiện tại
                $filterMonth = $currentMonth;
                $filterYear = $currentYear;
                $revenue = $this->dashboardModel->getRevenue($currentMonth, $currentYear);
                $totalOrders = $this->dashboardModel->getTotalOrders($currentMonth, $currentYear);
                $revenueChart = $this->dashboardModel->getRevenueByDay($currentMonth, $currentYear);
                $chartType = 'daily';
                $chartTitle = "Doanh thu tháng $currentMonth/$currentYear";
                $chartLabels = array_map(function($day) {
                    return "Ngày $day";
                }, array_keys($revenueChart));
                $recentOrdersTitle = "Đơn hàng gần đây - Tháng $currentMonth/$currentYear";
                $statisticsTitle = "Tháng $currentMonth/$currentYear";
                break;
        }
        
        // Lấy đơn hàng gần đây theo bộ lọc
        $recentOrders = $this->dashboardModel->getRecentOrdersByFilter(10, $filterType, $filterMonth, $filterYear);
        
        // Lấy dữ liệu thống kê
        $data = [
            'revenue' => $revenue,
            'totalOrders' => $totalOrders,
            'totalProducts' => $this->dashboardModel->getTotalProducts(),
            'totalCustomers' => $this->dashboardModel->getTotalCustomers(),
            'revenueChart' => $revenueChart,
            'recentOrders' => $recentOrders,
            'years' => $this->dashboardModel->getOrderYears(),
            'filterMonth' => $filterMonth,
            'filterYear' => $filterYear,
            'filterType' => $filterType,
            'chartType' => $chartType,
            'chartTitle' => $chartTitle,
            'chartLabels' => $chartLabels,
            'recentOrdersTitle' => $recentOrdersTitle,
            'statisticsTitle' => $statisticsTitle
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
