<?php
include_once 'models/Employee.php';

class AdminEmployeeController {
    
    // Hiển thị danh sách nhân viên
    public static function index($db) {
        $employeeModel = new Employee($db);
        $employees = $employeeModel->getAll();
        
        // Thống kê
        $totalStaff = $employeeModel->countStaff();
        $activeStaff = $employeeModel->countActiveStaff();
        $newStaffThisMonth = $employeeModel->countNewStaffThisMonth();
        
        $content = 'views/admin/manage_employees.php';
        include 'views/admin/layout_admin.php';
    }
    
    // Hiển thị form thêm nhân viên
    public static function createForm($db) {
        $content = 'views/admin/add_employee.php';
        include 'views/admin/layout_admin.php';
    }
    
    // Thêm nhân viên mới
    public static function create($db) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employee = new Employee($db);
            $employee->name = trim($_POST['name']);
            $employee->phone = trim($_POST['phone']);
            $employee->email = trim($_POST['email'] ?? '');
            $employee->position = 'staff'; // Chỉ thêm staff
            $employee->hire_date = $_POST['hire_date'] ?? date('Y-m-d');
            
            // Kiểm tra dữ liệu
            if (empty($employee->name)) {
                $_SESSION['error'] = 'Tên nhân viên không được để trống!';
                header("Location: index.php?action=add_employee");
                exit;
            }
            
            if (empty($employee->phone)) {
                $_SESSION['error'] = 'Số điện thoại không được để trống!';
                header("Location: index.php?action=add_employee");
                exit;
            }
            
            if ($employee->create()) {
                $_SESSION['success'] = 'Thêm nhân viên thành công!';
                $_SESSION['new_employee_phone'] = $employee->phone;
                header("Location: index.php?action=manage_employees");
                exit;
            } else {
                $_SESSION['error'] = 'Lỗi khi thêm nhân viên!';
                header("Location: index.php?action=add_employee");
                exit;
            }
        }
    }
    
    // Hiển thị form sửa nhân viên
    public static function editForm($db, $id) {
        $employee = new Employee($db);
        if ($employee->getById($id)) {
            $content = 'views/admin/edit_employee.php';
            include 'views/admin/layout_admin.php';
        } else {
            $_SESSION['error'] = 'Không tìm thấy nhân viên!';
            header("Location: index.php?action=manage_employees");
            exit;
        }
    }
    
    // Cập nhật thông tin nhân viên
    public static function update($db) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employee = new Employee($db);
            $employee->id = $_POST['id'];
            $employee->name = trim($_POST['name']);
            $employee->phone = trim($_POST['phone']);
            $employee->email = trim($_POST['email'] ?? '');
            $employee->hire_date = $_POST['hire_date'] ?? date('Y-m-d');
            
            // Kiểm tra dữ liệu
            if (empty($employee->name)) {
                $_SESSION['error'] = 'Tên nhân viên không được để trống!';
                header("Location: index.php?action=edit_employee&id=" . $employee->id);
                exit;
            }
            
            if (empty($employee->phone)) {
                $_SESSION['error'] = 'Số điện thoại không được để trống!';
                header("Location: index.php?action=edit_employee&id=" . $employee->id);
                exit;
            }
            
            if ($employee->update()) {
                $_SESSION['success'] = 'Cập nhật thông tin thành công!';
                header("Location: index.php?action=manage_employees");
                exit;
            } else {
                $_SESSION['error'] = 'Lỗi khi cập nhật!';
                header("Location: index.php?action=edit_employee&id=" . $employee->id);
                exit;
            }
        }
    }
    
    // Vô hiệu hóa nhân viên
    public static function delete($db) {
        if (isset($_POST['id'])) {
            $employee = new Employee($db);
            $employee->id = $_POST['id'];
            
            // Kiểm tra xem có phải admin không
            if ($employee->getById($_POST['id'])) {
                if ($employee->position == 'admin') {
                    $_SESSION['error'] = 'Không thể vô hiệu hóa tài khoản Admin!';
                    header("Location: index.php?action=manage_employees");
                    exit;
                }
            }
            
            if ($employee->deactivate()) {
                $_SESSION['success'] = 'Đã vô hiệu hóa nhân viên!';
            } else {
                $_SESSION['error'] = 'Lỗi khi vô hiệu hóa!';
            }
        }
        header("Location: index.php?action=manage_employees");
        exit;
    }
    
    // Reset mật khẩu nhân viên
    public static function resetPassword($db) {
        if (isset($_POST['employee_id'])) {
            $employee = new Employee($db);
            $employee->id = $_POST['employee_id'];
            
            if ($employee->getById($_POST['employee_id'])) {
                if ($employee->resetPassword()) {
                    $_SESSION['success'] = 'Đã reset mật khẩu thành công!';
                    $_SESSION['reset_employee_phone'] = $employee->phone;
                } else {
                    $_SESSION['error'] = 'Lỗi khi reset mật khẩu!';
                }
            } else {
                $_SESSION['error'] = 'Không tìm thấy nhân viên!';
            }
        }
        header("Location: index.php?action=manage_employees");
        exit;
    }
    
    // Xem thông tin tài khoản nhân viên (AJAX)
    public static function viewAccountInfo($db, $employee_id) {
        $employee = new Employee($db);
        $userInfo = $employee->getUserInfo($employee_id);
        
        if ($userInfo) {
            echo '<div class="card">
                    <div class="card-header">
                        <h6>📋 Thông tin tài khoản</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Họ tên:</strong> ' . htmlspecialchars($userInfo['name']) . '</p>
                        <p><strong>Username:</strong> ' . htmlspecialchars($userInfo['username']) . '</p>
                        <p><strong>Vị trí:</strong> 
                            <span class="badge bg-' . ($userInfo['position'] == 'admin' ? 'danger' : 'primary') . '">
                                ' . ($userInfo['position'] == 'admin' ? 'Admin' : 'Staff') . '
                            </span>
                        </p>
                        <p><strong>Trạng thái tài khoản:</strong> 
                            <span class="badge bg-' . ($userInfo['user_status'] == 'active' ? 'success' : 'danger') . '">
                                ' . ($userInfo['user_status'] == 'active' ? 'Hoạt động' : 'Vô hiệu hóa') . '
                            </span>
                        </p>
                        <p><strong>Mật khẩu:</strong> 
                            <span class="text-muted">Đã được mã hóa (không thể xem)</span>
                        </p>';
            
            // Chỉ hiện nút reset cho staff
            if ($userInfo['position'] == 'staff') {
                echo '<button type="button" class="btn btn-warning btn-sm" 
                                onclick="resetPassword(' . $employee_id . ')">
                            <i class="fas fa-key"></i> Reset mật khẩu
                        </button>';
            }
            
            echo '</div></div>';
        } else {
            echo '<div class="alert alert-danger">Không tìm thấy thông tin tài khoản!</div>';
        }
    }
}
?> 