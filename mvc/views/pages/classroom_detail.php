<div class="content" data-id="<?php echo $data['Detail']['mahocphan'] ?>">
    <div class="row">
        <div class="col-4 flex-grow-1">
            <form action="#" id="search-form" onsubmit="return false;">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="si si-magnifier"></i></span>
                    <input type="text" class="form-control" placeholder="Tìm kiếm sinh viên..." id="search-input"
                        name="search-input">
                </div>
            </form>
        </div>
        <div class="col-8 d-flex align-items-center justify-content-end gap-3">
            <button type="button" class="btn btn-sm btn-primary" id="exportStudents"><i
                    class="fa-solid fa-file me-1"></i>Xuất danh sách
                SV</button>
            <button type="button" class="btn btn-primary btn-sm" id="exportScores"><i
                    class="fa-solid fa-file me-1"></i>Xuất bảng
                điểm</button>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasSetting" aria-controls="offcanvasSetting"><i class="fa fa-cog"></i></button>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <?php echo  $data['Detail']['mamonhoc'] . " - " . $data['Detail']['tenmonhoc'] . " - " . $data['Detail']['tenlop'] . " - " . $data['Detail']['tenkhoahoc']   ?>
                    </h3>
                    <div class="block-options">
                        <div class="block-options-item">
                            <code>Sỉ số: <span class="number-participants"></span></code>
                        </div>
                    </div>
                </div>
                <div class="block-content">
                    <table class="table table-vcenter">
                        <thead>
                            <tr class="table-col-title">
                                <th class="text-center">STT</th>
                                <th class="col-sort" data-sort-column="hoten" data-sort-order="default">Họ tên</th>
                                <th class="text-center col-sort" data-sort-column="id" data-sort-order="default">Mã sinh
                                    viên</th>
                                <th class="text-center">Giới tính</th>
                                <th class="text-center">Ngày sinh</th>
                                <th class="text-center" style="width: 150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="list-student">
                        </tbody>
                    </table>
                    <?php if (isset($data["Plugin"]["pagination"]))
                        require "./mvc/views/inc/pagination.php" ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSetting" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">
            <?php echo  $data['Detail']['mamonhoc'] . " - " . $data['Detail']['tenmonhoc'] . " - " . $data['Detail']['tenlop'] . " - " . $data['Detail']['tenkhoahoc']   ?>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body content-side">
        <div class="block block-transparent pull-x pull-t mb-0">
            <ul class="nav nav-tabs nav-tabs-block nav-justified" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="so-settings-tab" data-bs-toggle="tab"
                        data-bs-target="#so-settings" role="tab" aria-controls="so-settings" aria-selected="true">
                        <i class="fa fa-fw fa-cog me-2"></i> Đề kiểm tra
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="so-profile-tab" data-bs-toggle="tab" data-bs-target="#so-profile"
                        role="tab" aria-controls="so-profile" aria-selected="false" tabindex="-1">
                        <i class="fa fa-fw fa-bell me-2"></i> Thông báo
                    </button>
                </li>
            </ul>
            <div class="block-content tab-content overflow-hidden">
                <div class="tab-pane pull-x fade fade-up show active" id="so-settings" role="tabpanel"
                    aria-labelledby="so-settings-tab" tabindex="0">
                    <div class="list-test px-2"></div>
                </div>
                <div class="tab-pane pull-x fade fade-up" id="so-profile" role="tabpanel"
                    aria-labelledby="so-profile-tab" tabindex="0">
                    <ul class="list-announce nav-items my-2">
                        <p class="text-center">Không có thông báo</p>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>