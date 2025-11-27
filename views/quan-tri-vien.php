<?php
// views/quan-tri-vien.php (File layout chính)
session_start();

// --- PHẦN KIỂM TRA QUAN TRỌNG ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['maVT'])) { // Thêm check vai trò: && $_SESSION['maVT'] == 1
    header("Location: ../index.php?action=login");
    exit;
}
?>

<?php
require_once __DIR__ . '/../controllers/TaikhoanController.php'; 
$n = new TaikhoanController();
?>
<!-- Php xử lý thêm tài khoản -->
<?php
    if(isset($_POST['submit']))
    {
        $hoten = $_POST['ho-ten'];
        $email = $_POST['email']; //đây cũng là username
        $vaitro = $_POST['vai-tro']; // đây là số
        $trangthai = $_POST['trang-thai'];
        $pass = $_POST['mat-khau'];
        //$ngaytao = $_POST['ngay-tao']; // mặc định là ngày hiện tại, sửa lại trong database, bỏ cột này trên form model
        if($n->CheckEmailTonTai($email) == 1)
        {
            $soluongnguoidung = $n->countnguoidung();
            if($soluongnguoidung != -1)
            {
                $soluongnguoidung += 1;
                $maND = "ND".$soluongnguoidung;
                // đã đủ giá trị cần thiết, bắt đầu thêm dữ liệu mới 
                $n->themtaikhoan($maND, $hoten, $email, $vaitro, $pass, $trangthai);
            }
            else
            {
                echo "Chưa có người dùng";
            }
        }
        else
        {
            echo "Email đã tồn tại";
        }
    }
?>
<!-- Php vô hiệu hóa tài khoản -->
<?php
if(isset($_POST['xoa']))
{
    $maND = $_POST['mand'];
    $n->XoaTK($maND);
}
?>
<!-- Php sửa thông tin tài khoản -->
<?php
if(isset($_POST['submit-sua']))
{
    $maND = $_POST['sua-maND'];
    $hoTen = $_POST['sua-hoTen'];
    $email = $_POST['sua-email'];
    $maVT = $_POST['sua-tenVT'];
    $username = $_POST['sua-username'];
    $trangthai = $_POST['sua-trangthai'];
    $password = $_POST['sua-password'];
    $ngayTao = $_POST['sua-ngayTao'];
    $passwordmoi = $_POST['sua-passwordmoi'];
    $passwordnhaplai = $_POST['sua-passwordnhaplai'];
    $ketqua = $n->SuaTK($maND, $hoTen, $maVT, $trangthai, $password, $passwordmoi, $passwordnhaplai);
    if($ketqua != 1)
    {
        echo "<script> alert('Sửa tài khoản thất bại'); </script>";
    }
}
?>

<!-- Chuẩn bị cho header và footer -->
<?php
    // --- XÁC ĐỊNH TAB ACTIVE TỪ URL ---
    $active_tab = $_GET['tab'] ?? 'tong-quan'; // Lấy 'tab' từ URL, mặc định là 'tong-quan'

    // --- CÀI ĐẶT BIẾN CHO HEADER/FOOTER ---
    $page_title = 'Bảng điều khiển Quản trị viên';
    $user_name = 'Quản trị viên';
    $css_file = 'quan-tri-vien.css';
    $js_file = 'quan-tri-vien.js'; // Vẫn giữ file JS riêng (có thể trống hoặc chứa code khác)
?>
<!-- PHP  Hiển thị website-->
<?php
    // Include thanh bên - PHP sẽ đặt class 'active' đúng chỗ dựa vào $active_tab
    require_once 'partials/header.php';
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