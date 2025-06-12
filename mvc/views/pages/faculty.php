<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Danh sách Khoa</h3>
            <div class="block-options">
                <button type="button" class="btn btn-hero btn-primary" data-bs-toggle="modal" id="open-modal-add-faculty"
                    data-bs-target="#modal-add-faculty" data-role="monhoc" data-action="create"><i
                        class="fa-regular fa-plus"></i> Thêm Khoa</button>
            </div>
        </div>

        <div class="block-content">
            <form action="#" method="POST" id="search-form" onsubmit="return false;">

                <div class="row mb-3">
                    <div class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-alt" id="search-input" name="search-input"
                                placeholder="Tìm kiếm khoa...">
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
                            <th class="text-center">Mã khoa</th>
                            <th>Tên khoa</th>
                            <th>Trưởng khoa</th>
                            <th class="text-center col-header-action">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="list-faculty">
                    </tbody>
                </table>
            </div>
            <?php if (isset($data["Plugin"]["pagination"])) require "./mvc/views/inc/pagination.php" ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-add-faculty" tabindex="-1" role="dialog" aria-labelledby="modal-add-faculty"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="block block-rounded block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title add-faculty-element">Thêm Khoa</h3>
                    <h3 class="block-title update-faculty-element">Chỉnh sửa Khoa</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <form class="block-content fs-sm form-add-faculty">
                    <!-- <div class="mb-3">
                        <label for="" class="form-label">Mã Khoa</label>
                        <input type="text" class="form-control form-control-alt" name="makhoa" id="makhoa"
                            placeholder="Nhập mã môn học">
                    </div> -->
                    <div class="mb-3">
                        <label for="" class="form-label">Tên khoa</label>
                        <input type="text" class="form-control form-control-alt" name="tenkhoa" id="tenkhoa"
                            placeholder="Nhập tên Khoa">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Trưởng Khoa</label>
                        <br></br>
                        <select class="js-select2 form-select" id="tengiaovien" name="tengiaovien"
                            data-placeholder="Chọn giáo viên" data-tab="1">
                            <option></option>
                        </select>
                    </div>

                </form>
                <div class="block-content block-content-full text-end bg-body">
                    <button type="button" class="btn btn-sm btn-alt-secondary me-1"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-primary add-faculty-element"
                        id="add_faculty">Lưu</button>
                    <button type="button" class="btn btn-sm btn-primary update-faculty-element" id="update_faculty"
                        data-id="">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
</div>