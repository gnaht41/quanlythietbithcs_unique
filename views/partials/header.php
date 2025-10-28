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

    <link rel="stylesheet" href="css/<?php echo htmlspecialchars($css_file ?? 'default.css'); ?>" />
</head>

<body>
    <header>
        <div class="logo">Trường THCS XYZ</div>
        <div class="user-info">
            <span>Xin chào, <?php echo htmlspecialchars($user_name ?? 'Người dùng'); ?>!</span>

            <a href="../index.php?action=logout" id="nut-dang-xuat-link"
                style="color: white; background-color: #e74c3c; padding: 8px 12px; border-radius: 8px; text-decoration: none;">Đăng
                xuất</a>
        </div>
    </header>