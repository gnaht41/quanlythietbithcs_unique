<?php
// views/pages_hieu-truong/duyet-thanh-ly.php
require_once __DIR__ . '/../../controllers/TV_duyet-thanhly.php';

$ctrl = new TV_DuyetThanhLyController();
$vm   = $ctrl->getViewModel();

$keyword = $vm['keyword'] ?? '';
$status  = $vm['status'] ?? '';
$list    = $vm['list'] ?? [];

function formatMaDeXuatTL($maTL, $ngayLap): string {
    $ym = !empty($ngayLap) ? date('Ym', strtotime($ngayLap)) : date('Ym');
    return sprintf('TL-%s-%02d', $ym, (int)$maTL); // giống mẫu TL-202409-01
}
?>

<section id="duyet-thanh-ly" class="trang-an" <?php echo ($active_tab != 'duyet-thanh-ly') ? 'style="display:none;"' : ''; ?>>

  <div class="head" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
    <h2 style="margin:0;">Duyệt thanh lý</h2>
    <button type="button" class="btn btn-primary" onclick="window.print()">In danh sách</button>
  </div>

  <div class="card" style="background:#fff;border-radius:10px;padding:14px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <input type="hidden" name="tab" value="duyet-thanh-ly">

      <input
        type="text"
        name="q"
        value="<?php echo htmlspecialchars($keyword); ?>"
        placeholder="Tìm theo mã đề xuất / thiết bị"
        style="height:36px;padding:0 12px;border:1px solid #d0d7de;border-radius:8px;outline:none;"
      >

      <select name="status" style="height:36px;padding:0 10px;border:1px solid #d0d7de;border-radius:8px;outline:none;">
        <option value="">-- Trạng thái --</option>
        <option value="Chờ duyệt" <?php echo ($status==='Chờ duyệt')?'selected':''; ?>>Chờ duyệt</option>
        <option value="Đã duyệt" <?php echo ($status==='Đã duyệt')?'selected':''; ?>>Đã duyệt</option>
        <option value="Từ chối"  <?php echo ($status==='Từ chối')?'selected':''; ?>>Từ chối</option>
      </select>

      <button type="submit" style="height:36px;border-radius:8px;padding:0 14px;border:0;background:#16a34a;color:#fff;">Lọc</button>
      <a href="?tab=duyet-thanh-ly" style="color:#2563eb;text-decoration:none;">Xóa lọc</a>
    </form>
  </div>

  <div class="card" style="margin-top:14px;background:#fff;border-radius:10px;padding:0;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr style="background:#f8fafc;border-bottom:1px solid #e5e7eb;">
          <th style="text-align:left;padding:12px 14px;width:50px;">STT</th>
          <th style="text-align:left;padding:12px 14px;width:170px;">Mã đề xuất</th>
          <th style="text-align:left;padding:12px 14px;">Lý do thanh lý</th>
          <th style="text-align:left;padding:12px 14px;width:140px;">Trạng thái</th>
          <th style="text-align:left;padding:12px 14px;width:140px;">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($list)): ?>
          <tr><td colspan="5" style="padding:14px;color:#64748b;">Không có dữ liệu.</td></tr>
        <?php else: ?>
          <?php $i=1; foreach ($list as $row): ?>
            <?php
              $maTL = (int)($row['maTL'] ?? 0);
              $ngayLap = $row['ngayLap'] ?? '';
              $maHienThi = formatMaDeXuatTL($maTL, $ngayLap);
              $lyDo = $row['lyDoThanhLy'] ?? '-';
              $trangThai = $row['trangThai'] ?? '-';
            ?>
            <tr style="border-bottom:1px solid #eef2f7;">
              <td style="padding:12px 14px;"><?php echo $i++; ?></td>
              <td style="padding:12px 14px;"><?php echo htmlspecialchars($maHienThi); ?></td>
              <td style="padding:12px 14px;"><?php echo htmlspecialchars($lyDo ?: '-'); ?></td>
              <td style="padding:12px 14px;" id="tl-status-<?php echo $maTL; ?>">
                <?php echo htmlspecialchars($trangThai); ?>
              </td>
              <td style="padding:12px 14px;">
                <button
                  type="button"
                  style="height:30px;border-radius:8px;padding:0 10px;border:1px solid #d1d5db;background:#f3f4f6;"
                  onclick="openTLDetail(<?php echo $maTL; ?>)"
                >Thao tác</button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- MODAL -->
  <div id="tlOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;opacity:0;transition:opacity .18s ease;">
    <div id="tlModal" style="width:820px;max-width:92vw;background:#fff;border-radius:12px;position:absolute;left:50%;top:50%;transform:translate(-50%,-48%);box-shadow:0 10px 30px rgba(0,0,0,.22);padding:18px;opacity:0;transition:opacity .18s ease, transform .18s ease;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div>
          <h3 style="margin:0 0 8px 0;">Chi tiết đề xuất thanh lý</h3>
        </div>
        <button type="button" onclick="closeTLModal()" style="border:0;background:#f1f5f9;border-radius:8px;width:32px;height:32px;cursor:pointer;">×</button>
      </div>

      <div style="margin-top:6px;">
        <div><b>Mã:</b> <span id="tl_code">-</span></div>
        <div style="margin-top:6px;"><b>Thiết bị:</b></div>
        <ul id="tl_items" style="margin:6px 0 0 18px;color:#334155;">
          <li>-</li>
        </ul>

        <div style="margin-top:10px;">
          <b>Lý do:</b> <span id="tl_reason">-</span>
        </div>

        <div style="margin-top:10px;">
          <b>Trạng thái:</b> <span id="tl_status">-</span>
        </div>
      </div>

      <hr style="border:0;border-top:1px solid #e2e8f0;margin:14px 0;">

      <div>
        <div style="font-weight:600;margin-bottom:6px;">Quyết định</div>
        <select id="tl_decision" style="width:100%;height:38px;border:1px solid #d0d7de;border-radius:8px;padding:0 10px;outline:none;">
          <option value="approve">Phê duyệt</option>
          <option value="reject">Từ chối</option>
        </select>
      </div>

      <!-- Không có ghi chú vì DB không có chỗ lưu -->

      <div style="display:flex;gap:10px;align-items:center;margin-top:14px;">
        <button type="button" onclick="submitTLDecision()" style="height:34px;border-radius:8px;padding:0 14px;border:0;background:#16a34a;color:#fff;cursor:pointer;">Lưu</button>
        <button type="button" explaining="close" onclick="closeTLModal()" style="height:34px;border-radius:8px;padding:0 14px;border:1px solid #d1d5db;background:#f3f4f6;cursor:pointer;">Hủy</button>
        <span id="tl_msg" style="color:#ef4444;margin-left:8px;"></span>
      </div>
    </div>
  </div>

  <script>
    const AJAX_URL_TL = '/quanlythietbithcs_unique/controllers/TV_duyet-thanhly.php';
    let currentTL = 0;

    function openTLModal() {
      const ov = document.getElementById('tlOverlay');
      const md = document.getElementById('tlModal');
      ov.style.display = 'block';
      requestAnimationFrame(() => {
        ov.style.opacity = '1';
        md.style.opacity = '1';
        md.style.transform = 'translate(-50%,-50%)';
      });
    }

    function closeTLModal() {
      const ov = document.getElementById('tlOverlay');
      const md = document.getElementById('tlModal');
      ov.style.opacity = '0';
      md.style.opacity = '0';
      md.style.transform = 'translate(-50%,-48%)';
      setTimeout(() => { ov.style.display = 'none'; }, 180);
      document.getElementById('tl_msg').textContent = '';
      currentTL = 0;
    }

    async function openTLDetail(maTL) {
      currentTL = maTL;
      document.getElementById('tl_msg').textContent = '';
      openTLModal();

      try {
        const res = await fetch(`${AJAX_URL_TL}?ajax=detail&maTL=${maTL}`, { credentials: 'same-origin' });
        const json = await res.json();

        if (!json.success) {
          document.getElementById('tl_msg').textContent = json.message || 'Lỗi tải dữ liệu';
          return;
        }

        const header = json.data.header;
        const items  = json.data.items || [];
        const lyDoTong = json.data.lyDoTong || '-';

        const ym = header.ngayLap ? `${new Date(header.ngayLap).getFullYear()}${String(new Date(header.ngayLap).getMonth()+1).padStart(2,'0')}` : '';
        const code = ym ? `TL-${ym}-${String(header.maTL).padStart(2,'0')}` : `TL-??????-${String(header.maTL).padStart(2,'0')}`;
        document.getElementById('tl_code').textContent = code;

        document.getElementById('tl_status').textContent = header.trangThai || '-';
        document.getElementById('tl_reason').textContent = lyDoTong;

        const ul = document.getElementById('tl_items');
        ul.innerHTML = '';
        if (!items.length) {
          ul.innerHTML = '<li></li>';
        } else {
          items.forEach(it => {
            const li = document.createElement('li');
            const tt = it.tinhTrang ? ` | Tình trạng: ${it.tinhTrang}` : '';
            li.textContent = `- ${it.tenTB} | SL: ${it.soLuong}${tt}`;
            ul.appendChild(li);
          });
        }

      } catch (e) {
        document.getElementById('tl_msg').textContent = 'Lỗi tải dữ liệu';
      }
    }

    async function submitTLDecision() {
      if (!currentTL) return;

      const decision = document.getElementById('tl_decision').value;
      document.getElementById('tl_msg').textContent = '';

      try {
        const form = new FormData();
        form.append('maTL', currentTL);
        form.append('decision', decision);

        const res = await fetch(`${AJAX_URL_TL}?ajax=update`, {
          method: 'POST',
          body: form,
          credentials: 'same-origin'
        });

        const json = await res.json();
        if (!json.success) {
          document.getElementById('tl_msg').textContent = json.message || 'Lỗi lưu';
          return;
        }

        // cập nhật trạng thái trên bảng
        const cell = document.getElementById('tl-status-' + currentTL);
        if (cell && json.newStatus) cell.textContent = json.newStatus;

        // ✅ duyệt/từ chối xong tự đóng modal
        closeTLModal();

      } catch (e) {
        document.getElementById('tl_msg').textContent = 'Lỗi lưu';
      }
    }

    // click ngoài modal để đóng
    document.getElementById('tlOverlay').addEventListener('click', function(e){
      if (e.target === this) closeTLModal();
    });
  </script>

</section>
