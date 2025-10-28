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
    require_once 'pages_nhan-vien-thiet-bi/tong-quan.php';
    require_once 'pages_nhan-vien-thiet-bi/danh-sach-thiet-bi.php'; // Bạn sẽ copy nội dung vào đây
    require_once 'pages_nhan-vien-thiet-bi/quan-ly-danh-muc.php';
    require_once 'pages_nhan-vien-thiet-bi/phieu-muon.php';
    require_once 'pages_nhan-vien-thiet-bi/quan-ly-bao-tri.php';
    require_once 'pages_nhan-vien-thiet-bi/quan-ly-kiem-ke.php';
    require_once 'pages_nhan-vien-thiet-bi/lap-bao-cao.php';
    ?>

  </main>

</div>

<?php // --- INCLUDE FOOTER ---
require_once 'partials/footer.php';
?>