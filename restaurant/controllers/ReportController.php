<?php
include_once 'config/database.php';
include_once 'models/Report.php';

class ReportController {
    public function index() {
        $database = new Database();
        $db = $database->getConnection();

        $report = new Report($db);

    
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-t');

        
        $totalOrders = $report->getTotalOrders($from, $to);
        $totalItemsSold = $report->getTotalItemsSold($from, $to);
        $totalRevenue = $report->getTotalRevenue($from, $to);
       $topItems = $report->getTopSellingItems($from, $to, 5);

        $result = $report->getRevenueByDateRange($from, $to);

        $chartData = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $chartData[] = [
                'date' => $row['order_date'],
                'revenue' => $row['daily_revenue']
            ];
        }

        $content = 'views/admin/report.php';
        include 'views/admin/layout_admin.php';
    }
}
?>
