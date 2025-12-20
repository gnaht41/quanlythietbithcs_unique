<?php
class CT_BaoCaoHuHongModel {
    private static $data = [];
    private static $deletedIds = [];
    
    public function layThietBiDangMuon() {
        // Sử dụng model phiếu mượn để lấy dữ liệu
        require_once __DIR__ . '/CT_PhieuMuonModel.php';
        $phieuMuonModel = new CT_PhieuMuonModel();
        
        // Lấy danh sách phiếu mượn (bao gồm cả phiếu mẫu)
        $danhSachPhieu = $phieuMuonModel->layDanhSach(1, 20); // Giả sử maND = 1
        
        error_log("=== DAMAGE REPORT EQUIPMENT DEBUG ===");
        error_log("Found " . count($danhSachPhieu) . " phiếu mượn");
        
        $data = [];
        foreach ($danhSachPhieu as $phieu) {
            error_log("Processing phiếu: " . $phieu['ma'] . " - Status: " . $phieu['trangthai']);
            
            // CHỈ LẤY PHIẾU CÓ TRẠNG THÁI "ĐANG MƯỢN"
            if ($phieu['trangthai'] !== 'Đang mượn') {
                error_log("Skipping phiếu " . $phieu['ma'] . " - Status: " . $phieu['trangthai']);
                continue;
            }
            
            // Parse thiết bị từ phiếu mượn
            $thietBiText = $phieu['thietbi'];
            error_log("Equipment text: " . $thietBiText);
            
            // Tách các thiết bị nếu có nhiều thiết bị
            if (strpos($thietBiText, ',') !== false) {
                // Nhiều thiết bị: "Máy tính Dell SL:1, Loa Bluetooth SL:1"
                $thietBiParts = explode(',', $thietBiText);
                foreach ($thietBiParts as $part) {
                    $part = trim($part);
                    if (preg_match('/(.+) SL:(\d+)/', $part, $matches)) {
                        $tenTB = trim($matches[1]);
                        $soLuong = $matches[2];
                        $maTB = 'TB-' . substr(md5($tenTB), 0, 3);
                        
                        $data[] = [
                            'maPhieu' => $phieu['ma'],
                            'maTB' => $maTB,
                            'tenTB' => $tenTB,
                            'soLuong' => $soLuong,
                            'thoiGianTao' => $phieu['ngaymuon'],
                            'trangThai' => $phieu['trangthai']
                        ];
                        
                        error_log("Added equipment: " . $tenTB . " from phiếu " . $phieu['ma']);
                    }
                }
            } else {
                // Một thiết bị: "Máy tính Dell SL:1"
                if (preg_match('/(.+) SL:(\d+)/', $thietBiText, $matches)) {
                    $tenTB = trim($matches[1]);
                    $soLuong = $matches[2];
                    $maTB = 'TB-' . substr(md5($tenTB), 0, 3);
                    
                    $data[] = [
                        'maPhieu' => $phieu['ma'],
                        'maTB' => $maTB,
                        'tenTB' => $tenTB,
                        'soLuong' => $soLuong,
                        'thoiGianTao' => $phieu['ngaymuon'],
                        'trangThai' => $phieu['trangthai']
                    ];
                    
                    error_log("Added equipment: " . $tenTB . " from phiếu " . $phieu['ma']);
                }
            }
        }
        
        error_log("Total equipment for damage report: " . count($data));
        
        // Nếu không có dữ liệu thật, trả về test data
        return !empty($data) ? $data : $this->getTestData();
    }
    
    public function taoBaoCao($data) {
        // Validate required fields
        if (empty($data['maTB']) || empty($data['tenTB']) || empty($data['tinhTrang']) || empty($data['noiDungBaoCao'])) {
            throw new Exception('Thiếu thông tin bắt buộc');
        }
        
        // Save to database using the same format as phiếu mượn
        require_once __DIR__ . '/QT_Log.php';
        $log = new Log();
        
        // Check session
        if (!isset($_SESSION)) {
            session_start();
        }
        $maND = $_SESSION['maND'] ?? 1;
        
        error_log("=== SAVE DAMAGE REPORT DEBUG ===");
        error_log("Session maND: " . $maND);
        error_log("Input data: " . print_r($data, true));
        
        // Generate unique report code
        $maBaoCao = 'BC' . date('ymd') . rand(100, 999);
        
        // Create compact format for database storage
        $hanhDong = "BC:$maBaoCao|TB:{$data['tenTB']}|TT:" . substr($data['tinhTrang'], 0, 30) . "|ND:" . substr($data['noiDungBaoCao'], 0, 50);
        
        error_log("hanhDong: " . $hanhDong);
        error_log("Length: " . strlen($hanhDong));
        
        $result = $log->ghiLog($maND, $hanhDong, "BaoCaoHuHong");
        
        error_log("Database save result: " . ($result ? 'SUCCESS' : 'FAILED'));
        
        if (!$result) {
            throw new Exception('Lỗi lưu báo cáo vào database');
        }
        
        return true;
    }
    
    public function layDanhSach() {
        $data = [];
        
        // Read from database first
        require_once __DIR__ . '/QT_Database.php';
        $db = new Database();
        
        if ($conn = $db->getConnection()) {
            $sql = "SELECT maLog, hanhDong, thoiGian FROM BangGhiLog WHERE hanhDong LIKE 'BC:%' ORDER BY thoiGian DESC LIMIT 20";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            error_log("=== DAMAGE REPORT LIST DEBUG ===");
            error_log("Found " . $result->num_rows . " damage reports in database");
            
            while ($row = $result->fetch_assoc()) {
                error_log("Raw record: " . print_r($row, true));
                $parsed = $this->parseBaoCao($row);
                if ($parsed) {
                    $data[] = $parsed;
                    error_log("Parsed successfully: " . $parsed['maBaoCao']);
                } else {
                    error_log("Failed to parse record ID: " . $row['maLog']);
                }
            }
        }
        
        error_log("Total parsed damage reports: " . count($data));
        
        // Only add test data if NO real data exists
        if (empty($data) && !in_array(999, self::$deletedIds)) {
            error_log("Adding test data because no real data found");
            $data[] = [
                'maBaoCao' => 999,
                'maTB' => 'TB-001',
                'tenTB' => 'Máy tính Dell',
                'tinhTrang' => 'Màn hình bị vỡ, không hiển thị được',
                'noiDungBaoCao' => 'Máy tính bị rơi làm vỡ màn hình, cần thay thế màn hình mới',
                'ngayBaoCao' => date('Y-m-d H:i:s'),
                'trangThai' => 'Đang xử lý'
            ];
        }
        
        // Don't merge with memory data if we have database data
        $finalData = empty($data) ? array_merge($data, self::$data) : $data;
        error_log("Final data count: " . count($finalData));
        
        return $finalData;
    }
    
    public function layChiTiet($id) {
        // First check database
        require_once __DIR__ . '/QT_Database.php';
        $db = new Database();
        
        if ($conn = $db->getConnection()) {
            $sql = "SELECT maLog, hanhDong, thoiGian FROM BangGhiLog WHERE maLog = ? AND hanhDong LIKE 'BC:%'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return $this->parseBaoCao($row);
            }
        }
        
        // Fallback to test data
        if ($id == 999) {
            return [
                'maBaoCao' => 999,
                'maTB' => 'TB-001',
                'tenTB' => 'Máy tính Dell',
                'tinhTrang' => 'Màn hình bị vỡ, không hiển thị được',
                'noiDungBaoCao' => 'Máy tính bị rơi làm vỡ màn hình, cần thay thế màn hình mới',
                'ngayBaoCao' => date('Y-m-d H:i:s'),
                'trangThai' => 'Đang xử lý'
            ];
        }
        
        // Check memory data
        foreach (self::$data as $item) {
            if ($item['maBaoCao'] == $id) return $item;
        }
        
        return null;
    }
    
    private function parseBaoCao($row) {
        $hanhDong = $row['hanhDong'];
        
        error_log("=== PARSE DAMAGE REPORT ===");
        error_log("Raw hanhDong: " . $hanhDong);
        
        // Parse format: BC:BC251220123|TB:Máy tính Dell|TT:Màn hình bị vỡ|ND:Chi tiết hư hỏng
        if (preg_match('/BC:(BC\w+)\|TB:(.+?)\|TT:(.+?)\|ND:(.+)/', $hanhDong, $matches)) {
            $maBaoCao = $matches[1];
            $tenTB = $matches[2];
            $tinhTrang = $matches[3];
            $noiDung = $matches[4];
            
            // Generate maTB from tenTB
            $maTB = 'TB-' . substr(md5($tenTB), 0, 3);
            
            error_log("✅ Parsed damage report: " . $maBaoCao);
            
            return [
                'maBaoCao' => $row['maLog'], // Use database ID for operations
                'maTB' => $maTB,
                'tenTB' => $tenTB,
                'tinhTrang' => $tinhTrang,
                'noiDungBaoCao' => $noiDung,
                'ngayBaoCao' => $row['thoiGian'],
                'trangThai' => 'Đang xử lý'
            ];
        }
        
        error_log("❌ Could not parse damage report format");
        return null;
    }
    
    public function capNhat($id, $data) {
        require_once __DIR__ . '/QT_Database.php';
        $db = new Database();
        
        if ($conn = $db->getConnection()) {
            // Get current record
            $stmt = $conn->prepare("SELECT hanhDong FROM BangGhiLog WHERE maLog = ? AND hanhDong LIKE 'BC:%'");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $hanhDong = $row['hanhDong'];
                
                // Extract current data
                if (preg_match('/BC:(BC\w+)\|TB:(.+?)\|TT:(.+?)\|ND:(.+)/', $hanhDong, $matches)) {
                    $maBaoCao = $matches[1];
                    $tenTB = $matches[2];
                    
                    // Create updated record
                    $newHanhDong = "BC:$maBaoCao|TB:$tenTB|TT:" . substr($data['tinhTrang'], 0, 30) . "|ND:" . substr($data['noiDungBaoCao'], 0, 50);
                    
                    $updateStmt = $conn->prepare("UPDATE BangGhiLog SET hanhDong = ? WHERE maLog = ?");
                    $updateStmt->bind_param("si", $newHanhDong, $id);
                    return $updateStmt->execute();
                }
            }
        }
        
        // Fallback to memory update
        for ($i = 0; $i < count(self::$data); $i++) {
            if (self::$data[$i]['maBaoCao'] == $id) {
                self::$data[$i]['tinhTrang'] = $data['tinhTrang'];
                self::$data[$i]['noiDungBaoCao'] = $data['noiDungBaoCao'];
                self::$data[$i]['ngayCapNhat'] = date('Y-m-d H:i:s');
                return true;
            }
        }
        return false;
    }
    
    public function xoa($id) {
        require_once __DIR__ . '/QT_Database.php';
        $db = new Database();
        
        if ($conn = $db->getConnection()) {
            $stmt = $conn->prepare("DELETE FROM BangGhiLog WHERE maLog = ? AND hanhDong LIKE 'BC:%'");
            $stmt->bind_param("i", $id);
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                return true;
            }
        }
        
        // Fallback to memory deletion
        self::$data = array_filter(self::$data, fn($item) => $item['maBaoCao'] != $id);
        self::$deletedIds[] = $id;
        return true;
    }
    
    private function getTestData() {
        // Test data phù hợp với phiếu mẫu từ trang phiếu mượn
        return [
            ['maPhieu' => 'PM251220001', 'maTB' => 'TB-001', 'tenTB' => 'Máy tính Dell', 'soLuong' => 1, 'trangThai' => 'Đang mượn'],
            ['maPhieu' => 'PM251220002', 'maTB' => 'TB-002', 'tenTB' => 'Loa Bluetooth', 'soLuong' => 2, 'trangThai' => 'Đang mượn'],
            ['maPhieu' => 'PM251220003', 'maTB' => 'TB-003', 'tenTB' => 'Máy tính Dell', 'soLuong' => 1, 'trangThai' => 'Đang mượn'],
            ['maPhieu' => 'PM251220003', 'maTB' => 'TB-004', 'tenTB' => 'Loa Bluetooth', 'soLuong' => 1, 'trangThai' => 'Đang mượn']
        ];
    }
}
?>
