<?php
// controllers/ThanhLyController.php
session_start();
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/ThanhLy.php';

$db = (new Database())->getConnection();
$model = new ThanhLy($db);

// Chỉ cho Hiệu trưởng (maVT = 2)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || (int)($_SESSION['maVT'] ?? 0) !== 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'detail': {
            $maTL = (int)($_GET['maTL'] ?? 0);
            if ($maTL <= 0) {
                echo json_encode(['success' => false, 'message' => 'Thiếu mã đề xuất']);
                break;
            }
            $rows = $model->getDetail($maTL);
            echo json_encode(['success' => true, 'data' => $rows]);
            break;
        }

        case 'update': {
            $maTL      = (int)($_POST['maTL'] ?? 0);
            $trangThai = trim($_POST['trangThai'] ?? '');
            $ghiChu    = trim($_POST['ghiChu'] ?? '');

            if ($maTL <= 0 || ($trangThai !== 'Đã duyệt' && $trangThai !== 'Từ chối')) {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
                break;
            }
            if ($trangThai === 'Từ chối' && $ghiChu === '') {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập lý do từ chối']);
                break;
            }

            $ok = $model->updateStatus($maTL, $trangThai, $ghiChu);
            if ($ok) {
                $hanhDong = ($trangThai === 'Đã duyệt') ? 'Duyệt thanh lý' : 'Từ chối thanh lý';
                $model->writeLog($_SESSION['maND'], $hanhDong, 'KeHoachThanhLy#'.$maTL);
                echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể lưu, vui lòng thử lại']);
            }
            break;
        }

        default:
            echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: '.$e->getMessage()]);
}
