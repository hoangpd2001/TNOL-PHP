<nav class="nav border-bottom bg-white position-fixed top-0 w-100 ">

    <div class="container d-flex justify-content-between align-items-center py-2">
        <button id="btn-thoat" class="btn btn-hero btn-danger" role="button"><i class="fa fa-power-off"></i>
            Thoát</button>
        <div class="nav-center">
            <span class="fw-bold fs-5">Tự ôn luyện</span>
        </div>
        <div class="nav-right d-flex align-items-center">
            <div class="nav-time me-4">
                <span class="fw-bold"><i class="far fa-clock mx-2"></i><span id="timer">00:00:00</span></span>
            </div>
            <button id="btn-nop-bai" class="btn btn-hero btn-primary" role="button"><i class="far fa-file-lines me-1"></i> Nộp bài</button>
        </div>
    </div>
</nav>
<div class="container mb-5 mt-6" id="dethicontent" data-id="<?php echo $data['mamon']; ?>"
    data-thoigian="<?php echo $data['thoigian']; ?>"
    data-chuongs="<?php echo $data['chuongs']; ?>"
    data-socau="<?php echo $data['socau']; ?>">
    <div class="row">
        <div class="col-8" id="list-question">
        </div>
        <div class="col-4 bg-white p-3 rounded border h-100 sidebar-answer">
            <ul class="answer">
            </ul>
        </div>
    </div>
</div>

<script>
    const dethidata = <?php echo json_encode($data); ?>;
</script>