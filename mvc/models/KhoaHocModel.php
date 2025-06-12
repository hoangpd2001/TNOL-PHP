<?php
class KhoaHocModel extends DB
{
    public function create($makhoahoc, $tenkhoahoc, $trangthai)
    {
        $valid = true;
        $sql = "INSERT INTO `khoahoc` (`makhoahoc`, `tenkhoahoc`, `trangthai`) VALUES ('$makhoahoc', '$tenkhoahoc', '$trangthai')";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function update($makhoahoc, $tenkhoahoc, $trangthai)
    {
        $valid = true;
        $sql = "UPDATE `khoahoc` SET `tenkhoahoc`='$tenkhoahoc', `trangthai`='$trangthai' WHERE `makhoahoc`='$makhoahoc'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function delete($makhoahoc)
    {
        $valid = true;
        $sql = "UPDATE `khoahoc` SET `trangthai` = 0 WHERE `makhoahoc` = '$makhoahoc'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM `khoahoc`";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getQuery($filter, $input, $args)
    {
        $query = "SELECT *  FROM `khoahoc` WHERE `khoahoc`.`trangthai` = 1";
        if ($input) {
            $query = $query . " AND (`tenkhoahoc` LIKE N'%${input}%' OR `makhoahoc` LIKE '%${input}%')";
        }

        return $query;
    }
    public function getById($id)
    {
        $sql = "SELECT * FROM `khoahoc` WHERE `makhoahoc` = '$id'";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function search($input)
    {
        $sql = "SELECT * FROM `khoahoc` WHERE `makhoahoc` LIKE '%$input%' OR `tenkhoahoc` LIKE N'%$input%'";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function checkKhoaHoc($makhoahoc)
    {
        $sql = "SELECT * FROM `khoahoc` WHERE `makhoahoc` = '$makhoahoc'";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
