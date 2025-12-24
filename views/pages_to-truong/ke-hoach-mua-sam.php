<?php
require_once __DIR__ . '/../../models/QT_KeHoachModel.php';
require_once __DIR__ . '/../../models/QT_ThietBi.php';

$khModel = new QT_KeHoachModel();
$dsPhieu = $khModel->getAll();

$tbModel = new ThietBi();
$dsThietBi = $tbModel->getListForSelect(); // Hàm đã thêm ở file QT_ThietBi.php
?>

<script>
    window.dsThietBi = <?= json_encode($dsThietBi, JSON_UNESCAPED_UNICODE) ?>;
</script>

<section id="ke-hoach-mua-sam" class="trang-an"
    <?= ($active_tab != 'ke-hoach-mua-sam') ? 'style="display:none;"' : ''; ?>>
    <h2>Kế hoạch mua sắm thiết bị</h2>

    <button class="btn-primary" onclick="lapPhieuMoi()">➕ Lập kế hoạch mới</button>

    <div class="ds-phieu">
        <?php if (empty($dsPhieu)): ?>
            <p class="thong-bao-trong">Chưa có kế hoạch mua sắm nào.</p>
        <?php else: ?>
            <?php foreach ($dsPhieu as $p): ?>
                <?php
                $chiTiet = $khModel->getChiTiet($p['maMS']);
                $nguoiDuyet = $p['nguoiDuyet'] ? ($khModel->getTenNguoiDung($p['nguoiDuyet']) ?? 'Không rõ') : '-';
                $data = [
                    'maMS'      => $p['maMS'],
                    'trangThai' => $p['trangThai'],
                    'chiTiet'   => $chiTiet
                ];
                ?>
                <div class="phieu-card">
                    <div class="phieu-info">
                        <strong>Phiếu #<?= $p['maMS'] ?></strong><br>
                        <small>Ngày lập: <?= date('d/m/Y', strtotime($p['ngayLap'])) ?></small><br>
                        <small>Người lập: <?= htmlspecialchars($p['tenNguoiLap']) ?></small><br>
                        <small>Người duyệt: <?= htmlspecialchars($nguoiDuyet) ?></small>
                    </div>

                    <div class="phieu-trangthai">
                        <span class="trang-thai <?= strtolower(str_replace(' ', '-', $p['trangThai'])) ?>">
                            <?= $p['trangThai'] ?>
                        </span>
                    </div>

                    <div class="phieu-actions">
                        <button class="btn-secondary" onclick='xemPhieu(<?= json_encode($data, JSON_UNESCAPED_UNICODE) ?>)'>
                            👁 Xem chi tiết
                        </button>

                        <?php if ($p['trangThai'] === 'Chờ duyệt'): ?>
                            <a class="btn-xoa" href="../controllers/QT_KeHoachController.php?action=delete&maMS=<?= $p['maMS'] ?>"
                                onclick="return confirm('Xóa phiếu này?')">🗑 Xóa</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div class="modal" id="modal-phieu">
        <div class="noi-dung-modal">
            <button class="dong-x" onclick="dongModal()">×</button>
            <h3 id="modal-title">Phiếu kế hoạch mua sắm</h3>

            <form method="post" action="../controllers/QT_KeHoachController.php">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="maMS" id="maMS">

                <table id="bang-chi-tiet">
                    <thead>
                        <tr>
                            <th>Thiết bị</th>
                            <th>Số lượng</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="btn-them" onclick="themDong()">➕ Thêm thiết
                        bị</button>
                    <button type="submit" class="btn-primary" id="btn-luu">💾 Lưu phiếu</button>
                    <button type="button" class="btn-secondary" onclick="dongModal()">Đóng</button>
                </div>
            </form>
        </div>
    </div>
</section>