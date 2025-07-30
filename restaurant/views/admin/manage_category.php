<?php
// Đọc toàn bộ kết quả vào mảng
$categories = [];
if (isset($result) && $result->rowCount() > 0) {
    $categories = $result->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Quản lý Loại món</h3>
        <div>
            <a href="index.php?action=add_category" class="btn btn-primary">Thêm Loại món</a>
        </div>
    </div>

  
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-1"><?= count($categories) ?></h4>
                    <small class="text-muted">Tổng số loại món</small>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="10%">ID</th>
                        <th width="70%">Tên loại</th>
                        <th width="20%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $row) : ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars($row['id']) ?></strong></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <a href="index.php?action=delete_category&id=<?= $row['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa loại món này?')">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

 
    <?php if (empty($categories)): ?>
    <div class="text-center py-5">
        <h5 class="text-muted">Chưa có loại món nào</h5>
        <p class="text-muted">Bắt đầu bằng cách thêm loại món mới.</p>
        <a href="index.php?action=add_category" class="btn btn-primary">Thêm loại món đầu tiên</a>
    </div>
    <?php endif; ?>

   
    <div class="mt-4">
        <a href="index.php?action=menu" class="btn btn-outline-secondary">Quay lại Quản lý Món ăn</a>
    </div>
</div>
