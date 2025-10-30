<?php // views/pages_hieu-truong/duyet-ke-hoach.php ?>
<section id="duyet-ke-hoach" class="trang-an" <?php echo ($active_tab !== 'duyet-ke-hoach') ? 'style="display:none;"' : ''; ?>>
    <?php
    require_once __DIR__ . '/../../models/Database.php';
    require_once __DIR__ . '/../../models/KeHoach.php';
    $db = (new Database())->getConnection();
    $kh = new KeHoach($db);
    $rows = $kh->getAll();
    ?>

    <div class="header-block">
        <h2>Duyệt kế hoạch mua sắm</h2>
        <button id="btnPrintList2" class="btn btn-light">In danh sách</button>
    </div>

    <div class="filter-row">
        <input type="text" id="txtSearch2" placeholder="Tìm theo mã kế hoạch / nội dung / mô tả" />
        <select id="slTrangThai2">
            <option value="">-- Trạng thái --</option>
            <option>Đang chờ</option>
            <option>Đang xét</option>
            <option>Đã duyệt</option>
            <option>Từ chối</option>
        </select>
        <button class="btn btn-primary" id="btnFilter2">Lọc</button>
        <button class="btn btn-outline" id="btnClear2">Xóa lọc</button>
    </div>

    <div class="table-wrapper">
        <table class="bang-du-lieu" id="tblKeHoach">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mã kế hoạch</th>
                    <th>Nội dung</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($rows && $rows->num_rows > 0): $i=1; while($r = $rows->fetch_assoc()): ?>
                <tr data-makh="<?php echo htmlspecialchars($r['maKH']); ?>" 
                    data-preview="<?php echo htmlspecialchars($r['moTaDaiDien'] ?? ''); ?>">
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($r['maKH']); ?></td>
                    <td>
                        <div class="line-1-2">
                            <div class="text-strong"><?php echo htmlspecialchars($r['noiDung'] ?? ''); ?></div>
                            <?php if(!empty($r['moTaDaiDien'])): ?>
                            <div class="text-muted sm">VD: <?php echo htmlspecialchars($r['moTaDaiDien']); ?> (<?php echo (int)$r['soMuc']; ?> mục)</div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="badge <?php 
                            $st = $r['trangThai'];
                            echo ($st==='Đã duyệt'?'success':($st==='Từ chối'?'danger':'warning')); ?>">
                            <?php echo htmlspecialchars($r['trangThai']); ?>
                        </span>
                    </td>
                    <td><button class="btn btn-secondary btn-action" data-makh="<?php echo htmlspecialchars($r['maKH']); ?>">Thao tác</button></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="5">Chưa có kế hoạch mua sắm.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal chi tiết -->
    <div id="modalKH" class="modal" style="display:none;">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3>Chi tiết kế hoạch mua sắm</h3>
                <button id="btnCloseModal2" class="close">×</button>
            </div>

            <div class="modal-body">
                <div class="kv">
                    <div class="kv-row">
                        <div class="kv-label">Mã:</div>
                        <div class="kv-value" id="k_maKH">—</div>
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-card__title">Hạng mục</div>
                    <div class="detail-scroll">
                        <table class="detail-table" id="k_tblCT">
                            <thead>
                                <tr>
                                    <th style="width:80px">#</th>
                                    <th>Hạng mục / Mô tả</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="is-empty"><td colspan="2">Đang tải...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="fg-item">
                        <label for="k_trangThai">Quyết định</label>
                        <select id="k_trangThai">
                            <option value="Đã duyệt">Phê duyệt</option>
                            <option value="Từ chối">Từ chối</option>
                        </select>
                    </div>
                    <div class="fg-item fg-col-2">
                        <label for="k_ghiChu">Ghi chú</label>
                        <textarea id="k_ghiChu" rows="3" placeholder="Nội dung ghi chú khi duyệt/từ chối..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button id="btnSaveKH" class="btn btn-primary">Lưu</button>
                <button id="btnCancelKH" class="btn">Hủy</button>
            </div>
        </div>
    </div>
</section>

<style>
/* ——— giữ nguyên style nền & bảng như trang thanh lý ——— */
:root{--bg:#f3f6fb;--card:#fff;--text:#1f2937;--muted:#6b7280;--line:#e5e7eb;--primary:#1677ff;--success:#0ea5e9;--warn:#f59e0b;--danger:#ef4444;--radius:14px;--gap:14px}
section#duyet-ke-hoach{background:linear-gradient(180deg,#f7f9fc 0%,#f2f6ff 100%);min-height:calc(100vh - 140px);padding:24px 18px 36px;color:var(--text)}
section#duyet-ke-hoach>.header-block,section#duyet-ke-hoach>.filter-row,section#duyet-ke-hoach>.table-wrapper{width:min(1180px,100%);margin-inline:auto}
.header-block{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
.header-block h2{font-size:28px;font-weight:700;margin:0;color:#0f172a}
.btn{padding:9px 14px;border-radius:10px;border:1px solid var(--line);background:#fff;cursor:pointer}
.btn-light{background:#eef2ff;border-color:#e0e7ff}
.filter-row{display:grid;grid-template-columns:1fr 180px auto auto;gap:var(--gap);margin:8px auto 16px}
.filter-row input[type=text],.filter-row select{border:1px solid var(--line);border-radius:10px;padding:10px 12px;background:#fff;outline:none}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary)}
.table-wrapper{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:0 10px 26px rgba(16,24,40,.06);padding:8px}
.bang-du-lieu{width:100%;border-collapse:separate;border-spacing:0 8px;font-size:15px}
.bang-du-lieu thead th{text-align:left;font-weight:600;color:var(--muted);padding:10px 14px}
.bang-du-lieu tbody tr{background:#fff;border:1px solid var(--line);box-shadow:0 4px 14px rgba(0,0,0,.04)}
.bang-du-lieu tbody tr td{padding:12px 14px;vertical-align:middle}
.bang-du-lieu tbody tr td:first-child{border-radius:10px 0 0 10px;width:56px;color:#64748b}
.bang-du-lieu tbody tr td:last-child{border-radius:0 10px 10px 0;width:120px}
.badge{display:inline-block;padding:6px 10px;border-radius:999px;font-size:12.5px;font-weight:600}
.badge.success{background:#e6fffb;color:#0b7a75;border:1px solid #b3f7ef}
.badge.warning{background:#fff7e6;color:#ad6800;border:1px solid #ffe1af}
.badge.danger{background:#ffecec;color:#a8071a;border:1px solid #ffcccc}
.line-1-2 .text-strong{font-weight:600}
.text-muted.sm{font-size:12.5px;color:#6b7280;margin-top:2px}

/* Modal (giống trang thanh lý) */
.modal{position:fixed;inset:0;background:rgba(15,23,42,.35);display:flex;align-items:center;justify-content:center;z-index:9999}
.modal-dialog{background:#fff;border-radius:16px;width:min(760px,92vw);box-shadow:0 24px 60px rgba(2,6,23,.25);overflow:hidden}
.modal-header,.modal-footer{padding:14px 18px;background:#fff}
.modal-header{border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between}
.modal-header h3{margin:0;font-size:20px}
.close{background:none;border:none;font-size:22px;cursor:pointer;line-height:1}
.modal-body{padding:16px 18px 8px}
.kv{display:block;margin-bottom:8px}
.kv-row{display:grid;grid-template-columns:110px 1fr;gap:8px;margin-bottom:6px}
.kv-label{font-weight:600;color:#111827}
.kv-value{color:#111827}

.detail-card{border:1px solid var(--line);border-radius:12px;background:#fbfdff}
.detail-card__title{padding:10px 12px;border-bottom:1px solid var(--line);font-weight:600;color:#0f172a}
.detail-scroll{max-height:280px;overflow:auto}
.detail-table{width:100%;border-collapse:separate;border-spacing:0}
.detail-table thead th{position:sticky;top:0;z-index:1;background:#f8fafc;border-bottom:1px solid var(--line);text-align:left;font-weight:600;color:#4b5563;padding:10px 12px}
.detail-table tbody td{padding:10px 12px;border-bottom:1px dashed #eef1f4;vertical-align:top}
.detail-table tbody tr:last-child td{border-bottom:none}

.form-grid{display:grid;grid-template-columns:220px 1fr;gap:14px;margin-top:14px}
.fg-item label{font-weight:600;margin-bottom:6px;display:block}
textarea,.modal select{border:1px solid var(--line);border-radius:10px;padding:10px 12px;background:#fff;outline:none}
textarea:focus,.modal select:focus{border-color:#bdd2ff;box-shadow:0 0 0 4px rgba(22,119,255,.08)}
.fg-col-2{grid-column:1/3}

@media (max-width:768px){
  .filter-row{grid-template-columns:1fr}
  .form-grid{grid-template-columns:1fr}
  .fg-col-2{grid-column:auto}
}
</style>

<script>
(function(){
    const $ = (s, r=document)=>r.querySelector(s);
    const $$ = (s, r=document)=>Array.from(r.querySelectorAll(s));

    // API an toàn (trước /views/)
    const BASE = window.location.pathname.replace(/\/views\/.*/, '');
    const API  = BASE + '/controllers/KeHoachController.php';

    const modal = $("#modalKH");
    const k_maKH = $("#k_maKH");
    const k_tbl  = $("#k_tblCT").querySelector('tbody');
    const k_trangThai = $("#k_trangThai");
    const k_ghiChu    = $("#k_ghiChu");

    // Mở modal & load chi tiết
    $$("#tblKeHoach .btn-action").forEach(btn=>{
        btn.addEventListener('click', async ()=>{
            const maKH = btn.getAttribute('data-makh');
            await openModal(maKH);
        });
    });

    $("#btnCloseModal2")?.addEventListener('click', closeModal);
    $("#btnCancelKH")?.addEventListener('click', closeModal);

    async function openModal(maKH){
        k_maKH.textContent = maKH;
        k_tbl.innerHTML = `<tr class="is-empty"><td colspan="2">Đang tải...</td></tr>`;
        k_trangThai.value = 'Đã duyệt';
        k_ghiChu.value = '';

        modal.style.display = 'flex';
        try{
            const res = await fetch(`${API}?action=detail&maKH=${encodeURIComponent(maKH)}`);
            const j = await res.json();
            if(!j.success){ k_tbl.innerHTML = `<tr><td colspan="2">Lỗi tải dữ liệu</td></tr>`; return; }

            if (!j.data.length){
                k_tbl.innerHTML = `<tr><td colspan="2">Không có hạng mục trong kế hoạch này.</td></tr>`;
                return;
            }

            k_tbl.innerHTML = '';
            j.data.forEach((row, idx)=>{
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="text-align:center">${idx+1}</td>
                    <td>${escapeHtml(row.moTa || '')}</td>
                `;
                k_tbl.appendChild(tr);
            });

            $("#btnSaveKH").setAttribute('data-makh', String(maKH));
        }catch(err){
            alert('Lỗi kết nối máy chủ');
            k_tbl.innerHTML = `<tr><td colspan="2">Lỗi kết nối.</td></tr>`;
        }
    }
    function closeModal(){ modal.style.display = 'none'; }

    // Lưu duyệt / từ chối
    $("#btnSaveKH")?.addEventListener('click', async ()=>{
        const maKH = $("#btnSaveKH").getAttribute('data-makh') || '';
        const body = new FormData();
        body.append('action','update');
        body.append('maKH', maKH);
        body.append('trangThai', k_trangThai.value);
        body.append('ghiChu', k_ghiChu.value.trim());

        try{
            const res = await fetch(API, { method:'POST', body });
            const j = await res.json();
            if(j.success){
                alert('Cập nhật thành công');
                location.reload();
            }else{
                alert(j.message || 'Không thể lưu, vui lòng thử lại');
            }
        }catch(e){
            alert('Lỗi kết nối máy chủ');
        }
    });

    // Lọc danh sách (client-side)
    $("#btnFilter2")?.addEventListener('click', ()=>{
        const kw = ($("#txtSearch2").value||'').toLowerCase();
        const st = $("#slTrangThai2").value;
        $$("#tblKeHoach tbody tr").forEach(tr=>{
            const ma = tr.children[1].textContent.toLowerCase();
            const nd = tr.children[2].textContent.toLowerCase();
            const trangThai = tr.children[3].innerText.trim();
            const matchKw = !kw || ma.includes(kw) || nd.includes(kw) || (tr.dataset?.preview||'').toLowerCase().includes(kw);
            const matchSt = !st || (st === trangThai);
            tr.style.display = (matchKw && matchSt) ? '' : 'none';
        });
    });
    $("#btnClear2")?.addEventListener('click', ()=>{
        $("#txtSearch2").value=''; $("#slTrangThai2").value='';
        $("#btnFilter2").click();
    });

    $("#btnPrintList2")?.addEventListener('click', ()=> window.print());

    // Escaper
    function escapeHtml(s){
        return String(s??'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));
    }
})();
</script>
