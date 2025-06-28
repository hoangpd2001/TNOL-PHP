<?php

use function PHPSTORM_META\type;

class CauHoiModel extends DB{
    public function create($noidung, $dokho, $machuong, $nguoitao,$trangthai)
    {
        $sql = "INSERT INTO `cauhoi`(`noidung`, `dokho`,  `machuong`, `nguoitao`, trangthai) VALUES ('$noidung','$dokho','$machuong','$nguoitao', '$trangthai')";
        $result = mysqli_query($this->con, $sql);
        return $this->con;
    }

    public function update($macauhoi, $noidung, $dokho, $machuong, $nguoitao, $trangthai)
    {
        $valid = true;
        $sql = "UPDATE `cauhoi` SET `noidung`='$noidung',`dokho`='$dokho',`machuong`='$machuong', `trangthai` = '$trangthai' WHERE `macauhoi`=$macauhoi";
        $result = mysqli_query($this->con, $sql);
        if(!$result) $valid = false;
        return $valid;
    }

    public function delete($macauhoi)
    {
        $valid = true;
        $sql = "UPDATE `cauhoi` SET `trangthai`='-1' WHERE `macauhoi`= $macauhoi";
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
    public function countQuestion($chuongs)
    {
        // Danh sách chương có thể là "35,37,42" → cần xử lý để an toàn
        $chuongs = explode(',', $chuongs);
        $chuongs = array_map('intval', $chuongs); // tránh SQL injection
        $listChuong = implode(',', $chuongs);

        $sql = "SELECT COUNT(*) as total 
                FROM cauhoi 
                WHERE machuong IN ($listChuong) AND trangthai = 2";

        $result = mysqli_query($this->con, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            return intval($row['total']);
        }
        return 0;
    }

    public function getQuestionByClientReview($chuongs, $socau)
    {
        $chuongsArr = explode(',', $chuongs);
        if (count($chuongsArr) == 0 || $socau <= 0) return [];

        // Bước 1: Đếm số lượng câu hỏi theo chương và độ khó
        $chuong_data = [];
        foreach ($chuongsArr as $machuong) {
            $sql = "SELECT dokho, COUNT(*) as total 
                FROM cauhoi 
                WHERE machuong = '$machuong' AND trangthai = 1 
                GROUP BY dokho";
            $result = mysqli_query($this->con, $sql);

            $chuong_data[$machuong] = [
                'total' => 0,
                'dokho' => [1 => 0, 2 => 0, 3 => 0],
            ];

            while ($row = mysqli_fetch_assoc($result)) {
                $chuong_data[$machuong]['dokho'][$row['dokho']] = (int)$row['total'];
                $chuong_data[$machuong]['total'] += (int)$row['total'];
            }
        }

        $total_available = array_sum(array_column($chuong_data, 'total'));
        if ($total_available < $socau) $socau = $total_available;

        $alloc = [];
        $remaining = $socau;

        foreach ($chuong_data as $machuong => $data) {
            if ($remaining <= 0) break;
            $proportional = min($data['total'], round($data['total'] / $total_available * $socau));
            $alloc[$machuong] = $proportional;
            $remaining -= $proportional;
        }

        // Phân bổ phần dư nếu còn
        if ($remaining > 0) {
            foreach ($chuong_data as $machuong => $data) {
                if ($remaining <= 0) break;
                $canTake = $data['total'] - ($alloc[$machuong] ?? 0);
                if ($canTake > 0) {
                    $take = min($canTake, $remaining);
                    $alloc[$machuong] = ($alloc[$machuong] ?? 0) + $take;
                    $remaining -= $take;
                }
            }
        }

        // Bước 3: Lấy câu hỏi theo phân bổ và độ khó
        $questions = [];
        foreach ($alloc as $machuong => $sl_cau) {
            if ($sl_cau <= 0) continue;

            $count_per_dokho = floor($sl_cau / 3);
            $remain = $sl_cau % 3;
            $dokho_alloc = [1 => $count_per_dokho, 2 => $count_per_dokho, 3 => $count_per_dokho];
            for ($i = 1; $i <= $remain; $i++) {
                $dokho_alloc[$i]++;
            }

            foreach ($dokho_alloc as $dokho => $count) {
                if ($count <= 0) continue;

                $maxAvail = $chuong_data[$machuong]['dokho'][$dokho];
                $count = min($count, $maxAvail);

                $sql = "SELECT * FROM cauhoi 
                    WHERE machuong = '$machuong' AND dokho = $dokho AND trangthai = 1 
                    ORDER BY RAND() LIMIT $count";
                $res = mysqli_query($this->con, $sql);
                while ($row = mysqli_fetch_assoc($res)) {
                    $row['cautraloi'] = $this->getAnswersByQuestionId($row['macauhoi']);
                    $questions[] = $row;
                    if (count($questions) >= $socau) return $questions;
                }
            }
        }

        return $questions;
    }
    public function getAnserByClientReview($macauhoiListString)
    {
        if (!$macauhoiListString) return [];

        $sql = "SELECT macauhoi, macautl 
                FROM cautraloi 
                WHERE ladapan = 1 AND macauhoi IN ($macauhoiListString)";

        $res = mysqli_query($this->con, $sql);
        $result = [];

        while ($row = mysqli_fetch_assoc($res)) {
            $macauhoi = $row['macauhoi'];
            $macautl_dung = $row['macautl'];

            $result[$macauhoi] = $macautl_dung;
        }

        return $result;
    }




    private function getAnswersByQuestionId($macauhoi)
    {
        $sql = "SELECT macautl, noidungtl 
                FROM cautraloi 
                WHERE macauhoi = '$macauhoi' 
                ORDER BY RAND()";
        $result = mysqli_query($this->con, $sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
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
        if($args['type']==0){
            $type = " cauhoi.trangthai = " . 0 ;
        }else{
            $type = " (cauhoi.trangthai = " . 1 . " OR cauhoi.trangthai = " . 2 . " ) ";
        }

        $query = "
            SELECT cauhoi.*, chuong.machuong, chuong.tenchuong, monhoc.mamonhoc, tenmonhoc, makhoa 
            FROM cauhoi 
            JOIN chuong ON cauhoi.machuong = chuong.machuong 
            JOIN monhoc ON chuong.mamonhoc = monhoc.mamonhoc 
            WHERE ". $type;

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
            if (!empty($filter['trangthai']) && $filter['trangthai'] != 3) {
                $query .= " AND cauhoi.trangthai = " . $filter['trangthai'];
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
