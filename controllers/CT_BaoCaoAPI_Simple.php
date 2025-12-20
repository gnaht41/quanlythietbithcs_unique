<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../models/CT_BaoCaoHuHongModel.php';

$action = $_GET['action'] ?? '';
$model = new CT_BaoCaoHuHongModel();

try {
    switch ($action) {
        case 'lay-thiet-bi-dang-muon':
            $data = $model->layThietBiDangMuon();
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'tao-bao-cao':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) throw new Exception('Không có dữ liệu');
            
            $model->taoBaoCao($input);
            echo json_encode(['success' => true, 'message' => 'Tạo báo cáo thành công']);
            break;

        case 'danh-sach-bao-cao':
            $data = $model->layDanhSach();
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'chi-tiet-bao-cao':
            $id = $_GET['id'] ?? 0;
            $data = $model->layChiTiet($id);
            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy báo cáo']);
            }
            break;

        case 'cap-nhat-bao-cao':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['maBaoCao'])) {
                throw new Exception('Thiếu thông tin báo cáo');
            }
            
            $model->capNhat($input['maBaoCao'], $input);
            echo json_encode(['success' => true, 'message' => 'Cập nhật báo cáo thành công']);
            break;

        case 'xoa-bao-cao':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['maBaoCao'] ?? 0;
            
            $model->xoa($id);
            echo json_encode(['success' => true, 'message' => 'Xóa thành công']);
            break;

        default:
            throw new Exception('Action không hợp lệ');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>