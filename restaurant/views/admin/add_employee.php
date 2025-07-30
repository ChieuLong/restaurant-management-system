<?php
// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-primary">
                <i class="fas fa-user-plus"></i> Thêm nhân viên mới
            </h2>
        </div>
    </div>

    <!-- Thông báo lỗi -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Form thêm nhân viên -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-edit"></i> Thông tin nhân viên
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?action=create_employee">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i> Họ và tên <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                                   required>
                            <div class="form-text">Nhập họ và tên đầy đủ của nhân viên</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone"></i> Số điện thoại <span class="text-danger">*</span>
                            </label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" 
                                   pattern="[0-9]{10,11}" required>
                            <div class="form-text">Số điện thoại sẽ được dùng làm username và password mặc định</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            <div class="form-text">Email không bắt buộc</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="hire_date" class="form-label">
                                <i class="fas fa-calendar"></i> Ngày vào làm
                            </label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                   value="<?= htmlspecialchars($_POST['hire_date'] ?? date('Y-m-d')) ?>">
                            <div class="form-text">Ngày nhân viên bắt đầu làm việc</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Lưu ý:</h6>
                    <ul class="mb-0">
                        <li>Nhân viên mới sẽ được tạo với vai trò <strong>Staff</strong></li>
                        <li>Tài khoản đăng nhập sẽ được tạo tự động</li>
                        <li><strong>Username:</strong> Số điện thoại</li>
                        <li><strong>Password:</strong> Số điện thoại (cần đổi sau khi đăng nhập)</li>
                    </ul>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php?action=manage_employees" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Thêm nhân viên
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validate form
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    
    if (name === '') {
        alert('Vui lòng nhập họ và tên!');
        e.preventDefault();
        return false;
    }
    
    if (phone === '') {
        alert('Vui lòng nhập số điện thoại!');
        e.preventDefault();
        return false;
    }
    
    // Kiểm tra định dạng số điện thoại
    const phoneRegex = /^[0-9]{10,11}$/;
    if (!phoneRegex.test(phone)) {
        alert('Số điện thoại phải có 10-11 chữ số!');
        e.preventDefault();
        return false;
    }
});
</script> 