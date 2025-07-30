<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && password_verify($this->password, $row['password'])) {
            // Tạo statement mới với dữ liệu đã verify
            $result = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE id = :id");
            $result->bindParam(':id', $row['id']);
            $result->execute();
            return $result;
        }
        
        // Trả về statement rỗng nếu không match
        return $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE 1=0");
    }


}
?>
