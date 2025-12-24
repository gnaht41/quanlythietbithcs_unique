<section id="nhat-ky" class="trang-an" <?= ($active_tab != 'nhat-ky') ? 'style="display:none;"' : ''; ?>>

    <h2>Nhật ký hệ thống</h2>

    <table border="1" width="100%" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Thời gian</th>
                <th>Người thực hiện</th>
                <th>Hành động</th>
                <th>Đối tượng</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= htmlspecialchars($log['thoiGian']) ?></td>
                <td><?= htmlspecialchars($log['hoTen'] ?? 'Hệ thống') ?></td>
                <td><?= htmlspecialchars($log['hanhDong']) ?></td>
                <td><?= htmlspecialchars($log['doiTuong']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="4">Chưa có dữ liệu nhật ký</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

</section>