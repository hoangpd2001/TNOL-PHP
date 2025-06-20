<?php
class User extends Controller
{
    public $NguoiDungModel;

    public function __construct()
    {
        $this->NguoiDungModel = $this->model("NguoiDungModel");
        parent::__construct();
        require_once "./mvc/core/Pagination.php";
    }

    public function default()
    {
        if (AuthCore::checkPermission("nguoidung", "view")) {
            $this->view("main_layout", [
                "Page" => "user",
                "Title" => "Quản lý người dùng",
                "Script" => "user",
                "Plugin" => [
                    "sweetalert2" => 1,
                    "datepicker" => 1,
                    "flatpickr" => 1,
                    "notify" => 1,
                    "jquery-validate" => 1,
                    "select" => 1,
                    "pagination" => [],
                ],
                "Roles" => $this->NguoiDungModel->getAllRoles(),
            ]);
        } else $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
    }


    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = $_POST['masinhvien'];
            $email = $_POST['email'];
            $hoten = $_POST['hoten'];
            $ngaysinh = $_POST['ngaysinh'];
            $gioitinh = $_POST['gioitinh'];
            $password = $_POST['password'];
            $nhomquyen = 11;
            $trangthai = $_POST['status'];
            $malop = $_POST['malop'];
            $result = $this->NguoiDungModel->create($id, $email, $hoten, $password, $ngaysinh, $gioitinh, $nhomquyen, $trangthai, $malop);
            echo $result;
        }
    }

    public function checkUser()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = $_POST['mssv'];
            $email = $_POST['email'];
            $result = $this->NguoiDungModel->checkUser($id, $email);
            echo json_encode($result);
        }
    }

    public function getData()
    {
        $data = $this->NguoiDungModel->getAll();
        echo json_encode($data);
    }
    public function getDataByRole()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $manhomquyen = $_POST['manhomquyen'];
            $data = $this->NguoiDungModel->getDataByRole($manhomquyen);
            echo json_encode($data);
        }
    }
    public function getAllByClassroom()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $malop = $_POST['malop'];
            $data = $this->NguoiDungModel->getAllByClassroom($malop);
            echo json_encode($data);
        }
    }
    public function deleteData()
    {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $result = $this->NguoiDungModel->delete($id);
        }
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = $_POST['id'];
            $email = $_POST['email'];
            $hoten = $_POST['hoten'];
            $ngaysinh = $_POST['ngaysinh'];
            $gioitinh = $_POST['gioitinh'];
            $password = $_POST['password'];
            $nhomquyen = $_POST['role'];
            $trangthai = $_POST['status'];
            $result = $this->NguoiDungModel->update($id, $email, $hoten, $password, $ngaysinh, $gioitinh, $nhomquyen, $trangthai);
            echo $result;
        }
    }

    public function getDetail()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $result = $this->NguoiDungModel->getById($_POST['id']);
            echo json_encode($result);
        }
    }

    public function addExcel()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            error_reporting(E_ERROR | E_PARSE); // 1. Tắt lỗi nhẹ
            ini_set('display_errors', 0);

            require_once 'vendor/autoload.php';

            if (!isset($_FILES["fileToUpload"]) || $_FILES["fileToUpload"]["error"] !== 0) {
                echo json_encode(["error" => "Không nhận được file hoặc file bị lỗi"]);
                return;
            }


            $inputFileName = $_FILES["fileToUpload"]["tmp_name"];

            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                echo json_encode(["error" => "Lỗi không thể đọc file: " . $e->getMessage()]);
                return;
            }

            $sheet = $objPHPExcel->setActiveSheetIndex(0);
            $Totalrow = $sheet->getHighestRow();
            $LastColumn = $sheet->getHighestColumn();
            $TotalCol = PHPExcel_Cell::columnIndexFromString($LastColumn);
            $data = [];

            for ($i = 3; $i <= $Totalrow; $i++) {
                $fullname = "";
                $email = "";
                $mssv = "";
                $ngaysinhRaw = "";
                $gioitinhRaw = "";

                for ($j = 0; $j < $TotalCol; $j++) {
                    $cellValue = $sheet->getCellByColumnAndRow($j, $i)->getValue();

                    if ($j == 1) $mssv = $cellValue;
                    if ($j == 2) $fullname .= $cellValue;
                    if ($j == 3) $ngaysinhRaw = $cellValue;
                    if ($j == 4) $gioitinhRaw = $cellValue;
                    if ($j == 5) $email = $cellValue;
                }

                // Xử lý ngày sinh
                $ngaysinh = null;
                if (!empty($ngaysinhRaw)) {
                    $dateParts = explode('/', trim($ngaysinhRaw));
                    if (count($dateParts) == 3) {
                        $ngaysinh = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0]; // yyyy-mm-dd
                    }
                }

                // Xử lý giới tính
                $gioitinh = strtolower(trim($gioitinhRaw)) === 'nam' ? 1 : 0;

                $data[] = [
                    'fullname' => trim($fullname),
                    'email' => trim($email),
                    'mssv' => trim($mssv),
                    'ngaysinh' => $ngaysinh,
                    'gioitinh' => $gioitinh,
                    'nhomquyen' => 11,
                    'trangthai' => 1
                ];
            }


            echo json_encode($data);
        }
    }


    public function addFileExcel()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $listUser = $_POST['listuser'];
            $password = $_POST['password'];
            $malop = $_POST['malop'];
            $result = $this->NguoiDungModel->addFile($listUser, $password, $malop);
            echo $result;
        }
    }

    public function addFileExcelGroup()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $listUser = $_POST['listuser'];
            $password = $_POST['password'];
            $manhom = $_POST['group'];
            $result = $this->NguoiDungModel->addFileGroup($listUser, $password, $manhom);
            echo $result;
        }
    }

    public function getQuery($filter, $input, $args)
    {
        $query = $this->NguoiDungModel->getQuery($filter, $input, $args);
        return $query;
    }
}
