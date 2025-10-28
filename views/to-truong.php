<?php
// views/to-truong.php (File layout chính)
session_start();

// --- KIỂM TRA SESSION ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['maVT'])) { // Thêm check vai trò: && $_SESSION['maVT'] == 3
    header("Location: ../index.php?action=login");
    exit;
}

// --- XÁC ĐỊNH TAB ACTIVE TỪ URL --- // <<< THÊM DÒNG NÀY
$active_tab = $_GET['tab'] ?? 'tong-quan';

// --- CÀI ĐẶT BIẾN ---
$page_title = 'Bảng điều khiển Tổ trưởng Chuyên môn';
$user_name = 'Tổ trưởng Chuyên môn';
$css_file = 'to-truong.css';
$js_file = 'to-truong.js'; // File JS riêng

// --- INCLUDE HEADER ---
require_once 'partials/header.php';
?>

<div class="khoi-chua">

    <?php // --- INCLUDE SIDEBAR ---
    // sidebar-totruong.php cần được cập nhật như hướng dẫn trước
    require_once 'partials/sidebar-totruong.php';
    ?>

    <main>
        <h1>Bảng điều khiển Tổ trưởng Chuyên môn</h1>

        <?php // --- INCLUDE CÁC TRANG CON ---
        // Các file này cần được cập nhật điều kiện style như hướng dẫn trước
        require_once 'pages_to-truong/tong-quan.php';
        require_once 'pages_to-truong/danh-sach-thiet-bi.php'; // Bạn sẽ copy nội dung vào đây
        require_once 'pages_to-truong/lap-ke-hoach-mua-sam.php';
        require_once 'pages_to-truong/theo-doi-thiet-bi.php';
        ?>

    </main>

</div>

<?php // --- INCLUDE FOOTER ---
require_once 'partials/footer.php';
?>