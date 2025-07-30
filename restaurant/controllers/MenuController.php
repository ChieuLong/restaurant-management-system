<?php
include_once 'config/database.php';
include_once 'models/Menu.php';
include_once 'models/Category.php';

class MenuController {
    public function index() {
        $database = new Database();
        $db = $database->getConnection();

        $menu = new Menu($db);
        $category = new Category($db);

        $result = $menu->getAllWithCategory(); 
        $categories = $category->getAll();

        $content = 'views/admin/manage_menu.php';
        include 'views/admin/layout_admin.php';
    }

    public function add() {
        $database = new Database();
        $db = $database->getConnection();

        $menu = new Menu($db);
        $category = new Category($db);

        $categories = $category->getAll(); 

        if ($_POST) {
            $menu->name = trim($_POST['name']);
            $menu->price = $_POST['price'];
            $menu->category_id = $_POST['category_id'];  
            $menu->description = trim($_POST['description']);

            // Xử lý upload ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $menu->image = $fileName;
                } else {
                    echo "Lỗi upload ảnh!";
                    return;
                }
            } else {
                $menu->image = null;
            }

            if ($menu->create()) {
                header("Location: index.php?action=menu");
                exit;
            } else {
                echo "Lỗi thêm món!";
            }
        } else {
            $content = 'views/admin/add_menu.php';
            include 'views/admin/layout_admin.php';
        }
    }

    public function edit() {
        $database = new Database();
        $db = $database->getConnection();

        $menu = new Menu($db);
        $category = new Category($db);

        $categories = $category->getAll();

        $menu->id = $_GET['id'] ?? 0;
        $menu->getOne();

        if ($_POST) {
            $menu->name = trim($_POST['name']);
            $menu->price = $_POST['price'];
            $menu->category_id = $_POST['category_id']; 
            $menu->description = trim($_POST['description']);

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $menu->image = $fileName;
                } else {
                    echo "Lỗi upload ảnh!";
                    return;
                }
            }
            // Nếu không upload mới thì giữ ảnh cũ

            if ($menu->update()) {
                header("Location: index.php?action=menu");
                exit;
            } else {
                echo "Lỗi cập nhật!";
            }
        } else {
            $content = 'views/admin/edit_menu.php';
            include 'views/admin/layout_admin.php';
        }
    }

    public function delete() {
        $database = new Database();
        $db = $database->getConnection();

        $menu = new Menu($db);
        $menu->id = $_GET['id'] ?? 0;

        if ($menu->delete()) {
            header("Location: index.php?action=menu");
            exit;
        } else {
            echo "Lỗi xóa!";
        }
    }
}
