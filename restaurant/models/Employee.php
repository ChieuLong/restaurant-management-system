<?php
class Employee {
    private $conn;
    private $table_name = "employees";

    public $id;
    public $name;
    public $phone;
    public $email;
    public $position;
    public $status;
    public $hire_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả nhân viên
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy tất cả nhân viên staff (không bao gồm admin)
    public function getAllStaff() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE position = 'staff' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy nhân viên theo ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->phone = $row['phone'];
            $this->email = $row['email'];
            $this->position = $row['position'];
            $this->status = $row['status'];
            $this->hire_date = $row['hire_date'];
            return true;
        }
        return false;
    }

    // Tạo nhân viên mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET name = :name, phone = :phone, email = :email, 
                      position = :position, hire_date = :hire_date, status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":hire_date", $this->hire_date);

        if ($stmt->execute()) {
            $employee_id = $this->conn->lastInsertId();
            
            // Tự động tạo tài khoản đăng nhập
            $this->createUserAccount($employee_id);
            
            return $employee_id;
        }
        return false;
    }

    // Cập nhật thông tin nhân viên
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET name = :name, phone = :phone, email = :email, hire_date = :hire_date
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":hire_date", $this->hire_date);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            // Cập nhật username nếu phone thay đổi
            $this->updateUserAccount();
            return true;
        }
        return false;
    }

    // Vô hiệu hóa nhân viên
    public function deactivate() {
        $query = "UPDATE " . $this->table_name . " SET status = 'inactive' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) {
            // Vô hiệu hóa tài khoản đăng nhập
            $this->deactivateUserAccount();
            return true;
        }
        return false;
    }

    // Tạo tài khoản đăng nhập
    private function createUserAccount($employee_id) {
        $query = "INSERT INTO users (username, password, role, employee_id) 
                  VALUES (:username, :password, :role, :employee_id)";
        
        $stmt = $this->conn->prepare($query);
        
        // Username = phone, Password = phone (có thể đổi sau)
        $username = $this->phone;
        $password = password_hash($this->phone, PASSWORD_DEFAULT);
        $role = $this->position;
        
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":employee_id", $employee_id);
        
        return $stmt->execute();
    }

    // Cập nhật tài khoản đăng nhập
    private function updateUserAccount() {
        $query = "UPDATE users SET username = :username WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $this->phone);
        $stmt->bindParam(":employee_id", $this->id);
        return $stmt->execute();
    }

    // Vô hiệu hóa tài khoản đăng nhập
    private function deactivateUserAccount() {
        $query = "UPDATE users SET status = 'inactive' WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":employee_id", $this->id);
        return $stmt->execute();
    }

    // Reset mật khẩu nhân viên
    public function resetPassword() {
        $query = "UPDATE users SET password = :password WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        
        // Reset về mật khẩu mặc định (SĐT)
        $new_password = password_hash($this->phone, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":password", $new_password);
        $stmt->bindParam(":employee_id", $this->id);
        
        return $stmt->execute();
    }

    // Lấy thông tin tài khoản
    public function getUserInfo($employee_id = null) {
        $id = $employee_id ?? $this->id;
        $query = "SELECT u.username, u.status as user_status, e.* 
                  FROM users u 
                  JOIN employees e ON u.employee_id = e.id 
                  WHERE e.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thống kê nhân viên
    public function countStaff() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE position = 'staff'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function countActiveStaff() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE position = 'staff' AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function countNewStaffThisMonth() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE position = 'staff' AND MONTH(created_at) = MONTH(CURDATE()) 
                  AND YEAR(created_at) = YEAR(CURDATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?> 