<?php
// views/partials/sidebar-nhanvienthietbi.php
// Biến $active_tab được truyền từ nhan-vien-thiet-bi.php
?>
<aside class="thanh-ben">
    <ul>
        <li><a href="?tab=tong-quan" class="<?php echo ($active_tab == 'tong-quan') ? 'active' : ''; ?>">Tổng quan</a>
        </li>
        <li><a href="?tab=danh-sach-thiet-bi"
                class="<?php echo ($active_tab == 'danh-sach-thiet-bi') ? 'active' : ''; ?>">Danh sách thiết bị</a></li>
        <li><a href="?tab=quan-ly-danh-muc"
                class="<?php echo ($active_tab == 'quan-ly-danh-muc') ? 'active' : ''; ?>">Quản lý danh mục thiết bị</a>
        </li>
        <li><a href="?tab=phieu-muon" class="<?php echo ($active_tab == 'phieu-muon') ? 'active' : ''; ?>">Phiếu
                mượn</a></li>
        <li><a href="?tab=quan-ly-bao-tri"
                class="<?php echo ($active_tab == 'quan-ly-bao-tri') ? 'active' : ''; ?>">Quản lý bảo trì/sửa chữa</a>
        </li>
        <li><a href="?tab=quan-ly-kiem-ke"
                class="<?php echo ($active_tab == 'quan-ly-kiem-ke') ? 'active' : ''; ?>">Quản lý kiểm kê</a></li>
        <li><a href="?tab=lap-bao-cao" class="<?php echo ($active_tab == 'lap-bao-cao') ? 'active' : ''; ?>">Lập báo
                cáo</a></li>
    </ul>
</aside>