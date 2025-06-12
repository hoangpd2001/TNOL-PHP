<div class="content">
    <div class="row">
        <div class="col-6 flex-grow-1">
            <div class="input-group">
                <button class="btn btn btn-alt-primary dropdown-toggle btn-filter" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">Đang giảng dạy</button>
                <ul class="dropdown-menu mt-1">
                    <li><a class="dropdown-item filter-search" href="javascript:void(0)" data-value="1">Đang giảng
                            dạy</a></li>
                    <li><a class="dropdown-item filter-search" href="javascript:void(0)" data-value="0">Đã ẩn</a></li>
                </ul>
                <input type="text" class="form-control" placeholder="Tìm kiếm nhóm..." id="form-search-group">
            </div>
        </div>
        <div class="col-6 d-flex align-items-center justify-content-end gap-3">
            <button type="button" class="btn btn-hero btn-primary" data-bs-toggle="modal"
                data-bs-target="#modal-add-group" data-role="hocphan" data-action="create"><i class="fa fa-fw fa-plus me-1"></i> Thêm học phần</button>
        </div>
    </div>
    <div class="class-group" id="class-group">
    </div>
</div>
<div class="modal fade" id="modal-add-group" tabindex="-1" role="dialog" aria-labelledby="modal-add-group"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="block block-rounded block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title add-group-element">Thêm nhóm</h3>
                    <h3 class="block-title update-group-element">Cập nhật thông tin nhóm</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <form class="block-content fs-sm form-add-group">
                    <div class="mb-3">
                        <label for="tenkhoa" class="form-label d-block">Tên khoa</label>
                        <select class="js-select2 form-select" id="ten-khoa" name="ten-khoa"
                            data-placeholder="Chọn khoa"
                            style="width: 100%;">
                            <option></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label d-block">Tên Ngành</label>
                        <select class="js-select2 form-select" id="ten-nganh" name="ten-nganh"
                            data-placeholder="Chọn ngành" data-tab="1"
                            style="width: 100%;">
                            <option></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label d-block">Tên khóa học</label>
                        <select class="js-select2 form-select" id="ten-khoahoc" name="ten-khoahoc"
                            data-placeholder="Chọn khóa học" data-tab="1"
                            style="width: 100%;">
                            <option></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label d-block">Tên môn học</label>
                        <select class="js-select2 form-select" id="ten-monhoc" name="ten-monhoc"
                            data-placeholder="Chọn môn học"
                            style="width: 100%;">

                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Tên giáo viên</label>
                        <select class="js-select2 form-select" id="ten-giaovien" name="ten-giaovien" style="width: 100%;"
                            data-placeholder="Chọn môn học">
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Ghi chú</label>
                        
                            <input type="text" class="form-control" name="ghi-chu" id="ghi-chu"
                                placeholder="Nhập ghi chú">
                        
                    </div>
                </form>

                <div class="block-content block-content-full text-end bg-body">
                    <button type="button" class="btn btn-sm btn-alt-secondary me-1"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-primary add-group-element" id="add-group">Lưu</button>
                    <button type="button" class="btn btn-sm btn-primary update-group-element" id="update-group"
                        data-id="">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
</div>

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