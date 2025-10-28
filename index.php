<?php
// index.php (Thư mục gốc)
// session_start(); // Session đã được start trong AuthController

require_once 'controllers/AuthController.php';

$action = $_GET['action'] ?? 'login'; // Mặc định là hiển thị form login

$authController = new AuthController();

switch ($action) {
    case 'doLogin': // Xử lý dữ liệu từ form đăng nhập
        $authController->handleLogin();
        break;
    case 'logout':
        $authController->handleLogout();
        break;
    case 'login': // Hiển thị form đăng nhập
    default:
        // Nếu đã đăng nhập, chuyển hướng đến trang tương ứng
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            // Ánh xạ lại để chuyển hướng nếu truy cập index.php trực tiếp khi đã đăng nhập
            $role_pages_index = [
                1 => 'quan-tri-vien.php',
                2 => 'hieu-truong.php',
                3 => 'to-truong.php',
                4 => 'giao-vien.php',
                5 => 'nhan-vien-thiet-bi.php'
            ];
            $maVT = $_SESSION['maVT'] ?? null;
            if ($maVT && isset($role_pages_index[$maVT])) {
                header("Location: views/" . $role_pages_index[$maVT]);
                exit();
            } else {
                // Nếu vai trò không xác định, đăng xuất và về trang login
                $authController->handleLogout();
            }
        } else {
            // Nếu chưa đăng nhập, hiển thị form login
            $error_msg = '';
            if (isset($_GET['error']) && $_GET['error'] == 1) {
                // Có thể lấy lỗi chi tiết từ session flash nếu bạn triển khai
                $error_msg = 'Sai tài khoản hoặc mật khẩu!';
            }
            $authController->showLoginForm($error_msg);
        }
        break;
}
