<?php
// views/partials/sidebar-giaovien.php
// Biến $active_tab được truyền từ giao-vien.php
?>
<aside class="thanh-ben">
    <ul>
        <li><a href="?tab=tong-quan" class="<?php echo ($active_tab == 'tong-quan') ? 'active' : ''; ?>">Tổng quan</a>
        </li>
        <li><a href="?tab=danh-sach-thiet-bi"
                class="<?php echo ($active_tab == 'danh-sach-thiet-bi') ? 'active' : ''; ?>">Danh sách thiết bị</a></li>
        <li><a href="?tab=phieu-muon" class="<?php echo ($active_tab == 'phieu-muon') ? 'active' : ''; ?>">Phiếu
                mượn</a></li>
        <li><a href="?tab=lich-su-muon" class="<?php echo ($active_tab == 'lich-su-muon') ? 'active' : ''; ?>">Lịch sử
                mượn</a></li>
        <li><a href="?tab=bao-cao-hu-hong" class="<?php echo ($active_tab == 'bao-cao-hu-hong') ? 'active' : ''; ?>">Báo
                cáo hư/hỏng</a></li>
    </ul>
</aside>