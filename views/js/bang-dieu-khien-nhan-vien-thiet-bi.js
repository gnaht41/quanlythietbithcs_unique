// UI-only. Không xử lý dữ liệu thật; backend nối sau.
// - Giữ Logout có xác nhận
// - Chuông thông báo
// - CRUD danh mục
// - Phiếu mượn: tạo/sửa, thêm item từ picker, xem, duyệt/từ chối, ghi nhận trả (UI)
(function () {
    // Helpers
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
    const openModal = (el) => { if (el) { el.classList.add('open'); document.body.classList.add('no-scroll'); } };
    const closeModal = (el) => { if (el) { el.classList.remove('open'); document.body.classList.remove('no-scroll'); } };

    // Logout
    $('#nut-dang-xuat')?.addEventListener('click', () => {
        if (confirm('Bạn có chắc muốn đăng xuất?')) {
            window.location.href = '../html/dang-nhap.html';
        }
    });

    // Sidebar navigation
    $$('.thanh-ben a').forEach(a => {
        a.addEventListener('click', (e) => {
            e.preventDefault();
            const pageId = a.dataset.page;
            $$('.thanh-ben a').forEach(x => x.classList.remove('active'));
            a.classList.add('active');
            $$('main > section.trang-an').forEach(p => p.style.display = 'none');
            $('#' + pageId).style.display = 'block';
        });
    });

    // Notifications (bell)
    const nutTB = $('#nut-thong-bao');
    const modalTB = $('#modal-thong-bao');
    const badgeTB = $('#so-luong-thong-bao');
    const setBadge = (n) => { if (!badgeTB) return; badgeTB.style.display = n > 0 ? 'inline-block' : 'none'; badgeTB.textContent = n; };
    const openTB = () => { openModal(modalTB); nutTB?.setAttribute('aria-expanded', 'true'); };
    const closeTB = () => { closeModal(modalTB); nutTB?.setAttribute('aria-expanded', 'false'); };

    nutTB?.addEventListener('click', (e) => { e.stopPropagation(); modalTB?.classList.contains('open') ? closeTB() : openTB(); });
    modalTB?.addEventListener('click', (e) => { if (e.target === modalTB) closeTB(); });
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modalTB?.classList.contains('open')) closeTB(); });
    $('#dong-thong-bao')?.addEventListener('click', closeTB);
    $('#dong-thong-bao-x')?.addEventListener('click', closeTB);
    $('#danh-dau-doc')?.addEventListener('click', () => { setBadge(0); closeTB(); });

    /* ================= Quản lý danh mục (UI) ================ */
    const modalTBien = $('#modal-thiet-bi');
    const formTBien = $('#form-thiet-bi');
    const tbodyDM = $('#bang-danh-muc');

    $('#nut-them-thiet-bi')?.addEventListener('click', () => {
        formTBien?.reset();
        $('#id-thiet-bi').value = '';
        $('#tieu-de-modal').textContent = 'Thêm thiết bị';
        openModal(modalTBien);
    });
    $('#dong-modal')?.addEventListener('click', () => closeModal(modalTBien));
    $('#huy-modal')?.addEventListener('click', () => closeModal(modalTBien));

    tbodyDM?.addEventListener('click', (e) => {
        const btn = e.target.closest('button'); if (!btn) return;
        const row = btn.closest('tr'); if (!row) return;

        if (btn.classList.contains('nut-sua')) {
            $('#tieu-de-modal').textContent = 'Sửa thiết bị';
            $('#id-thiet-bi').value = row.cells[0].textContent.trim();
            $('#ten-thiet-bi').value = row.cells[1].textContent.trim();
            $('#nhom-thiet-bi').value = row.cells[2].textContent.trim();
            $('#don-vi').value = row.cells[3].textContent.trim();
            $('#so-luong').value = row.cells[4].textContent.trim();
            $('#lop').value = row.cells[5].textContent.trim();
            $('#tinh-trang').value = row.cells[6].textContent.trim();
            $('#ghi-chu').value = row.cells[7].textContent.trim();
            openModal(modalTBien);
        }
        if (btn.classList.contains('nut-xoa')) {
            if (confirm('Xóa thiết bị này?')) row.remove();
        }
    });

    formTBien?.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = $('#id-thiet-bi').value.trim();
        const ten = $('#ten-thiet-bi').value.trim();
        const nhom = $('#nhom-thiet-bi').value.trim();
        const dv = $('#don-vi').value.trim();
        const sl = $('#so-luong').value.trim();
        const lop = $('#lop').value.trim();
        const tt = $('#tinh-trang').value.trim();
        const gc = $('#ghi-chu').value.trim();
        if (!ten || !nhom || !dv || !sl || !lop || !tt) { alert('Vui lòng nhập đủ thông tin bắt buộc!'); return; }

        if (!id) {
            const nextId = tbodyDM.rows.length + 1;
            const tr = tbodyDM.insertRow(-1);
            tr.innerHTML = `
        <td>${nextId}</td>
        <td>${ten}</td>
        <td>${nhom}</td>
        <td>${dv}</td>
        <td>${sl}</td>
        <td>${lop}</td>
        <td>${tt}</td>
        <td>${gc || ''}</td>
        <td><button class="nut-sua">Sửa</button> <button class="nut-xoa">Xóa</button></td>
      `;
        } else {
            const idx = Array.from(tbodyDM.rows).findIndex(r => r.cells[0].textContent.trim() === id);
            if (idx > -1) {
                const row = tbodyDM.rows[idx];
                row.cells[1].textContent = ten;
                row.cells[2].textContent = nhom;
                row.cells[3].textContent = dv;
                row.cells[4].textContent = sl;
                row.cells[5].textContent = lop;
                row.cells[6].textContent = tt;
                row.cells[7].textContent = gc;
            }
        }
        closeModal(modalTBien);
        alert('(UI) Lưu danh mục thành công!');
    });

    $('#nut-nhap-excel')?.addEventListener('click', () => {
        const fi = $('#file-excel');
        if (!fi || fi.files.length === 0) { alert('Vui lòng chọn file Excel!'); return; }
        alert(`(UI) Đã chọn file: ${fi.files[0].name} — xử lý ở backend.`);
    });

    /* ================= Phiếu mượn (UI) ================ */
    const modalPhieu = $('#modal-phieu');
    const modalPicker = $('#picker-thiet-bi');
    const modalXem = $('#modal-xem');
    const modalTra = $('#modal-tra');
    const dsItemBody = $('#ds-item-phieu');
    let itemAutoId = 0;

    // mở modal tạo phiếu
    $('#nut-them-phieu')?.addEventListener('click', () => {
        $('#tieu-de-phieu').textContent = 'Tạo phiếu mượn';
        $('#form-phieu')?.reset();
        dsItemBody.innerHTML = '';
        itemAutoId = 0;
        openModal(modalPhieu);
    });

    // đóng modal phiếu bằng các nút có data-close
    modalPhieu?.addEventListener('click', (e) => {
        if (e.target === modalPhieu) closeModal(modalPhieu);
        if (e.target.matches('[data-close]')) closeModal(modalPhieu);
    });
    modalPicker?.addEventListener('click', (e) => { if (e.target === modalPicker || e.target.matches('[data-close]')) closeModal(modalPicker); });
    modalXem?.addEventListener('click', (e) => { if (e.target === modalXem || e.target.matches('[data-close]')) closeModal(modalXem); });
    modalTra?.addEventListener('click', (e) => { if (e.target === modalTra || e.target.matches('[data-close]')) closeModal(modalTra); });

    // nút Thêm thiết bị -> mở picker
    $('#nut-them-item')?.addEventListener('click', () => openModal(modalPicker));

    // chọn thiết bị trong picker -> thêm dòng item vào phiếu
    $('#bang-picker')?.addEventListener('click', (e) => {
        const btn = e.target.closest('.chon-thiet-bi'); if (!btn) return;
        const ten = btn.dataset.ten;
        const dv = btn.dataset.dv;
        const lop = btn.dataset.lop;
        const tt = btn.dataset.tt;
        itemAutoId++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
      <td>${ten}</td>
      <td>${dv}</td>
      <td><input type="number" min="1" value="1" class="inp-sl" /></td>
      <td>${lop}</td>
      <td>${tt}</td>
      <td><button type="button" class="nut-huy nut-xoa-item">Xóa</button></td>
    `;
        dsItemBody.appendChild(tr);
        closeModal(modalPicker);
    });

    // xóa item trong phiếu
    dsItemBody?.addEventListener('click', (e) => {
        const btn = e.target.closest('.nut-xoa-item'); if (!btn) return;
        btn.closest('tr')?.remove();
    });

    // bảng phiếu: xem / sửa / duyệt / từ chối / ghi nhận trả
    $('#bang-phieu')?.addEventListener('click', (e) => {
        const btn = e.target.closest('button'); if (!btn) return;

        if (btn.classList.contains('nut-xem')) openModal(modalXem);
        if (btn.classList.contains('nut-sua')) { // mở modal phiếu ở trạng thái sửa
            $('#tieu-de-phieu').textContent = 'Sửa phiếu mượn';
            // demo: reset items & add 1 item mẫu
            $('#form-phieu')?.reset();
            dsItemBody.innerHTML = '';
            const demo = document.createElement('tr');
            demo.innerHTML = `<td>Máy chiếu Epson</td><td>Chiếc</td><td><input type="number" min="1" value="1" class="inp-sl" /></td><td>8</td><td>Tốt</td><td><button type="button" class="nut-huy nut-xoa-item">Xóa</button></td>`;
            dsItemBody.appendChild(demo);
            openModal(modalPhieu);
        }
        if (btn.classList.contains('nut-duyet')) alert('(UI) Đã duyệt phiếu!');
        if (btn.classList.contains('nut-tu-choi')) alert('(UI) Đã từ chối phiếu!');
        if (btn.classList.contains('nut-tra')) openModal(modalTra);
    });

    // In DS phiếu
    $('#nut-in-ds-phieu')?.addEventListener('click', () => window.print());

    /* ============== Bảo trì (UI) ============== */
    const modalBT = $('#modal-bao-tri');
    $('#nut-them-bao-tri')?.addEventListener('click', () => {
        $('#tieu-de-bao-tri').textContent = 'Thêm bảo trì';
        $('#form-bao-tri')?.reset();
        $('#id-bao-tri').value = '';
        openModal(modalBT);
    });
    $('#dong-bao-tri')?.addEventListener('click', () => closeModal(modalBT));
    $('#huy-bao-tri')?.addEventListener('click', () => closeModal(modalBT));

    const tbodyBT = $('#bang-bao-tri');
    tbodyBT?.addEventListener('click', (e) => {
        const btn = e.target.closest('.nut-sua'); if (!btn) return;
        const row = btn.closest('tr');
        $('#tieu-de-bao-tri').textContent = 'Sửa bảo trì';
        $('#id-bao-tri').value = row.cells[0].textContent.trim();
        $('#thiet-bi-bao-tri').value = row.cells[1].textContent.trim();
        $('#ngay-bao-tri').value = row.cells[2].textContent.trim();
        $('#mo-ta-bao-tri').value = row.cells[3].textContent.trim();
        $('#trang-thai-bao-tri').value = row.cells[4].textContent.trim();
        openModal(modalBT);
    });

    $('#form-bao-tri')?.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = $('#id-bao-tri').value.trim();
        const tb = $('#thiet-bi-bao-tri').value.trim();
        const ngay = $('#ngay-bao-tri').value.trim();
        const mt = $('#mo-ta-bao-tri').value.trim();
        const tt = $('#trang-thai-bao-tri').value.trim();
        if (!tb || !ngay || !mt || !tt) { alert('Nhập đủ thông tin!'); return; }

        if (!id) {
            const nextId = tbodyBT.rows.length + 1;
            const tr = tbodyBT.insertRow(-1);
            tr.innerHTML = `<td>${nextId}</td><td>${tb}</td><td>${ngay}</td><td>${mt}</td><td>${tt}</td><td><button class="nut-sua">Sửa</button></td>`;
        } else {
            const idx = Array.from(tbodyBT.rows).findIndex(r => r.cells[0].textContent.trim() === id);
            if (idx > -1) {
                const row = tbodyBT.rows[idx];
                row.cells[1].textContent = tb;
                row.cells[2].textContent = ngay;
                row.cells[3].textContent = mt;
                row.cells[4].textContent = tt;
            }
        }
        closeModal(modalBT);
        alert('(UI) Lưu bảo trì thành công!');
    });

    /* ============== Kiểm kê (UI) ============== */
    const tbodyKK = $('#bang-kiem-ke');
    tbodyKK?.addEventListener('click', (e) => {
        const btn = e.target.closest('.nut-cap-nhat-kk'); if (!btn) return;
        const row = btn.closest('tr');
        const duKien = parseInt(row.cells[3].textContent, 10);
        const thucTe = parseInt(row.querySelector('.so-luong-thuc-te')?.value, 10);
        if (Number.isNaN(thucTe)) { alert('Nhập SL thực tế hợp lệ!'); return; }
        const chenh = thucTe - duKien;
        row.querySelector('.chenh-lech').textContent = chenh === 0 ? '0' : (chenh > 0 ? `+${chenh} (tăng)` : `${chenh} (giảm)`);
        alert('(UI) Cập nhật kiểm kê!');
    });
    $('#nut-tao-dot-kiem-ke')?.addEventListener('click', () => alert('(UI) Tạo đợt kiểm kê mới.'));
    $('#nut-in-bien-ban')?.addEventListener('click', () => window.print());

    /* ============== Báo cáo (UI) ============== */
    $('#nut-tao-bao-cao')?.addEventListener('click', () => alert('(UI) Tạo báo cáo.'));
})();
