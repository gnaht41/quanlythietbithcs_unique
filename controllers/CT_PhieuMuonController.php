<?php
// controllers/CT_PhieuMuonController.php
session_start();

require_once __DIR__ . '/../models/CT_PhieuMuonModel.php';

class CT_PhieuMuonController
{
    private $model;

    public function __construct()
    {
        $this->model = new CT_PhieuMuonModel();
    }

    // Xử lý các request API
    public function handleRequest()
    {
        // Bật error reporting để debug
        error_reporting(E_ALL);
        ini_set('display_errors', 0); // Không hiển thị lỗi trực tiếp

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            $this->sendResponse(false, 'Vui lòng đăng nhập để tiếp tục');
            return;
        }

        // Kiểm tra quyền giáo viên (maVT = 2)
        if (!isset($_SESSION['maVT']) || $_SESSION['maVT'] != 2) {
            $this->sendResponse(false, 'Không có quyền truy cập chức năng này');
            return;
        }

        $action = $_GET['action'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'];

        try {
            switch ($action) {
                case 'tao-phieu-muon':
                    if ($method === 'POST') {
                        $this->taoPhieuMuon();
                    } else {
                        $this->sendResponse(false, 'Method không được hỗ trợ');
                    }
                    break;

                case 'danh-sach-phieu-muon':
                    if ($method === 'GET') {
                        $this->layDanhSachPhieuMuon();
                    } else {
                        $this->sendResponse(false, 'Method không được hỗ trợ');
                    }
                    break;

                case 'chi-tiet-phieu-muon':
                    if ($method === 'GET') {
                        $this->layChiTietPhieuMuon();
                    } else {
                        $this->sendResponse(false, 'Method không được hỗ trợ');
                    }
                    break;

                case 'cap-nhat-phieu-muon':
                    if ($method === 'POST') {
                        $this->capNhatPhieuMuon();
                    } else {
                        $this->sendResponse(false, 'Method không được hỗ trợ');
                    }
                    break;

                case 'huy-phieu-muon':
                    if ($method === 'POST') {
                        $this->huyPhieuMuon();
                    } else {
                        $this->sendResponse(false, 'Method không được hỗ trợ');
                    }
                    break;

                case 'danh-sach-thiet-bi':
                    if ($method === 'GET') {
                        $this->layDanhSachThietBi();
                    } else {
                        $this->sendResponse(false, 'Method không được hỗ trợ');
                    }
                    break;

                case 'danh-sach-muc-dich':
                    if ($method === 'GET') {
                        $this->layDanhSachMucDich();
                    } else {
                        $this->sendResponse(false, 'Method không được hỗ trợ');
                    }
                    break;

                default:
                    $this->sendResponse(false, 'Action không hợp lệ');
                    break;
            }
        } catch (Exception $e) {
            // Log lỗi để debug
            error_log("CT_PhieuMuonController Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->sendResponse(false, 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    // API tạo phiếu mượn mới
    private function taoPhieuMuon()
    {
        // Lấy dữ liệu từ POST
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        // Debug: Log dữ liệu nhận được
        error_log("Dữ liệu tạo phiếu mượn: " . json_encode($input));

        // Validate dữ liệu đầu vào
        if (
            empty($input['ngayMuon']) || empty($input['ngayTraDuKien']) ||
            empty($input['mucDich']) || empty($input['thietBi'])
        ) {
            error_log("Validation failed - Missing data: " . json_encode([
                'ngayMuon' => $input['ngayMuon'] ?? 'missing',
                'ngayTraDuKien' => $input['ngayTraDuKien'] ?? 'missing',
                'mucDich' => $input['mucDich'] ?? 'missing',
                'thietBi' => isset($input['thietBi']) ? 'present' : 'missing'
            ]));
            $this->sendResponse(false, 'Thiếu thông tin bắt buộc');
            return;
        }

        // Thêm mã người dùng từ session
        $input['maND'] = $_SESSION['maND'];

        // Debug: Log dữ liệu trước khi gửi vào model
        error_log("Dữ liệu gửi vào model: " . json_encode($input));

        // Gọi model để tạo phiếu mượn
        $result = $this->model->taoPhieuMuon($input);

        // Debug: Log kết quả từ model
        error_log("Kết quả từ model: " . json_encode($result));

        // Ghi log nếu tạo phiếu thành công
        if ($result['success'] && isset($result['data']['maPhieu'])) {
            try {
                require_once __DIR__ . '/../models/QT_Log.php';
                $log = new Log();

                $chiTiet = [
                    'ngayMuon' => $input['ngayMuon'],
                    'ngayTraDuKien' => $input['ngayTraDuKien'],
                    'mucDich' => $input['mucDich'],
                    'soLuongThietBi' => count($input['thietBi']),
                    'danhSachThietBi' => array_column($input['thietBi'], 'maTB')
                ];

                $log->logTaoPhieuMuon($_SESSION['maND'], $result['data']['maPhieu'], $chiTiet);
            } catch (Exception $e) {
                error_log("Lỗi ghi log tạo phiếu mượn: " . $e->getMessage());
            }
        }

        $this->sendResponse($result['success'], $result['message'], $result['data'] ?? null);
    }

    // API lấy danh sách phiếu mượn của giáo viên
    private function layDanhSachPhieuMuon()
    {
        $maND = $_SESSION['maND'];
        $result = $this->model->layDanhSachPhieuMuon($maND);

        // Debug: Log dữ liệu trả về
        error_log("Dữ liệu phiếu mượn: " . json_encode($result['data']));

        // Debug: Kiểm tra từng item có mucDich không
        if ($result['data']) {
            foreach ($result['data'] as $item) {
                error_log("Phiếu {$item['maPhieu']}: mucDich = " . ($item['mucDich'] ?? 'NULL'));
            }
        }

        $this->sendResponse($result['success'], 'Lấy danh sách thành công', $result['data']);
    }

    // API lấy chi tiết một phiếu mượn
    private function layChiTietPhieuMuon()
    {
        $maPhieu = $_GET['maPhieu'] ?? '';

        if (empty($maPhieu) || !is_numeric($maPhieu)) {
            $this->sendResponse(false, 'Mã phiếu mượn không hợp lệ');
            return;
        }

        $maND = $_SESSION['maND'];
        $result = $this->model->layChiTietPhieuMuon($maPhieu, $maND);

        if ($result['success']) {
            $this->sendResponse(true, 'Lấy chi tiết thành công', $result['data']);
        } else {
            $this->sendResponse(false, $result['message']);
        }
    }

    // API cập nhật phiếu mượn
    private function capNhatPhieuMuon()
    {
        $maPhieu = $_GET['maPhieu'] ?? $_POST['maPhieu'] ?? '';

        if (empty($maPhieu) || !is_numeric($maPhieu)) {
            $this->sendResponse(false, 'Mã phiếu mượn không hợp lệ');
            return;
        }

        // Lấy dữ liệu từ POST
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        // Validate dữ liệu đầu vào
        if (
            empty($input['ngayMuon']) || empty($input['ngayTraDuKien']) ||
            empty($input['mucDich']) || empty($input['thietBi'])
        ) {
            $this->sendResponse(false, 'Thiếu thông tin bắt buộc');
            return;
        }

        $maND = $_SESSION['maND'];
        $result = $this->model->capNhatPhieuMuon($maPhieu, $input, $maND);
        $this->sendResponse($result['success'], $result['message']);
    }

    // API hủy phiếu mượn
    private function huyPhieuMuon()
    {
        $maPhieu = $_GET['maPhieu'] ?? $_POST['maPhieu'] ?? '';

        if (empty($maPhieu) || !is_numeric($maPhieu)) {
            $this->sendResponse(false, 'Mã phiếu mượn không hợp lệ');
            return;
        }

        $maND = $_SESSION['maND'];
        $result = $this->model->huyPhieuMuon($maPhieu, $maND);
        $this->sendResponse($result['success'], $result['message']);
    }

    // API lấy danh sách thiết bị có thể mượn
    private function layDanhSachThietBi()
    {
        $result = $this->model->layDanhSachThietBi();
        $this->sendResponse($result['success'], 'Lấy danh sách thiết bị thành công', $result['data']);
    }

    // API lấy danh sách mục đích thường dùng
    private function layDanhSachMucDich()
    {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $result = $this->model->layDanhSachMucDich($limit);
        $this->sendResponse($result['success'], 'Lấy danh sách mục đích thành công', $result['data']);
    }

    // Gửi response JSON
    private function sendResponse($success, $message, $data = null)
    {
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Phương thức để gọi từ bên ngoài (cho AJAX)
    public static function processRequest()
    {
        $controller = new self();
        $controller->handleRequest();
    }
}

// Xử lý request nếu file được gọi trực tiếp
if (basename($_SERVER['PHP_SELF']) === 'CT_PhieuMuonController.php') {
    CT_PhieuMuonController::processRequest();
}
