<?php // views/pages_hieu-truong/duyet-thanh-ly.php ?>
<section id="duyet-thanh-ly" class="trang-an" <?php echo ($active_tab !== 'duyet-thanh-ly') ? 'style="display:none;"' : ''; ?>>
    <?php
    require_once __DIR__ . '/../../models/Database.php';
    require_once __DIR__ . '/../../models/ThanhLy.php';
    $db = (new Database())->getConnection();
    $tl = new ThanhLy($db);
    $rows = $tl->getAll();
    ?>

    <div class="header-block">
        <h2>Duyệt thanh lý</h2>
        <button id="btnPrintList" class="btn btn-light">In danh sách</button>
    </div>

    <div class="filter-row">
        <input type="text" id="txtSearch" placeholder="Tìm theo mã đề xuất / thiết bị" />
        <select id="slTrangThai">
            <option value="">-- Trạng thái --</option>
            <option>Chờ duyệt</option>
            <option>Đã duyệt</option>
            <option>Từ chối</option>
        </select>
        <button class="btn btn-primary" id="btnFilter">Lọc</button>
        <button class="btn btn-outline" id="btnClear">Xóa lọc</button>
    </div>

    <div class="table-wrapper">
        <table class="bang-du-lieu" id="tblThanhLy">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mã đề xuất</th>
                    <th>Lý do thanh lý</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($rows && $rows->num_rows > 0): $i=1; while($r = $rows->fetch_assoc()): ?>
                <tr data-matl="<?php echo (int)$r['maTL']; ?>" data-dstb="<?php echo htmlspecialchars($r['dsTB'] ?? ''); ?>">
                    <td><?php echo $i++; ?></td>
                    <td>TL-<?php echo str_pad((int)$r['maTL'], 8, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($r['lyDo'] ?? ''); ?></td>
                    <td>
                        <span class="badge <?php 
                            echo ($r['trangThai']==='Đã duyệt'?'success':($r['trangThai']==='Từ chối'?'danger':'warning')); ?>">
                            <?php echo htmlspecialchars($r['trangThai']); ?>
                        </span>
                    </td>
                    <td><button class="btn btn-secondary btn-action" data-matl="<?php echo (int)$r['maTL']; ?>">Thao tác</button></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="5">Chưa có đề xuất thanh lý.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="modalTL" class="modal" style="display:none;">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3>Chi tiết đề xuất thanh lý</h3>
                <button id="btnCloseModal" class="close">×</button>
            </div>

            <div class="modal-body">
                <div class="kv">
                    <div class="kv-row">
                        <div class="kv-label">Mã:</div>
                        <div class="kv-value" id="m_maTL">—</div>
                    </div>
                </div>

                <!-- Card bảng chi tiết -->
                <div class="detail-card">
                    <div class="detail-card__title">Thiết bị</div>
                    <div class="detail-scroll">
                        <table class="detail-table" id="m_tblTB">
                            <thead>
                                <tr>
                                    <th style="min-width:260px">Thiết bị</th>
                                    <th style="width:90px">SL</th>
                                    <th style="width:160px">Tình trạng</th>
                                    <th style="min-width:260px">Lý do</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS đổ dữ liệu vào đây -->
                                <tr class="is-empty"><td colspan="4">Đang tải...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="fg-item">
                        <label for="m_trangThai">Quyết định</label>
                        <select id="m_trangThai">
                            <option value="Đã duyệt">Phê duyệt</option>
                            <option value="Từ chối">Từ chối</option>
                        </select>
                    </div>
                    <div class="fg-item fg-col-2">
                        <label for="m_ghiChu">Ghi chú</label>
                        <textarea id="m_ghiChu" rows="3" placeholder="Nội dung ghi chú khi duyệt/từ chối..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button id="btnSaveTL" class="btn btn-primary">Lưu</button>
                <button id="btnCancelTL" class="btn">Hủy</button>
            </div>
        </div>
    </div>
</section>

<style>
/* ====== Layout nền & bảng danh sách (giữ như bản đã cân đối) ====== */
:root{
  --bg:#f3f6fb; --card:#fff; --text:#1f2937; --muted:#6b7280; --line:#e5e7eb;
  --primary:#1677ff; --success:#0ea5e9; --warn:#f59e0b; --danger:#ef4444;
  --radius:14px; --gap:14px;
}
section#duyet-thanh-ly{background:linear-gradient(180deg,#f7f9fc 0%,#f2f6ff 100%);min-height:calc(100vh - 140px);padding:24px 18px 36px;color:var(--text)}
section#duyet-thanh-ly>.header-block,section#duyet-thanh-ly>.filter-row,section#duyet-thanh-ly>.table-wrapper{width:min(1180px,100%);margin-inline:auto}
.header-block{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
.header-block h2{font-size:28px;font-weight:700;margin:0;color:#0f172a}
.btn{padding:9px 14px;border-radius:10px;border:1px solid var(--line);background:#fff;cursor:pointer}
.btn:active{transform:translateY(1px)} .btn-light{background:#eef2ff;border-color:#e0e7ff}
.filter-row{display:grid;grid-template-columns:1fr 180px auto auto;gap:var(--gap);margin:8px auto 16px}
.filter-row input[type=text],.filter-row select{border:1px solid var(--line);border-radius:10px;padding:10px 12px;background:#fff;outline:none;transition:box-shadow .15s ease,border-color .15s ease}
.filter-row input[type=text]:focus,.filter-row select:focus{border-color:#bdd2ff;box-shadow:0 0 0 4px rgba(22,119,255,.08)}
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

/* ====== Modal chi tiết – bảng đẹp, cân đối ====== */
.modal{position:fixed;inset:0;background:rgba(15,23,42,.35);display:flex;align-items:center;justify-content:center;z-index:9999}
.modal-dialog{background:#fff;border-radius:16px;width:min(760px,92vw);box-shadow:0 24px 60px rgba(2,6,23,.25);overflow:hidden}
.modal-header,.modal-footer{padding:14px 18px;background:#fff}
.modal-header{border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between}
.modal-header h3{margin:0;font-size:20px}
.close{background:none;border:none;font-size:22px;cursor:pointer;line-height:1}
.modal-body{padding:16px 18px 8px}

/* Cặp nhãn – giá trị */
.kv{display:block;margin-bottom:8px}
.kv-row{display:grid;grid-template-columns:110px 1fr;gap:8px;margin-bottom:6px}
.kv-label{font-weight:600;color:#111827}
.kv-value{color:#111827}

/* Card chứa bảng chi tiết */
.detail-card{border:1px solid var(--line);border-radius:12px;background:#fbfdff}
.detail-card__title{padding:10px 12px;border-bottom:1px solid var(--line);font-weight:600;color:#0f172a}
.detail-scroll{max-height:280px;overflow:auto}
.detail-table{width:100%;border-collapse:separate;border-spacing:0}
.detail-table thead th{position:sticky;top:0;z-index:1;background:#f8fafc;border-bottom:1px solid var(--line);text-align:left;font-weight:600;color:#4b5563;padding:10px 12px}
.detail-table tbody td{padding:10px 12px;border-bottom:1px dashed #eef1f4;vertical-align:top}
.detail-table tbody tr:last-child td{border-bottom:none}
.detail-table .td-name{font-weight:600;color:#0f172a}
.td-state{white-space:nowrap}
.td-reason{color:#374151}

/* Lưới form dưới bảng */
.form-grid{display:grid;grid-template-columns:220px 1fr;gap:14px;margin-top:14px}
.fg-item label{font-weight:600;margin-bottom:6px;display:block}
textarea,.modal select{border:1px solid var(--line);border-radius:10px;padding:10px 12px;resize:vertical;background:#fff;outline:none}
textarea:focus,.modal select:focus{border-color:#bdd2ff;box-shadow:0 0 0 4px rgba(22,119,255,.08)}
.fg-col-2{grid-column:1/3}

/* Responsive */
@media (max-width: 768px){
  .filter-row{grid-template-columns:1fr}
  .form-grid{grid-template-columns:1fr}
  .fg-col-2{grid-column:auto}
}
</style>

<script>
(function(){
    const $ = (s, r=document)=>r.querySelector(s);
    const $$ = (s, r=document)=>Array.from(r.querySelectorAll(s));

    // Đường dẫn API an toàn (trước /views/)
    const BASE = window.location.pathname.replace(/\/views\/.*/, '');
    const API  = BASE + '/controllers/ThanhLyController.php';

    const modal = $("#modalTL");
    const m_maTL = $("#m_maTL");
    const m_tbl  = $("#m_tblTB").querySelector('tbody');
    const m_trangThai = $("#m_trangThai");
    const m_ghiChu    = $("#m_ghiChu");

    // Mở modal & load chi tiết
    $$("#tblThanhLy .btn-action").forEach(btn=>{
        btn.addEventListener('click', async ()=>{
            const maTL = btn.getAttribute('data-matl');
            await openModal(maTL);
        });
    });

    $("#btnCloseModal")?.addEventListener('click', closeModal);
    $("#btnCancelTL")?.addEventListener('click', closeModal);

    async function openModal(maTL){
        m_maTL.textContent = 'TL-' + String(maTL).padStart(8,'0');
        m_tbl.innerHTML = `<tr class="is-empty"><td colspan="4">Đang tải...</td></tr>`;
        m_trangThai.value = 'Đã duyệt';
        m_ghiChu.value = '';

        modal.style.display = 'flex';
        try{
            const res = await fetch(`${API}?action=detail&maTL=${maTL}`);
            const j = await res.json();
            if(!j.success){ m_tbl.innerHTML = `<tr><td colspan="4">Lỗi tải dữ liệu</td></tr>`; return; }

            if (!j.data.length){
                m_tbl.innerHTML = `<tr><td colspan="4">Không có thiết bị trong phiếu này.</td></tr>`;
                return;
            }

            // Render hàng chi tiết
            m_tbl.innerHTML = '';
            j.data.forEach(row=>{
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="td-name">${escapeHtml(row.tenTB || '')}</td>
                    <td style="text-align:center">${escapeHtml(row.soLuong ?? '')}</td>
                    <td class="td-state">${escapeHtml(row.tinhTrang || '')}</td>
                    <td class="td-reason">${escapeHtml(row.lyDoItem || '')}</td>
                `;
                m_tbl.appendChild(tr);
            });

            $("#btnSaveTL").setAttribute('data-matl', String(maTL));
        }catch(err){
            alert('Lỗi kết nối máy chủ');
            m_tbl.innerHTML = `<tr><td colspan="4">Lỗi kết nối.</td></tr>`;
        }
    }
    function closeModal(){ modal.style.display = 'none'; }

    // Lưu quyết định
    $("#btnSaveTL")?.addEventListener('click', async ()=>{
        const maTL = parseInt($("#btnSaveTL").getAttribute('data-matl')||'0',10);
        const body = new FormData();
        body.append('action','update');
        body.append('maTL', String(maTL));
        body.append('trangThai', m_trangThai.value);
        body.append('ghiChu', m_ghiChu.value.trim());

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
    $("#btnFilter")?.addEventListener('click', ()=>{
        const kw = ($("#txtSearch").value||'').toLowerCase();
        const st = $("#slTrangThai").value;
        $$("#tblThanhLy tbody tr").forEach(tr=>{
            const ma = tr.children[1].textContent.toLowerCase();
            const lydo = tr.children[2].textContent.toLowerCase();
            const trangThai = tr.children[3].innerText.trim();
            const matchKw = !kw || ma.includes(kw) || lydo.includes(kw) || (tr.dataset?.dstb||'').toLowerCase().includes(kw);
            const matchSt = !st || (st === trangThai);
            tr.style.display = (matchKw && matchSt) ? '' : 'none';
        });
    });
    $("#btnClear")?.addEventListener('click', ()=>{
        $("#txtSearch").value=''; $("#slTrangThai").value='';
        $("#btnFilter").click();
    });

    $("#btnPrintList")?.addEventListener('click', ()=> window.print());

    // Helper: escape HTML
    function escapeHtml(s){
        return String(s??'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));
    }
})();
</script>
