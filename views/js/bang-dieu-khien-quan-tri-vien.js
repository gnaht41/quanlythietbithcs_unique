(function () {
    // ===== Helpers =====
    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
    const openModal = (el) => { if (el) { el.classList.add('open'); document.body.classList.add('no-scroll'); } };
    const closeModal = (el) => { if (el) { el.classList.remove('open'); document.body.classList.remove('no-scroll'); } };

    // ===== Logout =====
    $('#nut-dang-xuat')?.addEventListener('click', () => {
        if (confirm('Bạn có chắc muốn đăng xuất?')) {
            alert('Đăng xuất thành công!');
            window.location.href = '../html/dang-nhap.html';
        }
    });

    // ===== Sidebar navigation =====
    $$('.thanh-ben a').forEach(a => {
        a.addEventListener('click', (e) => {
            e.preventDefault();
            const pageId = a.dataset.page;
            $$('.thanh-ben a').forEach(x => x.classList.remove('active'));
            a.classList.add('active');
            $$('main > section.trang-an').forEach(s => s.style.display = 'none');
            const el = document.getElementById(pageId);
            if (el) el.style.display = 'block';
        });
    });

    // ===== Notifications (bell) =====
    const nutTB = $('#nut-thong-bao');
    const modalTB = $('#modal-thong-bao');
    const dongTB = $('#dong-thong-bao');
    const dongTBX = $('#dong-thong-bao-x');
    const markRead = $('#danh-dau-doc');
    const badge = $('#so-luong-thong-bao');

    const setBadge = (n) => { if (!badge) return; if (n > 0) { badge.textContent = n; badge.style.display = 'inline-block'; } else { badge.style.display = 'none'; } };
    const openTB = () => { openModal(modalTB); nutTB?.setAttribute('aria-expanded', 'true'); (dongTBX || modalTB.querySelector('button'))?.focus(); };
    const closeTB = () => { closeModal(modalTB); nutTB?.setAttribute('aria-expanded', 'false'); nutTB?.focus(); };

    nutTB?.addEventListener('click', (e) => { e.stopPropagation(); modalTB?.classList.contains('open') ? closeTB() : openTB(); });
    dongTB?.addEventListener('click', closeTB);
    dongTBX?.addEventListener('click', closeTB);
    modalTB?.addEventListener('click', (e) => { if (e.target === modalTB) closeTB(); });
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modalTB?.classList.contains('open')) closeTB(); });
    markRead?.addEventListener('click', () => { setBadge(0); closeTB(); });

    // ===== Quản lý tài khoản =====
    const tbodyND = $('#bang-nguoi-dung');
    const modalND = $('#modal-nguoi-dung');
    const formND = $('#form-nguoi-dung');

    $('#nut-them-nguoi-dung')?.addEventListener('click', () => {
        $('#tieu-de-nd').textContent = 'Thêm tài khoản';
        formND?.reset();
        $('#id-nd').value = '';
        $('#ngay-tao').valueAsDate = new Date();
        openModal(modalND);
    });
    $('#dong-modal-nd')?.addEventListener('click', () => closeModal(modalND));
    $('#huy-nd')?.addEventListener('click', () => closeModal(modalND));

    // Sửa/Xóa/Khoá/Reset PW
    tbodyND?.addEventListener('click', (e) => {
        const btn = e.target.closest('button'); if (!btn) return;
        const row = btn.closest('tr');

        if (btn.classList.contains('nut-sua')) {
            $('#tieu-de-nd').textContent = 'Sửa tài khoản';
            $('#id-nd').value = row.cells[0].textContent.trim();
            $('#ho-ten').value = row.cells[1].textContent.trim();
            $('#email').value = row.cells[2].textContent.trim();
            $('#vai-tro').value = row.cells[3].textContent.trim();
            $('#trang-thai').value = row.cells[4].textContent.trim();
            $('#ngay-tao').value = row.cells[5].textContent.trim();
            $('#mat-khau').value = '';
            openModal(modalND);
        }

        if (btn.classList.contains('nut-xoa')) {
            if (confirm('Xóa tài khoản này?')) row.remove();
        }

        if (btn.classList.contains('nut-khoa')) {
            const cur = row.cells[4].textContent.trim();
            row.cells[4].textContent = (cur === 'Hoạt động') ? 'Vô hiệu hóa' : 'Hoạt động';
            btn.textContent = (cur === 'Hoạt động') ? 'Kích hoạt' : 'Vô hiệu';
            btn.classList.toggle('nut-kich-hoat');
            alert('Cập nhật trạng thái tài khoản!');
        }

        if (btn.classList.contains('nut-reset')) {
            if (confirm('Đặt lại mật khẩu và gửi email hướng dẫn?')) alert('Đã đặt lại mật khẩu (demo).');
        }
    });

    formND?.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = $('#id-nd').value.trim();
        const hoten = $('#ho-ten').value.trim();
        const email = $('#email').value.trim();
        const vtro = $('#vai-tro').value.trim();
        const tt = $('#trang-thai').value.trim();
        const mk = $('#mat-khau').value.trim();
        const ngay = $('#ngay-tao').value || new Date().toISOString().split('T')[0];

        if (!hoten || !email || !vtro || !tt) { alert('Vui lòng nhập đủ các trường bắt buộc!'); return; }

        if (!id) {
            const nextId = tbodyND.rows.length
                ? Math.max(...Array.from(tbodyND.rows).map(r => parseInt(r.cells[0].textContent, 10))) + 1
                : 1;
            const newRow = tbodyND.insertRow(-1);
            newRow.innerHTML = `
        <td>${nextId}</td>
        <td>${hoten}</td>
        <td>${email}</td>
        <td>${vtro}</td>
        <td>${tt}</td>
        <td>${ngay}</td>
        <td class="hanh-dong">
          <button class="nut-sua">Sửa</button>
          <button class="nut-khoa">${tt === 'Hoạt động' ? 'Vô hiệu' : 'Kích hoạt'}</button>
          <button class="nut-xoa">Xóa</button>
          <button class="nut-reset">Đặt lại mật khẩu</button>
        </td>
      `;
            if (mk && mk.length < 6) alert('Mật khẩu khởi tạo nên từ 6 ký tự trở lên.');
        } else {
            const row = Array.from(tbodyND.rows).find(r => r.cells[0].textContent.trim() === id);
            if (row) {
                row.cells[1].textContent = hoten;
                row.cells[2].textContent = email;
                row.cells[3].textContent = vtro;
                row.cells[4].textContent = tt;
                row.cells[5].textContent = ngay;
                const btnKhoa = row.querySelector('.nut-khoa');
                if (btnKhoa) btnKhoa.textContent = (tt === 'Hoạt động') ? 'Vô hiệu' : 'Kích hoạt';
            }
        }
        closeModal(modalND);
        alert('Lưu tài khoản thành công!');
    });

    // Lọc người dùng (demo)
    $('#nut-ap-dung-loc')?.addEventListener('click', () => {
        const kw = ($('#loc-tu-khoa')?.value || '').toLowerCase();
        const vr = $('#loc-vai-tro')?.value || '';
        const st = $('#loc-trang-thai')?.value || '';
        Array.from(tbodyND?.rows || []).forEach(r => {
            const _ten = (r.cells[1].textContent || '').toLowerCase();
            const _email = (r.cells[2].textContent || '').toLowerCase();
            const _vr = r.cells[3].textContent || '';
            const _st = r.cells[4].textContent || '';
            const okKW = kw ? (_ten.includes(kw) || _email.includes(kw)) : true;
            const okVR = vr ? (_vr === vr) : true;
            const okST = st ? (_st === st) : true;
            r.style.display = (okKW && okVR && okST) ? '' : 'none';
        });
    });

    // ===== Phân quyền =====
    const defaultPolicies = {
        // 11 dòng module, mỗi dòng là 6 bit (Xem,Tạo,Sửa,Xóa,Duyệt,Xuất), nối bằng |
        'Admin': '111111|111110|111111|111110|111111|101111|111111|111111|111100|100101|100101',
        'Hiệu trưởng': '100011|100011|100111|100011|100111|100001|100111|100111|100000|100001|100001',
        'Tổ trưởng chuyên môn': '100001|100001|100101|100001|100101|100001|100001|100001|100000|100000|100001',
        'Giáo viên': '100000|100000|100100|100000|100000|100001|100000|100000|100000|100000|100001',
        'Nhân viên thiết bị': '111100|111100|111100|111100|111100|100001|100000|100000|100000|100000|100001'
    };

    const applyPolicyBits = (bits) => {
        const rows = $$('#tbody-quyen tr');
        const segs = bits.split('|');
        rows.forEach((row, i) => {
            const checks = $$('.quyen', row);
            const segment = segs[i] || '000000';
            checks.forEach((c, j) => {
                if (c.disabled) { c.checked = false; return; }
                c.checked = segment[j] === '1';
            });
        });
    };

    const readPolicyBits = () => {
        const rows = $$('#tbody-quyen tr');
        return rows.map(row => {
            return $$('.quyen', row).map(c => c.disabled ? '0' : (c.checked ? '1' : '0')).join('');
        }).join('|');
    };

    $('#nap-mac-dinh')?.addEventListener('click', () => {
        const role = $('#chon-vai-tro').value;
        const bits = defaultPolicies[role] || defaultPolicies['Giáo viên'];
        applyPolicyBits(bits);
        alert('Đã nạp quyền mặc định cho vai trò: ' + role);
    });

    $('#luu-phan-quyen')?.addEventListener('click', () => {
        const role = $('#chon-vai-tro').value;
        const bits = readPolicyBits();
        localStorage.setItem('perm_' + role, bits);
        alert('Đã lưu phân quyền cho vai trò: ' + role);
    });

    (function initPerm() {
        const role = $('#chon-vai-tro').value;
        const saved = localStorage.getItem('perm_' + role);
        applyPolicyBits(saved || defaultPolicies[role] || defaultPolicies['Giáo viên']);
    })();

    // ===== Nhật ký: lọc & xuất =====
    const tbodyNK = $('#bang-nhat-ky');
    $('#loc-nhat-ky')?.addEventListener('click', () => {
        const kw = ($('#nk-tu-khoa')?.value || '').toLowerCase();
        const d1 = $('#nk-tu-ngay')?.value ? new Date($('#nk-tu-ngay').value) : null;
        const d2 = $('#nk-den-ngay')?.value ? new Date($('#nk-den-ngay').value) : null;
        const role = $('#nk-vai-tro')?.value || '';
        const act = $('#nk-hanh-dong')?.value || '';

        Array.from(tbodyNK?.rows || []).forEach(r => {
            const time = r.cells[0].textContent.trim();
            const mail = (r.cells[1].textContent || '').toLowerCase();
            const vr = r.cells[2].textContent.trim();
            const ac = r.cells[3].textContent.trim();
            const obj = (r.cells[4].textContent || '').toLowerCase();
            const all = `${mail} ${obj} ${ac}`.toLowerCase();

            let ok = true;
            if (kw) ok = ok && all.includes(kw);
            if (role) ok = ok && (vr === role);
            if (act) ok = ok && (ac === act);

            if (d1 || d2) {
                const d = new Date(time.replace(' ', 'T'));
                if (d1) ok = ok && (d >= d1);
                if (d2) ok = ok && (d <= new Date(d2.getTime() + 24 * 60 * 60 * 1000 - 1));
            }
            r.style.display = ok ? '' : 'none';
        });
    });

    $('#xuat-nhat-ky')?.addEventListener('click', () => {
        const visibleRows = Array.from(tbodyNK.rows).filter(r => r.style.display !== 'none');
        const headers = ['Thời gian', 'Người dùng', 'Vai trò', 'Hành động', 'Đối tượng', 'Kết quả', 'IP/Thiết bị'];
        const csv = [
            headers.join(','),
            ...visibleRows.map(r => Array.from(r.cells).map(c => `"${c.textContent.replace(/"/g, '""')}"`).join(','))
        ].join('\n');

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = 'nhat-ky-he-thong.csv';
        document.body.appendChild(a); a.click(); a.remove();
        URL.revokeObjectURL(url);
    });

    // ===== Danh sách thiết bị: demo lọc UI =====
    const tbListBody = $('#bang-ds-thiet-bi');
    $('#nut-loc-tb')?.addEventListener('click', () => {
        const kw = ($('#tk-ten')?.value || '').toLowerCase();
        const dm = $('#tk-danh-muc')?.value || '';
        const tt = $('#tk-tinh-trang')?.value || '';
        const lop = ($('#tk-lop')?.value || '').replace(/\s/g, '');
        Array.from(tbListBody?.rows || []).forEach(r => {
            const ten = (r.cells[1].textContent || '').toLowerCase();
            const _dm = (r.cells[2].textContent || '');
            const _tt = (r.cells[6].textContent || '');
            const _lop = (r.cells[5].textContent || '').replace(/\s/g, '');
            const okKW = kw ? ten.includes(kw) : true;
            const okDM = dm ? _dm.toLowerCase().includes(dm) : true;
            const okTT = tt ? _tt === tt : true;
            const okLop = lop ? _lop.includes(lop) : true;
            r.style.display = (okKW && okDM && okTT && okLop) ? '' : 'none';
        });
    });
    $('#nut-xoa-loc-tb')?.addEventListener('click', () => {
        $('#tk-ten').value = ''; $('#tk-danh-muc').value = ''; $('#tk-tinh-trang').value = ''; $('#tk-lop').value = '';
        Array.from(tbListBody?.rows || []).forEach(r => r.style.display = '');
    });

})();
