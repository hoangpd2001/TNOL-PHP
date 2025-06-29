function dateIsValid(date) {
  return !Number.isNaN(new Date(date).getTime());
}

function showListTest(tests) {
  console.log(tests);
  console.log("setest:");
  const format = new Intl.DateTimeFormat(navigator.language, {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });
  html = ``;
  if (tests.length == 0) {
    html += `<p class="text-center">Không có dữ liệu</p>`;
    $(".pagination").hide();
  } else {
    tests.forEach((test) => {
      let htmlTestState = ``;

      const state = {};
      const now = test.trangthai;
      if (now == -1) {
        state.color = "secondary";
        state.text = "Đề ôn luyện";
      } else if (now == 0) {
        state.color = "primary";
        state.text = "Đề thi chờ duyệt";
      } else {
        state.color = "danger";
        state.text = "Đề thi";
      }
      const total =
        Number(test.socaude) + Number(test.socautb) + Number(test.socaukho);
      htmlTestState += `<button class="btn btn-sm btn-alt-${state.color} rounded-pill px-3 me-1 my-1" disabled>${state.text}</button>`;
      html += `<div class="block block-rounded block-fx-pop mb-2">
                  <div class="block-content block-content-full border-start border-3 border-${state.color}">
                      <div class="d-md-flex justify-content-md-between align-items-md-center">
                          <div class="p-1 p-md-3">
                              <h3 class="h4 fw-bold mb-3">
                                  <a href="./test/detail/${test.made}" class="text-dark link-fx">${test.tende}</a>
                              </h3>
                              <p class="fs-sm text-muted mb-1">
                                  <i class="fa fa-layer-group me-1"></i></i> Môn học: <strong data-bs-toggle="tooltip" data-bs-animation="true" data-bs-placement="top" title="${test.nhom}" style="cursor:pointer">${test.tenmonhoc}</strong>
                              </p>
                               <p class="fs-sm text-muted mb-1">
                                  <i class="fa fa-user me-1"></i> Người tạo: <span>${test.nguoitao}-${test.hoten}</span> 
                              </p>
                              <p class="fs-sm text-muted mb-1">
                                  <i class="fa fa-calendar me-1"></i> Ngày tạo: <span>${test.thoigiantao}</span> 
                              </p>
                              <p class="fs-sm text-muted mb-1">
                                  <i class="fa fa-question-circle me-1"></i> Số câu hỏi: <span class="me-4">${total}     </span> 
                                  <i class="fa fa-clock  me-1"></i> Thời gian: <span>${test.thoigianthi} Phút</span> 
                              </p>
                          </div>
                          <div class="p-1 p-md-3">
                            
                            <a class="btn btn-sm btn-alt-success rounded-pill px-3 me-1 my-1 btn-detail-test" data-made="${test.made}">
      <i class="fa fa-eye opacity-50 me-1"></i> Xem chi tiết
  </a>
  
                              <a data-role="dethi" data-action="update" class="btn btn-sm btn-alt-primary rounded-pill px-3 me-1 my-1" href="./test/update/${test.made}">
                                  <i class="fa fa-wrench opacity-50 me-1"></i> Chỉnh sửa
                              </a>
                    <button 
                        type="button" 
                        class="btn btn-sm btn-alt-success rounded-pill px-3 me-1 my-1 btn-update-test" 
                        data-made="${test.made}">
                        <i class="fa fa-check opacity-50 me-1"></i> Duyệt đề
                        </button>


                              <a data-role="dethi" data-action="delete" class="btn btn-sm btn-alt-danger rounded-pill px-3 my-1 btn-delete" href="javascript:void(0)" data-id="${test.made}">
                                  <i class="fa fa-times opacity-50 me-1"></i> Xoá đề
                              </a>
                              
                          </div>
                      </div>
                  </div>
          </div>`;
    });
  }
  $("#list-test").html(html);
  $('[data-bs-toggle="tooltip"]').tooltip();
}

$(document).ready(function () {
  let e = Swal.mixin({
    buttonsStyling: !1,
    target: "#page-container",
    customClass: {
      confirmButton: "btn btn-success m-1",
      cancelButton: "btn btn-danger m-1",
      input: "form-control",
    },
  });

  $(document).on("click", ".btn-delete", function () {
    let index = $(this).data("index");
    e.fire({
      title: "Are you sure?",
      text: "Bạn có chắc chắn muốn xoá đề thi?",
      icon: "warning",
      showCancelButton: !0,
      customClass: {
        confirmButton: "btn btn-danger m-1",
        cancelButton: "btn btn-secondary m-1",
      },
      confirmButtonText: "Vâng, tôi chắc chắn!",
      html: !1,
      preConfirm: (e) =>
        new Promise((e) => {
          setTimeout(() => {
            e();
          }, 50);
        }),
    }).then((t) => {
      if (t.value == true) {
        $.ajax({
          type: "post",
          url: "./test/delete",
          data: {
            made: $(this).data("id"),
          },
          dataType: "json",
          success: function (response) {
            if (response) {
              e.fire("Deleted!", "Xóa đề thi thành công!", "success");
              // dethi.splice(index,1);
              // showListTest(dethi);
              mainPagePagination.getPagination(
                mainPagePagination.option,
                mainPagePagination.valuePage.curPage
              );
            } else {
              e.fire("Lỗi !", "Xoá đề thi không thành công !)", "error");
            }
          },
        });
      }
    });
  });

  $(".filtered-by-state").click(function (e) {
    e.preventDefault();
    $(".btn-filtered-by-state").text($(this).text());
    const state = $(this).data("value");
    if (state != "3") {
      mainPagePagination.option.filter = state;
    } else {
      delete mainPagePagination.option.filter;
    }

    mainPagePagination.getPagination(
      mainPagePagination.option,
      mainPagePagination.valuePage.curPage
    );
  });

  $(document).on("click", ".btn-detail-test", function (e) {
    e.preventDefault();
    console.log("duoc goi");

    const made = $(this).data("made");
    console.log("Được gọi, mã đề:", made);

    $.ajax({
      type: "post",
      url: "./test/getQuestionsOfTestManual",
      data: { made: made },
      dataType: "json",
      success: function (data) {
        console.log("Data getQuestionsOfTestManual:", data);
        showQuestions(data);
      },
      error: function (xhr, status, error) {
        console.error("Lỗi khi gọi API:", error);
      },
    });
  });
  function showQuestions(data) {
    $("#questions-container").empty();

    data.forEach((q) => {
      const answersHtml = q.cautraloi
        .map(
          (a, idx) => `
              <div class="form-check">
                  <input class="form-check-input" type="radio" name="q${
                    q.macauhoi
                  }" id="a${a.macautl}" disabled>
                  <label class="form-check-label" for="a${a.macautl}">
                      ${String.fromCharCode(65 + idx)}. ${a.noidungtl}
                  </label>
              </div>
          `
        )
        .join("");

      const questionHtml = `
              <div class="card mb-3">
                  <div class="card-body">
                      <div class="mb-2"><strong>Câu ${q.thutu}</strong> </div>
                      <div class="mb-2">${q.noidung}</div>
                      <div>${answersHtml}</div>
                  </div>
              </div>
          `;

      $("#questions-container").append(questionHtml);
    });

    // ✅ Hiển thị modal
    const modal = new bootstrap.Modal(
      document.getElementById("modal-question-list")
    );
    modal.show();
  }

  $(document).on("click", ".btn-update-test", function (e) {
    e.preventDefault();
    console.log("duoc goi");

    const made = $(this).data("made");
    console.log("Được gọi, mã đề:", made);

    $.ajax({
      type: "post",
      url: "./test/updateApproveTest",
      data: { made: made },
      dataType: "json",
      success: function (data) {
        if (data ) {
          Swal.fire("Thành công!", "Duyệt đề thi thành công!", "success");
          // Làm mới danh sách đề thi
          mainPagePagination.getPagination(
            mainPagePagination.option,
            mainPagePagination.valuePage.curPage
          );
        } else {
          Swal.fire("Lỗi!", "Duyệt đề thi không thành công!", "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("Lỗi AJAX:", error);
        Swal.fire("Lỗi!", "Không thể kết nối đến server!", "error");
      },
    });
  });
  
});

// Get current user ID
const container = document.querySelector(".content");
const currentUser = container.dataset.id;
delete container.dataset.id;

// Pagination
const mainPagePagination = new Pagination(null, null, showListTest);
mainPagePagination.option.controller = "test";
mainPagePagination.option.model = "DeThiModel";
mainPagePagination.option.id = currentUser;
mainPagePagination.option.custom.function = "getAllCreatedTestBaseApprove";
mainPagePagination.getPagination(
  mainPagePagination.option,
  mainPagePagination.valuePage.curPage
);
