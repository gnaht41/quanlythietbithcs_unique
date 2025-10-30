<?php
// controllers/LogController.php
session_start();

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Log.php';

$db = (new Database())->getConnection();
$model = new LogModel($db);

// Chỉ Admin (maVT = 1)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || (int)($_SESSION['maVT'] ?? 0) !== 1) {
    http_response_code(403);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

// Lấy filter từ request
$filter = [
    'q'      => $_GET['q']      ?? $_POST['q']      ?? '',
    'from'   => $_GET['from']   ?? $_POST['from']   ?? '',
    'to'     => $_GET['to']     ?? $_POST['to']     ?? '',
    'role'   => $_GET['role']   ?? $_POST['role']   ?? '',
    'action' => $_GET['act']    ?? $_POST['act']    ?? '',
];

try {
    if ($action === 'export') {
        // Xuất CSV
        $rows = $model->getLogs($filter);
        $fname = 'nhatky_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$fname.'"');

        $out = fopen('php://output', 'w');
        // BOM để Excel hiển thị UTF-8
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($out, ['Thời gian', 'Người dùng', 'Vai trò', 'Hành động', 'Đối tượng', 'Kết quả']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['thoiGian'],
                $r['email'] ?: '',
                $r['tenVT'] ?: '',
                $r['hanhDong'] ?: '',
                $r['doiTuong'] ?: '',
                $r['ketQua'] ?: 'Thành công',
            ]);
        }
        fclose($out);
        exit;
    }

    // Mặc định: trả JSON danh sách
    header('Content-Type: application/json; charset=UTF-8');
    $rows = $model->getLogs($filter);
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: '.$e->getMessage()]);
}
