$(document).ready(function () {
  // Tải danh sách môn học khi vào trang
  loadHocPhan();

  // Khi chọn môn học thì tải chương
  $("#hocphan").on("change", function () {
    const mamon = $(this).val();
    if (mamon) {
      loadChuongByMon(mamon);
    } else {
      $("#list-hocphan").html(
        `<div class="text-center fs-sm"><img style="width:100px" src="./public/media/svg/empty_data.png" alt=""></div>`
      );
    }
  });

  // Hàm tải danh sách môn học (từ DB)
  function loadHocPhan() {
    $.ajax({
      url: "./subject/getDatabyUser", // Tùy đường dẫn PHP backend
      type: "GET",
      dataType: "json",
      success: function (res) {

        if (res.length > 0) {
          let htmlSelect = '<option value="">-- Chọn môn học --</option>';
          res.forEach((mh) => {
            htmlSelect += `<option value="${mh.mamonhoc}">${mh.tenmonhoc}</option>`;
          });
          $("#hocphan").html(htmlSelect);
        } else {
          $("#hocphan").html('<option value="">Không có môn học</option>');
        }
      },
      error: function (res) {
        console.log(res)
        Dashmix.helpers("jq-notify", {
          type: "danger",
          icon: "fa fa-times me-1",
          message: "Không tải được danh sách môn học!",
        });
      },
    });
  }

  // Hàm tải danh sách chương
  function loadChuongByMon(mamon) {
    console.log("📘 Đang tải chương cho môn:", mamon);
    $.ajax({
      url: "./subject/getAllChapter",
      type: "POST",
      data: {
        mamonhoc: mamon,
      },
      dataType: "json",
      success: function (res) {
        if (res.length > 0) {
          let html = `
            <div class="col-12 mb-2">
              <label class="form-check">
                <input class="form-check-input" type="checkbox" id="check-all-chuong">
                <span class="form-check-label fw-semibold text-primary">Chọn toàn bộ chương</span>
              </label>
            </div>
          `;

          res.forEach((chuong) => {
            html += `
              <div class="col-md-6 mb-2">
                <label class="form-check">
                  <input class="form-check-input chuong-check" type="checkbox" value="${chuong.machuong}">
                  <span class="form-check-label">${chuong.tenchuong}</span>
                </label>
              </div>`;
          });

          $("#list-hocphan").html(html);

          // Sự kiện chọn toàn bộ
          $("#check-all-chuong").on("change", function () {
            $(".chuong-check").prop("checked", this.checked);
          });

          // Sự kiện khi người dùng bỏ chọn 1 chương
          $(document)
            .off("change", ".chuong-check")
            .on("change", ".chuong-check", function () {
              if (!this.checked) {
                $("#check-all-chuong").prop("checked", false);
              } else if (
                $(".chuong-check:checked").length === $(".chuong-check").length
              ) {
                $("#check-all-chuong").prop("checked", true);
              }
            });
        } else {
          $("#list-hocphan").html(`
            <div class="text-center fs-sm">
              <img style="width:100px" src="./public/media/svg/empty_data.png" alt="">
              <p class="mt-2">Không có chương</p>
            </div>
          `);
        }
      },
      error: function (err) {
        console.error("❌ Lỗi load chương:", err);
        Dashmix.helpers("jq-notify", {
          type: "danger",
          icon: "fa fa-times me-1",
          message: "Không thể tải chương học!",
        });
      },
    });
  }
  
  // Xử lý nút xác nhận
  $("#btn-add-test").on("click", function (e) {
    e.preventDefault();
    const structure = $("#structure").val();
    const mamon = $("#hocphan").val();
    const chuongs = $(".chuong-check:checked")
      .map(function () {
        return $(this).val();
      })
      .get();

    if (!mamon || chuongs.length === 0 || !structure) {
      Dashmix.helpers("jq-notify", {
        type: "danger",
        icon: "fa fa-times me-1",
        message: "Vui lòng chọn đầy đủ môn học, chương và cấu trúc đề!",
      });
      return;
    }

    const [thoigian, socau] = structure.split("-");
    const query = $.param({
      mamon,
      chuongs: chuongs.join(","),
      thoigian,
      socau,
    });

    // Gọi server kiểm tra trước
    $.ajax({
      type: "POST",
      url: "./question/count",
      data: {
        chuongs: chuongs.join(","),
      },
      dataType: "json",
      success: function (res) {
        if (res.total < parseInt(socau)) {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: `Chỉ có ${res.total} câu hỏi, không đủ ${socau} câu!`,
          });
        } else {
          // Đủ thì chuyển trang
          window.location.href = `client/review_action?${query}`;
        }
      },
      error: function () {
        Dashmix.helpers("jq-notify", {
          type: "danger",
          icon: "fa fa-times me-1",
          message: "Lỗi khi kiểm tra số lượng câu hỏi!",
        });
      },
    });
  });
  
});
