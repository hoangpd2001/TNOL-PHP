Dashmix.helpersOnLoad(["js-flatpickr", "jq-datepicker", "jq-select2"]);

Dashmix.onLoad(() =>
  class {
    static initValidation() {
      Dashmix.helpers("jq-validation"),
        jQuery(".form-add-course").validate({
          rules: {
            makhoahoc: {
              required: !0,
              digits: true,
            },
            tenkhoahoc: {
              required: !0,
            },
            giaovienphutrach: {
              required: !0,
            },
          },
          messages: {
            makhoahoc: {
              required: "Vui lòng nhập mã khoá học",
              digits: "Mã khoá học phải là các ký tự số",
            },
            tenkhoahoc: {
              required: "Vui lòng nhập tên khoá học",
            },
            giaovienphutrach: {
              required: "Vui lòng chọn giáo viên phụ trách",
            },
          },
        });
    }

    static init() {
      this.initValidation();
    }
  }.init()
);
function showData(courses) {
  let html = "";
  courses.forEach((course) => {
    html += `<tr tid="${course.makhoahoc}">
              <td class="text-center fs-sm"><strong>${course.makhoahoc}</strong></td>
              <td>${course.tenkhoahoc}</td>
            
              <td class="text-center col-action">
                  <a class="btn btn-sm btn-alt-secondary btn-edit-course" href="javascript:void(0)"
                      data-bs-toggle="tooltip" data-id="${course.makhoahoc}" aria-label="Sửa" title="Sửa">
                      <i class="fa fa-fw fa-pencil"></i>
                  </a>
                  <a class="btn btn-sm btn-alt-secondary btn-delete-course" href="javascript:void(0)"
                      data-bs-toggle="tooltip" data-id="${course.makhoahoc}" aria-label="Xoá" title="Xoá">
                      <i class="fa fa-fw fa-times"></i>
                  </a>
              </td>
          </tr>`;
  });
  $("#list-course").html(html);
  $('[data-bs-toggle="tooltip"]').tooltip();
}

$(document).ready(function () {
  $("[data-bs-target='#modal-add-course']").click(function (e) {
    e.preventDefault();
    $(".update-course-element").hide();
    $(".add-course-element").show();
  });

  function checkCourseExists(makhoahoc) {
    let check = true;
    $.ajax({
      type: "post",
      url: "./course/checkCourse",
      data: {
        makhoahoc: makhoahoc,
      },
      async: false,
      dataType: "json",
      success: function (response) {
        if (response.length !== 0) {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: "Mã khoá học đã tồn tại!",
          });
          check = false;
        }
      },
    });
    return check;
  }

  $("#add_course").on("click", function () {
    let makhoahoc = $("#makhoahoc").val();
    if ($(".form-add-course").valid() && checkCourseExists(makhoahoc)) {
      $.ajax({
        type: "post",
        url: "./course/add",
        data: {
          makhoahoc: makhoahoc,
          tenkhoahoc: $("#tenkhoahoc").val(),
          magiaovien: $("#giaovienphutrach").val(),
        },
        success: function (response) {
          if (response) {
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: "Thêm khoá học thành công!",
            });
            $("#modal-add-course").modal("hide");
            mainCoursePagination.getPagination(
              mainCoursePagination.option,
              mainCoursePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Thêm khoá học thất bại!",
            });
          }
        },
      });
    }
  });

  $(document).on("click", ".btn-edit-course", function () {
    $(".update-course-element").show();
    $(".add-course-element").hide();
    let makhoahoc = $(this).data("id");


      $.ajax({
        type: "post",
        url: "./course/getDetail",
        data: {
          makhoahoc: makhoahoc,
        },
        dataType: "json",
        success: function (response) {
          if (response) {
            $("#makhoahoc").val(response.makhoahoc),
              $("#tenkhoahoc").val(response.tenkhoahoc),
              $("#giaovienphutrach").val(response.magiaovien).trigger("change"),
              $("#modal-add-course").modal("show"),
              $("#update_course").data("id", response.makhoahoc);
          }
        },
    
    });
  });

  $("#modal-add-course").on("hidden.bs.modal", function () {
    $("#makhoahoc").val(""),
      $("#tenkhoahoc").val(""),
      $("#giaovienphutrach").val("").trigger("change"),
      $("#update_course").data("id", "");
  });

  $("#open-modal-add-course").click(function () {
   
  });

  $("#update_course").click(function (e) {
    e.preventDefault();
    let makhoahoc = $(this).data("id");
    if ($(".form-add-course").valid()) {
      $.ajax({
        type: "post",
        url: "./course/update",
        data: {
          makhoahoc: makhoahoc,
          tenkhoahoc: $("#tenkhoahoc").val(),
          magiaovien: $("#giaovienphutrach").val(),
        },
        success: function (response) {
          if (response) {
            $("#modal-add-course").modal("hide");
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: "Cập nhật khoá học thành công!",
            });
            mainCoursePagination.getPagination(
              mainCoursePagination.option,
              mainCoursePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Cập nhật thất bại!",
            });
          }
        },
      });
    }
  });

  $(document).on("click", ".btn-delete-course", function () {
    let trid = $(this).data("id");
    let swal = Swal.mixin({
      buttonsStyling: !1,
      target: "#page-container",
      customClass: {
        confirmButton: "btn btn-success m-1",
        cancelButton: "btn btn-danger m-1",
        input: "form-control",
      },
    });

    swal
      .fire({
        title: "Bạn chắc chứ?",
        text: "Bạn có chắc chắn muốn xoá khoá học?",
        icon: "warning",
        showCancelButton: !0,
        confirmButtonText: "Vâng, tôi chắc chắn!",
      })
      .then((result) => {
        if (result.value === true) {
          $.ajax({
            type: "post",
            url: "./course/delete",
            data: {
              makhoahoc: trid,
            },
            success: function (response) {
              if (response) {
                swal.fire(
                  "Đã xoá!",
                  "Khoá học đã được xoá thành công!",
                  "success"
                );
                mainCoursePagination.getPagination(
                  mainCoursePagination.option,
                  mainCoursePagination.valuePage.curPage
                );
              } else {
                swal.fire("Lỗi!", "Xoá khoá học thất bại!", "error");
              }
            },
          });
        }
      });
  });
});

// Pagination
const mainCoursePagination = new Pagination();
mainCoursePagination.option.controller = "course";
mainCoursePagination.option.model = "KhoaHocModel";
mainCoursePagination.option.limit = 10;
mainCoursePagination.getPagination(
  mainCoursePagination.option,
  mainCoursePagination.valuePage.curPage
);
