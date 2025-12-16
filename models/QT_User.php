<?php
// models/QT_User.php
require_once 'QT_Database.php';

class User
{
    private $conn;
    private $table_name = "TaiKhoan";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Hàm kiểm tra đăng nhập
    public function checkLogin($username, $password)
    {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Lỗi kết nối CSDL.'];
        }

        // Truy vấn sử dụng Prepared Statements để bảo mật
        $query = "SELECT tk.maND, nd.maVT
                  FROM " . $this->table_name . " tk
                  JOIN NguoiDung nd ON tk.maND = nd.maND
                  WHERE tk.username = ? AND tk.password = ?"; // Lưu ý: Mật khẩu chưa hash

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return ['success' => false, 'message' => 'Lỗi chuẩn bị câu lệnh: ' . $this->conn->error];
        }

        // Gắn tham số (s = string)
        $stmt->bind_param("ss", $username, $password);

        // Thực thi
        if (!$stmt->execute()) {
            $stmt->close();
            return ['success' => false, 'message' => 'Lỗi thực thi câu lệnh: ' . $stmt->error];
        }


        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $stmt->close();
            // Trả về mảng chứa thông tin nếu thành công
            return ['success' => true, 'data' => ['maND' => $row['maND'], 'maVT' => $row['maVT']]];
        } else {
            $stmt->close();
            // Trả về false nếu không tìm thấy
            return ['success' => false, 'message' => 'Sai tài khoản hoặc mật khẩu!'];
        }
    }
}