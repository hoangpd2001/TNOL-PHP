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

  // Thêm khóa học
  $("#add_course").on("click", function () {
    if ($(".form-add-course").valid()) {
      $.ajax({
        type: "post",
        url: "./course/add",
        data: {
          tenkhoahoc: $("#tenkhoahoc").val(),
          magiaovien: $("#giaovienphutrach").val(), // nếu có dùng ở controller
        },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: res.message || "Thêm khóa học thành công!",
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
              message: res.message || "Thêm khóa học thất bại!",
            });
          }
        },
        error: function () {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: "Lỗi kết nối tới server!",
          });
        },
      });
    }
  });

  // Nhấn nút sửa
  $(document).on("click", ".btn-edit-course", function () {
    $(".update-course-element").show();
    $(".add-course-element").hide();
    const makhoahoc = $(this).data("id");

    $.ajax({
      type: "post",
      url: "./course/getDetail",
      data: { makhoahoc },
      dataType: "json",
      success: function (res) {
        if (res.status && res.data) {
          $("#makhoahoc").val(res.data.makhoahoc);
          $("#tenkhoahoc").val(res.data.tenkhoahoc);
          $("#giaovienphutrach")
            .val(res.data.magiaovien || "")
            .trigger("change");
          $("#update_course").data("id", res.data.makhoahoc);
          $("#modal-add-course").modal("show");
        } else {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: res.message || "Không tìm thấy khóa học!",
          });
        }
      },
      error: function () {
        Dashmix.helpers("jq-notify", {
          type: "danger",
          icon: "fa fa-times me-1",
          message: "Lỗi khi lấy thông tin khóa học!",
        });
      },
    });
  });

  // Reset form khi đóng modal
  $("#modal-add-course").on("hidden.bs.modal", function () {
    $("#makhoahoc").val("");
    $("#tenkhoahoc").val("");
    $("#giaovienphutrach").val("").trigger("change");
    $("#update_course").data("id", "");
    $(".add-course-element").show();
    $(".update-course-element").hide();
  });

  // Mở modal thêm
  $("#open-modal-add-course").click(function () {
    $(".add-course-element").show();
    $(".update-course-element").hide();
    $("#modal-add-course").modal("show");
  });

  // Cập nhật khóa học
  $("#update_course").click(function (e) {
    e.preventDefault();
    let makhoahoc = $(this).data("id");
    if ($(".form-add-course").valid()) {
      $.ajax({
        type: "post",
        url: "./course/update",
        data: {
          makhoahoc,
          tenkhoahoc: $("#tenkhoahoc").val(),
          magiaovien: $("#giaovienphutrach").val(), // nếu controller có hỗ trợ
        },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            $("#modal-add-course").modal("hide");
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: res.message || "Cập nhật khóa học thành công!",
            });
            mainCoursePagination.getPagination(
              mainCoursePagination.option,
              mainCoursePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: res.message || "Cập nhật khóa học thất bại!",
            });
          }
        },
        error: function () {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: "Lỗi kết nối khi cập nhật!",
          });
        },
      });
    }
  });

  // Xoá khóa học
  $(document).on("click", ".btn-delete-course", function () {
    const makhoahoc = $(this).data("id");

    Swal.fire({
      title: "Bạn chắc chắn muốn xoá?",
      text: "Hành động này không thể hoàn tác!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Vâng, xoá!",
      cancelButtonText: "Hủy",
      customClass: {
        confirmButton: "btn btn-danger m-1",
        cancelButton: "btn btn-secondary m-1",
      },
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "post",
          url: "./course/delete",
          data: { makhoahoc },
          dataType: "json",
          success: function (res) {
            if (res.status) {
              Swal.fire(
                "Đã xoá!",
                res.message || "Xoá khóa học thành công!",
                "success"
              );
              mainCoursePagination.getPagination(
                mainCoursePagination.option,
                mainCoursePagination.valuePage.curPage
              );
            } else {
              Swal.fire(
                "Lỗi!",
                res.message || "Xoá khóa học thất bại!",
                "error"
              );
            }
          },
          error: function () {
            Swal.fire("Lỗi!", "Không thể kết nối đến server!", "error");
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
