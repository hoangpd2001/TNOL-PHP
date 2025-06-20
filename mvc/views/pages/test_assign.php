<style>
    #page-footer {
        display: none
    }
</style>
<form class="row g-0 flex-md-grow-1 form-taodethi">
    <div class="col-md-4 col-lg-5 col-xl-3 order-md-1 bg-white">
        <div class="content px-2">
            <div class="d-md-none push">
                <button type="button" class="btn w-100 btn-alt-primary" data-toggle="class-toggle"
                    data-target="#side-content" data-class="d-none">
                    CẤU HÌNH
                </button>
            </div>
            <div id="side-content" class="d-none d-md-block push">
                <h3 class="fs-5 mb-3">CẤU HÌNH</h3>

                <!-- Chọn loại nhóm / học phần -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Chế độ phân công</label>
                    <div class="btn-group w-100" role="group" aria-label="Chế độ phân công">

                        <input type="radio" class="btn-check" name="nhom_hocphan_mode" id="chon-hocphan" autocomplete="off" value="hocphan" checked>
                        <label class="btn btn-outline-primary w-50" for="chon-hocphan">Học phần</label>

                        <input type="radio" class="btn-check" name="nhom_hocphan_mode" id="chon-nhom" autocomplete="off" value="nhom">
                        <label class="btn btn-outline-primary w-50" for="chon-nhom">Nhóm</label>
                    </div>
                </div>

                <!-- Chọn loại đề thi -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Loại đề</label>
                    <div class="btn-group w-100" role="group" aria-label="Loại đề">
                        <input type="radio" class="btn-check" name="loaide_mode" id="onluyen" autocomplete="off" value="-1" checked>
                        <label class="btn btn-outline-success w-50" for="onluyen">Ôn luyện</label>
                        <input type="radio" class="btn-check" name="loaide_mode" id="dethi" autocomplete="off" value="1">
                        <label class="btn btn-outline-success w-50" for="dethi">Đề thi</label>


                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="col-md-8 col-lg-7 col-xl-9 order-md-0">
        <div class="content content-full">
            <form class="block block-rounded form-tao-de">
                <div class="block-header block-header-default">

                    <h3 class="block-title">Giao đề thi</h3>
                </div>
                <div class="mb-4" id="hocphan-wrapper">
                    <div class=" block block-rounded border">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Giao cho học phần </h3>
                            <div class="block-option">
                                <select class="js-select2 form-select" id="hocphan" name="hocphan"
                                    style="width: 100%;" data-placeholder="Chọn học phần giảng dạy...">
                                </select>
                            </div>
                        </div>
                        <div class="block-content pb-3">
                            <div class="row" id="list-hocphan">
                                <div class="text-center fs-sm"><img style="width:100px"
                                        src="./public/media/svg/empty_data.png" alt=""></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4" id="nhom-wrapper" style="display: none;">
                    <div class=" block block-rounded border">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Giao cho nhóm</h3>
                            <div class="block-option">
                                <select class="js-select2 form-select" id="nhom" name="nhom"
                                    style="width: 100%;" data-placeholder="Chọn nhóm  giảng dạy...">
                                </select>
                            </div>
                        </div>
                        <div class="block-content pb-3">
                            <div class="row" id="list-nhom">
                                <div class="text-center fs-sm"><img style="width:100px"
                                        src="./public/media/svg/empty_data.png" alt=""></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block block-content">
                    <div class="row mb-4">
                        <label class="form-label" for="time-start">Thời gian bắt
                            đầu</label>
                        <div class="col-xl-6">
                            <input type="text" class="js-flatpickr form-control" id="time-start" name="time-start"
                                data-enable-time="true" data-time_24hr="true" placeholder="Từ">
                        </div>
                        <div class="col-xl-6" id="time-end-wrapper">
                            <input type=" text" class="js-flatpickr form-control" id="time-end" name="time-end"
                                data-enable-time="true" data-time_24hr="true" placeholder="Đến">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="name-exam" class="form-label">Tên đề kiểm tra</label>
                            <select class="js-select2 form-select" id="name-exam" name="name-exam"
                                data-placeholder="Chọn đề kiểm tra" data-tab="1">
                                <option></option>
                            </select>
                        </div>
                    </div>
<!-- 
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">Thời gian làm bài</span>
                            <input type="number" class="form-control text-center" id="exam-time" name="exam-time"
                                placeholder="00">
                            <span class="input-group-text">phút</span>
                        </div>
                    </div> -->
            </form>

            <div class="mb-4">
                <?php
                echo '<button type="submit" class="btn btn-hero btn-primary" id="btn-add-test"><i class="fa fa-fw fa-plus me-1"></i> Xác nhận</button>';
                ?>
            </div>
        </div>
</form>
</div>
</div>
</d>