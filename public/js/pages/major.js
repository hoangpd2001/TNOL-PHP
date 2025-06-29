Dashmix.helpersOnLoad(["js-flatpickr", "jq-datepicker", "jq-select2"]);

Dashmix.onLoad(() =>
  class {
    static initValidation() {
      Dashmix.helpers("jq-validation");
      jQuery(".form-add-major").validate({
        rules: {
          manganh: {
            required: true,
            digits: true,
          },
          tennganh: {
            required: true,
          },

        },
        messages: {
          manganh: {
            required: "Vui lòng nhập mã ngành",
            digits: "Mã ngành phải là các ký tự số",
          },
          tennganh: {
            required: "Vui lòng cung cấp tên ngành",
          },
       
        },
      });
    }

    static init() {
      this.initValidation();
    }
  }.init()
);


let facultyMap = {};
function load(){
  $.get(
    "./faculty/getAll",
    function (data) {
      let html = "<option></option>";
      data.data.forEach((item) => {
        html += `<option value="${item.makhoa}">${item.tenkhoa}</option>`;
      });
      $("#main-page-khoa").html(html);

      data.data.forEach((item) => {
        facultyMap[item.makhoa] = item.tenkhoa;
      });
      //  showData(subjects);
    },
    "json"
  );
}
load();
function loadSelect2() {
  $(".js-select2").each(function () {
    const id = $(this).attr("id");
    $(this).select2({
      dropdownParent: $("#modal-add-major"), // Fix lỗi nhập trong modal
      width: "100%",
      placeholder: $(this).data("placeholder") || "", // Lấy từ attribute nếu có
      minimumResultsForSearch: 0,
    });
  });
}

$("#main-page-khoa").on("change", function () {
  let makhoa = $(this).val();
  // Reset filter
  //
  // Ajax call + pagination
  mainPagePagination.option.filter = {};
  mainPagePagination.option.filter.makhoa = makhoa;
  mainPagePagination.getPagination(
    mainPagePagination.option,
    mainPagePagination.valuePage.curPage
  );
});
function showData(majors) {
  let html = "";
  console.log("dang load roi", majors);
  majors.forEach((major) => {
    html += `<tr tid="${major.manganh}">
              <td class="text-center fs-sm"><strong>${major.manganh}</strong></td>
              <td>${major.tennganh}</td>
              <td class="d-none d-sm-table-cell fs-sm">${major.tenkhoa}</td>
              <td class="text-center col-action">
                  <a data-role="monhoc" data-action="update" class="btn btn-sm btn-alt-secondary btn-edit-major" href="javascript:void(0)"
                      data-bs-toggle="tooltip" aria-label="Sửa ngành" data-bs-original-title="Sửa ngành" data-id="${major.manganh}">
                      <i class="fa fa-fw fa-pencil"></i>
                  </a>
                  <a data-role="monhoc" data-action="delete" class="btn btn-sm btn-alt-secondary btn-delete-major" href="javascript:void(0)"
                      data-bs-toggle="tooltip" aria-label="Xoá ngành" data-bs-original-title="Xoá ngành" data-id="${major.manganh}">
                      <i class="fa fa-fw fa-times"></i>
                  </a>
              </td>
          </tr>`;
  });
  $("#list-major").html(html);
  $('[data-bs-toggle="tooltip"]').tooltip();
}
function loadFaculties() {
  $.get(
    "./faculty/getAll",
    function (data) {
    
      let html = "<option></option>";
      data.data.forEach((item) => {
        html += `<option value="${item.makhoa}">${item.tenkhoa}</option>`;
      });
      $("#tenkhoa").html(html);
    },
    "json"
  );
}
$(document).ready(function () {
  $("[data-bs-target='#modal-add-major']").click(function (e) {
    e.preventDefault();
    $(".update-major-element").hide();
    $(".add-major-element").show();
    $(".form-add-major")[0].reset();
    $("#tenkhoa").empty().append("<option></option>");
    loadFaculties(); // load khoa
  });
  // Khi modal đã hiển thị hoàn toàn thì khởi tạo Select2
  $("#modal-add-major").on("shown.bs.modal", function () {
   // loadSelect2();
  });



  $("#add_major").on("click", function () {
    if ($(".form-add-major").valid()) {
      $.ajax({
        type: "post",
        url: "./major/add",
        data: {
          tennganh: $("#tennganh").val(),
          makhoa: $("#tenkhoa").val(),
        },
        dataType: "json",
        success: function (response) {
          if (response.status) {
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: response.message || "Thêm ngành thành công!",
            });
            $("#modal-add-major").modal("hide");

            // Làm mới danh sách
            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: response.message || "Thêm ngành không thành công!",
            });
          }
        },
        error: function () {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: "Lỗi kết nối đến server!",
          });
        },
      });
    }
  });
  
  $(document).on("click", ".btn-edit-major", function () {
    $(".update-major-element").show();
    $(".add-major-element").hide();

    const manganh = $(this).data("id");

    $.ajax({
      type: "post",
      url: "./major/getDetail",
      data: { manganh },
      dataType: "json",
      success: function (response) {
        if (response.status && response.data) {
          // Gọi API lấy danh sách khoa
          $.get(
            "./faculty/getAll",
            function (faculties) {
              let html = "<option></option>";
              faculties.data.forEach((item) => {
                html += `<option value="${item.makhoa}">${item.tenkhoa}</option>`;
              });

              $("#tenkhoa")
                .html(html)
                .select2({
                  dropdownParent: $("#modal-add-major"),
                  width: "100%",
                  placeholder: $("#tenkhoa").data("placeholder") || "",
                  minimumResultsForSearch: 0,
                });

              // Set giá trị sau khi đã load xong khoa
              const major = response.data;
              $("#manganh").val(major.manganh);
              $("#tennganh").val(major.tennganh);
              $("#tenkhoa").val(major.makhoa).trigger("change");

              $("#update_major").data("id", major.manganh);
              $("#modal-add-major").modal("show");
            },
            "json"
          );
        } else {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: response.message || "Không tìm thấy ngành!",
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
  });
  
  

  // Đóng modal thì reset form
  $("#modal-add-major").on("hidden.bs.modal", function () {
    $("#manganh").val("");
    $("#tennganh").val("");
    $("#tenkhoa").val("");
    $("#update_major").data("id", "");
    load();
  });

  // $("#open-modal-add-major").click(function (e) {
  //   loadFaculties(function () {});
  // });

  $("#update_major").click(function (e) {
    e.preventDefault();
    let manganh = $(this).data("id");

    if ($(".form-add-major").valid()) {
      $.ajax({
        type: "post",
        url: "./major/update",
        data: {
          manganh: manganh,
          tennganh: $("#tennganh").val(),
          makhoa: $("#tenkhoa").val(),
        },
        dataType: "json",
        success: function (response) {
          if (response.status) {
            $("#modal-add-major").modal("hide");

            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: response.message || "Cập nhật ngành thành công!",
            });

            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: response.message || "Cập nhật ngành không thành công!",
            });
          }
        },
        error: function () {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: "Lỗi kết nối đến server!",
          });
        },
      });
    }
  });
  $(document).on("click", ".btn-delete-major", function () {
    const manganh = $(this).data("id");

    const swal = Swal.mixin({
      buttonsStyling: false,
      target: "#page-container",
      customClass: {
        confirmButton: "btn btn-danger m-1",
        cancelButton: "btn btn-secondary m-1",
        input: "form-control",
      },
    });

    swal
      .fire({
        title: "Bạn chắc chứ?",
        text: "Bạn có chắc chắn muốn xoá ngành?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Vâng, tôi chắc chắn!",
      })
      .then((result) => {
        if (result.value === true) {
          $.ajax({
            type: "post",
            url: "./major/delete",
            data: { manganh },
            dataType: "json",
            success: function (response) {
              if (response.status) {
                swal.fire(
                  "Đã xoá!",
                  response.message || "Xoá ngành thành công!",
                  "success"
                );
                mainPagePagination.getPagination(
                  mainPagePagination.option,
                  mainPagePagination.valuePage.curPage
                );
              } else {
                swal.fire(
                  "Lỗi!",
                  response.message || "Xoá ngành không thành công!",
                  "error"
                );
              }
            },
            error: function () {
              swal.fire("Lỗi!", "Không thể kết nối đến server!", "error");
            },
          });
        }
      });
  });
  
  
});

// Pagination
const mainPagePagination = new Pagination();
mainPagePagination.option.controller = "major";
mainPagePagination.option.model = "nganhModel";
mainPagePagination.option.limit = 10;
mainPagePagination.getPagination(
  mainPagePagination.option,
  mainPagePagination.valuePage.curPage
);
