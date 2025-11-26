<?php
// views/pages_to-truong/theo-doi-thiet-bi.php
// Đảm bảo chỉ 1 section mở/đóng và view tự load model khi được include trong layout

if (!isset($active_tab)) {
    $active_tab = $_GET['tab'] ?? 'tong-quan';
}

require_once __DIR__ . '/../../models/ToTruongTheoDoiModel.php';
$model = new ToTruongTheoDoiModel();

$maMH = isset($_GET['maMH']) ? intval($_GET['maMH']) : null;
$monHoc = $model->getDanhSachMonHoc();
$ds = $model->getDanhSachTheoDoi($maMH);
?>

<section id="theo-doi-thiet-bi" class="trang-an"
    <?php echo ($active_tab != 'theo-doi-thiet-bi') ? 'style="display:none;"' : ''; ?>>

    <form id="form-filter-td" method="GET" class="bo-loc" style="max-width:760px; align-items:center;">
        <input type="hidden" name="tab" value="theo-doi-thiet-bi">

        <label for="sel-maMH" style="margin-right:8px;">Môn học</label>
        <select id="sel-maMH" name="maMH" aria-label="Lọc theo môn" style="min-width:220px;">
            <option value="">-- Chọn môn học --</option>
            <?php foreach ($monHoc as $m): ?>
                <option value="<?= $m['maMH'] ?>" <?= ($maMH && $maMH == $m['maMH']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($m['tenMonHoc']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="btn-primary" type="submit" style="margin-left:8px;">Lọc</button>

        <!-- Link bỏ lọc (điền lại URL chỉ có tab) -->
        <a class="btn-secondary" href="?tab=theo-doi-thiet-bi"
            style="margin-left:8px;text-decoration:none;padding:8px 12px;border-radius:6px;display:inline-block;">
            Bỏ lọc
        </a>
    </form>

    <div id="bang-theo-doi" style="margin-top:12px;">
        <table>
            <thead>
                <tr>
                    <th>Thiết bị</th>
                    <th>Môn</th>
                    <th>Số lượng tổng</th>
                    <th>Số thực tế (KK mới nhất)</th>
                    <th>Chênh lệch</th>
                    <th>Tình trạng</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ds)): ?>
                    <?php foreach ($ds as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['tenTB']) ?></td>
                            <td><?= htmlspecialchars($d['tenMonHoc']) ?></td>
                            <td><?= (int)$d['soLuongTong'] ?></td>
                            <td><?= (int)$d['soLuongThucTe'] ?></td>
                            <td
                                class="<?= ((int)$d['chenhLech'] < 0) ? 'chenh-am' : (((int)$d['chenhLech'] > 0) ? 'chenh-duong' : '') ?>">
                                <?= (int)$d['chenhLech'] ?>
                            </td>
                            <td><?= htmlspecialchars($d['tinhTrang']) ?></td>
                            <td><?= htmlspecialchars($d['ghiChu'] ?: '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:16px;color:#666;">Không có dữ liệu phù hợp</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>