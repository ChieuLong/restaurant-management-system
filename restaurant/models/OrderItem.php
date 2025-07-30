<?php
class OrderItem {
    private $conn;
    private $table_name = "order_items";

    public $id;
    public $order_id;
    public $menu_item_id;
    public $menu_item_name; 
    public $quantity;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET order_id=:order_id, 
                      menu_item_id=:menu_item_id, 
                      menu_item_name=:menu_item_name, 
                      quantity=:quantity";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":order_id", $this->order_id);
        $stmt->bindParam(":menu_item_id", $this->menu_item_id);
        $stmt->bindParam(":menu_item_name", $this->menu_item_name); 
        $stmt->bindParam(":quantity", $this->quantity);

        return $stmt->execute();
    }

    public function getByOrderId($order_id) {
        $query = "SELECT oi.*, m.price 
              FROM " . $this->table_name . " oi
              JOIN menu_items m ON oi.menu_item_id = m.id
              WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $order_id);
        $stmt->execute();
        return $stmt;
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
