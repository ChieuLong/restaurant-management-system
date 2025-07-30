<?php
include_once 'models/Order.php';
$order_model = new Order($db);
$order = $order_model->getById($_GET['id']);
?>
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <h2 class="text-primary mb-0">
          <i class="fas fa-receipt"></i> Chi Tiết Đơn Hàng #<?php echo $_GET['id']; ?>
        </h2>
        <div>
          <?php if ($order['status'] == 'processing'): ?>
            <a href="index.php?action=edit_order_staff&id=<?php echo $_GET['id']; ?>" class="btn btn-warning btn-staff me-2">
              <i class="fas fa-edit"></i> Sửa Đơn
            </a>
          <?php endif; ?>
          <a href="index.php?action=view_orders_staff" class="btn btn-outline-secondary btn-staff">
            <i class="fas fa-arrow-left"></i> Quay lại
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="card card-staff">
        <div class="card-header bg-light">
          <h5 class="mb-0"><i class="fas fa-utensils"></i> Danh sách món đã đặt</h5>
        </div>
        <div class="card-body">
          <?php 
          include_once 'models/OrderItem.php';
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
      <div class="card card-staff">
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
                <span class="badge bg-danger fs-6"></i> Chưa thanh toán</span>
              <?php endif; ?>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label text-muted">Thời gian tạo:</label>
            <div class="fs-6"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></div>
          </div>
          
          <div class="mb-3">
            <label class="form-label text-muted">Staff phụ trách:</label>
            <div class="fs-6"><?php echo $_SESSION['username'] ?? ''; ?></div>
          </div>

          <div class="mb-3">
            <label class="form-label text-muted">Ghi chú:</label>
            <div class="fs-6"><?php echo htmlspecialchars($order['note'] ?? ''); ?></div>
          </div>

          <hr>

          <div class="d-grid gap-2">
            <?php if ($order['status'] == 'processing'): ?>
              <form method="POST" action="index.php?action=pay_order_staff" class="d-grid">
                <input type="hidden" name="order_id" value="<?php echo $_GET['id']; ?>">
                <button type="submit" class="btn btn-success btn-lg btn-staff">
                  <i class="fas fa-check-circle"></i> Xác nhận Thanh toán
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
          
<style>
@media print {
  .staff-sidebar, .staff-header, .btn, nav {
    display: none !important;
  }
  .staff-container {
    margin: 0 !important;
    box-shadow: none !important;
  }
  .staff-content {
    padding: 0 !important;
  }
}
</style>    