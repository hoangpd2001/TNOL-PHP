<?php
class MonHocModel extends DB
{
    public function create($mamon, $tenmon, $sotinchi, $sotietlythuyet, $sotietthuchanh, $makhoa)
    {
        $valid = true;
        $sql = "INSERT INTO `monhoc`(`mamonhoc`, `tenmonhoc`, `sotinchi`, `sotietlythuyet`, `sotietthuchanh`, `trangthai`,`makhoa`) VALUES ('$mamon','$tenmon','$sotinchi','$sotietlythuyet','$sotietthuchanh', 1,'$makhoa')";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function update($id, $mamon, $tenmon, $sotinchi, $sotietlythuyet, $sotietthuchanh,$makhoa)
    {
        $valid = true;
        $sql = "UPDATE `monhoc` SET `mamonhoc`='$mamon',`tenmonhoc`='$tenmon',`sotinchi`='$sotinchi',`sotietlythuyet`='$sotietlythuyet',`sotietthuchanh`='$sotietthuchanh',`makhoa`='$makhoa' WHERE `mamonhoc`='$id'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function delete($mamon)
    {
        $valid = true;
        $sql = "UPDATE `monhoc` SET `trangthai`= 0 WHERE `mamonhoc`='$mamon'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM `monhoc` WHERE `trangthai` = 1";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM `monhoc` WHERE `mamonhoc` = '$id'";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function search($input)
    {
        $sql = "SELECT * FROM `monhoc` WHERE `mamonhoc` LIKE '%$input%' OR `tenmonhoc` LIKE N'%$input%';";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getAllByFaculty($makhoa)
    {
        $sql = "SELECT * FROM `monhoc` WHERE `makhoa` = $makhoa";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }
    public function getAllSubjectAssignment($userid)
    {
        $sql = "SELECT monhoc.* FROM phancong, monhoc WHERE manguoidung = '$userid' AND monhoc.mamonhoc = phancong.mamonhoc AND monhoc.trangthai = 1";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getQuery($filter, $input, $args)
    {
        if ($input) {
            return $this->getQueryWithInput($filter, $input, $args);
        }
        $query = "SELECT * FROM `monhoc` WHERE `trangthai` = 1";
        if (isset($filter)) {
            if (isset($filter['makhoa'])) {
                $query .= " AND monhoc.makhoa = " . $filter['makhoa'];
            }
        }
     
        return $query;


    }
    public function getQueryWithInput($filter, $input, $args)
    {
        $query = "SELECT * FROM `monhoc` WHERE `trangthai` = 1";
        if ($input) {
            $query = $query . " AND (`monhoc`.`tenmonhoc` LIKE N'%${input}%' OR `monhoc`.`mamonhoc` LIKE '%${input}%')";
        }
        if (isset($filter)) {
            if (isset($filter['makhoa'])) {
                $query .= " AND monhoc.makhoa = " . $filter['makhoa'];
            }
        }
        return $query;
    }

    public function checkSubject($mamon)
    {
        $sql = "SELECT * FROM `monhoc` WHERE `mamonhoc` = $mamon";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
