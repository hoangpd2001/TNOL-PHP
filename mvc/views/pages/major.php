<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Danh sách ngành học</h3>
            <div class="block-options">
                <button type="button" class="btn btn-hero btn-primary" data-bs-toggle="modal"
                    data-bs-target="#modal-add-major" data-role="monhoc" data-action="create"><i
                        class="fa-regular fa-plus"></i> Thêm ngành học</button>
            </div>
        </div>

        <div class="block-content">
            <form action="#" method="POST" id="search-form" onsubmit="return false;">
                <div class="row mb-3">
                    <div class="col-xl-4 d-flex gap-2 align-items-center pb-2">
                        <select class="js-select2 form-select" id="main-page-khoa" name="main-page-khoa"
                            data-placeholder="Chọn Khoa" data-tab="1">
                            <option></option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-alt" id="search-input" name="search-input"
                                placeholder="Tìm kiếm ngành học...">
                            <button class="input-group-text bg-body border-0 btn-search">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th class="text-center">Mã ngành</th>
                            <th>Tên ngành</th>
                            <th class="d-none d-sm-table-cell text-center">Khoa</th>
                            <th class="text-center col-header-action">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="list-major">
                        <!-- Dữ liệu ngành học sẽ được đổ vào đây -->
                    </tbody>
                </table>
            </div>
            <?php if (isset($data["Plugin"]["pagination"])) require "./mvc/views/inc/pagination.php" ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-add-major" tabindex="-1" role="dialog" aria-labelledby="modal-add-major"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="block block-rounded block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title add-major-element">Thêm ngành học</h3>
                    <h3 class="block-title update-major-element">Chỉnh sửa ngành học</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <form class="block-content fs-sm form-add-major">
                    <!-- <div class="mb-3">
                        <label for="manganh" class="form-label">Mã ngành học</label>
                        <input type="text" class="form-control form-control-alt" name="manganh" id="manganh"
                            placeholder="Nhập mã ngành học">
                    </div> -->
                    <div class="mb-3">
                        <label for="tennganh" class="form-label">Tên ngành học</label>
                        <input type="text" class="form-control form-control-alt" name="tennganh" id="tennganh"
                            placeholder="Nhập tên ngành học">
                    </div>
                    <div class="mb-3">
                        <label for="tenkhoa" class="form-label">Tên khoa</label>
                        <select class="js-select2 form-select" id="tenkhoa" name="tenkhoa"
                            data-placeholder="Chọn khoa" data-tab="1" style="width: 100%;">
                            <option></option>
                        </select>
                    </div>
                </form>
                <div class="block-content block-content-full text-end bg-body">
                    <button type="button" class="btn btn-sm btn-alt-secondary me-1"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-primary add-major-element"
                        id="add_major">Lưu</button>
                    <button type="button" class="btn btn-sm btn-primary update-major-element" id="update_major"
                        data-id="">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
</div>