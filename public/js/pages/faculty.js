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

  $("#add_faculty").on("click", function () {
    let makhoa = $("#makhoa").val();
    if ($(".form-add-faculty").valid() && checkTonTai(makhoa)) {
      $.ajax({
        type: "post",
        url: "./faculty/add",
        data: {
          makhoa: makhoa,
          tenkhoa: $("#tenkhoa").val(),
          magiaovien: $("#tengiaovien").val(),
        },
        success: function (response) {
          if (response) {
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: "Thêm khoa thành công!",
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
              message: "Thêm khoa không thành công!",
            });
          }
        },
      });
    }
  });

  $(document).on("click", ".btn-edit-faculty", function () {
    $(".update-faculty-element").show();
    $(".add-faculty-element").hide();
    let makhoa = $(this).data("id");
    console.log("makhoa", makhoa);
     loadUser(function () {
    $.ajax({
      type: "post",
      url: "./faculty/getDetail",
      data: {
        makhoa: makhoa,
      },
      dataType: "json",
      success: function (response) {
        console.log("response", response);
        if (response) {
          $("#makhoa").val(response.makhoa),
            $("#tenkhoa").val(response.tenkhoa),
            $("#tengiaovien").val(response.magiaovien).trigger("change"),
            $("#modal-add-faculty").modal("show"),
            $("#update_faculty").data("id", response.makhoa);
        }
      },
    });
    });
  });

  // Đóng modal thì reset form
  $("#modal-add-faculty").on("hidden.bs.modal", function () {
    $("#makhoa").val(""),
      $("#tenkhoa").val(""),
      $("#tengiaovien").val(""),
      $("#update_faculty").data("id", "");
  });
  $("#open-modal-add-faculty").click(function (e) {
    loadUser(function () {});
  });
  $("#update_faculty").click(function (e) {
    e.preventDefault();
    let makhoa = $(this).data("id");
    if ($(".form-add-faculty").valid()) {
      $.ajax({
        type: "post",
        url: "./faculty/update",
        data: {
          makhoa: makhoa,
          tenkhoa: $("#tenkhoa").val(),
          magiaovien: $("#tengiaovien").val(),
        },
        success: function (response) {
          if (response) {
            console.log("response", response);
            $("#modal-add-faculty").modal("hide");
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: "Cập nhật khoa thành công!",
            });
            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Cập nhật khoa không thành công!",
            });
          }
        },
      });
    }
  });

  $(document).on("click", ".btn-delete-faculty", function () {
    let trid = $(this).data("id");
    let e = Swal.mixin({
      buttonsStyling: !1,
      target: "#page-container",
      customClass: {
        confirmButton: "btn btn-success m-1",
        cancelButton: "btn btn-danger m-1",
        input: "form-control",
      },
    });

    e.fire({
      title: "Are you sure?",
      text: "Bạn có chắc chắn muốn xoá khoa?",
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
          url: "./faculty/delete",
          data: {
            makhoa: trid,
          },
          success: function (response) {
            if (response) {
              e.fire("Deleted!", "Xóa khoa thành công!", "success");
              mainPagePagination.getPagination(
                mainPagePagination.option,
                mainPagePagination.valuePage.curPage
              );
            } else {
              e.fire("Lỗi !", "Xoá khoa không thành công !)", "error");
            }
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
