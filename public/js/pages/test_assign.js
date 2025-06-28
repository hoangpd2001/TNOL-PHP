Dashmix.helpersOnLoad(["js-flatpickr", "jq-datepicker", "jq-select2"]);

let listnhoms = [];
let listhocphans=[];
let listdes=[];

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
          "validTimeEnd",
          function (value, element) {
            var startTime = new Date($("#time-start").val());
            var currentTime = new Date();
            var endTime = new Date(value);
            return endTime > startTime && endTime > currentTime;
          },
          "Thời gian kết thúc phải lớn hơn thời gian bắt đầu và không bé hơn thời gian hiện tại"
        );

      $.validator.addMethod(
        "validTimeStart",
        function (value, element) {
          var startTime = new Date(value);
          var currentTime = new Date();
          return startTime > currentTime;
        },
        "Thời gian bắt đầu không được bé hơn thời gian hiện tại"
      );

      jQuery(".form-taodethi").validate({
        rules: {
          "name-exam": {
            required: true,
          },
          "time-start": {
            required: !0,
            validTimeStart: true,
          },
          "time-end": {
            required: !0,
            validTimeEnd: true,
          },
          // "nhom": {
          //   required: !0,
          // },
          user_nhomquyen: {
            required: !0,
          },
         
        
        },
        messages: {
          "name-exam": {
            required: "Vui lòng nhập tên đề kiểm tra",
          },
          "time-start": {
            required: "Vui lòng chọn thời điểm bắt đầu của bài kiểm tra",
            validTimeStart:
              "Thời gian bắt đầu không được bé hơn thời gian hiện tại",
          },
          "time-end": {
            required: "Vui lòng chọn thời điểm kết thúc của bài kiểm tra",
            validTimeEnd: "Thời gian kết thúc không hợp lệ",
          },
          // "nhom": {
          //   required: "Vui lòng chọn nhóm học phần giảng dạy",
          // },
       
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
  setlisthocphan();
  $("#name-exam").prop("disabled", true);
  setListde(-1);
  function setlistnhom() {
    let html = "<option></option>";
    $.ajax({
      type: "post",
      url: "./module/loadData",
      async: false,
      data: {
        hienthi: 1,
      },
      dataType: "json",
      success: function (response) {
        listnhoms = response;
    console.log("duocgoi")
        response.forEach((item, index) => {
          html += `<option value="${index}">${
            item.hoten +
            " - HK" +
            item.hocky +
            " - NH" +
            item.namhoc 
          }</option>`;
        });
        $("#nhom").html(html);
        
      },
    });
  }
  function setlisthocphan() {
    let html = "<option></option>";
    $.ajax({
      type: "post",
      url: "./classmodule/loadData",
      async: false,
      data: {
        hienthi: 1,
      },
      dataType: "json",
      success: function (response) {
        listhocphans = response;
        console.log(response);
        response.forEach((item, index) => {
          html += `<option value="${index}">${
            item.mamonhoc +
            " - " +
            item.tenmonhoc +
            " - " +
            item.tennganh +
            " - " +
            item.tenkhoahoc
          }</option>`;
        });
        $("#hocphan").html(html);

      },
    });
  }
  function setListde(loaide, mamonhoc) 
  {
    console.log("setlistdene : mh", mamonhoc, "   loaide : ", loaide );
    let html = "<option></option>";
    $.ajax({
      type: "post",
      url: "./test/getTestByUserAndType",
      async: false,
      data: {
        trangthai: loaide,
        mamonhoc:mamonhoc
      },
      dataType: "json",
      success: function (response) {
        listdes = response;
        console.log(response);
        response.forEach((item) => {
          html += `<option value="${item.made}">${
            item.made +
            " - " +
            item.tende 
          }</option>`;
        });
        $("#name-exam").html(html);
      },
    });
  }
  // Khi chọn nhóm học phần 
  $("#nhom").on("change", function () {
    let index = $(this).val();
    showListnhom(index);
  //  showListnhom();
  });

  $("#hocphan").on("change", function () {
    let index = $(this).val();
    showListHocPhan(index);
    $("#name-exam").prop("disabled", false); 
    const mode = $('input[name="nhom_hocphan_mode"]:checked').val();
    const loai = $('input[name="loaide_mode"]:checked').val();
    const hocphanId = $("#hocphan").val(); // lấy id học phần đang chọn
    if (hocphanId && listhocphans[hocphanId]) {
      const mamonhoc = listhocphans[hocphanId].mamonhoc;
      setListde(loai, mamonhoc);
    } else {
      console.warn("Chưa chọn học phần hoặc không tìm thấy môn học.");
    }
  });

  // Hiển thị danh sách nhóm học phần
  function showListnhom(index) {
    let html = ``;
    console.log("listnhom: ", listnhoms)
    if (listnhoms[index].nhom.length > 0) {
      html += `<div class="col-12 mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="select-all-listnhom">
                <label class="form-check-label" for="select-all-listnhom">Chọn tất cả</label>
            </div></div>`;
      listnhoms[index].nhom.forEach((item) => {
        html += `<div class="col-4">
                    <div class="form-check">
                        <input class="form-check-input select-listnhom-item" type="checkbox" value="${item.manhom}"
                            id="nhom-${item.manhom}" name="nhom-${item.manhom}">
                        <label class="form-check-label" for="nhom-${item.manhom}">${item.tennhom}</label>
                    </div>
                </div>`;
      });
    } else {
      html += `<div class="text-center fs-sm"><img style="width:100px" src="./public/media/svg/empty_data.png" alt=""></div>`;
    }
    $("#list-nhom").html(html);
  }

  // Chọn || Huỷ chọn tất cả nhóm
  $(document).on("click", "#select-all-listnhom", function () {
    let check = $(this).prop("checked");
    $(".select-listnhom-item").prop("checked", check);
  });

  // Lấy các nhóm được chọn
  function getlistnhomSelected() {
    let result = [];
    $(".select-listnhom-item").each(function () {
      if ($(this).prop("checked") == true) {
        result.push($(this).val());
      }
    });
    return result;
  }
  function showListHocPhan(index) {
    let html = ``;
    console.log("listhocphan: ", listhocphans);
    if (listhocphans[index].lop.length > 0) {
      html += `<div class="col-12 mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="select-all-listhocphan">
                <label class="form-check-label" for="select-all-listhocphan">Chọn tất cả</label>
            </div></div>`;
      listhocphans[index].lop.forEach((item) => {
        html += `<div class="col-4">
                    <div class="form-check">
                        <input class="form-check-input select-listhocphan-item" type="checkbox" value="${item.mahocphan}"
                            id="hocphan-${item.mahocphan}" name="hocphan-${item.mahocphan}">
                        <label class="form-check-label" for="hocphan-${item.mahocphan}">${item.tenlop}</label>
                    </div>
                </div>`;
      });
    } else {
      html += `<div class="text-center fs-sm"><img style="width:100px" src="./public/media/svg/empty_data.png" alt=""></div>`;
    }
    $("#list-hocphan").html(html);
  }

  // Chọn || Huỷ chọn tất cả nhóm
  $(document).on("click", "#select-all-listhocphan", function () {
    let check = $(this).prop("checked");
    $(".select-listhocphan-item").prop("checked", check);
  });

  // Lấy các nhóm được chọn
  function getlisthocphanSelected() {
    let result = [];
    $(".select-listhocphan-item").each(function () {
      if ($(this).prop("checked") == true) {
        result.push($(this).val());
      }
    });
    return result;
  }
  $('input[name="nhom_hocphan_mode"]').on("change", function () {
    const mode = $(this).val();
    if (mode === "hocphan") {
      $("#hocphan-wrapper").show();
      $("#nhom-wrapper").hide();
      $("#name-exam").prop("disabled", true); 
    setlisthocphan();
    } else {
        setlistnhom();
      $("#hocphan-wrapper").hide();
      $("#nhom-wrapper").show();
      $("#name-exam").prop("disabled", false);
      const loai = $('input[name="loaide_mode"]:checked').val();
      setListde(loai);
    }
  });
  $('input[name="loaide_mode"]').on("change", function () {
    const loai = $(this).val();
    $("#name-exam").prop("disabled", false);
    const mode = $('input[name="nhom_hocphan_mode"]:checked').val();
    if (loai === "-1") {
      $("#time-end-wrapper").show();
        if(mode == "hocphan"){
            const hocphanId = $("#hocphan").val();
            if (hocphanId && listhocphans[hocphanId]) {
              const mamonhoc = listhocphans[hocphanId].mamonhoc;
              setListde(loai, mamonhoc);
            } else {
              console.warn("Chưa chọn học phần hoặc không tìm thấy môn học.");
            }
        }else{
            setListde(loai);
        }
    } else {
      $("#time-end-wrapper").hide();
      if (mode == "hocphan") {
        const hocphanId = $("#hocphan").val();
        if (hocphanId && listhocphans[hocphanId]) {
          const mamonhoc = listhocphans[hocphanId].mamonhoc;
          setListde(loai, mamonhoc);
        } else {
          console.warn("Chưa chọn học phần hoặc không tìm thấy môn học.");
        }
      }else{
        setListde(loai);
      }
    } 
  });
  
  
 

  // Xừ lý sự kiện nhấn nút tạo đề
  $("#btn-add-test").click(function (e) {
    e.preventDefault();
    if ($(".form-taodethi").valid()) {
      const mode = $('input[name="nhom_hocphan_mode"]:checked').val();
      let manhomOrhocphan;
      let hinhthuc;
      if (mode == "hocphan") {
        manhomOrhocphan = getlisthocphanSelected();
        hinhthuc = "hocphan"
        console.log("hocphan",manhomOrhocphan);
      } else {
        manhomOrhocphan = getlistnhomSelected();
        hinhthuc = "nhom";
        console.log("nhom",manhomOrhocphan);
      }      
      if (manhomOrhocphan.length !=0) {
        console.log("batdau");
        console.log($("#name-exam").val());
        $.ajax({
          type: "post",
          url: "./test/addAssign",
          data: {
            made: $("#name-exam").val(),
            thoigianbatdau: $("#time-start").val(),
            thoigianketthuc: $("#time-end").val(),
            hinhthuc:hinhthuc,
            manhom: manhomOrhocphan,
          },
          success: function (response) {
            console.log(response);
            if (response) {
              Dashmix.helpers("jq-notify", {
                type: "success",
                icon: "fa fa-times me-1",
                message: "Giao đề thành công!",
              });
              location.href = "./test/review"; 
            } else {
              Dashmix.helpers("jq-notify", {
                type: "danger",
                icon: "fa fa-times me-1",
                message: "Đề này đã được giao trước đó rồi!",
              });
            }
          },
        });
      } else {
        Dashmix.helpers("jq-notify", {
          type: "success",
          icon: "fa fa-times me-1",
          message: "Bạn phải chọn ít nhất một nhóm học phần!",
        });
      
      }
    }
  });

 

 


  // Xử lý nút cập nhật đề thi
  
});
