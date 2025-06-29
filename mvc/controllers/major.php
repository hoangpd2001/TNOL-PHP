<?php
require_once "./mvc/core/AuthCore.php";
require_once "./mvc/core/Response.php";
require_once "./mvc/core/Pagination.php";

use Core\Response;
class Major extends Controller
{
    private $majorModel;

    public function __construct()
    {
        $this->majorModel = $this->model("NganhModel");
    }

    public function default()
    {
        if (AuthCore::checkPermission("monhoc", "view")) {
            $this->view("main_layout", [
                "Page" => "major",
                "Title" => "Quản lý ngành học",
                "Script" => "major",
                "Plugin" => [
                    "sweetalert2"     => 1,
                    "jquery-validate" => 1,
                    "select"          => 1,
                    "notify"          => 1,
                    "pagination"      => [],
                ]
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_403", "Title" => "Lỗi !"]);
        }
    }

    public function add()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $tennganh = trim($_POST['tennganh'] ?? '');
        $makhoa   = trim($_POST['makhoa'] ?? '');

        if (empty($tennganh) || empty($makhoa)) {
            return Response::json(false, 'Thiếu dữ liệu ngành hoặc khoa', null, 400);
        }

        if ($this->majorModel->check($tennganh, $makhoa)) {
            return Response::json(false, 'Tên ngành đã tồn tại trong khoa này');
        }

        $result = $this->majorModel->create($tennganh, $makhoa, 1);
        return Response::json(
            $result,
            $result ? 'Thêm ngành học thành công' : 'Thêm ngành học thất bại'
        );
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $manganh = trim($_POST['manganh'] ?? '');
        $tennganh = trim($_POST['tennganh'] ?? '');
        $makhoa = trim($_POST['makhoa'] ?? '');

        if (empty($manganh) || empty($tennganh) || empty($makhoa)) {
            return Response::json(false, 'Thiếu dữ liệu cập nhật', null, 400);
        }

        if ($this->majorModel->check2($tennganh, $makhoa, $manganh)) {
            return Response::json(false, 'Tên ngành đã tồn tại trong khoa này');
        }

        $result = $this->majorModel->update($manganh, $tennganh, $makhoa, 1);
        return Response::json(
            $result,
            $result ? 'Cập nhật ngành học thành công' : 'Cập nhật ngành học thất bại'
        );
    }

    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $manganh = trim($_POST['manganh'] ?? '');
        if (empty($manganh)) {
            return Response::json(false, 'Thiếu mã ngành', null, 400);
        }

        $result = $this->majorModel->delete($manganh);
        return Response::json(
            $result,
            $result ? 'Xoá ngành học thành công' : 'Xoá ngành học thất bại'
        );
    }

    public function getAll()
    {
        $data = $this->majorModel->getAll();
        return Response::json(true, '', $data);
    }

    public function getAllFaculty()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $makhoa = trim($_POST['makhoa'] ?? '');
        if (empty($makhoa)) {
            return Response::json(false, 'Thiếu mã khoa', null, 400);
        }

        $data = $this->majorModel->getAllFaculty($makhoa);
        return Response::json(true, '', $data);
    }

    public function getDetail()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $manganh = trim($_POST['manganh'] ?? '');
        if (empty($manganh)) {
            return Response::json(false, 'Thiếu mã ngành', null, 400);
        }

        $data = $this->majorModel->getById($manganh);
        return Response::json(
            $data !== null,
            $data ? '' : 'Không tìm thấy thông tin ngành học',
            $data
        );
    }
}
