<?php
require_once "./mvc/models/LopModel.php";


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
    public function getBySubject($trangthai, $userid )
    {
        
        $filter = "";
        if ($trangthai) {
            $filter .= " AND hp.trangthai = '$trangthai'";
        }
        if (!class_exists('NguoiDungModel')) {
            require_once './mvc/models/NguoiDungModel.php';
        }
        $nguoidungModel = new NguoiDungModel() ;
        if (!$nguoidungModel->checkAdmin($userid)) {
            $filter .= " AND hp.magiaovien = '$userid'";
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
            WHERE hp.trangthai = 1 ".  $filter."
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
    public function getAllModule_User($user_id, $hienthi)
    {
        $sql = "SELECT monhoc.mamonhoc,monhoc.tenmonhoc,hocphan.mahocphan, tenlop, tenkhoahoc, 
         gv.hoten,
         gv.avatar
        FROM  hocphan, nguoidung gv , monhoc, lop, sinhvien sv, khoahoc
        WHERE  gv.id = hocphan.magiaovien 
        AND lop.makhoahoc = khoahoc.makhoahoc
        AND monhoc.mamonhoc = hocphan.mamonhoc AND hocphan.malop = lop.malop
        AND lop.malop = sv.malop
        AND hocphan.trangthai = $hienthi AND sv.id = '$user_id'";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getDetailGroup($mahocphan)
    {
        $sql = "SELECT hocphan.mahocphan, hocphan.magiaovien, nguoidung.hoten,
         nguoidung.avatar, tenmonhoc, hocphan.mamonhoc,tenkhoahoc, tennganh,tenlop
        FROM hocphan, nguoidung, monhoc, lop, khoahoc,nganh
        WHERE nguoidung.id = hocphan.magiaovien and hocphan.malop = lop.malop and lop.makhoahoc = khoahoc.makhoahoc and lop.manganh = nganh.manganh 
        and monhoc.mamonhoc = hocphan.mamonhoc AND hocphan.mahocphan = $mahocphan";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }


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
        $query = "SELECT ND.id, avatar, hoten, email, gioitinh, ngaysinh, SUBSTRING_INDEX(hoten, ' ', -1) AS firstname 
        FROM hocphan HP, lop L,sinhvien SV, nguoidung ND 
        WHERE HP.malop = L.malop and L.malop = SV.malop and SV.id = ND.id  AND HP.mahocphan = " . $args['mahocphan'];
        if ($input) {
            $query .= " AND (ND.hoten LIKE N'%${input}%' OR ND.id LIKE N'%${input}%')";
        }
        $query .= " ORDER BY firstname $order";
        return $query;
    }

    public function getQuery($filter, $input, $args)
    {
        $query = "SELECT ND.id, avatar, hoten, email, gioitinh, ngaysinh FROM hocphan HP,lop L, nguoidung ND,sinhvien SV 
        WHERE HP.malop = L.malop and L.malop = SV.malop and SV.id = ND.id 
        AND HP.mahocphan = " . $args['mahocphan'];
        if ($input) {
            $query .= " AND (ND.hoten LIKE N'%${input}%' OR ND.id LIKE N'%${input}%')";
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
