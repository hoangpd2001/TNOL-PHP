Dashmix.helpersOnLoad(["js-flatpickr", "jq-datepicker", "jq-select2"]);

let listMon = [];
$(document).ready(function () {
  $(".js-select2").select2({
    width: "100%",
  });
});
$("#chuong, #dethimau").select2({
  width: "100%",
});
function getToTalQuestionOfChapter(chuong, monhoc, dokho) {
  var result = 0;

  
  $.ajax({
    url: "./question/getsoluongcauhoi",
    type: "post",
    data: {
      chuong: chuong,
      monhoc: monhoc,
      dokho: dokho,
    },
    async: false,
    success: function (response) {
      result = response;
     
    },
  });
  return result;
}

function getMinutesBetweenDates(start, end) {
  // Chuyển đổi đối số thành đối tượng Date
  const startDate = new Date(start);
  const endDate = new Date(end);

  // Tính số phút giữa hai khoảng thời gian
  const diffMs = endDate.getTime() - startDate.getTime();
  const diffMins = Math.round(diffMs / 60000);

  // Trả về số phút tính được
  return diffMins;
}

Dashmix.onLoad(() =>
  class {
    static initValidation() {
      Dashmix.helpers("jq-validation"),
       
        $.validator.addMethod(
          "validSoLuong",
          function (value, element, param) {
            // Lấy giá trị chương, lọc bỏ các phần tử rỗng hoặc undefined
           
            let rawChuong = $("#chuong").val();
            let c = Array.isArray(rawChuong)
              ? rawChuong.filter((item) => item !== "")
              : [];

            // Lấy mã môn học từ nhóm học phần
            let m = $("#monhoc").val(); 

            // Gọi hàm lấy số lượng câu hỏi từ chương
            let result =
              parseInt(getToTalQuestionOfChapter(c, m, param)) >=
              parseInt(value);

            return result;
          },
          "Số lượng câu hỏi không đủ"
        );
        

      jQuery(".form-taodethi").validate({
        rules: {
          "name-exam": {
            required: true,
          },
     
          "exam-time": {
            required: !0,
            digits: true,
            // validThoigianthi: true,
          },
          "monhoc": {
            required: !0,
          },
          user_nhomquyen: {
            required: !0,
          },
          chuong: {
            required: !0,
          },
          coban: {
            required: !0,
            digits: true,
            validSoLuong: 1,
          },
          trungbinh: {
            required: !0,
            digits: true,
            validSoLuong: 2,
          },
          kho: {
            required: !0,
            digits: true,
            validSoLuong: 3,
          },
        },
        messages: {
          "name-exam": {
            required: "Vui lòng nhập tên đề kiểm tra",
          },
          "exam-time": {
            required: "Vui lòng chọn thời gian làm bài kiểm tra",
          },
          "monhoc": {
            required: "Vui lòng chọn nhóm học phần giảng dạy",
          },
          chuong: {
            required: "Vui lòng chọn số chương cho đề kiểm tra",
          },
          coban: {
            required: "Vui lòng cho biết số câu dễ",
            digits: "Vui lòng nhập số",
          },
          trungbinh: {
            required: "Vui lòng cho biết số câu trung bình",
            digits: "Vui lòng nhập số",
          },
          kho: {
            required: "Vui lòng cho biết số câu khó",
            digits: "Vui lòng nhập số",
          },
        },
      });
    }
    static init() {
      this.initValidation();
    }
  }.init()
);

$(document).ready(function () {
  // Xử lý cắt url để lấy mã đề thi
  let url = location.href.split("/");
  let param = 0;
  if (url[url.length - 2] == "update") {
    param = url[url.length - 1];
    getDetail(param);
  }

  function showGroup() {
    let html = "<option></option>";
    $.ajax({
      type: "post",
      url: "./subject/getAllByFaculty",
      async: false,
      data: {
        hienthi: 1,
      },
      dataType: "json",
      success: function (response) {
        console.log(response);
        listMon = response;
        response.forEach((item, index) => {
          html += `<option value="${item.mamonhoc}">${
            item.mamonhoc + " - " + item.tenmonhoc
          }</option>`;
        });
        $("#monhoc").html(html);
      },
    });
  }

  // Khi chọn nhóm học phần thì chương sẽ tự động đổi để phù hợp với môn học
  $("#monhoc").on("change", function () {
    let mamonhoc = $("#monhoc").val();
    showChapter(mamonhoc);
    showTest(mamonhoc);
  });

  // Hiển thị chương
  function showChapter(mamonhoc) {
    let html = "<option value=''></option>";
    $("#chuong").val(null).trigger("change");
    $.ajax({
      type: "post",
      url: "./subject/getAllChapter",
      async: false,
      data: {
        mamonhoc: mamonhoc,
      },
      dataType: "json",
      success: function (data) {
        data.forEach((item) => {
          html += `<option value="${item.machuong}">${item.tenchuong}</option>`;
        });
        $("#chuong").html(html);
        $("#chuong").select2({
          width: "100%", // đảm bảo full width
        });
      },
    });
  }

  function showTest(mamonhoc) {
    console.log("duoc goi");
    let html = "<option value=''></option>";
    $("#dethimau").val(null).trigger("change");
    $.ajax({
      type: "post",
      url: "./test/getAllTestByUserAndSubject",
      async: false,
      data: {
        mamonhoc: mamonhoc,
      },
      dataType: "json",
      success: function (dethis) {
        let html = "";

        dethis.forEach((item, index) => {
          html += `
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" 
              name="dethimau[]" 
              id="de_${item.made}" 
              value="${item.made}"
              data-socaude="${item.socaude}" 
              data-socautb="${item.socautb}" 
              data-socaukho="${item.socaukho}" 
              data-thoigianthi="${item.thoigianthi}">
            <label class="form-check-label" for="de_${item.made}">
              <strong>${item.tende}</strong> | Người tạo: ${item.nguoitao} | 
              Số câu: ${item.socaude}/${item.socautb}/${item.socaukho} | 
              Thời gian: ${item.thoigianthi} phút
            </label>
          </div>
        `;
        
        });

        $("#list-dethimau").html(html);
        $("#dethimau").select2({
          width: "100%", // đảm bảo full width
        });
      },
    });
  }
  // Lắng nghe sự kiện sau khi render xong danh sách đề
  $(document).on("change", 'input[name="dethimau[]"]', function () {

    const checked = $('input[name="dethimau[]"]:checked');
    if (checked.length === 0) {
      // Nếu không chọn đề nào thì hiện lại hết
      $('input[name="dethimau[]"]').closest(".form-check").show();
      $("#coban").val("");
      $("#trungbinh").val("");
      $("#kho").val("");
      $("#exam-time").val("");
      return;
    }

    // Lấy đề đầu tiên được chọn làm chuẩn
    const firstChecked = checked.first();
    const id = firstChecked.val();

    // Lấy dữ liệu đề mẫu từ checkbox HTML
    const [socaude, socautb, socaukho, thoigianthi] = [
      firstChecked.data("socaude"),
      firstChecked.data("socautb"),
      firstChecked.data("socaukho"),
      firstChecked.data("thoigianthi"),
    ];
    $("#coban").val(socaude);
    $("#trungbinh").val(socautb);
    $("#kho").val(socaukho);
    $("#exam-time").val(thoigianthi);
  
    // Duyệt tất cả checkbox để kiểm tra
    $('input[name="dethimau[]"]').each(function () {
      const current = $(this);
      const match =
        current.data("socaude") == socaude &&
        current.data("socautb") == socautb &&
        current.data("socaukho") == socaukho &&
        current.data("thoigianthi") == thoigianthi;

      // Nếu không match, ẩn đi
      if (!match && !current.is(":checked")) {
        current.closest(".form-check").hide();
      } else {
        current.closest(".form-check").show();
      }
    });
  });

  // Chọn || Huỷ chọn tất cả nhóm
  $(document).on("click", "#select-all-group", function () {
    let check = $(this).prop("checked");
    $(".select-group-item").prop("checked", check);
  });

  // Lấy các nhóm được chọn
  function getlistMonelected() {
    let result = [];
    $(".select-group-item").each(function () {
      if ($(this).prop("checked") == true) {
        result.push($(this).val());
      }
    });
    return result;
  }
  $(".show-dethi").hide();
  $("input[name='loaide']").on("change", function () {
    const value = $(this).val(); // 1, 2, hoặc 0

    if (value == "1") {
      // Tự động -> hiện chương, ẩn đề mẫu
      $(".show-chap").show();
      $(".show-dethi").hide();
      $("#dethimau").val(null).trigger("change");
    } else if (value == "2") {
      // Đề mẫu -> ẩn chương, hiện đề mẫu
      $(".show-chap").hide();
      $(".show-dethi").show();
      $("#chuong").val(null).trigger("change");
    } else {
      // Thủ công -> ẩn cả chương và đề mẫu
      $(".show-chap").hide();
      $(".show-dethi").hide();
      $("#chuong, #dethimau").val(null).trigger("change");
    }
  });

  showGroup();

  // Xừ lý sự kiện nhấn nút tạo đề
  $("#btn-add-test").click(function (e) {
    e.preventDefault();
    if ($(".form-taodethi").valid()) {
      const selectedTests = $('input[name="dethimau[]"]:checked')
        .map(function () {
          return $(this).val();
        })
        .get();
      if (
        selectedTests.length <= 1 &&
        Number($("input[name='loaide']:checked").val())==2
      ) {
        Dashmix.helpers("jq-notify", {
          type: "danger",
          icon: "fa fa-times me-1",
          message: "Số lượng đề thi mẫu phải lớn hơn 1!",
        });
      }

      $.ajax({
        type: "post",
        url: "./test/addTest",
        data: {
          mamonhoc: $("#monhoc").val(),
          tende: $("#name-exam").val(),
          thoigianthi: $("#exam-time").val(),
          socaude: $("#coban").val(),
          socautb: $("#trungbinh").val(),
          socaukho: $("#kho").val(),
          chuong: $("#chuong").val(),
          loaide: Number($("input[name='loaide']:checked").val()),
          trangthai: $("#trangthai").prop("checked") ? 0 : -1,
          xemdiem: $("#xemdiem").prop("checked") ? 1 : 0,
          xemdapan: $("#xemda").prop("checked") ? 1 : 0,
          xembailam: $("#xembailam").prop("checked") ? 1 : 0,
          daocauhoi: $("#daocauhoi").prop("checked") ? 1 : 0,
          daodapan: $("#daodapan").prop("checked") ? 1 : 0,
          tudongnop: $("#tudongnop").prop("checked") ? 1 : 0,
          dethimau: selectedTests,
        },
        success: function (response) {
          if (response) {
            console.log(response);
            const loaide = $("input[name='loaide']:checked").val();
            if (loaide != 0) {
              location.href = "./test/base"; // Tự động
            } else {
              location.href = `./test/select/${response}`; //tạo đề thủ công
            }
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Tạo đề thi không thành công!",
            });
          }
        },
      });
    }
  });

  /*Chỉnh sửa đề thi*/
  $("#btn-update-quesoftest").hide();
  // Khởi tạo biến đề thi để chứa thông tin đề
  let infodethi;
  showGroup();
  function getDetail(made) {
    return $.ajax({
      type: "post",
      url: "./test/getDetail",
      data: {
        made: made,
      },
      dataType: "json",
      success: function (response) {
        if (response.loaide == 0) {
          $("#btn-update-quesoftest").show();
          $("#btn-update-quesoftest").attr(
            "href",
            `./test/select/${response.made}`
          );
        }
        infodethi = response;
        showInfo(response);
      },
    });
  }

  function checkDate(time) {
    let valid = true;
    let dateToCompare = new Date(time);
    let currentTime = new Date(); // Thời gian hiện tại
    if (dateToCompare.getTime() >= currentTime.getTime()) valid = false;
    return valid;
  }

  // Hiển thị thông tin đề thi
  function showInfo(dethi) {
    let checkD = checkDate(dethi.thoigianbatdau);
    $("#name-exam").val(dethi.tende),
      $("#exam-time").val(dethi.thoigianthi),
      $("#time-start").flatpickr({
        enableTime: true,
        altInput: true,
        allowInput: checkD,
        defaultDate: dethi.thoigianbatdau,
        onReady: function (selectedDates, dateStr, instance) {
          if (checkD) {
            $(instance.input).prop("disabled", true);
            instance._input.disabled = true;
          }
        },
      });
    $("#time-end").flatpickr({
      enableTime: true,
      altInput: true,
      allowInput: true,
      defaultDate: dethi.thoigianketthuc,
    });
    $("#coban").val(dethi.socaude), $("#coban").prop("disabled", checkD);
    $("#trungbinh").val(dethi.socautb),
      $("#trungbinh").prop("disabled", checkD);
    $("#kho").val(dethi.socaukho), $("#kho").prop("disabled", checkD);
    $(`input[name='loaide'][value='${dethi.loaide}']`).prop("checked", true);
    if (checkD) {
      $("input[name='loaide']").prop("disabled", true);
    }
    $("#xemdiem").prop("checked", dethi.xemdiemthi == "1");
    $("#xemda").prop("checked", dethi.xemdapan == "1");
    $("#xembailam").prop("checked", dethi.xemdapan == "1");
    $("#daocauhoi").prop("checked", dethi.troncauhoi == "1");
    $("#daodapan").prop("checked", dethi.trondapan == "1");
    $("#tudongnop").prop("checked", dethi.nopbaichuyentab == "1");
    $("#btn-update-test").data("id", dethi.made);

    $.when(showGroup(), showChapter(dethi.monthi)).done(function () {
      $("#monhoc").val(dethi.monthi).trigger("change").prop("disabled", true);
      setGroup(dethi.nhom, dethi.thoigianbatdau);
      if (dethi.loaide == "1") {
        $("#chuong").prop("disabled", checkD);
        $("#chuong").val(dethi.chuong).trigger("change");
      } else $(".show-chap").hide();
    });
  }

  function setGroup(list, date) {
    let v = checkDate(date);
    $("#select-all-group").prop("disabled", v);
    list.forEach((item) => {
      $(`.select-group-item[value='${item}']`).prop("checked", true);
      $(`.select-group-item[value='${item}']`).prop("disabled", v);
    });
  }

  function validUpdate() {
    let check = true;
    if ($("#name-exam").val() == "") {
      Dashmix.helpers("jq-notify", {
        type: "danger",
        icon: "fa fa-times me-1",
        message: "Tên đề không được để trống",
      });
      check = false;
    }
    var startTime = new Date($("#time-start").val());
    var endTime = new Date($("#time-end").val());

    if (endTime <= startTime) {
      Dashmix.helpers("jq-notify", {
        type: "danger",
        icon: "fa fa-times me-1",
        message: "Thời gian kết thúc không được bé hơn thời gian bắt đầu",
      });
      check = false;
    }

    if (endTime < new Date(infodethi.thoigianketthuc)) {
      Dashmix.helpers("jq-notify", {
        type: "danger",
        icon: "fa fa-times me-1",
        message: "Thời gian kết thúc không được bé hơn thời gian kết thúc cũ",
      });
      check = false;
    }

    console.log(getMinutesBetweenDates(startTime, endTime));
    if (
      endTime > startTime &&
      getMinutesBetweenDates(startTime, endTime) < infodethi.thoigianthi
    ) {
      Dashmix.helpers("jq-notify", {
        type: "danger",
        icon: "fa fa-times me-1",
        message: "Thời gian làm bài không hợp lệ",
      });
      check = false;
    }

    return check;
  }

  // Xử lý nút cập nhật đề thi
  $("#btn-update-test").click(function (e) {
    e.preventDefault();
    if (
      (!checkDate(infodethi.thoigianbatdau) && $(".form-taodethi").valid()) ||
      validUpdate()
    ) {
      let loaide = parseInt($("input[name='loaide']:checked").val());
      let made = $(this).data("id");
      let socaude = $("#coban").val();
      let socautb = $("#trungbinh").val();
      let socaukho = $("#kho").val();
      $.ajax({
        type: "post",
        url: "./test/updateTest",
        data: {
          made: made,
          mamonhoc: $("#monhoc").val(),
          tende: $("#name-exam").val(),
          thoigianthi: $("#exam-time").val(),
          thoigianbatdau: $("#time-start").val(),
          thoigianketthuc: $("#time-end").val(),
          socaude: socaude,
          socautb: socautb,
          socaukho: socaukho,
          chuong: $("#chuong").val(),
          loaide: loaide,
          xemdiem: $("#xemdiem").prop("checked") ? 1 : 0,
          xemdapan: $("#xemda").prop("checked") ? 1 : 0,
          xembailam: $("#xembailam").prop("checked") ? 1 : 0,
          daocauhoi: $("#daocauhoi").prop("checked") ? 1 : 0,
          daodapan: $("#daodapan").prop("checked") ? 1 : 0,
          tudongnop: $("#tudongnop").prop("checked") ? 1 : 0,
          manhom: getlistMonelected(),
        },
        success: function (response) {
          if (response) {
            if (
              (infodethi.loaide == 1 && loaide == 0) ||
              (loaide == 0 &&
                (infodethi.socaude != socaude ||
                  infodethi.socautb != socautb ||
                  infodethi.socaukho != socaukho))
            ) {
              location.href = `./test/select/${made}`;
            } else {
              location.href = `./test/base`;
            }
          } else {
            Dashmix.helpers("jq-notify", {
              type: "danger",
              icon: "fa fa-times me-1",
              message: "Cập nhật đề thi không thành công!",
            });
          }
        },
      });
    }
  });
});
