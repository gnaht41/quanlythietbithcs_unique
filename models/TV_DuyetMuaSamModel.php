<?php
require_once 'QT_Database.php';
require_once 'QT_Log.php';

class TV_DuyetMuaSamModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->getConnection();
        if ($this->conn) {
            $this->conn->set_charset("utf8mb4");
        }
    }

    public function getAll()
    {
        $sql = "
            SELECT k.*, nd.hoTen AS tenNguoiLap
            FROM KeHoachMuaSam k
            LEFT JOIN NguoiDung nd ON k.nguoiLap = nd.maND
            ORDER BY k.maMS DESC
        ";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($maMS)
    {
        $stmt = $this->conn->prepare("SELECT * FROM KeHoachMuaSam WHERE maMS = ? LIMIT 1");
        $stmt->bind_param("i", $maMS);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }

    public function getChiTiet($maMS)
    {
        $stmt = $this->conn->prepare("
            SELECT c.*, t.tenTB, t.donVi
            FROM ChiTietMuaSam c
            JOIN ThietBi t ON c.maTB = t.maTB
            WHERE c.maKH = ?
            ORDER BY c.maCTMS ASC
        ");
        $stmt->bind_param("i", $maMS);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    public function getTenNguoiDung($maND)
    {
        if (!$maND) return null;
        $stmt = $this->conn->prepare("SELECT hoTen FROM NguoiDung WHERE maND = ? LIMIT 1");
        $stmt->bind_param("i", $maND);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result ? $result['hoTen'] : null;
    }

    public function duyetPhieu($maMS, $decision, $maND)
    {
        $phieu = $this->getById($maMS);
        if (!$phieu) {
            return ['success' => false, 'message' => 'Phiếu không tồn tại.'];
        }

        $newStatus = $decision === 'approve' ? 'Đã duyệt' : 'Từ chối';

        $stmt = $this->conn->prepare("UPDATE KeHoachMuaSam SET trangThai = ?, nguoiDuyet = ? WHERE maMS = ?");
        $stmt->bind_param("sii", $newStatus, $maND, $maMS);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            $log = new Log();
            $log->ghiLog($maND, 'UPDATE', 'KeHoachMuaSam', $maMS); // Ghi log là UPDATE
        }

        return [
            'success' => $success,
            'message' => $success ? 'OK' : 'Lỗi khi cập nhật.',
            'newStatus' => $newStatus
        ];
    }
}
