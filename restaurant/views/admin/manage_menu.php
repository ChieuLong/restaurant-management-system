<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Món ăn</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php
// Đọc toàn bộ kết quả vào mảng
$menu_items = [];
if (isset($result) && $result->rowCount() > 0) {
    $menu_items = $result->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Quản lý Món ăn</h3>
        <div>
            <a href="index.php?action=categories" class="btn btn-outline-secondary me-2">Quản lý Loại món</a>
            <a href="index.php?action=add_menu" class="btn btn-primary">Thêm món mới</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-1"><?= count($menu_items) ?></h4>
                    <small class="text-muted">Tổng số món</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-success mb-1">
                        <?= count(array_unique(array_column($menu_items, 'category_name'))) ?>
                    </h4>
                    <small class="text-muted">Loại món</small>
                </div>
            </div>
        </div>
       


    <div class="row g-3">
        <?php foreach ($menu_items as $row) : ?>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card h-100">
            
                <?php if (!empty($row['image'])) : ?>
                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" 
                         class="card-img-top"
                         alt="Ảnh món ăn"
                         style="height: 150px; object-fit: cover;">
                <?php else : ?>
                    <div class="bg-light d-flex align-items-center justify-content-center"
                         style="height: 150px;">
                        <small class="text-muted">Chưa có ảnh</small>
                    </div>
                <?php endif; ?>

             
                <div class="card-body">
                    <h6 class="card-title mb-2"><?= htmlspecialchars($row['name']) ?></h6>
                    
                    <div class="mb-2">
                        <strong class="text-success"><?= number_format($row['price'], 0, ',', '.') ?> VND</strong>
                    </div>
                    
                    <div class="mb-2">
                        <?php if (!empty($row['category_name'])): ?>
                            <span class="badge bg-primary"><?= htmlspecialchars($row['category_name']) ?></span>
                        <?php else: ?>
                            <span class="badge bg-warning">Chưa phân loại</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($row['description'])): ?>
                        <small class="text-muted">
                            <?= htmlspecialchars(substr($row['description'], 0, 60)) ?>...
                        </small>
                    <?php endif; ?>
                </div>

                
                <div class="card-footer bg-white border-0 pt-0">
                    <div class="d-flex gap-2">
                        <a href="index.php?action=edit_menu&id=<?= $row['id'] ?>" 
                           class="btn btn-sm btn-outline-primary flex-fill">Sửa</a>
                        <a href="index.php?action=delete_menu&id=<?= $row['id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Bạn có chắc chắn muốn xóa món này?');">Xóa</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($menu_items)): ?>
    <div class="text-center py-5">
        <h5 class="text-muted">Chưa có món ăn nào</h5>
        <p class="text-muted">Bắt đầu bằng cách thêm món mới vào menu.</p>
        <a href="index.php?action=add_menu" class="btn btn-primary">Thêm món đầu tiên</a>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
