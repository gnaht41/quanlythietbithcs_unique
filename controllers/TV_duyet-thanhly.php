<?php
// controllers/TV_duyet-thanhly.php
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ Controller CHỈ require Model, KHÔNG require QT_Database
require_once __DIR__ . '/../models/TV_duyet-thanhly.php';

class TV_DuyetThanhLyController
{
    private $model;

    public function __construct()
    {
        $this->model = new TV_DuyetThanhLyModel();
    }

    private function isHieuTruong(): bool
    {
        return isset($_SESSION['loggedin'], $_SESSION['maVT'])
            && $_SESSION['loggedin'] === true
            && (int)$_SESSION['maVT'] === 5;
    }

    private function jsonFail(int $code, string $msg): void
    {
        http_response_code($code);
        echo json_encode(['success' => false, 'message' => $msg], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function handleAjax(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!$this->isHieuTruong()) $this->jsonFail(403, 'Bạn không có quyền.');

        try {
            $ajax = $_GET['ajax'] ?? '';

            if ($ajax === 'detail') {
                $maTL = isset($_GET['maTL']) ? (int)$_GET['maTL'] : 0;
                if ($maTL <= 0) $this->jsonFail(400, 'Thiếu mã thanh lý.');

                $data = $this->model->getChiTiet($maTL);
                if (empty($data['header'])) $this->jsonFail(404, 'Không tìm thấy phiếu.');

                echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if ($ajax === 'update') {
                $maTL = isset($_POST['maTL']) ? (int)$_POST['maTL'] : 0;
                $decision = trim($_POST['decision'] ?? '');

                if ($maTL <= 0) $this->jsonFail(400, 'Thiếu mã thanh lý.');
                if (!in_array($decision, ['approve', 'reject'], true)) $this->jsonFail(400, 'Quyết định không hợp lệ.');

                $newStatus = ($decision === 'approve') ? 'Đã duyệt' : 'Từ chối';
                $maND = (int)($_SESSION['maND'] ?? 0);

                $ok = $this->model->capNhatTrangThai($maTL, $newStatus, $maND);
                if (!$ok) $this->jsonFail(500, 'Cập nhật thất bại.');

                // ✅ Ghi nhật ký hệ thống
                $this->model->ghiLog($maND, 'DUYET', 'KeHoachThanhLy', $maTL);

                echo json_encode(['success' => true, 'newStatus' => $newStatus], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $this->jsonFail(400, 'AJAX action không hợp lệ.');
        } catch (Throwable $e) {
            $this->jsonFail(500, 'Lỗi server: ' . $e->getMessage());
        }
    }

    public function getViewModel(): array
    {
        $vm = [
            'keyword' => $_GET['q'] ?? '',
            'status'  => $_GET['status'] ?? '',
            'list'    => [],
            'message' => '',
            'message_type' => 'info'
        ];

        if (!$this->isHieuTruong()) {
            $vm['message'] = 'Bạn không có quyền truy cập chức năng này.';
            $vm['message_type'] = 'error';
            return $vm;
        }

        $vm['list'] = $this->model->getDanhSach($vm['keyword'], $vm['status']);
        return $vm;
    }
}

// ✅ gọi ajax trực tiếp
if (isset($_GET['ajax'])) {
    (new TV_DuyetThanhLyController())->handleAjax();
}
