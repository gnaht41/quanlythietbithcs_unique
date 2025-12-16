<?php
// controllers/QT_AuthController.php
session_start(); // Start session here
require_once __DIR__ . '/../models/QT_User.php'; // Path to User model

class AuthController
{

    // Map role IDs (maVT) to the correct PHP file names in the views directory
    // mapping chính xác theo qltb.sql
    private $role_pages = [
        1 => 'quan-tri-vien.php',       // Admin
        2 => 'giao-vien.php',           // Giáo viên
        3 => 'nhan-vien-thiet-bi.php',  // Nhân viên
        4 => 'to-truong.php',           // Tổ trưởng
        5 => 'hieu-truong.php'          // Ban giám hiệu (Hiệu trưởng)
    ];


    public function showLoginForm($error_message = '')
    {
        require_once __DIR__ . '/../views/dang-nhap.php'; // Path to login view
    }

    public function handleLogin()
    {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $this->showLoginForm('Vui lòng nhập đầy đủ tài khoản và mật khẩu.');
            return;
        }

        $userModel = new User();
        $loginResult = $userModel->checkLogin($username, $password);

        if ($loginResult['success']) {
            $userData = $loginResult['data'];
            $maVT = $userData['maVT'];

            // Store user info in session
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['maND'] = $userData['maND'];
            $_SESSION['maVT'] = $maVT;

            // Determine the redirect page based on role ID (maVT)
            if (isset($this->role_pages[$maVT])) {
                $redirect_page = $this->role_pages[$maVT];

                // --- !!! CORRECTED REDIRECTION PATH !!! ---
                // Redirect to the correct PHP file inside the 'views' directory
                header("Location: views/" . $redirect_page);
                exit(); // Stop script execution after redirect
                // --- !!! END CORRECTION !!! ---

            } else {
                session_unset();
                session_destroy();
                $this->showLoginForm('Vai trò không hợp lệ hoặc chưa được cấu hình.');
            }
        } else {
            // Show login form again with error message
            $this->showLoginForm($loginResult['message']);
        }
    }

    public function handleLogout()
    {
        session_unset();
        session_destroy();
        header("Location: index.php?action=login"); // Redirect back to login page
        exit();
    }
}