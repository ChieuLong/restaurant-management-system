<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Sửa Món ăn</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Sửa Món Ăn</h3>
        <a href="index.php?action=menu" class="btn btn-outline-secondary">Quay lại</a>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên món</label>
                                    <input type="text" 
                                           id="name" 
                                           name="name"
                                           value="<?= htmlspecialchars($menu->name) ?>"
                                           class="form-control" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Giá (VNĐ)</label>
                                    <input type="number" 
                                           id="price" 
                                           name="price" 
                                           step="0.01"
                                           value="<?= htmlspecialchars($menu->price) ?>"
                                           class="form-control" 
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Loại món</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Chọn loại món</option>
                                <?php while ($row = $categories->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <option value="<?= $row['id'] ?>" 
                                            <?= ($menu->category_id ?? '') == $row['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($row['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="4"
                                      class="form-control"><?= htmlspecialchars($menu->description) ?></textarea>
                        </div>

                        <!-- Current Image -->
                        <?php if (!empty($menu->image)) : ?>
                        <div class="mb-3">
                            <label class="form-label">Ảnh hiện tại</label>
                            <div class="d-block">
                                <img src="uploads/<?= htmlspecialchars($menu->image) ?>"
                                     alt="Ảnh món ăn"
                                     class="img-thumbnail"
                                     style="max-width: 200px; height: auto;">
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- New Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Ảnh mới (nếu thay đổi)</label>
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   class="form-control" 
                                   accept="image/*">
                            <small class="text-muted">Chọn file ảnh mới (JPG, PNG, GIF)</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="index.php?action=menu" class="btn btn-outline-secondary">Hủy</a>
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
