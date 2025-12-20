<?php
class CT_ApiHelper {
    public static function checkAuth() {
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            self::error('Chưa đăng nhập');
        }
        return $_SESSION['maND'] ?? 1;
    }
    
    public static function getAction() {
        $action = $_GET['action'] ?? $_POST['action'] ?? '';
        if (!$action) {
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? '';
        }
        return $action;
    }
    
    public static function success($message, $data = null) {
        $response = ['success' => true, 'message' => $message];
        if ($data !== null) $response['data'] = $data;
        echo json_encode($response);
        exit;
    }
    
    public static function error($message) {
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
    
    public static function parseThietBi($tbCount) {
        $tbText = [];
        for ($i = 0; $i < $tbCount; $i++) {
            $ma = $_POST["tb_ma_$i"] ?? '';
            $ten = $_POST["tb_ten_$i"] ?? '';
            $sl = $_POST["tb_sl_$i"] ?? 1;
            if ($ma && $ten) $tbText[] = strip_tags($ten) . ' SL:' . $sl;
        }
        return $tbText;
    }
}
?>