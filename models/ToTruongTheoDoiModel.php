<?php
// models/ToTruongTheoDoiModel.php
require_once __DIR__ . '/Database.php';

class ToTruongTheoDoiModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getDanhSachMonHoc()
    {
        $out = [];
        $sql = "SELECT maMH, tenMonHoc FROM MonHoc ORDER BY tenMonHoc ASC";
        if ($res = $this->conn->query($sql)) {
            while ($row = $res->fetch_assoc()) $out[] = $row;
            $res->free();
        }
        return $out;
    }

    /**
     * Trả về mảng các thiết bị với các khóa:
     * maTB, tenTB, tenMonHoc, soLuongTong, soLuongThucTe, chenhLech, tinhTrang, ghiChu
     */
    public function getDanhSachTheoDoi($maMH = null)
    {
        $params = "";
        if (!empty($maMH)) {
            $params = " AND tb.maMH = " . intval($maMH);
        }

        $sql = "
            SELECT
                tb.maTB,
                tb.tenTB,
                tb.soLuong AS soLuongTong,
                tb.tinhTrang,
                tb.ghiChu,
                mh.tenMonHoc,
                (
                    SELECT ct.soLuongThucTe
                    FROM ChiTietKiemKe ct
                    JOIN KiemKe kk ON kk.maKK = ct.maKK
                    WHERE ct.maTB = tb.maTB
                    ORDER BY kk.ngayKK DESC, kk.maKK DESC
                    LIMIT 1
                ) AS soLuongThucTe
            FROM ThietBi tb
            LEFT JOIN MonHoc mh ON mh.maMH = tb.maMH
            WHERE 1=1
            {$params}
            ORDER BY mh.tenMonHoc, tb.tenTB
            LIMIT 1000
        ";

        $out = [];
        if ($res = $this->conn->query($sql)) {
            while ($row = $res->fetch_assoc()) {
                $tong = isset($row['soLuongTong']) ? (int)$row['soLuongTong'] : 0;
                $thucte = isset($row['soLuongThucTe']) && $row['soLuongThucTe'] !== null ? (int)$row['soLuongThucTe'] : $tong;
                $chenh = $thucte - $tong;

                $out[] = [
                    'maTB' => $row['maTB'] ?? null,
                    'tenTB' => $row['tenTB'] ?? '',
                    'tenMonHoc' => $row['tenMonHoc'] ?? '',
                    'soLuongTong' => $tong,
                    'soLuongThucTe' => $thucte,
                    'chenhLech' => $chenh,
                    'tinhTrang' => $row['tinhTrang'] ?? '',
                    'ghiChu' => $row['ghiChu'] ?? ''
                ];
            }
            $res->free();
        }
        return $out;
    }
}
