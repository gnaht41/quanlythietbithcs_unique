<?php
class CT_PhieuMuonModel {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/QT_Database.php';
        $this->db = new Database();
    }
    
    public function taoPhieu($maND, $ngayMuon, $ngayTra, $mucDich, $thietBi) {
        require_once __DIR__ . '/QT_Log.php';
        $log = new Log();
        $maPhieu = 'PM' . date('ymd') . rand(100, 999);
        
        // Rút gọn tối đa để tránh bị cắt
        $thietBiShort = [];
        foreach ($thietBi as $tb) {
            // Chỉ lấy từ đầu tiên + SL
            $parts = explode(' ', $tb);
            $name = $parts[0]; // Chỉ lấy từ đầu (Máy, Loa, etc.)
            $sl = 'SL:1';
            if (strpos($tb, 'SL:') !== false) {
                $sl = substr($tb, strpos($tb, 'SL:'));
            }
            $thietBiShort[] = $name . ' ' . $sl;
        }
        $thietBiText = implode(',', $thietBiShort); // Dùng dấu phẩy thay vì ", "
        
        // Rút gọn mục đích
        $mucDichShort = $mucDich;
        $mucDichMap = [
            'Dạy học' => 'DH',
            'Họp phụ huynh' => 'HPH', 
            'Hội nghị' => 'HN',
            'Thi cử' => 'TC',
            'Hoạt động ngoại khóa' => 'HDNK'
        ];
        if (isset($mucDichMap[$mucDich])) {
            $mucDichShort = $mucDichMap[$mucDich];
        }
        
        // Format với năm đầy đủ để hỗ trợ 2026: PM:PM123|TB:Máy SL:1|MD:DH|NM:20/12/2025|NT:27/01/2026
        // Giáo viên tạo phiếu => trạng thái mặc định là 'Chờ duyệt' (không được duyệt tự động)
        $ngayMuonShort = date('d/m/Y', strtotime($ngayMuon));
        $ngayTraShort = date('d/m/Y', strtotime($ngayTra));
        $hanhDong = "PM:$maPhieu|TB:$thietBiText|MD:$mucDichShort|NM:$ngayMuonShort|NT:$ngayTraShort|TT:Chờ duyệt";
        
        error_log("=== SAVE DEBUG ===");
        error_log("mucDich input: " . $mucDich . " -> " . $mucDichShort);
        error_log("hanhDong: " . $hanhDong);
        error_log("Length: " . strlen($hanhDong));
        
        return $log->ghiLog($maND, $hanhDong, "PhieuMuon");
    }
    
    public function layDanhSach($maND, $limit = 10) {
        $data = [];
        if ($conn = $this->db->getConnection()) {
            $stmt = $conn->prepare("SELECT maLog, hanhDong, thoiGian FROM BangGhiLog WHERE maND = ? AND (hanhDong LIKE 'Phieu muon%' OR hanhDong LIKE 'PM:%') ORDER BY thoiGian DESC LIMIT ?");
            $stmt->bind_param("ii", $maND, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->parsePhieu($row);
            }
        }
        
        // Luôn thêm 6 phiếu mẫu mặc định vào đầu danh sách
        $phieuMau = [
            [
                'id' => 9001,
                'ma' => 'PM251220001',
                'thietbi' => 'Máy tính Dell SL:1',
                'ngaymuon' => '2024-12-17', // Ngày 17/12/2024
                'ngaytra' => '2024-12-24',  // Ngày 24/12/2024
                'mucdich' => 'Dạy học',
                'trangthai' => 'Đang mượn'
            ],
            [
                'id' => 9002,
                'ma' => 'PM251220461',
                'thietbi' => 'Máy tính Dell SL:1',
                'ngaymuon' => '2024-12-19', // Ngày 19/12/2024
                'ngaytra' => '2024-12-27',  // Ngày 27/12/2024
                'mucdich' => 'Dạy học',
                'trangthai' => 'Đang mượn'
            ],
            [
                'id' => 9003,
                'ma' => 'PM251220454',
                'thietbi' => 'Loa Bluetooth SL:2',
                'ngaymuon' => '2024-12-19', // Ngày 19/12/2024
                'ngaytra' => '2024-12-27',  // Ngày 27/12/2024
                'mucdich' => 'Họp phụ huynh',
                'trangthai' => 'Đang mượn'
            ],
            [
                'id' => 9004,
                'ma' => 'PM251220772',
                'thietbi' => 'Máy chiếu Epson SL:1',
                'ngaymuon' => '2024-12-19', // Ngày 19/12/2024
                'ngaytra' => '2024-12-27',  // Ngày 27/12/2024
                'mucdich' => 'Hội nghị',
                'trangthai' => 'Đang mượn'
            ],
            [
                'id' => 9005,
                'ma' => 'PM251220964',
                'thietbi' => 'Máy tính Dell SL:1',
                'ngaymuon' => '2024-12-24', // Ngày 24/12/2024
                'ngaytra' => '2024-12-28',  // Ngày 28/12/2024
                'mucdich' => 'Thi cử',
                'trangthai' => 'Chờ duyệt'
            ],
            [
                'id' => 9006,
                'ma' => 'PM251220961',
                'thietbi' => 'Loa Bluetooth SL:3',
                'ngaymuon' => '2024-12-26', // Ngày 26/12/2024
                'ngaytra' => '2024-12-30',  // Ngày 30/12/2024
                'mucdich' => 'Hoạt động ngoại khóa',
                'trangthai' => 'Chờ duyệt'
            ]
        ];
        
        // Gộp dữ liệu thật với phiếu mẫu (dữ liệu thật ở trên, phiếu mẫu ở dưới)
        return array_merge($data, $phieuMau);
    }
    
    public function layChiTiet($maND, $id) {
        // Kiểm tra phiếu mẫu trước
        if ($id >= 9001 && $id <= 9006) {
            $phieuMau = [
                9001 => [
                    'id' => 9001,
                    'ma' => 'PM251220001',
                    'thietbi' => 'Máy tính Dell SL:1',
                    'ngaymuon' => '2024-12-17',
                    'ngaytra' => '2024-12-24',
                    'mucdich' => 'Dạy học',
                    'trangthai' => 'Đang mượn'
                ],
                9002 => [
                    'id' => 9002,
                    'ma' => 'PM251220461',
                    'thietbi' => 'Máy tính Dell SL:1',
                    'ngaymuon' => '2024-12-19',
                    'ngaytra' => '2024-12-27',
                    'mucdich' => 'Dạy học',
                    'trangthai' => 'Đang mượn'
                ],
                9003 => [
                    'id' => 9003,
                    'ma' => 'PM251220454',
                    'thietbi' => 'Loa Bluetooth SL:2',
                    'ngaymuon' => '2024-12-19',
                    'ngaytra' => '2024-12-27',
                    'mucdich' => 'Họp phụ huynh',
                    'trangthai' => 'Đang mượn'
                ],
                9004 => [
                    'id' => 9004,
                    'ma' => 'PM251220772',
                    'thietbi' => 'Máy chiếu Epson SL:1',
                    'ngaymuon' => '2024-12-19',
                    'ngaytra' => '2024-12-27',
                    'mucdich' => 'Hội nghị',
                    'trangthai' => 'Đang mượn'
                ],
                9005 => [
                    'id' => 9005,
                    'ma' => 'PM251220964',
                    'thietbi' => 'Máy tính Dell SL:1',
                    'ngaymuon' => '2024-12-24',
                    'ngaytra' => '2024-12-28',
                    'mucdich' => 'Thi cử',
                    'trangthai' => 'Chờ duyệt'
                ],
                9006 => [
                    'id' => 9006,
                    'ma' => 'PM251220961',
                    'thietbi' => 'Loa Bluetooth SL:3',
                    'ngaymuon' => '2024-12-26',
                    'ngaytra' => '2024-12-30',
                    'mucdich' => 'Hoạt động ngoại khóa',
                    'trangthai' => 'Chờ duyệt'
                ]
            ];
            
            if (isset($phieuMau[$id])) {
                return $phieuMau[$id];
            }
        }
        
        if ($conn = $this->db->getConnection()) {
            $stmt = $conn->prepare("SELECT maLog, hanhDong, thoiGian FROM BangGhiLog WHERE maLog = ? AND maND = ? AND (hanhDong LIKE 'Phieu muon%' OR hanhDong LIKE 'PM:%')");
            $stmt->bind_param("ii", $id, $maND);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return $this->parsePhieu($row);
            }
        }
        return null;
    }
    
    public function capNhat($maND, $id, $ngayMuon, $ngayTra, $mucDich, $thietBi) {
        if ($conn = $this->db->getConnection()) {
            $stmt = $conn->prepare("SELECT hanhDong FROM BangGhiLog WHERE maLog = ? AND maND = ?");
            $stmt->bind_param("ii", $id, $maND);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $maPhieu = 'PM' . date('ymd') . rand(100, 999);
                if (preg_match('/PM:?(PM\w+)/', $row['hanhDong'], $matches)) {
                    $maPhieu = $matches[1];
                }
                
                // Sử dụng format ngắn gọn giống taoPhieu
                $thietBiShort = [];
                foreach ($thietBi as $tb) {
                    $parts = explode(' ', $tb);
                    $name = $parts[0];
                    $sl = 'SL:1';
                    if (strpos($tb, 'SL:') !== false) {
                        $sl = substr($tb, strpos($tb, 'SL:'));
                    }
                    $thietBiShort[] = $name . ' ' . $sl;
                }
                $thietBiText = implode(',', $thietBiShort);
                
                // Rút gọn mục đích
                $mucDichShort = $mucDich;
                $mucDichMap = [
                    'Dạy học' => 'DH',
                    'Họp phụ huynh' => 'HPH', 
                    'Hội nghị' => 'HN',
                    'Thi cử' => 'TC',
                    'Hoạt động ngoại khóa' => 'HDNK'
                ];
                if (isset($mucDichMap[$mucDich])) {
                    $mucDichShort = $mucDichMap[$mucDich];
                }
                
                $ngayMuonShort = date('d/m/Y', strtotime($ngayMuon));
                $ngayTraShort = date('d/m/Y', strtotime($ngayTra));
                $hanhDong = "PM:$maPhieu|TB:$thietBiText|MD:$mucDichShort|NM:$ngayMuonShort|NT:$ngayTraShort";
                
                $updateStmt = $conn->prepare("UPDATE BangGhiLog SET hanhDong = ? WHERE maLog = ? AND maND = ?");
                $updateStmt->bind_param("sii", $hanhDong, $id, $maND);
                return $updateStmt->execute();
            }
        }
        return false;
    }
    
    public function xoa($maND, $id) {
        if ($conn = $this->db->getConnection()) {
            $stmt = $conn->prepare("DELETE FROM BangGhiLog WHERE maLog = ? AND maND = ?");
            $stmt->bind_param("ii", $id, $maND);
            return $stmt->execute() && $stmt->affected_rows > 0;
        }
        return false;
    }
    
    private function taoPhieuMauVaoDatabase($maND) {
        require_once __DIR__ . '/QT_Log.php';
        $log = new Log();
        
        // Tạo 5 phiếu mẫu trực tiếp vào database
        $sampleData = [
            [
                'maPhieu' => 'PM' . date('ymd') . '001',
                'thietBi' => 'Máy SL:1',
                'mucDich' => 'DH',
                'ngayMuon' => date('d/m'),
                'ngayTra' => date('d/m', strtotime('+7 days')),
                'trangThai' => 'Đang mượn'
            ],
            [
                'maPhieu' => 'PM' . date('ymd') . '002',
                'thietBi' => 'Loa SL:2',
                'mucDich' => 'HPH',
                'ngayMuon' => date('d/m'),
                'ngayTra' => date('d/m', strtotime('+5 days')),
                'trangThai' => 'Đang mượn'
            ],
            [
                'maPhieu' => 'PM' . date('ymd') . '003',
                'thietBi' => 'Máy SL:1,Loa SL:1',
                'mucDich' => 'HN',
                'ngayMuon' => date('d/m'),
                'ngayTra' => date('d/m', strtotime('+3 days')),
                'trangThai' => 'Đang mượn'
            ],
            [
                'maPhieu' => 'PM' . date('ymd') . '004',
                'thietBi' => 'Máy SL:2',
                'mucDich' => 'TC',
                'ngayMuon' => date('d/m'),
                'ngayTra' => date('d/m', strtotime('+10 days')),
                'trangThai' => 'Chờ duyệt'
            ],
            [
                'maPhieu' => 'PM' . date('ymd') . '005',
                'thietBi' => 'Loa SL:3',
                'mucDich' => 'HDNK',
                'ngayMuon' => date('d/m'),
                'ngayTra' => date('d/m', strtotime('+14 days')),
                'trangThai' => 'Chờ duyệt'
            ]
        ];
        
        $success = 0;
        foreach ($sampleData as $data) {
            $hanhDong = "PM:{$data['maPhieu']}|TB:{$data['thietBi']}|MD:{$data['mucDich']}|NM:{$data['ngayMuon']}|NT:{$data['ngayTra']}|TT:{$data['trangThai']}";
            if ($log->ghiLog($maND, $hanhDong, "PhieuMuon")) {
                $success++;
            }
        }
        
        error_log("=== SAMPLE DATA CREATED ===");
        error_log("Created $success sample records for user $maND");
        
        return $success;
    }
    
    public function forceTaoPhieuMau($maND) {
        return $this->taoPhieuMauVaoDatabase($maND);
    }
    
    private function parsePhieu($row) {
        $hanhDong = $row['hanhDong'];
        
        error_log("=== PARSE DEBUG ===");
        error_log("Raw hanhDong: " . $hanhDong);
        error_log("Length: " . strlen($hanhDong));
        
        // Mặc định
        $thietBiText = 'Thiết bị';
        $mucDich = 'Chưa cập nhật';
        $ngayMuon = date('Y-m-d', strtotime($row['thoiGian']));
        $ngayTra = date('Y-m-d', strtotime($row['thoiGian'] . ' +7 days'));
        $maPhieu = 'PM' . substr($row['maLog'], -3);
        
        // Map mục đích ngắn gọn về đầy đủ
        $mucDichMap = [
            'DH' => 'Dạy học',
            'HPH' => 'Họp phụ huynh', 
            'HN' => 'Hội nghị',
            'TC' => 'Thi cử',
            'HDNK' => 'Hoạt động ngoại khóa'
        ];
        
        // New compact format: PM:PM123|TB:Máy SL:1|MD:DH|NM:20/12|NT:27/12|TT:Đang mượn
        if (preg_match('/PM:(PM\w+)\|TB:(.+?)\|MD:(.+?)\|NM:(.+?)\|NT:(.+?)\|TT:(.+)/', $hanhDong, $matches)) {
            $maPhieu = $matches[1];
            $thietBiRaw = $matches[2];
            $mucDichRaw = $matches[3];
            $ngayMuonRaw = $matches[4];
            $ngayTraRaw = $matches[5];
            $trangThai = $matches[6];
            
            // Expand thiết bị names
            $thietBiParts = explode(',', $thietBiRaw);
            $thietBiExpanded = [];
            foreach ($thietBiParts as $part) {
                $part = trim($part);
                if (strpos($part, 'Máy') === 0) {
                    $thietBiExpanded[] = str_replace('Máy', 'Máy tính Dell', $part);
                } else if (strpos($part, 'Loa') === 0) {
                    $thietBiExpanded[] = str_replace('Loa', 'Loa Bluetooth', $part);
                } else {
                    $thietBiExpanded[] = $part;
                }
            }
            $thietBiText = implode(', ', $thietBiExpanded);
            
            // Expand mục đích
            $mucDich = isset($mucDichMap[$mucDichRaw]) ? $mucDichMap[$mucDichRaw] : $mucDichRaw;
            
            // Expand dates - hỗ trợ năm 2026
            $currentYear = date('Y');
            if (strpos($ngayMuonRaw, '/') !== false) {
                $parts = explode('/', $ngayMuonRaw);
                // Nếu có 3 phần (dd/mm/yyyy) thì dùng năm đó, nếu không thì dùng năm hiện tại
                if (count($parts) == 3) {
                    $year = $parts[2];
                    $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                } else {
                    $year = $currentYear;
                    $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                }
                $ngayMuon = $year . '-' . $month . '-' . $day;
            }
            if (strpos($ngayTraRaw, '/') !== false) {
                $parts = explode('/', $ngayTraRaw);
                // Nếu có 3 phần (dd/mm/yyyy) thì dùng năm đó, nếu không thì dùng năm hiện tại
                if (count($parts) == 3) {
                    $year = $parts[2];
                    $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                } else {
                    $year = $currentYear;
                    $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                }
                $ngayTra = $year . '-' . $month . '-' . $day;
            }
            
            error_log("✅ New compact format: mucDich = " . $mucDich . ", trangThai = " . $trangThai);
        }
        // Fallback for format without status
        else if (preg_match('/PM:(PM\w+)\|TB:(.+?)\|MD:(.+?)\|NM:(.+?)\|NT:(.+)/', $hanhDong, $matches)) {
            $maPhieu = $matches[1];
            $thietBiRaw = $matches[2];
            $mucDichRaw = $matches[3];
            $ngayMuonRaw = $matches[4];
            $ngayTraRaw = $matches[5];
            $trangThai = 'Chờ duyệt'; // Default status for old records
            
            // Same expansion logic as above
            $thietBiParts = explode(',', $thietBiRaw);
            $thietBiExpanded = [];
            foreach ($thietBiParts as $part) {
                $part = trim($part);
                if (strpos($part, 'Máy') === 0) {
                    $thietBiExpanded[] = str_replace('Máy', 'Máy tính Dell', $part);
                } else if (strpos($part, 'Loa') === 0) {
                    $thietBiExpanded[] = str_replace('Loa', 'Loa Bluetooth', $part);
                } else {
                    $thietBiExpanded[] = $part;
                }
            }
            $thietBiText = implode(', ', $thietBiExpanded);
            
            $mucDich = isset($mucDichMap[$mucDichRaw]) ? $mucDichMap[$mucDichRaw] : $mucDichRaw;
            
            $currentYear = date('Y');
            if (strpos($ngayMuonRaw, '/') !== false) {
                $parts = explode('/', $ngayMuonRaw);
                // Nếu có 3 phần (dd/mm/yyyy) thì dùng năm đó, nếu không thì dùng năm hiện tại
                if (count($parts) == 3) {
                    $year = $parts[2];
                    $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                } else {
                    $year = $currentYear;
                    $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                }
                $ngayMuon = $year . '-' . $month . '-' . $day;
            }
            if (strpos($ngayTraRaw, '/') !== false) {
                $parts = explode('/', $ngayTraRaw);
                // Nếu có 3 phần (dd/mm/yyyy) thì dùng năm đó, nếu không thì dùng năm hiện tại
                if (count($parts) == 3) {
                    $year = $parts[2];
                    $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                } else {
                    $year = $currentYear;
                    $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                }
                $ngayTra = $year . '-' . $month . '-' . $day;
            }
            
            error_log("✅ Old format without status: mucDich = " . $mucDich);
        }
        // Fallback for partial matches
        else if (preg_match('/PM:(PM\w+)\|TB:(.+?)\|MD:(.+)/', $hanhDong, $matches)) {
            $maPhieu = $matches[1];
            $thietBiText = $matches[2];
            $mucDichRaw = $matches[3];
            $mucDich = isset($mucDichMap[$mucDichRaw]) ? $mucDichMap[$mucDichRaw] : $mucDichRaw;
            error_log("✅ Partial format: mucDich = " . $mucDich);
        }
        // Old format fallbacks
        else if (preg_match('/Phieu muon (PM\w+): (.+?) \| MD: (.+?) \| NM: (.+?) \| NT: (.+)/', $hanhDong, $matches)) {
            $maPhieu = $matches[1];
            $thietBiText = $matches[2];
            $mucDich = $matches[3];
            $ngayMuon = $matches[4];
            $ngayTra = $matches[5];
            error_log("✅ Old MD format: mucDich = " . $mucDich);
        }
        else if (preg_match('/Phieu muon (PM\w+): (.+?) \| Muc dich: (.+?) \| Ngay muon: (.+?) \| Ngay tra: (.+)/', $hanhDong, $matches)) {
            $maPhieu = $matches[1];
            $thietBiText = $matches[2];
            $mucDich = $matches[3];
            $ngayMuon = $matches[4];
            $ngayTra = $matches[5];
            error_log("✅ Old full format: mucDich = " . $mucDich);
        }
        else if (preg_match('/Phieu muon (PM\w+): (.+)/', $hanhDong, $matches)) {
            $maPhieu = $matches[1];
            $thietBiText = $matches[2];
            $mucDich = 'Mượn thiết bị';
            error_log("⚠️ Basic format: mucDich = " . $mucDich);
        }
        else {
            error_log("❌ NO PATTERN MATCHED!");
        }
        
        error_log("Final: maPhieu=$maPhieu, thietBi=$thietBiText, mucDich=$mucDich");
        
        return [
            'id' => $row['maLog'],
            'ma' => $maPhieu,
            'thietbi' => $thietBiText,
            'ngaymuon' => $ngayMuon,
            'ngaytra' => $ngayTra,
            'mucdich' => $mucDich,
            'trangthai' => $trangThai ?? 'Chờ duyệt'
        ];
    }
}
?>