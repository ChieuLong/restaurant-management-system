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
                <i class="fas fa-user-edit"></i> Sửa thông tin nhân viên
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

    <!-- Form sửa nhân viên -->
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-edit"></i> Thông tin nhân viên
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?action=update_employee">
                <input type="hidden" name="id" value="<?= $employee->id ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i> Họ và tên <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($employee->name) ?>" 
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
                                   value="<?= htmlspecialchars($employee->phone) ?>" 
                                   pattern="[0-9]{10,11}" required>
                            <div class="form-text">Số điện thoại sẽ được cập nhật làm username</div>
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
                                   value="<?= htmlspecialchars($employee->email) ?>">
                            <div class="form-text">Email không bắt buộc</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="hire_date" class="form-label">
                                <i class="fas fa-calendar"></i> Ngày vào làm
                            </label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                   value="<?= htmlspecialchars($employee->hire_date) ?>">
                            <div class="form-text">Ngày nhân viên bắt đầu làm việc</div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin hiện tại -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user-tag"></i> Vị trí hiện tại
                            </label>
                            <input type="text" class="form-control" 
                                   value="<?= $employee->position == 'admin' ? 'Admin' : 'Staff' ?>" 
                                   readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-info-circle"></i> Trạng thái hiện tại
                            </label>
                            <input type="text" class="form-control" 
                                   value="<?= $employee->status == 'active' ? 'Hoạt động' : 'Vô hiệu hóa' ?>" 
                                   readonly>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> Lưu ý:</h6>
                    <ul class="mb-0">
                        <li>Khi thay đổi số điện thoại, username sẽ được cập nhật tự động</li>
                        <li>Mật khẩu không thay đổi khi cập nhật thông tin</li>
                        <li>Để reset mật khẩu, sử dụng chức năng "Reset mật khẩu" trong danh sách</li>
                    </ul>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php?action=manage_employees" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Cập nhật
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