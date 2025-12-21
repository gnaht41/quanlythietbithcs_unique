<?php
// controllers/YN_TaiKhoanController.php
require_once __DIR__ . '/../models/YN_TaiKhoan.php';
require_once __DIR__ . '/../models/QT_Log.php'; // Thêm require cho Log

class YN_TaiKhoanController extends YN_TaiKhoan
{
    public function hienthiTaiKhoan()
    {
        $this->GetDanhSachTaiNguoiDung();
    }

    public function getDanhSachNguoiDung($filters = [])
    {
        return $this->searchNguoiDung($filters);
    }

    public function getVaiTroList()
    {
        return $this->getAllVaiTro();
    }

    public function CheckEmailTonTai($email)
    {
        $tontai = $this->CheckEmail($email);
        if ($tontai == 1) {
            //Email chưa tồn tại
            return 1;
        } else {
            //email đã tồn tại
            return 0;
        }
    }

    public function countnguoidung()
    {
        $row = $this->getnguoidung();
        if ($row === null)
            return -1;
        return (int)$row->num_rows;
    }

    public function themtaikhoan($hoten, $email, $maVT, $pass, $trangthai, $user)
    {
        if ($maVT == 1) {
            echo "<script>alert('Không được phép thêm tài khoản Admin!');</script>";
            return;
        }

        $maND = $this->getNextMaND();

        $sqlnguoidung = "INSERT INTO nguoidung (maND, hoTen, email, maVT)
                     VALUES ('$maND','$hoten','$email','$maVT')";

        $sqltaikhoan = "INSERT INTO taikhoan (username,password,maND,trangThai)
                    VALUES ('$user','$pass','$maND','$trangthai')";

        if ($this->sql($sqlnguoidung) && $this->sql($sqltaikhoan)) {

            $logModel = new Log();
            $logModel->ghiLog($_SESSION['maND'], 'INSERT', 'TaiKhoan', $maND);

            echo "<script>
                alert('Thêm tài khoản thành công!');
                window.location='?tab=ql-nguoi-dung';
              </script>";
            exit;
        }

        echo "<script>alert('Thêm tài khoản thất bại!');</script>";
    }


    // Vô hiệu hóa người dùng - khóa tài khoản
    public function XoaTK($maND)
    {
        // Kiểm tra không cho xóa Admin
        $checkAdmin = "SELECT maVT FROM nguoidung WHERE maND = '$maND'";
        $result = $this->con->query($checkAdmin);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['maVT'] == 1) {
                echo "<script>alert('Không được phép khóa tài khoản Admin!'); history.back();</script>";
                return;
            }
        }

        $sql = "UPDATE taikhoan SET trangThai = 'Khoá' WHERE maND = '$maND'";
        if ($this->sql($sql) == 1) {
            // Ghi log
            $logModel = new Log();
            $logModel->ghiLog($_SESSION['maND'], 'DELETE', 'TaiKhoan', $maND);

            echo "<script>alert('Đã khóa tài khoản!'); window.location='" . $_SERVER['REQUEST_URI'] . "';</script>";
            exit;
        } else {
            echo "<script>alert('Khóa tài khoản thất bại!'); history.back();</script>";
        }
    }

    // Sửa tài khoản người dùng
    public function SuaTK($maND, $hoTen, $maVT, $trangthai, $password, $passwordmoi, $passwordnhaplai)
    {
        // Kiểm tra không cho sửa thành Admin
        if ($maVT == 1) {
            echo "<script>alert('Không được phép đổi vai trò thành Admin!'); history.back();</script>";
            return 0;
        }

        // Kiểm tra không cho sửa Admin
        $checkAdmin = "SELECT maVT FROM nguoidung WHERE maND = '$maND'";
        $result = $this->con->query($checkAdmin);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['maVT'] == 1) {
                echo "<script>alert('Không được phép sửa tài khoản Admin!'); history.back();</script>";
                return 0;
            }
        }

        // Cập nhật trạng thái tài khoản
        $sql3 = "UPDATE taikhoan SET trangThai = '$trangthai' WHERE maND = '$maND'";
        $this->sql($sql3);

        // Kiểm tra và cập nhật mật khẩu nếu cần
        if (!empty($password) && !empty($passwordmoi) && !empty($passwordnhaplai)) {
            $rowND = $this->gettaikhoanMaND($maND);
            if ($rowND && $rowND->num_rows > 0) {
                $taikhoan = $rowND->fetch_assoc();
                if ($password == $taikhoan['password']) {
                    if ($passwordmoi == $passwordnhaplai) {
                        $sql1 = "UPDATE taikhoan SET password = '$passwordmoi' WHERE maND = '$maND'";
                        $this->sql($sql1);
                    } else {
                        echo "<script>alert('Mật khẩu mới không khớp!'); history.back();</script>";
                        return 0;
                    }
                } else {
                    echo "<script>alert('Mật khẩu hiện tại không đúng!'); history.back();</script>";
                    return 0;
                }
            }
        }

        // Cập nhật thông tin người dùng
        $sql2 = "UPDATE nguoidung SET hoTen = '$hoTen', maVT = $maVT WHERE maND = '$maND'";
        if ($this->sql($sql2) == 1) {
            // Ghi log
            $logModel = new Log();
            $logModel->ghiLog($_SESSION['maND'], 'UPDATE', 'TaiKhoan', $maND);

            echo "<script>alert('Cập nhật thành công!'); window.location='" . $_SERVER['REQUEST_URI'] . "';</script>";
            return 1;
        }
        return 0;
    }

    // Kiểm tra đăng nhập
    public function kiemTraDangNhap($username, $password)
    {
        return $this->checkTrangThaiDangNhap($username, $password);
    }
}