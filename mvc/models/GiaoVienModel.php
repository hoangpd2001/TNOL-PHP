

<?php

class GiaoVienModel extends DB
{

    public function getAllFaculty($makhoa)
    {
        $sql = "SELECT * FROM `nguoidung`, giangvien WHERE giangvien.magiangvien = nguoidung.id and giangvien.`makhoa` = '$makhoa' and manhomquyen = 10";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
