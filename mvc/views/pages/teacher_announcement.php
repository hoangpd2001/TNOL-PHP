<div class="content" data-id="<?php echo $_SESSION["user_id"] ?>">
    <form action="#" method="POST" id="search-form" onsubmit="return false;">
        <div class="row mb-4">
            <div class="col-6 flex-grow-1">
                <div class="input-group">
                    <button class="btn btn-alt-primary dropdown-toggle btn-filtered-by-state" id="dropdown-filter-state" type="button" data-bs-toggle="dropdown" aria-expanded="false">Tất cả</button>
                    <ul class="dropdown-menu mt-1" aria-labelledby="dropdown-filter-state">
                        <li><a class="dropdown-item filtered-by-state" href="javascript:void(0)" data-value="1">Học Phần</a></li>
                        <li><a class="dropdown-item filtered-by-state" href="javascript:void(0)" data-value="0">Nhóm</a></li>
                    </ul>
                    <input type="text" class="form-control" id="search-input" name="search-input" placeholder="Tìm kiếm thông báo...">
                </div>
            </div>
            <div data-role="thongbao" và data-action="create" class="col-6 d-flex align-items-center justify-content-end gap-3">
                <a class="btn btn-hero btn-primary" href="./teacher_announcement/add"><i class="fa fa-fw fa-plus me-1"></i> Tạo thông báo</a>
            </div>
        </div>
    </form>
    <div class="list-announces" id="list-announces">
        <!-- list  -->
    </div>
    <div class="row my-3">
        <?php if (isset($data["Plugin"]["pagination"])) require "./mvc/views/inc/pagination.php" ?>
    </div>
</div>
<script>
    // Lấy phần tử "Học" theo data-value
    const defaultItem = document.querySelector('.filtered-by-state[data-value="1"]');
    const filterBtn = document.getElementById("dropdown-filter-state");

    if (defaultItem && filterBtn) {
        // Đặt mặc định là "Học"
        filterBtn.textContent = defaultItem.textContent.trim();
        filterBtn.setAttribute("data-value", "1");

        // Nếu có sự kiện click custom, gọi thủ công
        if (typeof filterByState === "function") {
            filterByState(1);
        }
    }
</script>