<?php
require_once 'QT_Database.php';

class QT_KeHoachModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->getConnection();
    }

    // Lấy danh sách tất cả phiếu + tên người lập
    public function getAll()
    {
        $sql = "
            SELECT k.*, nd.hoTen AS tenNguoiLap
            FROM KeHoachMuaSam k
            JOIN NguoiDung nd ON k.nguoiLap = nd.maND
            ORDER BY k.maMS DESC
        ";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Lấy thông tin 1 phiếu
    public function getById($maMS)
    {
        $stmt = $this->conn->prepare("SELECT * FROM KeHoachMuaSam WHERE maMS = ?");
        $stmt->bind_param("i", $maMS);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Tạo phiếu mới
    public function create($maND)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO KeHoachMuaSam (ngayLap, trangThai, nguoiLap)
            VALUES (CURDATE(), 'Chờ duyệt', ?)
        ");
        $stmt->bind_param("i", $maND);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Lấy chi tiết phiếu
    public function getChiTiet($maMS)
    {
        $stmt = $this->conn->prepare("
            SELECT ct.maTB, ct.soLuong, tb.tenTB, tb.donVi
            FROM ChiTietMuaSam ct
            JOIN ThietBi tb ON tb.maTB = ct.maTB
            WHERE ct.maKH = ?
        ");
        $stmt->bind_param("i", $maMS);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Xóa chi tiết cũ
    public function clearChiTiet($maMS)
    {
        $stmt = $this->conn->prepare("DELETE FROM ChiTietMuaSam WHERE maKH = ?");
        $stmt->bind_param("i", $maMS);
        $stmt->execute();
    }

    // Thêm 1 dòng chi tiết
    public function addChiTiet($maMS, $maTB, $soLuong)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO ChiTietMuaSam (maKH, maTB, soLuong)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iii", $maMS, $maTB, $soLuong);
        $stmt->execute();
    }

    // Xóa phiếu (chỉ dùng khi không có chi tiết)
    public function delete($maMS)
    {
        $this->conn->query("DELETE FROM ChiTietMuaSam WHERE maKH = " . (int)$maMS);
        $this->conn->query("DELETE FROM KeHoachMuaSam WHERE maMS = " . (int)$maMS);
    }

    // Lấy tên người duyệt (nếu có)
    public function getTenNguoiDung($maND)
    {
        if (!$maND) return null;
        $stmt = $this->conn->prepare("SELECT hoTen FROM NguoiDung WHERE maND = ?");
        $stmt->bind_param("i", $maND);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['hoTen'] : null;
    }
}
