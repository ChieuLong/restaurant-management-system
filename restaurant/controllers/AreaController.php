<?php
include_once 'config/database.php';
include_once 'models/Area.php';

class AreaController {
    public function index() {
        $db = (new Database())->getConnection();
        $area = new Area($db);
        $result = $area->getAll();
        $content = 'views/admin/manage_areas.php';
        include 'views/admin/layout_admin.php';
    }

    public function add() {
        $db = (new Database())->getConnection();
        $area = new Area($db);
        if ($_POST) {
            $area->name = $_POST['name'];
            $area->create();
            header("Location: index.php?action=areas");
        } else {
            $content = 'views/admin/add_area.php';
            include 'views/admin/layout_admin.php';
        }
    }

    public function delete() {
        $db = (new Database())->getConnection();
        $area = new Area($db);
        $area->id = $_GET['id'];
        $area->delete();
        header("Location: index.php?action=areas");
    }
}
?>
