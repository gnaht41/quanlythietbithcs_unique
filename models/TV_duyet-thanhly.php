<?php
// models/TV_duyet-thanhly.php
require_once __DIR__ . '/QT_Database.php';

class TV_DuyetThanhLyModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
        if ($this->conn) $this->conn->set_charset('utf8mb4');
    }

    private function cols(string $table): array
    {
        $cols = [];
        $rs = $this->conn->query("SHOW COLUMNS FROM `$table`");
        if ($rs) {
            while ($r = $rs->fetch_assoc()) $cols[] = $r['Field'];
        }
        return $cols;
    }

    public function getDanhSach(string $keyword = '', string $status = ''): array
    {
        if (!$this->conn) return [];

        $sql = "
            SELECT
                tl.maTL, tl.ngayLap, tl.trangThai, tl.nguoiLap, tl.nguoiDuyet,
                MIN(ct.lyDo) AS lyDoThanhLy
            FROM kehoachthanhly tl
            LEFT JOIN chitietthanhly ct ON ct.maTL = tl.maTL
            LEFT JOIN thietbi tb ON tb.maTB = ct.maTB
            WHERE 1=1
        ";

        $types = "";
        $params = [];

        $keyword = trim($keyword);
        if ($keyword !== '') {
            if (ctype_digit($keyword)) {
                $sql .= " AND tl.maTL = ? ";
                $types .= "i";
                $params[] = (int)$keyword;
            } else {
                $sql .= " AND (ct.lyDo LIKE ? OR tb.tenTB LIKE ?) ";
                $types .= "ss";
                $like = "%".$keyword."%";
                $params[] = $like;
                $params[] = $like;
            }
        }

        $status = trim($status);
        if ($status !== '') {
            $sql .= " AND tl.trangThai = ? ";
            $types .= "s";
            $params[] = $status;
        }

        $sql .= " GROUP BY tl.maTL ORDER BY tl.maTL DESC ";

        $stmt = $this->conn->prepare($sql);
        if ($types !== "") $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function getChiTiet(int $maTL): array
    {
        if (!$this->conn) return ['header' => null, 'items' => [], 'lyDoTong' => ''];

        $stmt = $this->conn->prepare("SELECT maTL, ngayLap, trangThai, nguoiLap, nguoiDuyet FROM kehoachthanhly WHERE maTL=? LIMIT 1");
        $stmt->bind_param("i", $maTL);
        $stmt->execute();
        $header = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$header) return ['header' => null, 'items' => [], 'lyDoTong' => ''];

        $ctCols = $this->cols('chitietthanhly');
        $qty = in_array('soLuong', $ctCols) ? 'soLuong' : (in_array('SL', $ctCols) ? 'SL' : 'soLuong');
        $hasTinhTrang = in_array('tinhTrang', $ctCols);
        $hasLyDo = in_array('lyDo', $ctCols);
        $order = in_array('maCTTL', $ctCols) ? " ORDER BY ct.maCTTL ASC" : "";

        $sql = "SELECT
                    ct.maTB,
                    ct.$qty AS soLuong,".
                    ($hasTinhTrang ? " ct.tinhTrang," : " '' AS tinhTrang,").
                    ($hasLyDo ? " ct.lyDo," : " '' AS lyDo,").
                " tb.tenTB
                FROM chitietthanhly ct
                INNER JOIN thietbi tb ON tb.maTB = ct.maTB
                WHERE ct.maTL = ?".$order;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maTL);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $reasons = [];
        foreach ($items as $it) {
            $r = trim((string)($it['lyDo'] ?? ''));
            if ($r !== '' && !in_array($r, $reasons, true)) $reasons[] = $r;
        }
        $lyDoTong = $reasons ? implode('; ', $reasons) : '-';

        return ['header' => $header, 'items' => $items, 'lyDoTong' => $lyDoTong];
    }

    public function capNhatTrangThai(int $maTL, string $newStatus, int $nguoiDuyet): bool
    {
        if (!$this->conn) return false;

        $stmt = $this->conn->prepare("UPDATE kehoachthanhly SET trangThai=?, nguoiDuyet=? WHERE maTL=?");
        $stmt->bind_param("sii", $newStatus, $nguoiDuyet, $maTL);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function ghiLog(int $maND, string $hanhDong, string $doiTuong, int $doiTuongId): bool
    {
        if (!$this->conn) return false;

        $stmt = $this->conn->prepare(
            "INSERT INTO bangghilog (thoiGian, maND, hanhDong, doiTuong, doiTuongId)
             VALUES (NOW(), ?, ?, ?, ?)"
        );
        $stmt->bind_param("issi", $maND, $hanhDong, $doiTuong, $doiTuongId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
