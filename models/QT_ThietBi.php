<?php
// models/QT_ThietBi.php
require_once 'QT_Database.php';

class ThietBi
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy danh sách môn học (cho combobox)
    public function getAllMonHoc()
    {
        $sql = "SELECT maMH, tenMonHoc FROM MonHoc";
        $result = $this->conn->query($sql);

        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Tìm kiếm / tra cứu thiết bị
    public function search($filters = [])
    {
        $sql = "
            SELECT 
                tb.maTB,
                tb.tenTB,
                tb.donVi,
                tb.lop,
                tb.soLuongTong,
                tb.soLuongKhaDung,
                tb.tinhTrang,
                mh.tenMonHoc
            FROM ThietBi tb
            LEFT JOIN MonHoc mh ON tb.maMH = mh.maMH
            WHERE tb.isHidden = 0
        ";

        $params = [];
        $types  = "";

        if (!empty($filters['tenTB'])) {
            $sql .= " AND tb.tenTB LIKE ? ";
            $params[] = '%' . $filters['tenTB'] . '%';
            $types .= "s";
        }

        if (!empty($filters['lop'])) {
            $sql .= " AND tb.lop = ? ";
            $params[] = $filters['lop'];
            $types .= "s";
        }

        if (!empty($filters['maMH'])) {
            $sql .= " AND tb.maMH = ? ";
            $params[] = (int)$filters['maMH'];
            $types .= "i";
        }

        $sql .= " ORDER BY tb.tenTB ASC";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Lỗi SQL: " . $this->conn->error);
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
}