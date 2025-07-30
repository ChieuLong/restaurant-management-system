<?php
include_once 'config/database.php';
include_once 'models/Order.php';
include_once 'models/OrderItem.php';
include_once 'models/Table.php';
include_once 'models/Menu.php';

class OrderController {
    public function index() {
        $database = new Database();
        $db = $database->getConnection();

        $order = new Order($db);
        $result = $order->getAll();

        $content = 'views/admin/manage_orders.php';
        include 'views/admin/layout_admin.php';
    }

    public function add() {
        $database = new Database();
        $db = $database->getConnection();

        $table = new Table($db);
        $menu = new Menu($db);

        $tables = $table->getAll();
        $menu_items = $menu->getAll();

        if ($_POST) {
            $order = new Order($db);
            $order->table_id = $_POST['table_id'];
            $order->staff_id = $_SESSION['user_id'];

            $order_id = $order->create();

            if ($order_id) {
                foreach ($_POST['menu_item_id'] as $index => $menu_item_id) {
                   
                    $orderItem = new OrderItem($db);
                    $orderItem->order_id = $order_id;
                    $orderItem->menu_item_id = $menu_item_id;
                    $orderItem->quantity = $_POST['quantity'][$index];

                
                    $menu_item_name = '';
                    foreach ($menu_items as $item) {
                        if ($item['id'] == $menu_item_id) {
                            $menu_item_name = $item['name'];
                            break;
                        }
                    }
                    $orderItem->menu_item_name = $menu_item_name;
                    $orderItem->create();
                }
                header("Location: index.php?action=orders");
            } else {
                echo "Lỗi tạo đơn hàng!";
            }
        } else {
            // Không còn chức năng tạo đơn hàng cho admin, chuyển hướng về danh sách đơn hàng
            header("Location: index.php?action=orders");
            exit;
        }
    }

    public function view() {
        $database = new Database();
        $db = $database->getConnection();

        $orderItem = new OrderItem($db);
        $items = $orderItem->getByOrderId($_GET['id']);

        $content = 'views/admin/view_order.php';
        include 'views/admin/layout_admin.php';
    }

    public function delete() {
        $database = new Database();
        $db = $database->getConnection();

        $order = new Order($db);
        $order->id = $_GET['id'];

        if ($order->delete()) {
            header("Location: index.php?action=orders");
        } else {
            echo "Lỗi xóa đơn!";
        }
    }
}
?>
