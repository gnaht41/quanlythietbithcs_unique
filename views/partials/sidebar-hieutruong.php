<?php
// views/partials/sidebar-hieutruong.php
// Biến $active_tab được truyền từ hieu-truong.php
?>
<aside class="thanh-ben">
        <ul>
                <li><a href="?tab=danh-sach-thiet-bi"
                                class="<?php echo ($active_tab == 'danh-sach-thiet-bi') ? 'active' : ''; ?>">Danh sách thiết bị</a></li>
                <li><a href="?tab=duyet-mua-sam" class="<?php echo ($active_tab == 'duyet-mua-sam') ? 'active' : ''; ?>">Duyệt
                                kế hoạch mua sắm</a></li>
                <li><a href="?tab=duyet-thanh-ly" class="<?php echo ($active_tab == 'duyet-thanh-ly') ? 'active' : ''; ?>">Duyệt
                                thanh lý</a></li>
                <li><a href="?tab=ket-qua-thong-ke"
                                class="<?php echo ($active_tab == 'ket-qua-thong-ke') ? 'active' : ''; ?>">Kết
                                quả thống kê</a></li>
        </ul>
</aside>