<?php
// models/YN_TaiKhoan.php
require_once __DIR__ . '/QT_Database.php';

class YN_TaiKhoan
{
    protected $con;
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->con = $this->db->getConnection();

        if ($this->con == null) {
            die("Lỗi kết nối database");
        }

        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    public function __destruct()
    {
        if ($this->db) {
            $this->db->closeConnection();
        }
    }

    // Lấy danh sách tài khoản người dùng (KHÔNG HIỂN THỊ ADMIN - maVT = 1)
    public function GetDanhSachTaiNguoiDung()
    {
        $sql = "
            SELECT *
            FROM nguoidung nd
            LEFT JOIN vaitro vt ON nd.maVT = vt.maVT
            LEFT JOIN taikhoan tk ON tk.maND = nd.maND
            WHERE nd.maVT != 1
            ORDER BY nd.maND ASC
        ";
        $rs = $this->con->query($sql);

        if (!$rs) {
            echo '<tr><td colspan="6">Lỗi truy vấn: ' . htmlspecialchars($this->con->error) . '</td></tr>';
            return;
        }
        if ($rs->num_rows === 0) {
            echo '<tr><td colspan="6">Chưa có người dùng</td></tr>';
            return;
        }

        $i = 0;
        while ($row = $rs->fetch_assoc()) {
            $i++;
            $statusStyle = ($row['trangThai'] == 'Khoá') ? 'style="color: red; font-weight: bold;"' : '';

            echo '<tr>
                    <td>' . $i . '</td>
                    <td>' . htmlspecialchars($row['hoTen']) . '</td>
                    <td>' . htmlspecialchars($row['email']) . '</td>
                    <td>' . htmlspecialchars($row['tenVT'] ?? '—') . '</td>
                    <td ' . $statusStyle . '>' . htmlspecialchars($row['trangThai']) . '</td>
                    <td class="hanh-dong">
                        <button type="button" name="sua" class="nut-sua nut-sua-nguoi-dung" 
                                data-mand="' . htmlspecialchars($row['maND']) . '"
                                data-hoten="' . htmlspecialchars($row['hoTen']) . '"
                                data-username="' . htmlspecialchars($row['username']) . '"
                                data-email="' . htmlspecialchars($row['email']) . '"
                                data-tenvt="' . htmlspecialchars($row['maVT']) . '"
                                data-trangthai="' . htmlspecialchars($row['trangThai']) . '">
                        Sửa</button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="mand" value="' . htmlspecialchars($row['maND']) . '">
                            <button type="submit" name="xoa" class="nut-xoa" 
                                    onclick="return confirm(\'Bạn có chắc muốn khóa tài khoản này?\')">Khóa</button>
                        </form>
                    </td>
                  </tr>';
        }
    }

    // Lấy danh sách vai trò (KHÔNG BAO GỒM ADMIN)
    public function getAllVaiTro()
    {
        $sql = "SELECT maVT, tenVT FROM vaitro WHERE maVT != 1 ORDER BY maVT";
        $result = $this->con->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Tìm kiếm người dùng (KHÔNG BAO GỒM ADMIN)
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
            LEFT JOIN vaitro vt ON nd.maVT = vt.maVT
            LEFT JOIN taikhoan tk ON tk.maND = nd.maND
            WHERE nd.maVT != 1
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

    // Kiểm tra Email
    public function checkEmail($email)
    {
        $sql = "select * from nguoidung where email = '$email'";
        $Email = $this->con->query($sql);
        if ($Email->num_rows > 0) {
            return 0;
        } else {
            return 1;
        }
    }

    // Truy vấn sql
    public function sql($sql)
    {
        if ($this->con->query($sql)) {
            return 1;
        } else {
            return 0;
        }
    }

    // Lấy thông tin taikhoan
    public function gettaikhoan()
    {
        $sql = "select * from taikhoan";
        $row = $this->con->query($sql);
        if ($row != false) {
            if ($row->num_rows > 0) {
                return $row;
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    // Lấy thông tin nguoidung (KHÔNG BAO GỒM ADMIN)
    public function getnguoidung()
    {
        $sql = "select * from nguoidung where maVT != 1";
        $row = $this->con->query($sql);
        if ($row != false) {
            if ($row->num_rows > 0) {
                return $row;
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    public function gettaikhoanMaND($maND)
    {
        $sql = "select * from taikhoan where maND ='$maND'";
        $row = $this->con->query($sql);
        if ($row != false) {
            if ($row->num_rows > 0) {
                return $row;
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    // Kiểm tra trạng thái tài khoản khi đăng nhập
    public function checkTrangThaiDangNhap($username, $password)
    {
        $sql = "SELECT tk.*, nd.maVT 
                FROM taikhoan tk 
                JOIN nguoidung nd ON tk.maND = nd.maND 
                WHERE tk.username = '$username' AND tk.password = '$password'";

        $result = $this->con->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Kiểm tra trạng thái (nếu Khoá thì không cho đăng nhập, trả về thông báo)
            if ($row['trangThai'] == 'Khoá') {
                return ['status' => 'locked', 'message' => 'Tài khoản đã bị khóa!'];
            }

            return ['status' => 'success', 'data' => $row];
        }

        return ['status' => 'failed', 'message' => 'Sai tên đăng nhập hoặc mật khẩu!'];
    }

    // THÊM HÀM NÀY
    public function getNextMaND()
    {
        $sql = "SELECT MAX(maND) AS max_id FROM nguoidung";
        $rs = $this->con->query($sql);
        if ($rs && $row = $rs->fetch_assoc()) {
            return ((int)$row['max_id']) + 1;
        }
        return 1;
    }
}