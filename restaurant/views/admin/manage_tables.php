<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Bàn ăn</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
<?php
// Đọc toàn bộ kết quả vào mảng
$tables = [];
if (isset($result) && $result->rowCount() > 0) {
    $tables = $result->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Quản lý Bàn ăn</h3>
        <div>
            <a href="index.php?action=add_table" class="btn btn-primary me-2">Thêm bàn mới</a>
            <a href="index.php?action=areas" class="btn btn-outline-secondary">Quản lý Khu vực</a>
        </div>
    </div>

   
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-1"><?= count($tables) ?></h4>
                    <small class="text-muted">Tổng số bàn</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-success mb-1"><?= count(array_unique(array_column($tables, 'area_name'))) ?></h4>
                    <small class="text-muted">Khu vực</small>
                </div>
            </div>
        </div>
      

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="30%">Tên bàn</th>
                        <th width="40%">Khu vực</th>
                        <th width="20%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables as $row) : ?>
                    <tr>
                    
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <?php if (!empty($row['area_name'])): ?>
                                <?= htmlspecialchars($row['area_name']) ?>
                            <?php else: ?>
                                <span class="text-warning">Chưa phân khu</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?action=edit_table&id=<?= $row['id'] ?>" 
                               class="btn btn-sm btn-outline-primary me-1">Sửa</a>
                            <a href="index.php?action=delete_table&id=<?= $row['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa bàn này?');">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

   
    <?php if (empty($tables)): ?>
    <div class="text-center py-5">
        <h5 class="text-muted">Chưa có bàn nào</h5>
        <p class="text-muted">Bắt đầu bằng cách thêm bàn mới vào hệ thống.</p>
        <a href="index.php?action=add_table" class="btn btn-primary">Thêm bàn đầu tiên</a>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
