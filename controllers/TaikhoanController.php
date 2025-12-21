<?php
// controllers/QuanlytaikhoanController.php
require_once __DIR__ . '/../models/Taikhoan.php';

class TaikhoanController extends Taikhoan
{
    // public function main()
    // {
    //     if ($_GET['action'] === 'getUser' && isset($_GET['mand'])) {
    //         $mand = intval($_GET['mand']);
    //         $model = new Taikhoan();
    //         $user = $model->layThongTinNguoiDung($mand);

    //         header('Content-Type: application/json');
    //         echo json_encode($user);
    //         exit;
    //     }   
    // }
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
        if($tontai == 1)
        {
            //Email chưa tồn tại
            return 1;
        }
        else
        {
            //email đã tồn tại
            return 0;
        }
    }
    public function countnguoidung()
    {
        $row = $this->getnguoidung();
        if($row === null)
            return -1;
        return (int)$row->num_rows;
    }

    public function themtaikhoan($maND, $hoten, $email, $maVT, $pass, $trangthai, $user)
    {
        $sqlnguoidung = "INSERT INTO nguoidung (maND, hoTen, email, maVT) 
                        VALUES ('$maND','$hoten','$email', '$maVT');";
        $sqltaikhoan = "INSERT INTO taikhoan (username, password, maND, trangThai)
                        VALUES ('$user','$pass', '$maND', '$trangthai');";
        if($this->sql($sqlnguoidung))
        {
            if($this->sql($sqltaikhoan))
            {
                header("Location: " . $_SERVER['REQUEST_URI']);
            }
            else
            {
                echo "Thêm tài khoản lỗi";
            }
        }
        else
        {
            echo "Thêm người dùng lỗi";
        } 
    }

    // Vô hiệu hóa người dùng - xóa người dùng
    public function XoaTK($maND)
    {
        $sql = "UPDATE taikhoan SET trangThai = 'Khoá' WHERE maND = '$maND'";
        if($this->sql($sql) == 1)
        {
            header("Location: " . $_SERVER['REQUEST_URI']);
        }
        else
        {
            echo "Xóa tài khoản thất bại !";
        }
    }
    // Sửa tài khoản người dùng
    public function SuaTK($maND, $hoTen, $maVT, $trangthai, $password, $passwordmoi, $passwordnhaplai)
    {
        // kiểm tra và cập nhật mật khẩu nếu cần
        if($password != "")
        {
            $rowND = $this->gettaikhoanMaND($maND);
            $taikhoan = $rowND->fetch_assoc();
            if($password == $taikhoan['password'])
            {
                if($passwordmoi == $passwordnhaplai)
                {
                    $sql1 = "update taikhoan set password = '$passwordmoi' where maND = '$maND'";
                    $updatePass = $this->sql($sql1);
                }
            }
        }

        $sql2 = "update nguoidung set hoTen = '$hoTen', maVT = $maVT where maND = '$maND'";
        // $sql3 = "update taikhoan set trangThai = '$trangthai' 
        //                          where maND = '$maND'";
        if($this->sql($sql2) == 1)
        {
            
            return 1;
            // if($this->sql($sql3) == 1)
            // {
            //     return 1;
            // }
        }
    }
}

// Gọi controller khi file được truy cập
// $n = new TaikhoanController();
// $n->main();
