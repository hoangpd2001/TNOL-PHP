Dashmix.helpersOnLoad(["js-flatpickr", "jq-datepicker"]);

Dashmix.onLoad(() =>
  class {
    static initValidation() {
      Dashmix.helpers("jq-validation"),
        jQuery(".form-add-user").validate({
          rules: {
            masinhvien: {
              required: !0,
            },
            user_email: {
              required: !0,
              emailWithDot: !0,
            },
            user_name: {
              required: !0,
            },
            user_gender: {
              required: !0,
            },
            user_ngaysinh: {
              required: !0,
            },
            user_nhomquyen: {
              required: !0,
            },
            user_password: {
              required: !0,
              minlength: 5,
            },
          },
          messages: {
            masinhvien: {
              required: "Vui lòng nhập mã sinh viên của bạn",
            },
            user_email: {
              required: "Vui lòng cung cấp email của bạn",
              emailWithDot: "Phải nhập đúng định dạng email",
            },
            user_name: {
              required: "Cung cấp đầy đủ họ tên",
            },
            user_gender: {
              required: "Tích chọn 1 trong 2",
            },
            user_ngaysinh: {
              required: "Vui lòng cho biết ngày sinh của bạn",
            },
            user_nhomquyen: {
              required: "Vui lòng chọn nhóm quyền",
            },
            user_password: {
              required: "Nhập mật khẩu",
              minlength: "Mật khẩu phải có ít nhất 5 ký tự!",
            },
          },
        });
    }

    static init() {
      this.initValidation();
    }
  }.init()
);

const showData = function (users) {
  let html = "";
  console.log(users);
  users.forEach((user) => {    if (user.manhomquyen != 11) {
    return;
  }
    html += `<tr>
                          <td class="text-center">
                              <strong>${user.id}</strong>
                          </td>
                          <td class="fs-sm d-flex align-items-center">
                              <img class="img-avatar img-avatar48 me-3" src="./public/media/avatars/${
                                user.avatar == null
                                  ? `avatar2.jpg`
                                  : user.avatar
                              }" alt="">
                              <div class="d-flex flex-column">
                                  <strong class="text-primary">${
                                    user.hoten
                                  }</strong>
                                  <span class="fw-normal fs-sm text-muted">${
                                    user.email
                                  }</span>
                              </div>
                          </td>
                          <td class="text-center">${
                            user.gioitinh == 1 ? "Nam" : "Nữ"
                          }</td>
                          <td class="text-center">${user.ngaysinh}</td>
                          <td class="text-center">${user.tennhomquyen}</td>
                          <td class="text-center">${user.ngaythamgia}</td>
                          <td class="text-center">
                              <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill ${
                                user.trangthai == 1
                                  ? "bg-success-light text-success"
                                  : "bg-danger-light text-danger"
                              } bg-success-light text-success">${
      user.trangthai == 1 ? "Hoạt động" : "Khoá"
    }</span>
                          </td> 
                          <td class="text-center col-action">
                              <a data-role="nguoidung" data-action="update" class="btn btn-sm btn-alt-secondary user-edit" href="javascript:void(0)"
                              data-bs-toggle="tooltip" aria-label="Chỉnh sửa" data-bs-original-title="Chỉnh sửa" data-id="${
                                user.id
                              }">
                                  <i class="fa fa-fw fa-pencil"></i>
                              </a>
                              <a data-role="nguoidung" data-action="delete" class="btn btn-sm btn-alt-secondary user-delete" href="javascript:void(0)" data-bs-toggle="tooltip"
                                  aria-label="Xoá" data-bs-original-title="Xoá" data-id="${
                                    user.id
                                  }">
                                  <i class="fa fa-fw fa-times"></i>
                              </a>
                          </td>
                      </tr>
      `;
  });
  $("#list-user").html(html);
  $('[data-bs-toggle="tooltip"]').tooltip();
};

$(document).ready(function () {
  $("#user_nhomquyen").select2({
    dropdownParent: $("#modal-add-user"),
  });
  $("#user_nganh").select2({
    dropdownParent: $("#modal-add-user"),
  });
  $("#user_khoahoc").select2({
    dropdownParent: $("#modal-add-user"),
  });
  $("#user_lop").select2({
    dropdownParent: $("#modal-add-user"),
  });
  $("#user_lopexcel").select2({
    dropdownParent: $("#modal-add-user"),
  });
  $.get(
    "./roles/getAll",
    function (data) {
      let html = `<option></option>`;
      data.forEach((item) => {
        html += `<option value="${item.manhomquyen}">${item.tennhomquyen}</option>`;
      });
      $("#user_nhomquyen").html(html);
      document
        .getElementById("user_nhomquyen")
        .dispatchEvent(new Event("change"));
    },
    "json"
  );
  $.get(
    "./classroom/getAll",
    function (data) {
      let html = `<option></option>`;
      data.forEach((item) => {
        html += `<option value="${item.malop}">${item.tenlop}-${item.tenkhoahoc}</option>`;
      });
      $("#user_lopexcel").html(html);
    },
    "json"
  );
  $.get(
    "./course/getAll",
    function (data) {
      let html = `<option></option>`;
      data.forEach((item) => {
        html += `<option value="${item.makhoahoc}">${item.tenkhoahoc}</option>`;
      });
      $("#user_khoahoc").html(html);   
     },
    "json"
  );
  $.get(
    "./major/getAll",
    function (data) {
      let html = `<option></option>`;
      data.forEach((item) => {
        html += `<option value="${item.manganh}">${item.tennganh}</option>`;
      });
      $("#user_nganh").html(html);
    
    },
    "json"
  );
  $("[data-bs-target='#modal-add-user']").click(function (e) {
    e.preventDefault();
    clearInputFields();
    $(".add-user-element").show();
    $(".update-user-element").hide();
  });
  function loadLopHocTheoNganhVaKhoaHoc() {
    const khoahocId = $("#user_khoahoc").val();
    const nganhId = $("#user_nganh").val();

    // Chỉ khi cả hai đều được chọn thì mới gọi API
    if (khoahocId && nganhId) {
      console.log(khoahocId, nganhId);
      $.post(
        `./classroom/getByMajorCount`,
        {
          makhoahoc: khoahocId,
          manganh: nganhId,
        },
        function (data) {
          console.log(data);
          let html = `<option></option>`;
          data.forEach((item) => {
            html += `<option value="${item.malop}">${item.tenlop}</option>`;
          });
          $("#user_lop").html(html);
        },
        "json"
      );
    } else {
      // Nếu chưa chọn đủ, reset lớp học
      $("#user_lop").html(`<option></option>`);
    }
    
  }

  // Gắn sự kiện khi thay đổi ngành hoặc khóa học
  $("#user_khoahoc, #user_nganh").on("change", loadLopHocTheoNganhVaKhoaHoc);
  
  function checkUser(id, email) {
    let result = true;
    $.ajax({
      type: "post",
      url: "./user/checkUser",
      data: {
        mssv: id,
        email: email,
      },
      async: false,
      dataType: "json",
      success: function (response) {
        if (response.length !== 0) {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: `Người dùng đã tồn tại!`,
          });
          result = false;
        }
      },
    });
    return result;
  }

  function checkUserUpdate(id, email) {
    let result = true;
    $.ajax({
      type: "post",
      url: "./user/checkUser",
      data: {
        mssv: id,
        email: email,
      },
      async: false,
      dataType: "json",
      success: function (response) {
        if (response.length != 1) {
          Dashmix.helpers("jq-notify", {
            type: "danger",
            icon: "fa fa-times me-1",
            message: `Người dùng đã tồn tại!`,
          });
          result = false;
        }
      },
    });
    return result;
  }

  $("#btn-add-user").on("click", function (e) {
    e.preventDefault();
    let mssv = $("#masinhvien").val();
    let email = $("#user_email").val();
    // Validate user
    if ($(".form-add-user").valid() && checkUser(mssv, email)) {
      $.ajax({
        type: "post",
        url: "./user/add",
        data: {
          masinhvien: $("#masinhvien").val(),
          hoten: $("#user_name").val(),
          gioitinh: $('input[name="user_gender"]:checked').val(),
          ngaysinh: $("#user_ngaysinh").val(),
          email: $("#user_email").val(),
          password: $("#user_password").val(),
          malop: $("#user_lop").val(),
          status: $("#user_status").prop("checked") ? 1 : 0,
        },
        success: function (response) {
          console.log(response.valid);
          Dashmix.helpers("jq-notify", {
            type: "success",
            icon: "fa fa-check me-1",
            message: `Thêm người dùng thành công!`,
          });
          $("#modal-add-user").modal("hide");
          mainPagePagination.getPagination(
            mainPagePagination.option,
            mainPagePagination.valuePage.curPage
          );
        },
      });
    } else {
      Dashmix.helpers("jq-notify", {
        type: "danger",
        icon: "fa fa-times me-1",
        message: `Thêm người dùng không thành công!`,
      });
    }
  });

  $(document).on("click", ".user-edit", function () {
    let id = $(this).data("id");
    $(".add-user-element").hide();
    $(".update-user-element").show();
    $("#btn-update-user").data("id", id);
    $.ajax({
      type: "post",
      url: "./user/getDetail",
      data: {
        id: id,
      },
      dataType: "json",
      success: function (response) {
        $("#masinhvien").val(response.id);
        $("#masinhvien").prop("disabled", true);
        $("#user_name").val(response.hoten),
          $(`input[name="user_gender"][value="${response.gioitinh}"]`).prop(
            "checked",
            true
          ),
          $("#user_ngaysinh").val(response.ngaysinh);
        $("#user_email").val(response.email);
        $("#user_nhomquyen").val(response.manhomquyen).trigger("change");
        $("#user_status").prop("checked", response.trangthai == 1);
        $("#modal-add-user").modal("show");
      },
    });
  });

  $("#btn-update-user").click(function (e) {
    e.preventDefault();
    let mssv = $("#masinhvien").val();
    let email = $("#user_email").val();
    if (checkUserUpdate(mssv, email)) {
      $.ajax({
        type: "post",
        url: "./user/update",
        data: {
          id: $(this).data("id"),
          hoten: $("#user_name").val(),
          gioitinh: $('input[name="user_gender"]:checked').val(),
          ngaysinh: $("#user_ngaysinh").val(),
          email: email,
          role: $("#user_nhomquyen").val(),
          password: $("#user_password").val(),
          status: $("#user_status").prop("checked") ? 1 : 0,
        },
        success: function (response) {
          console.log(response);
          mainPagePagination.getPagination(
            mainPagePagination.option,
            mainPagePagination.valuePage.curPage
          );
          $("#modal-add-user").modal("hide");
        },
      });
    }
  });

  $(document).on("click", ".user-delete", function () {
    var trid = $(this).data("id");
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
      text: "Bạn có chắc chắn muốn xoá người dùng này?",
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
          url: "./user/deleteData",
          data: {
            id: trid,
          },
          success: function (response) {
            e.fire("Deleted!", "Xóa người dùng thành công!", "success");
            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
          },
        });
      } else {
        e.fire("Cancelled", "Bạn đã không xóa người dùng :)", "error");
      }
    });
  });
  let listUser = null;
  $("#nhap-file").click(function (e) {
    e.preventDefault();
    listUser = null;
    console.log("Nhập file");
    let password = $("#ps_user_group").val();
    let file_cauhoi = $("#file-cau-hoi").val();
    let malop = $("#user_lopexcel").val();
    if (password == "" || file_cauhoi == "" || malop == "") {
      Dashmix.helpers("jq-notify", {
        type: "danger",
        icon: "fa fa-times me-1",
        message: "Vui lòng điền đầy đủ thông tin!",
      });
    } else {
      var file = $("#file-cau-hoi")[0].files[0];
      var formData = new FormData();
      formData.append("fileToUpload", file);
      $.ajax({
        type: "post",
        url: "./user/addExcel",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        beforeSend: function () {
          Dashmix.layout("header_loader_on");
        },
        success: function (response) {
          console.log(response);
          listUser = response;
          $("#modal-student-excel").modal("show"); 
          showstudent();
         // addExcel(response, password);
        },
        complete: function () {
          Dashmix.layout("header_loader_off");
        },
      });
    }
  });
  function showstudent() {
    let html = "";
    if (listUser.length > 0) {
      listUser.forEach((student, index) => {
        html += `<tr>
                        <td class="text-center fs-sm"><strong>${
                          index + 1
                        }</strong></td>
                        <td>${student["mssv"]}</td>
                        <td>${student["fullname"]}</td>
                        <td>${student["ngaysinh"]}</td>
                        <td>${student["gioitinh"] == 1 ? "Nam" : "Nữ"}</td>
                        <td>${student["email"]}</td>
                        <td class="text-center col-action">        
                                <button class="btn btn-sm btn-alt-danger student-delete" data-mssv="${
                                  student["mssv"]
                                }">
            <i class="fa fa-fw fa-times"></i>
          </button>
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
    $(".student-delete").on("click", function () {
      const mssv = $(this).data("mssv");
      deleteStudentByMSSV(mssv);
    });
  }
  function deleteStudentByMSSV(mssv) {
    listUser = listUser.filter((student) => student.mssv !== mssv);
    showstudent();
  }
  
  function addExcel(listuser, password,malop) {
    $.ajax({
      type: "post",
      url: "./user/addFileExcel",
      data: {
        listuser: listuser,
        password: password,
        malop : malop,
      },
      success: function (response) {
        mainPagePagination.getPagination(
          mainPagePagination.option,
          mainPagePagination.valuePage.curPage
        );
        $("#ps_user_group").val("");
        $("#file-cau-hoi").val("");
        $("#modal-add-user").modal("hide");
        Dashmix.helpers("jq-notify", {
          type: "success",
          icon: "fa fa-times me-1",
          message: response,
        });
      },
    });
  }

  $("#add-user-excel").click(function () {
    addExcel(listUser, $("#ps_user_group").val(), $("#user_lopexcel").val());
  });
  $("#btn-and").click(function () {
    $("#btabs-static-home-tab").tab("show");
  });

  function clearInputFields() {
    $("#masinhvien").val("");
    $("#masinhvien").prop("disabled", false);
    $("#user_email").val("");
    $("#user_name").val("");
    $(`input[name="user_gender"]`).prop("checked", false);
    $("#user_ngaysinh").val("");
    $("#user_nhomquyen").val(1).trigger("change");
    $("#user_password").val("");
    $("#user_status").prop("checked", 1);
    $("#ps_user_group").val("");
  }

  $(".filtered-by-role").click(function (e) {
    e.preventDefault();
    $(".btn-filtered-by-role").text($(this).text());
    let roleID = +$(this).data("id");
    if (roleID === 0) {
      delete mainPagePagination.option.filter.role;
    } else {
      mainPagePagination.option.filter.role = roleID;
    }
    mainPagePagination.getPagination(
      mainPagePagination.option,
      mainPagePagination.valuePage.curPage
    );
  });
});

// Pagination
const mainPagePagination = new Pagination();
mainPagePagination.option.controller = "user";
mainPagePagination.option.model = "NguoiDungModel";
mainPagePagination.option.limit = 10;
mainPagePagination.option.filter = {};
mainPagePagination.getPagination(
  mainPagePagination.option,
  mainPagePagination.valuePage.curPage
);
