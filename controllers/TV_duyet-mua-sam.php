<?php
// controllers/TV_duyet-mua-sam.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/TV_duyet-mua-sam.php';

function tv_ms_require_hieu_truong_json()
{
    if (!isset($_SESSION['loggedin'], $_SESSION['maVT']) || $_SESSION['loggedin'] !== true || (int)$_SESSION['maVT'] !== 5) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Bạn không có quyền.']);
        exit;
    }
}

/**
 * AJAX endpoints:
 *  - GET  ?ajax=detail&maMS=1
 *  - POST ?ajax=update  (action=duyet_mua_sam, maMS, decision, ghiChu)
 */
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');
    tv_ms_require_hieu_truong_json();

    $model = new TV_DuyetMuaSamModel();
    $ajax = $_GET['ajax'];

    try {
        if ($ajax === 'detail') {
            $maMS = isset($_GET['maMS']) ? (int)$_GET['maMS'] : 0;
            if ($maMS <= 0) {
                echo json_encode(['success' => false, 'message' => 'Thiếu mã kế hoạch.']);
                exit;
            }

            $detail = $model->getChiTietKeHoach($maMS);
            if (empty($detail['header'])) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy kế hoạch.']);
                exit;
            }

            echo json_encode(['success' => true, 'data' => $detail]);
            exit;
        }

        if ($ajax === 'update') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method không hợp lệ.']);
                exit;
            }

            $maMS = (int)($_POST['maMS'] ?? 0);
            $decision = $_POST['decision'] ?? '';
            $ghiChu = trim($_POST['ghiChu'] ?? '');
            $maND = (int)($_SESSION['maND'] ?? 0);

            $result = $model->capNhatTrangThai($maMS, $decision, $ghiChu, $maND);
            echo json_encode($result);
            exit;
        }

        echo json_encode(['success' => false, 'message' => 'AJAX action không hợp lệ.']);
        exit;
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Lỗi server.', 'error' => $e->getMessage()]);
        exit;
    }
}

// ===== Controller dùng cho View (MVC) =====
class TV_DuyetMuaSamController
{
    private $model;

    public function __construct()
    {
        $this->model = new TV_DuyetMuaSamModel();
    }

    private function requireHieuTruong(): bool
    {
        return isset($_SESSION['loggedin'], $_SESSION['maVT'])
            && $_SESSION['loggedin'] === true
            && (int)$_SESSION['maVT'] === 5;
    }

    public function getViewModel(): array
    {
        $data = [
            'keyword' => $_GET['q'] ?? '',
            'status'  => $_GET['status'] ?? '',
            'list'    => [],
            'message' => '',
            'message_type' => 'info',
        ];

        if (!$this->requireHieuTruong()) {
            $data['message'] = 'Bạn không có quyền truy cập chức năng này.';
            $data['message_type'] = 'error';
            return $data;
        }

        $data['list'] = $this->model->getDanhSachKeHoach($data['keyword'], $data['status']);
        return $data;
    }
}
