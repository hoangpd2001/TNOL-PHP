<?php
class AnnouncementModel extends DB 
{
    
    public function create($thoigiantao,$nguoitao,$nhom,$content, $loaigiao)
    {
        $sql = "INSERT INTO `thongbao`(`noidung`,`thoigiantao`,`nguoitao`) VALUES ('$content','$thoigiantao','$nguoitao')";
        $result = mysqli_query($this->con, $sql);
        if($result) {
            $matb = mysqli_insert_id($this->con);
            // Một thông báo gửi cho nhiều nhóm 
            $result = $this->sendAnnouncement($matb, $nhom, $loaigiao);
            return $matb;
        } else return false;
    }

    public function getById($matb) {
        $sql = "SELECT * FROM thongbao WHERE matb = '$matb'";
        $result = mysqli_query($this->con,$sql);
        return mysqli_fetch_assoc($result);
    }

    public function sendAnnouncement($matb,$nhom, $loaigiao)
    {
        $valid = true;
        foreach ($nhom as $manhom) {
            $sql = "INSERT INTO `chitietthongbao`(`matb`, `manguongiao`, `loaigiao`) VALUES ('$matb','$manhom', '$loaigiao')";
            $result = mysqli_query($this->con, $sql);
            if (!$result) $valid = false;
        }
        return $valid;
    }

    public function getAnnounce($manhom, $loaigiao)
    {
        $manhom = (int)$manhom;


        $sql = "
            SELECT
                tb.matb,
                tb.noidung,
                MAX(u.avatar) AS avatar,
                MAX(u.hoten) AS hoten,
                MAX(tb.thoigiantao) AS thoigiantao
            FROM thongbao tb
            JOIN chitietthongbao ctp ON ctp.matb = tb.matb AND ctp.manguongiao = $manhom AND ctp.loaigiao=$loaigiao
            JOIN nguoidung u ON tb.nguoitao = u.id
            GROUP BY tb.matb, tb.noidung
            ORDER BY thoigiantao DESC;
        ";

        $result = mysqli_query($this->con, $sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getAll($user_id) 
    {
        $sql = "SELECT `chitietthongbao`.`matb`,`tennhom`,`noidung`, `tenmonhoc` ,`namhoc`, `hocky`, `thoigiantao`
        FROM `thongbao`, `chitietthongbao`,`nhom`,`monhoc` 
        WHERE `thongbao`.`matb` = `chitietthongbao`.`matb` AND `chitietthongbao`.`manhom` = `nhom`.`manhom` AND `nhom`.`mamonhoc` = `monhoc`.`mamonhoc`
        AND `thongbao`.`nguoitao` = $user_id ORDER BY thoigiantao DESC";
        $result = mysqli_query($this->con,$sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)){
            $matb = $row['matb'];
            $index = array_search($matb, array_column($rows, 'matb'));
            if ($index === false) {
                $item = [
                    "matb" => $matb,
                    "noidung" => $row['noidung'],
                    "tenmonhoc" => $row['tenmonhoc'],
                    "namhoc" => $row['namhoc'],
                    "hocky" => $row['hocky'],
                    "thoigiantao" => $row['thoigiantao'],
                    "nhom" => [$row['tennhom']]
                ];
                array_push($rows, $item);
            } else {
                array_push($rows[$index]["nhom"], $row['tennhom']);
            }
        }
        return $rows;
    }

    public function deleteAnnounce($matb)
    {  
        $result = $this->deleteDetailAnnounce($matb);
        if ($result) {
            $sql = "DELETE FROM `thongbao` WHERE `matb` = $matb";
            $result = mysqli_query($this->con,$sql);
            return true;
        } else return false;
    }


    // Xóa thông báo trong bảng thongbao
    public function deleteDetailAnnounce($matb)
    {
        $valid = true;
        $sql = "DELETE FROM `chitietthongbao` WHERE `matb` = $matb";
        $result = mysqli_query($this->con,$sql);
        if (!$result) $valid = false;
        return $valid; 
    }

    public function getDetail($matb)
    {
        $sql_announce = "SELECT `thongbao`.`matb`,`noidung`, `tenmonhoc` ,`namhoc`, `hocky` 
        FROM `thongbao`, `chitietthongbao`,`nhom`,`monhoc` 
        WHERE `thongbao`.`matb` = `chitietthongbao`.`matb` AND `chitietthongbao`.`manhom` = `nhom`.`manhom` AND `nhom`.`mamonhoc` = `monhoc`.`mamonhoc`
        AND `thongbao`.`matb` = $matb";
        $result_announce = mysqli_query($this->con,$sql_announce);
        $thongbao = mysqli_fetch_assoc($result_announce);
        if($thongbao != null) {
            $sql_sendAnnounce = "SELECT `manhom` FROM `chitietthongbao` WHERE `matb` = $matb";
            $result_sendAnnounce = mysqli_query($this->con,$sql_sendAnnounce);
            $thongbao['nhom'] = array();
            while ($row = mysqli_fetch_assoc($result_sendAnnounce)) {
                $thongbao['nhom'][] = $row['manhom'];
            } 
        }
        return $thongbao;
    }

    public function updateAnnounce($matb,$noidung,$nhom,$loaigiao)
    {
        $valid = true;
        $sql = "UPDATE `thongbao` SET `noidung`='$noidung' WHERE `matb` = $matb" ;
        $result = mysqli_query($this->con, $sql);
        if($result) {
            $this->deleteDetailAnnounce($matb);
            $this->sendAnnouncement($matb, $nhom, $loaigiao);
        } else $valid = false;
        return $valid; 
    }

    public function getNotifications($id)
    {
        $sql = "SELECT `tennhom`,`avatar`,`hoten`,`noidung`, `thoigiantao` ,`chitietnhom`.`manhom` , monhoc.mamonhoc, monhoc.tenmonhoc
        FROM `thongbao`,`chitietthongbao`,`chitietnhom`, `nguoidung`,`nhom` ,`monhoc`
        WHERE `thongbao`.`matb` = `chitietthongbao`.`matb` AND `chitietthongbao`.`manhom` = `chitietnhom`.`manhom` 
        AND `thongbao`.`nguoitao` = `nguoidung`.`id` 
        AND `chitietnhom`.`manhom` = `nhom`.`manhom`
        AND `monhoc`.`mamonhoc` = `nhom`.`mamonhoc`
        AND `chitietnhom`.`manguoidung` = $id
        ORDER BY thoigiantao DESC LIMIT 0, 5";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    public function getQuery($filter, $input, $args)
    {
        $query = "";
        $state = $filter;
        if($state == 0){
            $query = "SELECT TB.*, namhoc, hocky, GROUP_CONCAT(N.tennhom SEPARATOR ', ') AS nhom
            FROM thongbao TB
            JOIN chitietthongbao CTTB ON TB.matb = CTTB.matb
            JOIN nhom N ON CTTB.manguongiao = N.manhom
            WHERE TB.nguoitao = '" . $args['id'] . "' AND CTTB.loaigiao = 0";
        }else{
            $query = "SELECT TB.*, tennganh AS namhoc ,tenkhoahoc AS hocky, 
                GROUP_CONCAT(L.tenlop SEPARATOR ', ') AS nhom
            FROM thongbao TB
            JOIN chitietthongbao CTTB ON TB.matb = CTTB.matb
            JOIN hocphan N ON CTTB.manguongiao = N.mahocphan
            JOIN lop L ON N.malop = L.malop
            JOIN khoahoc KH ON KH.makhoahoc = L.makhoahoc
            JOIN nganh NH ON NH.manganh = L.manganh
            WHERE TB.nguoitao = '" . $args['id'] . "' AND CTTB.loaigiao = 1" ;
        }
        

        if ($input) {
            $query .= " AND noidung LIKE N'%${input}%'";
        }

        $query .= " GROUP BY TB.matb ORDER BY thoigiantao DESC";
        return $query;
    }
}
?>