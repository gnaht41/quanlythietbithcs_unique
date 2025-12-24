<?php
// models/get_thietbi.php
// File helper để lấy danh sách thiết bị từ database

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/QT_Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        throw new Exception('Không thể kết nối database');
    }

    // Lấy danh sách thiết bị có thể mượn (soLuongKhaDung > 0)
    $sql = "SELECT 
                maTB,
                tenTB,
                donVi,
                soLuongTong,
                soLuongKhaDung,
                tinhTrang
            FROM thietbi 
            WHERE soLuongKhaDung > 0 
            AND isHidden = 0
            ORDER BY tenTB ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'maTB' => $row['maTB'],
            'tenTB' => $row['tenTB'],
            'donVi' => $row['donVi'],
            'soLuongTong' => $row['soLuongTong'],
            'soLuongKhaDung' => $row['soLuongKhaDung'],
            'tinhTrang' => $row['tinhTrang']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
