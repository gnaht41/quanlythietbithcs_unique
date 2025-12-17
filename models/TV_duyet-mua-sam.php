<?php
// models/TV_duyet-mua-sam.php

require_once __DIR__ . '/QT_Database.php';
require_once __DIR__ . '/QT_Log.php';

class TV_DuyetMuaSamModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
        if ($this->conn) {
            $this->conn->set_charset('utf8mb4');
        }
    }

    private function hasColumn(string $table, string $column): bool
    {
        if (!$this->conn) return false;

        $sql = "SELECT 1
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND COLUMN_NAME = ?
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $table, $column);
        $stmt->execute();
        $stmt->store_result();
        $ok = $stmt->num_rows > 0;
        $stmt->close();
        return $ok;
    }

    private function parseMaMSFromKeyword(string $kw): ?int
    {
        $kw = trim($kw);
        if ($kw === '') return null;

        if (ctype_digit($kw)) return (int)$kw;

        // KHM-2024-001 -> 001
        if (preg_match('/KHM-\d{4}-(\d{1,6})/i', $kw, $m)) {
            return (int)$m[1];
        }
        return null;
    }

    public function getDanhSachKeHoach(string $keyword = '', string $trangThai = ''): array
    {
        if (!$this->conn) return [];

        $fields = ["maMS", "ngayLap", "trangThai", "nguoiLap", "nguoiDuyet"];

        if ($this->hasColumn('KeHoachMuaSam', 'namHoc'))  $fields[] = "namHoc";
        if ($this->hasColumn('KeHoachMuaSam', 'lyDo'))    $fields[] = "lyDo";
        if ($this->hasColumn('KeHoachMuaSam', 'mucDich')) $fields[] = "mucDich";

        $sql = "SELECT " . implode(',', $fields) . " FROM KeHoachMuaSam WHERE 1=1";
        $types = "";
        $params = [];

        $allowed = ['Chờ duyệt', 'Đã duyệt', 'Từ chối'];
        if (in_array($trangThai, $allowed, true)) {
            $sql .= " AND trangThai = ?";
            $types .= "s";
            $params[] = $trangThai;
        }

        $maMS = $this->parseMaMSFromKeyword($keyword);
        if ($maMS !== null && $maMS > 0) {
            $sql .= " AND maMS = ?";
            $types .= "i";
            $params[] = $maMS;
        }

        $sql .= " ORDER BY maMS DESC";

        $stmt = $this->conn->prepare($sql);
        if ($types !== "") $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function getChiTietKeHoach(int $maMS): array
    {
        if (!$this->conn) return ['header' => [], 'items' => []];

        $fields = ["maMS", "ngayLap", "trangThai", "nguoiLap", "nguoiDuyet"];
        if ($this->hasColumn('KeHoachMuaSam', 'namHoc'))  $fields[] = "namHoc";
        if ($this->hasColumn('KeHoachMuaSam', 'lyDo'))    $fields[] = "lyDo";
        if ($this->hasColumn('KeHoachMuaSam', 'mucDich')) $fields[] = "mucDich";

        $sqlHeader = "SELECT " . implode(',', $fields) . " FROM KeHoachMuaSam WHERE maMS = ? LIMIT 1";
        $stmt = $this->conn->prepare($sqlHeader);
        $stmt->bind_param("i", $maMS);
        $stmt->execute();
        $header = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$header) return ['header' => [], 'items' => []];

        // ChiTietMuaSam.maKH -> KeHoachMuaSam.maMS
        $sqlItems = "SELECT
                        ct.maCTMS,
                        ct.maTB,
                        ct.soLuong,
                        tb.tenTB,
                        mh.tenMonHoc
                     FROM ChiTietMuaSam ct
                     INNER JOIN ThietBi tb ON tb.maTB = ct.maTB
                     LEFT JOIN MonHoc mh ON mh.maMH = tb.maMH
                     WHERE ct.maKH = ?
                     ORDER BY ct.maCTMS ASC";
        $stmt = $this->conn->prepare($sqlItems);
        $stmt->bind_param("i", $maMS);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return ['header' => $header, 'items' => $items];
    }

    public function capNhatTrangThai(int $maMS, string $decision, string $ghiChu, int $maND): array
    {
        if (!$this->conn) return ['success' => false, 'message' => 'Không kết nối được CSDL.'];
        if ($maMS <= 0 || $maND <= 0) return ['success' => false, 'message' => 'Dữ liệu không hợp lệ.'];

        if (!in_array($decision, ['approve', 'reject'], true)) {
            return ['success' => false, 'message' => 'Quyết định không hợp lệ.'];
        }

        $stmt = $this->conn->prepare("SELECT trangThai FROM KeHoachMuaSam WHERE maMS = ? LIMIT 1");
        $stmt->bind_param("i", $maMS);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) return ['success' => false, 'message' => 'Không tìm thấy kế hoạch mua sắm.'];
        if (($row['trangThai'] ?? '') !== 'Chờ duyệt') {
            return ['success' => false, 'message' => 'Kế hoạch này không còn ở trạng thái "Chờ duyệt".'];
        }

        $newStatus = ($decision === 'approve') ? 'Đã duyệt' : 'Từ chối';

        $stmt = $this->conn->prepare("UPDATE KeHoachMuaSam SET trangThai = ?, nguoiDuyet = ? WHERE maMS = ?");
        $stmt->bind_param("sii", $newStatus, $maND, $maMS);
        $stmt->execute();
        $stmt->close();

        // log
        $log = new Log();
        $log->ghiLog(
            $maND,
            ($decision === 'approve') ? 'APPROVE_PURCHASE_PLAN' : 'REJECT_PURCHASE_PLAN',
            'KeHoachMuaSam',
            $maMS,
            $ghiChu
        );

        return ['success' => true, 'message' => 'Cập nhật thành công.', 'newStatus' => $newStatus];
    }
}
