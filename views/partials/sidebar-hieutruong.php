<?php
// views/partials/sidebar-hieutruong.php
// Biến $active_tab được truyền từ hieu-truong.php
?>
<aside class="thanh-ben">
    <ul>
        <li><a href="?tab=tong-quan" class="<?php echo ($active_tab == 'tong-quan') ? 'active' : ''; ?>">Tổng quan</a>
        </li>
        <li><a href="?tab=danh-sach-thiet-bi"
                class="<?php echo ($active_tab == 'danh-sach-thiet-bi') ? 'active' : ''; ?>">Danh sách thiết bị</a></li>
        <li><a href="?tab=duyet-ke-hoach" class="<?php echo ($active_tab == 'duyet-ke-hoach') ? 'active' : ''; ?>">Duyệt
                kế hoạch mua sắm</a></li>
        <li><a href="?tab=duyet-thanh-ly" class="<?php echo ($active_tab == 'duyet-thanh-ly') ? 'active' : ''; ?>">Duyệt
                thanh lý</a></li>
        <li><a href="?tab=bao-cao-thong-ke"
                class="<?php echo ($active_tab == 'bao-cao-thong-ke') ? 'active' : ''; ?>">Báo cáo & Thống kê</a></li>
    </ul>
</aside>