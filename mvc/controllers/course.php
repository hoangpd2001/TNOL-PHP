<?php
require_once "./mvc/core/AuthCore.php";

class Course extends Controller
{
    public $khoaHocModel;

    public function __construct()
    {
        $this->khoaHocModel = $this->model("KhoaHocModel");
        require_once "./mvc/core/Pagination.php";
    }

    public function default()
    {
        if (AuthCore::checkPermission("monhoc", "view")) {
            $this->view("main_layout", [
                "Page" => "course",
                "Title" => "Quản lý khóa học",
                "Script" => "course",
                "Plugin" => [
                    "sweetalert2" => 1,
                    "jquery-validate" => 1,
                    "select" => 1,
                    "notify" => 1,
                    "pagination" => [],
                ]
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
        }
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $makhoahoc = $_POST['makhoahoc'];
            $tenkhoahoc = $_POST['tenkhoahoc'];
            $trangthai = 1;
            $result = $this->khoaHocModel->create($makhoahoc, $tenkhoahoc, $trangthai);
            echo $result;
        }
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $makhoahoc = $_POST['makhoahoc'];
            $tenkhoahoc = $_POST['tenkhoahoc'];
            $trangthai = 1;
            $result = $this->khoaHocModel->update($makhoahoc, $tenkhoahoc, $trangthai);
            echo $result;
        }
    }

    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $makhoahoc = $_POST['makhoahoc'];
            $result = $this->khoaHocModel->delete($makhoahoc);
            echo $result;
        }
    }

    public function getAll()
    {
        $data = $this->khoaHocModel->getAll();
        echo json_encode($data);
    }

    public function getDetail()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['makhoahoc'])) {
            $data = $this->khoaHocModel->getById($_POST['makhoahoc']);
            echo json_encode($data);
        } else {
            echo json_encode(null);
        }
    }
}
