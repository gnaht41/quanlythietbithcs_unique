<?php // views/pages_quan-tri-vien/nhat-ky.php ?>
<section id="nhat-ky" class="trang-an" <?php echo ($active_tab !== 'nhat-ky') ? 'style="display:none;"' : ''; ?>>
    <div class="header-block">
        <h2>Nhật ký hệ thống</h2>
        <button id="btnExportCSV" class="btn btn-light">Xuất CSV</button>
    </div>

    <div class="filter-row">
        <input type="text" id="q" placeholder="Từ khóa (email, hành động, đối tượng)" />
        <input type="date" id="from" />
        <input type="date" id="to" />
        <select id="role">
            <option value="">-- Vai trò --</option>
            <option value="1">Admin</option>
            <option value="2">Hiệu trưởng</option>
            <option value="3">Tổ trưởng</option>
            <option value="4">Giáo viên</option>
            <option value="5">Nhân viên thiết bị</option>
        </select>
        <select id="act">
            <option value="">-- Hành động --</option>
            <option>Duyệt kế hoạch</option>
            <option>Duyệt thanh lý</option>
            <option>Từ chối kế hoạch</option>
            <option>Từ chối thanh lý</option>
            <option>Thêm thiết bị</option>
            <option>Cập nhật thông tin</option>
            <option>Tạo phiếu mượn</option>
            <option>Ghi nhận báo cáo</option>
        </select>
        <button class="btn btn-primary" id="btnFilter">Lọc</button>
        <button class="btn btn-outline" id="btnClear">Xóa lọc</button>
    </div>

    <div class="table-wrapper">
        <table class="bang-du-lieu" id="tblLog">
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Người dùng</th>
                    <th>Vai trò</th>
                    <th>Hành động</th>
                    <th>Đối tượng</th>
                    <th>Kết quả</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="6">Đang tải...</td></tr>
            </tbody>
        </table>
    </div>
</section>

<style>
:root{--bg:#f3f6fb;--card:#fff;--text:#1f2937;--muted:#6b7280;--line:#e5e7eb;--primary:#1677ff;--radius:14px;--gap:14px}
section#nhat-ky{background:linear-gradient(180deg,#f7f9fc 0%,#f2f6ff 100%);min-height:calc(100vh - 140px);padding:24px 18px 36px;color:var(--text)}
section#nhat-ky>.header-block,section#nhat-ky>.filter-row,section#nhat-ky>.table-wrapper{width:min(1180px,100%);margin-inline:auto}
.header-block{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
.header-block h2{font-size:28px;font-weight:700;margin:0;color:#0f172a}
.btn{padding:9px 14px;border-radius:10px;border:1px solid var(--line);background:#fff;cursor:pointer}
.btn-light{background:#eef2ff;border-color:#e0e7ff}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.btn-outline{background:#fff}
.filter-row{display:grid;grid-template-columns:1fr 160px 160px 160px 180px auto auto;gap:var(--gap);margin:8px auto 16px}
.filter-row input[type=text],.filter-row select,.filter-row input[type=date]{border:1px solid var(--line);border-radius:10px;padding:10px 12px;background:#fff;outline:none}
.table-wrapper{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:0 10px 26px rgba(16,24,40,.06);padding:8px}
.bang-du-lieu{width:100%;border-collapse:separate;border-spacing:0 8px;font-size:15px}
.bang-du-lieu thead th{text-align:left;font-weight:600;color:var(--muted);padding:10px 14px}
.bang-du-lieu tbody tr{background:#fff;border:1px solid var(--line);box-shadow:0 4px 14px rgba(0,0,0,.04)}
.bang-du-lieu tbody tr td{padding:12px 14px;vertical-align:middle}
.bang-du-lieu tbody tr td:first-child{border-radius:10px 0 0 10px}
.bang-du-lieu tbody tr td:last-child{border-radius:0 10px 10px 0}
@media (max-width: 1024px){ .filter-row{grid-template-columns:1fr 1fr 1fr 1fr 1fr auto auto} }
@media (max-width: 768px){
  .filter-row{grid-template-columns:1fr}
  .bang-du-lieu thead{display:none}
  .bang-du-lieu tbody tr{display:grid;grid-template-columns:1fr 1fr;row-gap:8px}
  .bang-du-lieu tbody tr td{border-radius:0 !important}
}
</style>

<script>
(function(){
  const $ = (s, r=document)=>r.querySelector(s);
  const $$ = (s, r=document)=>Array.from(r.querySelectorAll(s));
  const BASE = window.location.pathname.replace(/\/views\/.*/, '');
  const API  = BASE + '/controllers/LogController.php';

  const q   = $("#q"), from = $("#from"), to = $("#to"),
        role = $("#role"), act = $("#act"),
        tableBody = $("#tblLog tbody");

  async function loadLogs(){
    tableBody.innerHTML = `<tr><td colspan="6">Đang tải...</td></tr>`;
    const params = new URLSearchParams({
      action: 'list',
      q: q.value.trim(),
      from: from.value,
      to: to.value,
      role: role.value,
      act: act.value
    });
    try{
      const res = await fetch(`${API}?${params.toString()}`);
      const j = await res.json();
      if(!j.success){ tableBody.innerHTML = `<tr><td colspan="6">Lỗi tải dữ liệu</td></tr>`; return; }
      renderRows(j.data||[]);
    }catch(e){
      tableBody.innerHTML = `<tr><td colspan="6">Lỗi kết nối máy chủ</td></tr>`;
    }
  }

  function renderRows(rows){
    if(!rows.length){
      tableBody.innerHTML = `<tr><td colspan="6">Không có dữ liệu</td></tr>`;
      return;
    }
    tableBody.innerHTML = '';
    rows.forEach(r=>{
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${escapeHtml(r.thoiGian||'')}</td>
        <td>${escapeHtml(r.email||'')}</td>
        <td>${escapeHtml(r.tenVT||'')}</td>
        <td>${escapeHtml(r.hanhDong||'')}</td>
        <td>${escapeHtml(r.doiTuong||'')}</td>
        <td>Thành công</td>
      `;
      tableBody.appendChild(tr);
    });
  }

  // Lọc
  $("#btnFilter").addEventListener('click', loadLogs);
  $("#btnClear").addEventListener('click', ()=>{
    q.value=''; from.value=''; to.value=''; role.value=''; act.value='';
    loadLogs();
  });

  // Xuất CSV
  $("#btnExportCSV").addEventListener('click', ()=>{
    const params = new URLSearchParams({
      action: 'export',
      q: q.value.trim(),
      from: from.value,
      to: to.value,
      role: role.value,
      act: act.value
    });
    window.location.href = `${API}?${params.toString()}`;
  });

  // Auto load lần đầu
  loadLogs();

  function escapeHtml(s){ return String(s??'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m])); }
})();
</script>
