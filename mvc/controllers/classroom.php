<?php
require_once "./mvc/core/AuthCore.php";

class Classroom  extends Controller
{
    public $lopModel;

    public function __construct()
    {
        $this->lopModel = $this->model("LopModel");
        require_once "./mvc/core/Pagination.php";
    }

    public function default()
    {
        if (AuthCore::checkPermission("monhoc", "view")) {
            $this->view("main_layout", [
                "Page" => "classroom",
                "Title" => "Quản lý lớp",
                "Script" => "classroom",
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
        
        $tenlop = $_POST['tenlop'];
        $manganh = $_POST['manganh'];
        $makhoahoc = $_POST['makhoahoc'];
        $magiaovien = $_POST['magiaovien'];
        $result = $this->lopModel->create($tenlop, $manganh, $makhoahoc, $magiaovien);
        echo $result;
    }

    public function update()
    {
        $malop = $_POST['malop'];
        $tenlop = $_POST['tenlop'];
        $manganh = $_POST['manganh'];
        $makhoahoc = $_POST['makhoahoc'];
        $magiaovien = $_POST['magiaovien'];
        $trangthai = $_POST['trangthai'];
        $result = $this->lopModel->update( $malop, $tenlop, $manganh, $makhoahoc, $magiaovien, $trangthai);
        echo $result;
    }

    public function delete()
    {
        $malop = $_POST['malop'];
        $result = $this->lopModel->delete($malop);
        echo $result;
    }

    public function getDetail()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = $this->lopModel->getById($_POST['malop']);
            echo json_encode($data);
        }
        echo false;
    }

    public function getAll()
    {
        $data = $this->lopModel->getAll();
        echo json_encode($data);
    }
    public function getByMajorCount()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = $this->lopModel->getByMajorCount($_POST['manganh'], $_POST['makhoahoc']);
            echo json_encode($data);
        }
        echo false;
    }

    public function search()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $result = $this->lopModel->search($_POST['input']);
            echo json_encode($result);
        }
    }

    public function getQuery($filter, $input, $args)
    {
        $result = $this->lopModel->getQuery($filter, $input, $args);
        return $result;
    }
}
