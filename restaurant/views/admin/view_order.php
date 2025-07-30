<?php
include_once 'models/Order.php';
include_once 'models/OrderItem.php';
include_once 'models/Table.php';
include_once 'models/User.php';

$order_model = new Order($db);
$order = $order_model->getById($_GET['id']);

// Lấy thông tin bàn và khu vực
$table_model = new Table($db);
$table_model->id = $order['table_id'];
$table_model->getOne();

// Lấy thông tin nhân viên
$user_model = new User($db);
$staff_name = '';
if ($order['staff_id']) {
    $query = "SELECT username FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $order['staff_id']);
    $stmt->execute();
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
    $staff_name = $staff['username'] ?? '';
}

// Lấy tên khu vực
$area_name = '';
if ($table_model->area_id) {
    $query = "SELECT name FROM areas WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $table_model->area_id);
    $stmt->execute();
    $area = $stmt->fetch(PDO::FETCH_ASSOC);
    $area_name = $area['name'] ?? '';
}
?>
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <h2 class="text-primary mb-0">
          <i class="fas fa-receipt"></i> Chi Tiết Đơn Hàng #<?php echo $_GET['id']; ?>
        </h2>
        <div>
          <a href="index.php?action=orders" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header bg-light">
          <h5 class="mb-0"><i class="fas fa-utensils"></i> Danh sách món đã đặt</h5>
        </div>
        <div class="card-body">
          <?php 
          $orderItem_model = new OrderItem($db);
          $items = $orderItem_model->getByOrderId($_GET['id']);
          
          if ($items->rowCount() > 0): 
          ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>STT</th>
                    <th>Tên món</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-end">Thành tiền</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $total = 0;
                  $stt = 1;
                  while ($item = $items->fetch(PDO::FETCH_ASSOC)): 
                    $is_cancelled = isset($item['status']) && $item['status'] == 'cancelled';
                    $itemPrice = $is_cancelled ? 0 : $item['price'];
                    $itemTotal = $item['quantity'] * $itemPrice;
                    $total += $itemTotal;
                  ?>
                    <tr class="<?php echo $is_cancelled ? 'table-secondary text-muted' : ''; ?>">
                      <td><?php echo $stt++; ?></td>
                      <td>
                        <?php if ($is_cancelled): ?>
                            <del><strong><?php echo htmlspecialchars($item['menu_item_name']); ?></strong></del>
                            <span class="badge bg-danger ms-2">Đã hủy</span>
                        <?php else: ?>
                            <strong><?php echo htmlspecialchars($item['menu_item_name']); ?></strong>
                        <?php endif; ?>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($item['quantity']); ?></span>
                      </td>
                      <td class="text-end">
                        <?php if ($is_cancelled): ?>
                            <del><?php echo number_format($item['price']); ?> VNĐ</del>
                        <?php else: ?>
                            <?php echo number_format($item['price']); ?> VNĐ
                        <?php endif; ?>
                      </td>
                      <td class="text-end">
                        <?php if ($is_cancelled): ?>
                            <del><strong><?php echo number_format($item['quantity'] * $item['price']); ?> VNĐ</strong></del>
                        <?php else: ?>
                            <strong><?php echo number_format($itemTotal); ?> VNĐ</strong>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
                <tfoot class="table-light">
                  <tr>
                    <td colspan="4" class="text-end">
                      <h5 class="mb-0">Tổng cộng:</h5>
                    </td>
                    <td class="text-end">
                      <h4 class="text-primary mb-0"><?php echo number_format($total); ?> VNĐ</h4>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          <?php else: ?>
            <div class="text-center py-5">
              <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
              <h5 class="text-muted">Không tìm thấy món nào trong đơn hàng này</h5>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-light">
          <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label text-muted">Mã đơn hàng:</label>
            <div class="fs-5 fw-bold text-primary">#<?php echo $_GET['id']; ?></div>
          </div>
          
          <div class="mb-3">
            <label class="form-label text-muted">Trạng thái:</label>
            <div>
              <?php if ($order['status'] == 'paid'): ?>
                <span class="badge bg-success fs-6"><i class="fas fa-check"></i> Đã thanh toán</span>
              <?php else: ?>
                <span class="badge bg-danger fs-6"><i class="fas fa-times"></i> Chưa thanh toán</span>
              <?php endif; ?>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label text-muted">Thời gian tạo:</label>
            <div class="fs-6"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></div>
          </div>

          <div class="mb-3">
            <label class="form-label text-muted">Bàn:</label>
            <div class="fs-6">
              <strong><?php echo htmlspecialchars($table_model->name); ?></strong>
              <?php if ($area_name): ?>
                <span class="text-muted">(<?php echo htmlspecialchars($area_name); ?>)</span>
              <?php endif; ?>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label text-muted">Nhân viên phụ trách:</label>
            <div class="fs-6"><?php echo htmlspecialchars($staff_name); ?></div>
          </div>

          <?php if (!empty($order['note'])): ?>
          <div class="mb-3">
            <label class="form-label text-muted">Ghi chú:</label>
            <div class="fs-6 p-2 bg-light rounded">
              <?php echo htmlspecialchars($order['note']); ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
          
<style>
@media print {
  .btn, nav, .sidebar {
    display: none !important;
  }
  .container-fluid {
    margin: 0 !important;
    padding: 0 !important;
  }
  .card {
    border: none !important;
    box-shadow: none !important;
  }
  .card-header {
    background: #f8f9fa !important;
  }
}
</style>
