<?php
require_once 'vendor/autoload.php';
require_once 'vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
class Classmodule extends Controller
{
    public $hocphanModel;
    public $nguoiDungModel;
    function __construct()
    {
        $this->hocphanModel = $this->model("HocPhanModel");
        $this->nguoiDungModel = $this->model("NguoiDungModel");
        parent::__construct();
        require_once "./mvc/core/Pagination.php";
    }

    public function default()
    {
        if (AuthCore::checkPermission("hocphan", "view")) {
            $this->view("main_layout", [
                "Page" => "classmodule",
                "Title" => "Quản lý nhóm học phần",
                "Script" => "classmodule",
                "Plugin" => [
                    "sweetalert2" => 1,
                    "select" => 1,
                    "jquery-validate" => 1,
                    "notify" => 1
                ]
            ]);
        } else
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
    }

    public function detail($mahocphan)
    {
        $chitiethocphan = $this->hocphanModel->getDetailGroup($mahocphan);
        $check = $this->nguoiDungModel->checkAdmin($_SESSION['user_id']);
        if (AuthCore::checkPermission("hocphan", "view") && ($_SESSION['user_id'] == $chitiethocphan['magiaovien'] || $check)) {
            $this->view("main_layout", [
                "Page" => "classroom_detail",
                "Title" => "Quản lý nhóm",
                "Plugin" => [
                    "datepicker" => 1,
                    "flatpickr" => 1,
                    "sweetalert2" => 1,
                    "jquery-validate" => 1,
                    "notify" => 1,
                    "pagination" => [],
                ],
                "Script" => "classroom_detail",
                "Detail" => $chitiethocphan
            ]);
        } else
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
    }

    public function loadData()
    {
        //AuthCore::checkAuthentication();
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
             $hienthi = $_POST['hienthi'];
             $user_id = $_SESSION['user_id'];
            $result = $this->hocphanModel->getBySubject($hienthi,$user_id);
            echo json_encode($result);
        } else
            echo json_encode(false);
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("hocphan", "create")) {
            $mangnganh = $_POST['manganh'];
            $makhoahoc = $_POST['makhoahoc'];
            $monhoc = $_POST['monhoc'];
            $magiaovien = $_POST['magiaovien'];
            $ghichu = $_POST['ghichu'];
            $result = $this->hocphanModel->create($mangnganh,$makhoahoc,$monhoc,$magiaovien,$ghichu);
            echo $result;
        } else
            echo json_encode(false);
    }

    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("hocphan", "delete")) {
            $mahocphan = $_POST['mahocphan'];
            $result = $this->hocphanModel->delete($mahocphan);
            echo $result;
        } else
            echo json_encode(false);
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("hocphan", "update")) {
            $mahocphan = $_POST['mahocphan'];
            $magiaovien = $_POST['magiaovien'];
            $ghichu = $_POST['ghichu'];
            $result = $this->hocphanModel->update($mahocphan,  $ghichu, $magiaovien);
            echo json_encode($result);
        } else
            echo json_encode(false);
    }

    public function hide()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("hocphan", "create")) {
            $mahocphan = $_POST['mahocphan'];
            $giatri = $_POST['giatri'];

            $result = $this->hocphanModel->hide($mahocphan, $giatri);

            if ($result) {
                http_response_code(200);
                echo json_encode(["success" => true]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Cập nhật thất bại"]);
            }
        } else {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Không có quyền"]);
        }

        exit; 
    }


    public function getDetail()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
            $mahocphan = $_POST['mahocphan'];
            $result = $this->hocphanModel->getById($mahocphan);
            echo json_encode($result);
        } else
            echo json_encode(false);
    }


    public function updateInvitedCode()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("hocphan", "create")) {
            $mahocphan = $_POST['mahocphan'];
            $result = $this->hocphanModel->updateInvitedCode($mahocphan);
            echo $result;
        }
    }

    public function getInvitedCode()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("hocphan", "view")) {
            $mahocphan = $_POST['mahocphan'];
            $result = $this->hocphanModel->getInvitedCode($mahocphan);
            echo $result['mamoi'];
        }
    }

    public function getSvList()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mahocphan = $_POST['mahocphan'];
            $result = $this->hocphanModel->getSvList($mahocphan);
            echo json_encode($result);
        }
    }


    public function addSV()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mahocphan = $_POST['mahocphan'];
            $mssv = $_POST['mssv'];
            $hoten = $_POST['hoten'];
            $password = $_POST['password'];
            $result = $this->hocphanModel->addSV($mssv, $hoten, $password);
            $joinGroup = $this->hocphanModel->join($mahocphan, $mssv);
            echo $joinGroup;
        }
    }

    public function addSvGroup()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mahocphan = $_POST['mahocphan'];
            $mssv = $_POST['mssv'];
            $joinGroup = $this->hocphanModel->join($mahocphan, $mssv);
            echo ($joinGroup);
        }
    }

    public function checkAcc()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mahocphan = $_POST['mahocphan'];
            $mssv = $_POST['mssv'];
            $result = $this->hocphanModel->checkAcc($mssv, $mahocphan);
            echo $result;
        }
    }


    public function exportExcelStudentS()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mahocphan = $_POST['mahocphan'];
            $result = $this->hocphanModel->getStudentByGroup($mahocphan);
            //Khởi tạo đối tượng
            $excel = new PHPExcel();
            //Chọn trang cần ghi (là số từ 0->n)
            $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
            $excel->getActiveSheet()->setTitle("Danh sách kết quả");

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
            $excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);


            //Xét in đậm cho khoảng cột
            $phpColor = new PHPExcel_Style_Color();
            $phpColor->setRGB('FFFFFF');
            $excel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
            $excel->getActiveSheet()->getStyle('A1:F1')->getFont()->setColor($phpColor);
            $excel->getActiveSheet()->getStyle('A1:F1')->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '33FF33')
                    )
                )
            );
            $excel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->applyFromArray(
                array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );;
            //Tạo tiêu đề cho từng cột
            //Vị trí có dạng như sau:
            /**
             * |A1|B1|C1|..|n1|
             * |A2|B2|C2|..|n1|
             * |..|..|..|..|..|
             * |An|Bn|Cn|..|nn|
             */
            $excel->getActiveSheet()->setCellValue('A1', 'MSSV');
            $excel->getActiveSheet()->setCellValue('B1', 'Họ và tên');
            $excel->getActiveSheet()->setCellValue('C1', 'Email');
            $excel->getActiveSheet()->setCellValue('D1', 'Ngày tham gia');
            $excel->getActiveSheet()->setCellValue('E1', 'Ngày Sinh');
            $excel->getActiveSheet()->setCellValue('F1', 'Giới tính');
            // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
            // dòng bắt đầu = 2
            $numRow = 2;
            foreach ($result as $row) {
                $excel->getActiveSheet()->setCellValue('A' . $numRow, $row["id"]);
                $excel->getActiveSheet()->setCellValue('B' . $numRow, $row["hoten"]);
                $excel->getActiveSheet()->setCellValue('C' . $numRow, $row["email"]);
                $excel->getActiveSheet()->setCellValue('D' . $numRow, $row["ngaythamgia"]);
                $excel->getActiveSheet()->setCellValue('E' . $numRow, $row["ngaysinh"]);
                if ($row["gioitinh"] == 0) {
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, "Nữ");
                } else if ($row["gioitinh"] == 1) {
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, "Nam");
                } else {
                    $excel->getActiveSheet()->setCellValue('F' . $numRow, "Null");
                }

                $excel->getActiveSheet()->getStyle("A" . $numRow . ":F" . "$numRow")->getAlignment()->applyFromArray(
                    array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );;
                $numRow++;
            }
            ob_start();
            $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $write->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'status' => TRUE,
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );

            die(json_encode($response));
        }
    }

    public function getGroupSize()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mahocphan = $_POST['mahocphan'];
            $result = $this->hocphanModel->getGroupSize($mahocphan);
            echo $result;
        }
    }

    public function kickUser()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mahocphan = $_POST['mahocphan'];
            $mssv = $_POST['manguoidung'];
            $result = $this->hocphanModel->kickUser($mahocphan, $mssv);
            echo $result;
        }
    }
}
