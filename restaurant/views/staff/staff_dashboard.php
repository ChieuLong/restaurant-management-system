<?php
include_once 'models/Table.php';
include_once 'models/Order.php';

$table_model = new Table($db);
$order_model = new Order($db);

// Lấy dữ liệu cho thẻ thống kê
$available_tables = $table_model->countAvailable();
$processing_orders = $order_model->countProcessingOrders();

// Lấy dữ liệu cho sơ đồ bàn
$tables_with_status = $table_model->getAllWithOrderInfo();
?>
<style>
    .table-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .table-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .table-card-header {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .status-badge {
        font-size: 0.9rem;
        padding: 0.5em 0.8em;
    }
    .stat-card {
        border-left: 5px solid;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
    }
    .border-success { border-left-color: #198754 !important; }
    .border-warning { border-left-color: #ffc107 !important; }
</style>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card bg-success text-white shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="card-title text-uppercase mb-2">Bàn Trống</h6>
                            <span class="h2 fw-bold mb-0"><?php echo $available_tables; ?></span>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card bg-warning text-white shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="card-title text-uppercase mb-2">Đơn Chưa Thanh Toán</h6>
                            <span class="h2 fw-bold mb-0"><?php echo $processing_orders; ?></span>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="row mb-4">
        <div class="col-12">
            <h3 class="text-secondary mb-3">
                <i class="fas fa-th-large"></i> Sơ Đồ Bàn
            </h3>
        </div>
    </div>

    <div class="row">
        <?php 
        if ($tables_with_status->rowCount() > 0):
            while ($table = $tables_with_status->fetch(PDO::FETCH_ASSOC)):
                $is_occupied = !empty($table['order_id']);
        ?>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card table-card h-100 border-2 <?php echo $is_occupied ? 'border-danger' : 'border-success'; ?>">
                    <div class="card-body text-center d-flex flex-column justify-content-between">
                        <div>
                            <i class="fas fa-chair fa-3x mb-3 <?php echo $is_occupied ? 'text-danger' : 'text-success'; ?>"></i>
                            <h5 class="card-title table-card-header"><?php echo htmlspecialchars($table['name']); ?></h5>
                            <p class="text-muted mb-3"><?php echo htmlspecialchars($table['area_name']); ?></p>
                            <?php if ($is_occupied): ?>
                                <span class="badge bg-danger status-badge"><i class="fas fa-utensils"></i> Đang hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-success status-badge"><i class="fas fa-check-circle"></i> Trống</span>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4">
                            <?php if ($is_occupied): ?>
                                <a href="index.php?action=view_order_detail_staff&id=<?php echo $table['order_id']; ?>" 
                                   class="btn btn-danger w-100">
                                   <i class="fas fa-eye"></i> Xem Đơn Hàng
                                </a>
                            <?php else: ?>
                                <a href="index.php?action=add_order_staff&table_id=<?php echo $table['id']; ?>" 
                                   class="btn btn-success w-100">
                                   <i class="fas fa-plus-circle"></i> Gọi Món
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            endwhile;
        else: 
        ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i> Chưa có bàn nào được cấu hình trong hệ thống. Vui lòng thêm bàn ở trang quản trị.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
