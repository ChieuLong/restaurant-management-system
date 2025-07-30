<?php

$areas = [];
if (isset($result) && $result->rowCount() > 0) {
    $areas = $result->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Quản lý Khu vực</h3>
        <div>
            <a href="index.php?action=add_area" class="btn btn-primary">Thêm Khu vực</a>
        </div>
    </div>


    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-1"><?= count($areas) ?></h4>
                    <small class="text-muted">Tổng số khu vực</small>
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
                        <th width="70%">Tên Khu vực</th>
                        <th width="20%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($areas as $row) : ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars($row['id']) ?></strong></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <a href="index.php?action=delete_area&id=<?= $row['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa khu vực này?')">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

   
    <?php if (empty($areas)): ?>
    <div class="text-center py-5">
        <h5 class="text-muted">Chưa có khu vực nào</h5>
        <p class="text-muted">Bắt đầu bằng cách thêm khu vực mới.</p>
        <a href="index.php?action=add_area" class="btn btn-primary">Thêm khu vực đầu tiên</a>
    </div>
    <?php endif; ?>


    <div class="mt-4">
        <a href="index.php?action=tables" class="btn btn-outline-secondary">Quay lại Bàn ăn</a>
    </div>
</div>
