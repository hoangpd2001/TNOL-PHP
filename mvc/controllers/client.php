<?php
class Client extends Controller{

    public $nhommodel;
    public $dethimodel;
    public $nguoidungmodel;
    public $hocPhanmodel;
    public function __construct()
    {
        $this->nhommodel = $this->model("NhomModel");
        $this->hocPhanmodel = $this->model("HocPhanModel");
        $this->dethimodel = $this->model("DeThiModel");
        $this->nguoidungmodel = $this->model("NguoiDungModel");
        parent::__construct();
        require_once "./mvc/core/Pagination.php";
    }

    public function group()
    {
        if (AuthCore::checkPermission("tghocphan", "join")) {
            $this->view("main_layout", [
                "Page" => "client_group",
                "Title" => "Nhóm",
                "Script" => "client_group",
                "Plugin" => [
                    "jquery-validate" => 1,
                    "notify" => 1,
                    "datepicker" => 1,
                    "flatpickr" => 1,
                    "sweetalert2" => 1,
                    "select" => 1,
                ]
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
        }
    }
    public function module()
    {
        if (AuthCore::checkPermission("tghocphan", "join")) {
            $this->view("main_layout", [
                "Page" => "client_module",
                "Title" => "Học phần",
                "Script" => "client_module",
                "Plugin" => [
                    "jquery-validate" => 1,
                    "notify" => 1,
                    "datepicker" => 1,
                    "flatpickr" => 1,
                    "sweetalert2" => 1,
                    "select" => 1,
                ]
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
        }
    }
    public function review_action()
    {
        $mamon = $_GET['mamon'] ?? null;
        $chuongs = $_GET['chuongs'] ?? null;
        $thoigian = $_GET['thoigian'] ?? null;
        $socau = $_GET['socau'] ?? null;
        if (AuthCore::checkPermission("tghocphan", "join")) {
            $this->view("single_layout", [
                "Page" => "client_review_action",
                "Title" => "Học phần",
                "mamon" => $mamon,
                "chuongs" => $chuongs,
                "thoigian" => $thoigian,
                "socau" =>$socau,
                "Script" => "client_review_action",
                "Plugin" => [
                
                    "sweetalert2" => 1,
                
                    
                ]
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
        }
    }
    
    public function autoreview()
    {
        if (AuthCore::checkPermission("tghocphan", "join")) {
            $this->view("main_layout", [
                "Page" => "client_review",
                "Title" => "Ôn luyện",
                "Script" => "client_review",
                "Plugin" => [
                    "jquery-validate" => 1,
                    "notify" => 1,
                    "datepicker" => 1,
                    "flatpickr" => 1,
                    "sweetalert2" => 1,
                    "select" => 1,
                ]
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
        }
    } 
    public function test() {
        if (AuthCore::checkPermission("tgthi", "join")) {
            $this->view("main_layout", [
                "Page" => "client_schedule_test",
                "Title" => "Lịch kiểm tra",
                "Script" => "client_schedule_test",
                "user_id" => $_SESSION['user_id'],
                "Plugin" => [
                    "pagination" => [],
                ],
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
        }
    }
    public function review()
    {
        if (AuthCore::checkPermission("tgthi", "join")) {
            $this->view("main_layout", [
                "Page" => "client_schedule_review",
                "Title" => "Lịch kiểm tra",
                "Script" => "client_schedule_review",
                "user_id" => $_SESSION['user_id'],
                "Plugin" => [
                    "pagination" => [],
                ],
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
        }
    }
    // /client/test pagination
    public function getQuery($filter, $input, $args) {
        AuthCore::checkAuthentication();
        $query = $this->dethimodel->getQuery($filter, $input, $args);
        return $query;
    }

    public function joinGroup()
    {
        if($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("tghocphan", "join")) {
            $mamoi = $_POST['mamoi'];
            $manguoidung = $_SESSION['user_id'];
            $result_manhom = $this->nhommodel->getIdFromInvitedCode($mamoi);
            if($result_manhom != null) {
                $manhom = $result_manhom['manhom'];
                $result = $this->nhommodel->join($manhom,$manguoidung);
                if($result) {
                    echo json_encode($this->nhommodel->getDetailGroup($manhom));
                }
                else echo json_encode(1);
            } else echo json_encode(0);
        }
    }
    
    public function loadDataGroups() {
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $manguoidung = $_SESSION['user_id'];
            $hienthi = $_POST['hienthi'];
            $result = $this->nhommodel->getAllGroup_User($manguoidung,$hienthi);
            echo json_encode($result);
        }
    }
    public function loadDataModule()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $manguoidung = $_SESSION['user_id'];
            $hienthi = $_POST['hienthi'];
            $result = $this->hocPhanmodel->getAllModule_User($manguoidung, $hienthi);
            echo json_encode($result);
        }
    }

    public function getFriendList()
    {
        AuthCore::checkAuthentication();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $manguoidung = $_SESSION['user_id'];
            $manhom = $_POST['manhom'];
            $result = $this->nhommodel->getSvList($manhom);
            $index = -1;
            $i = 0;
            while($i <= count($result) && $index == -1) {
                if($result[$i]['id'] == $manguoidung) {
                    $index = $i;
                } else $i++;
            }
            array_splice($result, $index, 1);
            echo json_encode($result);
        }
    }

    public function hide()
    {
        if($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("tghocphan", "join")) {
            $manhom = $_POST['manhom'];
            $giatri =$_POST['giatri'];
            $manguoidung = $_SESSION['user_id'];
            $result = $this->nhommodel->sv_hide($manhom,$manguoidung,$giatri);
            echo $result;
        } else echo json_encode(false);
    }

    public function delete()
    {
        if($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("tghocphan", "join")) {
            $manhom = $_POST['manhom'];
            $manguoidung = $_SESSION['user_id'];
            $result = $this->nhommodel->SVDelete($manhom,$manguoidung);
            echo $result;
        } else echo json_encode(false);
    }    
}
?>