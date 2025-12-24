<?php
session_start();

require_once __DIR__ . '/../models/TV_DuyetMuaSamModel.php';

header('Content-Type: application/json; charset=utf-8');

if (
    !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true ||
    !isset($_SESSION['maVT']) || $_SESSION['maVT'] != 5 ||
    !isset($_SESSION['maND'])
) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập hoặc không có quyền Hiệu trưởng.']);
    exit;
}

$model = new TV_DuyetMuaSamModel();

$action = $_POST['action'] ?? '';

if ($action !== 'duyet') {
    echo json_encode(['success' => false, 'message' => 'Action không hợp lệ.']);
    exit;
}

$maMS = (int)($_POST['maMS'] ?? 0);
$decision = $_POST['decision'] ?? '';
$maND = (int)$_SESSION['maND'];

if ($maMS <= 0 || !in_array($decision, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

$result = $model->duyetPhieu($maMS, $decision, $maND);

echo json_encode($result);
exit;
