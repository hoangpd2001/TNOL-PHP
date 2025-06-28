<?php
class KetQuaModel extends DB
{
    public function start($made, $manguoidung, $loaigiao, $manguongiao)
    {
        $valid = true;
        $sql = "INSERT INTO `ketqua`(`made`, `manguoidung`, `loaigiao`, `manguongiao`)
         VALUES ('$made','$manguoidung','$loaigiao', '$manguongiao')";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }
    public function updateChangeTab($made, $manguoidung)
    {
        $solanchuyentab = $this->getChangeTab($made, $manguoidung)['solanchuyentab'];
        $sql = "UPDATE `ketqua` SET `solanchuyentab`='$solanchuyentab' WHERE `made`='$made' AND `manguoidung`='$manguoidung'";
        $valid = true;
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function getChangeTab($made, $manguoidung)
    {
        $sql = "SELECT `solanchuyentab` FROM `ketqua` WHERE `made`='$made' AND `manguoidung`='$manguoidung'";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function getMaKQ($made, $manguoidung, $loaigiao, $manguongiao)
    {
        $sql = "SELECT * FROM `ketqua` WHERE `made` = '$made' AND `manguoidung` = '$manguoidung'
        AND `loaigiao` = '$loaigiao' AND `manguongiao` = '$manguongiao'";

        $result = mysqli_query($this->con, $sql);

        return mysqli_fetch_assoc($result);
    }

    public function socaudung($listCauTraLoi)
    {
        $socaudung = 0;
        foreach ($listCauTraLoi as $tl) {
            $macauhoi = $tl['macauhoi'];
            $cautraloi = $tl['cautraloi'];
            $sql = "SELECT * FROM cautraloi ctl WHERE ctl.macauhoi = '$macauhoi' AND ctl.macautl = '$cautraloi' AND ctl.ladapan = 1";
            $result = mysqli_query($this->con, $sql);
            if (mysqli_num_rows($result) > 0) $socaudung++;
        }
        return $socaudung;
    }

    public function submit($made, $nguoidung, $list, $thoigian, $loaigiao, $manguongiao)
    {
        $sql_ketqua = "Select * from ketqua where made = '$made' and manguoidung = '$nguoidung'
         and loaigiao = '$loaigiao' and manguongiao = '$manguongiao'";
        $result_ketqua = mysqli_query($this->con, $sql_ketqua);
        $data = mysqli_fetch_assoc($result_ketqua);
        $thoigianvaolam = strtotime($data['thoigianvaothi']);
        $thoigianlambai = strtotime($thoigian) - $thoigianvaolam;
        $valid = true;
        $socaudung = $this->socaudung($list);
        $socau = count($list);
        $diem = round((10 / $socau * $socaudung), 2);
        $sql = "UPDATE `ketqua` SET `diemthi`='$diem',`thoigianlambai`='$thoigianlambai',`socaudung`='$socaudung' WHERE manguoidung = '$nguoidung' and made = '$made'  and loaigiao = '$loaigiao' and manguongiao = '$manguongiao'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        $makq = $data['makq'];
        foreach ($list as $ct) {
            $macauhoi = $ct['macauhoi'];
            $cautraloi = $ct['cautraloi'];
            $sql = "UPDATE `chitietketqua` SET `dapanchon`='$cautraloi' WHERE `makq`='$makq' AND `macauhoi`='$macauhoi'";
            $insertCt = mysqli_query($this->con, $sql);
            if (!$insertCt) $valid = false;
        }
        return $valid;
    }

    public function tookTheExam($made)
    {
        $sql = "select * from ketqua kq join nguoidung nd on kq.manguoidung = nd.id where kq.made = '$made'";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getExamineeByGroup($made, $manhom)
    {
        $sql = "SELECT KQ.*, email, hoten, avatar FROM ketqua KQ, nguoidung ND, chitietnhom CTN WHERE KQ.manguoidung = ND.id AND CTN.manguoidung = ND.id AND KQ.made = $made AND CTN.manhom = $manhom";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    // Lấy ra điểm tất cả đề thi của nhóm học phần để xuất file Excel
    public function getMarkOfAllTest($manhom)
    {
        // Lấy danh sách đề thi
        $sql_giaodethi = "SELECT dethi.made,tende FROM giaodethi,dethi WHERE manhom = $manhom AND giaodethi.made = dethi.made";
        $result_giaodethi = mysqli_query($this->con, $sql_giaodethi);
        $arr_dethi = array();
        while ($row = mysqli_fetch_assoc($result_giaodethi)) {
            $arr_dethi[] = $row;
        }

        // Lấy danh sách sinh viên
        $sql_sinhvien = "SELECT id,hoten FROM nguoidung, chitietnhom WHERE nguoidung.id = chitietnhom.manguoidung AND chitietnhom.manhom = $manhom";
        $result_sinhvien = mysqli_query($this->con, $sql_sinhvien);
        $arr_sinhvien = array();
        while ($row = mysqli_fetch_assoc($result_sinhvien)) {
            $arr_sinhvien[] = $row;
        }

        // Lấy bảng điểm
        $arr_ketqua = array();
        foreach ($arr_dethi as $dethi) {
            $arr_ketqua[$dethi['made']] = $this->getMarkOfOneTest($manhom, $dethi['made']);
        }

        // Xử lý header 
        $header = array("Mã sinh viên", "Tên sinh viên");
        foreach ($arr_dethi as $dethi) $header[] = $dethi['tende'];

        // Xử lý mảng
        $arr_result = array($header);
        for ($i = 0; $i < count($arr_sinhvien); $i++) {
            $row = array($arr_sinhvien[$i]['id'], $arr_sinhvien[$i]['hoten']);
            for ($j = 0; $j < count($arr_dethi); $j++) {
                array_push($row, $arr_ketqua[$arr_dethi[$j]['made']][$i]['diemthi']);
            }
            $arr_result[] = $row;
        }

        return $arr_result;
    }

    public function getMarkOfOneTest($manhom, $made)
    {
        $sql = "SELECT DISTINCT giaodethi.made,chitietnhom.manguoidung,ketqua.diemthi
        FROM giaodethi, chitietnhom LEFT JOIN ketqua ON chitietnhom.manguoidung = ketqua.manguoidung AND ketqua.made = $made 
        WHERE giaodethi.manhom = chitietnhom.manhom AND giaodethi.manhom = $manhom AND giaodethi.made = $made";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    // Lấy thông tin đề thi, kết quả của sinh viên để xuất file PDF
    public function getInfoPrintPdf($makq)
    {
        $sql = "SELECT DISTINCT ketqua.made, tende, tenmonhoc, mamonhoc, thoigianthi, manguoidung, hoten, socaudung,(socaude + socautb + socaukho) AS tongsocauhoi , diemthi
        FROM chitietketqua, ketqua, dethi, monhoc, nguoidung
        WHERE chitietketqua.makq = '$makq' AND chitietketqua.makq = ketqua.makq AND ketqua.manguoidung = nguoidung.id AND ketqua.made = dethi.made AND dethi.monthi = monhoc.mamonhoc";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    // Lấy điểm để thống kê 
    public function getStatictical($made, $manhom, $loaigiao)
    {
        $made = (int)$made;
        $diemthi = array_fill(0, 10, 0);
        $tongdiem = 0;
        $soluong = 0;
        $max = 0;
        $chuanop = 0;
        $khongthi = 0;

        // Convert manhom to array if it's a single number
        if (!is_array($manhom)) {
            $manhom = [$manhom];
        }

        $inManhom = implode(',', array_map('intval', $manhom));

        if ($loaigiao == 0) {
            // Giao theo nhóm
            $sql = "
         SELECT 
                CTN.manguoidung, 
                KQ.manguoidung AS mandkq, 
                KQ.makq, 
                KQ.made, 
                KQ.diemthi
            FROM chitietnhom CTN
            JOIN giaodethi GD ON CTN.manhom = GD.manhom AND GD.made = $made
            LEFT JOIN ketqua KQ 
                ON KQ.manguoidung = CTN.manguoidung 
                AND KQ.made = $made 
                AND KQ.manguongiao = CTN.manhom  
            WHERE CTN.manhom IN ($inManhom)

        ";
        } else {
            // Giao theo học phần
            $sql = "
            SELECT 
                ND.id AS manguoidung,
                KQ.manguoidung AS mandkq,
                KQ.makq,
                KQ.made,
                KQ.diemthi
            FROM hocphan HP
            JOIN giaodethilop GD ON GD.mahocphan = HP.mahocphan AND GD.madethi = $made
            JOIN lop L ON HP.malop = L.malop
            JOIN sinhvien SV ON SV.malop = L.malop
            JOIN nguoidung ND ON ND.id = SV.id
            LEFT JOIN ketqua KQ ON KQ.manguoidung = ND.id AND KQ.made = $made
            WHERE HP.mahocphan IN ($inManhom)

        ";
        }

        $result = mysqli_query($this->con, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['diemthi'] !== null) {
                $tongdiem += $row['diemthi'];
                $soluong++;
                $index = ceil($row['diemthi']) > 0 ? ceil($row['diemthi']) - 1 : 0;
                $diemthi[$index]++;
                if ($row['diemthi'] > $max) $max = $row['diemthi'];
            } else {
                if ($row['mandkq'] !== null) {
                    $chuanop++;
                } else {
                    $khongthi++;
                }
            }
        }

        return array(
            "diem_trung_binh" => $soluong !== 0 ? round($tongdiem / $soluong, 2) : 0,
            "da_nop_bai" => $soluong,
            "chua_nop_bai" => $chuanop,
            "khong_thi" => $khongthi,
            "diem_cao_nhat" => $max,
            "thong_ke_diem" => $diemthi
        );
    }


    public function getQueryAddColumnFirstname($original_query, $column, $order)
    {
        $from_index = strpos($original_query, "FROM");
        $select_string = substr($original_query, 0, $from_index) . ", SUBSTRING_INDEX(hoten, ' ', -1) AS firstname ";
        $from_string = substr($original_query, $from_index);
        return "$select_string $from_string ORDER BY firstname $order";
    }

    public function getListAbsentFromTest($filter, $input, $args)
    {
        $made = (int)$args['made'];
        $loaigiao = (int)$args['loaigiao'];
       $manhomList = is_array($args['manhom']) ? $args['manhom'] : [$args['manhom']];
        $inNguonGiao = implode(',', array_map('intval', $manhomList));
        $in = "";
        if (!empty($input)) {
            $input = addslashes($input);
            $in= " AND (hoten LIKE N'%$input%' OR ND.id LIKE '%$input%')";
        }

        if ($loaigiao == 0) {
            return "
        SELECT 
            NULL AS makq, $made AS made, ND.id AS manguoidung,
            NULL AS diemthi, NULL AS thoigianvaothi, NULL AS thoigianlambai,
            NULL AS socaudung, NULL AS solanchuyentab,
            ND.email, ND.hoten, ND.avatar,
            0 AS loaigiao,
            CTN.manhom AS manguongiao,
            N.tennhom AS tennguongiao
        FROM chitietnhom CTN
        JOIN nguoidung ND ON CTN.manguoidung = ND.id
        JOIN nhom N ON CTN.manhom = N.manhom
        WHERE CTN.manhom IN ($inNguonGiao)
        AND NOT EXISTS (
            SELECT 1 FROM ketqua KQ 
            WHERE KQ.made = $made 
            AND KQ.manguoidung = ND.id 
            AND KQ.manguongiao = CTN.manhom
        ) $in
        ";
        } else {
            return "
          SELECT 
                NULL AS makq, $made AS made, ND.id AS manguoidung,
                NULL AS diemthi, NULL AS thoigianvaothi, NULL AS thoigianlambai,
                NULL AS socaudung, NULL AS solanchuyentab,
                ND.email, ND.hoten, ND.avatar,
                1 AS loaigiao,
                HP.mahocphan AS manguongiao,
                L.tenLop AS tennguongiao
            FROM hocphan HP
            JOIN lop L ON L.malop = HP.malop
            JOIN sinhvien SV ON SV.malop = L.malop
            JOIN nguoidung ND ON ND.id = SV.id
            WHERE HP.mahocphan IN ($inNguonGiao)
            AND NOT EXISTS (
                SELECT 1 FROM ketqua KQ 
                WHERE KQ.made = $made 
                AND KQ.manguoidung = ND.id 
                AND KQ.manguongiao = HP.mahocphan
            )
            $in
        ";
        }
    }

    public function getQueryAll($filter, $input, $args)
    {
        $made = (int)$args['made'];
        $loaigiao = (int)$args['loaigiao'];
       $manhomList = is_array($args['manhom']) ? $args['manhom'] : [$args['manhom']];
$inNguonGiao = implode(',', array_map('intval', $manhomList));


        if ($loaigiao == 0) {
            $present_query = "
                SELECT 
                    KQ.makq, KQ.made, KQ.manguoidung, KQ.diemthi, KQ.thoigianvaothi, KQ.thoigianlambai,
                    KQ.socaudung, KQ.solanchuyentab,
                    ND.email, ND.hoten, ND.avatar,
                    0 AS loaigiao,
                    CTN.manhom AS manguongiao,
                    N.tennhom AS tennguongiao
                FROM ketqua KQ
                JOIN nguoidung ND ON KQ.manguoidung = ND.id
                JOIN chitietnhom CTN ON CTN.manguoidung = ND.id  AND CTN.manhom = KQ.manguongiao
                JOIN nhom N ON CTN.manhom = N.manhom
                WHERE KQ.made = $made AND N.manhom IN ($inNguonGiao)
            ";
        } else {
            $present_query = "
                SELECT 
                KQ.makq, KQ.made, KQ.manguoidung, KQ.diemthi, KQ.thoigianvaothi, KQ.thoigianlambai,
                KQ.socaudung, KQ.solanchuyentab,
                ND.email, ND.hoten, ND.avatar,
                1 AS loaigiao,
                HP.mahocphan AS manguongiao,
                L.tenLop AS tennguongiao
            FROM ketqua KQ
            JOIN nguoidung ND ON KQ.manguoidung = ND.id
            JOIN sinhvien SV ON SV.id = ND.id
            JOIN lop L ON SV.malop = L.malop
            JOIN hocphan HP ON HP.malop = L.malop AND KQ.manguongiao = HP.mahocphan
            WHERE KQ.made = $made AND HP.mahocphan IN ($inNguonGiao)
            ";
        }

        $absent_query = $this->getListAbsentFromTest($filter, $input, $args);
        $query = "($present_query) UNION ALL ($absent_query)";

        // Search
        if (!empty($input)) {
            $input = addslashes($input);
            $query = "SELECT * FROM ($query) AS T WHERE hoten LIKE N'%$input%' OR manguoidung LIKE '%$input%'";
        }

        // Sort
        if (isset($args["custom"]["function"]) && $args["custom"]["function"] === "sort") {
            $column = $args["custom"]["column"];
            $order = strtoupper($args["custom"]["order"]) === "DESC" ? "DESC" : "ASC";

            if ($column === "hoten") {
                $query = $this->getQueryAddColumnFirstname($query, $column, $order);
            } elseif (in_array($column, ['manguoidung', 'diemthi', 'thoigianvaothi', 'thoigianlambai', 'solanchuyentab'])) {
                $query .= " ORDER BY $column $order";
            } else {
                $query .= " ORDER BY manguoidung ASC";
            }
        } else {
            $query .= " ORDER BY manguoidung ASC";
        }

        return $query;
    }

    public function getQuery($filter, $input, $args)
    {
        switch ($filter) {
            case "all":
                return $this->getQueryAll($filter, $input, $args);

            case "absent":
                return $this->getListAbsentFromTest($filter, $input, $args);

            case "interrupted":
                $args['custom']['_where'] = "KQ.thoigianvaothi IS NOT NULL AND KQ.diemthi IS NULL";
                break;

            case "present":
                $args['custom']['_where'] = "KQ.diemthi IS NOT NULL";
                break;
        }

        // Nếu là present hoặc interrupted
        $made = (int)$args['made'];
        $loaigiao = (int)$args['loaigiao'];
       $manhomList = is_array($args['manhom']) ? $args['manhom'] : [$args['manhom']];
$inNguonGiao = implode(',', array_map('intval', $manhomList));

        $whereMore = isset($args['custom']['_where']) ? " AND " . $args['custom']['_where'] : "";

        if ($loaigiao == 0) {
            $query = "
                SELECT 
                    KQ.makq, KQ.made, KQ.manguoidung, KQ.diemthi, KQ.thoigianvaothi, KQ.thoigianlambai,
                    KQ.socaudung, KQ.solanchuyentab,
                    ND.email, ND.hoten, ND.avatar,
                    0 AS loaigiao,
                    N.manhom AS manguongiao,
                    N.tennhom AS tennguongiao
                FROM ketqua KQ
                JOIN nguoidung ND ON KQ.manguoidung = ND.id
                JOIN chitietnhom CTN ON CTN.manguoidung = ND.id and CTN.manhom = KQ.manguongiao
                JOIN nhom N ON CTN.manhom = N.manhom
                WHERE KQ.made = $made AND N.manhom IN ($inNguonGiao) $whereMore
            ";
        } else {
            $query = "
                SELECT 
                    KQ.makq, KQ.made, KQ.manguoidung, KQ.diemthi, KQ.thoigianvaothi, KQ.thoigianlambai,
                    KQ.socaudung, KQ.solanchuyentab,
                    ND.email, ND.hoten, ND.avatar,
                    1 AS loaigiao,
                    HP.mahocphan AS manguongiao,
                    L.tenLop AS tennguongiao
                FROM ketqua KQ
                JOIN nguoidung ND ON KQ.manguoidung = ND.id
                JOIN sinhvien SV ON SV.id = ND.id
                JOIN lop L ON SV.malop = L.malop
                JOIN hocphan HP ON HP.malop = L.malop AND KQ.manguongiao = HP.mahocphan
                WHERE KQ.made = $made AND HP.mahocphan IN ($inNguonGiao) $whereMore
            ";
        }

        // Search
        if (!empty($input)) {
            $input = addslashes($input);
            $query .= " AND (hoten LIKE N'%$input%' OR ND.id LIKE '%$input%')";
        }

        // Sort
        if (isset($args["custom"]["function"]) && $args["custom"]["function"] === "sort") {
            $column = $args["custom"]["column"];
            $order = strtoupper($args["custom"]["order"]) === "DESC" ? "DESC" : "ASC";

            if ($column === "hoten") {
                $query = $this->getQueryAddColumnFirstname($query, $column, $order);
            } elseif (in_array($column, ['manguoidung', 'diemthi', 'thoigianvaothi', 'thoigianlambai', 'solanchuyentab'])) {
                $query .= " ORDER BY $column $order";
            } else {
                $query .= " ORDER BY manguoidung ASC";
            }
        } else {
            $query .= " ORDER BY manguoidung ASC";
        }

        return $query;
    }


    public function getTestScoreGroup($made, $manhom)
    {
        $sql = "SELECT ds.manguoidung,ds.hoten,kqt.diemthi,kqt.thoigianvaothi,kqt.thoigianlambai,kqt.socaudung,kqt.solanchuyentab FROM (SELECT ctn.manguoidung,nd.hoten FROM chitietnhom ctn JOIN nguoidung nd ON ctn.manguoidung=nd.id WHERE ctn.manhom=$manhom) ds LEFT JOIN 
        (SELECT kq.manguoidung,kq.diemthi,kq.thoigianvaothi,kq.thoigianlambai,kq.socaudung,kq.solanchuyentab FROM ketqua kq JOIN giaodethi gdt ON kq.made=gdt.made WHERE gdt.made=$made AND gdt.manhom=$manhom) kqt ON ds.manguoidung=kqt.manguoidung";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getTestAll($made, $ds)
    {
        $list = implode(", ", $ds);
        $sql = "(SELECT KQ.makq, KQ.made, CTN.manguoidung, KQ.diemthi, KQ.thoigianvaothi, KQ.thoigianlambai, KQ.socaudung, KQ.solanchuyentab, email, hoten, avatar FROM chitietnhom CTN JOIN nguoidung ND ON ND.id = CTN.manguoidung LEFT JOIN ketqua KQ ON CTN.manguoidung = KQ.manguoidung AND KQ.made = $made WHERE KQ.made IS NULL AND CTN.manhom IN ($list))
        UNION
        (SELECT DISTINCT KQ.*, email, hoten, avatar FROM ketqua KQ, nguoidung ND, chitietnhom CTN WHERE KQ.manguoidung = ND.id AND CTN.manguoidung = ND.id AND KQ.made = $made  AND CTN.manhom IN ($list))
        ORDER BY manguoidung ASC";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function chuyentab($made, $id)
    {
        $sql_dethi = "SELECT * FROM ketqua WHERE made='$made' AND manguoidung='$id'";
        $result = mysqli_query($this->con, $sql_dethi);
        $data_dethi = mysqli_fetch_assoc($result);
        $solan = $data_dethi['solanchuyentab'];
        $solan++;
        $sql_update = "UPDATE ketqua SET solanchuyentab = '$solan' WHERE made='$made' AND manguoidung='$id'";
        $result_update = mysqli_query($this->con, $sql_update);
        $sql_check = "SELECT * FROM dethi where made = '$made'";
        $result_check = mysqli_query($this->con, $sql_check);
        $data_check = mysqli_fetch_assoc($result_check);
        return $data_check['nopbaichuyentab'];
    }
}
