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

    public function ghiLog($maND, $hanhDong, $doiTuong, $doiTuongId = null)
    {
        if (!$this->conn) return false;

        $sql = "INSERT INTO BangGhiLog
            (maND, hanhDong, doiTuong, doiTuongId)
            VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return false;

        $doiTuongId = $doiTuongId ?? 0;

        $stmt->bind_param(
            "issi",
            $maND,
            $hanhDong,
            $doiTuong,
            $doiTuongId
        );

        if (!$stmt->execute()) {
            // debug khi cần
            // die($stmt->error);
            return false;
        }

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
            l.doiTuong
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