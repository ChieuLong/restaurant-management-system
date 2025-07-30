<?php
include_once 'config/database.php';
include_once 'models/Category.php';

class CategoryController {
    public function index() {
        $database = new Database();
        $db = $database->getConnection();
        $category = new Category($db);
        $result = $category->getAll();
        $content = 'views/admin/manage_category.php';
        include 'views/admin/layout_admin.php';
    }

    public function add() {
        $database = new Database();
        $db = $database->getConnection();
        $category = new Category($db);

        if ($_POST) {
            $category->name = $_POST['name'];
            $category->create();
            header("Location: index.php?action=categories");
        } else {
            $content = 'views/admin/add_category.php';
            include 'views/admin/layout_admin.php';
        }
    }

    public function delete() {
        $database = new Database();
        $db = $database->getConnection();
        $category = new Category($db);
        $category->id = $_GET['id'];
        $category->delete();
        header("Location: index.php?action=categories");
    }
}
?>
