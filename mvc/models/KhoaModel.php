<?php
class KhoaModel extends DB
{
    public function create($makhoa, $tenkhoa,$magiaovien)
    {
        $valid = true;
        $sql = "INSERT INTO `khoa`(`makhoa`, `tenkhoa`, `trangthai`, `magiaovien`) VALUES ('$makhoa','$tenkhoa', 1,'$magiaovien')";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function update( $makhoa, $tenkhoa, $magiaovien)
    {
        $valid = true;
        $sql = "UPDATE `khoa` SET  `tenkhoa`='$tenkhoa', `magiaovien` = '$magiaovien', `trangthai`=1 WHERE `makhoa`='$makhoa'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function delete($makhoa)
    {
        $valid = true;
        $sql = "UPDATE `khoa` SET `trangthai`= 0 WHERE `makhoa`='$makhoa'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM `khoa`" ;
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getQuery($filter, $input, $args)
    {
        $query = "SELECT * ,`nguoidung`.`hoten` as tengiaovien FROM `khoa`, `nguoidung` WHERE `khoa`.`trangthai` = 1 AND `khoa`.`magiaovien` = `nguoidung`.`id`";
        if ($input) {
            $query = $query . " AND (`khoa`.`tenkhoa` LIKE N'%${input}%' OR `khoa`.`makhoa` LIKE '%${input}%')";
        }

        return $query;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM `khoa` WHERE `makhoa` = '$id'";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function search($input)
    {
        $sql = "SELECT * FROM `khoa` WHERE `makhoa` LIKE '%$input%' OR `tenkhoa` LIKE N'%$input%'";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function checkKhoa($makhoa)
    {
        $sql = "SELECT * FROM `khoa` WHERE `makhoa` = '$makhoa'";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
