<?php
// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Lấy loại trang và tham số lọc
$page_type = $_GET['type'] ?? 'list';
$status_filter = $_GET['status'] ?? '';
$position_filter = $_GET['position'] ?? '';

include_once 'models/Employee.php';
$employeeModel = new Employee($db);

// Thống kê tổng quan
$totalStaff = $employeeModel->countStaff();
$activeStaff = $employeeModel->countActiveStaff();
$newStaffThisMonth = $employeeModel->countNewStaffThisMonth();
$inactiveStaff = $totalStaff - $activeStaff;

// Lấy danh sách nhân viên theo filter
if ($page_type == 'list') {
    $employees = $employeeModel->getAll();
} elseif ($page_type == 'active') {
    $employees = $employeeModel->getAllStaff();
} elseif ($page_type == 'new') {
    $employees = $employeeModel->getAllStaff(); // Có thể thêm filter theo tháng
}

// Hàm lấy thống kê nhân viên theo tháng
function getEmployeeStatsByMonth($db) {
    $query = "SELECT 
                MONTH(created_at) as month,
                YEAR(created_at) as year,
                COUNT(*) as new_employees
              FROM employees 
              WHERE position = 'staff' 
              AND YEAR(created_at) = YEAR(CURDATE())
              GROUP BY MONTH(created_at), YEAR(created_at)
              ORDER BY year DESC, month DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm lấy thống kê nhân viên theo vị trí
function getEmployeeStatsByPosition($db) {
    $query = "SELECT 
                position,
                COUNT(*) as count,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count
              FROM employees 
              GROUP BY position";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$monthlyStats = getEmployeeStatsByMonth($db);
$positionStats = getEmployeeStatsByPosition($db);
?>

<?php
// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}
?>
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-primary mb-0">
                        <i class="fas fa-users"></i> Quản lý nhân viên
                    </h2>
                    <p class="text-muted mb-0">Quản lý thông tin và tài khoản nhân viên hệ thống</p>
                </div>
                <a href="index.php?action=add_employee" class="btn btn-primary btn-lg shadow-sm">
                    <i class="fas fa-plus me-2"></i>
                    Thêm nhân viên mới
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                <div>
                    <strong>Thành công!</strong> <?= $_SESSION['success'] ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-lg me-3 text-danger"></i>
                <div>
                    <strong>Lỗi!</strong> <?= $_SESSION['error'] ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- New Employee Account Info -->
    <?php if (isset($_SESSION['new_employee_phone'])): ?>
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading fw-bold">
                        <i class="fas fa-user-plus me-2"></i>
                        Tài khoản mới đã được tạo thành công!
                    </h6>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h6 class="text-primary mb-2">Thông tin đăng nhập:</h6>
                                <p class="mb-1"><strong>Username:</strong> <code class="bg-white px-2 py-1 rounded"><?= htmlspecialchars($_SESSION['new_employee_phone']) ?></code></p>
                                <p class="mb-0"><strong>Password:</strong> <code class="bg-white px-2 py-1 rounded"><?= htmlspecialchars($_SESSION['new_employee_phone']) ?></code></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-warning bg-opacity-10 p-3 rounded border border-warning">
                                <h6 class="text-warning mb-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Lưu ý quan trọng
                                </h6>
                                <p class="text-warning mb-0">Vui lòng thông báo cho nhân viên đổi mật khẩu ngay sau khi đăng nhập lần đầu!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['new_employee_phone']); ?>
    <?php endif; ?>

    <!-- Reset Password Info -->
    <?php if (isset($_SESSION['reset_employee_phone'])): ?>
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-key fa-2x me-3 text-warning"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading fw-bold">
                        <i class="fas fa-unlock me-2"></i>
                        Mật khẩu đã được reset thành công!
                    </h6>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h6 class="text-warning mb-2">Thông tin mới:</h6>
                                <p class="mb-1"><strong>Username:</strong> <code class="bg-white px-2 py-1 rounded"><?= htmlspecialchars($_SESSION['reset_employee_phone']) ?></code></p>
                                <p class="mb-0"><strong>Password mới:</strong> <code class="bg-white px-2 py-1 rounded"><?= htmlspecialchars($_SESSION['reset_employee_phone']) ?></code></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-warning bg-opacity-10 p-3 rounded border border-warning">
                                <h6 class="text-warning mb-2">
                                    <i class="fas fa-bell me-2"></i>
                                    Thông báo
                                </h6>
                                <p class="text-warning mb-0">Vui lòng thông báo cho nhân viên về thông tin đăng nhập mới!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['reset_employee_phone']); ?>
    <?php endif; ?>

    <!-- Navigation Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" id="employeeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $page_type == 'list' ? 'active' : '' ?>" 
                            onclick="changePage('list')" type="button">
                        <i class="fas fa-list"></i> Danh sách nhân viên
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $page_type == 'stats' ? 'active' : '' ?>" 
                            onclick="changePage('stats')" type="button">
                        <i class="fas fa-chart-bar"></i> Thống kê nhân viên
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $page_type == 'accounts' ? 'active' : '' ?>" 
                            onclick="changePage('accounts')" type="button">
                        <i class="fas fa-user-cog"></i> Quản lý tài khoản
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Filter Form -->
    <?php if ($page_type == 'list'): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="index.php" class="row g-3">
                        <input type="hidden" name="action" value="manage_employees">
                        <input type="hidden" name="type" value="list">
                        
                        <div class="col-md-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" <?= $status_filter == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                <option value="inactive" <?= $status_filter == 'inactive' ? 'selected' : '' ?>>Vô hiệu hóa</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Vị trí</label>
                            <select name="position" class="form-select">
                                <option value="">Tất cả vị trí</option>
                                <option value="admin" <?= $position_filter == 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="staff" <?= $position_filter == 'staff' ? 'selected' : '' ?>>Staff</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="index.php?action=manage_employees&type=list" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Xóa lọc
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($page_type == 'list'): ?>
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Tổng nhân viên
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalStaff + 1 ?></div>
                                <small class="text-muted">Staff: <?= $totalStaff ?> | Admin: 1</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Đang hoạt động
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $activeStaff + 1 ?></div>
                                <small class="text-muted">Staff: <?= $activeStaff ?> | Admin: 1</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Mới tháng này
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $newStaffThisMonth ?></div>
                                <small class="text-muted">Nhân viên mới</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Vô hiệu hóa
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $inactiveStaff ?></div>
                                <small class="text-muted">Tạm nghỉ</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-list me-2"></i>
                    Danh sách nhân viên
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <a class="dropdown-item" href="index.php?action=add_employee">
                            <i class="fas fa-plus fa-sm fa-fw me-2 text-gray-400"></i>
                            Thêm nhân viên
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-download fa-sm fa-fw me-2 text-gray-400"></i>
                            Xuất danh sách
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="employeeTable" width="100%" cellspacing="0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th>Thông tin nhân viên</th>
                                <th>Liên hệ</th>
                                <th>Vị trí</th>
                                <th>Ngày vào làm</th>
                                <th>Trạng thái</th>
                                <th style="width: 180px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($employee = $employees->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary fs-6">#<?= $employee['id'] ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-user fa-lg text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-gray-900"><?= htmlspecialchars($employee['name']) ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope me-1"></i>
                                                <?= htmlspecialchars($employee['email'] ?: 'Chưa có email') ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <span class="fw-medium"><?= htmlspecialchars($employee['phone']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $employee['position'] == 'admin' ? 'danger' : 'primary' ?> fs-6 px-3 py-2">
                                        <i class="fas fa-<?= $employee['position'] == 'admin' ? 'crown' : 'user' ?> me-1"></i>
                                        <?= $employee['position'] == 'admin' ? 'Admin' : 'Staff' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar text-info me-2"></i>
                                        <span><?= date('d/m/Y', strtotime($employee['hire_date'])) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $employee['status'] == 'active' ? 'success' : 'secondary' ?> fs-6 px-3 py-2">
                                        <i class="fas fa-<?= $employee['status'] == 'active' ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                        <?= $employee['status'] == 'active' ? 'Hoạt động' : 'Vô hiệu hóa' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-info btn-sm" 
                                                onclick="viewAccountInfo(<?= $employee['id'] ?>)"
                                                title="Xem thông tin tài khoản">
                                            <i class="fas fa-user"></i>
                                        </button>
                                        <a href="index.php?action=edit_employee&id=<?= $employee['id'] ?>" 
                                           class="btn btn-outline-warning btn-sm" title="Sửa thông tin">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($employee['position'] == 'staff'): ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteEmployee(<?= $employee['id'] ?>, '<?= htmlspecialchars($employee['name']) ?>')"
                                                title="Vô hiệu hóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($page_type == 'stats'): ?>
        <!-- Statistics Page -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">
                            <i class="fas fa-users"></i> Tổng nhân viên
                        </h5>
                        <p class="display-6 text-success fw-bold"><?= $totalStaff + 1 ?></p>
                        <small class="text-muted">Staff: <?= $totalStaff ?> | Admin: 1</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title text-primary">
                            <i class="fas fa-user-check"></i> Đang hoạt động
                        </h5>
                        <p class="display-6 text-primary fw-bold"><?= $activeStaff + 1 ?></p>
                        <small class="text-muted">Staff: <?= $activeStaff ?> | Admin: 1</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Statistics -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line"></i> Nhân viên mới theo tháng (<?= date('Y') ?>)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-info">
                            <tr>
                                <th>Tháng</th>
                                <th class="text-center">Số nhân viên mới</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($monthlyStats)): ?>
                                <?php foreach ($monthlyStats as $stat): ?>
                                    <tr>
                                        <td><?= date('F Y', mktime(0, 0, 0, $stat['month'], 1, $stat['year'])) ?></td>
                                        <td class="text-center fw-bold"><?= number_format($stat['new_employees']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Không có dữ liệu</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Position Statistics -->
        <div class="card mt-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie"></i> Thống kê theo vị trí
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-warning">
                            <tr>
                                <th>Vị trí</th>
                                <th class="text-center">Tổng số</th>
                                <th class="text-center">Đang hoạt động</th>
                                <th class="text-center">Vô hiệu hóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($positionStats)): ?>
                                <?php foreach ($positionStats as $stat): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?= $stat['position'] == 'admin' ? 'danger' : 'primary' ?> fs-6 px-3 py-2">
                                                <?= $stat['position'] == 'admin' ? 'Admin' : 'Staff' ?>
                                            </span>
                                        </td>
                                        <td class="text-center fw-bold"><?= number_format($stat['count']) ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><?= number_format($stat['active_count']) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?= number_format($stat['count'] - $stat['active_count']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Không có dữ liệu</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($page_type == 'accounts'): ?>
        <!-- Account Management Page -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h5 class="card-title text-info">
                            <i class="fas fa-user-circle"></i> Tài khoản hoạt động
                        </h5>
                        <p class="display-6 text-info fw-bold"><?= $activeStaff + 1 ?></p>
                        <small class="text-muted">Có thể đăng nhập</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">
                            <i class="fas fa-key"></i> Cần reset mật khẩu
                        </h5>
                        <p class="display-6 text-warning fw-bold">0</p>
                        <small class="text-muted">Mật khẩu mặc định</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h5 class="card-title text-danger">
                            <i class="fas fa-user-times"></i> Tài khoản bị khóa
                        </h5>
                        <p class="display-6 text-danger fw-bold"><?= $inactiveStaff ?></p>
                        <small class="text-muted">Vô hiệu hóa</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Management Info -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Hướng dẫn quản lý tài khoản
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">📋 Thông tin tài khoản:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Username = Số điện thoại</li>
                            <li><i class="fas fa-check text-success me-2"></i>Password mặc định = Số điện thoại</li>
                            <li><i class="fas fa-check text-success me-2"></i>Tự động tạo khi thêm nhân viên</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-warning">🔑 Quản lý mật khẩu:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-exclamation-triangle text-warning me-2"></i>Reset về mật khẩu mặc định</li>
                            <li><i class="fas fa-exclamation-triangle text-warning me-2"></i>Nhân viên cần đổi sau khi đăng nhập</li>
                            <li><i class="fas fa-exclamation-triangle text-warning me-2"></i>Admin không thể reset mật khẩu</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal xem thông tin tài khoản -->
<div class="modal fade" id="accountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-circle me-2"></i>
                    Thông tin tài khoản
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="accountModalBody">
                <!-- Nội dung sẽ được load bằng AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal reset mật khẩu -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-gradient-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i>
                    Reset mật khẩu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                    <h6 class="fw-bold">Bạn có chắc muốn reset mật khẩu?</h6>
                    <p class="text-muted">Mật khẩu sẽ được đặt lại về số điện thoại của nhân viên!</p>
                </div>
            </div>
            <div class="modal-footer">
                <form method="POST" action="index.php?action=reset_password">
                    <input type="hidden" name="employee_id" id="resetEmployeeId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Hủy
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key me-2"></i>
                        Reset mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom CSS for professional look */
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
}
.bg-gradient-warning {
    background: linear-gradient(135deg, #f6c23e 0%, #f4b619 100%);
}

.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}

.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin: 0 2px;
}

.avatar {
    min-width: 50px;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

.modal-content {
    border: none;
    border-radius: 0.5rem;
}

.badge {
    font-weight: 500;
}

.nav-tabs .nav-link {
    border: none;
    border-radius: 0.5rem 0.5rem 0 0;
    margin-right: 0.25rem;
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    border: none;
}
</style>

<script>
function changePage(type) {
    const url = new URL(window.location);
    url.searchParams.set('type', type);
    window.location.href = url.toString();
}

function viewAccountInfo(employeeId) {
    fetch('index.php?action=view_account_info&id=' + employeeId)
        .then(response => response.text())
        .then(html => {
            document.getElementById('accountModalBody').innerHTML = html;
            new bootstrap.Modal(document.getElementById('accountModal')).show();
        });
}

function resetPassword(employeeId) {
    document.getElementById('resetEmployeeId').value = employeeId;
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}

function deleteEmployee(employeeId, employeeName) {
    if (confirm('Bạn có chắc muốn vô hiệu hóa nhân viên "' + employeeName + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?action=delete_employee';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = employeeId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script> 