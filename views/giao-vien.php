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

        <?php 
        // --- HIỂN THỊ TRANG THEO TAB ---
        switch($active_tab) {
            case 'chi-tiet-phieu':  // <--- THÊM CASE NÀY
                require_once 'pages_giao-vien/phieu-muon.php';  // Vẫn include file cũ
                break;  // Phần chi tiết sẽ tự chạy khi tab = chi-tiet-phieu
            case 'lap-phieu-muon':  // <--- THÊM CASE NÀY
                require_once 'pages_giao-vien/phieu-muon.php';  // Vẫn include file cũ
                break;  // Phần chi tiết sẽ tự chạy khi tab = chi-tiet-phieu
            case 'danh-sach-thiet-bi':
                require_once 'pages_giao-vien/danh-sach-thiet-bi.php';
                break;
            case 'phieu-muon':
                require_once 'pages_giao-vien/phieu-muon.php';
                break;
            case 'lich-su-muon':
                require_once 'pages_giao-vien/lich-su-muon.php';
                break;
            case 'bao-cao-hu-hong':
                require_once 'pages_giao-vien/bao-cao-hu-hong.php';
                break;
            
            default:
                echo '<div style="padding:20px;"><h2>Chào mừng Giáo viên!</h2><p>Chọn chức năng từ menu bên trái.</p></div>';
        }
        ?>

    </main>

</div>

<?php // --- INCLUDE FOOTER ---
require_once 'partials/footer.php';
?>