<style>
    #page-footer {
        display: none
    }
</style>
<form class="row g-0 flex-md-grow-1 form-taothongbao" onsubmit="return false;">
    <div class="col-md-8 col-lg-7 col-xl-9 col-custom-9 order-md-0">
        <div class="content content-full">
            <form class="block block-rounded form-tao-de" novalidate="novalidate">
                <div class="block-header block-header-default">
                    <?php
                    $title = "";
                    if ($data["Action"] == "create") $title = "Tạo mới và gửi thông báo";
                    else if ($data["Action"] == "update") $title = "Cập nhật thông báo";
                    ?>
                    <h3 class="block-title"><?php echo $title ?></h3>
                </div>
                <div class="block block-content">
                    <div class="form-floating mb-4">
                        <textarea class="form-control" id="name-exam" name="name-exam"
                            style="height: 100px" placeholder="Nhập nội dung thông báo"></textarea>
                        <label class="form-label" for="name-exam">Nội dung thông báo</label>
                    </div>

                    <div class="mb-4">
                        <div class="block block-rounded border">
                            <div class="block-header block-header-default d-flex align-items-center justify-content-between">
                                <h3 class="block-title">Thông báo cho</h3>

                                <!-- Radio lựa chọn loại nhận -->
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="loaigiao" id="radio-nhom" value="0" checked>
                                        <label class="form-check-label" for="radio-nhom">Nhóm</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="loaigiao" id="radio-hocphan" value="1">
                                        <label class="form-check-label" for="radio-hocphan">Học phần</label>
                                    </div>
                                </div>
                            </div>

                            <div class="block-content pb-3">
                                <select class="js-select2 form-select" id="nhom-hp" name="nhom-hp"
                                    style="width: 100%;" data-placeholder="Chọn nhóm hoặc học phần..."
                                    <?php if ($data["Action"] == "update") echo "disabled" ?>>
                                </select>
                                <div class="row mt-2" id="list-group" name="list-group">
                                    <div class="text-center fs-sm"><img style="width:100px"
                                            src="./public/media/svg/empty_data.png" alt=""></div>
                                </div>
                            </div>
                        </div>

                        <div class="block-content pb-3">
                            <div class="row" id="list-group" name="list-group">
                                <div class="text-center fs-sm"><img style="width:100px"
                                        src="./public/media/svg/empty_data.png" alt=""></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <?php
                    if ($data["Action"] == "create") echo '<button type="submit" class="btn btn-hero btn-primary" id="btn-send-announcement"><i class="fa fa-fw fa-plus me-1"></i>Gửi thông báo</button>';
                    else if ($data["Action"] == "update") echo '<button type="submit" class="btn btn-hero btn-primary" id="btn-update-announce"><i class="fa fa-fw fa-plus me-1"></i>Cập nhật thông báo</button>';
                    ?>
                </div>
        </div>
</form>
</div>
</div>
</form>