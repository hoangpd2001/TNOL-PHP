<?php
require_once "./mvc/core/AuthCore.php";

class Major extends Controller
{
    public $majorModel;

    public function __construct()
    {
        $this->majorModel = $this->model("NganhModel");
        require_once "./mvc/core/Pagination.php";
    }

    public function default()
    {
        if (AuthCore::checkPermission("monhoc", "view")) {
            $this->view("main_layout", [
                "Page" => "major",
                "Title" => "Quản lý ngành học",
                "Script" => "major",
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
          
            $tennganh = $_POST['tennganh'];
            $makhoa = $_POST['makhoa'];
            //   $trangthai = $_POST['trangthai'];
            $result = $this->majorModel->create($tennganh, $makhoa, 1);
            echo $result;
        }
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $manganh = $_POST['manganh'];
            $tennganh = $_POST['tennganh'];
            $makhoa = $_POST['makhoa'];
            // $trangthai = $_POST['trangthai'];
            $result = $this->majorModel->update($manganh, $tennganh, $makhoa, 1);
            echo $result;
        }
    }

    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $manganh = $_POST['manganh'];
            $result = $this->majorModel->delete($manganh);
            echo $result;
        }
    }

    public function getAll()
    {

        $data = $this->majorModel->getAll();
        echo json_encode($data);
    }

    public function getAllFaculty()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $makhoa = $_POST['makhoa'];
            $data = $this->majorModel->getAllFaculty($makhoa);
            echo json_encode($data);
        }
    }
    public function getDetail()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['manganh'])) {
            $data = $this->majorModel->getById($_POST['manganh']);
            echo json_encode($data);
        } else {
            echo json_encode(null);
        }
    }
}
