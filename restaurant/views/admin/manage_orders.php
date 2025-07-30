<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
<?php
$filter = $_GET['filter'] ?? 'all';

// Đọc toàn bộ kết quả vào mảng
$orders = [];
$count_unpaid = 0;
if (isset($result) && $result->rowCount() > 0) {
    $orders = $result->fetchAll(PDO::FETCH_ASSOC);
    foreach ($orders as $row) {
        if ($row['status'] != 'paid') $count_unpaid++;
    }
}
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Quản lý Đơn hàng</h3>
        <span class="badge bg-danger fs-6">Chưa thanh toán: <?php echo $count_unpaid; ?></span>
    </div>

   
    <div class="mb-4">
        <a href="index.php?action=manage_orders&filter=all" 
           class="btn btn-sm <?php if($filter=='all') echo 'btn-primary'; else echo 'btn-outline-primary'; ?> me-2">
            Tất cả
        </a>
        <a href="index.php?action=manage_orders&filter=unpaid" 
           class="btn btn-sm <?php if($filter=='unpaid') echo 'btn-danger'; else echo 'btn-outline-danger'; ?> me-2">
            Chưa thanh toán
        </a>
        <a href="index.php?action=manage_orders&filter=paid" 
           class="btn btn-sm <?php if($filter=='paid') echo 'btn-success'; else echo 'btn-outline-success'; ?>">
            Đã thanh toán
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="10%">ID</th>
                        <th width="20%">Bàn</th>
                        <th width="20%">Nhân viên</th>
                        <th width="20%">Ngày tạo</th>
                        <th width="15%">Trạng thái</th>
                        <th width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $row) : ?>
                    <?php
                        if ($filter == 'paid' && $row['status'] != 'paid') continue;
                        if ($filter == 'unpaid' && $row['status'] == 'paid') continue;
                    ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars($row['id']) ?></strong></td>
                        <td><?= htmlspecialchars($row['table_name']) ?></td>
                        <td><?= htmlspecialchars($row['staff_name']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <?php if ($row['status'] == 'paid'): ?>
                                <span class="badge bg-success">Đã thanh toán</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Chưa thanh toán</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?action=view_order&id=<?= $row['id'] ?>" 
                               class="btn btn-sm btn-outline-primary me-1">Xem</a>
                            <a href="index.php?action=delete_order&id=<?= $row['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


    <?php if (empty($orders)): ?>
    <div class="text-center py-5">
        <h5 class="text-muted">Không có đơn hàng nào</h5>
        <p class="text-muted">Chưa có đơn hàng nào được tạo trong hệ thống.</p>
    </div>
    <?php endif; ?>
</div>

<style>
.order-card {
    transition: all 0.3s ease;
    border-radius: 12px;
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.nav-pills .nav-link {
    border-radius: 8px !important;
    margin: 0 4px;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
    background-color: #f8f9fa;
}

.nav-pills .nav-link.active {
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
