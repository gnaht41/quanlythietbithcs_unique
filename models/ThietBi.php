<?php
// File: models/ThietBi.php
require_once 'Database.php';

class ThietBi
{
    private $conn;
    private $table_name = "ThietBi";
    private $monhoc_table = "MonHoc"; // Thêm tên bảng Môn học

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Hàm tìm kiếm thiết bị với các bộ lọc
    public function searchDevices($filters = [])
    {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Lỗi kết nối CSDL.', 'data' => []];
        }

        // Base query - JOIN với bảng MonHoc
        $query = "SELECT t.maTB, t.tenTB, m.tenMonHoc, t.donVi, t.soLuong, t.lop, t.tinhTrang
                  FROM " . $this->table_name . " t
                  LEFT JOIN " . $this->monhoc_table . " m ON t.maMH = m.maMH"; // Sử dụng LEFT JOIN để vẫn hiển thị TB nếu không có môn học

        $whereClauses = [];
        $params = [];
        $types = "";

        // Thêm điều kiện lọc dựa trên input
        if (!empty($filters['tenTB'])) {
            $whereClauses[] = "t.tenTB LIKE ?"; // Thêm alias 't.'
            $params[] = "%" . $filters['tenTB'] . "%";
            $types .= "s";
        }
        if (!empty($filters['maMH'])) { // Lọc theo mã môn học
            $whereClauses[] = "t.maMH = ?"; // Thêm alias 't.'
            $params[] = $filters['maMH'];
            $types .= "i"; // 'i' for integer
        }
        if (!empty($filters['tinhTrang'])) {
            $whereClauses[] = "t.tinhTrang = ?"; // Thêm alias 't.'
            $params[] = $filters['tinhTrang'];
            $types .= "s";
        }
        if (!empty($filters['lop'])) {
            // Cẩn thận khi dùng LIKE với số nguyên, có thể không hiệu quả
            // Nếu cột lop chỉ lưu 1 số, nên cân nhắc dùng = ?
            $whereClauses[] = "t.lop LIKE ?"; // Thêm alias 't.'
            $params[] = "%" . $filters['lop'] . "%";
            $types .= "s"; // Dùng 's' vì LIKE thường dùng với string
        }


        // Gắn các điều kiện WHERE nếu có
        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $query .= " ORDER BY t.maTB ASC"; // Sắp xếp theo ID thiết bị

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            error_log("Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error);
            return ['success' => false, 'message' => 'Lỗi chuẩn bị câu lệnh: ' . $this->conn->error, 'data' => []];
        }

        // Bind parameters nếu có
        if (!empty($params)) {
            if (!$stmt->bind_param($types, ...$params)) {
                error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
                $stmt->close();
                return ['success' => false, 'message' => 'Lỗi gắn tham số: ' . $stmt->error, 'data' => []];
            }
        }

        // Execute
        if (!$stmt->execute()) {
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            $stmt->close();
            return ['success' => false, 'message' => 'Lỗi thực thi câu lệnh: ' . $stmt->error, 'data' => []];
        }

        $result = $stmt->get_result();
        $devices = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $devices[] = $row;
            }
        }

        $stmt->close();
        return ['success' => true, 'message' => 'Tìm thấy ' . count($devices) . ' thiết bị.', 'data' => $devices];
    }

    // Hàm lấy danh sách Môn học (thay cho nhóm)
    public function getMonHoc()
    {
        if (!$this->conn) return [];
        $query = "SELECT maMH, tenMonHoc FROM " . $this->monhoc_table . " ORDER BY tenMonHoc ASC";
        $result = $this->conn->query($query);
        $monHocs = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $monHocs[] = $row; // Trả về cả maMH và tenMonHoc
            }
        }
        return $monHocs;
    }

    // Hàm lấy danh sách Tình trạng (có thể query từ DB nếu cần)
    public function getTinhTrang()
    {
        // Ví dụ lấy DISTINCT từ bảng ThietBi
        if (!$this->conn) return ['Tốt', 'Đang sửa', 'Hỏng']; // Giá trị mặc định nếu lỗi DB
        $query = "SELECT DISTINCT tinhTrang FROM " . $this->table_name . " WHERE tinhTrang IS NOT NULL AND tinhTrang != '' ORDER BY tinhTrang ASC";
        $result = $this->conn->query($query);
        $tinhTrangs = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tinhTrangs[] = $row['tinhTrang'];
            }
        }
        // Nếu query không trả về gì, dùng giá trị mặc định
        return !empty($tinhTrangs) ? $tinhTrangs : ['Tốt', 'Bình thường', 'Cần bảo trì', 'Hỏng'];
    }
}
