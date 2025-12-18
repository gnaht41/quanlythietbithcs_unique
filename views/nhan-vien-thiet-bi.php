<?php
// views/nhan-vien-thiet-bi.php (File layout chính)
session_start();

// --- KIỂM TRA SESSION ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['maVT'])) { // Thêm check vai trò: && $_SESSION['maVT'] == 5
  header("Location: ../index.php?action=login");
  exit;
}

// --- XÁC ĐỊNH TAB ACTIVE TỪ URL --- // <<< THÊM DÒNG NÀY
$active_tab = $_GET['tab'] ?? 'tong-quan';

// --- CÀI ĐẶT BIẾN ---
$page_title = 'Bảng điều khiển Nhân viên Thiết bị';
$user_name = 'Nhân viên Thiết bị';
$css_file = 'nhan-vien-thiet-bi.css';
$js_file = 'nhan-vien-thiet-bi.js'; // File JS riêng

// --- INCLUDE HEADER ---
require_once 'partials/header.php';
?>

<div class="khoi-chua">

    <?php // --- INCLUDE SIDEBAR ---
  // sidebar-nhanvienthietbi.php cần được cập nhật như hướng dẫn trước
  require_once 'partials/sidebar-nhanvienthietbi.php';
  ?>

    <main>
        <h1>Bảng điều khiển Nhân viên Thiết bị</h1>

        <?php // --- INCLUDE CÁC TRANG CON ---
    // Các file này cần được cập nhật điều kiện style như hướng dẫn trước
    switch($active_tab){
    case 'danh-sach-thiet-bi':
        require_once 'pages_nhan-vien-thiet-bi/danh-sach-thiet-bi.php';
        break;
    case 'quan-ly-thiet-bi':
        require_once 'pages_nhan-vien-thiet-bi/quan-ly-thiet-bi.php';
        break;
    case 'them-thiet-bi':
        require_once 'pages_nhan-vien-thiet-bi/them-thiet-bi.php';
        break;
    case 'sua-thiet-bi':
        require_once 'pages_nhan-vien-thiet-bi/sua-thiet-bi.php';
        break;
    case 'phieu-muon':
        require_once 'pages_nhan-vien-thiet-bi/phieu-muon.php';
        break;
    case 'quan-ly-kiem-ke':
        require_once 'pages_nhan-vien-thiet-bi/quan-ly-kiem-ke.php';
        break;
    case 'lap-bao-cao':
        require_once 'pages_nhan-vien-thiet-bi/lap-bao-cao.php';
        break;
    default:
        require_once 'pages_nhan-vien-thiet-bi/danh-sach-thiet-bi.php';
}

    ?>

    </main>

</div>

<?php // --- INCLUDE FOOTER ---
require_once 'partials/footer.php';
?>