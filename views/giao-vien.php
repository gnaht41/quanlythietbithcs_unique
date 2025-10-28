<?php
// views/giao-vien.php (File layout chính)
session_start();

// --- KIỂM TRA SESSION ---
// (Thêm điều kiện kiểm tra vai trò nếu cần, ví dụ: && $_SESSION['maVT'] == 4)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['maVT'])) {
  header("Location: ../index.php?action=login");
  exit;
}

// --- CÀI ĐẶT BIẾN ---
$page_title = 'Bảng điều khiển Giáo viên';
$user_name = 'Giáo viên'; // Hoặc lấy từ session
$css_file = 'giao-vien.css';
$js_file = 'giao-vien.js';

// --- INCLUDE HEADER ---
require_once 'partials/header.php';
?>

<div class="khoi-chua">

  <?php // --- INCLUDE SIDEBAR ---
  require_once 'partials/sidebar-giaovien.php';
  ?>

  <main>
    <h1>Bảng điều khiển Giáo viên</h1>

    <?php // --- INCLUDE CÁC TRANG CON ---
    require_once 'pages_giao-vien/tong-quan.php';
    require_once 'pages_giao-vien/danh-sach-thiet-bi.php';
    require_once 'pages_giao-vien/phieu-muon.php';
    require_once 'pages_giao-vien/lich-su-muon.php';
    require_once 'pages_giao-vien/bao-cao-hu-hong.php';

    ?>

  </main>

</div>

<?php // --- INCLUDE FOOTER ---
require_once 'partials/footer.php';
?>