Dashmix.helpersOnLoad(["js-flatpickr", "jq-datepicker", "jq-select2"]);

Dashmix.onLoad(() =>
  class {
    static initValidation() {
      Dashmix.helpers("jq-validation"),
        jQuery(".form-add-classroom").validate({
          rules: {
            malop: {
              required: !0,
              digits: true,
            },
            "tenkhoa": {
              required: !0,
            },
          },
          messages: {
            malop: {
              required: "Vui lòng nhập mã môn học",
              digits: "Mã môn học phải là các ký tự số",
            },
            "tenkhoa": {
              required: "Vui lòng cung cấp tên môn học",
            },
          },
        });
    }

    static init() {
      this.initValidation();
    }
  }.init()
);


function loadSelect2() {
  $(".js-select2").each(function () {
    const id = $(this).attr("id");
    $(this).select2({
      dropdownParent: $("#modal-add-classroom"), // Fix lỗi nhập trong modal
      width: "100%",
      placeholder: $(this).data("placeholder") || "", // Lấy từ attribute nếu có
      minimumResultsForSearch: 0,
    });
  });
}

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
    //  showData(classroom);
  },
  "json"
);
let courseMap = {};
$.get(
  "./course/getAll",
  function (data) {
    let html = "<option></option>";
    data.forEach((item) => {
      html += `<option value="${item.makhoahoc}">${item.tenkhoahoc}</option>`;
    });
    $("#main-page-khoahoc").html(html);
    data.forEach((item) => {
      courseMap[item.makhoahoc] = item.tenkhoahoc;
    });
    //  showData(classroom);
  },
  "json"
);

$("#main-page-khoa").on("change", function () {
  let makhoa = $(this).val();
  // Reset filter
  //
  let id = $(this).data("tab");
  let html = "<option></option>";
  $.ajax({
    type: "post",
    url: "./major/getAllFaculty",
    data: {
      makhoa: makhoa,
    },
    dataType: "json",
    success: function (data) {
      data.forEach((item) => {
        html += `<option value="${item.manganh}">${item.tennganh}</option>`;
      });
      $(`#main-page-nganh[data-tab="${id}"]`).html(html);
    },
  });

  // Reset filter
  $("#main-page-makhoa").val(0).trigger("change");
  // Ajax call + pagination
  mainPagePagination.option.filter = {};
  mainPagePagination.option.filter.makhoa = makhoa;
  mainPagePagination.getPagination(
    mainPagePagination.option,
    mainPagePagination.valuePage.curPage
  );
});
$("#main-page-nganh").on("change", function () {
  const manganh = $(this).val();
  mainPagePagination.option.filter.manganh = manganh;
  mainPagePagination.getPagination(
    mainPagePagination.option,
    mainPagePagination.valuePage.curPage
  );
});

$("#main-page-khoahoc").on("change", function () {
  const makhoahoc = $(this).val();
  mainPagePagination.option.filter.makhoahoc = makhoahoc;
  mainPagePagination.getPagination(
    mainPagePagination.option,
    mainPagePagination.valuePage.curPage
  );
});
function showData(classroom) {
  let html = "";
  console.log(classroom);
  classroom.forEach((classroom) => {
    let tenkhoahoc = courseMap[classroom.makhoahoc] || "Không xác định";
    html += `<tr tid="${classroom.malop}">
              <td class="text-center fs-sm"><strong>${classroom.malop}</strong></td>
              <td>${classroom.tenlop}</td>
              <td class="d-none d-sm-table-cell text-center fs-sm">${classroom.tongsinhvien}</td>
              
             <td class="d-none d-sm-table-cell text-center fs-sm">${tenkhoahoc}</td>
              <td class="text-center col-action">
                  <a data-role="chuong" data-action="view" class="btn btn-sm btn-alt-secondary classroom-info" data-bs-toggle="modal" data-bs-target="#modal-student" href="javascript:void(0)"
                      data-bs-toggle="tooltip" aria-label="Thêm chương" data-bs-original-title="Chi tiết chương" data-id="${classroom.malop}">
                      <i class="fa fa-circle-info"></i>
                  </a>
                  <a data-role="monhoc" data-action="update" class="btn btn-sm btn-alt-secondary btn-edit-classroom" href="javascript:void(0)"
                      data-bs-toggle="tooltip" aria-label="Sửa môn học" data-bs-original-title="Sửa môn học" data-id="${classroom.malop}">
                      <i class="fa fa-fw fa-pencil"></i>
                  </a>
                  <a data-role="monhoc" data-action="delete" class="btn btn-sm btn-alt-secondary btn-delete-classroom" href="javascript:void(0)"
                      data-bs-toggle="tooltip" aria-label="Xoá môn học" data-bs-original-title="Xoá môn học" data-id="${classroom.malop}">
                      <i class="fa fa-fw fa-times"></i>
                  </a>
              </td>
          </tr>`;
  });
  $("#list-classroom").html(html);
  $('[data-bs-toggle="tooltip"]').tooltip();
}
function loadFaculties() {
  return new Promise((resolve) => {
    $.get(
      "./faculty/getAll",
      function (data) {
        let html = "<option></option>";
        data.forEach((item) => {
          html += `<option value="${item.makhoa}">${item.tenkhoa}</option>`;
        });
        $("#tenkhoa").html(html);
        resolve();
      },
      "json"
    );
  });
}

function loadMajorsByFaculty(makhoa) {
  return new Promise((resolve, reject) => {
    if (!makhoa) {
      console.error("Không có mã khoa để load ngành");
      return reject();
    }

    $.ajax({
      type: "POST",
      url: "./major/getAllFaculty",
      data: { makhoa },
      dataType: "json",
      success: function (data) {
        let html = "<option></option>";
        data.forEach((item) => {
          html += `<option value="${item.manganh}">${item.tennganh}</option>`;
        });
        $("#tennganh").html(html);
        resolve();
      },
      error: function () {
        console.error("Lỗi khi tải danh sách ngành theo khoa.");
        reject();
      },
    });
  });
}


function loadCourses() {
  return new Promise((resolve, reject) => {
    $.get(
      "./course/getAll",
      function (data) {
        let html = "<option></option>";
        data.forEach((item) => {
          html += `<option value="${item.makhoahoc}">${item.tenkhoahoc}</option>`;
        });
        $("#tenkhoahoc").html(html);
        resolve();
      },
      "json"
    ).fail(() => {
      console.error("Lỗi khi tải danh sách khoá học.");
      reject();
    });
  });
}



$(document).ready(function () {
  // Khi chọn Khoa trong modal, sẽ load ngành theo khoa
  $("#tenkhoa").on("change", function () {
    const makhoa = $(this).val();
    $("#tennganh").empty().append("<option></option>");

    if (!makhoa) return;

    $.ajax({
      type: "POST",
      url: "./major/getAllFaculty", // API trả về danh sách ngành theo makhoa
      data: { makhoa: makhoa },
      dataType: "json",
      success: function (data) {
        let html = "<option></option>";
        data.forEach((item) => {
          html += `<option value="${item.manganh}">${item.tennganh}</option>`;
        });
        $("#tennganh").html(html).trigger("change");
      },
      error: function () {
        console.error("Lỗi khi tải danh sách ngành theo khoa.");
      },
    });
  });

  $("[data-bs-target='#modal-add-classroom']").click(function (e) {
    e.preventDefault();

    // Reset form & trạng thái
    $(".update-classroom-element").hide();
    $(".add-classroom-element").show();
    $(".form-add-classroom")[0].reset();
    $("#tenkhoa").empty().append("<option></option>");
    $("#tennganh").empty().append("<option></option>");
    $("#tenkhoahoc").empty().append("<option></option>");

    // Gọi các hàm load dữ liệu
    loadFaculties(); // load khoa
    loadCourses(); // load khóa học
  });

  // Khi modal đã hiển thị hoàn toàn thì khởi tạo Select2
  $("#modal-add-classroom").on("shown.bs.modal", function () {
    loadSelect2();
  });

  function checkTonTai(malop) {
    let check = true;
    $.ajax({
      type: "post",
      url: "./classroom/checkclassroom",
      data: {
        malop: malop,
      },
      async: false,
      dataType: "json",
      success: function (response) {
        if (response.length !== 0) {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: `Môn học đã tồn tại!`,
          });
          check = false;
        }
      },
    });
    return check;
  }

  $("#add_classroom").on("click", function () {
    let malop = $("#malop").val();
    if ($(".form-add-classroom").valid() && checkTonTai(malop)) {
      $.ajax({
        type: "post",
        url: "./classroom/add",
        data: {
          malop: malop,
          tenmon: $("#tenlop").val(),
          sotinchi: $("#sotinchi").val(),
          sotietlythuyet: $("#sotiet_lt").val(),
          sotietthuchanh: $("#sotiet_th").val(),
          makhoa: $("#tenkhoa").val(),
        },
        success: function (response) {
          if (response) {
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: "Thêm môn học thành công!",
            });
            $("#modal-add-classroom").modal("hide");
            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Thêm môn học không thành công!",
            });
          }
        },
      });
    }
  });
  $(document).on("click", ".btn-edit-classroom", async function () {
    $(".update-classroom-element").show();
    $(".add-classroom-element").hide();
    $(".form-add-classroom")[0].reset();

    $("#tenkhoa").empty().append("<option></option>");
    $("#tennganh").empty().append("<option></option>");
    $("#tenkhoahoc").empty().append("<option></option>");

    let malop = $(this).data("id");

    // 1. Lấy chi tiết lớp
    const response = await $.ajax({
      type: "post",
      url: "./classroom/getDetail",
      data: { malop },
      dataType: "json",
    });

    if (response) {
      $("#malop").val(response.malop);
      $("#tenlop").val(response.tenlop);
      $("#sotinchi").val(response.sotinchi);
      $("#sotiet_lt").val(response.sotietlythuyet);
      $("#sotiet_th").val(response.sotietthuchanh);

      // 2. Load danh sách khóa học và chọn đúng
      await loadCourses();
      $("#tenkhoahoc").val(response.makhoahoc);

      // 3. Load danh sách khoa và chọn đúng
      await loadFaculties();
      $("#tenkhoa").val(response.makhoa);

      // 4. Load danh sách ngành theo khoa vừa chọn và gán đúng ngành
      await loadMajorsByFaculty(response.makhoa);
      $("#tennganh").val(response.manganh);

      // 5. Mở modal
      $("#modal-add-classroom").modal("show");
      $("#update_classroom").data("id", response.malop);
    }
  });
  
  // Đóng modal thì reset form
  $("#modal-add-classroom").on("hidden.bs.modal", function () {
    $("#malop").val(""),
      $("#tenlop").val(""),
      $("#sotinchi").val(""),
      $("#sotiet_lt").val(""),
      $("#sotiet_th").val(""),
      $("#tenkhoa").val(""),
      $("#update_classroom").data("id", "");
  });

  $("#update_classroom").click(function (e) {
    e.preventDefault();
    let malop = $(this).data("id");
    if ($(".form-add-classroom").valid()) {
      $.ajax({
        type: "post",
        url: "./classroom/update",
        data: {
          id: malop,
          malop: $("#malop").val(),
          tenmon: $("#tenlop").val(),
          sotinchi: $("#sotinchi").val(),
          makhoa: $("#tenkhoa").val(),
          sotietlythuyet: $("#sotiet_lt").val(),
          sotietthuchanh: $("#sotiet_th").val(),
        },
        success: function (response) {
          if (response) {
            $("#modal-add-classroom").modal("hide");
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: "Cập nhật môn học thành công!",
            });
            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Cập nhật môn học không thành công!",
            });
          }
        },
      });
    }
  });

  $(document).on("click", ".btn-delete-classroom", function () {
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
      text: "Bạn có chắc chắn muốn xoá nhóm môn học?",
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
          url: "./classroom/delete",
          data: {
            malop: trid,
          },
          success: function (response) {
            if (response) {
              e.fire("Deleted!", "Xóa môn học thành công!", "success");
              mainPagePagination.getPagination(
                mainPagePagination.option,
                mainPagePagination.valuePage.curPage
              );
            } else {
              e.fire("Lỗi !", "Xoá môn học không thành công !)", "error");
            }
          },
        });
      }
    });
  });

  //student
  $(document).on("click", ".classroom-info", function () {
    var id = $(this).data("id");
    $("#malop_chuong").val(id);
    showstudent(id);
  });

  function resetFormstudent() {
    $("#collapsestudent").collapse("hide");
    $("#name_student").val("");
  }

  $("#modal-student").on("hidden.bs.modal", function () {
    resetFormstudent();
  });

  function showstudent(malop) {
    $.ajax({
      type: "post",
      url: "./user/getAllByClassroom",
      data: {
        malop: malop,
      },
      dataType: "json",
      success: function (response) {
        let html = "";
        if (response.length > 0) {
          response.forEach((student, index) => {
            html += `<tr>
                        <td class="text-center fs-sm"><strong>${
                          index + 1
                        }</strong></td>
                        <td>${student["id"]}</td>
                        <td>${student["hoten"]}</td>
                        <td>${student["ngaysinh"]}</td>
                        <td>${student["email"]}</td>
                        <td class="text-center col-action">
                          
                            <a data-role="chuong" data-action="delete" class="btn btn-sm btn-alt-secondary student-delete" href="javascript:void(0)"
                                data-bs-toggle="tooltip" aria-label="Delete"
                                data-bs-original-title="Delete" data-id="${
                                  student["id"]
                                }">
                                <i class="fa fa-fw fa-times"></i>
                            </a>
                        </td>
                    </tr>`;
          });
        } else {
          html += `<tr>
              <td colspan="6">
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem 0;">
                  <img src="./public/media/svg/empty_data.png" alt="Không có dữ liệu" style="width: 180px; display: block;" />
                  <p style="margin-top: 1rem; text-align: center; color: #6c757d;">Không có dữ liệu</p>
                </div>
              </td>
            </tr>
            `;
        }
        $("#list-student").html(html);
      },
    });
  }

  $("#btn-add-student").click(function () {
    $("#add-student").show();
    $("#edit-student").hide();
    $("#name_student").val("");
  });

  $("#add-student").on("click", function (e) {
    e.preventDefault();
    let malop = $("#malop_chuong").val();
    if ($("#name_student").val() == "") {
      Dashmix.helpers("jq-notify", {
        type: "danger",
        icon: "fa fa-times me-1",
        message: "Tên chương không để trống!",
      });
    } else {
      $.ajax({
        type: "post",
        url: "./classroom/addstudent",
        data: {
          malop: malop,
          tenchuong: $("#name_student").val(),
        },
        success: function (response) {
          if (response) {
            resetFormstudent();
            showstudent(malop);
          }
        },
      });
    }
  });

  $(".close-student").click(function (e) {
    e.preventDefault();
    $("#collapsestudent").collapse("hide");
  });

  $(document).on("click", ".student-delete", function () {
    let machuong = $(this).data("id");

    let e = Swal.mixin({
      buttonsStyling: !1,
      target: "#page-container",
      customClass: {
        confirmButton: "btn btn-danger m-1",
        cancelButton: "btn btn-secondary m-1",
        input: "form-control",
      },
    });

    e.fire({
      title: "Are you sure?",
      text: "Bạn có chắc chắn muốn xoá sinh viên khỏi lớp nàynày?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Vâng, tôi chắc chắn!",
      cancelButtonText: "Huỷ",
    }).then((result) => {
      if (result.isConfirmed) {
        // Chỉ khi người dùng xác nhận mới xoá
        $.ajax({
          type: "post",
          url: "./classroom/studentDelete",
          data: {
            machuong: machuong,
          },
          success: function (response) {
            if (response) {
              Dashmix.helpers("jq-notify", {
                type: "success",
                icon: "fa fa-check me-1",
                message: "Xoá sinh viên thành công!",
              });
              showstudent($("#malop_chuong").val());
            } else {
              Dashmix.helpers("jq-notify", {
                type: "danger",
                icon: "fa fa-times me-1",
                message: "Xoá sinh viên không thành công!",
              });
            }
          },
        });
      }
    });
  });
});

// Pagination
const mainPagePagination = new Pagination();
mainPagePagination.option.controller = "classroom";
mainPagePagination.option.model = "LopModel";
mainPagePagination.option.limit = 10;
mainPagePagination.getPagination(
  mainPagePagination.option,
  mainPagePagination.valuePage.curPage
);
