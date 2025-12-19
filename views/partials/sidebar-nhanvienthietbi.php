<?php
// views/partials/sidebar-nhanvienthietbi.php
// Biến $active_tab được truyền từ nhan-vien-thiet-bi.php
?>
<aside class="thanh-ben">
    <ul>
        <li><a href="?tab=danh-sach-thiet-bi"
                class="<?php echo ($active_tab == 'danh-sach-thiet-bi') ? 'active' : ''; ?>">Danh sách thiết bị</a></li>
        <li><a href="?tab=phieu-muon" class="<?php echo ($active_tab == 'phieu-muon') ? 'active' : ''; ?>">Phiếu
                mượn</a></li>
        <li><a href="?tab=ke-hoach-thanh-ly"
                class="<?php echo ($active_tab == 'ke-hoach-thanh-ly') ? 'active' : ''; ?>">Kế hoạch thanh lý</a></li>
        <li><a href="?tab=quan-ly-thiet-bi"
                class="<?php echo ($active_tab == 'quan-ly-thiet-bi') ? 'active' : ''; ?>">Quản lý thiết bị</a>
        </li>
        <li><a href="?tab=ket-qua-thong-ke"
                class="<?php echo ($active_tab == 'ket-qua-thong-ke') ? 'active' : ''; ?>">Kết
                quả thống kê</a></li>
    </ul>
</aside>