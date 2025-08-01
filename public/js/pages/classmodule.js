Dashmix.helpersOnLoad(["jq-select2"]);

Dashmix.onLoad(() =>
  class {
    static initValidation() {
      Dashmix.helpers("jq-validation"),
        jQuery(".form-add-group").validate({
          rules: {
            "ten-khoa": {
              required: !0,
            },
            "ten-khoahoc": {
              required: !0,
            },
            "ten-monhoc": {
              required: !0,
            },
            "ten-nganh": {
              required: !0,
            },
            "hoc-ky": {
              required: !0,
            },
          },
          messages: {
            "ten-khoa": {
              required: "Vui lòng chọn khoa",
            },
            "ten-khoahoc": {
              required: "Vui lòng chọn khóa học",
            },
            "ten-monhoc": {
              required: "Vui lòng chọn môn học",
            },
            "ten-nganh": {
              required: "Vui lòng chọn ngành học ",
            },
            "hoc-ky": {
              required: "Vui lòng chọn học kỳ",
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
      dropdownParent: $("#modal-add-group"), // Fix lỗi nhập trong modal
      width: "100%",
      placeholder: $(this).data("placeholder") || "", // Lấy từ attribute nếu có
      minimumResultsForSearch: 0,
    });
  });
}



$(document).ready(function () {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success me-2",
      cancelButton: "btn btn-danger",
    },
    buttonsStyling: false,
  });

  let groups = [];
  let mode = 1;

  function loadDataGroup(hienthi) {
    console.log("load")
    $.ajax({
      type: "post",
      url: "./classmodule/loadData",
      data: {
        hienthi: hienthi,
      },
      dataType: "json",
      success: function (response) {
        console.log(response)
        showGroup(response);
        groups = response;
      },
    });
  }

  loadDataGroup(mode);
  function showGroup(list) {
    let html = "";
    let d = 0;
    console.log(list);
    if (list.length === 0) {
      html += `<p class="text-center mt-5">Không có dữ liệu</p>`;
    } else {
      list.forEach((subject, index) => {
        const htmlBtnToggle =
          mode === 1
            ? `<button data-index="${index}" type="button" class="btn btn-alt-secondary btn-sm btn-hide-all ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Ẩn tất cả"><i class="far fa-eye-slash"></i></button>`
            : `<button data-index="${index}" type="button" class="btn btn-alt-secondary btn-sm btn-unhide-all ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Huỷ tất cả"><i class="fa fa-rotate-left"></i></button>`;

        html += `
        <div>
          <div class="heading-group d-flex align-items-center">
            <h2 class="content-heading pb-0" id="${d++}">
              <span class="mamonhoc">${subject.mamonhoc}</span> -
              <span class="tenmonhoc">${subject.tenmonhoc}</span> -
              <span class="namhoc">${subject.tennganh}</span> -
              <span class="hocky">${subject.tenkhoahoc}</span>
            </h2>
            ${htmlBtnToggle}
          </div>
          <div class="row">`;

        subject.lop.forEach((group) => {
          const btnToggle =
            group.trangthai == 1
              ? `<a class="nav-main-link dropdown-item btn-hide-group" href="javascript:void(0)" data-id="${group.mahocphan}">
                <i class="nav-main-link-icon si si-eye me-2  text-danger"></i>
                <span class="nav-main-link-name fw-normal  text-danger">Ẩn nhóm</span>
              </a>`
              : `<a class="nav-main-link dropdown-item btn-unhide-group" href="javascript:void(0)" data-id="${group.mahocphan}">
                <i class="nav-main-link-icon si si-action-undo me-2 text-dark"></i>
                <span class="nav-main-link-name fw-normal">Khôi phục</span>
              </a>`;

          html += `
          <div class="col-sm-6 col-lg-6 col-xl-3">
            <div class="block block-rounded">
              <div class="block-header block-header-default">
                <h3 class="block-title block-class-name">${group.tenlop}</h3>
                <div class="block-options">
                  <div class="dropdown">
                    <button type="button" class="btn btn-alt-secondary dropdown-toggle module__dropdown" data-bs-toggle="dropdown" data-id="${group.mahocphan}">
                      <i class="si si-settings"></i>
                    </button>
                    <div class="dropdown-menu fs-sm">
                     <a class="nav-main-link dropdown-item manhom" href="classmodule/detail/${group.mahocphan}">
                                        <i class="nav-main-link-icon si si-info me-2 text-dark"></i>
                                        <span class="nav-main-link-name fw-normal">Thông tin chi tiết </span>
                                    </a>
                      <a class="nav-main-link dropdown-item manhom classroom-info"  data-id="${group.malop}"  data-bs-toggle="modal" data-bs-target="#modal-student">
                        <i class="nav-main-link-icon si si-info me-2 text-dark"></i>
                        <span class="nav-main-link-name fw-normal">Danh sách sinh viên</span>
                      </a>
                      <a class="nav-main-link dropdown-item btn-update-group" href="javascript:void(0)" data-id="${group.mahocphan}" data-role="hocphan" data-action="update">
                        <i class="nav-main-link-icon si si-pencil me-2 text-dark"></i>
                        <span class="nav-main-link-name fw-normal">Sửa thông tin</span>
                      </a>
                      ${btnToggle}
                 
                    </div>
                  </div>
                </div>
              </div>
              <div class="block-content">
                <p class="block-class-note">${group.tengiaovien}</p>
                <p class="block-class-note">${group.ghichu}</p>
                <p class="Si-So">Sỉ số: <span>${group.siso}</span></p>
              </div>
            </div>
          </div>`;
        });

        html += `</div></div>`;
      });
    }
    // <a class="nav-main-link dropdown-item btn-delete-group" href="javascript:void(0)" data-id="${group.mahocphan}" data-role="hocphan" data-action="delete">
    //   <i class="nav-main-link-icon si si-trash me-2 text-danger"></i>
    //   <span class="nav-main-link-name fw-normal text-danger">Xoá nhóm</span>
    // </a>
    $("#class-group").html(html);
    $('[data-bs-toggle="tooltip"]').tooltip();
  }
  let facultyMap = {};
  $.get(
    "./faculty/getAll",
    function (data) {
      let html = "<option></option>";
      data.data.forEach((item) => {
        html += `<option value="${item.makhoa}">${item.tenkhoa}</option>`;
      });
      $("#ten-khoa").html(html);
      data.data.forEach((item) => {
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
      data.data.forEach((item) => {
        html += `<option value="${item.makhoahoc}">${item.tenkhoahoc}</option>`;
      });
      $("#ten-khoahoc").html(html);
      data.data.forEach((item) => {
        courseMap[item.makhoahoc] = item.tenkhoahoc;
      });
      //  showData(classroom);
    },
    "json"
  );

  $.get(
    "./subject/getData",
    function (data) {
      console.log(data)
      let html = "<option></option>";
      data.forEach((item) => {
        html += `<option value="${item.mamonhoc}">${
          item.mamonhoc + " - " + item.tenmonhoc
        }</option>`;
      });
      $("#ten-monhoc").html(html);
    },
    "json"
  );
  $.get(
    "./teacher/getData",
    function (data) {
      console.log(data);
      let html = "<option></option>";
      data.forEach((item) => {
        html += `<option value="${item.id}">${
          item.id + " - " + item.hoten
        }</option>`;
      });
      $("#ten-giaovien").html(html);
    },
    "json"
  );
  $("#ten-khoa").on("change", function () {
    console.log("change");
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
        data.data.forEach((item) => {
          html += `<option value="${item.manganh}">${item.tennganh}</option>`;
        });
        $(`#ten-nganh`).html(html);
      },
    });

  });
  function renderListYear() {
    let html = "<option></option>";
    let currentYear = new Date().getFullYear();
    let start = currentYear - 10;
    let end = currentYear + 10;
    for (let i = start; i <= end; i++) {
      html += `<option value="${i}">${i + " - " + (i + 1)}</option>`;
    }
    $("#nam-hoc").html(html);
    $("#nam-hoc").val(currentYear).trigger("change");
  }

  renderListYear();
  $("#modal-add-group").on("shown.bs.modal", function () {
    loadSelect2();
  });
  
  $("#add-group").click(function (e) {
    e.preventDefault();
    if ($(".form-add-group").valid()) {
      $.ajax({
        type: "post",
        url: "./classmodule/add",
        data: {
          manganh: $("#ten-nganh").val(),
          makhoahoc: $("#ten-khoahoc").val(),
          monhoc: $("#ten-monhoc").val(),
          magiaovien: $("#ten-giaovien").val(),
          ghichu: $("#ghi-chu").val(),
        },
        success: function (response) {
          console.log(response);
          if (response) {
            $("#modal-add-group").modal("hide");
            loadDataGroup(mode);
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: "Thêm nhóm thành công!",
            });
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Thêm nhóm không thành công!",
            });
          }
        },
      });
    }
  });

  $(document).on("click", ".btn-hide-all", function () {
    let index = $(this).data("index");
    swalWithBootstrapButtons
      .fire({
        title: "Are you sure?",
        text: "Bạn có chắc chắn muốn ẩn hết các nhóm môn học này không!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Chắc chắn!",
        cancelButtonText: "Không!",
      })
      .then((result) => {
        if (result.isConfirmed) {
          groups[index].lop.forEach((item) => {
            updateHide(item.mahocphan, 0);
          });
          groups.splice(index, 1);
          Dashmix.helpers("jq-notify", {
            type: "success",
            icon: "fa fa-check me-1",
            message: "Ẩn nhóm thành công!",
          });
          showGroup(groups);
        }
      });
  });

  $(document).on("click", ".btn-unhide-all", function () {
    let index = $(this).data("index");
    swalWithBootstrapButtons
      .fire({
        title: "Are you sure?",
        text: "Bạn có chắc chắn muốn huỷ ẩn hết các nhóm môn học này không!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Chắc chắn!",
        cancelButtonText: "Không!",
      })
      .then((result) => {
        if (result.isConfirmed) {
          groups[index].lop.forEach((item) => {
            updateHide(item.mahocphan, 1);
          });
          groups.splice(index, 1);
          Dashmix.helpers("jq-notify", {
            type: "success",
            icon: "fa fa-check me-1",
            message: "Ẩn nhóm thành công!",
          });
          showGroup(groups);
        }
      });
  });

  $(document).on("click", ".btn-delete-group", function () {
    swalWithBootstrapButtons
      .fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
      })
      .then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: "post",
            url: "./module/delete",
            data: {
              manhom: $(this).data("id"),
            },
            success: function (response) {
              if (response) {
                swalWithBootstrapButtons.fire(
                  "Xoá thành công!",
                  "Nhóm đã được xoá thành công",
                  "success"
                );
                loadDataGroup(mode);
              }
            },
          });
        }
      });
  });

  $(document).on("click", ".btn-hide-group", function () {
    let manhom = $(this).data("id");
    updateHide(manhom, 0)
      .then((response) => {
        removeItem(manhom);
        showGroup(groups);
        Dashmix.helpers("jq-notify", {
          type: "success",
          icon: "fa fa-check me-1",
          message: "Ẩn nhóm thành công!",
        });
      })
      .catch((error) => {
        Dashmix.helpers("jq-notify", {
          type: "danger",
          icon: "fa fa-times me-1",
          message: "Ẩn nhóm không thành công!",
        });
      });
  });

  function removeItem(manhom) {
    for (let i = 0; i < groups.length; i++) {
      let index = groups[i].lop.findIndex((item) => item.mahocphan == manhom);
      if (index != -1) {
        groups[i].lop.splice(index, 1);
        if (groups[i].lop.length == 0) groups.splice(i, 1);
        break;
      }
    }
  }

  $(document).on("click", ".btn-unhide-group", function () {
    let manhom = $(this).data("id");
    updateHide(manhom, 1)
      .then((response) => {
        removeItem(manhom);
        showGroup(groups);
        Dashmix.helpers("jq-notify", {
          type: "success",
          icon: "fa fa-check me-1",
          message: "Huỷ ẩn nhóm thành công!",
        });
      })
      .catch((error) => {
        Dashmix.helpers("jq-notify", {
          type: "danger",
          icon: "fa fa-times me-1",
          message: "Huỷ ẩn nhóm không thành công!",
        });
      });
  });

  function updateHide(manhom, giatri) {
    return new Promise((resolve, reject) => {
      $.ajax({
        type: "post",
        url: "./classmodule/hide",
        data: {
          mahocphan: manhom,
          giatri: giatri,
        },
        success: function (response) {
          resolve(response);
        },
        error: function (error) {
          reject(error);
        },
      });
    });
  }
  $(document).on("click", ".btn-update-group", function () {
    $(".add-group-element").hide();
    $(".update-group-element").show();
    $("#modal-add-group").modal("show");

    let id = $(this).data("id");
    $("#update-group").data("id", id);

    $.ajax({
      type: "post",
      url: "./classmodule/getDetail",
      data: { mahocphan: id },
      dataType: "json",
      success: function (response) {
        console.log(response);

        // Set khoa trước
        $("#ten-khoa").val(response.makhoa).trigger("change");

        // Chờ ngành + giáo viên load xong sau khi thay đổi khoa
        waitForDependentData(response.manganh, response.magiaovien);

        // Các trường không phụ thuộc khoa thì set luôn
        $("#ten-khoahoc").val(response.makhoahoc).trigger("change");
        $("#ten-monhoc").val(response.mamonhoc).trigger("change");
        $("#ghi-chu").val(response.ghichu);
        $("select").prop("disabled", true);

        // 2. Mở khóa combobox giáo viên
        $("#ten-giaovien").prop("disabled", false);
        $("#ten-nganh").prop("disabled", true);
        // 3. Mở khóa ô ghi chú (nếu là <input> hoặc <textarea>)
        $("#ghi-chu").prop("disabled", false);
      },
      error: function (xhr, status, error) {
        console.error("Lỗi khi lấy chi tiết học phần:", error);
        alert("Không thể lấy dữ liệu học phần. Vui lòng thử lại.");
      },
    });
  });
  function waitForDependentData(manganh, magiangvien) {
    const makhoa = $("#ten-khoa").val();

    const loadNganh = $.ajax({
      url: "./major/getAllFaculty",
      type: "POST",
      data: { makhoa },
      dataType: "json",
    });

    const loadGiaoVien = $.ajax({
      url: "./teacher/getAllFaculty",
      type: "POST",
      data: { makhoa },
      dataType: "json",
    });

    $.when(loadNganh, loadGiaoVien).done(function (nganhRes, gvRes) {
      // Đổ ngành
      let nganhOptions = '<option value="">-- Chọn ngành --</option>';
      nganhRes[0].forEach(function (item) {
        nganhOptions += `<option value="${item.manganh}">${item.tennganh}</option>`;
      });
      $("#ten-nganh")
        .html(nganhOptions)
        .val(manganh)
        .trigger("change")
        .prop("disabled", false);

      // Đổ giáo viên
      $("#ten-nganh").prop("disabled", true);

      let gvOptions = '<option value="">-- Chọn giáo viên --</option>';
      gvRes[0].forEach(function (item) {
        gvOptions += `<option value="${item.id}">${item.id} - ${item.hoten}</option>`;
      });
      $("#ten-giaovien")
        .html(gvOptions)
        .val(magiangvien)
        .trigger("change")
        .prop("disabled", false);
    });
  }
  

  $("#update-group").click(function (e) {
    e.preventDefault();
    console.log($("#ten-giaovien").val());
    if ($(".form-add-group").valid()) {
      $.ajax({
        type: "post",
        url: "./classmodule/update",
        data: {
          manhom: $(this).data("id"),
          magiaovien: $("#ten-giaovien").val(),
          ghichu: $("#ghi-chu").val(),
        },
        success: function (response) {
          console.log(response);
          if (response == "true") {
            $("#modal-add-group").modal("hide");
            loadDataGroup(mode);
            Dashmix.helpers("jq-notify", {
              type: "success",
              icon: "fa fa-check me-1",
              message: "Cập nhật nhóm thành công!",
            });
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Cập nhật nhóm không thành công!",
            });
          }
        },
      });
    }
  });

  $("[data-bs-target='#modal-add-group']").click(function (e) {
    e.preventDefault();
    $("select").prop("disabled", false);
    $(".add-group-element").show();
    $(".update-group-element").hide();
  });

  // Reset form khi đóng modal
  $("#modal-add-group").on("hidden.bs.modal", function () {
    $("#ten-nhom").val(""),
      $("#ghi-chu").val(""),
      $("#mon-hoc").val("").trigger("change"),
      $("#nam-hoc").val("").trigger("change"),
      $("#hoc-ky").val("").trigger("change");
  });

  // Thay đổi text khi nhấn vào dropdown
  $(".filter-search").click(function (e) {
    e.preventDefault();
    $(".btn-filter").text($(this).text());
    mode = $(this).data("value");
    loadDataGroup(mode);
  });

  $("#form-search-group").on("input", function () {
    let result = [];
    let content = $(this).val().toLowerCase();
    console.log(groups);
    for (let i = 0; i < groups.length; i++) {
      if (
        groups[i].mamonhoc.includes(content) ||
        groups[i].tenmonhoc.toLowerCase().includes(content) ||
        groups[i].namhoc.includes(content)
      ) {
        result.push(groups[i]);
      }
    }
    showGroup(result);
  });
});

$(document).on("click", ".classroom-info", function () {
  console.log("si-info");
  var id = $(this).data("id");
  $("#malop_chuong").val(id);
  showstudent(id);
});
//student
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
        console.log("response", response);
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
