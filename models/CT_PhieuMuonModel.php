<?php
// models/CT_PhieuMuonModel.php
require_once 'QT_Database.php';

class CT_PhieuMuonModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Tạo phiếu mượn mới
    public function taoPhieuMuon($data)
    {
        try {
            // Bắt đầu transaction
            $this->conn->autocommit(false);

            // Validate dữ liệu đầu vào
            $validation = $this->validatePhieuMuon($data);
            if (!$validation['success']) {
                $this->conn->rollback();
                return $validation;
            }

            // Tạo mã phiếu mượn
            $maPhieu = $this->taoMaPhieuMuon();

            // Kiểm tra xem cột mucDich có tồn tại không
            $checkColumn = $this->conn->query("SHOW COLUMNS FROM PhieuMuon LIKE 'mucDich'");
            $hasMucDich = $checkColumn->num_rows > 0;

            // Insert vào bảng PhieuMuon
            if ($hasMucDich) {
                $sql = "INSERT INTO PhieuMuon (ngayMuon, ngayTraDuKien, trangThai, maND, mucDich) 
                        VALUES (?, ?, 'Chờ duyệt', ?, ?)";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    $this->conn->rollback();
                    return ['success' => false, 'message' => 'Lỗi chuẩn bị câu lệnh SQL'];
                }
                $stmt->bind_param('ssis', 
                    $data['ngayMuon'], 
                    $data['ngayTraDuKien'], 
                    $data['maND'], 
                    $data['mucDich']
                );
            } else {
                $sql = "INSERT INTO PhieuMuon (ngayMuon, ngayTraDuKien, trangThai, maND) 
                        VALUES (?, ?, 'Chờ duyệt', ?)";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    $this->conn->rollback();
                    return ['success' => false, 'message' => 'Lỗi chuẩn bị câu lệnh SQL'];
                }
                $stmt->bind_param('ssi', 
                    $data['ngayMuon'], 
                    $data['ngayTraDuKien'], 
                    $data['maND']
                );
            }

            if (!$stmt->execute()) {
                $this->conn->rollback();
                return ['success' => false, 'message' => 'Lỗi tạo phiếu mượn'];
            }

            $maPhieuId = $this->conn->insert_id;

            // Insert chi tiết phiếu mượn
            if (!empty($data['thietBi'])) {
                foreach ($data['thietBi'] as $thietBi) {
                    $sqlDetail = "INSERT INTO ChiTietPhieuMuon (maPhieu, maTB, soLuong) VALUES (?, ?, ?)";
                    $stmtDetail = $this->conn->prepare($sqlDetail);
                    
                    if (!$stmtDetail) {
                        $this->conn->rollback();
                        return ['success' => false, 'message' => 'Lỗi chuẩn bị câu lệnh chi tiết'];
                    }

                    $stmtDetail->bind_param('iii', $maPhieuId, $thietBi['maTB'], $thietBi['soLuong']);
                    
                    if (!$stmtDetail->execute()) {
                        $this->conn->rollback();
                        return ['success' => false, 'message' => 'Lỗi thêm chi tiết thiết bị'];
                    }
                }
            }

            // Commit transaction
            $this->conn->commit();
            $this->conn->autocommit(true);

            return [
                'success' => true, 
                'message' => 'Tạo phiếu mượn thành công',
                'data' => ['maPhieu' => $maPhieuId, 'maPhieuCode' => $maPhieu]
            ];

        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }

    // Tạo mã phiếu mượn theo format PM-YYYYMMDD-XXX
    private function taoMaPhieuMuon()
    {
        $today = date('Ymd');
        $prefix = "PM-{$today}-";
        
        // Lấy số thứ tự cuối cùng trong ngày
        $sql = "SELECT COUNT(*) as count FROM PhieuMuon WHERE DATE(ngayMuon) = CURDATE()";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        $sequence = str_pad($row['count'] + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $sequence;
    }

    // Validate dữ liệu phiếu mượn
    public function validatePhieuMuon($data)
    {
        $errors = [];

        // Validate ngày mượn
        if (empty($data['ngayMuon'])) {
            $errors[] = 'Ngày mượn không được để trống';
        } elseif (strtotime($data['ngayMuon']) < strtotime(date('Y-m-d'))) {
            $errors[] = 'Ngày mượn phải từ hôm nay trở đi';
        }

        // Validate ngày trả dự kiến
        if (empty($data['ngayTraDuKien'])) {
            $errors[] = 'Ngày trả dự kiến không được để trống';
        } elseif (!empty($data['ngayMuon']) && strtotime($data['ngayTraDuKien']) <= strtotime($data['ngayMuon'])) {
            $errors[] = 'Ngày trả dự kiến phải sau ngày mượn';
        }

        // Validate mục đích
        if (empty($data['mucDich'])) {
            $errors[] = 'Mục đích mượn không được để trống';
        } elseif (strlen($data['mucDich']) > 500) {
            $errors[] = 'Mục đích mượn không được vượt quá 500 ký tự';
        }

        // Validate thiết bị
        if (empty($data['thietBi']) || !is_array($data['thietBi'])) {
            $errors[] = 'Phải chọn ít nhất một thiết bị';
        } else {
            foreach ($data['thietBi'] as $index => $thietBi) {
                if (empty($thietBi['maTB']) || !is_numeric($thietBi['maTB'])) {
                    $errors[] = "Thiết bị thứ " . ($index + 1) . " không hợp lệ";
                }
                if (empty($thietBi['soLuong']) || !is_numeric($thietBi['soLuong']) || $thietBi['soLuong'] <= 0) {
                    $errors[] = "Số lượng thiết bị thứ " . ($index + 1) . " phải là số nguyên dương";
                } else {
                    // Kiểm tra số lượng có sẵn
                    if (!$this->kiemTraSoLuongKhaDung($thietBi['maTB'], $thietBi['soLuong'])) {
                        $errors[] = "Số lượng thiết bị thứ " . ($index + 1) . " vượt quá số lượng có sẵn";
                    }
                }
            }
        }

        if (!empty($errors)) {
            return ['success' => false, 'message' => implode(', ', $errors)];
        }

        return ['success' => true];
    }

    // Kiểm tra số lượng thiết bị có sẵn
    private function kiemTraSoLuongKhaDung($maTB, $soLuongMuon)
    {
        $sql = "SELECT soLuongKhaDung FROM ThietBi WHERE maTB = ? AND isHidden = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $maTB);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['soLuongKhaDung'] >= $soLuongMuon;
        }
        
        return false;
    }

    // Lấy danh sách phiếu mượn của giáo viên
    public function layDanhSachPhieuMuon($maND)
    {
        // Kiểm tra xem cột mucDich có tồn tại không
        $checkColumn = $this->conn->query("SHOW COLUMNS FROM PhieuMuon LIKE 'mucDich'");
        $hasMucDich = $checkColumn->num_rows > 0;

        $mucDichSelect = $hasMucDich ? "pm.mucDich," : "'Không có mục đích' as mucDich,";

        $groupByClause = $hasMucDich 
            ? "GROUP BY pm.maPhieu, pm.ngayMuon, pm.ngayTraDuKien, pm.ngayTraThucTe, pm.trangThai, pm.mucDich"
            : "GROUP BY pm.maPhieu, pm.ngayMuon, pm.ngayTraDuKien, pm.ngayTraThucTe, pm.trangThai";

        $sql = "
            SELECT 
                pm.maPhieu,
                pm.ngayMuon,
                pm.ngayTraDuKien,
                pm.ngayTraThucTe,
                pm.trangThai,
                {$mucDichSelect}
                GROUP_CONCAT(tb.tenTB SEPARATOR ', ') as danhSachThietBi,
                SUM(ctpm.soLuong) as tongSoLuong
            FROM PhieuMuon pm
            LEFT JOIN ChiTietPhieuMuon ctpm ON pm.maPhieu = ctpm.maPhieu
            LEFT JOIN ThietBi tb ON ctpm.maTB = tb.maTB
            WHERE pm.maND = ?
            {$groupByClause}
            ORDER BY pm.maPhieu DESC, pm.ngayMuon DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $maND);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return ['success' => true, 'data' => $data];
    }

    // Lấy chi tiết một phiếu mượn
    public function layChiTietPhieuMuon($maPhieu, $maND = null)
    {
        // Kiểm tra xem cột mucDich có tồn tại không
        $checkColumn = $this->conn->query("SHOW COLUMNS FROM PhieuMuon LIKE 'mucDich'");
        $hasMucDich = $checkColumn->num_rows > 0;

        $mucDichSelect = $hasMucDich ? "pm.mucDich," : "'Không có mục đích' as mucDich,";

        $sql = "
            SELECT 
                pm.maPhieu,
                pm.ngayMuon,
                pm.ngayTraDuKien,
                pm.ngayTraThucTe,
                pm.trangThai,
                {$mucDichSelect}
                pm.maND,
                nd.hoTen as tenNguoiMuon
            FROM PhieuMuon pm
            LEFT JOIN NguoiDung nd ON pm.maND = nd.maND
            WHERE pm.maPhieu = ?
        ";

        if ($maND) {
            $sql .= " AND pm.maND = ?";
        }

        $stmt = $this->conn->prepare($sql);
        if ($maND) {
            $stmt->bind_param('ii', $maPhieu, $maND);
        } else {
            $stmt->bind_param('i', $maPhieu);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        if ($phieu = $result->fetch_assoc()) {
            // Lấy chi tiết thiết bị
            $sqlDetail = "
                SELECT 
                    ctpm.maCT,
                    ctpm.maTB,
                    ctpm.soLuong,
                    ctpm.tinhTrangKhiTra,
                    tb.tenTB,
                    tb.donVi
                FROM ChiTietPhieuMuon ctpm
                LEFT JOIN ThietBi tb ON ctpm.maTB = tb.maTB
                WHERE ctpm.maPhieu = ?
            ";
            
            $stmtDetail = $this->conn->prepare($sqlDetail);
            $stmtDetail->bind_param('i', $maPhieu);
            $stmtDetail->execute();
            $resultDetail = $stmtDetail->get_result();

            $thietBi = [];
            while ($row = $resultDetail->fetch_assoc()) {
                $thietBi[] = $row;
            }

            $phieu['thietBi'] = $thietBi;
            return ['success' => true, 'data' => $phieu];
        }

        return ['success' => false, 'message' => 'Không tìm thấy phiếu mượn'];
    }

    // Hủy phiếu mượn (chỉ khi trạng thái "Chờ duyệt")
    public function huyPhieuMuon($maPhieu, $maND)
    {
        // Kiểm tra phiếu mượn có thuộc về giáo viên và đang chờ duyệt
        $sql = "SELECT trangThai FROM PhieuMuon WHERE maPhieu = ? AND maND = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $maPhieu, $maND);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['trangThai'] !== 'Chờ duyệt') {
                return ['success' => false, 'message' => 'Chỉ có thể hủy phiếu mượn đang chờ duyệt'];
            }

            // Cập nhật trạng thái thành "Đã hủy"
            $sqlUpdate = "UPDATE PhieuMuon SET trangThai = 'Đã hủy' WHERE maPhieu = ?";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param('i', $maPhieu);

            if ($stmtUpdate->execute()) {
                return ['success' => true, 'message' => 'Hủy phiếu mượn thành công'];
            } else {
                return ['success' => false, 'message' => 'Lỗi khi hủy phiếu mượn'];
            }
        }

        return ['success' => false, 'message' => 'Không tìm thấy phiếu mượn hoặc không có quyền'];
    }

    // Lấy danh sách thiết bị có thể mượn
    public function layDanhSachThietBi()
    {
        $sql = "
            SELECT 
                tb.maTB,
                tb.tenTB,
                tb.donVi,
                tb.soLuongTong,
                tb.soLuongKhaDung,
                tb.tinhTrang,
                mh.tenMonHoc
            FROM ThietBi tb
            LEFT JOIN MonHoc mh ON tb.maMH = mh.maMH
            WHERE tb.isHidden = 0 AND tb.soLuongKhaDung > 0
            ORDER BY tb.tenTB ASC
        ";

        $result = $this->conn->query($sql);
        $data = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return ['success' => true, 'data' => $data];
    }

    // Lấy danh sách mục đích thường dùng
    public function layDanhSachMucDich($limit = 10)
    {
        // Kiểm tra xem bảng MucDichMuon có tồn tại không
        $checkTable = $this->conn->query("SHOW TABLES LIKE 'MucDichMuon'");
        if ($checkTable->num_rows == 0) {
            // Nếu chưa có bảng, trả về danh sách mặc định
            return [
                'success' => true, 
                'data' => [
                    ['tenMucDich' => 'Dạy học', 'moTa' => 'Sử dụng thiết bị để giảng dạy trong lớp học'],
                    ['tenMucDich' => 'Thực hành', 'moTa' => 'Thực hành thí nghiệm, bài tập cho học sinh'],
                    ['tenMucDich' => 'Hội thảo', 'moTa' => 'Tổ chức hội thảo, seminar giáo dục'],
                    ['tenMucDich' => 'Thi cử', 'moTa' => 'Phục vụ các kỳ thi, kiểm tra'],
                    ['tenMucDich' => 'Hoạt động ngoại khóa', 'moTa' => 'Các hoạt động văn nghệ, thể thao']
                ]
            ];
        }

        $sql = "
            SELECT 
                maMucDich,
                tenMucDich,
                moTa,
                soLanSuDung
            FROM MucDichMuon 
            WHERE trangThai = 'Hoạt động'
            ORDER BY soLanSuDung DESC, tenMucDich ASC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return ['success' => true, 'data' => $data];
    }

    // Cập nhật phiếu mượn (chỉ khi chờ duyệt)
    public function capNhatPhieuMuon($maPhieu, $data, $maND)
    {
        try {
            // Kiểm tra quyền và trạng thái
            $checkSql = "SELECT trangThai FROM PhieuMuon WHERE maPhieu = ? AND maND = ?";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->bind_param('ii', $maPhieu, $maND);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if (!$row = $checkResult->fetch_assoc()) {
                return ['success' => false, 'message' => 'Không tìm thấy phiếu mượn hoặc không có quyền'];
            }

            if ($row['trangThai'] !== 'Chờ duyệt') {
                return ['success' => false, 'message' => 'Chỉ có thể sửa phiếu mượn đang chờ duyệt'];
            }

            // Validate dữ liệu
            $validation = $this->validatePhieuMuon($data);
            if (!$validation['success']) {
                return $validation;
            }

            // Bắt đầu transaction
            $this->conn->autocommit(false);

            // Kiểm tra xem cột mucDich có tồn tại không
            $checkColumn = $this->conn->query("SHOW COLUMNS FROM PhieuMuon LIKE 'mucDich'");
            $hasMucDich = $checkColumn->num_rows > 0;

            // Cập nhật thông tin phiếu mượn
            if ($hasMucDich) {
                $updateSql = "UPDATE PhieuMuon SET ngayMuon = ?, ngayTraDuKien = ?, mucDich = ? WHERE maPhieu = ?";
                $updateStmt = $this->conn->prepare($updateSql);
                $updateStmt->bind_param('sssi', $data['ngayMuon'], $data['ngayTraDuKien'], $data['mucDich'], $maPhieu);
            } else {
                $updateSql = "UPDATE PhieuMuon SET ngayMuon = ?, ngayTraDuKien = ? WHERE maPhieu = ?";
                $updateStmt = $this->conn->prepare($updateSql);
                $updateStmt->bind_param('ssi', $data['ngayMuon'], $data['ngayTraDuKien'], $maPhieu);
            }

            if (!$updateStmt->execute()) {
                $this->conn->rollback();
                return ['success' => false, 'message' => 'Lỗi cập nhật phiếu mượn'];
            }

            // Xóa chi tiết cũ
            $deleteSql = "DELETE FROM ChiTietPhieuMuon WHERE maPhieu = ?";
            $deleteStmt = $this->conn->prepare($deleteSql);
            $deleteStmt->bind_param('i', $maPhieu);
            $deleteStmt->execute();

            // Thêm chi tiết mới
            if (!empty($data['thietBi'])) {
                foreach ($data['thietBi'] as $thietBi) {
                    $insertSql = "INSERT INTO ChiTietPhieuMuon (maPhieu, maTB, soLuong) VALUES (?, ?, ?)";
                    $insertStmt = $this->conn->prepare($insertSql);
                    $insertStmt->bind_param('iii', $maPhieu, $thietBi['maTB'], $thietBi['soLuong']);
                    
                    if (!$insertStmt->execute()) {
                        $this->conn->rollback();
                        return ['success' => false, 'message' => 'Lỗi cập nhật chi tiết thiết bị'];
                    }
                }
            }

            $this->conn->commit();
            $this->conn->autocommit(true);

            return ['success' => true, 'message' => 'Cập nhật phiếu mượn thành công'];

        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }
}
?>