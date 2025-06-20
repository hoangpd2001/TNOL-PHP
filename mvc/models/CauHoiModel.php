<?php
class CauHoiModel extends DB{
    public function create($noidung, $dokho, $machuong, $nguoitao,$trangthai)
    {
        $sql = "INSERT INTO `cauhoi`(`noidung`, `dokho`,  `machuong`, `nguoitao`, trangthai) VALUES ('$noidung','$dokho','$machuong','$nguoitao', '$trangthai')";
        $result = mysqli_query($this->con, $sql);
        return $this->con;
    }

    public function update($macauhoi, $noidung, $dokho, $machuong, $nguoitao)
    {
        $valid = true;
        $sql = "UPDATE `cauhoi` SET `noidung`='$noidung',`dokho`='$dokho',`machuong`='$machuong' WHERE `macauhoi`=$macauhoi";
        $result = mysqli_query($this->con, $sql);
        if(!$result) $valid = false;
        return $valid;
    }

    public function delete($macauhoi)
    {
        $valid = true;
        $sql = "UPDATE `cauhoi` SET `trangthai`='0' WHERE `macauhoi`= $macauhoi";
        $result = mysqli_query($this->con, $sql);
        if(!$result) $valid = false;
        return $valid;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM cauhoi JOIN monhoc on  cauhoi.mamonhoc = monhoc.mamonhoc limit 5";
        $result = mysqli_query($this->con,$sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getById($macauhoi)
    {
        $sql = "SELECT cauhoi.*, mamonhoc FROM `cauhoi`, chuong WHERE `macauhoi` = $macauhoi and cauhoi.machuong = chuong.machuong";
        $result = mysqli_query($this->con,$sql);
        return mysqli_fetch_assoc($result);
    }

    public function getAllBySubject($mamonhoc)
    {
        $sql = "SELECT * FROM `cauhoi` WHERE `mamonhoc` = $mamonhoc";
        $result = mysqli_query($this->con,$sql);
        return mysqli_fetch_assoc($result);
    }

    public function getTotalPage($content,$selected){
        switch($selected){
            case "Tất cả": $sql = "SELECT * FROM cauhoi where noidung like '%$content%'"; 
            break;
            case "Môn học": $sql = "SELECT * FROM cauhoi where noidung like '%$content%'"; 
            break;
            case "Mức độ": $sql = "SELECT * FROM cauhoi where noidung like '%$content%'"; 
            break;
        }
        $result = mysqli_query($this->con,$sql);
        $count =mysqli_num_rows($result);
        $data = $count%5==0?$count/5:floor($count/5)+1;
        echo $data;
    }

    public function getQuestionBySubject($mamonhoc, $machuong, $dokho, $content, $page)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $sql = "SELECT macauhoi, noidung, dokho, machuong FROM cauhoi WHERE mamonhoc = '$mamonhoc'";
        $sql .= $machuong == 0 ? "" : " AND machuong = $machuong";
        $sql .= $dokho == 0 ? "" : " AND dokho = $dokho";
        $sql .= $content == '' ? "" : " AND noidung LIKE '%$content%'";
        $sql .= " ORDER BY macauhoi DESC limit $offset,$limit";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getTotalPageQuestionBySubject($mamonhoc, $machuong, $dokho, $content)
    {
        $limit = 10;
        $sql = "SELECT macauhoi, noidung, dokho, machuong FROM cauhoi WHERE mamonhoc = '$mamonhoc' WHERE trangthai='1'";
        $sql .= $machuong == 0 ? "" : " AND machuong = $machuong";
        $sql .= $dokho == 0 ? "" : " AND dokho = $dokho";
        $sql .= $content == '' ? "" : " AND noidung LIKE '%$content%'";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return count($rows)/$limit;
    }

    public function getQueryWithInput($filter, $input, $args)
    {
        $input_entity_encode = htmlentities($input);
        $query = "
            SELECT *, fnStripTags(noidung) 
            FROM cauhoi 
            JOIN chuong ON cauhoi.machuong = chuong.machuong 
            JOIN monhoc ON chuong.mamonhoc = monhoc.mamonhoc 
            WHERE (noidung LIKE N'%${input}%' OR fnStripTags(noidung) LIKE N'%${input_entity_encode}%') 
            AND cauhoi.trangthai = '1'
        ";

        if (isset($filter)) {
            if (!empty($filter['mamonhoc'])) {
                $query .= " AND monhoc.mamonhoc = '" . $filter['mamonhoc'] . "'";
            }
            if (!empty($filter['machuong'])) {
                $query .= " AND chuong.machuong = " . $filter['machuong'];
            }
            if (!empty($filter['dokho']) && $filter['dokho'] != 0) {
                $query .= " AND cauhoi.dokho = " . $filter['dokho'];
            }
        }

        return $query;
    }

    public function getQuery($filter, $input, $args)
    {
        if ($input) {
            return $this->getQueryWithInput($filter, $input, $args);
        }

        $query = "
            SELECT * 
            FROM cauhoi 
            JOIN chuong ON cauhoi.machuong = chuong.machuong 
            JOIN monhoc ON chuong.mamonhoc = monhoc.mamonhoc 
            WHERE cauhoi.trangthai = 1 
           ";

        if (isset($filter)) {
            if (!empty($filter['mamonhoc'])) {
                $query .= " AND monhoc.mamonhoc = '" . $filter['mamonhoc'] . "'";
            }
            if (!empty($filter['machuong'])) {
                $query .= " AND chuong.machuong = " . $filter['machuong'];
            }
            if (!empty($filter['dokho']) && $filter['dokho'] != 0) {
                $query .= " AND cauhoi.dokho = " . $filter['dokho'];
            }
        }

        return $query;
    }

    public function getsoluongcauhoi($chuong, $monhoc, $dokho)
    {
        $c = "";
        $mh = "";

        if (is_array($chuong) && count($chuong) > 0) {
            foreach ($chuong as $key => $machuong) {
                $c .= "chuong.machuong = '$machuong'";
                if ($key < count($chuong) - 1) {
                    $c .= " OR ";
                }
            }
            $dieukien = "($c)";
        } else {
            $dieukien = "chuong.mamonhoc = '$monhoc'";
        }

        $sql = "SELECT COUNT(cauhoi.macauhoi) AS soluong
                FROM cauhoi, chuong
                WHERE chuong.machuong = cauhoi.machuong 
                AND $dieukien AND cauhoi.dokho = $dokho";

        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result)['soluong'];
    }
}
?>
