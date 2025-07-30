<?php
// Lấy loại báo cáo và tham số lọc
$report_type = $_GET['type'] ?? 'revenue';
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$category_id = $_GET['category_id'] ?? ''; 

include_once 'models/Order.php';
include_once 'models/OrderItem.php';
include_once 'models/Menu.php';
include_once 'models/Category.php';
$orderModel = new Order($db);
$orderItemModel = new OrderItem($db);
$menuModel = new Menu($db);
$categoryModel = new Category($db);

// Hàm lấy doanh thu theo thời gian
function getRevenueByPeriod($db, $from, $to) {
    $query = "SELECT DATE(o.created_at) as period, 
                     COALESCE(SUM(oi.quantity * m.price), 0) as revenue,
                     COUNT(DISTINCT o.id) as order_count
              FROM orders o
              JOIN order_items oi ON o.id = oi.order_id
              JOIN menu_items m ON oi.menu_item_id = m.id
              WHERE DATE(o.created_at) >= :from AND DATE(o.created_at) <= :to
              AND o.status = 'paid'
              GROUP BY DATE(o.created_at) 
              ORDER BY period ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute(['from' => $from, 'to' => $to]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm lấy tổng đơn hàng đã thanh toán
function getTotalPaidOrders($db, $from, $to) {
    $query = "SELECT COUNT(*) as total_paid_orders
              FROM orders 
              WHERE DATE(created_at) >= :from AND DATE(created_at) <= :to
              AND status = 'paid'";
    
    $stmt = $db->prepare($query);
    $stmt->execute(['from' => $from, 'to' => $to]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_paid_orders'] ?? 0;
}

// Hàm lấy top món ăn bán chạy với filter danh mục
function getTopSellingItems($db, $from, $to, $category_id = '', $limit = 20) {
    $query = "SELECT m.name, m.price, m.category_id, c.name as category_name,
                     SUM(oi.quantity) as total_sold,
                     SUM(oi.quantity * m.price) as total_revenue
              FROM order_items oi
              JOIN orders o ON oi.order_id = o.id
              JOIN menu_items m ON oi.menu_item_id = m.id
              LEFT JOIN categories c ON m.category_id = c.id
              WHERE DATE(o.created_at) >= :from AND DATE(o.created_at) <= :to
              AND o.status = 'paid'";
    
    if (!empty($category_id)) {
        $query .= " AND m.category_id = :category_id";
    }
    
    $query .= " GROUP BY oi.menu_item_id, m.name, m.price, m.category_id, c.name
                ORDER BY total_sold DESC 
                LIMIT :limit";
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':from', $from);
    $stmt->bindValue(':to', $to);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    
    if (!empty($category_id)) {
        $stmt->bindValue(':category_id', $category_id);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm lấy báo cáo đơn hàng theo thời gian
function getOrderReport($db, $from, $to) {
    $query = "SELECT DATE(o.created_at) as period,
                     COUNT(*) as total_orders,
                     SUM(CASE WHEN o.status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                     SUM(CASE WHEN o.status = 'paid' THEN 1 ELSE 0 END) as completed_orders,
                     SUM(CASE WHEN o.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
              FROM orders o
              WHERE DATE(o.created_at) >= :from AND DATE(o.created_at) <= :to
              GROUP BY DATE(o.created_at)
              ORDER BY period ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute(['from' => $from, 'to' => $to]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm lấy tổng quan đơn hàng
function getOrderSummary($db, $from, $to) {
    $query = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_count,
                SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count
              FROM orders 
              WHERE DATE(created_at) >= :from AND DATE(created_at) <= :to";
    
    $stmt = $db->prepare($query);
    $stmt->execute(['from' => $from, 'to' => $to]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$categories = $categoryModel->getAll();

// Lấy dữ liệu theo loại báo cáo
if ($report_type == 'revenue') {
    $revenueData = getRevenueByPeriod($db, $from, $to);
    
    $totalRevenue = array_sum(array_column($revenueData, 'revenue'));
    $totalPaidOrders = getTotalPaidOrders($db, $from, $to);
} elseif ($report_type == 'top_items') {
    $topItems = getTopSellingItems($db, $from, $to, $category_id, 20);
    
    // Tính tổng doanh thu từ top items
    $totalRevenue = array_sum(array_column($topItems, 'total_revenue'));
    $totalItemsSold = array_sum(array_column($topItems, 'total_sold'));
} else {
    $orderData = getOrderReport($db, $from, $to);
    $orderSummary = getOrderSummary($db, $from, $to);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo - Restaurant Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-primary">
                <i class="fas fa-chart-bar"></i> Báo cáo hệ thống
            </h2>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $report_type == 'revenue' ? 'active' : '' ?>" 
                            onclick="changeReport('revenue')" type="button">
                        <i class="fas fa-dollar-sign"></i> Báo cáo doanh thu
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $report_type == 'orders' ? 'active' : '' ?>" 
                            onclick="changeReport('orders')" type="button">
                        <i class="fas fa-list"></i> Báo cáo đơn hàng
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $report_type == 'top_items' ? 'active' : '' ?>" 
                            onclick="changeReport('top_items')" type="button">
                        <i class="fas fa-trophy"></i> Top món ăn bán chạy
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="index.php" class="row g-3">
                        <input type="hidden" name="action" value="reports">
                        <input type="hidden" name="type" value="<?= $report_type ?>">
                        
                        <div class="col-md-2">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" 
                                   class="form-control" required>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" 
                                   class="form-control" required>
                        </div>
                        
                        
                        
                        <?php if ($report_type == 'top_items'): ?>
                        <div class="col-md-3">
                            <label class="form-label">Danh mục</label>
                            <select name="category_id" class="form-select">
                                <option value="">Tất cả danh mục</option>
                                <?php while ($category = $categories->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?= $category['id'] ?>" 
                                            <?= $category_id == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Lọc báo cáo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if ($report_type == 'revenue'): ?>
        <!-- Báo cáo doanh thu -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">
                            <i class="fas fa-dollar-sign"></i> Tổng doanh thu
                        </h5>
                        <p class="display-6 text-success fw-bold">
                            <?= number_format($totalRevenue ?? 0) ?> VND
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-body text-center">
                                            <h5 class="card-title text-primary">
                        <i class="fas fa-shopping-cart"></i> Đơn hàng đã hoàn thành
                    </h5>
                    <p class="display-6 text-primary fw-bold">
                        <?= number_format($totalPaidOrders ?? 0) ?>
                    </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng doanh thu theo thời gian -->
        <div class="card">
                    <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-line"></i> Doanh thu theo ngày
            </h5>
        </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>Thời gian</th>
                                <th class="text-center">Số đơn hàng</th>
                                <th class="text-end">Doanh thu (VND)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($revenueData)): ?>
                                <?php foreach ($revenueData as $row): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($row['period'])) ?></td>
                                        <td class="text-center"><?= number_format($row['order_count']) ?></td>
                                        <td class="text-end fw-bold"><?= number_format($row['revenue']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Không có dữ liệu</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($report_type == 'top_items'): ?>
        <!-- Top món ăn bán chạy -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">
                            <i class="fas fa-utensils"></i> Tổng món đã bán
                        </h5>
                        <p class="display-6 text-warning fw-bold">
                            <?= number_format($totalItemsSold ?? 0) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">
                            <i class="fas fa-dollar-sign"></i> Tổng doanh thu
                        </h5>
                        <p class="display-6 text-success fw-bold">
                            <?= number_format($totalRevenue ?? 0) ?> VND
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng top món ăn bán chạy -->
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-trophy"></i> Top 20 món ăn bán chạy
                    <?php if (!empty($category_id)): ?>
                        <span class="badge bg-info ms-2">
                            <?php 
                            $categories->execute(); // Reset pointer
                            while ($cat = $categories->fetch(PDO::FETCH_ASSOC)) {
                                if ($cat['id'] == $category_id) {
                                    echo htmlspecialchars($cat['name']);
                                    break;
                                }
                            }
                            ?>
                        </span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-warning">
                            <tr>
                                <th width="5%">#</th>
                                <th width="30%">Tên món</th>
                                <th width="15%">Danh mục</th>
                                <th class="text-center" width="15%">Số lượng bán</th>
                                <th class="text-end" width="15%">Đơn giá (VND)</th>
                                <th class="text-end" width="20%">Doanh thu (VND)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topItems)): ?>
                                <?php foreach ($topItems as $index => $item): ?>
                                    <tr>
                                        <td>
                                            <?php if ($index < 3): ?>
                                                <span class="badge bg-warning text-dark fs-6"><?= $index + 1 ?></span>
                                            <?php elseif ($index < 10): ?>
                                                <span class="badge bg-info text-white"><?= $index + 1 ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= $index + 1 ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold"><?= htmlspecialchars($item['name']) ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <?= htmlspecialchars($item['category_name'] ?? 'Không phân loại') ?>
                                            </span>
                                        </td>
                                        <td class="text-center fw-bold"><?= number_format($item['total_sold']) ?></td>
                                        <td class="text-end"><?= number_format($item['price']) ?></td>
                                        <td class="text-end fw-bold text-success"><?= number_format($item['total_revenue']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                        <br>Không có dữ liệu món ăn bán chạy trong khoảng thời gian này
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Báo cáo đơn hàng -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title text-primary">
                            <i class="fas fa-list"></i> Tổng đơn hàng
                        </h5>
                        <p class="display-6 text-primary fw-bold">
                            <?= number_format($orderSummary['total_orders'] ?? 0) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">
                            <i class="fas fa-clock"></i> Đang xử lý
                        </h5>
                        <p class="display-6 text-warning fw-bold">
                            <?= number_format($orderSummary['processing_count'] ?? 0) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">
                            <i class="fas fa-check"></i> Hoàn thành
                        </h5>
                        <p class="display-6 text-success fw-bold">
                            <?= number_format($orderSummary['completed_count'] ?? 0) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h5 class="card-title text-danger">
                            <i class="fas fa-times"></i> Đã hủy
                        </h5>
                        <p class="display-6 text-danger fw-bold">
                            <?= number_format($orderSummary['cancelled_count'] ?? 0) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng đơn hàng theo thời gian -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar"></i> Đơn hàng theo ngày
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Thời gian</th>
                                <th class="text-center">Tổng đơn</th>
                                <th class="text-center">Đang xử lý</th>
                                <th class="text-center">Hoàn thành</th>
                                <th class="text-center">Đã hủy</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orderData)): ?>
                                <?php foreach ($orderData as $row): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($row['period'])) ?></td>
                                        <td class="text-center fw-bold"><?= number_format($row['total_orders']) ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark"><?= number_format($row['processing_orders']) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><?= number_format($row['completed_orders']) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger"><?= number_format($row['cancelled_orders']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Không có dữ liệu</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function changeReport(type) {
    const url = new URL(window.location);
    url.searchParams.set('type', type);
    
    if (type !== 'top_items') {
        url.searchParams.delete('category_id');
    }
    
    window.location.href = url.toString();
}
</script>


