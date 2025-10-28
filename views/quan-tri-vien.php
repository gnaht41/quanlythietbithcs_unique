<?php
// views/quan-tri-vien.php (File layout chính)
session_start();

// --- PHẦN KIỂM TRA QUAN TRỌNG ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['maVT'])) {
    header("Location: ../index.php?action=login");
    exit;
}

// --- CÀI ĐẶT BIẾN CHO HEADER/FOOTER ---
$page_title = 'Bảng điều khiển Quản trị viên';
$user_name = 'Quản trị viên';
$css_file = 'quan-tri-vien.css';
$js_file = 'quan-tri-vien.js';

// --- INCLUDE HEADER CHUNG ---
require_once 'partials/header.php';
?>

<div class="khoi-chua">

    <?php
    // Include thanh bên (left) của Admin
    require_once 'partials/sidebar-admin.php';
    ?>

    <main>
        <h1>Bảng điều khiển Quản trị viên</h1>

        <?php
        // Include các trang con (sections)
        require_once 'pages_quan-tri-vien/tong-quan.php';
        require_once 'pages_quan-tri-vien/danh-sach-thiet-bi.php';
        require_once 'pages_quan-tri-vien/ql-nguoi-dung.php';
        require_once 'pages_quan-tri-vien/phan-quyen.php';
        require_once 'pages_quan-tri-vien/nhat-ky.php';
        ?>

    </main>
</div>
<?php
// --- INCLUDE FOOTER CHUNG ---
require_once 'partials/footer.php';
?>