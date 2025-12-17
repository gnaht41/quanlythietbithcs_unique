<?php
<<<<<<< Updated upstream
require_once __DIR__ . '/../../controllers/TV_duyet-mua-sam.php';

$ctrl = new TV_DuyetMuaSamController();
$vm = $ctrl->getViewModel();
=======
// views/pages_hieu-truong/duyet-mua-sam.php

require_once __DIR__ . '/../../controllers/TV_duyet-mua-sam.php';

$ctrl = new TV_DuyetMuaSamController();
$vm   = $ctrl->getViewModel();
>>>>>>> Stashed changes

$keyword = $vm['keyword'] ?? '';
$status  = $vm['status'] ?? '';
$list    = $vm['list'] ?? [];

<<<<<<< Updated upstream
function formatMaKeHoach($maMS, $ngayLap): string {
  $year = $ngayLap ? (int)date('Y', strtotime($ngayLap)) : (int)date('Y');
  return sprintf('KHM-%d-%03d', $year, (int)$maMS);
}
function tinhNamHoc($row): string {
  if (!empty($row['namHoc'])) return (string)$row['namHoc'];
  $y = !empty($row['ngayLap']) ? (int)date('Y', strtotime($row['ngayLap'])) : (int)date('Y');
  return $y . '-' . ($y + 1);
}
function lyDoMucDich($row): string {
  if (!empty($row['lyDo'])) return (string)$row['lyDo'];
  if (!empty($row['mucDich'])) return (string)$row['mucDich'];
  return '-';
=======
function tinhNamHocFromNgayLap($ngayLap): string {
    if (empty($ngayLap)) return '-';
    $y = (int)date('Y', strtotime($ngayLap));
    return $y . '-' . ($y + 1);
}

function formatMaKeHoach($maMS, $ngayLap): string {
    $y = !empty($ngayLap) ? (int)date('Y', strtotime($ngayLap)) : (int)date('Y');
    return sprintf('KHM-%d-%03d', $y, (int)$maMS);
>>>>>>> Stashed changes
}
?>

<section id="duyet-mua-sam" class="trang-an" <?php echo ($active_tab != 'duyet-mua-sam') ? 'style="display:none;"' : ''; ?>>

<<<<<<< Updated upstream
  <!-- CSS chỉ cho tab này -->
  <style>
    #duyet-mua-sam .head{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
    #duyet-mua-sam h2{margin:0;font-size:22px}
    #duyet-mua-sam .btn-blue{background:#0d6efd;color:#fff;border:none;border-radius:8px;padding:8px 12px;cursor:pointer}
    #duyet-mua-sam .filter{display:flex;gap:10px;flex-wrap:wrap;align-items:center;padding:10px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;margin:12px 0}
    #duyet-mua-sam .filter input,#duyet-mua-sam .filter select{padding:8px 10px;border:1px solid #d1d9e6;border-radius:10px;background:#fff}
    #duyet-mua-sam .btn-green{background:#10b981;color:#fff;border:none;border-radius:10px;padding:8px 14px;cursor:pointer}
    #duyet-mua-sam .link{color:#2563eb;text-decoration:none;font-weight:500}
    #duyet-mua-sam .link:hover{text-decoration:underline}

    #duyet-mua-sam table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.1)}
    #duyet-mua-sam th,#duyet-mua-sam td{padding:12px 14px;border-bottom:1px solid #eee;text-align:left}
    #duyet-mua-sam thead th{background:#f8fafc;color:#475569;font-weight:600;border-bottom:2px solid #e2e8f0}
    #duyet-mua-sam tbody tr:hover{background:#f9fafb}
    #duyet-mua-sam .col-stt{width:54px;text-align:center}

    #duyet-mua-sam .btn-action{background:#e5e7eb;border:1px solid #d1d5db;border-radius:8px;padding:6px 10px;cursor:pointer}

    /* Modal */
    body.no-scroll{overflow:hidden}
    #msModal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.45);z-index:9999;padding:18px}
    #msModal.show{display:flex}
    #msBox{width:min(820px,94vw);background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.25);padding:18px;position:relative}
    #msClose{position:absolute;right:14px;top:12px;width:34px;height:34px;border:none;border-radius:8px;background:#f1f5f9;cursor:pointer;font-size:18px}
    #msGrid{display:grid;grid-template-columns:1fr 1fr;gap:8px 30px;margin-top:8px}
    .label{font-weight:700;margin:10px 0 6px}
    #msItems{line-height:1.6}
    #msForm select,#msForm textarea{width:100%;padding:10px 12px;border:1px solid #d1d9e6;border-radius:10px}
    #msActions{display:flex;gap:10px;align-items:center;margin-top:12px}
  </style>

  <div class="head">
    <h2>Duyệt kế hoạch mua sắm</h2>
    <button class="btn-blue" type="button" onclick="window.print()">In danh sách</button>
  </div>

  <form class="filter" method="get">
    <input type="hidden" name="tab" value="duyet-mua-sam">
    <input type="text" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Tìm theo mã kế hoạch / #">
    <select name="status">
      <option value="">-- Trạng thái --</option>
      <option value="Chờ duyệt" <?php echo ($status==='Chờ duyệt')?'selected':''; ?>>Chờ duyệt</option>
      <option value="Đã duyệt" <?php echo ($status==='Đã duyệt')?'selected':''; ?>>Đã duyệt</option>
      <option value="Từ chối"  <?php echo ($status==='Từ chối')?'selected':'';  ?>>Từ chối</option>
    </select>
    <button class="btn-green" type="submit">Lọc</button>
    <a class="link" href="?tab=duyet-mua-sam">Xóa lọc</a>
  </form>

  <table>
    <thead>
      <tr>
        <th class="col-stt">#</th>
        <th>Mã kế hoạch</th>
        <th style="width:140px;">Năm học</th>
        <th>Lý do / Mục đích</th>
        <th style="width:130px;">Trạng thái</th>
        <th style="width:140px;">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($list)): ?>
        <tr><td colspan="6" style="text-align:center;padding:16px;color:#6b7280;">Chưa có kế hoạch mua sắm.</td></tr>
      <?php else: ?>
        <?php $stt=1; foreach($list as $row): ?>
          <?php
            $maMS=(int)$row['maMS'];
            $maHienThi=formatMaKeHoach($maMS,$row['ngayLap'] ?? null);
          ?>
          <tr data-row-ms="<?php echo $maMS; ?>">
            <td class="col-stt"><?php echo $stt++; ?></td>
            <td><?php echo htmlspecialchars($maHienThi); ?></td>
            <td><?php echo htmlspecialchars(tinhNamHoc($row)); ?></td>
            <td><?php echo htmlspecialchars(lyDoMucDich($row)); ?></td>
            <td class="status-cell"><?php echo htmlspecialchars($row['trangThai'] ?? ''); ?></td>
            <td>
              <button class="btn-action btn-open" type="button" data-ms="<?php echo $maMS; ?>">Thao tác</button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Modal -->
  <div id="msModal">
    <div id="msBox">
      <button id="msClose" type="button">×</button>

      <h3 style="margin:0 0 8px 0;">Chi tiết kế hoạch mua sắm</h3>

      <div id="msGrid">
        <div><b>Mã:</b> <span id="msMa">-</span></div>
        <div><b>Năm học:</b> <span id="msNamHoc">-</span></div>
        <div><b>Trạng thái:</b> <span id="msTrangThai">-</span></div>
        <div><b>Lý do / Mục đích:</b> <span id="msLyDo">-</span></div>
      </div>

      <div class="label">Hạng mục:</div>
      <div id="msItems">-</div>

      <hr style="margin:14px 0;border:none;border-top:1px solid #e2e8f0;">

      <form id="msForm">
        <input type="hidden" id="msId" value="">

        <div class="label">Quyết định</div>
        <select id="msDecision">
          <option value="approve">Phê duyệt</option>
          <option value="reject">Từ chối</option>
        </select>

        <div class="label">Ghi chú</div>
        <textarea id="msNote" rows="5" placeholder="Nội dung ghi chú khi duyệt/từ chối..."></textarea>

        <div id="msActions">
          <button class="btn-green" id="msSave" type="submit">Lưu</button>
          <button class="btn-action" id="msCancel" type="button">Hủy</button>
          <span id="msMsg" style="margin-left:6px;color:#475569;"></span>
        </div>
      </form>

=======
  <div class="head" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
    <div>
      <h2 style="margin:0 0 6px 0;">Duyệt kế hoạch mua sắm</h2>
    </div>
    <button type="button" class="btn btn-primary" onclick="window.print()">In danh sách</button>
  </div>

  <div class="card" style="background:#fff;border-radius:10px;padding:14px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <input type="hidden" name="tab" value="duyet-mua-sam">

      <input
        type="text"
        name="q"
        value="<?php echo htmlspecialchars($keyword); ?>"
        placeholder="Tìm theo mã kế hoạch / STT"
        style="height:36px;padding:0 12px;border:1px solid #d0d7de;border-radius:8px;outline:none;"
      >

      <select name="status" style="height:36px;padding:0 10px;border:1px solid #d0d7de;border-radius:8px;outline:none;">
        <option value="">-- Trạng thái --</option>
        <option value="Chờ duyệt" <?php echo ($status==='Chờ duyệt')?'selected':''; ?>>Chờ duyệt</option>
        <option value="Đã duyệt" <?php echo ($status==='Đã duyệt')?'selected':''; ?>>Đã duyệt</option>
        <option value="Từ chối"  <?php echo ($status==='Từ chối')?'selected':''; ?>>Từ chối</option>
      </select>

      <button type="submit" class="btn" style="height:36px;border-radius:8px;padding:0 14px;border:0;background:#16a34a;color:#fff;">Lọc</button>
      <a href="?tab=duyet-mua-sam" style="color:#2563eb;text-decoration:none;">Xóa lọc</a>
    </form>
  </div>

  <div class="card" style="margin-top:14px;background:#fff;border-radius:10px;padding:0;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr style="background:#f8fafc;border-bottom:1px solid #e5e7eb;">
          <th style="text-align:left;padding:12px 14px;width:50px;">STT</th>
          <th style="text-align:left;padding:12px 14px;">Mã kế hoạch</th>
          <th style="text-align:left;padding:12px 14px;width:160px;">Năm học</th>
          <th style="text-align:left;padding:12px 14px;width:140px;">Trạng thái</th>
          <th style="text-align:left;padding:12px 14px;width:140px;">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($list)): ?>
          <tr>
            <td colspan="5" style="padding:14px;color:#64748b;">Không có dữ liệu.</td>
          </tr>
        <?php else: ?>
          <?php $i=1; foreach ($list as $row): ?>
            <?php
              $maMS = (int)($row['maMS'] ?? 0);
              $ngayLap = $row['ngayLap'] ?? '';
              $maHienThi = formatMaKeHoach($maMS, $ngayLap);
              $namHoc = tinhNamHocFromNgayLap($ngayLap);
              $trangThai = $row['trangThai'] ?? '-';
            ?>
            <tr style="border-bottom:1px solid #eef2f7;">
              <td style="padding:12px 14px;"><?php echo $i++; ?></td>
              <td style="padding:12px 14px;"><?php echo htmlspecialchars($maHienThi); ?></td>
              <td style="padding:12px 14px;"><?php echo htmlspecialchars($namHoc); ?></td>
              <td style="padding:12px 14px;" id="ms-status-<?php echo $maMS; ?>">
                <?php echo htmlspecialchars($trangThai); ?>
              </td>
              <td style="padding:12px 14px;">
                <button
                  type="button"
                  class="btn"
                  style="height:30px;border-radius:8px;padding:0 10px;border:1px solid #d1d5db;background:#f3f4f6;"
                  onclick="openMSDetail(<?php echo $maMS; ?>)"
                >
                  Thao tác
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- MODAL -->
  <div id="msModalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;opacity:0;transition:opacity .18s ease;">
    <div id="msModal" style="width:820px;max-width:92vw;background:#fff;border-radius:12px;position:absolute;left:50%;top:50%;transform:translate(-50%,-48%);box-shadow:0 10px 30px rgba(0,0,0,.22);padding:18px;opacity:0;transition:opacity .18s ease, transform .18s ease;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div>
          <h3 style="margin:0 0 8px 0;">Chi tiết kế hoạch mua sắm</h3>
        </div>
        <button type="button" onclick="closeMSModal()" style="border:0;background:#f1f5f9;border-radius:8px;width:32px;height:32px;cursor:pointer;">×</button>
      </div>

      <div id="msDetailBody" style="display:grid;grid-template-columns:1fr 1fr;gap:10px 24px;margin-top:6px;color:#0f172a;">
        <div>
          <div><b>Mã:</b> <span id="ms_code">-</span></div>
          <div style="margin-top:6px;"><b>Trạng thái:</b> <span id="ms_status">-</span></div>
        </div>
        <div>
          <div><b>Năm học:</b> <span id="ms_namhoc">-</span></div>
        </div>

        <div style="grid-column:1 / -1;margin-top:6px;">
          <div><b>Hạng mục:</b></div>
          <ul id="ms_items" style="margin:6px 0 0 18px;color:#334155;">
            <li></li>
          </ul>
        </div>
      </div>

      <hr style="border:0;border-top:1px solid #e2e8f0;margin:14px 0;">

      <div>
        <div style="font-weight:600;margin-bottom:6px;">Quyết định</div>
        <select id="ms_decision" style="width:100%;height:38px;border:1px solid #d0d7de;border-radius:8px;padding:0 10px;outline:none;">
          <option value="approve">Phê duyệt</option>
          <option value="reject">Từ chối</option>
        </select>
      </div>

      <!-- ĐÃ BỎ Ô "GHI CHÚ" vì DB không có chỗ lưu -->

      <div style="display:flex;gap:10px;align-items:center;margin-top:14px;">
        <button type="button" onclick="submitMSDecision()" style="height:34px;border-radius:8px;padding:0 14px;border:0;background:#16a34a;color:#fff;cursor:pointer;">
          Lưu
        </button>
        <button type="button" onclick="closeMSModal()" style="height:34px;border-radius:8px;padding:0 14px;border:1px solid #d1d5db;background:#f3f4f6;cursor:pointer;">
          Hủy
        </button>
        <span id="ms_msg" style="color:#ef4444;margin-left:8px;"></span>
      </div>
>>>>>>> Stashed changes
    </div>
  </div>

  <script>
<<<<<<< Updated upstream
    // đường dẫn tới controller 
    var AJAX_URL = '../controllers/TV_duyet-mua-sam.php';

    function showModal(){
      document.getElementById('msModal').classList.add('show');
      document.body.classList.add('no-scroll');
    }
    function hideModal(){
      document.getElementById('msModal').classList.remove('show');
      document.body.classList.remove('no-scroll');
      document.getElementById('msMsg').innerHTML = '';
    }

    document.getElementById('msClose').onclick = hideModal;
    document.getElementById('msCancel').onclick = hideModal;

    // bấm ra ngoài modal để đóng
    document.getElementById('msModal').onclick = function(e){
      if(e.target.id === 'msModal') hideModal();
    }

    // mở chi tiết
    var buttons = document.getElementsByClassName('btn-open');
    for(var i=0;i<buttons.length;i++){
      buttons[i].onclick = function(){
        var maMS = this.getAttribute('data-ms');
        showModal();
        loadDetail(maMS);
      }
    }

    function loadDetail(maMS){
      document.getElementById('msMsg').innerHTML = 'Đang tải...';
      var xhr = new XMLHttpRequest();
      xhr.open('GET', AJAX_URL + '?ajax=detail&maMS=' + encodeURIComponent(maMS), true);
      xhr.withCredentials = true;
      xhr.onreadystatechange = function(){
        if(xhr.readyState === 4){
          try{
            var res = JSON.parse(xhr.responseText);
            if(!res.success){
              document.getElementById('msMsg').innerHTML = res.message || 'Lỗi tải';
              return;
            }
            var h = res.data.header;
            var items = res.data.items || [];

            // format mã KHM-YYYY-XXX
            var year = h.ngayLap ? (new Date(h.ngayLap)).getFullYear() : (new Date()).getFullYear();
            var maHienThi = 'KHM-' + year + '-' + String(h.maMS).padStart(3,'0');

            document.getElementById('msMa').innerHTML = maHienThi;
            document.getElementById('msNamHoc').innerHTML = h.namHoc || (year + '-' + (year+1));
            document.getElementById('msTrangThai').innerHTML = h.trangThai || '';
            document.getElementById('msLyDo').innerHTML = h.lyDo || h.mucDich || '-';
            document.getElementById('msId').value = h.maMS;

            if(items.length === 0){
              document.getElementById('msItems').innerHTML = '<i>Không có hạng mục.</i>';
            }else{
              var html = '';
              for(var j=0;j<items.length;j++){
                var mon = items[j].tenMonHoc ? ' (' + items[j].tenMonHoc + ')' : '';
                html += '• ' + items[j].tenTB + mon + ' - SL: ' + items[j].soLuong + '<br>';
              }
              document.getElementById('msItems').innerHTML = html;
            }

            document.getElementById('msMsg').innerHTML = '';
          }catch(e){
            document.getElementById('msMsg').innerHTML = 'Lỗi dữ liệu trả về';
          }
        }
      }
      xhr.send();
    }

    // Lưu duyệt/từ chối
    document.getElementById('msForm').onsubmit = function(e){
      e.preventDefault();

      var maMS = document.getElementById('msId').value;
      var decision = document.getElementById('msDecision').value;
      var ghiChu = document.getElementById('msNote').value;

      document.getElementById('msMsg').innerHTML = 'Đang lưu...';

      var xhr = new XMLHttpRequest();
      xhr.open('POST', AJAX_URL + '?ajax=update', true);
      xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=UTF-8');
      xhr.withCredentials = true;

      xhr.onreadystatechange = function(){
        if(xhr.readyState === 4){
          try{
            var res = JSON.parse(xhr.responseText);
            if(!res.success){
              document.getElementById('msMsg').innerHTML = res.message || 'Lưu thất bại';
              return;
            }

            // cập nhật trạng thái ở bảng
            if(res.newStatus){
              document.getElementById('msTrangThai').innerHTML = res.newStatus;
              var row = document.querySelector('tr[data-row-ms="'+maMS+'"]');
              if(row){
                var cell = row.querySelector('.status-cell');
                if(cell) cell.innerHTML = res.newStatus;
              }
            }

            document.getElementById('msMsg').innerHTML = res.message || 'Đã lưu';
          }catch(e){
            document.getElementById('msMsg').innerHTML = 'Lỗi khi lưu';
          }
        }
      }

      var body = 'maMS=' + encodeURIComponent(maMS)
               + '&decision=' + encodeURIComponent(decision)
               + '&ghiChu=' + encodeURIComponent(ghiChu);

      xhr.send(body);
    }
=======
    // Đổi về absolute path nếu bạn bị sai đường dẫn
    const AJAX_URL_MS = '/quanlythietbithcs_unique/controllers/TV_duyet-mua-sam.php';

    let currentMS = 0;

    function openMSModal() {
      const ov = document.getElementById('msModalOverlay');
      const md = document.getElementById('msModal');
      ov.style.display = 'block';
      // animation mượt
      requestAnimationFrame(() => {
        ov.style.opacity = '1';
        md.style.opacity = '1';
        md.style.transform = 'translate(-50%,-50%)';
      });
    }

    function closeMSModal() {
      const ov = document.getElementById('msModalOverlay');
      const md = document.getElementById('msModal');
      ov.style.opacity = '0';
      md.style.opacity = '0';
      md.style.transform = 'translate(-50%,-48%)';
      setTimeout(() => { ov.style.display = 'none'; }, 180);
      document.getElementById('ms_msg').textContent = '';
      currentMS = 0;
    }

    async function openMSDetail(maMS) {
      currentMS = maMS;
      document.getElementById('ms_msg').textContent = '';
      openMSModal();

      try {
        const res = await fetch(`${AJAX_URL_MS}?ajax=detail&maMS=${maMS}`, { credentials: 'same-origin' });
        const json = await res.json();

        if (!json.success) {
          document.getElementById('ms_msg').textContent = json.message || 'Lỗi tải dữ liệu';
          return;
        }

        const header = json.data.header;
        const items  = json.data.items || [];

        // hiển thị mã kiểu KHM-YYYY-XXX (giống ngoài danh sách)
        const y = header.ngayLap ? new Date(header.ngayLap).getFullYear() : (new Date()).getFullYear();
        const code = `KHM-${y}-${String(header.maMS).padStart(3,'0')}`;
        document.getElementById('ms_code').textContent = code;

        const namHoc = header.ngayLap ? `${y}-${y+1}` : '-';
        document.getElementById('ms_namhoc').textContent = namHoc;

        document.getElementById('ms_status').textContent = header.trangThai || '-';

        const ul = document.getElementById('ms_items');
        ul.innerHTML = '';
        if (!items.length) {
          ul.innerHTML = '<li>-</li>';
        } else {
          items.forEach(it => {
            const li = document.createElement('li');
            li.textContent = `${it.tenTB} - SL: ${it.soLuong}`;
            ul.appendChild(li);
          });
        }

      } catch (e) {
        document.getElementById('ms_msg').textContent = 'Lỗi tải dữ liệu';
      }
    }

    async function submitMSDecision() {
      if (!currentMS) return;

      const decision = document.getElementById('ms_decision').value;
      document.getElementById('ms_msg').textContent = '';

      try {
        const form = new FormData();
        form.append('maMS', currentMS);
        form.append('decision', decision);

        const res = await fetch(`${AJAX_URL_MS}?ajax=update`, {
          method: 'POST',
          body: form,
          credentials: 'same-origin'
        });

        const json = await res.json();
        if (!json.success) {
          document.getElementById('ms_msg').textContent = json.message || 'Lỗi lưu';
          return;
        }

        // cập nhật trạng thái ngay trên bảng
        const cell = document.getElementById('ms-status-' + currentMS);
        if (cell && json.newStatus) cell.textContent = json.newStatus;

        // ✅ yêu cầu của bạn: duyệt/từ chối xong tự động tắt form chi tiết
        closeMSModal();

      } catch (e) {
        document.getElementById('ms_msg').textContent = 'Lỗi lưu';
      }
    }

    // đóng modal khi bấm ra ngoài
    document.getElementById('msModalOverlay').addEventListener('click', function(e){
      if (e.target === this) closeMSModal();
    });
>>>>>>> Stashed changes
  </script>

</section>
