<?php
// models/QT_ThongKeModel.php

require_once 'QT_Database.php';

class QT_ThongKeModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->db->getConnection();
    }

    // Theo trạng thái
    public function getThongKeTrangThai()
    {
        $sql = "SELECT tinhTrang AS label, SUM(soLuongKhaDung) AS total, SUM(soLuongTong) AS tong
                FROM ThietBi
                WHERE isHidden = FALSE
                GROUP BY tinhTrang";

        return $this->executeQuery($sql);
    }

    // Theo môn học
    public function getThongKeMonHoc()
    {
        $sql = "SELECT mh.tenMonHoc AS label, SUM(tb.soLuongKhaDung) AS total, SUM(tb.soLuongTong) AS tong
                FROM MonHoc mh
                LEFT JOIN ThietBi tb ON mh.maMH = tb.maMH AND tb.isHidden = FALSE
                GROUP BY mh.maMH, mh.tenMonHoc
                ORDER BY mh.tenMonHoc";

        return $this->executeQuery($sql);
    }

    // Theo lớp
    public function getThongKeLop()
    {
        $sql = "SELECT lop AS label, SUM(soLuongKhaDung) AS total, SUM(soLuongTong) AS tong
                FROM ThietBi
                WHERE isHidden = FALSE
                GROUP BY lop
                ORDER BY lop";

        return $this->executeQuery($sql);
    }

    // Thiết bị cần thanh lý (hỏng nặng theo môn học)
    public function getThietBiHongNang()
    {
        $sql = "SELECT mh.tenMonHoc AS label, SUM(tb.soLuongTong - tb.soLuongKhaDung) AS hongNang
                FROM ThietBi tb
                LEFT JOIN MonHoc mh ON tb.maMH = mh.maMH
                WHERE tb.tinhTrang IN ('Hư nặng', 'Đang sửa') AND tb.isHidden = FALSE
                GROUP BY mh.maMH, mh.tenMonHoc";

        return $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        $data = [];
        $result = $this->db->conn->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}
