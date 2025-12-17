<?php
// views/pages_hieu-truong/ket-qua-kiem-ke.php

require_once __DIR__ . '/../../controllers/TV_ket-qua-kiem-ke.php';

$ctrl = new TV_KetQuaKiemKeController();
$vm = $ctrl->getViewModel();

$keyword = $vm['keyword'] ?? '';
$list    = $vm['list'] ?? [];

function formatMaKK($maKK, $ngayKK): string {
  $year = $ngayKK ? (int)date('Y', strtotime($ngayKK)) : (int)date('Y');
  return sprintf('KK-%d-%03d', $year, (int)$maKK);
}
?>

<section id="ket-qua-kiem-ke" class="trang-an" <?php echo ($active_tab != 'ket-qua-kiem-ke') ? 'style="display:none;"' : ''; ?>>

  <style>
    #ket-qua-kiem-ke .head{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
    #ket-qua-kiem-ke h2{margin:0;font-size:22px}
    #ket-qua-kiem-ke .btn-blue{background:#0d6efd;color:#fff;border:none;border-radius:8px;padding:8px 12px;cursor:pointer}

    #ket-qua-kiem-ke .filter{display:flex;gap:10px;flex-wrap:wrap;align-items:center;padding:10px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;margin:12px 0}
    #ket-qua-kiem-ke .filter input{padding:8px 10px;border:1px solid #d1d9e6;border-radius:10px;background:#fff;min-width:240px}
    #ket-qua-kiem-ke .btn-green{background:#10b981;color:#fff;border:none;border-radius:10px;padding:8px 14px;cursor:pointer}
    #ket-qua-kiem-ke .link{color:#2563eb;text-decoration:none;font-weight:500}
    #ket-qua-kiem-ke .link:hover{text-decoration:underline}

    #ket-qua-kiem-ke table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.1)}
    #ket-qua-kiem-ke th,#ket-qua-kiem-ke td{padding:12px 14px;border-bottom:1px solid #eee;text-align:left}
    #ket-qua-kiem-ke thead th{background:#f8fafc;color:#475569;font-weight:600;border-bottom:2px solid #e2e8f0}
    #ket-qua-kiem-ke tbody tr:hover{background:#f9fafb}
    #ket-qua-kiem-ke .col-stt{width:54px;text-align:center}
    #ket-qua-kiem-ke .btn-action{background:#e5e7eb;border:1px solid #d1d5db;border-radius:8px;padding:6px 10px;cursor:pointer}

    body.no-scroll{overflow:hidden}
    #kkModal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.45);z-index:9999;padding:18px}
    #kkModal.show{display:flex}
    #kkBox{width:min(900px,94vw);background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.25);padding:18px;position:relative}
    #kkClose{position:absolute;right:14px;top:12px;width:34px;height:34px;border:none;border-radius:8px;background:#f1f5f9;cursor:pointer;font-size:18px}
    #kkGrid{display:grid;grid-template-columns:1fr 1fr;gap:8px 30px;margin-top:8px}
    .label{font-weight:700;margin:10px 0 6px}
    #kkItems{line-height:1.6}
  </style>

  <div class="head">
    <h2>Kết quả kiểm kê</h2>
    <button class="btn-blue" type="button" onclick="window.print()">In danh sách</button>
  </div>

  <form class="filter" method="get">
    <input type="hidden" name="tab" value="ket-qua-kiem-ke">
    <input type="text" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Tìm theo mã kiểm kê / #">
    <button class="btn-green" type="submit">Lọc</button>
    <a class="link" href="?tab=ket-qua-kiem-ke">Xóa lọc</a>
  </form>

  <table>
    <thead>
      <tr>
        <th class="col-stt">STT</th>
        <th>Mã kiểm kê</th>
        <th style="width:140px;">Ngày kiểm kê</th>
        <th style="width:140px;">Loại kiểm kê</th>
        <th style="width:140px;">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($list)): ?>
        <tr><td colspan="6" style="text-align:center;padding:16px;color:#6b7280;">Chưa có đợt kiểm kê.</td></tr>
      <?php else: ?>
        <?php $stt=1; foreach($list as $row): ?>
          <?php
            $maKK = (int)$row['maKK'];
            $maHienThi = formatMaKK($maKK, $row['ngayKK'] ?? null);
          ?>
          <tr data-row-kk="<?php echo $maKK; ?>">
            <td class="col-stt"><?php echo $stt++; ?></td>
            <td><?php echo htmlspecialchars($maHienThi); ?></td>
            <td><?php echo !empty($row['ngayKK']) ? htmlspecialchars(date('d/m/Y', strtotime($row['ngayKK']))) : '-'; ?></td>
            <td><?php echo htmlspecialchars($row['loaiKiemKe'] ?? '-'); ?></td>
            <td>
              <button class="btn-action btn-open-kk" type="button" data-kk="<?php echo $maKK; ?>">Thao tác</button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Modal -->
  <div id="kkModal">
    <div id="kkBox">
      <button id="kkClose" type="button">×</button>

      <h3 style="margin:0 0 8px 0;">Chi tiết kiểm kê</h3>

      <div id="kkGrid">
        <div><b>Mã:</b> <span id="kkMa">-</span></div>
        <div><b>Ngày kiểm kê:</b> <span id="kkNgay">-</span></div>
        <div><b>Loại kiểm kê:</b> <span id="kkLoai">-</span></div>
      </div>

      <div class="label">Danh sách thiết bị kiểm kê:</div>
      <div id="kkItems">-</div>

      <div style="margin-top:10px;color:#475569;" id="kkMsg"></div>
    </div>
  </div>

  <script>
    var AJAX_URL_KK = '../controllers/TV_ket-qua-kiem-ke.php';

    function showKK(){
      document.getElementById('kkModal').classList.add('show');
      document.body.classList.add('no-scroll');
    }
    function hideKK(){
      document.getElementById('kkModal').classList.remove('show');
      document.body.classList.remove('no-scroll');
      document.getElementById('kkMsg').innerHTML = '';
    }

    document.getElementById('kkClose').onclick = hideKK;
    document.getElementById('kkModal').onclick = function(e){
      if(e.target.id === 'kkModal') hideKK();
    }

    var btns = document.getElementsByClassName('btn-open-kk');
    for(var i=0;i<btns.length;i++){
      btns[i].onclick = function(){
        var maKK = this.getAttribute('data-kk');
        showKK();
        loadKK(maKK);
      }
    }

    function loadKK(maKK){
      document.getElementById('kkMsg').innerHTML = 'Đang tải...';
      var xhr = new XMLHttpRequest();
      xhr.open('GET', AJAX_URL_KK + '?ajax=detail&maKK=' + encodeURIComponent(maKK), true);
      xhr.withCredentials = true;

      xhr.onreadystatechange = function(){
        if(xhr.readyState === 4){
          try{
            var res = JSON.parse(xhr.responseText);
            if(!res.success){
              document.getElementById('kkMsg').innerHTML = res.message || 'Lỗi tải';
              return;
            }

            var h = res.data.header;
            var items = res.data.items || [];

            var year = h.ngayKK ? (new Date(h.ngayKK)).getFullYear() : (new Date()).getFullYear();
            var maHienThi = 'KK-' + year + '-' + String(h.maKK).padStart(3,'0');

            document.getElementById('kkMa').innerHTML = maHienThi;
            document.getElementById('kkNgay').innerHTML = h.ngayKK ? (new Date(h.ngayKK)).toLocaleDateString('vi-VN') : '-';
            document.getElementById('kkLoai').innerHTML = h.loaiKiemKe || '-';

            if(items.length === 0){
              document.getElementById('kkItems').innerHTML = '<i>Không có chi tiết.</i>';
            } else {
              var html = '';
              for(var j=0;j<items.length;j++){
                var mon = items[j].tenMonHoc ? ' (' + items[j].tenMonHoc + ')' : '';
                html += '• ' + items[j].tenTB + mon
                      + ' - SL trước: ' + items[j].soLuongTruoc
                      + ' - SL thực tế: ' + items[j].soLuongThucTe
                      + ' - TT: ' + (items[j].tinhTrangTB || '-')
                      + '<br>';
              }
              document.getElementById('kkItems').innerHTML = html;
            }

            document.getElementById('kkMsg').innerHTML = '';
          }catch(e){
            document.getElementById('kkMsg').innerHTML = 'Lỗi dữ liệu trả về';
          }
        }
      }

      xhr.send();
    }
  </script>

</section>
