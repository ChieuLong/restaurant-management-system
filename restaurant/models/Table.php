<?php
class Table {
    private $conn;
    private $table_name = "tables";

    public $id;
    public $name;
    public $area_id;   
    public $area_name; 
    public $status;   

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả bàn + tên khu vực
    public function getAll() {
        $query = "SELECT t.*, a.name AS area_name
                  FROM " . $this->table_name . " t
                  LEFT JOIN areas a ON t.area_id = a.id
                  ORDER BY t.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy tất cả bàn kèm trạng thái và đơn hàng đang xử lý
    public function getAllWithOrderInfo() {
        $query = "SELECT 
                    t.id, 
                    t.name, 
                    t.status,
                    a.name AS area_name,
                    o.id AS order_id
                  FROM " . $this->table_name . " t
                  LEFT JOIN areas a ON t.area_id = a.id
                  LEFT JOIN (
                    SELECT id, table_id FROM orders WHERE status = 'processing'
                  ) o ON t.id = o.table_id
                  ORDER BY a.name, t.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy bàn còn trống
    public function getAvailable() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'available'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Đếm tổng số bàn
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Đếm bàn còn trống
    public function countAvailable() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " t
                  WHERE NOT EXISTS (
                      SELECT 1 FROM orders o 
                      WHERE o.table_id = t.id AND o.status = 'processing'
                  )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Thêm bàn mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET name = :name, area_id = :area_id, status = 'available'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":area_id", $this->area_id);

        return $stmt->execute();
    }

    // Lấy 1 bàn
    public function getOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->name = $row['name'];
            $this->area_id = $row['area_id'];
            $this->status = $row['status'];
        }
    }

    // Cập nhật bàn
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET name = :name, area_id = :area_id, status = :status
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":area_id", $this->area_id);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Cập nhật trạng thái bàn
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Xoá bàn
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
}
?>
