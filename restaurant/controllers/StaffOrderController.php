<?php
include_once 'models/Order.php';
include_once 'models/OrderItem.php';

class StaffOrderController {
    public static function saveOrder($db) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $table_id = $_POST['table_id'] ?? null;
            $quantities = [];
            if (!empty($_POST['quantities_json'])) {
                $quantities = json_decode($_POST['quantities_json'], true);
            }
            $note = $_POST['note'] ?? '';
            $staff_id = $_SESSION['user_id'] ?? null;

            //  Kiểm tra dữ liệu
            if (!$table_id) {
                echo '<div class="alert alert-danger">Lỗi: Không có table_id!</div>';
                return;
            }
            
            if (!$staff_id) {
                echo '<div class="alert alert-danger">Lỗi: Không có staff_id! Vui lòng đăng nhập lại.</div>';
                return;
            }

            // Lọc các món có số lượng > 0
            $order_items = [];
            foreach ($quantities as $menu_id => $qty) {
                if ($qty > 0) {
                    $order_items[] = [
                        'menu_id' => $menu_id,
                        'quantity' => $qty
                    ];
                }
            }

            if (empty($order_items)) {
                echo '<div class="alert alert-danger">Vui lòng chọn ít nhất một món!</div>';
                return;
            }

            // Tạo đơn hàng
            $order = new Order($db);
            $order->table_id = $table_id;
            $order->staff_id = $staff_id;
            if (property_exists($order, 'note')) $order->note = $note;
            $order_id = $order->create();

            if ($order_id) {
                // Lấy tên món ăn từ menu_items
                include_once 'models/Menu.php';
                $menuModel = new Menu($db);
                $menuItems = $menuModel->getAll();
                $menuMap = [];
                while ($m = $menuItems->fetch(PDO::FETCH_ASSOC)) {
                    $menuMap[$m['id']] = $m['name'];
                }
                
                foreach ($order_items as $item) {
                    $orderItem = new OrderItem($db);
                    $orderItem->order_id = $order_id;
                    $orderItem->menu_item_id = $item['menu_id'];
                    $orderItem->quantity = $item['quantity'];
                    $orderItem->menu_item_name = $menuMap[$item['menu_id']] ?? '';
                    $orderItem->create();
                }

                // Cập nhật trạng thái bàn thành 'occupied'
                include_once 'models/Table.php';
                $table_model = new Table($db);
                $table_model->updateStatus($table_id, 'occupied');

                echo '<div class="alert alert-success">Tạo đơn hàng thành công! </div>';
                
            } else {
                echo '<div class="alert alert-danger">Lỗi khi tạo đơn hàng!</div>';
            }
        }
    }

    public static function payOrder($db, $order_id) {
        // Lấy thông tin đơn hàng để biết table_id
        $order_model = new Order($db);
        $order = $order_model->getById($order_id);
        if (!$order) {
            return false; // Đơn hàng không tồn tại
        }

        // Cập nhật trạng thái đơn hàng
        $query = "UPDATE orders SET status = 'paid' WHERE id = :order_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        
        if ($stmt->execute()) {
            // Cập nhật trạng thái bàn thành 'available'
            include_once 'models/Table.php';
            $table_model = new Table($db);
            $table_model->updateStatus($order['table_id'], 'available');
            return true;
        }
        return false;
    }
} 