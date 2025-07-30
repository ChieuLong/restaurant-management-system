<?php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Staff Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    body { min-height: 100vh; display: flex; flex-direction: column; background: #f7f7f7; }
    .main-header { 
      background: #343a40; 
      color: #fff; 
      padding: 0.75rem 1.5rem; 
      display: flex; 
      align-items: center; 
      justify-content: space-between;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .main-header .logo { font-size: 1.6rem; font-weight: bold; letter-spacing: 1px; }
    .main-header .nav { display: flex; gap: 1.5rem; align-items: center; }
    .main-header .nav a { color: #fff; text-decoration: none; font-weight: 500; transition: color 0.2s; }
    .main-header .nav a:hover { color: #ffd699; }
    .main-header .user { font-size: 1rem; margin-left: 1.5rem; }
    .content { 
      flex: 1; 
      padding: 2rem 1rem; 
      margin-top: 80px; 
    }
    footer { background: #f8f9fa; text-align: center; padding: 1rem; }
    @media (max-width: 768px) {
      .main-header { 
        flex-direction: column; 
        align-items: flex-start; 
        gap: 0.5rem; 
        padding: 0.5rem 1rem;
      }
      .content { 
        padding: 1rem 0.2rem; 
        margin-top: 120px; 
      }
    }
  </style>
</head>
<body>
  <header class="main-header">
    <div class="logo"><i class="fa-solid fa-utensils"></i> Restaurant Staff</div>
    <nav class="nav">
      <a href="index.php?action=staff_dashboard"><i class="fa-solid fa-house"></i> Dashboard</a>
      <a href="index.php?action=add_order_staff"><i class="fa-solid fa-plus"></i> Gọi món mới</a>
      <a href="index.php?action=view_orders_staff"><i class="fa-solid fa-receipt"></i> Đơn hàng của tôi</a>
      <a href="index.php?action=logout"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
      <span class="user"><i class="fa-solid fa-user"></i> <?php echo $_SESSION['username'] ?? 'Staff'; ?></span>
    </nav>
  </header>
  <main class="content">
    <?php
    if (isset($view) && file_exists($view)) {
      include $view;
    } else {
      echo "<p>Không tìm thấy view.</p>";
    }
    ?>
  </main>
  <footer>&copy; <?php echo date('Y'); ?> Restaurant. All rights reserved.</footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
