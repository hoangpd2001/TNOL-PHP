<?php
require_once "./mvc/core/AuthCore.php";

class Faculty extends Controller
{
    public $khoaModel;

    public function __construct()
    {
        $this->khoaModel = $this->model("KhoaModel");
        require_once "./mvc/core/Pagination.php";
    }
    public function default()
    {
        if (AuthCore::checkPermission("monhoc", "view")) {
            $this->view("main_layout", [
                "Page" => "faculty",
                "Title" => "Quản lý môn học",
                "Script" => "faculty",
                "Plugin" => [
                    "sweetalert2" => 1,
                    "jquery-validate" => 1,
                    "select" => 1,
                    "notify" => 1,
                    "pagination" => [],
                ]
            ]);
        } else $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
    }
    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $makhoa = $_POST['makhoa'];
            $tenkhoa = $_POST['tenkhoa'];
            $magiaovien = $_POST['magiaovien'];
            $result = $this->khoaModel->create($makhoa, $tenkhoa,$magiaovien);
            echo $result;
        }
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $makhoa = $_POST['makhoa'];
            $tenkhoa = $_POST['tenkhoa'];
            $magiaovien = $_POST['magiaovien'];
            $result = $this->khoaModel->update( $makhoa, $tenkhoa, $magiaovien);
            echo $result;
        }
    }

    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $makhoa = $_POST['makhoa'];
            $result = $this->khoaModel->delete($makhoa);
            echo $result;
        }
    }

    public function getAll()
    {
        $data = $this->khoaModel->getAll();
        echo json_encode($data);
    }


    public function getDetail()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['makhoa'])) {
            $data = $this->khoaModel->getById($_POST['makhoa']);
            echo json_encode($data);
        } else {
            echo json_encode(null);
        }
    }
}
