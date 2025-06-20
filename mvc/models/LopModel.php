<?php
class LopModel extends DB
{
    public function create($tenlop, $manganh, $makhoahoc, $magiaovien)
    {
        $valid = true;
        $sql = "INSERT INTO `lop`(`tenlop`, `manganh`, `makhoahoc`, `magiaovien`, `trangthai`) 
                VALUES ( '$tenlop', '$manganh', '$makhoahoc', '$magiaovien', 1)";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $result;
    }

    public function update($malop, $tenlop, $manganh, $makhoahoc, $magiaovien, $trangthai)
    {
        $valid = true;
        $sql = "UPDATE `lop` 
                SET `tenlop`='$tenlop', `manganh`='$manganh', `makhoahoc`='$makhoahoc', `magiaovien`='$magiaovien', trangthai = '$trangthai' 
                WHERE `malop` = '$malop'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function delete($malop)
    {
        $valid = true;
        $sql = "UPDATE `lop` SET `trangthai` = 0 WHERE `malop` = '$malop'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function getAll()
    {
        $sql = "SELECT lop.*, khoahoc.tenkhoahoc FROM `lop`, khoahoc WHERE lop.makhoahoc = khoahoc.makhoahoc and lop.`trangthai` = 1";
        $result = mysqli_query($this->con, $sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getById($malop)
    {
        $sql = "SELECT lop.*, makhoa FROM `lop`, nganh WHERE nganh.manganh = lop.manganh and`malop` = '$malop'";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function getByMajorCount($manganh,$makhoahoc)
    {
        $sql = "SELECT * FROM `lop` WHERE manganh = $manganh and makhoahoc = $makhoahoc and trangthai = 1";
        $result = mysqli_query($this->con, $sql);
        $data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row; 
        }

        return $data;
    }

    public function search($input)
    {
        $sql = "SELECT * FROM `lop` WHERE `malop` LIKE '%$input%' OR `tenlop` LIKE N'%$input%'";
        $result = mysqli_query($this->con, $sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function checkClass($malop)
    {
        $sql = "SELECT * FROM `lop` WHERE `malop` = '$malop'";
        $result = mysqli_query($this->con, $sql);
        $rows = [];
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

        $query = " SELECT 
        lop.*, 
        nganh.makhoa, 
        nganh.tennganh,
        nguoidung.id,
        nguoidung.hoten,
        COUNT(sinhvien.id) AS tongsinhvien 
    FROM lop
    INNER JOIN nganh ON lop.manganh = nganh.manganh
    INNER join nguoidung on nguoidung.id = lop.magiaovien
    LEFT JOIN sinhvien ON sinhvien.malop = lop.malop
    WHERE 1 = 1";
        if (isset($filter)) {
            if (isset($filter['makhoa'])) {
                $query .= " AND makhoa = '" . $filter['makhoa'] . "'";
            }
            if (isset($filter['manganh'])) {
                $query .= " AND lop.manganh = '" . $filter['manganh'] . "'";
            }
            if (isset($filter['makhoahoc'])) {
                $query .= " AND makhoahoc = '" . $filter['makhoahoc'] . "'";
            }
            if (isset($filter['trangthai'])) {
                $query .= " AND lop.trangthai = '" . $filter['trangthai'] . "'";
            }
        }
        $query .= " GROUP BY lop.malop, lop.tenlop, lop.manganh, lop.makhoahoc, lop.trangthai, magiaovien, makhoa, nguoidung.id, nguoidung.hoten, nganh.tennganh";
        return $query;
    }

    public function getQueryWithInput($filter, $input, $args)
    {
        $query = " SELECT 
        lop.*, 
        nganh.makhoa, 
        nganh.tennganh,
        nguoidung.id,
        nguoidung.hoten,
        COUNT(sinhvien.id) AS tongsinhvien 
    FROM lop
    INNER JOIN nganh ON lop.manganh = nganh.manganh
    INNER join nguoidung on nguoidung.id = lop.magiaovien
    LEFT JOIN sinhvien ON sinhvien.malop = lop.malop
    WHERE 1 = 1
                  AND (`malop` LIKE '%$input%' OR `tenlop` LIKE N'%$input%')";
        if (isset($filter)) {
            if (isset($filter['makhoa'])) {
                $query .= " AND makhoa = '" . $filter['makhoa'] . "'";
            }
            if (isset($filter['manganh'])) {
                $query .= " AND lop.manganh = '" . $filter['manganh'] . "'";
            }
            if (isset($filter['makhoahoc'])) {
                $query .= " AND makhoahoc = '" . $filter['makhoahoc'] . "'";
            }
            if (isset($filter['trangthai'])) {
                $query .= " AND lop.trangthai = '" . $filter['trangthai'] . "'";
            }
        }
        $query .= " GROUP BY lop.malop, lop.tenlop, lop.manganh, lop.makhoahoc, lop.trangthai, magiaovien, makhoa, nguoidung.id, nguoidung.hoten, nganh.tennganh";

        return $query;
    }
}
