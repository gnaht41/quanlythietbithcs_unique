<?php
// views/quan-tri-vien.php (File layout chính)
session_start();

// --- PHẦN KIỂM TRA QUAN TRỌNG ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['maVT'])) { // Thêm check vai trò: && $_SESSION['maVT'] == 1
    header("Location: ../index.php?action=login");
    exit;
}

// --- XÁC ĐỊNH TAB ACTIVE TỪ URL ---
$active_tab = $_GET['tab'] ?? 'tong-quan'; // Lấy 'tab' từ URL, mặc định là 'tong-quan'

// --- CÀI ĐẶT BIẾN CHO HEADER/FOOTER ---
$page_title = 'Bảng điều khiển Quản trị viên';
$user_name = 'Quản trị viên';
$css_file = 'quan-tri-vien.css';
$js_file = 'quan-tri-vien.js'; // Vẫn giữ file JS riêng (có thể trống hoặc chứa code khác)

// --- INCLUDE HEADER CHUNG ---
require_once 'partials/header.php'; // Đã load main.css và $css_file
?>

<div class="khoi-chua">

    <?php
    // Include thanh bên - PHP sẽ đặt class 'active' đúng chỗ dựa vào $active_tab
    require_once 'partials/sidebar-admin.php';
    ?>

    <main>
        <h1>Bảng điều khiển Quản trị viên</h1>

        <?php
        // Include các trang con (sections) - PHP sẽ đặt style display dựa vào $active_tab
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
require_once 'partials/footer.php'; // Đã load main.js và $js_file
?>