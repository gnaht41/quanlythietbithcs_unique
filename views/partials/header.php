<?php
// File: views/partials/header.php
/*
 * Các biến này ($page_title, $user_name, $css_file)
 * phải được định nghĩa ở file cha (file gọi include)
 * TRƯỚC KHI gọi require_once 'partials/header.php';
 */
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?php echo htmlspecialchars($page_title ?? 'Bảng điều khiển'); ?></title>
    <link rel="stylesheet" href="css/main.css" />
    <?php if (isset($css_file) && !empty($css_file)): ?>
    <link rel="stylesheet" href="css/<?php echo htmlspecialchars($css_file); ?>" />
    <?php endif; ?>
    <!-- FAVICON - LOGO NHỎ TRÊN TAB TRÌNH DUYỆT -->
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Nếu bạn dùng file PNG thì thay bằng dòng dưới -->
    <link rel="icon" type="image/png" href="img/UniqueLogo.jpg">
</head>

<body>
    <header>
        <div class="logo">
            <img src="img/UniqueLogo.jpg" alt="Trường THCS Unique" class="logo-img">
            <span class="logo-text">UNIQUE MANAGEMENT</span>
        </div>
        <div class="user-info">
            <span>Xin chào, <?php echo htmlspecialchars($user_name ?? 'Người dùng'); ?>!</span>

            <a href="../index.php?action=logout" id="nut-dang-xuat-link"
                style="color: white; background-color: #e74c3c; padding: 8px 12px; border-radius: 8px; text-decoration: none;">Đăng
                xuất</a>
        </div>
    </header>