<?php
class Teacher extends Controller
{
    public $TeacherModel;

    public function __construct()
    {
        $this->TeacherModel = $this->model("GiaoVienModel");
        parent::__construct();
        require_once "./mvc/core/Pagination.php";
    }

    public function getAllFaculty()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $makhoa = $_POST['makhoa'];
            $data = $this->TeacherModel->getAllFaculty($makhoa);
            echo json_encode($data);
        }
    }
}
