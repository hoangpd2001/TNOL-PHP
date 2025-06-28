<?php

class teacher_announcement extends Controller
{
    public $AnnouncementModel;
    public $mailAuthmodel;
    public $nhomModel;
    public $nguoidungModel;
    public $hocPhanModel;
    public function __construct()
    {
        $this->AnnouncementModel = $this->model("AnnouncementModel");
        $this->mailAuthmodel = $this->model("MailAuth");
        $this->nhomModel = $this->model("NhomModel");
        $this->hocPhanModel = $this->model("HocPhanModel");
        $this->nguoidungModel = $this->model("NguoiDungModel");
        parent::__construct();
        require_once "./mvc/core/Pagination.php";
    }

    public function default()
    {
        if (AuthCore::checkPermission("thongbao", "view")) {
            $this->view("main_layout", [
                "Page" => "teacher_announcement",
                "Title" => "Thông báo",
                "Script" => "announcement",
                "Plugin" => [
                    "sweetalert2" => 1,
                    "datepicker" => 1,
                    "flatpickr" => 1,
                    "notify" => 1,
                    "jquery-validate" => 1,
                    "select" => 1,
                    "pagination" => [],
                ]
            ]);
        } else $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
    }

    public function add()
    {
        if (AuthCore::checkPermission("thongbao", "create")) {
            $this->view("main_layout", [
                "Page" => "add_announce",
                "Title" => "Tạo và gửi thông báo",
                "Script" => "announcement",
                "Plugin" => [
                    "sweetalert2" => 1,
                    "datepicker" => 1,
                    "flatpickr" => 1,
                    "notify" => 1,
                    "jquery-validate" => 1,
                    "select" => 1,
                ],
                "Action" => "create"
            ]);
        } else $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
    }

    public function update($matb)
    {
        $check = $this->AnnouncementModel->getById($matb);
        if (isset($check)) {
            if ($check['nguoitao'] == $_SESSION['user_id'] && AuthCore::checkPermission("thongbao", "update")) {
                $this->view("main_layout", [
                    "Page" => "add_announce",
                    "Title" => "Cập nhật thông báo",
                    "Plugin" => [
                        "datepicker" => 1,
                        "flatpickr" => 1,
                        "select" => 1,
                        "notify" => 1,
                        "jquery-validate" => 1,
                        "sweetalert2" => 1,
                    ],
                    "Script" => "update_announce",
                    "Action" => "update"
                ]);
            } else {
                $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
            }
        } else {
            $this->view("single_layout", ["Page" => "error/page_404", "Title" => "Lỗi !"]);
        }
    }

    public function sendAnnouncement()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("thongbao", "create")) {
            $nguoitao = $_SESSION['user_id'];
            $manhoms = $_POST['manhom'];
            $content = $_POST['noticeText'];
            $thoigiantao = $_POST['thoigiantao'];
            $loaigiao = $_POST['loaigiao'];
            $valid = $this->AnnouncementModel->create($thoigiantao, $nguoitao, $manhoms, $content, $loaigiao);
            // Gom toàn bộ nhân sự & nhóm
            $user = $this->nguoidungModel->getById($nguoitao);
            foreach ($manhoms as $manhom) {
                // Ép về integer
                $manhom = (int)$manhom;
                if ($loaigiao == "1") {
                    $group = $this->hocPhanModel->getDetailGroup($manhom);
                    $students = $this->hocPhanModel->getSvList($manhom);
                } else {
                    $group = $this->nhomModel->getDetailGroup($manhom);
                    $students = $this->nhomModel->getSvList($manhom);
                }
                $this->mailAuthmodel->sendAnnouncement(
                    $content,                 
                    $thoigiantao,                    
                    $loaigiao,
                    $group,
                    $students,
                    $user,
                );
            }
            echo $valid;
        }
    }

    public function getAnnounce()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $manhom = $_POST["manhom"];
            $loaigiao = $_POST["loaigiao"];
            $result = $this->AnnouncementModel->getAnnounce($manhom, $loaigiao);
            echo json_encode($result);
        }
    }

    public function getDetail()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $matb = $_POST["matb"];
            $result = $this->AnnouncementModel->getDetail($matb);
            echo json_encode($result);
        }
    }

    public function getListAnnounce()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_id = $_SESSION['user_id'];
            $result = $this->AnnouncementModel->getAll($user_id);
            echo json_encode($result);
        }
    }

    public function deleteAnnounce()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("thongbao", "delete")) {
            $matb = $_POST["matb"];
            $result = $this->AnnouncementModel->deleteAnnounce($matb);
            echo json_encode($result);
        }
    }

    public function updateAnnounce()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("thongbao", "update")) {
            $matb = $_POST["matb"];
            $noidung = $_POST["noidung"];
            $manhom = $_POST["manhom"];
            $result = $this->AnnouncementModel->updateAnnounce($matb, $noidung, $manhom);
            echo json_encode($result);
        }
    }

    public function getNotifications()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = $_SESSION["user_id"];
            $result = $this->AnnouncementModel->getNotifications($id);
            echo json_encode($result);
        }
    }
}
