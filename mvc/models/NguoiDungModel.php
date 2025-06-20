<?php
class NguoiDungModel extends DB
{

    public function create($id, $email, $fullname, $password, $ngaysinh, $gioitinh, $role, $trangthai, $malop = null)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `nguoidung`(`id`, `email`,`hoten`, `gioitinh`,`ngaysinh`,`matkhau`,`trangthai`, `manhomquyen`) VALUES ('$id','$email','$fullname','$gioitinh','$ngaysinh','$password',$trangthai, $role)";
        $check = true;
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $check = false;
        }
        $sql = "INSERT INTO `sinhvien`(`id`, `malop`) VALUES ('$id','$malop')";
        mysqli_query($this->con, $sql);
        return $check;
    }

    public function delete($id)
    {
        $check = true;
        $sql = "DELETE FROM `nguoidung` WHERE `id`='$id'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $check = false;
        return $check;
    }

    public function update($id, $email, $fullname, $password, $ngaysinh, $gioitinh, $role, $trangthai)
    {
        $querypass = '';
        if ($password != '') {
            $passwordd = password_hash($password, PASSWORD_DEFAULT);
            $querypass = ", `matkhau`='$passwordd'";
        }
        $sql = "UPDATE `nguoidung` SET `email`='$email', `hoten`='$fullname',`gioitinh`='$gioitinh',`ngaysinh`='$ngaysinh',`trangthai`='$trangthai',`manhomquyen`='$role' $querypass WHERE `id`='$id'";
        $check = true;
        $result = mysqli_query($this->con, $sql);
        if (!$result) $check = false;
        return $check;
    }

    // Update Profile
    public function updateProfile($fullname, $gioitinh, $ngaysinh, $email, $id)
    {
        $sql = "UPDATE `nguoidung` SET `email` = '$email',`hoten`='$fullname',`gioitinh`='$gioitinh',`ngaysinh`='$ngaysinh'WHERE `id`='$id'";
        $check = true;
        $result = mysqli_query($this->con, $sql);
        if (!$result) $check = false;
        return $check;
    }

    // Up avatar
    public function uploadFile($id, $tmpName, $imageExtension, $validImageExtension, $name)
    {
        $check = true;

        if (!in_array($imageExtension, $validImageExtension)) {
            $check = false;
        } else {
            $newImageName = $name . "-" . uniqid(); // Generate new name image
            $newImageName .= '.' . $imageExtension;

            move_uploaded_file($tmpName, './public/media/avatars/' . $newImageName);
            $sql = "UPDATE `nguoidung` SET `avatar`='$newImageName' WHERE `id`='$id'";
            mysqli_query($this->con, $sql);
            $check = true;
        }
        return $check;
    }

    public function getAllTeacherFaculty($makhoa)
    {
        $sql = "SELECT * FROM `nguoidung`, giangvien WHERE giangvien.magiangvien = nguoidung.id and giangvien.`makhoa` = '$makhoa' and manhomquyen = 10";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getAll()
    {
        $sql = "SELECT nguoidung.*, nhomquyen.`tennhomquyen`
        FROM nguoidung, nhomquyen
        WHERE nguoidung.`manhomquyen` = nhomquyen.`manhomquyen`";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getAllByClassroom($malop)
    {
        $sql = "SELECT nguoidung.*
        FROM nguoidung, sinhvien
        WHERE nguoidung.`id` = sinhvien.`id`  AND sinhvien.`malop` = '$malop'
        and nguoidung.manhomquyen = 11";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getById($id)
    {
        $sql = "SELECT * FROM `nguoidung` WHERE `id` = '$id'";
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function getByEmail($email)
    {
        $sql = "SELECT email,otp FROM `nguoidung` WHERE `email` = '$email'";
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
    }

    public function checkOpt($email, $opt)
    {
        $sql = "SELECT email,otp FROM `nguoidung` WHERE `email` = '$email' AND `otp` = '$opt'";
        $result = mysqli_query($this->con, $sql);
        return $result->num_rows;
    }


    public function changePassword($id, $new_password)
    {
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE `nguoidung` SET `matkhau`='$new_password' WHERE `id` = '$id'";
        $check = true;
        $result = mysqli_query($this->con, $sql);
        if (!$result) $check = false;
        return $check;
    }

    public function checkPassword($masv, $password)
    {
        $user = $this->getById($masv);
        $result = password_verify($password, $user['matkhau']);
        return $result;
    }

    public function checkLogin($masv, $password)
    {
        $user = $this->getById($masv);
        if ($user == '') {
            return json_encode(["message" => "Tài khoản không tồn tại !", "valid" => "false"]);
        } else if ($user['trangthai'] == 0) {
            return json_encode(["message" => "Tài khoản bị khóa !", "valid" => "false"]);
        } else {
            $result = password_verify($password, $user['matkhau']);
            if ($result) {
                $token = time() . password_hash($masv, PASSWORD_DEFAULT);
                $resultToken = $this->updateToken($masv, $token);
                if ($resultToken) {
                    setcookie("token", $token, time() + 7 * 24 * 3600, "/");
                    return json_encode(["message" => "Đăng nhập thành công !", "valid" => "true"]);
                } else {
                    return json_encode(["message" => "Đăng nhập không thành công !", "valid" => "false"]);
                }
            } else {
                return json_encode(["message" => "Sai mật khẩu !", "valid" => "false"]);
            }
        }
    }

    public function updateToken($masv, $token)
    {
        $valid = true;
        $sql = "UPDATE `nguoidung` SET `token`='$token' WHERE `id` = '$masv'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) $valid = false;
        return $valid;
    }

    public function validateToken($token)
    {
        $sql = "SELECT * FROM `nguoidung` WHERE `token` = '$token'";
        $result = mysqli_query($this->con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_name'] = $row['hoten'];
            $_SESSION['avatar'] = $row['avatar'];
            $_SESSION['user_role'] = $this->getRole($row['manhomquyen']);
            return true;
        }
        return false;
    }

    public function getRole($manhomquyen)
    {
        $sql = "SELECT chucnang, hanhdong FROM chitietquyen WHERE manhomquyen = $manhomquyen";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        $roles = array();
        foreach ($rows as $item) {
            $chucnang = $item['chucnang'];
            $hanhdong = $item['hanhdong'];
            if (!isset($roles[$chucnang])) {
                $roles[$chucnang] = array($hanhdong);
            } else {
                array_push($roles[$chucnang], $hanhdong);
            }
        }
        return $roles;
    }

    public function logout()
    {
        setcookie("token", "", time() - 10, '/');
        $id = $_SESSION['user_id'];
        $sql = "UPDATE `nguoidung` SET `token`= NULL WHERE `id` = '$id'";
        session_destroy();
        $result = mysqli_query($this->con, $sql);
        return $result;
    }

    public function updateOpt($email, $otp)
    {
        $sql = "UPDATE `nguoidung` SET `otp`= $otp WHERE `email`='$email'";
        $result = mysqli_query($this->con, $sql);
        return $result;
    }
    function addFile($data, $pass, $malop)
    {
        // Bật chế độ throw exception khi có lỗi
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $ok = 0;
        $fa = 0;

        foreach ($data as $user) {
            try {
                $fullname = $user['fullname'];
                $email = $user['email'];
                $mssv = $user['mssv'];
                $gioitinh = $user['gioitinh'];
                $ngaysinh = $user['ngaysinh'];
                $password = password_hash($pass, PASSWORD_DEFAULT);
                $trangthai = $user['trangthai'];
                $nhomquyen = $user['nhomquyen'];

                // Thực hiện INSERT vào bảng nguoidung
                $sql1 = "INSERT INTO `nguoidung`(`id`, `email`, `hoten`, ngaysinh, gioitinh, `matkhau`, `trangthai`, `manhomquyen`)
                         VALUES ('$mssv','$email','$fullname','$ngaysinh','$gioitinh','$password','$trangthai','$nhomquyen')";
                mysqli_query($this->con, $sql1);
                // INSERT tiếp vào bảng sinhvien
                $sql2 = "INSERT INTO `sinhvien`(`id`, `malop`) VALUES ('$mssv','$malop')";
                mysqli_query($this->con, $sql2);
                mysqli_commit($this->con);
                $ok++;
            } catch (mysqli_sql_exception $e) {
                // Không cho in lỗi ra trình duyệt
                // Ghi log nếu muốn
                // error_log("Lỗi với MSSV $mssv: " . $e->getMessage());
                $fa++;
            }
        }

        return "Thêm thành công $ok sinh viên, thất bại $fa sinh viên";
    }


    function addFileGroup($data, $pass, $group)
    {
        $check = true;
        foreach ($data as $user) {
            $fullname = $user['fullname'];
            $email = $user['email'];
            $mssv = $user['mssv'];
            $password = password_hash($pass, PASSWORD_DEFAULT);
            $trangthai = $user['trangthai'];
            $nhomquyen = $user['nhomquyen'];
            $sql = "INSERT INTO `nguoidung`(`id`,`email`, `hoten`, `matkhau`, `trangthai`, `manhomquyen`) VALUES ('$mssv','$email','$fullname','$password','$trangthai','$nhomquyen')";
            $result = mysqli_query($this->con, $sql);
            $joinGroup = $this->join($group, $mssv);
            if ($result) {
            } else {
                $check = false;
            }
        }
        return $check;
    }

    public function join($manhom, $manguoidung)
    {
        $valid = true;
        $sql = "INSERT INTO `chitietnhom`(`manhom`, `manguoidung`) VALUES ('$manhom','$manguoidung')";
        $result = mysqli_query($this->con, $sql);
        $this->updateSiso($manhom);
        if (!$result) {
            $valid = false;
        } else {
            $this->updateSiso($manhom);
        }
        return $valid;
    }

    public function updateSiso($manhom)
    {
        $valid = true;
        $sql = "UPDATE `nhom` SET `siso`= (SELECT count(*) FROM `chitietnhom` where manhom = $manhom ) WHERE `manhom` = $manhom";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            $valid = false;
        }
        return $valid;
    }

    public function getQuery($filter = [], $input = '')
    {
        $query = "
            SELECT ND.*, NQ.tennhomquyen, SV.malop, L.tenlop
            FROM nguoidung ND
            JOIN nhomquyen NQ ON ND.manhomquyen = NQ.manhomquyen
            LEFT JOIN sinhvien SV ON ND.id = SV.id
            LEFT JOIN lop L ON SV.malop = L.malop
            WHERE ND.manhomquyen = 11
        ";

        if (isset($filter['malop'])) {
            $query .= " AND SV.malop = '" . mysqli_real_escape_string($this->con, $filter['malop']) . "'";
        }

        if (isset($filter['manganh'])) {
            $query .= " AND L.manganh = '" . mysqli_real_escape_string($this->con, $filter['manganh']) . "'";
        }

        if (isset($filter['makhoa'])) {
            $query .= " AND L.manganh IN (
                SELECT manganh FROM nganh WHERE makhoa = '" . mysqli_real_escape_string($this->con, $filter['makhoa']) . "'
            )";
        }

        if (isset($filter['trangthai'])) {
            $query .= " AND ND.trangthai = " . intval($filter['trangthai']);
        }

        if (!empty($input)) {
            $escapedInput = mysqli_real_escape_string($this->con, $input);
            $query .= " AND (ND.hoten LIKE N'%$escapedInput%' OR ND.id LIKE '%$escapedInput%')";
        }

        $query .= " ORDER BY ND.id ASC";

        return $query;
    }


    public function checkUser($mssv, $email)
    {
        $sql = "SELECT * FROM `nguoidung` WHERE `id` = $mssv OR `email` = '$email'";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function checkAdmin($mssv)
    {
        $sql = "SELECT `manhomquyen` FROM `nguoidung` WHERE `id` = '$mssv'";
        $result = mysqli_query($this->con, $sql);

        if ($result && $row = mysqli_fetch_assoc($result)) {
            return $row['manhomquyen'] == 1;
        }

        return false;
    }


    public function checkEmail($id)
    {
        $sql = "SELECT * FROM nguoidung where id = '$id'";
        $result = mysqli_query($this->con, $sql);
        $data = mysqli_fetch_assoc($result);
        return $data['email'];
    }

    public function checkEmailExist($email)
    {
        $sql = "SELECT * FROM nguoidung where email = '$email'";
        $result = mysqli_query($this->con, $sql);
        $row = $result->num_rows;
        return $row;
    }

    public function updateEmail($id, $email)
    {
        $sql = "UPDATE `nguoidung` SET `email`='$email' WHERE `id`='$id'";
        $result = mysqli_query($this->con, $sql);
        return $result;
    }

    public function getAllRoles()
    {
        $sql = "SELECT * FROM nhomquyen WHERE trangthai = 1";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function getDataByRole($id)
    {
        $sql = "SELECT * FROM `nguoidung` WHERE `manhomquyen` = '$id'";
        $result = mysqli_query($this->con, $sql);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
