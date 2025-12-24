<?php
require_once __DIR__ . '/../../models/TV_DuyetMuaSamModel.php';

$khModel = new TV_DuyetMuaSamModel();
$dsPhieu = $khModel->getAll();

if (!isset($_SESSION['maVT']) || $_SESSION['maVT'] != 5) {
  echo '<div style="color:red; padding:20px; text-align:center;">Bạn không có quyền truy cập chức năng này.</div>';
  return;
}
?>

<link rel="stylesheet" href="../css/duyet-mua-sam.css">
<script src="../js/duyet-mua-sam.js" defer></script>

<section id="duyet-mua-sam" class="trang-an" <?= ($active_tab != 'duyet-mua-sam') ? 'style="display:none;"' : ''; ?>>
  <h2>Duyệt kế hoạch mua sắm</h2>
  <button class="btn-primary" onclick="window.print()">🖨 In danh sách</button>

  <div class="ds-phieu">
    <?php if (empty($dsPhieu)): ?>
      <p class="thong-bao-trong">Chưa có kế hoạch nào cần duyệt.</p>
    <?php else: ?>
      <?php foreach ($dsPhieu as $p): ?>
        <?php
        $chiTiet = $khModel->getChiTiet($p['maMS']);
        $nguoiDuyet = $p['nguoiDuyet'] ? ($khModel->getTenNguoiDung($p['nguoiDuyet']) ?? '-') : '-';

        $data = [
          'maMS'      => $p['maMS'],
          'trangThai' => $p['trangThai'],
          'chiTiet'   => $chiTiet,
          'header'    => [
            'ngayLap' => $p['ngayLap'],
            'tenNguoiLap' => $p['tenNguoiLap'] ?? '',
          ]
        ];
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_APOS);

        // Class không dấu để nhất quán
        $classTrangThai = match ($p['trangThai']) {
          'Chờ duyệt' => 'cho-duyet',
          'Đã duyệt'  => 'da-duyet',
          'Từ chối'   => 'tu-choi',
          default     => 'cho-duyet'
        };
        ?>
        <div class="phieu-card" data-phieu-id="<?= $p['maMS'] ?>">
          <div class="phieu-info">
            <strong>KHM-<?= date('Y', strtotime($p['ngayLap'])) ?>-<?= sprintf('%03d', $p['maMS']) ?></strong><br>
            <small>Ngày lập: <?= date('d/m/Y', strtotime($p['ngayLap'])) ?></small><br>
            <small>Người lập: <?= htmlspecialchars($p['tenNguoiLap'] ?? '') ?></small><br>
            <small>Người duyệt: <?= htmlspecialchars($nguoiDuyet) ?></small>
          </div>

          <div class="phieu-trangthai">
            <span class="trang-thai <?= $classTrangThai ?>">
              <?= $p['trangThai'] ?>
            </span>
          </div>

          <div class="phieu-actions">
            <button class="btn-secondary btn-thao-tac" data-json='<?= $jsonData ?>'>
              Thao tác
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Modal -->
  <div class="modal" id="modal-duyet">
    <div class="noi-dung-modal">
      <button class="dong-x" onclick="dongModal()">×</button>
      <h3 id="modal-title">Điều chỉnh quyết định duyệt</h3>

      <div style="margin-bottom:16px;">
        <strong>Mã phiếu:</strong> <span id="duyet-ma"></span><br>
        <strong>Trạng thái:</strong> <span id="duyet-trangthai"></span>
      </div>

      <table>
        <thead>
          <tr>
            <th>Thiết bị</th>
            <th style="text-align:center;width:120px;">Số lượng</th>
          </tr>
        </thead>
        <tbody id="duyet-chitiet"></tbody>
      </table>

      <form id="form-duyet">
        <input type="hidden" id="duyet-mams" value="">
        <div style="margin-top:20px;">
          <label><strong>Quyết định:</strong></label>
          <select id="duyet-quyetdinh">
            <option value="approve">Phê duyệt</option>
            <option value="reject">Từ chối</option>
          </select>
        </div>

        <div class="modal-actions">
          <button type="submit" class="btn-primary">Lưu quyết định</button>
          <button type="button" class="btn-secondary" onclick="dongModal()">Hủy</button>
          <span id="duyet-msg"></span>
        </div>
      </form>
    </div>
  </div>
</section>