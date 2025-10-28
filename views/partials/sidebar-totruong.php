<?php
// views/partials/sidebar-totruong.php
// Biến $active_tab được truyền từ to-truong.php
?>
<aside class="thanh-ben">
    <ul>
        <li><a href="?tab=tong-quan" class="<?php echo ($active_tab == 'tong-quan') ? 'active' : ''; ?>">Tổng quan</a>
        </li>
        <li><a href="?tab=danh-sach-thiet-bi"
                class="<?php echo ($active_tab == 'danh-sach-thiet-bi') ? 'active' : ''; ?>">Danh sách thiết bị</a></li>
        <li><a href="?tab=lap-ke-hoach-mua-sam"
                class="<?php echo ($active_tab == 'lap-ke-hoach-mua-sam') ? 'active' : ''; ?>">Lập kế hoạch mua sắm</a>
        </li>
        <li><a href="?tab=theo-doi-thiet-bi"
                class="<?php echo ($active_tab == 'theo-doi-thiet-bi') ? 'active' : ''; ?>">Theo dõi tình hình thiết
                bị</a></li>
    </ul>
</aside>