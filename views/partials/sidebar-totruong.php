<?php
// views/partials/sidebar-totruong.php
// Biến $active_tab được truyền từ to-truong.php
?>
<aside class="thanh-ben">
        <ul>
                <li><a href="?tab=danh-sach-thiet-bi"
                                class="<?php echo ($active_tab == 'danh-sach-thiet-bi') ? 'active' : ''; ?>">Danh sách thiết bị</a></li>
                <li><a href="?tab=ke-hoach-mua-sam"
                                class="<?php echo ($active_tab == 'ke-hoach-mua-sam') ? 'active' : ''; ?>">Lập kế hoạch mua sắm</a>
                </li>
                <li><a href="?tab=ket-qua-thong-ke"
                                class="<?php echo ($active_tab == 'ket-qua-thong-ke') ? 'active' : ''; ?>">Kết
                                quả thống kê</a></li>

        </ul>
</aside>