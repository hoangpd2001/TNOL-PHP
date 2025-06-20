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
$.get(
  "./faculty/getAll",
  function (data) {
    let html = "<option></option>";
    data.forEach((item) => {
      html += `<option value="${item.makhoa}">${item.tenkhoa}</option>`;
    });
   $("#main-page-khoa").html(html);

    data.forEach((item) => {
      facultyMap[item.makhoa] = item.tenkhoa;
    });
    //  showData(subjects);
  },
  "json"
);
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
      data.forEach((item) => {
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
    loadSelect2();
  });

  function checkTonTai(manganh) {
    let check = true;
    $.ajax({
      type: "post",
      url: "./major/checkmajor",
      data: {
        manganh: manganh,
      },
      async: false,
      dataType: "json",
      success: function (response) {
        if (response.length !== 0) {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: `Ngành đã tồn tại!`,
          });
          check = false;
        }
      },
    });
    return check;
  }

  $("#add_major").on("click", function () {


    if ($(".form-add-major").valid()) {
     
        $.ajax({
          type: "post",
          url: "./major/add",
          data: {
            tennganh: $("#tennganh").val(),
            makhoa: $("#tenkhoa").val(),
          },
          success: function (response) {
            if (response) {
              Dashmix.helpers("jq-notify", {
                type: "success",
                icon: "fa fa-check me-1",
                message: "Thêm ngành thành công!",
              });
              $("#modal-add-major").modal("hide");
              mainPagePagination.getPagination(
                mainPagePagination.option,
                mainPagePagination.valuePage.curPage
              );
            } else {
              Dashmix.helpers("jq-notify", {
                type: "danger",
                icon: "fa fa-times me-1",
                message: "Thêm ngành không thành công!",
              });
            }
          },
        });
    
    }
  });

  $(document).on("click", ".btn-edit-major", function () {
    $(".update-major-element").show();
    $(".add-major-element").hide();

    let manganh = $(this).data("id");
    console.log("manganh", manganh);

    $.ajax({
      type: "post",
      url: "./major/getDetail",
      data: {
        manganh: manganh,
      },
      dataType: "json",
      success: function (response) {
        console.log("response", response);
        if (response) {
          // Gọi load khoa, sau đó set giá trị makhoa
          $.get(
            "./faculty/getAll",
            function (data) {
              let html = "<option></option>";
              data.forEach((item) => {
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

              // Sau khi load xong thì set giá trị ngành
              $("#manganh").val(response.manganh);
              $("#tennganh").val(response.tennganh);
              $("#tenkhoa").val(response.makhoa).trigger("change");

              $("#update_major").data("id", response.manganh);
              $("#modal-add-major").modal("show");
            },
            "json"
          );
        }
      },
    });
  });
  

  // Đóng modal thì reset form
  $("#modal-add-major").on("hidden.bs.modal", function () {
    $("#manganh").val("");
    $("#tennganh").val("");
    $("#tenkhoa").val("");
    $("#update_major").data("id", "");
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
        success: function (response) {
          if (response) {
            console.log("response", response);
            $("#modal-add-major").modal("hide");
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: "Cập nhật ngành thành công!",
            });
            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Cập nhật ngành không thành công!",
            });
          }
        },
      });
    }
  });

  $(document).on("click", ".btn-delete-major", function () {
    let trid = $(this).data("id");
    let e = Swal.mixin({
      buttonsStyling: false,
      target: "#page-container",
      customClass: {
        confirmButton: "btn btn-success m-1",
        cancelButton: "btn btn-danger m-1",
        input: "form-control",
      },
    });

    e.fire({
      title: "Are you sure?",
      text: "Bạn có chắc chắn muốn xoá ngành?",
      icon: "warning",
      showCancelButton: true,
      customClass: {
        confirmButton: "btn btn-danger m-1",
        cancelButton: "btn btn-secondary m-1",
      },
      confirmButtonText: "Vâng, tôi chắc chắn!",
      html: false,
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
          url: "./major/delete",
          data: {
            manganh: trid,
          },
          success: function (response) {
            if (response) {
              e.fire("Deleted!", "Xóa ngành thành công!", "success");
              mainPagePagination.getPagination(
                mainPagePagination.option,
                mainPagePagination.valuePage.curPage
              );
            } else {
              e.fire("Lỗi !", "Xoá ngành không thành công !)", "error");
            }
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
