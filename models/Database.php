<?php
// models/Database.php
class Database
{
    private $host = 'localhost'; // Hoặc IP/hostname của server CSDL
    private $db_name = 'qltb'; // Tên database
    private $username = 'root'; // Username CSDL
    private $password = ''; // Password CSDL
    public $conn;

    // Lấy kết nối CSDL
    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            $this->conn->set_charset("utf8mb4"); // Đặt charset
        } catch (mysqli_sql_exception $exception) {
            echo "Lỗi kết nối: " . $exception->getMessage();
        }
        return $this->conn;
    }

    // Đóng kết nối (nếu cần)
    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}