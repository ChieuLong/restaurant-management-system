<?php
include_once 'config/database.php';
include_once 'models/Table.php';
include_once 'models/Area.php'; 

class TableController {
    public function index() {
        $database = new Database();
        $db = $database->getConnection();

        $table = new Table($db);
        $result = $table->getAll(); 

        $content = 'views/admin/manage_tables.php';
        include 'views/admin/layout_admin.php';
    }

    public function add() {
        $database = new Database();
        $db = $database->getConnection();

        $table = new Table($db);
        $area = new Area($db);
        $areas = $area->getAll(); 

        if ($_POST) {
            $table->name = $_POST['name'];
            $table->area_id = $_POST['area_id']; 

            if ($table->create()) {
                header("Location: index.php?action=tables");
            } else {
                echo "Lỗi thêm bàn!";
            }
        } else {
            $content = 'views/admin/add_table.php';
            include 'views/admin/layout_admin.php';
        }
    }

    public function edit() {
        $database = new Database();
        $db = $database->getConnection();

        $table = new Table($db);
        $area = new Area($db);
        $areas = $area->getAll(); 

        $table->id = $_GET['id'];
        $table->getOne(); 

        if ($_POST) {
            $table->name = $_POST['name'];
            $table->area_id = $_POST['area_id']; 

            if ($table->update()) {
                header("Location: index.php?action=tables");
            } else {
                echo "Lỗi cập nhật!";
            }
        } else {
            $content = 'views/admin/edit_table.php';
            include 'views/admin/layout_admin.php';
        }
    }

    public function delete() {
        $database = new Database();
        $db = $database->getConnection();

        $table = new Table($db);
        $table->id = $_GET['id'];

        if ($table->delete()) {
            header("Location: index.php?action=tables");
        } else {
            echo "Lỗi xoá!";
        }
    }
}
