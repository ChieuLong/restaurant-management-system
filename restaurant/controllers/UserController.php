<?php
// session_start(); // Đã được start ở index.php
include_once 'config/database.php';
include_once 'models/User.php';

class UserController
{
    public function login()
    {
        $database = new Database();
        $db = $database->getConnection();

        $user = new User($db);
        $user->username = $_POST['username'];
        $user->password = $_POST['password'];

        $stmt = $user->login();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username'];
            if ($row['role'] == 'admin') {
                header("Location: index.php?action=dashboard");
            } else {
                header("Location: index.php?action=staff_dashboard");
            }
        } else {
            echo "Sai thông tin đăng nhập.";
        }
    }



    public function logout()
    {
        session_destroy();
        header("Location: index.php");
    }
}
