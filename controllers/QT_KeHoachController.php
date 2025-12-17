<?php
session_start();

require_once '../models/QT_KeHoachModel.php';
require_once '../models/QT_Log.php';

$model = new QT_KeHoachModel();
$log = new Log();

$maND = $_SESSION['maND'] ?? 0;
if (!$maND) {
    header('Location: ../views/dang-nhap.php');
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

/* ================= LƯU PHIẾU ================= */
if ($action === 'save') {
    $maMS = $_POST['maMS'] ?? null;
    $maTB = $_POST['maTB'] ?? [];
    $soLuong = $_POST['soLuong'] ?? [];

    // Lọc thiết bị hợp lệ
    $chiTietHopLe = [];
    foreach ($maTB as $i => $tb) {
        $sl = intval($soLuong[$i] ?? 0);
        if ($tb && $sl > 0) {
            $chiTietHopLe[] = ['maTB' => $tb, 'soLuong' => $sl];
        }
    }

    // Nếu không còn thiết bị nào
    if (empty($chiTietHopLe)) {
        if ($maMS) {
            $phieu = $model->getById($maMS);
            if ($phieu && $phieu['trangThai'] === 'Chờ duyệt') {
                $model->delete($maMS); // Xóa luôn phiếu
                $log->ghiLog($maND, 'DELETE', 'KeHoachMuaSam', $maMS);
            }
        }
        header("Location: ../views/to-truong.php?tab=ke-hoach-mua-sam");
        exit;
    }

    // Tạo mới phiếu
    if (!$maMS) {
        $maMS = $model->create($maND);
        $log->ghiLog($maND, 'INSERT', 'KeHoachMuaSam', $maMS);
    } else {
        // Chỉ cho phép sửa nếu đang Chờ duyệt
        $phieu = $model->getById($maMS);
        if (!$phieu || $phieu['trangThai'] !== 'Chờ duyệt') {
            header("Location: ../views/to-truong.php?tab=ke-hoach-mua-sam");
            exit;
        }
    }

    // Ghi chi tiết mới
    $model->clearChiTiet($maMS);
    foreach ($chiTietHopLe as $ct) {
        $model->addChiTiet($maMS, $ct['maTB'], $ct['soLuong']);
    }

    $log->ghiLog($maND, 'UPDATE', 'KeHoachMuaSam', $maMS);
    header("Location: ../views/to-truong.php?tab=ke-hoach-mua-sam");
    exit;
}

/* ================= XOÁ PHIẾU (từ nút Xóa) ================= */
if ($action === 'delete') {
    $maMS = $_GET['maMS'] ?? 0;
    if ($maMS) {
        $phieu = $model->getById($maMS);
        if ($phieu && $phieu['trangThai'] === 'Chờ duyệt') {
            $model->delete($maMS);
            $log->ghiLog($maND, 'DELETE', 'KeHoachMuaSam', $maMS);
        }
    }
    header("Location: ../views/to-truong.php?tab=ke-hoach-mua-sam");
    exit;
}