<?php
class CT_BaoCaoHuHongModel
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/QT_Database.php';
        $this->db = new Database();
    }

    public function layThietBiDangMuon()
    {
        $conn = $this->db->getConnection();
        if (!$conn) return [];

        // Lấy thiết bị từ các phiếu mượn đang có trạng thái "Đang mượn"
        $sql = "SELECT 
                    pm.maPhieu,
                    tb.maTB,
                    tb.tenTB,
                    ct.soLuong,
                    pm.ngayMuon,
                    pm.trangThai
                FROM phieumuon pm
                INNER JOIN chitietphieumuon ct ON pm.maPhieu = ct.maPhieu
                INNER JOIN thietbi tb ON ct.maTB = tb.maTB
                WHERE pm.trangThai = 'Đang mượn'
                ORDER BY pm.ngayMuon DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'maPhieu' => 'PM' . str_pad($row['maPhieu'], 6, '0', STR_PAD_LEFT),
                'maTB' => $row['maTB'],
                'tenTB' => $row['tenTB'],
                'soLuong' => $row['soLuong'],
                'thoiGianTao' => $row['ngayMuon'],
                'trangThai' => $row['trangThai']
            ];
        }

        return $data;
    }

    public function taoBaoCao($data)
    {
        if (empty($data['maTB']) || empty($data['tenTB']) || empty($data['tinhTrang']) || empty($data['noiDungBaoCao'])) {
            throw new Exception('Thiếu thông tin bắt buộc');
        }

        $conn = $this->db->getConnection();
        if (!$conn) {
            throw new Exception('Không thể kết nối database');
        }

        $conn->begin_transaction();

        try {
            if (!isset($_SESSION)) {
                session_start();
            }
            $maND = $_SESSION['maND'] ?? 1;

            // Parse maPhieu từ format "PM000001" về số nguyên
            $maPhieu = $data['maPhieu'] ?? '';
            if (preg_match('/PM(\d+)/', $maPhieu, $matches)) {
                $maPhieuInt = (int)$matches[1];
            } else {
                $maPhieuInt = 0;
            }

            // Lấy maTB từ số hoặc tìm theo tên
            $maTB = $data['maTB'];
            if (!is_numeric($maTB)) {
                // Nếu maTB không phải số, tìm theo tên
                $sqlFind = "SELECT maTB FROM thietbi WHERE tenTB LIKE ? LIMIT 1";
                $stmtFind = $conn->prepare($sqlFind);
                $searchTerm = '%' . $data['tenTB'] . '%';
                $stmtFind->bind_param('s', $searchTerm);
                $stmtFind->execute();
                $resultFind = $stmtFind->get_result();

                if ($rowTB = $resultFind->fetch_assoc()) {
                    $maTB = $rowTB['maTB'];
                } else {
                    throw new Exception('Không tìm thấy thiết bị');
                }
            }

            // Insert vào bảng baocaohuhong
            $sql = "INSERT INTO baocaohuhong (maPhieu, maTB, tinhTrang, noiDungBaoCao, trangThai) 
                    VALUES (?, ?, ?, ?, 'dang-xu-ly')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $maPhieuInt, $maTB, $data['tinhTrang'], $data['noiDungBaoCao']);
            $stmt->execute();

            $maBaoCao = $conn->insert_id;

            // Ghi log
            require_once __DIR__ . '/QT_Log.php';
            $log = new Log();
            $log->ghiLog($maND, 'INSERT', 'BaoCaoHuHong', $maBaoCao);

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Lỗi tạo báo cáo: " . $e->getMessage());
            throw $e;
        }
    }

    public function layDanhSach()
    {
        $conn = $this->db->getConnection();
        if (!$conn) return [];

        $sql = "SELECT 
                    bc.maBaoCao,
                    bc.maPhieu,
                    bc.maTB,
                    tb.tenTB,
                    bc.tinhTrang,
                    bc.noiDungBaoCao,
                    bc.ngayBaoCao,
                    bc.trangThai
                FROM baocaohuhong bc
                LEFT JOIN thietbi tb ON bc.maTB = tb.maTB
                ORDER BY bc.ngayBaoCao DESC
                LIMIT 50";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'maBaoCao' => $row['maBaoCao'],
                'maTB' => $row['maTB'],
                'tenTB' => $row['tenTB'] ?? 'Không rõ',
                'tinhTrang' => $row['tinhTrang'],
                'noiDungBaoCao' => $row['noiDungBaoCao'],
                'ngayBaoCao' => $row['ngayBaoCao'],
                'trangThai' => $this->convertTrangThai($row['trangThai'])
            ];
        }

        return $data;
    }

    public function layChiTiet($id)
    {
        $conn = $this->db->getConnection();
        if (!$conn) return null;

        $sql = "SELECT 
                    bc.maBaoCao,
                    bc.maPhieu,
                    bc.maTB,
                    tb.tenTB,
                    bc.tinhTrang,
                    bc.noiDungBaoCao,
                    bc.ngayBaoCao,
                    bc.trangThai
                FROM baocaohuhong bc
                LEFT JOIN thietbi tb ON bc.maTB = tb.maTB
                WHERE bc.maBaoCao = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return [
                'maBaoCao' => $row['maBaoCao'],
                'maTB' => $row['maTB'],
                'tenTB' => $row['tenTB'] ?? 'Không rõ',
                'tinhTrang' => $row['tinhTrang'],
                'noiDungBaoCao' => $row['noiDungBaoCao'],
                'ngayBaoCao' => $row['ngayBaoCao'],
                'trangThai' => $this->convertTrangThai($row['trangThai'])
            ];
        }

        return null;
    }

    public function capNhat($id, $data)
    {
        $conn = $this->db->getConnection();
        if (!$conn) return false;

        $conn->begin_transaction();

        try {
            if (!isset($_SESSION)) {
                session_start();
            }
            $maND = $_SESSION['maND'] ?? 1;

            $sql = "UPDATE baocaohuhong 
                    SET tinhTrang = ?, noiDungBaoCao = ? 
                    WHERE maBaoCao = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssi', $data['tinhTrang'], $data['noiDungBaoCao'], $id);
            $stmt->execute();

            // Ghi log
            require_once __DIR__ . '/QT_Log.php';
            $log = new Log();
            $log->ghiLog($maND, 'UPDATE', 'BaoCaoHuHong', $id);

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Lỗi cập nhật báo cáo: " . $e->getMessage());
            return false;
        }
    }

    public function xoa($id)
    {
        $conn = $this->db->getConnection();
        if (!$conn) return false;

        $conn->begin_transaction();

        try {
            if (!isset($_SESSION)) {
                session_start();
            }
            $maND = $_SESSION['maND'] ?? 1;

            $sql = "DELETE FROM baocaohuhong WHERE maBaoCao = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            $success = $stmt->affected_rows > 0;

            if ($success) {
                // Ghi log
                require_once __DIR__ . '/QT_Log.php';
                $log = new Log();
                $log->ghiLog($maND, 'DELETE', 'BaoCaoHuHong', $id);
            }

            $conn->commit();
            return $success;
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Lỗi xóa báo cáo: " . $e->getMessage());
            return false;
        }
    }

    private function convertTrangThai($trangThai)
    {
        $map = [
            'dang-xu-ly' => 'Đang xử lý',
            'da-xu-ly' => 'Đã xử lý',
            'huy-bo' => 'Hủy bỏ'
        ];
        return $map[$trangThai] ?? $trangThai;
    }
}
