$(document).ready(function () {
  let questions = [];
  const mamon = $("#dethicontent").data("id");
  const thoigian = parseInt($("#dethicontent").data("thoigian"));

  const chuongs = $("#dethicontent").data("chuongs");
  const socau = $("#dethicontent").data("socau");
  const dethi = "dethitudong";
  const cautraloi = "cautraloitudong";
  const endTimeKey = "endTime_tuluyen";
  console.log(mamon);
  console.log(thoigian);
  console.log(chuongs);
  console.log(socau);

  function getQuestion() {
    console.log("getquestion duoc goi");
    return $.ajax({
      type: "post",
      url: "./question/getQuestionByClientReview",
      data: {
        chuongs: chuongs,
        mamon: mamon,
        socau: socau,
      },
      dataType: "json",
      success: function (response) {
        console.log(response);
        questions = response;
      },
    });
  }

  function showListQuestion(questions, answers) {
    let html = ``;
    console.log("data", dethidata);
    console.log(questions);
    console.log(answers);
    questions.forEach((question, index) => {
      html += `<div class="question rounded border mb-3 bg-white" id="c${
        index + 1
      }">
          <div class="question-top p-3">
              <p class="question-content fw-bold mb-3">${index + 1}. ${
        question.noidung
      }</p>
              <div class="row">`;
      question.cautraloi.forEach((ctl, i) => {
        html += `<div class="col-6 mb-1">
                  <p class="mb-1"><b>${String.fromCharCode(i + 65)}.</b> ${
          ctl.noidungtl
        }</p>
              </div>`;
      });
      html += `</div></div><div class="test-ans bg-primary rounded-bottom py-2 px-3 d-flex align-items-center"><p class="mb-0 text-white me-4">Đáp án của bạn:</p><div>`;
      question.cautraloi.forEach((ctl, i) => {
        let check = answers[index].cautraloi == ctl.macautl ? "checked" : "";
        html += `<input type="radio" class="btn-check" name="options-c${
          index + 1
        }" id="ctl-${ctl.macautl}" autocomplete="off" data-index="${
          index + 1
        }" data-macautl="${ctl.macautl}" ${check}>
                      <label class="btn btn-light rounded-pill me-2 btn-answer" for="ctl-${
                        ctl.macautl
                      }">${String.fromCharCode(i + 65)}</label>`;
      });
      html += `</div></div></div>`;
    });
    $("#list-question").html(html);
  }

  function showBtnSideBar(questions, answers) {
    let html = ``;
    questions.forEach((q, i) => {
      let isActive = answers[i].cautraloi == 0 ? "" : " active";
      html += `<li class="answer-item p-1"><a href="javascript:void(0)" class="answer-item-link btn btn-outline-primary w-100 btn-sm${isActive}" data-index="${
        i + 1
      }">${i + 1}</a></li>`;
    });
    $(".answer").html(html);
  }

  function initListAnswer(questions) {
    let listAns = questions.map((item) => {
      let itemAns = {};
      itemAns.macauhoi = item.macauhoi;
      itemAns.cautraloi = 0;
      return itemAns;
    });
    return listAns;
  }

  function changeAnswer(index, dapan) {
    let listAns = JSON.parse(localStorage.getItem(cautraloi));
    listAns[index].cautraloi = dapan;
    localStorage.setItem(cautraloi, JSON.stringify(listAns));
  }

  $.when(getQuestion()).done(function () {
    console.log("duoc chay");
    if (localStorage.getItem(dethi) == null) {
      localStorage.setItem(dethi, JSON.stringify(questions));
    }
    if (localStorage.getItem(cautraloi) == null) {
      localStorage.setItem(
        cautraloi,
        JSON.stringify(initListAnswer(questions))
      );
    }

    let listQues = JSON.parse(localStorage.getItem(dethi));
    let listAns = JSON.parse(localStorage.getItem(cautraloi));
    showListQuestion(listQues, listAns);
    showBtnSideBar(listQues, listAns);
    startCountdownFromGlobalTime();
  });

  $(document).on("click", ".btn-check", function () {
    let ques = $(this).data("index");
    $(`[data-index='${ques}']`).addClass("active");
    changeAnswer(ques - 1, $(this).data("macautl"));
  });

  $(document).on("click", ".answer-item-link", function () {
    let ques = $(this).data("index");
    document.getElementById(`c${ques}`).scrollIntoView();
  });

  $("#btn-nop-bai").click(function (e) {
    e.preventDefault();
    Swal.fire({
      title: "<p class='fs-3 mb-0'>Bạn có chắc chắn muốn nộp bài ?</p>",
      html: "<p class='text-muted fs-6 text-start mb-0'>Khi xác nhận nộp bài, bạn sẽ không thể sửa lại bài thi của mình. Chúc bạn may mắn!</p>",
      icon: "info",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Vâng, chắc chắn!",
      cancelButtonText: "Huỷ",
    }).then((result) => {
      if (result.isConfirmed) {
        nopbai();
      }
    });
  });
  function nopbai() {
    const dethiCheck = $("#dethicontent").data("id");
    const listAns = JSON.parse(localStorage.getItem(cautraloi));
    console.log("Đáp án người dùng:", listAns);

    $.ajax({
      type: "post",
      url: "./question/getAnserByClientReview",
      data: {
        cauhois: questions,
      },
      success: function (response) {
        const cauhois = JSON.parse(response);
        console.log("Dữ liệu từ server:", cauhois);

        let correct = 0;
        let html = `<div class="text-start">`;

        cauhois.forEach((q, index) => {
          let cauhoidetail = questions.find(
            (item) => item.macauhoi == q.macauhoi
          );
          const userAnswer = listAns.find(
            (item) => item.macauhoi == q.macauhoi
          );
          const userChoice = userAnswer ? userAnswer.cautraloi : null;
          const correctAnswer = q.cautraloi;

          const isCorrect = userChoice == correctAnswer;
          if (isCorrect) correct++;

          html += `
              <div class="mb-4 p-3 border rounded shadow-sm ${
                isCorrect ? "border-success" : "border-danger"
              }">
                <p class="fw-bold mb-2">Câu ${index + 1}: ${q.noidung}</p>
                <div class="list-group">
            `;

          cauhoidetail.cautraloi.forEach((ctl, i) => {
            const isUserChoice = ctl.macautl == userChoice;
            const isCorrectAnswer = ctl.macautl == correctAnswer;
            const optionLabel = String.fromCharCode(65 + i); // A, B, C, D

            let bgClass = "bg-light";
            let icon = "";
            let textClass = "text-dark";

            if (isUserChoice && isCorrectAnswer) {
              bgClass = "bg-success text-white";
              icon = "✅";
            } else if (isUserChoice && !isCorrectAnswer) {
              bgClass = "bg-danger text-white";
              icon = "❌";
            } else if (!isUserChoice && isCorrectAnswer) {
              bgClass = "bg-success bg-opacity-25";
              icon = "✔️";
            }

            html += `
                <div class="list-group-item ${bgClass} ${textClass} rounded mb-1">
                  <strong>${optionLabel}.</strong> ${ctl.noidungtl}
                  <span class="float-end">${icon}</span>
                </div>
              `;
          });

          html += `
                </div>
                <div class="mt-2">
                  <span class="badge ${
                    isCorrect ? "bg-success" : "bg-danger"
                  } fs-6">
                    ${isCorrect ? "Đúng" : "Sai"}
                  </span>
                </div>
              </div>
            `;
        });
          
        html += `</div>`;
        const total = cauhois.length;
        const score = ((correct / total) * 10).toFixed(2);
        html += `<div class="fs-4 fw-bold mt-4">Kết quả: ${correct}/${total} câu đúng (Điểm: ${score})</div>`;

        Swal.fire({
          title: "Kết quả bài làm",
          html: html,
          width: "60rem",
          showConfirmButton: true,
          confirmButtonText: "OK",
          customClass: {
            htmlContainer: "text-start",
          },
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "client/autoreview";
          }
        });
          
        localStorage.removeItem(cautraloi);
        localStorage.removeItem(dethi);
        localStorage.removeItem("endTime_tuluyen");
      },
      error: function (err) {
        console.error("Lỗi khi gửi dữ liệu:", err);
        Swal.fire("Lỗi", "Không thể nộp bài. Vui lòng thử lại!", "error");
      },
    });

    // Hàm helper đổi mã đáp án thành chữ (A, B, C, ...)
    function renderOptionLabel(macautl) {
      // Tìm đáp án trong questions để lấy vị trí
      for (const q of questions) {
        const ansIndex = q.cautraloi.findIndex((ctl) => ctl.macautl == macautl);
        if (ansIndex >= 0) {
          return String.fromCharCode(65 + ansIndex); // A, B, C, D,...
        }
      }
      return "(không rõ)";
    }
  }
  
  $("#btn-thoat").click(function (e) {
    e.preventDefault();
    Swal.fire({
      title: "Bạn có chắc chắn muốn thoát ?",
      html: "<p class='text-muted fs-6 text-start mb-0'>Khi xác nhận thoát, bạn sẽ không được tiếp tục làm bài ở lần thi này. Kết quả bài làm vẫn sẽ được nộp</p>",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Vâng, chắc chắn!",
      cancelButtonText: "Huỷ",
    }).then((result) => {
      if (result.isConfirmed) {
        location.href = "./dashboard";
      }
    });
  });
  // Biến thời gian kết thúc bài luyện
  let endTime = -1;
  startCountdownFromGlobalTime();
  // Gọi hàm bắt đầu đếm thời gian dựa trên biến `thoigian`
  function startCountdownFromGlobalTime() {
    const storedEndTime = localStorage.getItem(endTimeKey);
    console.log("thoigian", thoigian);
    if (storedEndTime) {
      endTime = parseInt(storedEndTime);
    } else {
      const now = new Date().getTime();
      endTime = now + thoigian * 60 * 1000; // đổi phút sang ms
      localStorage.setItem(endTimeKey, endTime);
    }

    countDown(); // bắt đầu đếm ngược
  }
  

  function countDown() {
    const x = setInterval(function () {
      const now = new Date().getTime();
      const distance = endTime - now;
      let hours = Math.floor(
        (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
      );
      if (hours < 10) hours = "0" + hours;

      let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      if (minutes < 10) minutes = "0" + minutes;

      let seconds = Math.floor((distance % (1000 * 60)) / 1000);
      if (seconds < 10) seconds = "0" + seconds;

      $("#timer").html(`${hours}:${minutes}:${seconds}`);

      // Hết giờ thì nộp bài
      if (distance <= 1000 && distance >= 0) {
        clearInterval(x);
        nopbai(); // tự động nộp bài
      }
    }, 1000);
  }

  // Xử lý khi người dùng reload hoặc đóng tab
  $(window).on("beforeunload", function () {
    const now = new Date().getTime();
    if (now > endTime) {
      localStorage.removeItem(endTimeKey);
      localStorage.removeItem(cautraloi);
      localStorage.removeItem(dethi);
    }else{
        countDown();
    }
  });
  
});
