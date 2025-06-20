<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Danh sách lớp học</h3>
            <div class="block-options">
                <button type="button" class="btn btn-hero btn-primary" data-bs-toggle="modal"
                    data-bs-target="#modal-add-classroom" data-role="monhoc" data-action="create">
                    <i class="fa-regular fa-plus"></i> Thêm lớp học
                </button>
            </div>
        </div>

        <div class="block-content">
            <form action="#" method="POST" id="search-form" onsubmit="return false;">
                <div class="row mb-3">
                    <div class="col-xl-2 d-flex gap-2 align-items-center pb-2">
                        <select class="js-select2 form-select" id="main-page-trangthai" name="main-page-trangthai"
                            data-placeholder="Trạng thái" data-tab="1">
                            <option value="">Tất cả</option>
                            <option value="1">Hoạt động</option>
                            <option value="0">Ngừng</option>
                        </select>
                    </div>

                    <div class="col-xl-3 d-flex gap-2 align-items-center pb-2">
                        <select class="js-select2 form-select" id="main-page-khoa" name="main-page-khoa"
                            data-placeholder="Chọn Khoa" data-tab="1">
                            <option></option>
                        </select>
                    </div>
                    <div class="col-xl-3 d-flex gap-2 align-items-center pb-2">
                        <select class="js-select2 form-select" id="main-page-nganh" name="main-page-nganh"
                            data-placeholder="Chọn Ngành" data-tab="1">
                            <option></option>
                        </select>
                    </div>
                    <div class="col-xl-2 d-flex gap-2 align-items-center pb-2">
                        <select class="js-select2 form-select" id="main-page-khoahoc" data-tab="1"
                            name="main-page-khoahoc" data-placeholder="Chọn Khóa">
                            <option></option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-alt" id="search-input" name="search-input"
                                placeholder="Tìm kiếm lớp học...">
                            <button class="input-group-text bg-body border-0 btn-search">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
            </form>

            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th class="text-center">Mã lớp</th>
                            <th>Tên lớp</th>
                            <th class="d-none d-sm-table-cell text-center">Số sinh viên</th>
                            <th class="d-none d-sm-table-cell text-center">Ngành học</th>
                            <th class="d-none d-sm-table-cell text-center">Khóa học</th>
                            <th class="d-none d-sm-table-cell text-center">Chủ nhiệm</th>
                            <th class="text-center col-header-action">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="list-classroom">
                        <!-- Danh sách lớp học sẽ load vào đây -->
                    </tbody>
                </table>
            </div>
            <?php if (isset($data["Plugin"]["pagination"])) require "./mvc/views/inc/pagination.php" ?>
        </div>
    </div>
</div>

<!-- Modal Thêm / Sửa Lớp -->
<div class="modal fade" id="modal-add-classroom" tabindex="-1" role="dialog" aria-labelledby="modal-add-classroom"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="block block-rounded block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title add-classroom-element">Thêm lớp học</h3>
                    <h3 class="block-title update-classroom-element">Chỉnh sửa lớp học</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <form class="block-content fs-sm form-add-classroom">
                    <!-- <div class="mb-3">
                        <label for="malop" class="form-label">Mã lớp học</label>
                        <input type="text" class="form-control form-control-alt" name="malop" id="malop"
                            placeholder="Nhập mã lớp học">
                    </div> -->
                    <div class="mb-3">
                        <label for="tenlop" class="form-label">Tên lớp học</label>
                        <input type="text" class="form-control form-control-alt" name="tenlop" id="tenlop" style="width: 100%;"
                            placeholder="Nhập tên lớp học">
                    </div>
                    <div class="mb-3">
                        <label for="tenkhoa" class="form-label d-block ">Tên khoa</label>
                        <select class="js-select2 form-select" id="tenkhoa" name="tenkhoa" style="width: 100%;"
                            data-placeholder="Chọn khoa">
                            <option></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tennganh" class="form-label d-block">Tên Ngành</label>
                        <select class="js-select2 form-select w-100" id="tennganh" name="tennganh" style="width: 100%;"
                            data-placeholder="Chọn ngành" data-tab="1">
                            <option></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tenkhoahoc" class="form-label d-block">Tên khóa học </label>
                        <select class="js-select2 form-select" id="tenkhoahoc" name="tenkhoahoc" style="width: 100%;"
                            data-placeholder="Chọn khóa học " data-tab="1">
                            <option></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tengiaovien" class="form-label d-block">Tên giáo viên </label>
                        <select class="js-select2 form-select" id="tengiaovien" name="tengiaovien" style="width: 100%;"
                            data-placeholder="Chọn giáo viên " data-tab="1">
                            <option></option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-5">
                        <label for="status" class="form-label">Trạng thái</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status">
                            <label class="form-check-label" for="status"></label>
                        </div>
                    </div>
                </form>
                <div class="block-content block-content-full text-end bg-body">
                    <button type="button" class="btn btn-sm btn-alt-secondary me-1"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-primary add-classroom-element"
                        id="add_classroom">Lưu</button>
                    <button type="button" class="btn btn-sm btn-primary update-classroom-element" id="update_classroom"
                        data-id="">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal danh sách sinh viên của lớp -->
<div class="modal fade" id="modal-student" tabindex="-1" role="dialog" aria-labelledby="modal-student"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document" style="max-width: 70%;">
        <div class=" modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Danh sách sinh viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-1">
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">MSSV</th>
                                <th>Họ và tên</th>
                                <th class="text-center">Ngày sinh</th>
                                <th class="text-center">Email</th>
                                <th class="text-center col-header-action">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="list-student">
                            <!-- Danh sách sinh viên thuộc lớp sẽ load ở đây -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>