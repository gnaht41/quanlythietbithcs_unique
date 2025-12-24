<?php
class CT_PhieuMuonModel
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/QT_Database.php';
        $this->db = new Database();
    }

    public function taoPhieu($maND, $ngayMuon, $ngayTra, $mucDich, $thietBi)
    {
        $conn = $this->db->getConnection();
        if (!$conn) return false;

        // Bắt đầu transaction
        $conn->begin_transaction();

        try {
            // 1. Tạo phiếu mượn trong bảng phieumuon
            $sql = "INSERT INTO phieumuon (ngayMuon, ngayTraDuKien, trangThai, maND) VALUES (?, ?, 'Chờ duyệt', ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssi', $ngayMuon, $ngayTra, $maND);
            $stmt->execute();

            $maPhieu = $conn->insert_id;

            // 2. Thêm chi tiết thiết bị vào chitietphieumuon
            $sqlDetail = "INSERT INTO chitietphieumuon (maPhieu, maTB, soLuong) VALUES (?, ?, ?)";
            $stmtDetail = $conn->prepare($sqlDetail);

            foreach ($thietBi as $tb) {
                // Parse: "Máy tính Dell SL:2" hoặc chỉ tên thiết bị
                $soLuong = 1;
                $tenTB = $tb;

                if (preg_match('/(.+)\s+SL:(\d+)/', $tb, $matches)) {
                    $tenTB = trim($matches[1]);
                    $soLuong = (int)$matches[2];
                }

                // Tìm maTB từ tên thiết bị
                $sqlFind = "SELECT maTB FROM thietbi WHERE tenTB LIKE ? LIMIT 1";
                $stmtFind = $conn->prepare($sqlFind);
                $searchTerm = '%' . $tenTB . '%';
                $stmtFind->bind_param('s', $searchTerm);
                $stmtFind->execute();
                $resultFind = $stmtFind->get_result();

                if ($rowTB = $resultFind->fetch_assoc()) {
                    $maTB = $rowTB['maTB'];
                    $stmtDetail->bind_param('iii', $maPhieu, $maTB, $soLuong);
                    $stmtDetail->execute();
                }
            }

            // 3. Ghi log vào bảng bangghilog
            require_once __DIR__ . '/QT_Log.php';
            $log = new Log();
            $log->ghiLog($maND, 'INSERT', 'PhieuMuon', $maPhieu);

            // Commit transaction
            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Lỗi tạo phiếu mượn: " . $e->getMessage());
            return false;
        }
    }

    public function layDanhSach($maND, $limit = 20)
    {
        $conn = $this->db->getConnection();
        if (!$conn) return [];

        $sql = "SELECT 
                    pm.maPhieu,
                    pm.ngayMuon,
                    pm.ngayTraDuKien,
                    pm.trangThai,
                    GROUP_CONCAT(CONCAT(tb.tenTB, ' SL:', ct.soLuong) SEPARATOR ', ') as thietBi
                FROM phieumuon pm
                LEFT JOIN chitietphieumuon ct ON pm.maPhieu = ct.maPhieu
                LEFT JOIN thietbi tb ON ct.maTB = tb.maTB
                WHERE pm.maND = ?
                GROUP BY pm.maPhieu
                ORDER BY pm.maPhieu DESC
                LIMIT ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $maND, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'id' => $row['maPhieu'],
                'ma' => 'PM' . str_pad($row['maPhieu'], 6, '0', STR_PAD_LEFT),
                'thietbi' => $row['thietBi'] ?? 'Không có thiết bị',
                'ngaymuon' => $row['ngayMuon'],
                'ngaytra' => $row['ngayTraDuKien'],
                'mucdich' => 'Mượn thiết bị',
                'trangthai' => $row['trangThai']
            ];
        }

        return $data;
    }

    public function layChiTiet($maND, $id)
    {
        $conn = $this->db->getConnection();
        if (!$conn) return null;

        $sql = "SELECT 
                    pm.maPhieu,
                    pm.ngayMuon,
                    pm.ngayTraDuKien,
                    pm.trangThai,
                    GROUP_CONCAT(CONCAT(tb.tenTB, ' SL:', ct.soLuong) SEPARATOR ', ') as thietBi
                FROM phieumuon pm
                LEFT JOIN chitietphieumuon ct ON pm.maPhieu = ct.maPhieu
                LEFT JOIN thietbi tb ON ct.maTB = tb.maTB
                WHERE pm.maPhieu = ? AND pm.maND = ?
                GROUP BY pm.maPhieu";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $id, $maND);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return [
                'id' => $row['maPhieu'],
                'ma' => 'PM' . str_pad($row['maPhieu'], 6, '0', STR_PAD_LEFT),
                'thietbi' => $row['thietBi'] ?? 'Không có thiết bị',
                'ngaymuon' => $row['ngayMuon'],
                'ngaytra' => $row['ngayTraDuKien'],
                'mucdich' => 'Mượn thiết bị',
                'trangthai' => $row['trangThai']
            ];
        }

        return null;
    }

    public function capNhat($maND, $id, $ngayMuon, $ngayTra, $mucDich, $thietBi)
    {
        $conn = $this->db->getConnection();
        if (!$conn) return false;

        $conn->begin_transaction();

        try {
            // 1. Cập nhật phiếu mượn
            $sql = "UPDATE phieumuon SET ngayMuon = ?, ngayTraDuKien = ? WHERE maPhieu = ? AND maND = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssii', $ngayMuon, $ngayTra, $id, $maND);
            $stmt->execute();

            // 2. Xóa chi tiết cũ
            $sqlDelete = "DELETE FROM chitietphieumuon WHERE maPhieu = ?";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bind_param('i', $id);
            $stmtDelete->execute();

            // 3. Thêm chi tiết mới
            $sqlDetail = "INSERT INTO chitietphieumuon (maPhieu, maTB, soLuong) VALUES (?, ?, ?)";
            $stmtDetail = $conn->prepare($sqlDetail);

            foreach ($thietBi as $tb) {
                $soLuong = 1;
                $tenTB = $tb;

                if (preg_match('/(.+)\s+SL:(\d+)/', $tb, $matches)) {
                    $tenTB = trim($matches[1]);
                    $soLuong = (int)$matches[2];
                }

                $sqlFind = "SELECT maTB FROM thietbi WHERE tenTB LIKE ? LIMIT 1";
                $stmtFind = $conn->prepare($sqlFind);
                $searchTerm = '%' . $tenTB . '%';
                $stmtFind->bind_param('s', $searchTerm);
                $stmtFind->execute();
                $resultFind = $stmtFind->get_result();

                if ($rowTB = $resultFind->fetch_assoc()) {
                    $maTB = $rowTB['maTB'];
                    $stmtDetail->bind_param('iii', $id, $maTB, $soLuong);
                    $stmtDetail->execute();
                }
            }

            // 4. Ghi log
            require_once __DIR__ . '/QT_Log.php';
            $log = new Log();
            $log->ghiLog($maND, 'UPDATE', 'PhieuMuon', $id);

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Lỗi cập nhật phiếu mượn: " . $e->getMessage());
            return false;
        }
    }

    public function xoa($maND, $id)
    {
        $conn = $this->db->getConnection();
        if (!$conn) return false;

        $conn->begin_transaction();

        try {
            // 1. Xóa chi tiết phiếu
            $sqlDetail = "DELETE FROM chitietphieumuon WHERE maPhieu = ?";
            $stmtDetail = $conn->prepare($sqlDetail);
            $stmtDetail->bind_param('i', $id);
            $stmtDetail->execute();

            // 2. Xóa phiếu mượn
            $sql = "DELETE FROM phieumuon WHERE maPhieu = ? AND maND = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $id, $maND);
            $stmt->execute();

            $success = $stmt->affected_rows > 0;

            if ($success) {
                // 3. Ghi log
                require_once __DIR__ . '/QT_Log.php';
                $log = new Log();
                $log->ghiLog($maND, 'DELETE', 'PhieuMuon', $id);
            }

            $conn->commit();
            return $success;
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Lỗi xóa phiếu mượn: " . $e->getMessage());
            return false;
        }
    }
}
