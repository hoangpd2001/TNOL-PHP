<?php
require_once "./mvc/core/AuthCore.php";
require_once "./mvc/core/Response.php";
require_once "./mvc/core/Pagination.php";

use Core\Response;
class Course extends Controller
{
    private $khoaHocModel;

    public function __construct()
    {
        $this->khoaHocModel = $this->model("KhoaHocModel");
    }

    public function default()
    {
        if (AuthCore::checkPermission("monhoc", "view")) {
            $this->view("main_layout", [
                "Page" => "course",
                "Title" => "Quản lý khóa học",
                "Script" => "course",
                "Plugin" => [
                    "sweetalert2"     => 1,
                    "jquery-validate" => 1,
                    "select"          => 1,
                    "notify"          => 1,
                    "pagination"      => [],
                ]
            ]);
        } else {
            $this->view("single_layout", [
                "Page" => "error/page_403",
                "Title" => "Lỗi !"
            ]);
        }
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $tenkhoahoc = trim($_POST['tenkhoahoc'] ?? '');
        $trangthai = 1;

        if (empty($tenkhoahoc)) {
            return Response::json(false, 'Thiếu tên khóa học', null, 400);
        }

        if ($this->khoaHocModel->check($tenkhoahoc)) {
            return Response::json(false, 'Tên khóa học đã tồn tại');
        }

        $result = $this->khoaHocModel->create($tenkhoahoc, $trangthai);
        return Response::json(
            $result,
            $result ? 'Thêm khóa học thành công' : 'Thêm khóa học thất bại'
        );
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $makhoahoc = trim($_POST['makhoahoc'] ?? '');
        $tenkhoahoc = trim($_POST['tenkhoahoc'] ?? '');
        $trangthai = 1;

        if (empty($makhoahoc) || empty($tenkhoahoc)) {
            return Response::json(false, 'Thiếu dữ liệu', null, 400);
        }

        if ($this->khoaHocModel->check2($tenkhoahoc, $makhoahoc)) {
            return Response::json(false, 'Tên khóa học đã tồn tại');
        }

        $result = $this->khoaHocModel->update($makhoahoc, $tenkhoahoc, $trangthai);
        return Response::json(
            $result,
            $result ? 'Cập nhật khóa học thành công' : 'Cập nhật khóa học thất bại'
        );
    }

    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $makhoahoc = trim($_POST['makhoahoc'] ?? '');
        if (empty($makhoahoc)) {
            return Response::json(false, 'Thiếu mã khóa học', null, 400);
        }

        $result = $this->khoaHocModel->delete($makhoahoc);
        return Response::json(
            $result,
            $result ? 'Xóa khóa học thành công' : 'Xóa khóa học thất bại'
        );
    }

    public function getAll()
    {
        $data = $this->khoaHocModel->getAll();
        return Response::json(true, '', $data);
    }

    public function getDetail()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $makhoahoc = trim($_POST['makhoahoc'] ?? '');
        if (empty($makhoahoc)) {
            return Response::json(false, 'Thiếu mã khóa học', null, 400);
        }

        $data = $this->khoaHocModel->getById($makhoahoc);
        return Response::json(
            $data !== null,
            $data ? '' : 'Không tìm thấy thông tin khóa học',
            $data
        );
    }
}
