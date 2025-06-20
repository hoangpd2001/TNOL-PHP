

<?php

class GiaoVienModel extends DB
{

    public function getAllFaculty($makhoa)
    {
        $sql = "SELECT * FROM `nguoidung`, giaovien WHERE giaovien.magiaovien = nguoidung.id and giaovien.`makhoa` = '$makhoa' and manhomquyen = 10";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getFacultyByGiaoVien($magiaovien)
    {
        $magiaovien = mysqli_real_escape_string($this->con, $magiaovien);
        $sql = "SELECT makhoa FROM giaovien WHERE magiaovien = '$magiaovien' LIMIT 1";
        $result = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row ? $row['makhoa'] : null;
    }

    public function getAll()
    {
        $sql = "SELECT nguoidung.*, khoa.* FROM `nguoidung`, giaovien, khoa WHERE nguoidung.id=giaovien.magiaovien and giaovien.makhoa=khoa.makhoa and manhomquyen = 10";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    public function create($id, $email, $fullname, $password, $ngaysinh, $gioitinh, $role, $trangthai,$makhoa)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `nguoidung`(`id`, `email`,`hoten`, `gioitinh`,`ngaysinh`,`matkhau`,`trangthai`, `manhomquyen`) VALUES ('$id','$email','$fullname','$gioitinh','$ngaysinh','$password',$trangthai, $role)";
        $check = true;
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $check = false;
        }
        $sql ="INSERT INTO `giaovien`(`magiaovien`, `makhoa`) VALUES ('$id','$makhoa')";
        mysqli_query($this->con, $sql);
        return $check;
    }

    public function getQuery($filter, $input, $args)
    {
        $query = "SELECT ND.*, NQ.tennhomquyen, K.makhoa, K.tenkhoa FROM nguoidung ND, nhomquyen NQ, giaovien GV ,khoa K WHERE ND.manhomquyen = NQ.manhomquyen and ND.id = GV.magiaovien and GV.makhoa = K.makhoa"; ;
        if (isset($filter['role'])) {
            $query .= " AND ND.manhomquyen = " . $filter['role'];
        }
        if ($input) {
            $query = $query . " AND (ND.hoten LIKE N'%${input}%' OR ND.id LIKE '%${input}%')";
        }
        $query = $query . " ORDER BY id ASC";
        return $query;
    }
}
