<?php
include_once 'models/Order.php';
include_once 'models/OrderItem.php';

$order_model = new Order($db);
$order = $order_model->getById($_GET['id']);

$orderItem_model = new OrderItem($db);
$items = $orderItem_model->getByOrderId($_GET['id']);

if (!$order || $order['status'] != 'processing') {
    header("Location: index.php?action=view_orders_staff");
    exit;
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="text-primary mb-0">
                    <i class="fas fa-edit"></i> Chỉnh Sửa Đơn Hàng #<?php echo htmlspecialchars($_GET['id']); ?>
                </h2>
                <div>
                    <a href="index.php?action=view_order_detail_staff&id=<?php echo htmlspecialchars($_GET['id']); ?>" class="btn btn-outline-secondary btn-staff">
                        <i class="fas fa-check"></i> Hoàn tất
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Cập nhật trạng thái món thành công!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card card-staff">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-utensils"></i> Danh sách món</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tên món</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $items->fetch(PDO::FETCH_ASSOC)): 
                            $is_cancelled = isset($item['status']) && $item['status'] == 'cancelled';
                        ?>
                            <tr class="<?php echo $is_cancelled ? 'table-secondary' : ''; ?>">
                                <td>
                                    <?php if ($is_cancelled): ?>
                                        <del class="text-muted"><?php echo htmlspecialchars($item['menu_item_name']); ?></del>
                                    <?php else: ?>
                                        <strong><?php echo htmlspecialchars($item['menu_item_name']); ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($item['quantity']); ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($is_cancelled): ?>
                                        <span class="badge bg-danger">Đã hủy</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Đã đặt</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($is_cancelled): ?>
                                        <form method="POST" action="index.php?action=update_item_status" class="d-inline">
                                            <input type="hidden" name="order_item_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="order_id" value="<?php echo $_GET['id']; ?>">
                                            <input type="hidden" name="new_status" value="active">
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-undo"></i> Đặt lại
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="index.php?action=update_item_status" class="d-inline">
                                            <input type="hidden" name="order_item_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="order_id" value="<?php echo $_GET['id']; ?>">
                                            <input type="hidden" name="new_status" value="cancelled">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-times"></i> Hủy món
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 