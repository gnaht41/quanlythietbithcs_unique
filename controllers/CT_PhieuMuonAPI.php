<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/CT_ApiHelper.php';
require_once __DIR__ . '/../models/CT_PhieuMuonModel.php';

try {
    $maND = CT_ApiHelper::checkAuth();
    $action = CT_ApiHelper::getAction();
    $model = new CT_PhieuMuonModel();

    switch ($action) {
        case 'create':
            $ngayMuon = $_POST['ngaymuon'] ?? '';
            $ngayTra = $_POST['ngaytra'] ?? '';
            $mucDich = $_POST['mucdich'] ?? '';
            $tbCount = (int)($_POST['tb_count'] ?? 0);

            if (!$ngayMuon || !$ngayTra || !$mucDich || $tbCount == 0) {
                throw new Exception("Thiếu thông tin bắt buộc");
            }

            $tbText = CT_ApiHelper::parseThietBi($tbCount);
            if (empty($tbText)) throw new Exception('Không có thiết bị');

            if ($model->taoPhieu($maND, $ngayMuon, $ngayTra, $mucDich, $tbText)) {
                CT_ApiHelper::success('Tạo phiếu thành công!');
            } else {
                throw new Exception('Lỗi lưu log');
            }
            break;

        case 'list':
            $data = $model->layDanhSach($maND);
            CT_ApiHelper::success('OK', $data);
            break;

        case 'detail':
            $id = $_GET['id'] ?? 0;
            if (!$id) CT_ApiHelper::error('Thiếu ID phiếu');

            $data = $model->layChiTiet($maND, $id);
            if ($data) {
                CT_ApiHelper::success('OK', $data);
            } else {
                CT_ApiHelper::error('Không tìm thấy phiếu');
            }
            break;

        case 'update':
            $id = $_POST['id'] ?? 0;
            $ngayMuon = $_POST['ngaymuon'] ?? '';
            $ngayTra = $_POST['ngaytra'] ?? '';
            $mucDich = $_POST['mucdich'] ?? '';
            $tbCount = (int)($_POST['tb_count'] ?? 0);

            if (!$id || !$ngayMuon || !$ngayTra || !$mucDich || $tbCount == 0) {
                throw new Exception("Thiếu thông tin bắt buộc");
            }

            // Kiểm tra trạng thái phiếu
            $phieu = $model->layChiTiet($maND, $id);
            if (!$phieu) {
                throw new Exception("Không tìm thấy phiếu");
            }
            if ($phieu['trangthai'] === 'Đang mượn') {
                throw new Exception("Không thể sửa phiếu đang mượn");
            }

            $tbText = CT_ApiHelper::parseThietBi($tbCount);
            if (empty($tbText)) throw new Exception('Không có thiết bị');

            if ($model->capNhat($maND, $id, $ngayMuon, $ngayTra, $mucDich, $tbText)) {
                CT_ApiHelper::success('Cập nhật phiếu thành công!');
            } else {
                throw new Exception('Lỗi cập nhật');
            }
            break;

        case 'delete':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Phương thức không hợp lệ');

            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? 0;
            if (!$id) throw new Exception('Thiếu ID phiếu');

            if ($model->xoa($maND, $id)) {
                CT_ApiHelper::success("Đã xóa phiếu ID:$id");
            } else {
                throw new Exception("Lỗi xóa phiếu ID:$id");
            }
            break;

        default:
            CT_ApiHelper::success('OK');
    }
} catch (Exception $e) {
    CT_ApiHelper::error($e->getMessage());
}
