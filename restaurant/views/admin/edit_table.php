<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Sửa Bàn ăn</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Sửa Bàn Ăn</h3>
        <a href="index.php?action=tables" class="btn btn-outline-secondary">Quay lại</a>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên bàn</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-control"
                                   value="<?= htmlspecialchars($table->name) ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="area_id" class="form-label">Khu vực</label>
                            <select name="area_id" id="area_id" class="form-select">
                                <option value="">Chọn khu vực</option>
                                <?php while ($row = $areas->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <option value="<?= $row['id'] ?>"
                                        <?= ($table->area_id ?? '') == $row['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($row['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="index.php?action=tables" class="btn btn-outline-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>