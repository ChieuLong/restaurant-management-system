<?php
require_once 'models/Order.php';
require_once 'models/OrderItem.php';
require_once 'models/Table.php';
require_once 'models/Menu.php';

class StaffOrderController {

    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function dashboard() {
        // Lấy dữ liệu thống kê
        $tableModel = new Table($this->db);
        $total_tables = $tableModel->countAll();
        $available_tables = $tableModel->countAvailable();

        $orderModel = new Order($this->db);
        $processing_orders = $orderModel->countProcessingByStaff($_SESSION['user_id']);

        include 'views/staff/dashboard.php';
    }

    public function addForm() {
        $tableModel = new Table($this->db);
        $available_tables = $tableModel->getAvailable();

        $menuModel = new Menu($this->db);
        $menu_items = $menuModel->getAll();

        include 'views/staff/add_order.php';
    }

    public function save() {
        
    }

    public function index() {
        $orderModel = new Order($this->db);
        $orders = $orderModel->getByStaff($_SESSION['user_id']);

        include 'views/staff/manage_orders.php';
    }

    public function view() {
        $orderModel = new Order($this->db);
        $order = $orderModel->getById($_GET['id']);
        $orderItems = $orderModel->getItems($_GET['id']);

        include 'views/staff/view_order.php';
    }
}
