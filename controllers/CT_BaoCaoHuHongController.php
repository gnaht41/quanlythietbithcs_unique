<?php
// Bắt đầu output buffering để tránh HTML leak
ob_start();

// Bật error reporting để debug
error_reporting(E_ALL);
ini_set('display_errors', 0); // Không hiển thị lỗi trực tiếp để không làm hỏng JSON

session_start();

// Clear any previous output
ob_clean();

// Set header JSON ngay từ đầu
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../models/CT_BaoCaoHuHongModel.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi load model: ' . $e->getMessage()]);
    exit;
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

try {
    $model = new CT_BaoCaoHuHongModel();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi tạo model: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';
$maND = $_SESSION['maND'] ?? '';

try {
    switch ($action) {
        case 'lay-thiet-bi-dang-muon':
            if (empty($maND)) {
                throw new Exception('Mã người dùng không hợp lệ');
            }
            
            $result = $model->layThietBiDangMuon($maND);
            echo json_encode([
                'success' => true,
                'data' => $result ?: []
            ]);
            break;
            
        case 'tao-bao-cao':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không được hỗ trợ');
            }
            
            // Lấy dữ liệu từ nhiều nguồn
            $input = null;
            
            // Thử lấy từ form data trước
            if (isset($_POST['json_data'])) {
                $input = json_decode($_POST['json_data'], true);
            } 
            // Nếu không có, thử lấy từ raw input
            else {
                $rawInput = file_get_contents('php://input');
                if (!empty($rawInput)) {
                    $input = json_decode($rawInput, true);
                }
            }
            
            // Nếu vẫn không có, thử lấy trực tiếp từ POST
            if (!$input && !empty($_POST)) {
                $input = $_POST;
            }
            
            if (!$input) {
                throw new Exception('Không nhận được dữ liệu. POST: ' . json_encode($_POST) . ', Raw: ' . file_get_contents('php://input'));
            }
            
            // Validate dữ liệu
            if (empty($input['maPhieu']) || empty($input['maTB']) || 
                empty($input['tinhTrang']) || empty($input['noiDungBaoCao'])) {
                throw new Exception('Vui lòng điền đầy đủ thông tin. Dữ liệu nhận được: ' . json_encode($input));
            }
            
            $data = [
                'maPhieu' => $input['maPhieu'],
                'maTB' => $input['maTB'],
                'tinhTrang' => $input['tinhTrang'],
                'noiDungBaoCao' => $input['noiDungBaoCao']
            ];
            
            $result = $model->taoBaoCaoHuHong($data);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tạo báo cáo hư hỏng thành công'
                ]);
            } else {
                throw new Exception('Không thể tạo báo cáo hư hỏng');
            }
            break;
            
        case 'danh-sach-bao-cao':
            if (empty($maND)) {
                throw new Exception('Mã người dùng không hợp lệ');
            }
            
            $result = $model->layDanhSachBaoCao($maND);
            echo json_encode([
                'success' => true,
                'data' => $result ?: []
            ]);
            break;
            
        case 'chi-tiet-bao-cao':
            $maBaoCao = $_GET['maBaoCao'] ?? '';
            if (empty($maBaoCao)) {
                throw new Exception('Mã báo cáo không hợp lệ');
            }
            
            // Kiểm tra quyền sở hữu
            if (!$model->kiemTraQuyenSoHuu($maBaoCao, $maND)) {
                throw new Exception('Không có quyền truy cập báo cáo này');
            }
            
            $result = $model->layChiTietBaoCao($maBaoCao);
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                throw new Exception('Không tìm thấy báo cáo');
            }
            break;
            
        case 'cap-nhat-bao-cao':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không được hỗ trợ');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $maBaoCao = $input['maBaoCao'] ?? '';
            
            if (empty($maBaoCao)) {
                throw new Exception('Mã báo cáo không hợp lệ');
            }
            
            // Kiểm tra quyền sở hữu
            if (!$model->kiemTraQuyenSoHuu($maBaoCao, $maND)) {
                throw new Exception('Không có quyền sửa báo cáo này');
            }
            
            // Validate dữ liệu
            if (empty($input['tinhTrang']) || empty($input['noiDungBaoCao'])) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }
            
            $data = [
                'tinhTrang' => $input['tinhTrang'],
                'noiDungBaoCao' => $input['noiDungBaoCao']
            ];
            
            $result = $model->capNhatBaoCao($maBaoCao, $data);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật báo cáo thành công'
                ]);
            } else {
                throw new Exception('Không thể cập nhật báo cáo');
            }
            break;
            
        case 'xoa-bao-cao':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không được hỗ trợ');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $maBaoCao = $input['maBaoCao'] ?? '';
            
            if (empty($maBaoCao)) {
                throw new Exception('Mã báo cáo không hợp lệ');
            }
            
            // Kiểm tra quyền sở hữu
            if (!$model->kiemTraQuyenSoHuu($maBaoCao, $maND)) {
                throw new Exception('Không có quyền xóa báo cáo này');
            }
            
            $result = $model->xoaBaoCao($maBaoCao);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Xóa báo cáo thành công'
                ]);
            } else {
                throw new Exception('Không thể xóa báo cáo');
            }
            break;
            
        default:
            throw new Exception('Hành động không hợp lệ');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>