<?php require "inc/head.php" ?>

<body>
    <!-- Page Container -->
    <div id="page-container" class="sidebar-dark side-scroll page-header-fixed page-header-glass main-content-boxed remember-theme">
        <!-- Header -->
        <header id="page-header">
            <div class="content-header">
                <div class="space-x-1 d-flex align-items-center space-x-2 animated zoomInRight">
                    <a class="link-fx fw-bold" href="home">
                        <i class="fa fa-fire text-primary"></i>
                        <span class="fs-4 text-dual">TNOL </span><span class="fs-4 text-primary">Hactech</span>
                    </a>
                </div>
                <div class="space-x-1">
                    <ul class="nav-main nav-main-horizontal nav-main-hover nav">
                        <li class="nav-main-item">
                            <a class="btn btn-hero rounded-pill btn-light px-3" data-toggle="class-toggle" data-target="#dark-mode-toggler" data-class="far fa" onclick="Dashmix.layout('dark_mode_toggle');" href="javascript:void(0)">
                                <i class="fa fa-moon" id="dark-mode-toggler"></i>
                            </a>
                        </li>
                        <?php 
                        if(!isset($_COOKIE['token'])) {
                            echo '<li class="nav-main-item">
                                <a class="btn btn-hero btn-light rounded-pill" href="auth/signin">
                                    <i class="fa fa-right-to-bracket me-2"></i>Đăng nhập
                                </a>
                            </li>';
                        } else {
                            echo '<li class="nav-main-item">
                                <a class="btn btn-hero btn-primary rounded-pill" href="dashboard">
                                    <i class="fa fa-rocket me-2"></i>Dashboard
                                </a>
                            </li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </header>
        <!-- END Header -->
        <!-- Main Container -->
        <main id="main-container">
            <!-- Hero -->
            <div class="hero bg-body-extra-light hero-bubbles hero-lg overflow-hidden">
                <div class="hero-inner">
                    <span class="hero-bubble hero-bubble-lg bg-warning" style="top: 20%; left: 10%;"></span>
                    <span class="hero-bubble bg-success" style="top: 20%; left: 80%;"></span>
                    <span class="hero-bubble hero-bubble-sm bg-xwork" style="top: 40%; left: 25%;"></span>
                    <span class="hero-bubble hero-bubble-lg bg-xmodern" style="top: 10%; left: 20%;"></span>
                    <span class="hero-bubble hero-bubble-lg bg-xeco" style="top: 30%; left: 90%;"></span>
                    <span class="hero-bubble hero-bubble-lg bg-danger" style="top: 35%; left: 20%;"></span>
                    <span class="hero-bubble hero-bubble-lg bg-warning" style="top: 60%; left: 35%;"></span>
                    <span class="hero-bubble bg-info" style="top: 60%; left: 80%;"></span>
                    <span class="hero-bubble hero-bubble-lg bg-xdream" style="top: 75%; left: 70%;"></span>
                    <span class="hero-bubble hero-bubble-lg bg-xpro" style="top: 75%; left: 10%;"></span>
                    <span class="hero-bubble bg-xplay" style="top: 90%; left: 90%;"></span>
                    <div class="position-relative d-flex align-items-center">
                        <div class="content content-full">
                            <div class="row g-6 w-100 py-7 overflow-hidden">
                                <div
                                    class="col-md-7 order-md-last py-4 d-md-flex align-items-md-center justify-content-md-end">
                                    <img class="img-fluid animated flipInX" src="./public/media/various/landing.png"
                                        alt="Hero Promo">
                                </div>
                                <div class="col-md-5 py-4 d-flex align-items-center invisible" data-toggle="appear"
                                    data-class="animated fadeInLeft">
                                    <div class="text-center text-md-start">
                                        <h1 class="fw-bold fs-2 mb-3">
                                            Hệ thống thi và tạo đề thi trắc nghiệm online tốt nhất.
                                        </h1>
                                        <p class="text-muted fw-medium mb-4">
                                            Hỗ trợ bạn các chức năng tốt nhất để dễ dàng tạo và quản lý ngân hàng câu
                                            hỏi,
                                            đề thi trắc nghiệm, bài giảng.
                                            Tổ chức các kỳ thi online, giao bài tập về nhà trên mọi nền tảng Web,
                                            Mobile...
                                        </p>
                                        <a class="btn btn-alt-primary py-2 px-3 m-1" href="auth/signin" target="_blank">
                                            <i class="fa fa-arrow-right opacity-50 me-1"></i> Tham gia ngay
                                        </a>
                                        <a class="btn btn-alt-secondary py-2 px-3 m-1 btn--scroll-to">
                                            <i class="fa fa-arrow-down opacity-50 me-1"></i> Tìm hiểu thêm
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->
            <!-- Section 1 -->
            <div class="position-relative bg-body-extra-light" id="section--1">
                <div class="position-absolute top-0 end-0 bottom-0 start-0 bg-body-light skew-y-1"></div>
                <div class="position-relative">
                    <div class="content content-full my-5">
                        <div class="row g-0 justify-content-center text-center">
                            <div class="col-xl-4 invisible" data-toggle="appear" data-class="animated flipInX">
                                <div class="w-100 py-4 px-2">
                                    <div class="d-inline-block bg-body-extra-light rounded p-1 mb-4">
                                        <div class="d-inline-block bg-xinspire-light rounded p-4">
                                            <i class="fa fa-cubes fa-2x text-xinspire-dark"></i>
                                        </div>
                                    </div>
                                    <h3 class="h4 fw-bold mb-1">
                                        Lưu trạng thái khi gặp sự cố
                                    </h3>
                                    <p class="fw-medium text-dark mb-0">
                                        Tính năng Lưu đáp án khi gặp sự cố giúp người dùng bảo vệ kết quả bài kiểm tra
                                        trắc nghiệm một cách dễ dàng và tiện lợi.
                                    </p>
                                </div>
                            </div>
                            <div class="col-xl-4 invisible" data-toggle="appear" data-class="animated flipInX">
                                <div class="w-100 py-4 px-2">
                                    <div class="d-inline-block bg-body-extra-light rounded p-1 mb-4">
                                        <div class="d-inline-block bg-xplay-lighter rounded p-4">
                                            <i class="fa fa-code fa-2x text-xplay"></i>
                                        </div>
                                    </div>
                                    <h3 class="h4 fw-bold mb-1">
                                        Tạo đề thi tự động
                                    </h3>
                                    <p class="fw-medium text-dark mb-0">
                                        Giúp nâng cao chính xác và hiệu quả của quá trình tạo đề thi, đồng thời tiết
                                        kiệm thời gian và công sức cho người dùng.
                                    </p>
                                </div>
                            </div>
                            <div class="col-xl-4 invisible" data-toggle="appear" data-class="animated flipInX">
                                <div class="w-100 py-4 px-2">
                                    <div class="d-inline-block bg-body-extra-light rounded p-1 mb-4">
                                        <div class="d-inline-block bg-xpro-lighter rounded p-4">
                                            <i class="fa fa-rocket fa-2x text-xpro"></i>
                                        </div>
                                    </div>
                                    <h3 class="h4 fw-bold mb-1">
                                        Phân loại câu hỏi
                                    </h3>
                                    <p class="fw-medium text-dark mb-0">
                                        Đưa ra các câu hỏi phù hợp với nhu cầu của người dùng, giúp tạo ra bài kiểm tra
                                        trắc nghiệm chất lượng và hiệu quả.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Section 1 -->

            <!-- Section 2 -->
            <div class="bg-body-extra-light">
                <div class="content content-full">
                    <div class="row">
                        <div class="order-md-1 col-md-6 d-flex align-items-center justify-content-center">
                            <img src="./public/media/various/landing_1.png" alt="" class="feature__img invisible"
                                data-toggle="appear" data-class="animated fadeInRight">
                        </div>
                        <div class="order-md-0 col-md-6 d-flex align-items-center">
                            <div>
                                <h3 class="h3 mb-4 fw-bolder invisible" data-toggle="appear"
                                    data-class="animated fadeInUp" data-offset="-200">
                                    Dễ dàng tạo bài thi online
                                </h3>
                                <p class="mb-4 invisible" data-toggle="appear" data-class="animated fadeInUp"
                                    data-offset="-200">Hệ thống dễ dàng tạo đề thi với nhiều tuỳ chọn, giúp giảng viên
                                    tạo
                                    đề thi nhanh chóng</p>
                                <ul class="text-dark mb-4 m-0 list-unstyled">
                                    <li class="list-landing invisible" data-toggle="appear"
                                        data-class="animated fadeInUp" data-offset="-200" data-timeout="200">Tạo bài
                                        kiểm tra với nhiều dạng câu hỏi</li>
                                    <li class="list-landing invisible" data-toggle="appear"
                                        data-class="animated fadeInUp" data-offset="-200" data-timeout="400">Câu hỏi
                                        chọn 1 kết quả</li>
                                    <li class="list-landing invisible" data-toggle="appear"
                                        data-class="animated fadeInUp" data-offset="-200" data-timeout="600">Trả lời
                                        đoạn văn ngắn</li>
                                    <li class="list-landing invisible" data-toggle="appear"
                                        data-class="animated fadeInUp" data-offset="-200" data-timeout="800">Trả lời
                                        bằng cách upload file (Hình ảnh, word, video)
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content content-full">
                    <div class="row">
                        <div class="order-md-0 col-md-6 d-flex align-items-center justify-content-center">
                            <img src="./public/media/various/landing_2.png" alt="" class="feature__img invisible"
                                data-toggle="appear" data-class="animated fadeInLeft">
                        </div>
                        <div class="order-md-1 col-md-6 d-flex align-items-center">
                            <div>
                                <h3 class="h3 mb-4 fw-bolder invisible" data-toggle="appear"
                                    data-class="animated fadeInUp" data-offset="-200">
                                    Lên trước lịch làm bài hoặc giới hạn thời gian làm bài thi
                                </h3>
                                <p class="invisible" data-toggle="appear" data-class="animated fadeInUp"
                                    data-offset="-200">
                                    Bạn có thể cài đặt thời gian để học sinh chỉ làm bài trong khung thời gian qui
                                    định:
                                </p>
                                <ul class="text-dark mb-4 m-0 list-unstyled">
                                    <li class="list-landing invisible" data-toggle="appear"
                                        data-class="animated fadeInUp" data-offset="-200" data-timeout="200">Qui định
                                        thời gian cho bài làm</li>
                                    <li class="list-landing invisible" data-toggle="appear"
                                        data-class="animated fadeInUp" data-offset="-200" data-timeout="400">Qui định
                                        thời gian có thể bắt đầu làm bài</li>
                                    <li class="list-landing invisible" data-toggle="appear"
                                        data-class="animated fadeInUp" data-offset="-200" data-timeout="600">Qui định
                                        thời gian kết thúc hiệu lực làm bài</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content content-full">
                    <div class="row">
                        <div class="order-md-1 col-md-6 d-flex align-items-center justify-content-center">
                            <img src="./public/media/various/landing_3.png" alt="" class="feature__img invisible"
                                data-toggle="appear" data-class="animated fadeInRight">
                        </div>
                        <div class="order-md-0 col-md-6 d-flex align-items-center">
                            <div>
                                <h3 class="h3 mb-4 fw-bolder invisible" data-toggle="appear"
                                    data-class="animated fadeInUp" data-offset="-200">
                                    Học sinh làm bài không cần cài đặt thêm ứng dụng
                                </h3>
                                <p class="mb-2 invisible" data-toggle="appear" data-class="animated fadeInUp"
                                    data-offset="-200">Hệ thống tạo đề thi trắc nghiệm của chúng tôi cho phép học sinh
                                    làm bài
                                    kiểm tra trực tuyến dễ dàng mà không cần cài đặt thêm ứng dụng.</p>
                                <ul class="text-dark mb-4 m-0 list-unstyled">
                                    <li class="list-landing invisible" data-toggle="appear"
                                        data-class="animated fadeInUp" data-offset="-200" data-timeout="200">Bảo mật
                                        thông tin</li>
                                    <li class="list-landing invisible" data-toggle="appear"
                                        data-class="animated fadeInUp" data-offset="-200" data-timeout="400">Giao diện
                                        làm bài kiểm tra trực quan và dễ tương tác
                                    </li>
                                    <li class="list-landing invisible" data-toggle="appear"
                                        data-class="animated fadeInUp" data-offset="-200" data-timeout="600">Giao diện
                                        tuỳ biến theo kích thước màn hình </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

        

                <!-- Footer Copyright -->
                <div class="row fs-sm pt-4">
                    <div class="col-md-6 offset-md-3 text-center">
                        Copyright © 2023 HactechVN. All rights reserved.
                    </div>
                </div>
                <!-- END Footer Copyright -->
            </div>
        </footer>
        <!-- END Footer -->
    </div>
    <!-- END Page Container -->

    <?php require "inc/script.php"?>
</body>

</html>