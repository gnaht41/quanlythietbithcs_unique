<?php
require_once 'QT_Database.php';

class ThietBiModel
{
    private $conn;
    private $table_name = "ThietBi";
    private $monhoc_table = "MonHoc";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /********** THÊM THIẾT BỊ **********/
    public function addDevice( 
        $tenTB,
        $maMH,
        $donVi,
        $soLuongTong,
        $soLuongKhaDung,
        $tinhTrang,
        $lop = null,
        $isHidden = false)
        {
        $query = "INSERT INTO {$this->table_name} 
                  (tenTB, maMH, donVi, lop, soLuongTong, soLuongKhaDung, tinhTrang, isHidden) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi chuẩn bị câu lệnh: ' . $this->conn->error];
        }

        $isHiddenInt = $isHidden ? 1 : 0;
        $stmt->bind_param(
            "sississi",
            $tenTB,
            $maMH,
            $donVi,
            $lop,
            $soLuongTong,
            $soLuongKhaDung,
            $tinhTrang,
            $isHiddenInt
        );


        $result = $stmt->execute();
        $insertId = $stmt->insert_id;
        $stmt->close();

        return $result 
            ? ['success' => true, 'message' => 'Thêm thiết bị thành công.', 'insert_id' => $insertId]
            : ['success' => false, 'message' => 'Thêm thất bại: ' . $stmt->error];
    }

    /********** CẬP NHẬT THIẾT BỊ **********/
    public function updateDevice($maTB, $tenTB, $maMH, $donVi, $lop, $soLuongTong, $soLuongKhaDung, $tinhTrang, $isHidden = false)
    {
        $query = "UPDATE {$this->table_name} 
                  SET tenTB = ?, maMH = ?, donVi = ?, lop = ?, 
                      soLuongTong = ?, soLuongKhaDung = ?, tinhTrang = ?, isHidden = ?
                  WHERE maTB = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi chuẩn bị câu lệnh: ' . $this->conn->error];
        }

        $isHiddenInt = $isHidden ? 1 : 0;
        $stmt->bind_param("sississii", $tenTB, $maMH, $donVi, $lop, $soLuongTong, $soLuongKhaDung, $tinhTrang, $isHiddenInt, $maTB);

        $result = $stmt->execute();
        $stmt->close();

        return $result
            ? ['success' => true, 'message' => 'Cập nhật thành công.']
            : ['success' => false, 'message' => 'Cập nhật thất bại: ' . $stmt->error];
    }

    /********** XÓA THIẾT BỊ **********/
    public function deleteDevice($maTB)
    {
        // Kiểm tra xem thiết bị có đang được sử dụng trong các bảng chi tiết không
        $relatedTables = [
            'ChiTietPhieuMuon' => 'maTB',
            'ChiTietMuaSam' => 'maTB',
            'ChiTietThanhLy' => 'maTB'
        ];

        foreach ($relatedTables as $table => $field) {
            $checkQuery = "SELECT COUNT(*) as count FROM $table WHERE $field = ?";
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bind_param("i", $maTB);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($res['count'] > 0) {
                return ['success' => false, 'message' => "Không thể xóa: Thiết bị đang được sử dụng trong $table."];
            }
        }

        // Nếu không có ràng buộc, tiến hành xóa
        $stmt = $this->conn->prepare("DELETE FROM {$this->table_name} WHERE maTB = ?");
        $stmt->bind_param("i", $maTB);
        $result = $stmt->execute();
        $stmt->close();

        return $result
            ? ['success' => true, 'message' => 'Xóa thiết bị thành công.']
            : ['success' => false, 'message' => 'Xóa thất bại (có thể do ràng buộc khóa ngoại).'];
    }

    /********** LẤY THIẾT BỊ THEO ID **********/
    public function getDeviceById($maTB)
    {
        $query = "SELECT t.*, m.tenMonHoc 
                  FROM {$this->table_name} t
                  LEFT JOIN {$this->monhoc_table} m ON t.maMH = m.maMH
                  WHERE t.maTB = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $maTB);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $result ?: null;
    }

    /********** TÌM KIẾM THIẾT BỊ **********/
    public function searchDevices($filters = [])
    {
        $query = "SELECT t.maTB, t.tenTB, t.donVi, t.lop, t.soLuongTong, t.soLuongKhaDung, 
                         t.tinhTrang, t.isHidden, m.tenMonHoc
                  FROM {$this->table_name} t
                  LEFT JOIN {$this->monhoc_table} m ON t.maMH = m.maMH
                  WHERE t.isHidden = FALSE"; // Chỉ hiển thị thiết bị không bị ẩn

        $whereClauses = [];
        $params = [];
        $types = "";

        if (!empty($filters['tenTB'])) {
            $whereClauses[] = "t.tenTB LIKE ?";
            $params[] = "%" . $filters['tenTB'] . "%";
            $types .= "s";
        }
        if (!empty($filters['maMH'])) {
            $whereClauses[] = "t.maMH = ?";
            $params[] = $filters['maMH'];
            $types .= "i";
        }
        if (!empty($filters['tinhTrang'])) {
            $whereClauses[] = "t.tinhTrang = ?";
            $params[] = $filters['tinhTrang'];
            $types .= "s";
        }
        if (!empty($filters['lop'])) {
            $whereClauses[] = "t.lop = ?";
            $params[] = $filters['lop'];
            $types .= "s";
        }

        if (!empty($whereClauses)) {
            $query .= " AND " . implode(" AND ", $whereClauses);
        }

        $query .= " ORDER BY t.maTB ASC";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi chuẩn bị truy vấn.', 'data' => []];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $devices = [];
        while ($row = $result->fetch_assoc()) {
            $devices[] = $row;
        }
        $stmt->close();

        return ['success' => true, 'data' => $devices];
    }

    /********** DANH SÁCH MÔN HỌC **********/
    public function getMonHoc()
    {
        $query = "SELECT maMH, tenMonHoc FROM {$this->monhoc_table} ORDER BY tenMonHoc ASC";
        $result = $this->conn->query($query);

        $monHocs = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $monHocs[] = $row;
            }
        }
        return $monHocs;
    }

    /********** DANH SÁCH TÌNH TRẠNG (THEO ENUM TRONG CSDL) **********/
    public function getTinhTrangList()
    {
        // Danh sách cố định theo ENUM trong CSDL
        return ['Tốt', 'Hư nhẹ', 'Hư nặng', 'Đang sửa'];
    }
}