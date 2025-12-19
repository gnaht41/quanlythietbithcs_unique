<?php
// models/CT_MucDichMuonModel.php
require_once 'QT_Database.php';

class CT_MucDichMuonModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy danh sách mục đích thường dùng
    public function layDanhSachMucDich($limit = 10)
    {
        $sql = "
            SELECT 
                maMucDich,
                tenMucDich,
                moTa,
                soLanSuDung
            FROM MucDichMuon 
            WHERE trangThai = 'Hoạt động'
            ORDER BY soLanSuDung DESC, tenMucDich ASC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return ['success' => true, 'data' => $data];
    }

    // Tìm kiếm mục đích theo từ khóa
    public function timKiemMucDich($keyword, $limit = 5)
    {
        $keyword = '%' . $keyword . '%';
        $sql = "
            SELECT 
                maMucDich,
                tenMucDich,
                moTa,
                soLanSuDung
            FROM MucDichMuon 
            WHERE trangThai = 'Hoạt động' 
            AND (tenMucDich LIKE ? OR moTa LIKE ?)
            ORDER BY soLanSuDung DESC, tenMucDich ASC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssi', $keyword, $keyword, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return ['success' => true, 'data' => $data];
    }

    // Thêm mục đích mới (nếu chưa tồn tại)
    public function themMucDichMoi($tenMucDich, $moTa = '')
    {
        // Kiểm tra xem mục đích đã tồn tại chưa
        $checkSql = "SELECT maMucDich FROM MucDichMuon WHERE tenMucDich = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bind_param('s', $tenMucDich);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            return ['success' => false, 'message' => 'Mục đích này đã tồn tại'];
        }

        // Thêm mục đích mới
        $insertSql = "INSERT INTO MucDichMuon (tenMucDich, moTa) VALUES (?, ?)";
        $insertStmt = $this->conn->prepare($insertSql);
        $insertStmt->bind_param('ss', $tenMucDich, $moTa);

        if ($insertStmt->execute()) {
            return [
                'success' => true, 
                'message' => 'Thêm mục đích thành công',
                'data' => ['maMucDich' => $this->conn->insert_id]
            ];
        }

        return ['success' => false, 'message' => 'Lỗi khi thêm mục đích'];
    }

    // Cập nhật số lần sử dụng
    public function capNhatSoLanSuDung($tenMucDich)
    {
        $sql = "UPDATE MucDichMuon SET soLanSuDung = soLanSuDung + 1 WHERE tenMucDich = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $tenMucDich);
        return $stmt->execute();
    }

    // Lấy thống kê mục đích được sử dụng nhiều nhất
    public function layThongKeMucDich($limit = 5)
    {
        $sql = "
            SELECT 
                tenMucDich,
                soLanSuDung,
                ROUND((soLanSuDung * 100.0 / (SELECT SUM(soLanSuDung) FROM MucDichMuon WHERE trangThai = 'Hoạt động')), 1) as tiLe
            FROM MucDichMuon 
            WHERE trangThai = 'Hoạt động' AND soLanSuDung > 0
            ORDER BY soLanSuDung DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return ['success' => true, 'data' => $data];
    }
}
?>