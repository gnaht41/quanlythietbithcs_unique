<?php
// controllers/TV_ket-qua-kiem-ke.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/TV_ket-qua-kiem-ke.php';

function tv_kk_require_hieu_truong_json()
{
    if (!isset($_SESSION['loggedin'], $_SESSION['maVT']) || $_SESSION['loggedin'] !== true || (int)$_SESSION['maVT'] !== 5) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Bạn không có quyền.']);
        exit;
    }
}

/**
 * AJAX:
 *  GET  ?ajax=detail&maKK=1   -> chi tiết kiểm kê
 */
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');
    tv_kk_require_hieu_truong_json();

    $model = new TV_KetQuaKiemKeModel();
    $ajax = $_GET['ajax'];

    try {
        if ($ajax === 'detail') {
            $maKK = isset($_GET['maKK']) ? (int)$_GET['maKK'] : 0;
            if ($maKK <= 0) {
                echo json_encode(['success' => false, 'message' => 'Thiếu mã kiểm kê.']);
                exit;
            }

            $detail = $model->getChiTietKiemKe($maKK);
            if (empty($detail['header'])) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy phiếu kiểm kê.']);
                exit;
            }

            echo json_encode(['success' => true, 'data' => $detail]);
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

// ===== Controller cho View =====
class TV_KetQuaKiemKeController
{
    private $model;

    public function __construct()
    {
        $this->model = new TV_KetQuaKiemKeModel();
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
            'list'    => [],
            'message' => '',
            'message_type' => 'info'
        ];

        if (!$this->requireHieuTruong()) {
            $data['message'] = 'Bạn không có quyền truy cập chức năng này.';
            $data['message_type'] = 'error';
            return $data;
        }

        $data['list'] = $this->model->getDanhSachKiemKe($data['keyword']);
        return $data;
    }
}
