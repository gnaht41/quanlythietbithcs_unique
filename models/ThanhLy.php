<?php
// models/ThanhLy.php
class ThanhLy {
    private mysqli $conn;

    public function __construct(mysqli $db) {
        $this->conn = $db;
    }

    /** Danh sách đề xuất thanh lý (gộp thiết bị, lấy 1 lý do đại diện từ chi tiết) */
    public function getAll(): mysqli_result|false {
        // Đảm bảo có cột trangThai (chạy 1 lần, bỏ qua nếu đã có)
        @$this->conn->query("ALTER TABLE kehoachthanhly ADD COLUMN IF NOT EXISTS trangThai VARCHAR(50) DEFAULT 'Chờ duyệt'");

        $sql = "
            SELECT 
                tl.maTL,
                COALESCE(tl.trangThai, 'Chờ duyệt') AS trangThai,
                MIN(ct.lyDo) AS lyDo, /* lấy 1 lý do đại diện từ chi tiết */
                GROUP_CONCAT(DISTINCT tb.tenTB ORDER BY tb.tenTB SEPARATOR ', ') AS dsTB
            FROM kehoachthanhly tl
            LEFT JOIN chitietthanhly ct ON ct.maTL = tl.maTL
            LEFT JOIN thietbi tb ON tb.maTB = ct.maTB
            GROUP BY tl.maTL, tl.trangThai
            ORDER BY tl.maTL DESC
        ";
        return $this->conn->query($sql);
    }

    /** Chi tiết 1 đề xuất: lấy 100% từ bảng chitietthanhly */
    public function getDetail(int $maTL): array {
        $sql = "
            SELECT 
                tl.maTL,
                tb.maTB,
                tb.tenTB,
                tb.soLuong,
                tb.tinhTrang,
                ct.lyDo AS lyDoItem
            FROM kehoachthanhly tl
            JOIN chitietthanhly ct ON ct.maTL = tl.maTL
            JOIN thietbi tb        ON tb.maTB = ct.maTB
            WHERE tl.maTL = ?
            ORDER BY tb.tenTB
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maTL);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $stmt->close();
        return $rows;
    }

    /** Cập nhật trạng thái duyệt */
    public function updateStatus(int $maTL, string $trangThai, ?string $ghiChu): bool {
        @$this->conn->query("ALTER TABLE kehoachthanhly ADD COLUMN IF NOT EXISTS trangThai VARCHAR(50) DEFAULT 'Chờ duyệt'");
        $sql = "UPDATE kehoachthanhly SET trangThai = ?, ghiChu = ? WHERE maTL = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $trangThai, $ghiChu, $maTL);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Ghi log hệ thống */
    public function writeLog(string $maND, string $hanhDong, string $doiTuong): void {
        $sql = "INSERT INTO bangghilog (thoiGian, nguoiDung, hanhDong, doiTuong) VALUES (NOW(), ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $maND, $hanhDong, $doiTuong);
        $stmt->execute();
        $stmt->close();
    }
}
