<?php
// views/giao-vien.php (File layout chính)
session_start();

// --- KIỂM TRA SESSION ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['maVT'])) { // Thêm check vai trò: && $_SESSION['maVT'] == 4
  header("Location: ../index.php?action=login");
  exit;
}

// --- XÁC ĐỊNH TAB ACTIVE TỪ URL --- // <<< THÊM DÒNG NÀY
$active_tab = $_GET['tab'] ?? 'tong-quan';

// --- CÀI ĐẶT BIẾN ---
$page_title = 'Bảng điều khiển Giáo viên';
$user_name = 'Giáo viên';
$css_file = 'giao-vien.css';
$js_file = 'giao-vien.js'; // File JS riêng (nếu có logic khác ngoài main.js)

// --- INCLUDE HEADER ---
require_once 'partials/header.php';
?>

<div class="khoi-chua">

  <?php // --- INCLUDE SIDEBAR ---
  // sidebar-giaovien.php cần được cập nhật như hướng dẫn trước
  require_once 'partials/sidebar-giaovien.php';
  ?>

  <main>
    <h1>Bảng điều khiển Giáo viên</h1>

    <?php // --- INCLUDE CÁC TRANG CON ---
    // Các file này cần được cập nhật điều kiện style như hướng dẫn trước
    require_once 'pages_giao-vien/tong-quan.php';
    require_once 'pages_giao-vien/danh-sach-thiet-bi.php'; // Bạn sẽ copy nội dung vào đây
    require_once 'pages_giao-vien/phieu-muon.php';
    require_once 'pages_giao-vien/lich-su-muon.php';
    require_once 'pages_giao-vien/bao-cao-hu-hong.php';
    ?>

  </main>

</div>

<?php // --- INCLUDE FOOTER ---
require_once 'partials/footer.php';
?>