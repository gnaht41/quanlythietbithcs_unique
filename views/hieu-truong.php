<?php
// views/hieu-truong.php (File layout chính)
session_start();

// --- KIỂM TRA SESSION ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['maVT'])) { // Thêm check vai trò: && $_SESSION['maVT'] == 2
    header("Location: ../index.php?action=login");
    exit;
}

// --- XÁC ĐỊNH TAB ACTIVE TỪ URL --- // <<< THÊM DÒNG NÀY
$active_tab = $_GET['tab'] ?? 'tong-quan';

// --- CÀI ĐẶT BIẾN ---
$page_title = 'Bảng điều khiển Hiệu trưởng';
$user_name = 'Hiệu trưởng';
$css_file = 'hieu-truong.css';
$js_file = 'hieu-truong.js'; // File JS riêng

// --- INCLUDE HEADER ---
require_once 'partials/header.php';
?>

<div class="khoi-chua">

    <?php // --- INCLUDE SIDEBAR ---
    // sidebar-hieutruong.php cần được cập nhật như hướng dẫn trước
    require_once 'partials/sidebar-hieutruong.php';
    ?>

    <main>
        <h1>Bảng điều khiển Hiệu trưởng</h1>

        <?php // --- INCLUDE CÁC TRANG CON ---
        // Các file này cần được cập nhật điều kiện style như hướng dẫn trước
        require_once 'pages_hieu-truong/tong-quan.php';
        require_once 'pages_hieu-truong/danh-sach-thiet-bi.php'; // Bạn sẽ copy nội dung vào đây
        require_once 'pages_hieu-truong/duyet-ke-hoach.php';
        require_once 'pages_hieu-truong/duyet-thanh-ly.php';   
        require_once 'pages_hieu-truong/bao-cao-thong-ke.php';
        ?>

    </main>

</div>

<?php // --- INCLUDE FOOTER ---
require_once 'partials/footer.php';
?>