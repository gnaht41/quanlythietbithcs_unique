<?php
// models/PhieuMuonModel.php
require_once 'QT_Database.php';

class PhieuMuonModel
{
    private $conn;
    public function getDanhSachLoc($keyword, $trangThai, $tuNgay, $denNgay)
{
    $sql = "
        SELECT pm.*, nd.hoTen
        FROM phieumuon pm
        JOIN nguoidung nd ON pm.maND = nd.maND
        WHERE 1=1
    ";

    $params = [];
    $types  = "";

    // Lọc theo tên người mượn
    if (!empty($keyword)) {
        $sql .= " AND nd.hoTen LIKE ? ";
        $params[] = "%$keyword%";
        $types .= "s";
    }

    // Lọc theo trạng thái
    if (!empty($trangThai)) {
        $sql .= " AND pm.trangThai = ? ";
        $params[] = $trangThai;
        $types .= "s";
    }

    // Lọc từ ngày
    if (!empty($tuNgay)) {
        $sql .= " AND pm.ngayMuon >= ? ";
        $params[] = $tuNgay;
        $types .= "s";
    }

    // Lọc đến ngày
    if (!empty($denNgay)) {
        $sql .= " AND pm.ngayMuon <= ? ";
        $params[] = $denNgay;
        $types .= "s";
    }

    $sql .= " ORDER BY pm.maPhieu ASC";

    $stmt = $this->conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return $stmt->get_result();
}
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }
    /* ==============================
   UPDATE TÌNH TRẠNG KHI TRẢ
   ============================== */
    public function updateTinhTrangKhiTra($maPhieu, $maTB, $tinhTrang)
    {
        $sql = "
            UPDATE chitietphieumuon
            SET tinhTrangKhiTra = ?
            WHERE maPhieu = ? AND maTB = ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $tinhTrang, $maPhieu, $maTB);
        return $stmt->execute();
    }

    /* ==============================
       LẤY DANH SÁCH PHIẾU MƯỢN
       ============================== */
    public function getAll()
    {
        $sql = "
            SELECT pm.*, nd.hoTen
            FROM phieumuon pm
            JOIN nguoidung nd ON pm.maND = nd.maND
            ORDER BY pm.maPhieu ASC
        ";
        return $this->conn->query($sql);
    }

    /* ==============================
       LẤY PHIẾU MƯỢN THEO ID
       ============================== */
   public function getById($maPhieu)
{
    $sql = "
        SELECT pm.*, nd.hoTen
        FROM phieumuon pm
        JOIN nguoidung nd ON pm.maND = nd.maND
        WHERE pm.maPhieu = ?
    ";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $maPhieu);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


    /* ==============================
       LẤY CHI TIẾT PHIẾU MƯỢN
       ============================== */
    public function getChiTiet($maPhieu)
    {
        $sql = "
            SELECT 
            ct.*, 
            tb.*
            FROM chitietphieumuon ct
            JOIN thietbi tb ON ct.maTB = tb.maTB
            WHERE ct.maPhieu = ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maPhieu);
        $stmt->execute();
        return $stmt->get_result();
    }

    /* ==============================
       THÊM PHIẾU MƯỢN
       ============================== */
    public function insert($ngayMuon, $ngayTraDuKien, $maND)
    {
        $sql = "
            INSERT INTO phieumuon (ngayMuon, ngayTraDuKien, trangThai, maND)
            VALUES (?, ?, 'Chờ duyệt', ?)
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $ngayMuon, $ngayTraDuKien, $maND);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    /* ==============================
       THÊM CHI TIẾT PHIẾU MƯỢN
       ============================== */
    public function insertChiTiet($maPhieu, $maTB, $soLuong)
    {
        $sql = "
            INSERT INTO chitietphieumuon (maPhieu, maTB, soLuong)
            VALUES (?, ?, ?)
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $maPhieu, $maTB, $soLuong);
        return $stmt->execute();
    }

    /* ==============================
       DUYỆT / CẬP NHẬT TRẠNG THÁI
       ============================== */
    public function updateTrangThai($maPhieu, $trangThai)
    {
        $sql = "UPDATE phieumuon SET trangThai = ? WHERE maPhieu = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $trangThai, $maPhieu);
        return $stmt->execute();
    }

    /* ==============================
       TRẢ THIẾT BỊ
       ============================== */
    public function traThietBi($maPhieu, $ngayTraThucTe)
    {
        $sql = "
            UPDATE phieumuon
            SET trangThai = 'Đã trả',
                ngayTraThucTe = ?
            WHERE maPhieu = ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $ngayTraThucTe, $maPhieu);
        return $stmt->execute();
    }

    /* ==============================
       XÓA PHIẾU MƯỢN
       ============================== */
    public function delete($maPhieu)
    {
        // Xóa chi tiết trước (FK)
        $sqlCT = "DELETE FROM chitietphieumuon WHERE maPhieu = ?";
        $stmtCT = $this->conn->prepare($sqlCT);
        $stmtCT->bind_param("i", $maPhieu);
        $stmtCT->execute();

        // Xóa phiếu
        $sql = "DELETE FROM phieumuon WHERE maPhieu = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maPhieu);
        return $stmt->execute();
    }
   public function isChoDuyet($maPhieu)
{
    $sql = "SELECT 1 FROM phieumuon 
            WHERE maPhieu = ? AND trangThai = 'Chờ duyệt'";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $maPhieu);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

/* ==============================
   DUYỆT NHIỀU PHIẾU
   ============================== */
public function duyetNhieuPhieu(array $dsMaPhieu)
{
    if (empty($dsMaPhieu)) return false;

    // Tạo ?,?,? theo số phần tử
    $placeholders = implode(',', array_fill(0, count($dsMaPhieu), '?'));
    $types = str_repeat('i', count($dsMaPhieu));

    $sql = "
        UPDATE phieumuon
        SET trangThai = 'Đã duyệt'
        WHERE maPhieu IN ($placeholders)
          AND trangThai = 'Chờ duyệt'
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param($types, ...$dsMaPhieu);
    return $stmt->execute();
}

}
