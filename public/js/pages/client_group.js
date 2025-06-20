Dashmix.helpersOnLoad(["jq-select2"]);

$(document).ready(function () {
    let groups = [];
    let mode = 1;
    $('.btn-join-group').on("click", function () {
        $.ajax({
            type: "post",
            url: "./client/joinGroup",
            data: {
                mamoi: $("#mamoi").val()
            },
            dataType: "json",
            success: function (response) {
                if(response == 0) {
                    Dashmix.helpers('jq-notify', { type: 'danger', icon: 'fa fa-times me-1', message: "Mã mời không hợp lệ !"});
                }else if(response == 1) {
                    Dashmix.helpers('jq-notify', { type: 'danger', icon: 'fa fa-times me-1', message: "Bạn đã tham gia nhóm này !"});
                } else {
                    $("#modal-join-group").modal("hide");
                    groups.push(response);
                    showListGroup(groups);
                    Dashmix.helpers('jq-notify', { type: 'success', icon: 'fa fa-check me-1', message: "Tham gia nhóm thành công !"});
                }
            }
        });
    });

    function loadDataGroups(hienthi) {
        console.log("duoc goi")
        $.ajax({
          type: "post",
          url: "./client/loadDataGroups",
          data: {
            hienthi: hienthi,
          },
          dataType: "json",
          success: function (data) {
            console.log("ok");
            console.log(data);
            groups = data;
            showListGroup(data);
          },
          error: function (xhr, status, error) {
            console.error("❌ AJAX error:", status);
            console.error("❌ Response text:", xhr.responseText);
            console.error("❌ Error thrown:", error);
          },
        });
    }

    loadDataGroups(mode)

    function showListGroup(groups) {
        let html = ``;
        if(groups.length == 0) {
            html += `<p class="text-center">Chưa tham gia lớp nào</p>`
        } else {
            groups.forEach((group,index) => {
                let btn_hide = group.hienthi == 1 ? `<a class="dropdown-item btn-hide-group" data-id="${group.manhom}" href="javascript:void(0)"><i class="nav-main-link-icon si si-eye me-2 text-dark"></i> Ẩn nhóm</a>` 
                : `<a class="dropdown-item btn-unhide-group" data-id="${group.manhom}" href="javascript:void(0)"><i class="nav-main-link-icon si si-action-undo me-2 text-dark"></i> Huỷ ẩn</a>`
                html += `<div class="col-md-6 col-xl-4">
                    <div class="block block-rounded h-100 mb-0">
                        <div class="block-header">
                            <div class="flex-grow-1 text-muted fs-sm fw-semibold">
                                <img class="img-avatar img-avatar32 img-avatar-thumb me-2" src="./public/media/avatars/${group.avatar}">
                                <span>${group.hoten}</span>
                            </div>
                            <div class="block-options">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-alt-secondary dropdown-toggle module__dropdown"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="si si-settings"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        ${btn_hide}
                                        <a class="dropdown-item btn-delete-group" data-id="${group.manhom}" href="javascript:void(0)">
                                            <i class="si si-logout me-2 fa-fw icon-dropdown-item"></i> 
                                            Thoát nhóm
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="block-content bg-body-light text-center">
                            <h3 class="fs-4 mb-3">
                                <a href="javascript:void(0)" class="link-fx">${group.tennhom}</a>
                            </h3>
                            <h5 class="text-muted mb-3" style="font-size:13px">NĂM HỌC ${group.namhoc} - HỌC KỲ ${group.hocky}</h5>
                            <div class="push">
                                <span class="badge bg-info text-uppercase fw-bold py-2 px-3">${group.tennhom}</span>
                            </div>
                        </div>
                        <div class="block-content block-content-full">
                            <div class="row g-sm">
                                <div class="col-12">
                                    <button class="btn w-100 btn-alt-secondary btn-view-group" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
                                    aria-controls="offcanvasExample" data-id="${group.manhom}" data-index="${index}">
                                        <i class="fa fa-eye me-1 opacity-50"></i> Xem nhóm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`
            });
        }
        $("#list-groups").html(html);
    }

    $(document).on('click', ".btn-view-group", function () {
        let manhom = $(this).data("id");
        let index = $(this).data("index");
        $(".offcanvas-title").text(`${groups[index].tennhom} - NH${groups[index].namhoc} - HK${groups[index].hocky} - ${groups[index].hoten}`)
        loadDataTest(manhom);
        loadDataFriend(manhom);
        loadDataAnnounce(manhom);
    });

    function showListTest(tests) {
      let html = ``;

      if (tests.length !== 0) {
        const format = new Intl.DateTimeFormat(navigator.language, {
          year: "numeric",
          month: "2-digit",
          day: "2-digit",
          hour: "2-digit",
          minute: "2-digit",
        });

        tests.forEach((test) => {
          const open = new Date(test.thoigianbatdau);
          let close =
            test.trangthai == 1
              ? new Date(open.getTime() + parseInt(test.thoigianthi) * 60000)
              : test.thoigianketthuc === "0000-00-00 00:00:00"
              ? null
              : new Date(test.thoigianketthuc);

          const now = new Date();
          const hinhthuc = test.trangthai == 1 ? "📝 Đề thi" : "📘 Đề ôn luyện";

          // Convert thoigianvaothi / lambai nếu có
          const hasEntered = !!test.thoigianvaothi;
          const hasFinished = !!test.thoigianlambai;

          let status = "";
          let color = "secondary";

          if (hasFinished) {
            color = "primary";
            status = `<span class="text-${color} fw-semibold">✅ Đã thi</span>`;
            if (parseInt(test.xemdiemthi) === 1 && test.diemthi !== null) {
              status += ` - <span>${parseFloat(test.diemthi).toFixed(
                2
              )} điểm</span>`;
            }
          } else if (hasEntered && now <= close) {
            color = "warning";
            status = `<span class="text-${color} fw-semibold">🟡 Đang làm bài</span>`;
          } else if (!hasEntered && now >= open && now <= close) {
            color = "success";
            status = `<span class="text-${color} fw-semibold">🟢 Đang diễn ra</span>`;
          } else if (now < open) {
            color = "secondary";
            status = `<span class="text-${color} fw-semibold">🕒 Chưa bắt đầu</span>`;
          } else if (!hasEntered && now > close) {
            color = "danger";
            status = `<span class="text-${color} fw-semibold">🔴 Đã hết hạn</span>`;
          }

          html += `
              <div class="block block-rounded block-fx-pop mb-2">
                <div class="block-content block-content-full border-start border-3 border-${color}">
                  <div class="d-md-flex justify-content-md-between align-items-md-center">
                    <div class="p-1 p-md-2">
                      <h3 class="h4 fw-bold mb-2">
                        <a href="./test/start/${test.made}?loainhom=nhom&manhom=${
            test.manhom
          }" class="text-dark link-fx">
                          ${test.tende}
                        </a>
                      </h3>
                      <p class="mb-1 text-muted">${hinhthuc}</p>
                   <p class="mb-1">
                        <i class="fa fa-clock me-1"></i> 
                        
                        ${
                          parseInt(test.trangthai) !== 1
                            ? `Từ <strong>${format.format(
                                open
                              )}</strong> đến <strong>${
                                close ? format.format(close) : "Không xác định"
                              }</strong>`
                            : ""
                        }
                        ${
                          parseInt(test.trangthai) === 1
                            ? `Bắt đầu <strong>${format.format(
                                open
                              )}</strong> | Thời gian: <strong>${
                                test.thoigianthi
                              } phút</strong>`
                            : ""
                        }
                        </p>


                            <p class="mb-0">
                            Trạng thái: ${status} 
                            ${
                              status.includes("Đã thi")
                                ? test.diemthi == null ||
                                  parseInt(test.xemdiemthi) === 0
                                  ? " - Điểm: Không công bố"
                                  : ` - Điểm: ${parseFloat(
                                      test.diemthi
                                    ).toFixed(2)}`
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
      
      

    function showAnnouncement(announces) {
        let html = "";
        if (announces.length != 0) {
            announces.forEach(announce => {
                html += `
                <li>
                <a class="d-flex text-dark py-2" href="javascript:void(0)">
                    <div class="flex-shrink-0 mx-3">
                        <img class="img-avatar img-avatar48" src="./public/media/avatars/${announce.avatar == null ? "avatar2.jpg" : announce.avatar}" alt="">
                    </div>
                    <div class="flex-grow-1 fs-sm pe-2">
                        <div class="fw-semibold">${announce.noidung}</div>
                        <div class="text-muted">${formatDate(announce.thoigiantao)}</div>
                    </div>
                </a>
            </li>
                `;
            })
        } else {
            html += `<p class="text-center">Không có thông báo</p>`
        }
        $(".list-announce").html(html);
    }

    function loadDataTest(manhom) {
        $.ajax({
            type: "post",
            url: "./test/getTestsGroupWithUserResult",
            data: {
                manhom: manhom
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                showListTest(response);
            }
        });
    }

    function loadDataFriend(manhom) {
        $.ajax({
            type: "post",
            url: "./client/getFriendList",
            data: {
                manhom: manhom
            },
            dataType: "json",
            success: function (response) {
                showListFriend(response);
            }
        });
    }

    function loadDataAnnounce(manhom)
    {
        $.ajax({
            type: "post",
            url: "./teacher_announcement/getAnnounce",
            data: {
                manhom: manhom
            },
            dataType: "json",
            success: function (response) {
                showAnnouncement(response);
            }
        });
    }

    function showListFriend(friends) {
        let html = ``;
        if(friends.length != 0) {
            friends.forEach(friend => {
                html += `<li>
                    <div class="d-flex py-2 align-items-center">
                        <div class="flex-shrink-0 mx-3 overlay-container">
                            <img class="img-avatar img-avatar48" src="./public/media/avatars/${friend.avatar == null ? "avatar2.jpg" : friend.avatar}" alt="">
                        </div>
                        <div class="fw-semibold">${friend.hoten}</div>
                    </div>
                </li>`
            });
        } else {
            html += `<p class="text-center">Không có dữ liệu</p>`
        }
        $(".list-friends").html(html);
    }

    $(".filter-search").click(function (e) { 
        e.preventDefault();
        mode = $(this).data("id");
        $(".btn-filter").text($(this).text());
        loadDataGroups(mode);
    });

    $("#form-search-group").on("input", function () {
        let content = $(this).val().toLowerCase();
        let result = groups.filter(item => item.tennhom.toLowerCase().includes(content) || item.hoten.toLowerCase().includes(content));
        showListGroup(result);
    });

    $(document).on("click", ".btn-hide-group", function () {
        let manhom = $(this).data("id");
        actionHide(manhom,0,"Ẩn nhóm thành công");
    });

    $(document).on("click", ".btn-unhide-group", function () {
        let manhom = $(this).data("id");
        actionHide(manhom,1,"Huỷ ẩn nhóm thành công");
    });

    function actionHide(manhom,value,message) {
        $.ajax({
            type: "post",
            url: "./client/hide",
            data: {
                manhom: manhom,
                giatri: value
            },
            success: function (response) {
                if (response) {
                    let index = groups.findIndex(item => item.manhom == manhom)
                    groups.splice(index,1);
                    showListGroup(groups);
                    Dashmix.helpers('jq-notify', { type: 'success', icon: 'fa fa-check me-1', message: message });
                }
            },
        });
    }

    $(document).on("click", ".btn-delete-group", function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-2",
                cancelButton: "btn btn-danger",
            },
            buttonsStyling: false,
        });

        swalWithBootstrapButtons
            .fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, out groups section!",
                cancelButtonText: "No, cancel!",
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "./client/delete",
                        data: {
                            manhom: $(this).data("id"),
                        },
                        success: function (response) {
                            if (response) {
                                swalWithBootstrapButtons.fire(
                                    "Thoát thành công!",
                                    "Bạn đã thoát nhóm thành công",
                                    "success"
                                );
                                loadDataGroups(mode);
                            }
                        },
                    });
                }
            });
    })
});