<?php
session_start();

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        //  ADMIN ROUTES 
        case 'login':
            include_once 'controllers/UserController.php';
            $controller = new UserController();
            $controller->login();
            break;
        case 'logout':
            include_once 'controllers/UserController.php';
            $controller = new UserController();
            $controller->logout();
            break;
        case 'dashboard':
            include_once 'controllers/AdminController.php';
            $controller = new AdminController();
            $controller->dashboard();
            break;
        case 'menu':
            include_once 'controllers/MenuController.php';
            $controller = new MenuController();
            $controller->index();
            break;
        case 'add_menu':
            include_once 'controllers/MenuController.php';
            $controller = new MenuController();
            $controller->add();
            break;
        case 'edit_menu':
            include_once 'controllers/MenuController.php';
            $controller = new MenuController();
            $controller->edit();
            break;
        case 'delete_menu':
            include_once 'controllers/MenuController.php';
            $controller = new MenuController();
            $controller->delete();
            break;
        case 'tables':
            include_once 'controllers/TableController.php';
            $controller = new TableController();
            $controller->index();
            break;
        case 'add_table':
            include_once 'controllers/TableController.php';
            $controller = new TableController();
            $controller->add();
            break;
        case 'edit_table':
            include_once 'controllers/TableController.php';
            $controller = new TableController();
            $controller->edit();
            break;
        case 'delete_table':
            include_once 'controllers/TableController.php';
            $controller = new TableController();
            $controller->delete();
            break;
        case 'orders':
            include_once 'controllers/OrderController.php';
            $controller = new OrderController();
            $controller->index();
            break;
        case 'add_order':
            include_once 'controllers/OrderController.php';
            $controller = new OrderController();
            $controller->add();
            break;
        case 'view_order':
            include_once 'controllers/OrderController.php';
            $controller = new OrderController();
            $controller->view();
            break;
        case 'delete_order':
            include_once 'controllers/OrderController.php';
            $controller = new OrderController();
            $controller->delete();
            break;
        case 'reports':
            include_once 'controllers/ReportController.php';
            $controller = new ReportController();
            $controller->index();
            break;
        case 'categories':
            include_once 'controllers/CategoryController.php';
            $controller = new CategoryController();
            $controller->index();
            break;
        case 'add_category':
            include_once 'controllers/CategoryController.php';
            $controller = new CategoryController();
            $controller->add();
            break;
        case 'delete_category':
            include_once 'controllers/CategoryController.php';
            $controller = new CategoryController();
            $controller->delete();
            break;
        case 'areas':
            include_once 'controllers/AreaController.php';
            $controller = new AreaController();
            $controller->index();
            break;
        case 'add_area':
            include_once 'controllers/AreaController.php';
            $controller = new AreaController();
            $controller->add();
            break;
        case 'delete_area':
            include_once 'controllers/AreaController.php';
            $controller = new AreaController();
            $controller->delete();
            break;

        // EMPLOYEE MANAGEMENT ROUTES
        case 'manage_employees':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
                header("Location: index.php"); exit;
            }
            include_once 'controllers/AdminEmployeeController.php';
            AdminEmployeeController::index($db);
            break;
        case 'add_employee':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
                header("Location: index.php"); exit;
            }
            include_once 'controllers/AdminEmployeeController.php';
            AdminEmployeeController::createForm($db);
            break;
        case 'create_employee':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
                header("Location: index.php"); exit;
            }
            include_once 'controllers/AdminEmployeeController.php';
            AdminEmployeeController::create($db);
            break;
        case 'edit_employee':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
                header("Location: index.php"); exit;
            }
            include_once 'controllers/AdminEmployeeController.php';
            AdminEmployeeController::editForm($db, $_GET['id']);
            break;
        case 'update_employee':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
                header("Location: index.php"); exit;
            }
            include_once 'controllers/AdminEmployeeController.php';
            AdminEmployeeController::update($db);
            break;
        case 'delete_employee':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
                header("Location: index.php"); exit;
            }
            include_once 'controllers/AdminEmployeeController.php';
            AdminEmployeeController::delete($db);
            break;
        case 'reset_password':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
                header("Location: index.php"); exit;
            }
            include_once 'controllers/AdminEmployeeController.php';
            AdminEmployeeController::resetPassword($db);
            break;
        case 'view_account_info':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
                header("Location: index.php"); exit;
            }
            include_once 'controllers/AdminEmployeeController.php';
            AdminEmployeeController::viewAccountInfo($db, $_GET['id']);
            break;

        // STAFF ROUTES 
        case 'staff_dashboard':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
                header("Location: index.php"); exit;
            }
            $view = 'views/staff/staff_dashboard.php';
            include 'views/staff/layout_staff.php';
            break;
        case 'add_order_staff':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
                header("Location: index.php"); exit;
            }
            $view = 'views/staff/add_order.php';
            include 'views/staff/layout_staff.php';
            break;
        case 'save_order_staff':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
                header("Location: index.php"); exit;
            }
            include_once 'controllers/StaffOrderController.php';
            // Hiển thị thông báo ngay trên trang add_order
            ob_start();
            $result = StaffOrderController::saveOrder($db);
            $order_message = ob_get_clean();
            
            // Nếu có lỗi và có table_id, chuyển hướng về trang với table_id
            if (strpos($order_message, 'alert-danger') !== false && isset($_POST['table_id'])) {
                header("Location: index.php?action=add_order_staff&table_id=" . $_POST['table_id'] . "&error=1");
                exit;
            }
            
            $view = 'views/staff/add_order.php';
            include 'views/staff/layout_staff.php';
            break;
        case 'view_orders_staff':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
                header("Location: index.php"); exit;
            }
            $view = 'views/staff/view_orders.php';
            include 'views/staff/layout_staff.php';
            break;
        case 'view_order_detail_staff':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
                header("Location: index.php"); exit;
            }
            $view = 'views/staff/view_order_detail.php';
            include 'views/staff/layout_staff.php';
            break;
            
            case 'edit_order_staff':
                if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
                    header("Location: index.php"); exit;
                }
                $view = 'views/staff/edit_order_staff.php';
                include 'views/staff/layout_staff.php';
                break;
            
            case 'update_item_status':
                if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
                    header("Location: index.php"); exit;
                }
                if (isset($_POST['order_item_id'], $_POST['new_status'], $_POST['order_id'])) {
                    include_once 'models/OrderItem.php';
                    $orderItem_model = new OrderItem($db);
                    $orderItem_model->updateStatus($_POST['order_item_id'], $_POST['new_status']);
                    header("Location: index.php?action=edit_order_staff&id=" . $_POST['order_id'] . "&status=success");
                    exit;
                }
                header("Location: index.php?action=staff_dashboard"); // Fallback
                exit;
                break;

        case 'pay_order_staff':
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
                header("Location: index.php"); exit;
            }
            include_once 'controllers/StaffOrderController.php';
            if (isset($_POST['order_id'])) {
                StaffOrderController::payOrder($db, $_POST['order_id']);
                // Chuyển hướng về danh sách đơn hàng với thông báo
                header("Location: index.php?action=view_orders_staff&payment=success");
                exit;
            }
            // Nếu không có order_id, quay lại danh sách
            header("Location: index.php?action=view_orders_staff");
            exit;
            break;

        // --- END STAFF ROUTES ---



        default:
            include_once 'views/login.php';
    }
} else {
    include_once 'views/login.php';
}
