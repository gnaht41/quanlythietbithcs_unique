<?php
// controllers/QT_AuthController.php
session_start();

require_once __DIR__ . '/../models/QT_User.php';
require_once __DIR__ . '/../models/QT_Log.php';

class AuthController
{
    private $role_pages = [
        1 => 'quan-tri-vien.php',
        2 => 'giao-vien.php',
        3 => 'nhan-vien-thiet-bi.php',
        4 => 'to-truong.php',
        5 => 'hieu-truong.php'
    ];

    public function showLoginForm($error_message = '')
    {
        require_once __DIR__ . '/../views/dang-nhap.php';
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

            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['maND'] = $userData['maND'];
            $_SESSION['maVT'] = $maVT;

            // ===== GHI LOG ĐĂNG NHẬP =====
            $logModel = new Log();
            $logModel->ghiLog(
                $userData['maND'],
                'LOGIN',
                'TaiKhoan',
                $userData['maND']
            );

            if (isset($this->role_pages[$maVT])) {
                header("Location: views/" . $this->role_pages[$maVT]);
                exit();
            } else {
                session_destroy();
                $this->showLoginForm('Vai trò không hợp lệ.');
            }
        } else {
            $this->showLoginForm($loginResult['message']);
        }
    }

    public function handleLogout()
    {
        if (isset($_SESSION['maND'])) {
            $logModel = new Log();
            $logModel->ghiLog(
                $_SESSION['maND'],
                'LOGOUT',
                'TaiKhoan',
                $_SESSION['maND']
            );
        }

        session_unset();
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}