<div class="content" data-id="<?php echo $data["user_id"] ?>">
    <form action="#" method="POST" id="search-form" onsubmit="return false;">
        <div class="row mb-4">
            <div class="col-6 flex-grow-1">
                <div class="input-group">
               
                    <input type="text" class="form-control" id="search-input" name="search-input" placeholder="Tìm kiếm đề thi...">
                </div>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end gap-3">
                <a data-role="dethi" data-action="create" class="btn btn-hero btn-primary" href="./test/add"><i class="fa fa-fw fa-plus me-1"></i> Tạo đề thi</a>
            </div>
        </div>
    </form>
    <div class="list-test" id="list-test">
    </div>
    <div class="row my-3">
        <?php if (isset($data["Plugin"]["pagination"])) require "./mvc/views/inc/pagination.php" ?>
    </div>
</div>
<div class="modal fade" id="modal-question-list" tabindex="-1" role="dialog" aria-labelledby="modal-question-list" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="block block-rounded block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Danh sách câu hỏi</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="block-content fs-sm" style="max-height: 70vh; overflow-y: auto;">
                    <div id="questions-container">
                        <!-- Danh sách câu hỏi sẽ được render ở đây -->
                    </div>
                </div>

                <div class="block-content block-content-full text-end bg-body">
                    <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>