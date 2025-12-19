<?php
// API đơn giản cho báo cáo hư hỏng - Giáo viên (CT)
ob_start();
session_start();
ob_clean();

// Chỉ trả về JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Tắt tất cả error display
ini_set('display_errors', 0);
error_reporting(0);

try {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
        exit;
    }

    $action = $_GET['action'] ?? '';
    $maND = $_SESSION['maND'] ?? 1;

    if ($action === 'lay-thiet-bi-dang-muon') {
        // Trả về dữ liệu test
        $testData = [
            [
                'maPhieu' => 'PM-TEST-001',
                'ngayMuon' => date('Y-m-d'),
                'ngayTraDuKien' => date('Y-m-d', strtotime('+7 days')),
                'maTB' => 'TB-TEST-001',
                'tenTB' => 'Máy tính test',
                'donVi' => 'Chiếc',
                'soLuong' => 1
            ],
            [
                'maPhieu' => 'PM-TEST-002',
                'ngayMuon' => date('Y-m-d'),
                'ngayTraDuKien' => date('Y-m-d', strtotime('+5 days')),
                'maTB' => 'TB-TEST-002',
                'tenTB' => 'Máy chiếu test',
                'donVi' => 'Chiếc',
                'soLuong' => 1
            ]
        ];
        
        echo json_encode(['success' => true, 'data' => $testData]);
        exit;
    }

    if ($action === 'tao-bao-cao' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy dữ liệu
        $input = null;
        
        if (isset($_POST['json_data'])) {
            $input = json_decode($_POST['json_data'], true);
        } else {
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
        }
        
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Không có dữ liệu']);
            exit;
        }
        
        // Validate
        if (empty($input['maPhieu']) || empty($input['maTB']) || 
            empty($input['tinhTrang']) || empty($input['noiDungBaoCao'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
            exit;
        }
        
        // Lưu vào database
        try {
            require_once __DIR__ . '/../models/QT_Database.php';
            
            $db = new Database();
            $conn = $db->getConnection();
            
            if (!$conn) {
                throw new Exception('Không thể kết nối database');
            }
            
            // Kiểm tra và tạo cột chiTiet nếu cần
            $checkColumn = $conn->query("SHOW COLUMNS FROM BangGhiLog LIKE 'chiTiet'");
            if ($checkColumn->num_rows == 0) {
                $conn->query("ALTER TABLE BangGhiLog ADD COLUMN chiTiet TEXT");
            }
            
            // Tạo dữ liệu JSON
            $chiTiet = json_encode([
                'loai' => 'bao_cao_hu_hong',
                'maPhieu' => $input['maPhieu'],
                'maTB' => $input['maTB'],
                'tinhTrang' => $input['tinhTrang'],
                'noiDungBaoCao' => $input['noiDungBaoCao'],
                'thoiGian' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            
            // Insert vào BangGhiLog
            $sql = "INSERT INTO BangGhiLog (maND, hanhDong, doiTuong, doiTuongId, chiTiet) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Lỗi prepare: ' . $conn->error);
            }
            
            $hanhDong = "Báo cáo hư hỏng thiết bị: " . $input['maTB'];
            $doiTuong = "ThietBi";
            $doiTuongId = 0;
            
            $stmt->bind_param("issis", $maND, $hanhDong, $doiTuong, $doiTuongId, $chiTiet);
            
            if ($stmt->execute()) {
                $insertId = $conn->insert_id;
                
                // Ghi log báo cáo hư hỏng
                try {
                    require_once __DIR__ . '/../models/QT_Log.php';
                    $log = new Log();
                    $log->logBaoCaoHuHong($maND, $input['maTB'], [
                        'tenTB' => 'Thiết bị báo cáo',
                        'tinhTrang' => $input['tinhTrang'],
                        'noiDungBaoCao' => $input['noiDungBaoCao']
                    ]);
                } catch (Exception $logError) {
                    // Không làm gián đoạn quá trình chính nếu log thất bại
                    error_log("Lỗi ghi log báo cáo: " . $logError->getMessage());
                }
                
                echo json_encode(['success' => true, 'message' => 'Lưu báo cáo thành công', 'id' => $insertId]);
            } else {
                throw new Exception('Lỗi execute: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
        }
        
        exit;
    }

    if ($action === 'danh-sach-bao-cao') {
        // Trả về danh sách báo cáo từ BangGhiLog
        try {
            require_once __DIR__ . '/../models/QT_Database.php';
            
            $db = new Database();
            $conn = $db->getConnection();
            
            $sql = "SELECT maLog, thoiGian, hanhDong, chiTiet FROM BangGhiLog 
                    WHERE maND = ? AND hanhDong LIKE '%báo cáo hư hỏng%' 
                    ORDER BY thoiGian DESC LIMIT 10";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $maND);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $chiTiet = json_decode($row['chiTiet'], true);
                
                if ($chiTiet && isset($chiTiet['loai']) && $chiTiet['loai'] === 'bao_cao_hu_hong') {
                    $data[] = [
                        'maBaoCao' => $row['maLog'],
                        'maPhieu' => $chiTiet['maPhieu'] ?? 'N/A',
                        'maTB' => $chiTiet['maTB'] ?? 'N/A',
                        'tenTB' => $chiTiet['maTB'] ?? 'Thiết bị test',
                        'tinhTrang' => $chiTiet['tinhTrang'] ?? 'N/A',
                        'noiDungBaoCao' => $chiTiet['noiDungBaoCao'] ?? 'N/A',
                        'ngayBaoCao' => $row['thoiGian'],
                        'trangThai' => 'dang-xu-ly'
                    ];
                }
            }
            
            echo json_encode(['success' => true, 'data' => $data]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
        
        exit;
    }

    if ($action === 'chi-tiet-bao-cao') {
        $maBaoCao = $_GET['id'] ?? 0;
        
        if (!$maBaoCao) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID báo cáo']);
            exit;
        }
        
        try {
            require_once __DIR__ . '/../models/QT_Database.php';
            
            $db = new Database();
            $conn = $db->getConnection();
            
            $sql = "SELECT maLog, thoiGian, hanhDong, chiTiet FROM BangGhiLog 
                    WHERE maLog = ? AND maND = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $maBaoCao, $maND);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $chiTiet = json_decode($row['chiTiet'], true);
                
                if ($chiTiet && isset($chiTiet['loai']) && $chiTiet['loai'] === 'bao_cao_hu_hong') {
                    $data = [
                        'maBaoCao' => $row['maLog'],
                        'maPhieu' => $chiTiet['maPhieu'] ?? 'N/A',
                        'maTB' => $chiTiet['maTB'] ?? 'N/A',
                        'tenTB' => $chiTiet['maTB'] ?? 'Thiết bị test',
                        'tinhTrang' => $chiTiet['tinhTrang'] ?? 'N/A',
                        'noiDungBaoCao' => $chiTiet['noiDungBaoCao'] ?? 'N/A',
                        'ngayBaoCao' => $row['thoiGian'],
                        'trangThai' => 'dang-xu-ly'
                    ];
                    
                    echo json_encode(['success' => true, 'data' => $data]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Không tìm thấy chi tiết báo cáo']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy báo cáo']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
        
        exit;
    }

    if ($action === 'cap-nhat-bao-cao' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy dữ liệu
        $input = null;
        
        if (isset($_POST['json_data'])) {
            $input = json_decode($_POST['json_data'], true);
        } else {
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
        }
        
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Không có dữ liệu']);
            exit;
        }
        
        $maBaoCao = $input['maBaoCao'] ?? 0;
        
        if (!$maBaoCao) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID báo cáo']);
            exit;
        }
        
        // Validate
        if (empty($input['tinhTrang']) || empty($input['noiDungBaoCao'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
            exit;
        }
        
        // Cập nhật database
        try {
            require_once __DIR__ . '/../models/QT_Database.php';
            
            $db = new Database();
            $conn = $db->getConnection();
            
            // Lấy dữ liệu cũ
            $sql = "SELECT chiTiet FROM BangGhiLog WHERE maLog = ? AND maND = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $maBaoCao, $maND);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $chiTietCu = json_decode($row['chiTiet'], true);
                
                if ($chiTietCu && isset($chiTietCu['loai']) && $chiTietCu['loai'] === 'bao_cao_hu_hong') {
                    // Cập nhật dữ liệu mới
                    $chiTietMoi = $chiTietCu;
                    $chiTietMoi['tinhTrang'] = $input['tinhTrang'];
                    $chiTietMoi['noiDungBaoCao'] = $input['noiDungBaoCao'];
                    $chiTietMoi['capNhatLan'] = date('Y-m-d H:i:s');
                    
                    $chiTietJson = json_encode($chiTietMoi, JSON_UNESCAPED_UNICODE);
                    
                    // Update vào database
                    $updateSql = "UPDATE BangGhiLog SET chiTiet = ?, hanhDong = ? WHERE maLog = ? AND maND = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    
                    $hanhDongMoi = "Cập nhật báo cáo hư hỏng thiết bị: " . $chiTietCu['maTB'];
                    $updateStmt->bind_param("ssii", $chiTietJson, $hanhDongMoi, $maBaoCao, $maND);
                    
                    if ($updateStmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Cập nhật báo cáo thành công']);
                    } else {
                        throw new Exception('Lỗi cập nhật: ' . $updateStmt->error);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Báo cáo không hợp lệ']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy báo cáo']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
        }
        
        exit;
    }

    if ($action === 'xoa-bao-cao' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy dữ liệu
        $input = null;
        
        if (isset($_POST['json_data'])) {
            $input = json_decode($_POST['json_data'], true);
        } else {
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
        }
        
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Không có dữ liệu']);
            exit;
        }
        
        $maBaoCao = $input['maBaoCao'] ?? 0;
        
        if (!$maBaoCao) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID báo cáo']);
            exit;
        }
        
        // Xóa khỏi database
        try {
            require_once __DIR__ . '/../models/QT_Database.php';
            
            $db = new Database();
            $conn = $db->getConnection();
            
            // Kiểm tra quyền sở hữu trước khi xóa
            $checkSql = "SELECT maLog, chiTiet FROM BangGhiLog WHERE maLog = ? AND maND = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("ii", $maBaoCao, $maND);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkRow = $checkResult->fetch_assoc()) {
                $chiTiet = json_decode($checkRow['chiTiet'], true);
                
                if ($chiTiet && isset($chiTiet['loai']) && $chiTiet['loai'] === 'bao_cao_hu_hong') {
                    // Xóa báo cáo
                    $deleteSql = "DELETE FROM BangGhiLog WHERE maLog = ? AND maND = ?";
                    $deleteStmt = $conn->prepare($deleteSql);
                    $deleteStmt->bind_param("ii", $maBaoCao, $maND);
                    
                    if ($deleteStmt->execute()) {
                        if ($deleteStmt->affected_rows > 0) {
                            // Ghi log xóa báo cáo
                            try {
                                $logSql = "INSERT INTO BangGhiLog (maND, hanhDong, doiTuong, doiTuongId, chiTiet) VALUES (?, ?, ?, ?, ?)";
                                $logStmt = $conn->prepare($logSql);
                                
                                $hanhDongLog = "Xóa báo cáo hư hỏng thiết bị: " . ($chiTiet['maTB'] ?? 'N/A');
                                $doiTuong = "BaoCaoHuHong";
                                $doiTuongId = $maBaoCao;
                                $chiTietLog = json_encode([
                                    'loai' => 'xoa_bao_cao_hu_hong',
                                    'maBaoCaoGoc' => $maBaoCao,
                                    'thongTinBaoCao' => $chiTiet,
                                    'thoiGian' => date('Y-m-d H:i:s')
                                ], JSON_UNESCAPED_UNICODE);
                                
                                $logStmt->bind_param("issis", $maND, $hanhDongLog, $doiTuong, $doiTuongId, $chiTietLog);
                                $logStmt->execute();
                            } catch (Exception $logError) {
                                // Không làm gián đoạn quá trình chính nếu log thất bại
                                error_log("Lỗi ghi log xóa báo cáo: " . $logError->getMessage());
                            }
                            
                            echo json_encode(['success' => true, 'message' => 'Xóa báo cáo thành công']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Không tìm thấy báo cáo để xóa']);
                        }
                    } else {
                        throw new Exception('Lỗi xóa: ' . $deleteStmt->error);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Báo cáo không hợp lệ']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy báo cáo hoặc không có quyền xóa']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
        }
        
        exit;
    }

    // Action không hợp lệ
    echo json_encode(['success' => false, 'message' => 'Action không hợp lệ: ' . $action]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>