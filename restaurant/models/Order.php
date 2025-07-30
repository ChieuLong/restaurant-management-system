<?php
class Order {
    private $conn;
    private $table_name = "orders";

    public $id;
    public $table_id;
    public $staff_id;
    public $status;
    public $created_at;
    public $note; 

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả đơn
    public function getAll() {
        $query = "SELECT o.*, t.name as table_name, u.username as staff_name, e.name as employee_name
                  FROM " . $this->table_name . " o
                  JOIN tables t ON o.table_id = t.id
                  JOIN users u ON o.staff_id = u.id
                  LEFT JOIN employees e ON u.employee_id = e.id
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getByStaff($staff_id) {
        $query = "SELECT o.*, t.name as table_name 
                  FROM " . $this->table_name . " o
                  JOIN tables t ON o.table_id = t.id
                  WHERE o.staff_id = :staff_id
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":staff_id", $staff_id);
        $stmt->execute();
        return $stmt;
    }

    //  Lấy chi tiết đơn
    public function getById($id) {
        $query = "SELECT o.*, t.name as table_name, u.username as staff_name, e.name as employee_name
                  FROM " . $this->table_name . " o
                  JOIN tables t ON o.table_id = t.id
                  JOIN users u ON o.staff_id = u.id
                  LEFT JOIN employees e ON u.employee_id = e.id
                  WHERE o.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //  Lấy món của đơn
    public function getItems($order_id) {
        $query = "SELECT oi.*, m.name as menu_name, m.price 
                  FROM order_items oi
                  JOIN menu_items m ON oi.menu_item_id = m.id
                  WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $order_id);
        $stmt->execute();
        return $stmt;
    }

    // Tạo đơn mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET table_id = :table_id, staff_id = :staff_id, status = 'processing', created_at = NOW()";
        if (isset($this->note)) {
            $query .= ", note = :note";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":table_id", $this->table_id);
        $stmt->bindParam(":staff_id", $this->staff_id);
        if (isset($this->note)) {
            $stmt->bindParam(":note", $this->note);
        }
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Đếm đơn đang xử lý của staff
    public function countProcessingByStaff($staff_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . "
                  WHERE staff_id = :staff_id AND status = 'processing'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":staff_id", $staff_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Đếm tất cả đơn đang xử lý
    public function countProcessingOrders() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'processing'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Xoá đơn
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
}
?>
