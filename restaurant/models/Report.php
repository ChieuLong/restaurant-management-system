<?php
class Report {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tổng số đơn hàng
    public function getTotalOrders($from, $to) {
        $query = "SELECT COUNT(*) as total_orders FROM orders WHERE created_at BETWEEN :from AND :to";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':from', $from);
        $stmt->bindParam(':to', $to);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_orders'] ?? 0;
    }

    // Tổng số món đã bán
    public function getTotalItemsSold($from, $to) {
        $query = "SELECT SUM(order_items.quantity) as total_items 
                  FROM order_items 
                  JOIN orders ON order_items.order_id = orders.id
                  WHERE orders.created_at BETWEEN :from AND :to";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':from', $from);
        $stmt->bindParam(':to', $to);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_items'] ?? 0;
    }

    // Tổng doanh thu
    public function getTotalRevenue($from, $to) {
        $query = "SELECT SUM(order_items.quantity * menu_items.price) as total_revenue
                  FROM order_items
                  JOIN menu_items ON order_items.menu_item_id = menu_items.id
                  JOIN orders ON order_items.order_id = orders.id
                  WHERE orders.created_at BETWEEN :from AND :to";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':from', $from);
        $stmt->bindParam(':to', $to);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_revenue'] ?? 0;
    }

    // Top món bán chạy
    public function getTopSellingItems($from, $to, $limit = 5) {
        $query = "SELECT menu_items.name, SUM(order_items.quantity) as total_sold
                  FROM order_items
                  JOIN menu_items ON order_items.menu_item_id = menu_items.id
                  JOIN orders ON order_items.order_id = orders.id
                  WHERE orders.created_at BETWEEN :from AND :to
                  GROUP BY menu_items.id
                  ORDER BY total_sold DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':from', $from);
        $stmt->bindParam(':to', $to);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Doanh thu theo ngày (timeline)
    public function getRevenueByDateRange($from, $to) {
        $query = "SELECT DATE(orders.created_at) as order_date, 
                         SUM(order_items.quantity * menu_items.price) as daily_revenue
                  FROM order_items
                  JOIN menu_items ON order_items.menu_item_id = menu_items.id
                  JOIN orders ON order_items.order_id = orders.id
                  WHERE orders.created_at BETWEEN :from AND :to
                  GROUP BY DATE(orders.created_at)
                  ORDER BY order_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':from', $from);
        $stmt->bindParam(':to', $to);
        $stmt->execute();
        return $stmt;
    }
}
?>
