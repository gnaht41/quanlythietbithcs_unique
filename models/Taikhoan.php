<?php
// models/Quanlytaikhoan.php
class Taikhoan
{
    protected $con;

    public function __construct()
    {
        require_once __DIR__ . '/clsconnect.php';
        $p = new connect();
        $this->con = $p->ketnoi();
        if ($this->con == null) {
            die("Lỗi kết nối database");
        }
        mysqli_set_charset($this->con, "utf8");
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    // Lấy danh sách tài khoản người dùng (JOIN để tránh N+1 query)
    public function GetDanhSachTaiNguoiDung()
    {
        $sql = "
            SELECT *
            FROM nguoidung nd
            LEFT JOIN vaitro vt  ON nd.maVT = vt.maVT
            LEFT JOIN taikhoan tk ON tk.maND = nd.maND
            WHERE tk.trangThai = 'Hoạt động'
            ORDER BY nd.MaND ASC
        ";
        $rs = $this->con->query($sql);

        if (!$rs) {
            echo '<tr><td colspan="7">Lỗi truy vấn: ' . htmlspecialchars($this->con->error) . '</td></tr>';
            return;
        }
        if ($rs->num_rows === 0) {
            echo '<tr><td colspan="7">Chưa có người dùng</td></tr>';
            return;
        }

        $i = 0;
        while ($row = $rs->fetch_assoc()) {
            $i++;
            echo '<tr>
                    <td>' . $i . '</td>
                    <td>' . htmlspecialchars($row['hoTen']) . '</td>
                    <td>' . htmlspecialchars($row['email']) . '</td>
                    <td>' . htmlspecialchars($row['tenVT'] ?? '—') . '</td>
                    <td>' . htmlspecialchars($row['trangThai']) . '</td>
                    <!-- <td>' . date("Y-m-d") . '</td> -->
                    <td class="hanh-dong">
                        <button type="button" name="sua" class="nut-sua" id="nut-sua-nguoi-dung" data-mand="'.$row['maND'].'"
                                                                                                 data-hoten="'.$row['hoTen'].'"
                                                                                                 data-username="'.$row['username'].'"
                                                                                                 data-email="'.$row['email'].'"
                                                                                                 data-tenvt="'.$row['maVT'].'"
                                                                                                 data-trangthai="'.$row['trangThai'].'"
                                                                                                 data-password="'.$row['password'].'">
                        Sửa</button>
                        <!-- <button class="nut-khoa">Vô hiệu</button> -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="mand" value="' . $row['maND'] . '">
                            <button type="submit" name="xoa" class="nut-xoa">Xóa</button>
                        </form>
                    </td>
                  </tr>';
                  
        }
    }

    public function getAllVaiTro()
    {
        $sql = "SELECT maVT, tenVT FROM vaitro ORDER BY maVT";
        $result = $this->con->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function searchNguoiDung($filters = [])
    {
        $sql = "
            SELECT
                nd.maND,
                nd.hoTen,
                nd.email,
                nd.maVT,
                vt.tenVT,
                tk.username,
                tk.password,
                tk.trangThai
            FROM nguoidung nd
            LEFT JOIN vaitro vt  ON nd.maVT = vt.maVT
            LEFT JOIN taikhoan tk ON tk.maND = nd.maND
            WHERE 1 = 1
        ";

        $params = [];
        $types = "";

        if (!empty($filters['tuKhoa'])) {
            $sql .= " AND (nd.hoTen LIKE ? OR nd.email LIKE ?) ";
            $keyword = '%' . $filters['tuKhoa'] . '%';
            $params[] = $keyword;
            $params[] = $keyword;
            $types .= "ss";
        }

        if (!empty($filters['maVT'])) {
            $sql .= " AND nd.maVT = ? ";
            $params[] = (int)$filters['maVT'];
            $types .= "i";
        }

        if (!empty($filters['trangThai'])) {
            $sql .= " AND tk.trangThai = ? ";
            $params[] = $filters['trangThai'];
            $types .= "s";
        }

        $sql .= " ORDER BY nd.maND ASC";

        $stmt = $this->con->prepare($sql);
        if ($stmt === false) {
            die("Lỗi SQL: " . $this->con->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $stmt->close();
        return $data;
    }

    //Kiểm tra Email
    public function checkEmail($email)
    {
        $sql = "select * from nguoidung where email = '$email'";
        $Email = $this->con->query($sql);
        if($Email->num_rows > 0){
            return 0; 
        }else{
            return 1;
        }
    }
    // public function checkEmail($email)
    // {
    //     $stmt = $this->con->prepare("SELECT * FROM nguoidung WHERE email = ?");
    //     $stmt->bind_param("s", $email);
    //     $stmt->execute();
    //     $result = $stmt->get_result();

    //     return $result->num_rows > 0 ? 1 : 0;
    // }


    //Ttruy vấn sql
    public function sql($sql)
    {
		if($this->con->query($sql))
		{
			return 1;	
		}
		else
		{
			return 0;	
		}
    }
    
    //lấy thông tin taikhoan
    public function gettaikhoan()
    {
        $sql = "select * from taikhoan";
        $row = $this->con->query($sql);
        if($row != false)
        {
            if($row->num_rows > 0)
            {
                return $row;
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return null;
        }
    }

    //lấy thông tin nguoidung
    public function getnguoidung()
    {
        $sql = "select * from nguoidung";
        $row = $this->con->query($sql);
        if($row != false)
        {
            if($row->num_rows > 0)
            {
                return $row;
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return null;
        }
    }

    public function gettaikhoanMaND($maND)
    {
        $sql = "select * from taikhoan where maND ='$maND'";
        $row = $this->con->query($sql);
        if($row != false)
        {
            if($row->num_rows > 0)
            {
                return $row;
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return null;
        }
    }

    // public function layThongTinNguoiDung($maND) {
    //     $sql = "SELECT nd.MaND, nd.hoTen, nd.email, nd.username, nd.maVT, tk.trangthai, tk.ngayTao
    //             FROM nguoidung nd
    //             LEFT JOIN taikhoan tk ON nd.MaND = tk.MaND
    //             WHERE nd.MaND = ?";
    //     $stmt = $this->con->prepare($sql);
    //     $stmt->bind_param("i", $maND);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     return $result->fetch_assoc();
    // }
}
