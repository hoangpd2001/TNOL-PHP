<?php
class NganhModel extends DB
{
    public function create($manganh, $tennganh, $makhoa, $trangthai)
    {
        $valid = true;
        $sql = "INSERT INTO `nganh`(`manganh`, `tennganh`, `makhoa`, `trangthai`) 
                VALUES ('$manganh', '$tennganh', '$makhoa', 1)";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function update($manganh, $tennganh, $makhoa, $trangthai)
    {
        $valid = true;
        $sql = "UPDATE `nganh` 
                SET `tennganh` = '$tennganh', `makhoa` = '$makhoa', `trangthai` = 1 
                WHERE `manganh` = '$manganh'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function delete($manganh)
    {
        $valid = true;
        $sql = "UPDATE `nganh` SET `trangthai` = 0 WHERE `manganh` = '$manganh'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM `nganh`";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getAllFaculty($makhoa)
    {
        $sql = "SELECT * FROM `nganh` WHERE `makhoa` = '$makhoa'";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }


    public function getById($manganh)
    {
        $sql = "SELECT * FROM `nganh` WHERE `manganh` = '$manganh'";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function search($input)
    {
        $sql = "SELECT * FROM `nganh` 
                WHERE `manganh` LIKE '%$input%' OR `tennganh` LIKE N'%$input%'";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function checkNganh($manganh)
    {
        $sql = "SELECT * FROM `nganh` WHERE `manganh` = '$manganh'";
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
        $query = "SELECT `nganh`.*, `khoa`.`tenkhoa` FROM `nganh` 
                  JOIN `khoa` ON `nganh`.`makhoa` = `khoa`.`makhoa` 
                  WHERE `nganh`.`trangthai` = 1";
        if (isset($filter)) {
            if (isset($filter['makhoa'])) {
                $query .= " AND nganh.makhoa = " . $filter['makhoa'];
            }
        }

        return $query;
    }
    // public function getQuery($filter, $input, $args)
    // {
    //     $query = "SELECT `nganh`.*, `khoa`.`tenkhoa` FROM `nganh` 
    //               JOIN `khoa` ON `nganh`.`makhoa` = `khoa`.`makhoa` 
    //               WHERE `nganh`.`trangthai` = 1";
    //     if ($input) {
    //         $query .= " AND (`nganh`.`tennganh` LIKE N'%${input}%' OR `nganh`.`manganh` LIKE '%${input}%')";
    //     }
    //     return $query;
    // }
    public function getQueryWithInput($filter, $input, $args)
    {
        $query = "SELECT `nganh`.*, `khoa`.`tenkhoa` FROM `nganh` 
                  JOIN `khoa` ON `nganh`.`makhoa` = `khoa`.`makhoa` 
                  WHERE `nganh`.`trangthai` = 1";
        if ($input) {
            $query = $query . " AND (`nganh`.`tennganh` LIKE N'%${input}%' OR `nganh`.`manganh` LIKE '%${input}%')";
        }
        if (isset($filter)) {
            if (isset($filter['makhoa'])) {
                $query .= " AND nganh.makhoa = " . $filter['makhoa'];
            }
        }
        return $query;
    }
}
