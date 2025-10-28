<?php
// views/nhan-vien-thiet-bi.php (File layout chính)
session_start();

// --- KIỂM TRA SESSION ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['maVT'])) { // Thêm check vai trò: && $_SESSION['maVT'] == 5
  header("Location: ../index.php?action=login");
  exit;
}

// --- CÀI ĐẶT BIẾN ---
$page_title = 'Bảng điều khiển Nhân viên Thiết bị';
$user_name = 'Nhân viên Thiết bị';
$css_file = 'nhan-vien-thiet-bi.css';
$js_file = 'nhan-vien-thiet-bi.js';

// --- INCLUDE HEADER ---
require_once 'partials/header.php';
?>

<div class="khoi-chua">

  <?php // --- INCLUDE SIDEBAR ---
  require_once 'partials/sidebar-nhanvienthietbi.php';
  ?>

  <main>
    <h1>Bảng điều khiển Nhân viên Thiết bị</h1>

    <?php // --- INCLUDE CÁC TRANG CON ---
    require_once 'pages_nhan-vien-thiet-bi/tong-quan.php';
    require_once 'pages_nhan-vien-thiet-bi/danh-sach-thiet-bi.php';
    require_once 'pages_nhan-vien-thiet-bi/quan-ly-danh-muc.php';
    require_once 'pages_nhan-vien-thiet-bi/phieu-muon.php';
    require_once 'pages_nhan-vien-thiet-bi/quan-ly-bao-tri.php';
    require_once 'pages_nhan-vien-thiet-bi/quan-ly-kiem-ke.php';
    require_once 'pages_nhan-vien-thiet-bi/lap-bao-cao.php';

    // Tùy chọn: Include modal thông báo chung nếu có
    // require_once 'partials/modal-thongbao.php';
    ?>

  </main>

</div>

<?php // --- INCLUDE FOOTER ---
require_once 'partials/footer.php';
?>