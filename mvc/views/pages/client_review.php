

<form class="row g-0 flex-md-grow-1 form-taodethi">
    <div class="col-md-8 col-lg-7 col-xl-9 order-md-0">
        <div class="content content-full">
            <form class="block block-rounded form-tao-de">
                <div class="block-header block-header-default">

                    <h3 class="block-title">Tự ôn luyện</h3>
                </div>
                <div class="mb-4" id="hocphan-wrapper">
                    <div class=" block block-rounded border">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Môn học </h3>
                            <div class="block-option">
                                <select class="js-select2 form-select" id="hocphan" name="hocphan"
                                    style="width: 100%;" data-placeholder="Chọn môn học ...">
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
                    <select class="form-select mb-4" id="structure">
                        <option value="15-10">15 phút - 10 câu</option>
                        <option value="30-20">30 phút - 20 câu</option>
                        <option value="45-30">45 phút - 30 câu</option>
                    </select>
            </form>
            <div class="mb-4">
                <?php
                echo '<button type="submit" class="btn btn-hero btn-primary" id="btn-add-test"><i class="fa fa-fw fa-plus me-1"></i> Xác nhận</button>';
                ?>
            </div>
        </div>
</form>
