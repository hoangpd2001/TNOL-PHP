<?php
require_once "./mvc/core/AuthCore.php";
require_once "./mvc/core/Response.php";
require_once "./mvc/core/Pagination.php";

use Core\Response;
class Faculty extends Controller
{
    private $khoaModel;

    public function __construct()
    {
        $this->khoaModel = $this->model("KhoaModel");
    }

    public function default()
    {
        if (!AuthCore::checkPermission("monhoc", "view")) {
            return Response::json(false, 'Forbidden', null, 403);
        }

        // Render view
        $this->view("main_layout", [
            "Page"   => "faculty",
            "Title"  => "Quản lý Môn học",
            "Script" => "faculty",
            "Plugin" => [
                "sweetalert2"     => 1,
                "jquery-validate" => 1,
                "select"         => 1,
                "notify"         => 1,
                "pagination"     => [],
            ]
        ]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $tenkhoa    = trim($_POST['tenkhoa'] ?? '');
        $magiaovien = trim($_POST['magiaovien'] ?? '');

        if (empty($tenkhoa) || empty($magiaovien)) {
            return Response::json(false, 'Thiếu dữ liệu', null, 400);
        }

        if ($this->khoaModel->check($tenkhoa)) {
            return Response::json(false, 'Tên khoa đã tồn tại');
        }

        $created = $this->khoaModel->create($tenkhoa, $magiaovien);
        return Response::json(
            $created,
            $created ? 'Thêm khoa thành công' : 'Thêm khoa thất bại'
        );
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $makhoa     = trim($_POST['makhoa'] ?? '');
        $tenkhoa    = trim($_POST['tenkhoa'] ?? '');
        $magiaovien = trim($_POST['magiaovien'] ?? '');

        if (empty($makhoa) || empty($tenkhoa) || empty($magiaovien)) {
            return Response::json(false, 'Thiếu dữ liệu', null, 400);
        }

        if ($this->khoaModel->check2($tenkhoa, $makhoa)) {
            return Response::json(false, 'Tên khoa đã tồn tại');
        }

        $updated = $this->khoaModel->update($makhoa, $tenkhoa, $magiaovien);
        return Response::json(
            $updated,
            $updated ? 'Cập nhật thành công' : 'Cập nhật thất bại'
        );
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }

        $makhoa = trim($_POST['makhoa'] ?? '');
        if (empty($makhoa)) {
            return Response::json(false, 'Thiếu mã khoa', null, 400);
        }

        $deleted = $this->khoaModel->delete($makhoa);
        return Response::json(
            $deleted,
            $deleted ? 'Xóa khoa thành công' : 'Xóa khoa thất bại'
        );
    }

    public function getAll()
    {
        $data = $this->khoaModel->getAll();
        return Response::json(true, '', $data);
    }

    public function getDetail()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return Response::json(false, 'Method Not Allowed', null, 405);
        }
        $makhoa = trim($_POST['makhoa'] ?? '');
        if (empty($makhoa)) {
            return Response::json(false, 'Thiếu mã khoa', null, 400);
        }

        $detail = $this->khoaModel->getById($makhoa);
        return Response::json(
            $detail !== null,
            $detail ? '' : 'Không tìm thấy thông tin',
            $detail
        );
    }
}
