<?php
// models/QT_Log.php
require_once 'QT_Database.php';

class Log
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function ghiLog($maND, $hanhDong, $doiTuong, $doiTuongId = null, $ghiChu = '')
    {
        if (!$this->conn) return false;

        $sql = "INSERT INTO BangGhiLog
                (maND, hanhDong, doiTuong, doiTuongId, ghiChu)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return false;

        $stmt->bind_param(
            "issis",
            $maND,
            $hanhDong,
            $doiTuong,
            $doiTuongId,
            $ghiChu
        );

        $stmt->execute();
        $stmt->close();
        return true;
    }
    public function getAllLogs()
    {
        if (!$this->conn) return [];

        $sql = "
        SELECT 
            l.thoiGian,
            nd.hoTen,
            l.hanhDong,
            l.doiTuong,
            l.ghiChu
        FROM BangGhiLog l
        LEFT JOIN NguoiDung nd ON l.maND = nd.maND
        ORDER BY l.thoiGian DESC
    ";

        $result = $this->conn->query($sql);
        $logs = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
        }
        return $logs;
    }
}