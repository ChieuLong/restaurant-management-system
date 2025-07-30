<?php
class Menu {
    private $conn;
    private $table_name = "menu_items";

    public $id;
    public $name;
    public $price;
    public $description;
    public $image;
    public $category_id; 

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả món 
    public function getAllWithCategory() {
        $query = "SELECT m.*, c.name AS category_name 
                  FROM " . $this->table_name . " m
                  LEFT JOIN categories c ON m.category_id = c.id
                  ORDER BY m.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Lấy tất cả 
    public function getAll() {
          $query = "SELECT menu_items.*, categories.name AS category_name
              FROM menu_items
              LEFT JOIN categories ON menu_items.category_id = categories.id
              ORDER BY menu_items.id DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
    }

    // Lấy 1 món theo ID
    public function getOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->price = $row['price'];
            $this->description = $row['description'];
            $this->image = $row['image'];
            $this->category_id = $row['category_id'];
        }
    }

    // Tạo món
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, price=:price, description=:description, image=:image, category_id=:category_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":category_id", $this->category_id);

        return $stmt->execute();
    }

    // Cập nhật món
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET name=:name, price=:price, description=:description, category_id=:category_id";

        if ($this->image) {
            $query .= ", image=:image";
        }

        $query .= " WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":category_id", $this->category_id);

        if ($this->image) {
            $stmt->bindParam(":image", $this->image);
        }

        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Xóa món
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
}
?>
