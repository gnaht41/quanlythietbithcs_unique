<?php
// models/KeHoach.php
class KeHoach {
    private mysqli $conn;

    public function __construct(mysqli $db) {
        $this->conn = $db;
    }

    /** Danh sách kế hoạch (gộp mô tả chi tiết để làm preview) */
    public function getAll(): mysqli_result|false {
        // Thêm cột ghiChu nếu chưa có (để lưu ghi chú duyệt/từ chối)
        @ $this->conn->query("ALTER TABLE kehoachmuasam ADD COLUMN IF NOT EXISTS ghiChu TEXT NULL");

        $sql = "
            SELECT 
                kh.maKH,
                kh.ngayLap,
                kh.noiDung,
                COALESCE(kh.trangThai, 'Đang chờ') AS trangThai,
                MIN(ct.moTa) AS moTaDaiDien,
                COUNT(ct.maCTKH) AS soMuc
            FROM kehoachmuasam kh
            LEFT JOIN chitietmuasam ct ON ct.maKH = kh.maKH
            GROUP BY kh.maKH, kh.ngayLap, kh.noiDung, kh.trangThai
            ORDER BY kh.ngayLap DESC, kh.maKH DESC
        ";
        return $this->conn->query($sql);
    }

    /** Chi tiết 1 kế hoạch – lấy 100% từ bảng chitietmuasam */
    public function getDetail(string $maKH): array {
        $sql = "
            SELECT 
                ct.maCTKH,
                ct.moTa
            FROM chitietmuasam ct
            WHERE ct.maKH = ?
            ORDER BY ct.maCTKH
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $maKH);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $stmt->close();
        return $rows;
    }

    /** Cập nhật trạng thái (Phê duyệt / Từ chối) + ghi chú */
    public function updateStatus(string $maKH, string $trangThai, ?string $ghiChu): bool {
        @ $this->conn->query("ALTER TABLE kehoachmuasam ADD COLUMN IF NOT EXISTS ghiChu TEXT NULL");
        $sql = "UPDATE kehoachmuasam SET trangThai = ?, ghiChu = ? WHERE maKH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $trangThai, $ghiChu, $maKH);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Ghi nhật ký */
    public function writeLog(string $maND, string $hanhDong, string $doiTuong): void {
        $sql = "INSERT INTO bangghilog (thoiGian, nguoiDung, hanhDong, doiTuong) VALUES (NOW(), ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $maND, $hanhDong, $doiTuong);
        $stmt->execute();
        $stmt->close();
    }
}
