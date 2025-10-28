<?php
// File: views/partials/sidebar-admin.php
// Biến $active_tab sẽ được truyền từ file layout (quan-tri-vien.php)
// Cần đảm bảo $active_tab luôn được định nghĩa (ví dụ, mặc định là 'tong-quan')
?>
<aside class="thanh-ben">
    <ul>
        <li><a href="?tab=tong-quan" class="<?php echo ($active_tab == 'tong-quan') ? 'active' : ''; ?>"
                data-page="tong-quan">Tổng quan</a></li>
        <li><a href="?tab=danh-sach-thiet-bi"
                class="<?php echo ($active_tab == 'danh-sach-thiet-bi') ? 'active' : ''; ?>"
                data-page="danh-sach-thiet-bi">Danh sách thiết bị</a></li>
        <li><a href="?tab=ql-nguoi-dung" class="<?php echo ($active_tab == 'ql-nguoi-dung') ? 'active' : ''; ?>"
                data-page="ql-nguoi-dung">Quản lý tài khoản</a></li>
        <li><a href="?tab=phan-quyen" class="<?php echo ($active_tab == 'phan-quyen') ? 'active' : ''; ?>"
                data-page="phan-quyen">Phân quyền truy cập</a></li>
        <li><a href="?tab=nhat-ky" class="<?php echo ($active_tab == 'nhat-ky') ? 'active' : ''; ?>"
                data-page="nhat-ky">Giám sát nhật ký hệ thống</a></li>
    </ul>
</aside>