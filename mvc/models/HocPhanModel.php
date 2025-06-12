<?php
include "./mvc/models/LopModel.php";
class HocPhanModel extends DB
{
    public function create($manganh, $makhoahoc, $monhoc, $magiaovien, $ghichu)
    {
        $lopModel = new LopModel();
        $listLop = $lopModel->getByMajorCount($manganh, $makhoahoc);
        $valid = true;
        foreach ($listLop as $lop) {
            $malop = $lop['malop'];
            $sql = "INSERT INTO `hocphan` (`ghichu`, `malop`, `magiaovien`, `mamonhoc`, trangthai) 
                    VALUES ('$ghichu','$malop','$magiaovien','$monhoc',1)";
            $result = mysqli_query($this->con, $sql);
            if (!$result) {
                $valid = false;
            }
        }

        return $valid;
    }


    public function update($mahocphan, $ghichu, $magiaovien)
    {
        $valid = true;
        $sql = "UPDATE `hocphan` SET `ghichu`='$ghichu',`magiaovien`='$magiaovien' WHERE `mahocphan`='$mahocphan'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $valid = false;
        }
        return $valid;
    }


    // Ẩn || Hiện nhóm
    public function hide($mahocphan, $giatri)
    {
        $valid = true;
        $sql = "UPDATE `hocphan` SET `trangthai`=' $giatri' WHERE `mahocphan`='$mahocphan'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $valid = false;
        }
        return $valid;
    }


    public function getById($mahocphan)
    {
        $sql = "SELECT hocphan.*, lop.manganh, nganh.makhoa, lop.makhoahoc
        FROM hocphan
        JOIN lop ON hocphan.malop = lop.malop
        JOIN nganh ON lop.manganh = nganh.manganh
        JOIN khoa ON nganh.makhoa = khoa.makhoa
        WHERE hocphan.mahocphan = '$mahocphan'";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    //    Lấy tất cả nhóm của người tạo và gom lại theo mã môn học, năm học, học kỳ
    public function getBySubject($manganh = null, $makhoahoc = null)
    {
        $filter = "";
        if ($manganh) {
            $filter .= " AND n.manganh = '$manganh'";
        }
        if ($makhoahoc) {
            $filter .= " AND kh.makhoahoc = '$makhoahoc'";
        }

        $sql = "SELECT 
                hp.mahocphan, 
                mh.mamonhoc, mh.tenmonhoc,
                l.malop, l.tenlop,
                n.manganh, n.tennganh,
                kh.makhoahoc, kh.tenkhoahoc,
                hp.trangthai, hp.ghichu,
                gv.id AS magiaovien, gv.hoten AS tengiaovien,
                COUNT(sv.id) AS siso
            FROM hocphan hp
            JOIN monhoc mh ON hp.mamonhoc = mh.mamonhoc
            JOIN lop l ON hp.malop = l.malop
            JOIN nganh n ON l.manganh = n.manganh
            JOIN khoahoc kh ON l.makhoahoc = kh.makhoahoc
            JOIN nguoidung gv ON hp.magiaovien = gv.id
            LEFT JOIN sinhvien sv ON sv.malop = l.malop
            WHERE hp.trangthai = 1 
            GROUP BY 
                hp.mahocphan, 
                mh.mamonhoc, mh.tenmonhoc,
                l.malop, l.tenlop,
                n.manganh, n.tennganh,
                kh.makhoahoc, kh.tenkhoahoc,
                hp.trangthai, hp.ghichu,
                gv.id, gv.hoten
            ORDER BY n.tennganh ASC, kh.tenkhoahoc ASC;
            ";

        $result = mysqli_query($this->con, $sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        $grouped = [];

        foreach ($rows as $row) {
            $key = $row['mamonhoc'] . '|' . $row['manganh'] . '|' . $row['makhoahoc'];

            $lop_detail = [
                "mahocphan" => $row["mahocphan"],
                "trangthai" => $row["trangthai"],
                "ghichu" => $row["ghichu"],
                "malop" => $row["malop"],
                "siso" => $row["siso"],
                "magiaovien" => $row["magiaovien"],
                "tengiaovien" => $row["tengiaovien"],
                "tenlop" => $row["tenlop"]
            ];

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    "mamonhoc" => $row["mamonhoc"],
                    "tenmonhoc" => $row["tenmonhoc"],
                    "manganh" => $row["manganh"],
                    "tennganh" => $row["tennganh"],
                    "makhoahoc" => $row["makhoahoc"],
                    "tenkhoahoc" => $row["tenkhoahoc"],
     
                    "lop" => [$lop_detail]
                ];
            } else {
                $grouped[$key]["lop"][] = $lop_detail;
            }
        }

        return array_values($grouped);
    }



    // Lấy các nhóm mà sinh viên tham gia
    public function getAllGroup_User($user_id, $hienthi)
    {
        $sql = "SELECT monhoc.mamonhoc,monhoc.tenmonhoc,hocphan.mahocphan, hocphan.tenhocphan, namhoc, hocky ,nguoidung.hoten, nguoidung.avatar,chitiethocphan.hienthi
        FROM chitiethocphan, hocphan, nguoidung, monhoc
        WHERE chitiethocphan.mahocphan = hocphan.mahocphan AND nguoidung.id = hocphan.giangvien AND monhoc.mamonhoc = hocphan.mamonhoc AND chitiethocphan.manguoidung = $user_id
        AND chitiethocphan.hienthi = $hienthi AND hocphan.trangthai != 0";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    // Lấy chi tiết một nhóm mà sinh viên tham gia
    // public function getDetailGroup($mahocphan)
    // {
    //     $sql = "SELECT monhoc.mamonhoc,monhoc.tenmonhoc,hocphan.mahocphan, hocphan.tenhocphan, namhoc, hocky, hocphan.giangvien, nguoidung.hoten, nguoidung.avatar
    //     FROM hocphan, nguoidung, monhoc
    //     WHERE nguoidung.id = hocphan.giangvien AND monhoc.mamonhoc = hocphan.mamonhoc AND hocphan.mahocphan = $mahocphan";
    //     $result = mysqli_query($this->con, $sql);
    //     return mysqli_fetch_assoc($result);
    // }

    // Lấy danh sách bạn học chung nhóm


    // hàm update sỉ số sinh viên trong nhóm
    public function updateSiso($mahocphan)
    {
        $valid = true;
        $sql = "UPDATE `hocphan` SET `siso`= (SELECT count(*) FROM `chitiethocphan` where mahocphan = $mahocphan ) WHERE `mahocphan` = $mahocphan";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $valid = false;
        }
        return $valid;
    }
    // Hàm lấy sinh viên ra từ nhóm



    // public function checkAcc($mssv, $mahocphan)
    // {
    //     $sql_checkGroup = "SELECT * FROM chitiethocphan where mahocphan='$mahocphan' AND manguoidung='$mssv'";
    //     $result_checkGroup = mysqli_query($this->con, $sql_checkGroup);
    //     if ($result_checkGroup->num_rows > 0) {
    //         return "0";
    //     }

    //     $sql_checkNguoiDung = "SELECT * FROM nguoidung where id='$mssv'";
    //     $result_checkNguoiDung = mysqli_query($this->con, $sql_checkNguoiDung);
    //     if ($result_checkNguoiDung->num_rows > 0) {
    //         return "-1";
    //     }
    //     return "1";
    // }

    public function getQuerySortByName($filter, $input, $args, $order)
    {
        $query = "SELECT ND.id, avatar, hoten, email, gioitinh, ngaysinh, SUBSTRING_INDEX(hoten, ' ', -1) AS firstname FROM chitietnhom CTN, nguoidung ND WHERE CTN.manguoidung = ND.id AND CTN.manhom = " . $args['manhom'];
        if ($input) {
            $query .= " AND (ND.hoten LIKE N'%${input}%' OR CTN.manguoidung LIKE N'%${input}%')";
        }
        $query .= " ORDER BY firstname $order";
        return $query;
    }

    public function getQuery($filter, $input, $args)
    {
        $query = "SELECT ND.id, avatar, hoten, email, gioitinh, ngaysinh FROM chitietnhom CTN, nguoidung ND WHERE CTN.manguoidung = ND.id AND CTN.manhom = " . $args['manhom'];
        if ($input) {
            $query .= " AND (ND.hoten LIKE N'%${input}%' OR CTN.manguoidung LIKE N'%${input}%')";
        }
        if (isset($filter)) {
            if (isset($filter['manhomquyen'])) {
                $query .= " AND manhomquyen = " . $filter['manhomquyen'];
            }
        }
        if (isset($args["custom"]["function"])) {
            $function = $args["custom"]["function"];
            switch ($function) {
                case "sort":
                    $column = $args["custom"]["column"];
                    $order = $args["custom"]["order"];
                    switch ($column) {
                        case "id":
                            $query .= " ORDER BY $column $order";
                            break;
                        case "hoten":
                            $query = $this->getQuerySortByName($filter, $input, $args, $order);
                            break;
                        default:
                    }
                    break;
                default:
            }
        }

        return $query;
    }
}
