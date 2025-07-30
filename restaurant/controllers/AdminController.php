<?php
class AdminController {
    public function dashboard() {
        // Chỉ admin mới vào được
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
            header("Location: index.php");
            exit;
        }
        $content = 'views/admin/dashboard.php';
        include 'views/admin/layout_admin.php';
    }
}
?>
