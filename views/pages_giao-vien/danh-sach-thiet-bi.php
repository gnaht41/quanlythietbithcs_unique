<?php
require_once __DIR__ . '/../../models/QT_ThietBi.php';

$model = new ThietBi();

// Lấy filter từ GET
$filters = [
    'tenTB' => $_GET['tenTB'] ?? '',
    'lop'   => $_GET['lop'] ?? '',
    'maMH'  => $_GET['maMH'] ?? ''
];

$thietBis = $model->search($filters);
$monHocs  = $model->getAllMonHoc();
?>

<section id="danh-sach-thiet-bi" class="trang-an"
    <?= ($active_tab != 'danh-sach-thiet-bi') ? 'style="display:none;"' : ''; ?>>

    <h2>Danh sách thiết bị</h2>

    <!-- FORM TÌM KIẾM -->
    <form method="get" class="bo-loc">
        <input type="hidden" name="tab" value="danh-sach-thiet-bi">

        <input type="text" name="tenTB" placeholder="Tên thiết bị" value="<?= htmlspecialchars($filters['tenTB']) ?>">

        <select name="lop">
            <option value="">-- Khối lớp --</option>
            <?php foreach (['6', '7', '8', '9'] as $lop): ?>
            <option value="<?= $lop ?>" <?= ($filters['lop'] == $lop) ? 'selected' : '' ?>>
                Khối <?= $lop ?>
            </option>
            <?php endforeach; ?>
        </select>

        <select name="maMH">
            <option value="">-- Môn học --</option>
            <?php foreach ($monHocs as $mh): ?>
            <option value="<?= $mh['maMH'] ?>" <?= ($filters['maMH'] == $mh['maMH']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($mh['tenMonHoc']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-primary">Tìm</button>
        <a href="?tab=danh-sach-thiet-bi" class="btn-secondary">Xóa lọc</a>
    </form>

    <!-- BẢNG DANH SÁCH -->
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên thiết bị</th>
                <th>Đơn vị</th>
                <th>Khối</th>
                <th>Môn học</th>
                <th>Tổng</th>
                <th>Khả dụng</th>
                <th>Tình trạng</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($thietBis)): ?>
            <tr>
                <td colspan="8">Không có thiết bị phù hợp</td>
            </tr>
            <?php else: ?>
            <?php foreach ($thietBis as $i => $tb): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($tb['tenTB']) ?></td>
                <td><?= htmlspecialchars($tb['donVi']) ?></td>
                <td><?= $tb['lop'] ?></td>
                <td><?= htmlspecialchars($tb['tenMonHoc']) ?></td>
                <td><?= $tb['soLuongTong'] ?></td>
                <td><?= $tb['soLuongKhaDung'] ?></td>
                <td><?= $tb['tinhTrang'] ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</section>