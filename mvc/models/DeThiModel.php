<?php
include "./mvc/models/CauTraLoiModel.php";
include "./mvc/models/NguoiDungModel.php";
class DeThiModel extends DB
{
    public function create(
        $monthi,
        $nguoitao,
        $tende,
        $thoigianthi,
        $hienthibailam,
        $xemdiemthi,
        $xemdapan,
        $troncauhoi,
        $trondapan,
        $nopbaichuyentab,
        $loaide,
        $socaude,
        $socautb,
        $socaukho,
        $chuong,
        $trangthai,
        $dethimau
    ) {
        $sql = "INSERT INTO `dethi`(`monthi`, `nguoitao`, `tende`, `thoigianthi`, 
         `hienthibailam`, `xemdiemthi`, `xemdapan`, 
        `troncauhoi`, `trondapan`, `nopbaichuyentab`, `loaide`, `socaude`, `socautb`,
         `socaukho`, `trangthai`) VALUES ('$monthi','$nguoitao','$tende','$thoigianthi',
         '$hienthibailam','$xemdiemthi','$xemdapan','$troncauhoi','$trondapan','$nopbaichuyentab',
         '$loaide','$socaude','$socautb','$socaukho', '$trangthai')";
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            $madethi = mysqli_insert_id($this->con);
            // Một đề thi giao cho nhiều nhóm
            // $result = $this->create_giaodethi($madethi);
            // Một đề thi thì có nhiều chương
               if($loaide ==1){
                $result = $this->create_chuongdethi($madethi, $chuong);}
            if ($loaide == 2) {
                $result = $this->create_detthimau($madethi, $dethimau);}
            return $madethi;
        } else return false;
    }

    // 
    public function create_dethi_auto($made, $monhoc, $chuong, $socaude, $socautb, $socaukho)
    {
        $valid = true;
        $sql_caude = "SELECT * FROM cauhoi ch join monhoc mh on ch.mamonhoc = mh.mamonhoc where (ch.trangthai=1 or ch.trangthai=2) and ch.mamonhoc = $monhoc and ch.dokho = 1 and ";
        $sql_cautb = "SELECT * FROM cauhoi ch join monhoc mh on ch.mamonhoc = mh.mamonhoc where (ch.trangthai=1 or ch.trangthai=2) and ch.mamonhoc = $monhoc and ch.dokho = 2 and ";
        $sql_caukho = "SELECT * FROM cauhoi ch join monhoc mh on ch.mamonhoc = mh.mamonhoc where (ch.trangthai=1 or ch.trangthai=2) and ch.mamonhoc = $monhoc and ch.dokho = 3 and ";
        $countChuong = count($chuong) - 1;
        $detailChuong = "(";
        $i = 0;
        while ($i < $countChuong) {
            $detailChuong .= "ch.machuong='$chuong[$i]' or ";
            $i++;
        }
        $detailChuong .= "ch.machuong=$chuong[$countChuong])";

        $sql_caude = $sql_caude . $detailChuong . " order by rand() limit $socaude";
        $sql_cautb = $sql_cautb . $detailChuong . " order by rand() limit $socautb";
        $sql_caukho = $sql_caukho . $detailChuong . " order by rand() limit $socaukho";

        $result_cd = mysqli_query($this->con, $sql_caude);
        $result_tb = mysqli_query($this->con, $sql_cautb);
        $result_ck = mysqli_query($this->con, $sql_caukho);

        $data_cd = array();

        while ($row = mysqli_fetch_assoc($result_cd)) {
            $data_cd[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_tb)) {
            $data_cd[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_ck)) {
            $data_cd[] = $row;
        }
        shuffle($data_cd);
        return $data_cd;
    }
    public function create_dethi_from_template($made, $listMade)
    {
        if (count($listMade) == 0) return [];

        // Lấy thông tin cấu trúc đề từ đề mẫu đầu tiên
        $madeMau = $listMade[0];
        $sql_cauhinh = "SELECT socaude, socautb, socaukho FROM dethi WHERE made = '$madeMau'";
        $result_cauhinh = mysqli_query($this->con, $sql_cauhinh);
        $cauhinh = mysqli_fetch_assoc($result_cauhinh);

        $socaude = (int)$cauhinh['socaude'];
        $socautb = (int)$cauhinh['socautb'];
        $socaukho = (int)$cauhinh['socaukho'];

        // Tạo chuỗi danh sách mã đề mẫu: (made = 101 OR made = 102 ...)
        $countMade = count($listMade) - 1;
        $detailMade = "(";
        for ($i = 0; $i < $countMade; $i++) {
            $detailMade .= "chitietdethi.made = '$listMade[$i]' OR ";
        }
        $detailMade .= "chitietdethi.made = '$listMade[$countMade]')";

        // Câu dễ
        $sql_caude = "SELECT cauhoi.* FROM cauhoi 
            JOIN chitietdethi ON cauhoi.macauhoi = chitietdethi.macauhoi 
            WHERE cauhoi.dokho = 1 AND $detailMade 
            ORDER BY RAND() LIMIT $socaude";

        // Câu TB
        $sql_cautb = "SELECT cauhoi.* FROM cauhoi 
            JOIN chitietdethi ON cauhoi.macauhoi = chitietdethi.macauhoi 
            WHERE cauhoi.dokho = 2 AND $detailMade 
            ORDER BY RAND() LIMIT $socautb";

        // Câu khó
        $sql_caukho = "SELECT cauhoi.* FROM cauhoi 
            JOIN chitietdethi ON cauhoi.macauhoi = chitietdethi.macauhoi 
            WHERE cauhoi.dokho = 3 AND $detailMade 
            ORDER BY RAND() LIMIT $socaukho";

        $data_all = [];

        $result_cd = mysqli_query($this->con, $sql_caude);
        $result_tb = mysqli_query($this->con, $sql_cautb);
        $result_ck = mysqli_query($this->con, $sql_caukho);

        while ($row = mysqli_fetch_assoc($result_cd)) {
            $data_all[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_tb)) {
            $data_all[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_ck)) {
            $data_all[] = $row;
        }

        shuffle($data_all);
        return $data_all;
    }


    public function create_chuongdethi($made, $chuong)
    {
        $valid = true;
        foreach ($chuong as $machuong) {
            $sql = "INSERT INTO `dethitudong`(`made`, `machuong`) VALUES ('$made','$machuong')";
            $result = mysqli_query($this->con, $sql);
            if (!$result) $valid = false;
        }
        return $valid;
    }
    public function create_detthimau($made, $dethimau)
    {
        $valid = true;
        foreach ($dethimau as $mau) {
            $sql = "INSERT INTO `dethimau`(`made`, `mademau`) VALUES ('$made','$mau')";
            $result = mysqli_query($this->con, $sql);
            if (!$result) $valid = false;
        }
        return $valid;
    }
    public function update_chuongdethi($made, $chuong)
    {
        $valid = true;
        $sql = "DELETE FROM `dethitudong` WHERE `made`='$made'";
        $result_del = mysqli_query($this->con, $sql);
        if ($result_del) $result_update = $this->create_chuongdethi($made, $chuong);
        else $valid = false;
        return $valid;
    }

    public function create_giaodethi($made, $nhom, $thoigianbatdau, $thoigianketthuc, $hinhthuc)
    {
        $valid = true;

        foreach ($nhom as $ma) {
            if ($hinhthuc == 'hocphan') {
                $sql = "INSERT INTO `giaodethilop`(`madethi`, `mahocphan`, `thoigianbatdau`, `thoigianketthuc`) 
                        VALUES ('$made', '$ma', '$thoigianbatdau', '$thoigianketthuc')";
            } else {
                $sql = "INSERT INTO `giaodethi`(`made`, `manhom`, `thoigianbatdau`, `thoigianketthuc`) 
                        VALUES ('$made', '$ma', '$thoigianbatdau', '$thoigianketthuc')";
            }

            $result = mysqli_query($this->con, $sql);
            if (!$result) {
                $valid = false;
            }
        }

        return $valid;
    }
    public function getDetailGiao($loaigiao, $manguongiao, $made)
    {
        if ($loaigiao == 1) {
            $sql = "SELECT giaodethilop.* FROM giaodethilop
                    WHERE mahocphan = '$manguongiao' AND madethi = '$made'";
        } else {
            $sql = "SELECT giaodethi.* FROM giaodethi
                    WHERE manhom = '$manguongiao' AND made = '$made'";
        }
        $result = mysqli_query($this->con, $sql);

        return mysqli_fetch_assoc($result); 
    }


    public function update_giaodethi($made, $nhom)
    {
        // $valid = true;
        // $sql = "DELETE FROM `giaodethi` WHERE `made`='$made'";
        // $result_del = mysqli_query($this->con, $sql);
        // if ($result_del) $result_update = $this->create_giaodethi($made, $nhom);
        // else $valid = false;
        // return $valid;
    }

    public function update($made, $monthi, $tende, $thoigianthi, $thoigianbatdau, $thoigianketthuc, $hienthibailam, $xemdiemthi, $xemdapan, $troncauhoi, $trondapan, $nopbaichuyentab, $loaide, $socaude, $socautb, $socaukho, $chuong, $nhom)
    {
        $valid = true;
        $sql = "UPDATE `dethi` SET `monthi`='$monthi',`tende`='$tende',`thoigianthi`='$thoigianthi',`thoigianbatdau`='$thoigianbatdau',`thoigianketthuc`='$thoigianketthuc',`hienthibailam`='$hienthibailam',`xemdiemthi`='$xemdiemthi',`xemdapan`='$xemdapan',`troncauhoi`='$troncauhoi',`trondapan`='$trondapan',`nopbaichuyentab`='$nopbaichuyentab',`loaide`='$loaide',`socaude`='$socaude',`socautb`='$socautb',`socaukho`='$socaukho' WHERE `made`='$made'";
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            // Một đề thi giao cho nhiều nhóm
            $result = $this->update_giaodethi($made, $nhom);
            // Một đề thi thì có nhiều chương
            $result = $this->update_chuongdethi($made, $chuong);
        } else $valid = false;
        return $valid;
    }

    public function delete($madethi)
    {
        $valid = true;
        $sql = "UPDATE `dethi` SET `trangthai`= 0 WHERE `made` = $madethi";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    // Lấy đề thi mà người dùng tạo
    public function getAll($nguoitao)
    {
        $sql = "SELECT dethi.made, tende, monhoc.tenmonhoc, thoigianbatdau, thoigianketthuc, nhom.tennhom, namhoc, hocky
        FROM dethi, monhoc, giaodethi, nhom
        WHERE dethi.monthi = monhoc.mamonhoc AND dethi.made = giaodethi.made AND nhom.manhom = giaodethi.manhom AND nguoitao = $nguoitao AND dethi.trangthai = 1
        ORDER BY dethi.made DESC";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $made = $row['made'];
            $index = array_search($made, array_column($rows, 'made'));
            if ($index === false) {
                $item = [
                    "made" => $made,
                    "tende" => $row['tende'],
                    "thoigianbatdau" => date_format(date_create($row['thoigianbatdau']), "H:i d/m/Y"),
                    "thoigianketthuc" => date_format(date_create($row['thoigianketthuc']), "H:i d/m/Y"),
                    "tenmonhoc" => $row['tenmonhoc'],
                    "namhoc" => $row['namhoc'],
                    "hocky" => $row['hocky'],
                    "nhom" => [$row['tennhom']]
                ];
                array_push($rows, $item);
            } else {
                array_push($rows[$index]["nhom"], $row['tennhom']);
            }
        }
        return $rows;
    }
    public function getAllTestByUserAndSubject($user_id, $mamonhoc){
        $nguoiDungModel = new NguoiDungModel();
        $check = $nguoiDungModel->checkAdmin($user_id);
        $user = $check ? " " : " AND nguoitao = '" . $user_id . "' ";
        $sql_dethi = "SELECT dethi.* 
        FROM dethi 
        WHERE dethi.monthi = '$mamonhoc' 
        $user
        AND dethi.loaide = 0;";
        $result_dethi = mysqli_query($this->con, $sql_dethi);

        $dethis = [];
        while ($row = mysqli_fetch_assoc($result_dethi)) {
            $dethis[] = $row;
        }
        return $dethis;
    }
    // Lấy chi tiết đề thi
    public function getById($made)
    {
        $sql_dethi = "SELECT dethi.*, monhoc.tenmonhoc FROM dethi, monhoc WHERE made = $made AND dethi.monthi = monhoc.mamonhoc";
        $result_dethi = mysqli_query($this->con, $sql_dethi);
        $dethi = mysqli_fetch_assoc($result_dethi);
        if ($dethi != null) {
            $sql_giaodethi = "SELECT manhom FROM giaodethi WHERE made = $made";
            $sql_dethitudong = "SELECT machuong FROM dethitudong WHERE made = $made";
            $result_giaodethi = mysqli_query($this->con, $sql_giaodethi);
            $result_dethitudong = mysqli_query($this->con, $sql_dethitudong);
            $dethi['chuong'] = array();
            while ($row = mysqli_fetch_assoc($result_dethitudong)) {
                $dethi['chuong'][] = $row['machuong'];
            }
            $dethi['nhom'] = array();
            while ($row = mysqli_fetch_assoc($result_giaodethi)) {
                $dethi['nhom'][] = $row['manhom'];
            }
        }
        return $dethi;
    }
    public function getByIdScheduleReview($made,  $mahocphan)
    {
        $sql_dethi = "SELECT dethi.*, monhoc.tenmonhoc, thoigianbatdau, thoigianketthuc
         FROM dethi, monhoc, giaodethilop, hocphan
         WHERE made = $made AND dethi.monthi = monhoc.mamonhoc and giaodethilop.madethi = dethi.made
            And giaodethilop.mahocphan = hocphan.mahocphan 
            And hocphan.mahocphan = '$mahocphan'" ;
        $result_dethi = mysqli_query($this->con, $sql_dethi);
        $dethi = mysqli_fetch_assoc($result_dethi);
        if ($dethi != null) {  
            $sql_dethitudong = "SELECT machuong FROM dethitudong WHERE made = $made";
            $result_dethitudong = mysqli_query($this->con, $sql_dethitudong);
            $dethi['chuong'] = array();
            while ($row = mysqli_fetch_assoc($result_dethitudong)) {
                $dethi['chuong'][] = $row['machuong'];
            }
        }
        return $dethi;
    }
    public function getByIdScheduleGroup($made, $manhom)
    {
        $sql_dethi = "SELECT dethi.*, monhoc.tenmonhoc, giaodethi.thoigianbatdau, 
        giaodethi.thoigianketthuc
        FROM dethi
        JOIN monhoc ON dethi.monthi = monhoc.mamonhoc
        JOIN giaodethi ON giaodethi.made = dethi.made
        WHERE dethi.made = $made AND giaodethi.manhom = '$manhom'";

        $result_dethi = mysqli_query($this->con, $sql_dethi);
        $dethi = mysqli_fetch_assoc($result_dethi);

        if ($dethi != null) {
            // Lấy các chương có trong đề tự động (nếu có)
            $sql_dethitudong = "SELECT machuong FROM dethitudong WHERE made = $made";
            $result_dethitudong = mysqli_query($this->con, $sql_dethitudong);
            $dethi['chuong'] = array();
            while ($row = mysqli_fetch_assoc($result_dethitudong)) {
                $dethi['chuong'][] = $row['machuong'];
            }

            // // Lấy tất cả các nhóm đã được giao đề này
            // $sql_giaodethi = "SELECT manhom FROM giaodethi WHERE made = $made";
            // $result_giaodethi = mysqli_query($this->con, $sql_giaodethi);
            // $dethi['nhom'] = array();
            // while ($row = mysqli_fetch_assoc($result_giaodethi)) {
            //     $dethi['nhom'][] = $row['manhom'];
            // }
        }
        
        return $dethi;
    }

    // Lấy thông tin cơ bản của đề thi ()
    public function getInfoTestBasic($made, $loaigiao, $manguongiao)
    {
        // Lấy thông tin đề thi + môn học
        $sql_dethi = "
            SELECT dethi.made, dethi.tende, dethi.thoigiantao, dethi.loaide, dethi.nguoitao,
                   monhoc.mamonhoc, monhoc.tenmonhoc
            FROM dethi
            JOIN monhoc ON dethi.monthi = monhoc.mamonhoc
            WHERE dethi.made = $made
        ";
        $result_dethi = mysqli_query($this->con, $sql_dethi);
        $dethi = mysqli_fetch_assoc($result_dethi);

        if ($dethi != null) {
            // Chuẩn hóa chuỗi manguongiao thành mảng số
            $arrNguonGiao = array_map('intval', explode(',', $manguongiao));
            $inNguonGiao = implode(',', $arrNguonGiao);

            // Tạo lại trường "nhom" để chứa kết quả
            $dethi['nhom'] = array();

            if ($loaigiao == 0) {
                // Giao đề cho nhóm
                $sql_giaodethi = "
                    SELECT giaodethi.manhom, nhom.tennhom
                    FROM giaodethi
                    JOIN nhom ON giaodethi.manhom = nhom.manhom
                    WHERE giaodethi.made = $made AND giaodethi.manhom IN ($inNguonGiao)
                ";
                $result = mysqli_query($this->con, $sql_giaodethi);
                while ($row = mysqli_fetch_assoc($result)) {
                    $dethi['nhom'][] = $row;
                }
            } else {
                // Giao đề cho học phần (sử dụng tên lớp thay vì tên học phần)
                $sql_giaodethi = "
                    SELECT giaodethilop.mahocphan AS manhom, lop.tenLop AS tennhom
                    FROM giaodethilop
                    JOIN hocphan ON giaodethilop.mahocphan = hocphan.mahocphan
                    JOIN lop ON hocphan.malop = lop.malop
                    WHERE giaodethilop.madethi = $made AND giaodethilop.mahocphan IN ($inNguonGiao)
                ";
                $result = mysqli_query($this->con, $sql_giaodethi);
                while ($row = mysqli_fetch_assoc($result)) {
                    $dethi['nhom'][] = $row;
                }
            }
        }

        return $dethi;
    }


    // Lấy đề thi của nhóm học phần
    public function getListTestGroup($manhom)
    {
        $sql = "SELECT dethi.made, dethi.tende, dethi.trangthai, giaodethi.thoigianbatdau, giaodethi.thoigianketthuc
        FROM giaodethi, dethi
        WHERE manhom = '$manhom' AND giaodethi.made = dethi.made ORDER BY dethi.made DESC";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $row['thoigianbatdau'] = date_format(date_create($row['thoigianbatdau']), "H:i d/m/Y");
            $row['thoigianketthuc'] = date_format(date_create($row['thoigianketthuc']), "H:i d/m/Y");
            $rows[] = $row;
        }
        return $rows;
    }
    public function getListTestModule($manhom)
    {
        $sql = "SELECT dethi.made, dethi.tende, dethi.trangthai, giaodethilop.thoigianbatdau, giaodethilop.thoigianketthuc
        FROM giaodethilop, dethi
        WHERE mahocphan = '$manhom' AND giaodethilop.madethi = dethi.made ORDER BY dethi.made DESC";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $row['thoigianbatdau'] = date_format(date_create($row['thoigianbatdau']), "H:i d/m/Y");
            $row['thoigianketthuc'] = date_format(date_create($row['thoigianketthuc']), "H:i d/m/Y");
            $rows[] = $row;
        }
        return $rows;
    }
    public function getTestByUserAndType($trangthai, $mamonhoc, $userid)
    {
        $nguoiDungModel = new NguoiDungModel();
        $check = $nguoiDungModel->checkAdmin($userid);
        $user = $check ? " " : " AND nguoitao = '" . $userid . "' ";
        $mon = "";
        if ($mamonhoc !== null) {
            $mon .= " AND monthi = '$mamonhoc' ";
        }
        $sql = "SELECT * 
        FROM dethi
        WHERE  dethi.trangthai = '$trangthai' $user $mon";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    // Lấy câu hỏi của đề thi
    public function getQuestionOfTest($made, $user_id, $loaigiao, $manguongiao)
    {
        $sql_dethi = "select * from dethi where made = '$made'";
        $data_dethi = mysqli_fetch_assoc(mysqli_query($this->con, $sql_dethi));
        $question = array();
        if ($data_dethi['loaide'] == 0) {
            $question = $this->getQuestionOfTestManual($made);
        } else if($data_dethi['loaide'] == 1){
            $question = $this->getQuestionTestAuto($made);
        }else if($data_dethi['loaide'] == 2){
            $question = $this->getQuestionFromBaseTest($made);
        }
        $makq = $this->getMaDe($made, $user_id, $loaigiao, $manguongiao);
        foreach ($question as $data) {
            $macauhoi = $data['macauhoi'];
            $sql = "INSERT INTO `chitietketqua`(`makq`, `macauhoi`) VALUES ('$makq','$macauhoi')";
            $addCtKq = mysqli_query($this->con, $sql);
        }

        return $question;
    }



    public function getMaDe($made, $user, $loaigiao, $manguongiao)
    {
        $sql = "SELECT * FROM `ketqua` WHERE made = '$made' and manguoidung = '$user' and loaigiao ='$loaigiao' and manguongiao='$manguongiao' ";
        $result = mysqli_query($this->con, $sql);
        $data = mysqli_fetch_assoc($result);
        return $data['makq'];
    }


    public function getQuestionTestAuto($made)
    {
        $sql_dethi = "select * from dethi where made = '$made'";
        $data_dethi = mysqli_fetch_assoc(mysqli_query($this->con, $sql_dethi));
        $socaude = $data_dethi['socaude'];
        $socautb = $data_dethi['socautb'];
        $socaukho = $data_dethi['socaukho'];
        $sql_cd = "select ch.macauhoi,ch.noidung,ch.dokho from dethitudong dttd join cauhoi ch on dttd.machuong=ch.machuong where ch.dokho = 1 and dttd.made = '$made' order by rand() limit $socaude";
        $sql_ctb = "select ch.macauhoi,ch.noidung,ch.dokho from dethitudong dttd join cauhoi ch on dttd.machuong=ch.machuong where ch.dokho = 2 and dttd.made = '$made' order by rand() limit $socautb";
        $sql_ck = "select ch.macauhoi,ch.noidung,ch.dokho from dethitudong dttd join cauhoi ch on dttd.machuong=ch.machuong where ch.dokho = 3 and dttd.made = '$made' order by rand() limit $socaukho";
        $result_cd = mysqli_query($this->con, $sql_cd);
        $result_tb = mysqli_query($this->con, $sql_ctb);
        $result_ck = mysqli_query($this->con, $sql_ck);
        $result = array();
        while ($row = mysqli_fetch_assoc($result_cd)) {
            $result[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_tb)) {
            $result[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_ck)) {
            $result[] = $row;
        }
        shuffle($result);
        $rows = array();

        $ctlmodel = new CauTraLoiModel();

        foreach ($result as $row) {
            $row['cautraloi'] = $ctlmodel->getAllWithoutAnswer($row['macauhoi']);
            $rows[] = $row;
        }
        return $rows;
    }
    public function getQuestionFromBaseTest($made)
    {
        // B1: Lấy thông tin cấu trúc đề từ mã đề đầu vào
        $sql_dethi = "SELECT socaude, socautb, socaukho FROM dethi WHERE made = '$made'";
        $data_dethi = mysqli_fetch_assoc(mysqli_query($this->con, $sql_dethi));

        $socaude = (int)$data_dethi['socaude'];
        $socautb = (int)$data_dethi['socautb'];
        $socaukho = (int)$data_dethi['socaukho'];

        // B2: Lấy danh sách đề mẫu từ bảng trung gian dethimau
        $sql_mau = "SELECT mademau FROM dethimau WHERE made = '$made'";
        $result_mau = mysqli_query($this->con, $sql_mau);

        $listMade = [];
        while ($row = mysqli_fetch_assoc($result_mau)) {
            $listMade[] = $row['mademau'];
        }

        if (count($listMade) == 0) return []; // Không có đề mẫu => return rỗng

        $in_made = implode("','", $listMade);

        // B3: Truy vấn từng nhóm độ khó từ các đề mẫu
        $sql_cd = "SELECT ch.macauhoi, ch.noidung, ch.dokho
               FROM chitietdethi ctd
               JOIN cauhoi ch ON ch.macauhoi = ctd.macauhoi
               WHERE ch.dokho = 1 AND ctd.made IN ('$in_made')
               ORDER BY RAND() LIMIT $socaude";

        $sql_ctb = "SELECT ch.macauhoi, ch.noidung, ch.dokho
                FROM chitietdethi ctd
                JOIN cauhoi ch ON ch.macauhoi = ctd.macauhoi
                WHERE ch.dokho = 2 AND ctd.made IN ('$in_made')
                ORDER BY RAND() LIMIT $socautb";

        $sql_ck = "SELECT ch.macauhoi, ch.noidung, ch.dokho
               FROM chitietdethi ctd
               JOIN cauhoi ch ON ch.macauhoi = ctd.macauhoi
               WHERE ch.dokho = 3 AND ctd.made IN ('$in_made')
               ORDER BY RAND() LIMIT $socaukho";
        $result_cd = mysqli_query($this->con, $sql_cd);
        $result_tb = mysqli_query($this->con, $sql_ctb);
        $result_ck = mysqli_query($this->con, $sql_ck);
        // B4: Ghép kết quả lại
        $result = array();
        while ($row = mysqli_fetch_assoc($result_cd)) {
            $result[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_tb)) {
            $result[] = $row;
        }
        while ($row = mysqli_fetch_assoc($result_ck)) {
            $result[] = $row;
        }
        shuffle($result);
        $rows = array();

        // B5: Lấy câu trả lời cho từng câu
        $ctlmodel = new CauTraLoiModel();
        $rows = [];

        foreach ($result as $row) {
            $row['cautraloi'] = $ctlmodel->getAllWithoutAnswer($row['macauhoi']);
            $rows[] = $row;
        }

        return $rows;
    }

    public function getQuestionByUser($made, $user)
    {

        $sql_ketqua = "SELECT * FROM ketqua where made = '$made' and manguoidung = '$user'";
        $result_ketqua = mysqli_query($this->con, $sql_ketqua);
        $data_ketqua = mysqli_fetch_assoc($result_ketqua);
        $ketqua = $data_ketqua['makq'];
        $sql_question = "SELECT * FROM chitietketqua ctkq JOIN cauhoi ch on ctkq.macauhoi = ch.macauhoi WHERE makq = '$ketqua'";
        $data_question = mysqli_query($this->con, $sql_question);
        $ctlmodel = new CauTraLoiModel();
        $sql_dethi = "SELECT * FROM dethi where made='$made'";
        $result_dethi = mysqli_query($this->con, $sql_dethi);
        $data_dethi = mysqli_fetch_assoc($result_dethi);
        $trondapan = $data_dethi['trondapan'];
        $rows = array();
        foreach ($data_question as $row) {
            if ($trondapan == 1) {
                $arrDapAn = $ctlmodel->getAllWithoutAnswer($row['macauhoi']);
                shuffle($arrDapAn);
                $row['cautraloi'] = $arrDapAn;
            } else {
                $row['cautraloi'] = $ctlmodel->getAllWithoutAnswer($row['macauhoi']);
            }
            $rows[] = $row;
        }
        $troncauhoi = $data_dethi['troncauhoi'];
        if ($troncauhoi == 1) {
            shuffle($rows);
        }

        return $rows;
    }
    public function checkAutoTest($made)
    {
        $sql = "SELECT 1 FROM dethitudong WHERE made = '$made' LIMIT 1";
        $result = mysqli_query($this->con, $sql);
        return mysqli_num_rows($result) > 0;
    }

    public function getNameGroup($manhom)
    {
        $sql = "SELECT * FROM `nhom` WHERE manhom=$manhom";
        $result = mysqli_query($this->con, $sql);
        $nameGroup = mysqli_fetch_assoc($result)['tennhom'];
        return $nameGroup;
    }

    // Tạo đề thủ công
    public function getQuestionOfTestManual($made)
    {
        $sql = "SELECT CTDT.macauhoi, noidung, dokho, thutu FROM chitietdethi CTDT, cauhoi CH WHERE CTDT.macauhoi = CH.macauhoi AND CTDT.made = $made ORDER BY thutu ASC";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        $ctlmodel = new CauTraLoiModel();
        while ($row = mysqli_fetch_assoc($result)) {
            $row['cautraloi'] = $ctlmodel->getAllWithoutAnswer($row['macauhoi']);
            $rows[] = $row;
        }

        return $rows;
    }
 
    // Lấy chi tiết đề thi của sinh viên
    public function getResultDetail($makq)
    {
        $sql = "SELECT cauhoi.macauhoi,cauhoi.noidung,cauhoi.dokho,chitietketqua.dapanchon FROM chitietketqua, cauhoi WHERE makq= '$makq' AND chitietketqua.macauhoi = cauhoi.macauhoi";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        $ctlmodel = new CauTraLoiModel();
        while ($row = mysqli_fetch_assoc($result)) {
            $row['cautraloi'] = $ctlmodel->getAll($row['macauhoi']);
            $rows[] = $row;
        }
        return $rows;
    }

    // Lấy thời gian kết thúc đề thi
    public function getTimeTest($dethi, $nguoidung, $loaigiao, $manguongiao)
    {
        $sql = "Select * from ketqua where made = '$dethi' and manguoidung = '$nguoidung'
        and loaigiao = '$loaigiao' and manguongiao = '$manguongiao'";
        if($loaigiao ==1 ){
            $sql_giao = "select * from giaodethilop where madethi = '$dethi' and mahocphan = '$manguongiao'";
        }else{
            $sql_giao = "select * from giaodethi where made = '$dethi' and manhom = '$manguongiao'";
        }
        $sql_dethi = "select * from dethi where made = '$dethi' ";
        $result_giao = mysqli_query($this->con, $sql_giao);
        $result_dethi = mysqli_query($this->con, $sql_dethi);
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            $data = mysqli_fetch_assoc($result);
            $data_giao = mysqli_fetch_assoc($result_giao);
            $data_dethi = mysqli_fetch_assoc($result_dethi);
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            if($data_dethi["trangthai"] == -1){
                $thoigianketthuc = date("Y-m-d H:i:s", strtotime($data['thoigianvaothi']) + ($data_dethi['thoigianthi'] * 60));
            }else{
                $thoigianketthuc = date("Y-m-d H:i:s", strtotime($data_giao['thoigianbatdau']) + ($data_dethi['thoigianthi'] * 60));
            }
            
            return $thoigianketthuc;
        }
        return false;
    }

    public function getTimeEndTest($dethi)
    {
        $sql_dethi = "select * from dethi where made = '$dethi'";
        $result_dethi = mysqli_query($this->con, $sql_dethi);
        $data_dethi = mysqli_fetch_assoc($result_dethi);
        $thoigianketthuc = date("Y-m-d H:i:s", strtotime($data_dethi['thoigianketthuc']));
        return $thoigianketthuc;
    }

    public function getGroupsTakeTests($tests)
    {
        $string = implode(', ', $tests);
        $sql = "SELECT GDT.*, tennhom, namhoc, hocky FROM giaodethi GDT, nhom N WHERE GDT.manhom = N.manhom AND made IN ($string)";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function checkStudentAllowed($manguoidung, $madethi)
    {
        $valid = false;

        // Check đề thi giao qua nhóm
        $sql_nhom = "
            SELECT 1
            FROM giaodethi GD
            JOIN chitietnhom CTN ON GD.manhom = CTN.manhom
            WHERE GD.made = '$madethi' AND CTN.manguoidung = '$manguoidung'
            LIMIT 1
        ";
        $result_nhom = mysqli_query($this->con, $sql_nhom);
        if (mysqli_fetch_assoc($result_nhom)) {
            $valid = true;
        }

        // Nếu chưa hợp lệ, check tiếp đề thi giao qua học phần
        if (!$valid) {
            $sql_hp = "
                SELECT 1
                FROM sinhvien SV
                JOIN lop L ON SV.malop = L.malop
                JOIN hocphan HP ON HP.malop = L.malop
                JOIN giaodethilop GDTL ON GDTL.mahocphan = HP.mahocphan
                WHERE SV.id = '$manguoidung' AND GDTL.madethi = '$madethi'
                LIMIT 1
            ";
            $result_hp = mysqli_query($this->con, $sql_hp);
            if (mysqli_fetch_assoc($result_hp)) {
                $valid = true;
            }
        }

        return $valid;
    }


    public function getQuery($filter, $input, $args)
    {
        $query = "";
        if (isset($args["custom"]["function"])) {
            $func = $args["custom"]["function"];
            switch ($func) {
                case "getUserTestSchedule":
                    // Lấy danh sách lịch thi đã được giao của người dùng
                    $query = "
                    SELECT * FROM (
                        -- Giao qua nhóm
                        SELECT 
                            DT.made, DT.tende, GDT.thoigianbatdau, GDT.thoigianketthuc,
                            DT.thoigianthi, (DT.socaude + DT.socautb + DT.socaukho) AS total, 
                            N.manhom AS magiao, N.tennhom AS tengiao, MH.tenmonhoc,
                            'nhom' AS loaigiao,
                            T2.diemthi
                        FROM chitietnhom CTN
                        JOIN nhom N ON N.manhom = CTN.manhom
                        JOIN giaodethi GDT ON GDT.manhom = CTN.manhom
                        JOIN dethi DT ON DT.made = GDT.made
                        JOIN monhoc MH ON MH.mamonhoc = DT.monthi
                        LEFT JOIN (
                            SELECT K1.made, K1.diemthi, K1.manguongiao
                            FROM ketqua K1
                            JOIN (
                                SELECT made, manguongiao, MAX(thoigianvaothi) AS max_time
                                FROM ketqua
                                WHERE manguoidung = '" . $args['manguoidung'] . "'
                                GROUP BY made, manguongiao
                            ) K2 ON K1.made = K2.made AND K1.manguongiao = K2.manguongiao AND K1.thoigianvaothi = K2.max_time
                            WHERE K1.manguoidung = '" . $args['manguoidung'] . "'
                        ) T2 ON DT.made = T2.made AND GDT.manhom = T2.manguongiao
                        WHERE N.trangthai != 0
                        AND DT.trangthai = 1
                        AND CTN.manguoidung = '" . $args['manguoidung'] . "'
                    
                        UNION ALL
                    
                        -- Giao qua học phần
                        SELECT 
                            DT.made, DT.tende, GDTL.thoigianbatdau, GDTL.thoigianketthuc,
                            DT.thoigianthi, (DT.socaude + DT.socautb + DT.socaukho) as total, 
                            HP.mahocphan AS magiao, L.tenlop AS tengiao, MH.tenmonhoc,
                            'hocphan' AS loaigiao,
                            T2.diemthi
                        FROM sinhvien SV
                        JOIN lop L ON SV.malop = L.malop
                        JOIN hocphan HP ON HP.malop = L.malop
                        JOIN giaodethilop GDTL ON GDTL.mahocphan = HP.mahocphan
                        JOIN dethi DT ON DT.made = GDTL.madethi
                        JOIN monhoc MH ON MH.mamonhoc = DT.monthi
                        LEFT JOIN (
                            SELECT K1.made, K1.diemthi, K1.manguongiao
                            FROM ketqua K1
                            JOIN (
                                SELECT made, manguongiao, MAX(thoigianvaothi) AS max_time
                                FROM ketqua
                                WHERE manguoidung = '" . $args['manguoidung'] . "'
                                GROUP BY made, manguongiao
                            ) K2 ON K1.made = K2.made AND K1.manguongiao = K2.manguongiao AND K1.thoigianvaothi = K2.max_time
                            WHERE K1.manguoidung = '" . $args['manguoidung'] . "'
                        ) T2 ON DT.made = T2.made AND HP.mahocphan = T2.manguongiao
                        WHERE SV.id = '" . $args['manguoidung'] . "'
                        AND DT.trangthai = 1
                    ) AS AllDeThi
                    WHERE 1=1
                    ";

                    if (isset($filter)) {
                        switch ($filter) {
                            case "0";
                                $query .= " AND CURRENT_TIMESTAMP() BETWEEN thoigianbatdau AND thoigianketthuc AND diemthi IS NULL";
                                break;
                            case "1";
                                $query .= " AND CURRENT_TIMESTAMP() > thoigianketthuc AND diemthi IS NULL";
                                break;
                            case "2";
                                $query .= " AND CURRENT_TIMESTAMP() < thoigianbatdau";
                                break;
                            case "3";
                                $query .= " AND diemthi IS NOT NULL";
                                break;
                            default:
                        }
                    }
                    if ($input) {
                        $query .= " AND (tende LIKE N'%$input%' OR tenmonhoc LIKE N'%$input%') AND DT.trangthai = 1";
                    }
                    $query .= " ORDER BY thoigianbatdau DESC";
                    break;
                case "getUserReviewSchedule":
                    // Lấy danh sách lịch on luyen đã được giao của người dùng
                    $query = "SELECT * FROM (
    -- Đề thi giao qua nhóm
                        SELECT 
                            DT.made, tende, GDT.thoigianbatdau, GDT.thoigianketthuc,
                            DT.thoigianthi, (DT.socaude + DT.socautb + DT.socaukho) AS total, 
                            N.manhom AS magiao, N.tennhom AS tengiao, MH.tenmonhoc,
                            'nhom' AS loaigiao,
                            T2.diemthi
                        FROM chitietnhom CTN
                        JOIN nhom N ON N.manhom = CTN.manhom
                        JOIN giaodethi GDT ON GDT.manhom = CTN.manhom
                        JOIN dethi DT ON DT.made = GDT.made
                        JOIN monhoc MH ON MH.mamonhoc = DT.monthi
                        LEFT JOIN (
                            SELECT made, diemthi, loaigiao, manguongiao
                            FROM ketqua
                            WHERE manguoidung = '" . $args['manguoidung'] . "'
                        ) T2 ON DT.made = T2.made AND T2.loaigiao = '0' AND T2.manguongiao = N.manhom
                        WHERE N.trangthai != 0
                        AND DT.trangthai = -1
                        AND CTN.manguoidung = '" . $args['manguoidung'] . "'

                        UNION ALL

                        -- Đề thi giao qua học phần
                        SELECT 
                            DT.made, tende, GDTL.thoigianbatdau, GDTL.thoigianketthuc, 
                            DT.thoigianthi, (DT.socaude + DT.socautb + DT.socaukho) AS total,
                            HP.mahocphan AS magiao, L.tenlop AS tengiao, MH.tenmonhoc,
                            'hocphan' AS loaigiao,
                            T2.diemthi
                        FROM sinhvien SV
                        JOIN lop L ON SV.malop = L.malop
                        JOIN hocphan HP ON HP.malop = L.malop
                        JOIN giaodethilop GDTL ON GDTL.mahocphan = HP.mahocphan
                        JOIN dethi DT ON DT.made = GDTL.madethi
                        JOIN monhoc MH ON MH.mamonhoc = DT.monthi
                        LEFT JOIN (
                            SELECT made, diemthi, loaigiao, manguongiao
                            FROM ketqua
                            WHERE manguoidung = '" . $args['manguoidung'] . "'
                        ) T2 ON DT.made = T2.made AND T2.loaigiao = '1' AND T2.manguongiao = HP.mahocphan
                        WHERE SV.id = '" . $args['manguoidung'] . "'
                        AND DT.trangthai = -1
                    ) AS AllDeThi

    
                                    ";
                    if (isset($filter)) {
                        switch ($filter) {
                            case "0";
                                $query .= " AND CURRENT_TIMESTAMP() BETWEEN thoigianbatdau AND thoigianketthuc AND diemthi IS NULL";
                                break;
                            case "1";
                                $query .= " AND CURRENT_TIMESTAMP() > thoigianketthuc AND diemthi IS NULL";
                                break;
                            case "2";
                                $query .= " AND CURRENT_TIMESTAMP() < thoigianbatdau";
                                break;
                            case "3";
                                $query .= " AND diemthi IS NOT NULL";
                                break;
                            default:
                        }
                    }
                    if ($input) {
                        $query .= " AND (tende LIKE N'%$input%' OR tenmonhoc LIKE N'%$input%') AND DT.trangthai = 1";
                    }
                    $query .= " ORDER BY thoigianbatdau DESC";
                    break;
                case "getAllCreatedTest":
                    // Lấy danh sách các đề thi đã giao của giảng viên (nhóm + học phần)
                    $nguoiDungModel = new NguoiDungModel();
                    $check = $nguoiDungModel->checkAdmin($args['id']);
                    $user = $check ? " " : " AND nguoitao = '" . $args['id'] . "' ";
                    $query = "
                            SELECT DT.made, tende,hoten, tenmonhoc, GROUP_CONCAT(DISTINCT N.manhom) as manguongiao
                            , thoigianbatdau, thoigianketthuc,
                            GROUP_CONCAT(N.tennhom SEPARATOR ', ') AS nhom, namhoc, hocky,
                            '0' AS loaigiao
                            FROM dethi DT
                            JOIN monhoc MH ON DT.monthi = MH.mamonhoc
                            JOIN giaodethi GDT ON DT.made = GDT.made
                            JOIN nhom N ON N.manhom = GDT.manhom
                            JOIN nguoidung ND ON ND.id = N.giangvien 
                            WHERE DT.trangthai = 1 $user
                        ";

                    if (isset($filter)) {
                        switch ($filter) {
                            case "0":
                                $query .= " AND CURRENT_TIMESTAMP() < thoigianbatdau";
                                break;
                            case "1":
                                $query .= " AND CURRENT_TIMESTAMP() BETWEEN thoigianbatdau AND thoigianketthuc";
                                break;
                            case "2":
                                $query .= " AND CURRENT_TIMESTAMP() > thoigianketthuc";
                                break;
                        }
                    }

                    if ($input) {
                        $query .= " AND (tende LIKE N'%$input%' OR tenmonhoc LIKE N'%$input%')";
                    }

                    $query .= " GROUP BY DT.made,thoigianbatdau,thoigianketthuc
                    
                            UNION ALL
                    
                            SELECT DT.made, tende,hoten, tenmonhoc, GROUP_CONCAT(DISTINCT HP.mahocphan) as manguongiao
                            , thoigianbatdau, thoigianketthuc,
                                GROUP_CONCAT(L.tenlop SEPARATOR ', ') AS nhom, 
                                nganh.tennganh AS namhoc,         -- hoặc dùng 'Không rõ'
                                tenkhoahoc AS hocky,          -- hoặc dùng 'Không rõ'
                                '1' AS loaigiao
                            FROM dethi DT
                            JOIN monhoc MH ON DT.monthi = MH.mamonhoc
                            JOIN giaodethilop GDTL ON DT.made = GDTL.madethi
                            JOIN hocphan HP ON HP.mahocphan = GDTL.mahocphan
                            JOIN lop L ON L.malop = HP.malop
                            JOIN nganh ON nganh.manganh = L.manganh
                            JOIN khoahoc ON  khoahoc.makhoahoc = L.makhoahoc
                            JOIN nguoidung ND ON ND.id = HP.magiaovien
                            WHERE  DT.trangthai = 1 $user
                        ";

                    if (isset($filter)) {
                        switch ($filter) {
                            case "0":
                                $query .= " AND CURRENT_TIMESTAMP() < thoigianbatdau";
                                break;
                            case "1":
                                $query .= " AND CURRENT_TIMESTAMP() BETWEEN thoigianbatdau AND thoigianketthuc";
                                break;
                            case "2":
                                $query .= " AND CURRENT_TIMESTAMP() > thoigianketthuc";
                                break;
                        }
                    }

                    if ($input) {
                        $query .= " AND (tende LIKE N'%$input%' OR tenmonhoc LIKE N'%$input%')";
                    }

                    $query .= " GROUP BY DT.made, thoigianbatdau,thoigianketthuc
                    
                            ORDER BY made DESC
                        ";
                    break;

                case "getAllCreatedReview":
                    // Lấy danh sách các đề ôn luyệnluyện đã giao  của giảng viên
                    $nguoiDungModel = new NguoiDungModel();
                    $check = $nguoiDungModel->checkAdmin($args['id']);
                    $user = $check ? " " : " AND nguoitao = '" . $args['id'] . "' ";
                    $query = "
                                SELECT DT.made, tende,hoten, tenmonhoc, GROUP_CONCAT(DISTINCT N.manhom) as manguongiao
                                , thoigianbatdau, thoigianketthuc,
                                GROUP_CONCAT(N.tennhom SEPARATOR ', ') AS nhom, namhoc, hocky,
                                '0' AS loaigiao
                                FROM dethi DT
                                JOIN monhoc MH ON DT.monthi = MH.mamonhoc
                                JOIN giaodethi GDT ON DT.made = GDT.made
                                JOIN nhom N ON N.manhom = GDT.manhom
                                JOIN nguoidung ND ON ND.id = N.giangvien 
                                WHERE  DT.trangthai = -1 $user
                            ";

                    if (isset($filter)) {
                        switch ($filter) {
                            case "0":
                                $query .= " AND CURRENT_TIMESTAMP() < thoigianbatdau";
                                break;
                            case "1":
                                $query .= " AND CURRENT_TIMESTAMP() BETWEEN thoigianbatdau AND thoigianketthuc";
                                break;
                            case "2":
                                $query .= " AND CURRENT_TIMESTAMP() > thoigianketthuc";
                                break;
                        }
                    }

                    if ($input) {
                        $query .= " AND (tende LIKE N'%$input%' OR tenmonhoc LIKE N'%$input%')";
                    }

                    $query .= " GROUP BY DT.made,thoigianbatdau,thoigianketthuc
                        
                                UNION ALL
                        
                                SELECT DT.made, tende,hoten, tenmonhoc, GROUP_CONCAT(DISTINCT HP.mahocphan) as manguongiao
                                , thoigianbatdau, thoigianketthuc,
                                    GROUP_CONCAT(L.tenlop SEPARATOR ', ') AS nhom, 
                                    nganh.tennganh AS namhoc,         -- hoặc dùng 'Không rõ'
                                    tenkhoahoc AS hocky,          -- hoặc dùng 'Không rõ'
                                    '1' AS loaigiao
                                FROM dethi DT
                                JOIN monhoc MH ON DT.monthi = MH.mamonhoc
                                JOIN giaodethilop GDTL ON DT.made = GDTL.madethi
                                JOIN hocphan HP ON HP.mahocphan = GDTL.mahocphan
                                JOIN lop L ON L.malop = HP.malop
                                JOIN nganh ON nganh.manganh = L.manganh
                                JOIN khoahoc ON  khoahoc.makhoahoc = L.makhoahoc
                                JOIN nguoidung ND ON ND.id = HP.magiaovien
                                WHERE  DT.trangthai = -1 $user
                            ";

                    if (isset($filter)) {
                        switch ($filter) {
                            case "0":
                                $query .= " AND CURRENT_TIMESTAMP() < thoigianbatdau";
                                break;
                            case "1":
                                $query .= " AND CURRENT_TIMESTAMP() BETWEEN thoigianbatdau AND thoigianketthuc";
                                break;
                            case "2":
                                $query .= " AND CURRENT_TIMESTAMP() > thoigianketthuc";
                                break;
                        }
                    }

                    if ($input) {
                        $query .= " AND (tende LIKE N'%$input%' OR tenmonhoc LIKE N'%$input%')";
                    }

                    $query .= " GROUP BY DT.made, thoigianbatdau,thoigianketthuc
                        
                                ORDER BY made DESC
                            ";
                    break;
                case "getAllCreatedTestBase":
                    // Lấy danh sách các đề đã tạo của giảng viên
                    $nguoiDungModel = new NguoiDungModel();
                    $check = $nguoiDungModel->checkAdmin($args['id']);
                    $user = $check ? " " : " AND nguoitao = '" . $args['id'] . "' ";
                    $query = "SELECT dethi.* , nguoidung.hoten, monhoc.tenmonhoc FROM dethi, monhoc, giaovien ,nguoidung
                        WHERE dethi.monthi = monhoc.mamonhoc and dethi.nguoitao = giaovien.magiaovien
                         and giaovien.magiaovien = nguoidung.id $user 

                    ";
                    if (isset($filter)) {
                        $query .= " AND dethi.trangthai = $filter";
                    }

                    if ($input) {
                        $query .= " AND (tende LIKE N'%$input%' OR tenmonhoc LIKE N'%$input%' OR hoten LIKE N'%$input%')";
                    }
                    //  $query .= " GROUP BY DT.made ORDER BY DT.made DESC";
                    break;
                case "getQuestionsForTest":
                    $query = "SELECT cauhoi.*, fnStripTags(noidung) AS noidungplaintext FROM cauhoi , chuong WHERE cauhoi.machuong = chuong.machuong AND (cauhoi.trangthai = 1 or cauhoi.trangthai = 2) AND chuong.mamonhoc = '" . $args['mamonhoc'] . "'";
                    if (isset($filter['machuong'])) {
                        $query .= " AND chuong.machuong = " . $filter['machuong'];
                    }
                    if (isset($filter['dokho'])) {
                        $query .= " AND dokho = " . $filter['dokho'];
                    }
                    if ($input) {
                        $input_entity_encode = htmlentities($input);
                        $query .= " AND (noidung LIKE N'%${input}%' OR fnStripTags(noidung) LIKE N'%${input_entity_encode}%')";
                    }
                    break;
                default:
            }
        }
        return $query;
    }
    public function getTestsGroupWithUserResult($manhom, $manguoidung)
    {
        $manhom = (int)$manhom;
        $manguoidung = addslashes($manguoidung);

        $sql = "
            SELECT 
                DT.made, DT.tende, DT.thoigianthi, GDT.manhom, DT.trangthai, 
                GDT.thoigianbatdau, GDT.thoigianketthuc,
                KQ.thoigianvaothi, KQ.thoigianlambai,
                CASE 
                    WHEN DT.xemdiemthi = 1 THEN KQ.diemthi 
                    ELSE NULL 
                END AS diemthi
            FROM giaodethi GDT
            JOIN dethi DT ON DT.made = GDT.made
            LEFT JOIN ketqua KQ 
                ON KQ.made = GDT.made AND KQ.manguoidung = '$manguoidung' AND KQ.manguongiao = GDT.manhom
            WHERE GDT.manhom = $manhom
            ORDER BY DT.made DESC
        ";

        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getTestsModuleWithUserResult($mahocphan, $manguoidung)
    {
        $mahocphan = (int)$mahocphan;
        $manguoidung = addslashes($manguoidung);

        $sql = "
            SELECT 
                DT.made, DT.tende, DT.thoigianthi, GDTL.mahocphan AS manhom, DT.trangthai,
                GDTL.thoigianbatdau, GDTL.thoigianketthuc,
                KQ.thoigianvaothi, KQ.thoigianlambai,
                CASE 
                    WHEN DT.xemdiemthi = 1 THEN KQ.diemthi 
                    ELSE NULL 
                END AS diemthi
            FROM giaodethilop GDTL
            JOIN dethi DT ON DT.made = GDTL.madethi
            LEFT JOIN ketqua KQ 
                ON KQ.made = GDTL.madethi AND KQ.manguoidung = '$manguoidung' AND KQ.manguongiao = GDTL.mahocphan
            WHERE GDTL.mahocphan = $mahocphan
            ORDER BY DT.made DESC
        ";

        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
