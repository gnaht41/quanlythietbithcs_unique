<?php
// models/TV_ket-qua-kiem-ke.php

require_once __DIR__ . '/QT_Database.php';

class TV_KetQuaKiemKeModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
        if ($this->conn) $this->conn->set_charset('utf8mb4');
    }

    private function parseMaKKFromKeyword(string $kw): ?int
    {
        $kw = trim($kw);
        if ($kw === '') return null;
        if (ctype_digit($kw)) return (int)$kw;

        // nếu bạn tìm theo dạng KK-2024-001
        if (preg_match('/KK-\d{4}-(\d{1,6})/i', $kw, $m)) {
            return (int)$m[1];
        }
        return null;
    }

    public function getDanhSachKiemKe(string $keyword = ''): array
    {
        if (!$this->conn) return [];

        // Bảng kiemke: maKK, ngayKK, loaiKiemKe, maND
        $sql = "SELECT maKK, ngayKK, loaiKiemKe, maND
                FROM kiemke
                WHERE 1=1";
        $types = "";
        $params = [];

        $maKK = $this->parseMaKKFromKeyword($keyword);
        if ($maKK !== null && $maKK > 0) {
            $sql .= " AND maKK = ?";
            $types .= "i";
            $params[] = $maKK;
        }

        $sql .= " ORDER BY maKK DESC";

        $stmt = $this->conn->prepare($sql);
        if ($types !== "") $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function getChiTietKiemKe(int $maKK): array
    {
        if (!$this->conn) return ['header' => [], 'items' => []];

        // Header
        $stmt = $this->conn->prepare("SELECT maKK, ngayKK, loaiKiemKe, maND FROM kiemke WHERE maKK = ? LIMIT 1");
        $stmt->bind_param("i", $maKK);
        $stmt->execute();
        $header = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$header) return ['header' => [], 'items' => []];

        // Detail: chitietkiemke(maTB) -> thietbi(maTB)
        $sqlItems = "SELECT
                        ct.maCTKK,
                        ct.maTB,
                        ct.soLuongTruoc,
                        ct.soLuongThucTe,
                        tb.tenTB,
                        tb.tinhTrang AS tinhTrangTB,
                        tb.soLuongTong,
                        tb.soLuongKhaDung,
                        mh.tenMonHoc
                     FROM chitietkiemke ct
                     INNER JOIN thietbi tb ON tb.maTB = ct.maTB
                     LEFT JOIN monhoc mh ON mh.maMH = tb.maMH
                     WHERE ct.maKK = ?
                     ORDER BY ct.maCTKK ASC";

        $stmt = $this->conn->prepare($sqlItems);
        $stmt->bind_param("i", $maKK);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return ['header' => $header, 'items' => $items];
    }
}
