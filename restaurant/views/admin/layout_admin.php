<!-- views/admin/layout_admin.php -->
<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { min-height: 100vh; display: flex; flex-direction: column; }
    .sidebar { height: 100vh; background: #343a40; color: #fff; padding-top: 2rem; }
    .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 12px 20px; }
    .sidebar a:hover { background: #495057; color: #fff; }
    .content { flex: 1; padding: 2rem; }
    footer { background: #f8f9fa; text-align: center; padding: 1rem; }
    .logo { font-size: 1.5rem; font-weight: bold; color: #fff; text-align: center; margin-bottom: 2rem; }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
        <div class="logo">🍽️ Restaurant</div>
            <a href="index.php?action=dashboard">🏠 Dashboard</a>
    <a href="index.php?action=menu">🍜 Quản lý Món ăn</a>
    <a href="index.php?action=tables">🪑 Quản lý Bàn ăn</a>
    <a href="index.php?action=orders">🧾 Quản lý Đơn hàng</a>
    <a href="index.php?action=manage_employees">👥 Quản lý Nhân viên</a>
    <a href="index.php?action=reports">📊 Báo cáo Doanh thu</a>
    <a href="index.php?action=logout">🚪 Đăng xuất</a>
      </nav>

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
        <?php include $view ?? $content; ?>
      </main>
    </div>
  </div>

  <footer>&copy; 2025 Restaurant. All rights reserved.</footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
