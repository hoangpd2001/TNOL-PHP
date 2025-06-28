Dashmix.helpersOnLoad(["js-flatpickr", "jq-datepicker"]);

Dashmix.onLoad(() =>
  class {
    static initValidation() {
      Dashmix.helpers("jq-validation"),
        jQuery("#addSvThuCong").validate({
          rules: {
            mssv: {
              required: !0,
            },
            hoten: {
              required: !0,
            },
            matkhau: {
              required: !0,
              minlength: 6,
            },
          },
          messages: {
            mssv: {
              required: "Vui lòng nhập mã sinh viên của bạn",
            },
            hoten: {
              required: "Cung cấp đầy đủ họ tên",
            },
            matkhau: {
              required: "Nhập mật khẩu",
              minlength: "Mật khẩu phải có ít nhất 6 ký tự!",
            },
          },
        });
    }

    static init() {
      this.initValidation();
    }
  }.init()
);



const mahocphan = $(".content").data("id");
const showData = function (students) {
    console.log("load")
    console.log(students);
  let html = "";
  let index = 1;
  let offset = (this.valuePage.curPage - 1) * this.option.limit;
  if (students.length == 0) {
    html += `<tr><td colspan="7" class="text-center">Không có dữ liệu</td></tr>`;
  } else {
    students.forEach((student) => {
      html += `
          <tr>
              <td class="text-center">${offset + index++}</td>
              <td class="fs-sm d-flex align-items-center">
                      <img class="img-avatar img-avatar48 me-3" src="./public/media/avatars/${
                        student.avatar == null ? `avatar2.jpg` : student.avatar
                      }"
                          alt="">
                      <div class="d-flex flex-column">
                          <a class="fw-semibold" href="javascript:void(0)">${
                            student.hoten
                          }</a>
                          <span class="fw-normal fs-sm text-muted">${
                            student.email
                          }</span>
                      </div>
                  </td>
              <td class="text-center">${student.id}</td>
                  <td class="text-center fs-sm">${
                    student.gioitinh == 1 ? "Nam" : "Nữ"
                  }</td>
                  <td class="text-center fs-sm">${student.ngaysinh}</td>
                  <td class="text-center">
                      <div class="btn-group">
                          <button type="button" class="btn btn-sm btn-alt-secondary kick-user"
                              data-bs-toggle="Delete" title="Delete" data-id="${
                                student.id
                              }">
                              <i class="fa fa-fw fa-times"></i>
                          </button>
                      </div>
                  </td>
              </tr>
          `;

    });
  }
  $("#list-student").html(html);
  $('[data-bs-toggle="tooltip"]').tooltip();
};

$(document).ready(function () {
  $(document).on("click", ".kick-user", function () {
    var mssv = $(this).data("id");
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
      text: "Bạn có chắc chắn muốn xóa người dùng này ra khỏi nhóm?",
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
          url: "./module/kickUser",
          data: {
            mahocphan: mahocphan,
            manguoidung: mssv,
          },
          success: function (response) {
            getGroupSize(mahocphan);
            mainPagePagination.getPagination(
              mainPagePagination.option,
              mainPagePagination.valuePage.curPage
            );
            e.fire("Deleted!", "Xóa người dùng thành công!", "success");
          },
        });
      }
    });
  });

  function loadList() {
    $.ajax({
      type: "post",
      url: "./module/getSvList",
      data: {
        mahocphan: mahocphan,
      },
      dataType: "json",
      success: function (response) {
        showData(response);
      },
    });
  }

  function showDataTest(tests) {
    let html = ``;
    if (tests.length != 0) {
      tests.forEach((test) => {
        html += `<div class="block block-rounded block-fx-pop mb-2">
                <div class="block-content block-content-full border-start border-3 border-primary">
                    <div class="d-md-flex justify-content-md-between align-items-md-center">
                        <div class="p-1 p-md-2">
                            <h3 class="h4 fw-bold mb-3">
                                <a href="./test/detail/${
                                  test.made
                                }?loaigiao=1&manguongiao=${mahocphan}" class="text-dark link-fx">${
                                test.tende
                              }</a>
                            </h3>
                             <p class="fs-sm text-muted mb-0">
                                <i class="fa fa-clock me-1"></i> Hình thức:  ${
                                  test.trangthai == -1 ? " Ôn luyện" : "Thi "
                                }</span>
                            </p>
                            <p class="fs-sm text-muted mb-0">
                                <i class="fa fa-clock me-1"></i> Diễn ra từ: <span style="color:red">${
                                  test.thoigianbatdau
                                }</span> ${
                                    test.trangthai == -1
                                      ? `đến <span style="color:red">${test.thoigianketthuc}</span>`
                                      : ""
                                  }
                            </p>
                        </div>
                    </div>
                </div>
            </div>`;
      });
    } else {
      html += `<p class="text-center">Chưa có đề thi...</p>`;
    }
    $(".list-test").html(html);
  }

  function loadDataAnnounce(manhom) {
    $.ajax({
      type: "post",
      url: "./teacher_announcement/getAnnounce",
      data: {
        manhom: manhom,
        loaigiao:1,
      },
      dataType: "json",
      success: function (response) {
        console.log(response);
        showAnnouncement(response);
      },
    });
  }

  function showAnnouncement(announces) {
    let html = "";
    if (announces.length != 0) {
      announces.forEach((announce) => {
        html += `
            <li>
            <a class="d-flex text-dark py-2" href="./teacher_announcement/update/${
              announce.matb
            }">
                <div class="flex-shrink-0 mx-3">
                    <img class="img-avatar img-avatar48" src="./public/media/avatars/${
                      announce.avatar == null ? "avatar2.jpg" : announce.avatar
                    }" alt="">
                </div>
                <div class="flex-grow-1 fs-sm pe-2">
                    <div class="fw-semibold">${announce.noidung}</div>
                    <div class="text-muted">${formatDate(
                      announce.thoigiantao
                    )}</div>
                </div>
            </a>
        </li>
            `;
      });
    } else {
      html += `<p class="text-center">Không có thông báo</p>`;
    }
    $(".list-announce").html(html);
  }

  function loadDataTest(mahocphan) {
    $.ajax({
      type: "post",
      url: "./test/getTestModule",
      data: {
        mahocphan: mahocphan,
      },
      dataType: "json",
      success: function (response) {
        showDataTest(response);
      },
    });
  }

  $("[data-bs-target='#offcanvasSetting']").click(function (e) {
    e.preventDefault();
    loadDataTest(mahocphan);
    loadDataAnnounce(mahocphan);
  });


  $("#exportStudents").click(function () {
    $.ajax({
      type: "post",
      url: "./module/exportExcelStudentS",
      data: {
        mahocphan: mahocphan,
      },
      dataType: "json",
      success: function (response) {
        var $a = $("<a>");
        $a.attr("href", response.file);
        $("body").append($a);
        $a.attr("download", "Danh sách sinh viên.xls");
        $a[0].click();
        $a.remove();
      },
    });
  });

  $("#exportScores").click(function () {
    $.ajax({
      type: "post",
      url: "./test/getMarkOfAllTest",
      data: {
        mahocphan: mahocphan,
      },
      dataType: "json",
      success: function (response) {
        var $a = $("<a>");
        $a.attr("href", response.file);
        $("body").append($a);
        $a.attr("download", "Danh sách điểm.xls");
        $a[0].click();
        $a.remove();
      },
    });
  });
  

  function getGroupSize(id) {
    $.ajax({
      type: "post",
      url: "./module/getGroupSize",
      data: {
        mahocphan: id,
      },
      success: function (response) {
        $(".number-participants").html(+response);
      },
      error: function (err) {
        console.error(err.responseText);
      },
    });
  }



  $(".table-col-title").click(function (e) {
    if (!e.target.classList.contains("col-sort")) {
      return;
    }
    const column = e.target.dataset.sortColumn;
    const prevSortOrder = e.target.dataset.sortOrder;
    let currentSortOrder = "";
    switch (prevSortOrder) {
      case "default":
        currentSortOrder = "asc";
        break;
      case "asc":
        currentSortOrder = "desc";
        break;
      case "desc":
        currentSortOrder = "default";
        break;
    }

    if (currentSortOrder === "default") {
      mainPagePagination.option.custom = {};
    } else {
      mainPagePagination.option.custom.function = "sort";
      mainPagePagination.option.custom.column = column;
      mainPagePagination.option.custom.order = currentSortOrder;
    }

    getGroupSize(mahocphan);

    // AJAX call (with pagination)
    mainPagePagination.valuePage.curPage = 1;
    mainPagePagination.getPagination(
      mainPagePagination.option,
      mainPagePagination.valuePage.curPage
    );

    // Display icon
    resetSortIcons();
    e.target.dataset.sortOrder = currentSortOrder;
  });

  function clearInputFields() {
    $("#mssv").val("");
    $("#hoten").val("");
    $("#matkhau").val("");
  }

  $("[data-bs-target='#modal-add-user']").click(function (e) {
    e.preventDefault();
    $("#collapseAddSv").collapse("hide");
    clearInputFields();
  });
});

// Pagination
const mainPagePagination = new Pagination();
mainPagePagination.option.controller = "classmodule";
mainPagePagination.option.model = "HocPhanModel";
mainPagePagination.option.limit = 10;
mainPagePagination.option.mahocphan = mahocphan;
mainPagePagination.option.filter = {};
mainPagePagination.getPagination(
  mainPagePagination.option,
  mainPagePagination.valuePage.curPage
);
