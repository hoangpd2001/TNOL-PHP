Dashmix.helpersOnLoad(["js-flatpickr", "jq-datepicker", "jq-select2"]);

Dashmix.onLoad(() =>
  class {
    static initValidation() {
      Dashmix.helpers("jq-validation"),
        jQuery(".form-add-faculty").validate({
          rules: {
            makhoa: {
              required: !0,
              digits: true,
            },
            tenkhoa: {
              required: !0,
            },
            tengiaovien: {
              required: !0,
            },
          },
          messages: {
            makhoa: {
              required: "Vui lòng nhập mã khoa",
              digits: "Mã khoa học phải là các ký tự số",
            },
            tenkhoa: {
              required: "Vui lòng cung cấp tên khoa",
            },
            tengiaovien: {
              required: "Vui lòng cho biết trưởng khoa",
            },
          },
        });
    }

    static init() {
      this.initValidation();
    }
  }.init()
);
let userMap = {};

function loadUser(callback) {
  $.ajax({
    url: "./user/getDataByRole",
    type: "POST",
    data: {
      manhomquyen: 10,
    },
    dataType: "json",
    success: function (data) {
      let html = "<option></option>";
      data.forEach((item) => {
        html += `<option value="${item.id}">${item.hoten}</option>`;
      });

      const $select = $("#tengiaovien");

      // Reset select2 nếu đã khởi tạo
      if ($select.hasClass("select2-hidden-accessible")) {
        $select.select2("destroy");
      }

      // Cập nhật option
      $select.html(html);

      // Khởi tạo lại select2
      $select.select2({
        dropdownParent: $("#modal-add-faculty"),
        placeholder: "Chọn giáo viên",
        allowClear: true,
        width: "100%",
      });

      // Gọi callback sau khi load xong
      if (typeof callback === "function") {
        callback();
      }
    },
    error: function (xhr, status, error) {
      console.error("Lỗi khi loadUser:", error);
    },
  });
}
  
function showData(facultys) {
  let html = "";
    console.log("dang load roi", facultys);
  facultys.forEach((faculty) => {
    html += `<tr tid="${faculty.makhoa}">
              <td class="text-center fs-sm"><strong>${faculty.makhoa}</strong></td>
              <td>${faculty.tenkhoa}</td>
              <td class="d-none d-sm-table-cell fs-sm">${faculty.tengiaovien}</td>
              <td class="text-center col-action">
                  <a data-role="monhoc" data-action="update" class="btn btn-sm btn-alt-secondary btn-edit-faculty" href="javascript:void(0)"
                      data-bs-toggle="tooltip" aria-label="Sửa khoa" data-bs-original-title="Sửa khoa" data-id="${faculty.makhoa}">
                      <i class="fa fa-fw fa-pencil"></i>
                  </a>
                  <a data-role="monhoc" data-action="delete" class="btn btn-sm btn-alt-secondary btn-delete-faculty" href="javascript:void(0)"
                      data-bs-toggle="tooltip" aria-label="Xoá khoa" data-bs-original-title="Xoá khoa" data-id="${faculty.makhoa}">
                      <i class="fa fa-fw fa-times"></i>
                  </a>
              </td>
          </tr>`;
  });
  $("#list-faculty").html(html);
  $('[data-bs-toggle="tooltip"]').tooltip();
}

$(document).ready(function () {
  $("[data-bs-target='#modal-add-faculty']").click(function (e) {
    e.preventDefault();
    $(".update-faculty-element").hide();
    $(".add-faculty-element").show();
  });

  function checkTonTai(makhoa) {
    let check = true;
    $.ajax({
      type: "post",
      url: "./faculty/checkfaculty",
      data: {
        makhoa: makhoa,
      },
      async: false,
      dataType: "json",
      success: function (response) {
        if (response.length !== 0) {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: `khoa đã tồn tại!`,
          });
          check = false;
        }
      },
    });
    return check;
  }
  // Thêm khoa
  $("#add_faculty").on("click", function () {
    let makhoa = $("#makhoa").val();
    if ($(".form-add-faculty").valid() && checkTonTai(makhoa)) {
      $.ajax({
        type: "post",
        url: "./faculty/add",
        data: {
          tenkhoa: $("#tenkhoa").val(),
          magiaovien: $("#tengiaovien").val(),
        },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: res.message || "Thêm khoa thành công!",
            });
            $("#modal-add-faculty").modal("hide");
            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: res.message || "Thêm khoa không thành công!",
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

  // Nhấn nút sửa khoa
  $(document).on("click", ".btn-edit-faculty", function () {
    $(".update-faculty-element").show();
    $(".add-faculty-element").hide();
    let makhoa = $(this).data("id");

    loadUser(function () {
      $.ajax({
        type: "post",
        url: "./faculty/getDetail",
        data: { makhoa },
        dataType: "json",
        success: function (res) {
          if (res.status && res.data) {
            $("#makhoa").val(res.data.makhoa);
            $("#tenkhoa").val(res.data.tenkhoa);
            $("#tengiaovien").val(res.data.magiaovien).trigger("change");
            $("#modal-add-faculty").modal("show");
            $("#update_faculty").data("id", res.data.makhoa);
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: res.message || "Không tìm thấy thông tin khoa!",
            });
          }
        },
        error: function () {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: "Lỗi khi lấy thông tin khoa!",
          });
        },
      });
    });
  });

  // Reset form khi đóng modal
  $("#modal-add-faculty").on("hidden.bs.modal", function () {
    $("#makhoa").val("");
    $("#tenkhoa").val("");
    $("#tengiaovien").val("");
    $("#update_faculty").data("id", "");
    $(".add-faculty-element").show();
    $(".update-faculty-element").hide();
  });

  // Mở modal thêm
  $("#open-modal-add-faculty").click(function () {
    loadUser(function () {});
    $(".add-faculty-element").show();
    $(".update-faculty-element").hide();
    $("#modal-add-faculty").modal("show");
  });

  // Cập nhật khoa
  $("#update_faculty").click(function (e) {
    e.preventDefault();
    let makhoa = $(this).data("id");
    if ($(".form-add-faculty").valid()) {
      $.ajax({
        type: "post",
        url: "./faculty/update",
        data: {
          makhoa,
          tenkhoa: $("#tenkhoa").val(),
          magiaovien: $("#tengiaovien").val(),
        },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            $("#modal-add-faculty").modal("hide");
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: res.message || "Cập nhật khoa thành công!",
            });
            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: res.message || "Cập nhật khoa thất bại!",
            });
          }
        },
        error: function () {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: "Lỗi khi cập nhật khoa!",
          });
        },
      });
    }
  });

  // Xóa khoa
  $(document).on("click", ".btn-delete-faculty", function () {
    let makhoa = $(this).data("id");

    Swal.fire({
      title: "Bạn chắc chắn muốn xóa?",
      text: "Hành động này không thể hoàn tác!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Vâng, xóa!",
      cancelButtonText: "Hủy",
      customClass: {
        confirmButton: "btn btn-danger m-1",
        cancelButton: "btn btn-secondary m-1",
      },
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "post",
          url: "./faculty/delete",
          data: { makhoa },
          dataType: "json",
          success: function (res) {
            if (res.status) {
              Swal.fire(
                "Đã xóa!",
                res.message || "Xóa khoa thành công!",
                "success"
              );
              mainPagePagination.getPagination(
                mainPagePagination.option,
                mainPagePagination.valuePage.curPage
              );
            } else {
              Swal.fire("Lỗi!", res.message || "Xóa khoa thất bại!", "error");
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
const mainPagePagination = new Pagination();
mainPagePagination.option.controller = "faculty";
mainPagePagination.option.model = "khoaModel";
mainPagePagination.option.limit = 10;
mainPagePagination.getPagination(
  mainPagePagination.option,
  mainPagePagination.valuePage.curPage
);
