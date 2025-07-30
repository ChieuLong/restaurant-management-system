<?php
include_once 'config/database.php';
include_once 'models/Menu.php';
include_once 'models/Order.php';
include_once 'models/OrderItem.php';

// Khởi tạo model
$menu_model = new Menu($db);
$order_model = new Order($db);
$order_item_model = new OrderItem($db);

// Xử lý thêm món vào giỏ hàng
if (isset($_POST['add_to_cart'])) {
    $menu_id = $_POST['menu_id'];
    $menu_name = $_POST['menu_name'];
    $menu_price = $_POST['menu_price'];
    $menu_image = $_POST['menu_image'];
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$menu_id])) {
        $_SESSION['cart'][$menu_id]['qty']++;
    } else {
        $_SESSION['cart'][$menu_id] = [
            'name' => $menu_name,
            'price' => $menu_price,
            'qty' => 1,
            'image' => $menu_image
        ];
    }
    header('Location: index.php?action=view_orders_staff');
    exit;
}

// Xử lý cập nhật/xóa món trong giỏ hàng
if (isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['qty'] = $qty;
        }
    }
    header('Location: index.php?action=view_orders_staff');
    exit;
}

// Xử lý thanh toán 
if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
   
    $staff_id = $_SESSION['user_id'] ?? 1;
    $table_id = 1; 
    $order_model->table_id = $table_id;
    $order_model->staff_id = $staff_id;
    $order_id = $order_model->create();
    if ($order_id) {
        foreach ($_SESSION['cart'] as $menu_id => $item) {
            $order_item_model->order_id = $order_id;
            $order_item_model->menu_item_id = $menu_id;
            $order_item_model->menu_item_name = $item['name'];
            $order_item_model->quantity = $item['qty'];
            $order_item_model->create();
        }
        unset($_SESSION['cart']);
        header('Location: index.php?action=view_orders_staff&success=1');
        exit;
    }
}

// Lấy danh sách món ăn
$menus = $menu_model->getAll();
$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['qty'];
}
?>
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-12">
      <h2 class="text-primary mb-3">
        <i class="fas fa-list"></i> Danh Sách Đơn Hàng
      </h2>
    </div>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle"></i> Đơn hàng đã được tạo thành công!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['payment']) && $_GET['payment'] == 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle"></i> Thanh toán đơn hàng thành công!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="card card-staff">
    <div class="card-header bg-light">
      <div class="row align-items-center">
        <div class="col">
          <h5 class="mb-0"><i class="fas fa-receipt"></i> Tất cả đơn hàng</h5>
        </div>
        <div class="col-auto">
          <a href="index.php?action=add_order_staff" class="btn btn-success btn-staff">
            <i class="fas fa-plus"></i> Tạo đơn mới
          </a>
        </div>
      </div>
    </div>
    <div class="card-body">
      <?php 
      include_once 'models/Order.php';
      $order_model = new Order($db);
      $result = $order_model->getAll();

      // Lọc và đếm
      $filter = $_GET['filter'] ?? 'all';
      $orders = [];
      $count_unpaid = 0;
      if ($result->rowCount() > 0) {
          $orders = $result->fetchAll(PDO::FETCH_ASSOC);
          foreach ($orders as $row) {
              if ($row['status'] != 'paid') $count_unpaid++;
          }
      }
      ?>
      <div class="mb-3 d-flex align-items-center justify-content-between">
        <div>
          <a href="index.php?action=view_orders_staff&filter=all" class="btn btn-outline-primary btn-sm <?php if($filter=='all') echo 'active'; ?>">Tất cả</a>
          <a href="index.php?action=view_orders_staff&filter=unpaid" class="btn btn-outline-danger btn-sm <?php if($filter=='unpaid') echo 'active'; ?>">Chưa thanh toán</a>
          <a href="index.php?action=view_orders_staff&filter=paid" class="btn btn-outline-success btn-sm <?php if($filter=='paid') echo 'active'; ?>">Đã thanh toán</a>
        </div>
        <div>
          <span class="badge bg-danger fs-6">Chưa thanh toán: <?php echo $count_unpaid; ?></span>
        </div>
      </div>
      <?php if (count($orders) > 0): ?>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>Mã đơn</th>
              <th>Bàn</th>
              <th>Staff</th>
              <th>Thời gian</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <?php
                if ($filter == 'paid' && $order['status'] != 'paid') continue;
                if ($filter == 'unpaid' && $order['status'] == 'paid') continue;
              ?>
              <tr>
                <td>
                  <span class="badge bg-primary fs-6">#<?php echo $order['id']; ?></span>
                </td>
                <td>
                  <i class="fas fa-chair text-primary"></i>
                  <?php echo $order['table_name']; ?>
                </td>
                <td>
                  <i class="fas fa-user text-info"></i>
                  <?php echo $order['staff_name']; ?>
                </td>
                <td>
                  <i class="fas fa-clock text-warning"></i>
                  <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                </td>
                <td>
                  <?php if ($order['status'] == 'paid'): ?>
                    <span class="badge bg-success"><i class="fas fa-check"></i> Đã thanh toán</span>
                  <?php else: ?>
                    <span class="badge bg-danger"><i class="fas fa-times"></i> Chưa thanh toán</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="index.php?action=view_order_detail_staff&id=<?php echo $order['id']; ?>" 
                       class="btn btn-sm btn-outline-info" 
                       title="Xem chi tiết">
                      Xem
                    </a>
                    <?php if ($order['status'] == 'processing'): ?>
                      <form method="POST" action="index.php?action=pay_order_staff" class="d-inline">
                          <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                          <button type="submit" class="btn btn-sm btn-outline-success" title="Thanh toán">
                              Thanh toán
                          </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
        <div class="text-center py-5">
          <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
          <h5 class="text-muted">Chưa có đơn hàng nào</h5>
          <p class="text-muted">Bắt đầu tạo đơn hàng đầu tiên!</p>
          <a href="index.php?action=add_order_staff" class="btn btn-primary btn-staff">
            <i class="fas fa-plus"></i> Tạo đơn hàng mới
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div> 
