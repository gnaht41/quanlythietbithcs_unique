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

    // ===== Navigation =====
    $$('.thanh-ben a').forEach(a => {
        a.addEventListener('click', (e) => {
            e.preventDefault();
            const pageId = a.dataset.page;
            $$('.thanh-ben a').forEach(x => x.classList.remove('active'));
            a.classList.add('active');
            $$('main > div.trang-an').forEach(p => p.style.display = 'none');
            const pageEl = document.getElementById(pageId);
            if (pageEl) pageEl.style.display = 'block';
        });
    });

    // ===== Notification (bell) =====
    const nutTB = $('#nut-thong-bao');
    const modalTB = $('#modal-thong-bao');
    const dongTB = $('#dong-thong-bao');
    const dongTBX = $('#dong-thong-bao-x');
    const badgeTB = $('#so-luong-thong-bao');
    const markRead = $('#danh-dau-doc');

    const setBadge = (n) => {
        if (!badgeTB) return;
        if (n > 0) { badgeTB.textContent = n; badgeTB.style.display = 'inline-block'; }
        else { badgeTB.style.display = 'none'; }
    };
    const openTB = () => {
        openModal(modalTB);
        nutTB?.setAttribute('aria-expanded', 'true');
        (dongTBX || modalTB.querySelector('button'))?.focus();
    };
    const closeTB = () => {
        closeModal(modalTB);
        nutTB?.setAttribute('aria-expanded', 'false');
        nutTB?.focus();
    };

    nutTB?.addEventListener('click', (e) => { e.stopPropagation(); modalTB?.classList.contains('open') ? closeTB() : openTB(); });
    dongTB?.addEventListener('click', closeTB);
    dongTBX?.addEventListener('click', closeTB);
    modalTB?.addEventListener('click', (e) => { if (e.target === modalTB) closeTB(); });
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modalTB?.classList.contains('open')) closeTB(); });
    markRead?.addEventListener('click', () => { setBadge(0); closeTB(); });
    if (badgeTB && (badgeTB.textContent.trim() === '' || badgeTB.textContent.trim() === '0')) setBadge(1);

    // ===== Quản lý danh mục thiết bị =====
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

    // Ủy quyền sự kiện sửa/xoá
    tbodyDM?.addEventListener('click', (e) => {
        const btn = e.target.closest('button');
        if (!btn) return;
        const row = btn.closest('tr');
        if (!row) return;

        if (btn.classList.contains('nut-sua')) {
            $('#tieu-de-modal').textContent = 'Sửa thiết bị';
            $('#id-thiet-bi').value = row.cells[0].textContent.trim();
            $('#ten-thiet-bi').value = row.cells[1].textContent.trim();
            $('#nhom-thiet-bi').value = row.cells[2].textContent.trim();
            $('#don-vi').value = row.cells[3].textContent.trim();
            $('#so-luong').value = row.cells[4].textContent.trim();
            $('#lop').value = row.cells[5].textContent.trim();
            $('#ghi-chu').value = row.cells[6].textContent.trim();
            openModal(modalTBien);
        }

        if (btn.classList.contains('nut-xoa')) {
            if (confirm('Xoá thiết bị này?')) {
                row.remove();
            }
        }
    });

    formTBien?.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = $('#id-thiet-bi').value.trim();
        const ten = $('#ten-thiet-bi').value.trim();
        const nhom = $('#nhom-thiet-bi').value.trim();
        const donvi = $('#don-vi').value.trim();
        const soLuong = $('#so-luong').value.trim();
        const lop = $('#lop').value.trim();
        const ghiChu = $('#ghi-chu').value.trim();

        if (!ten || !nhom || !donvi || !soLuong || !lop) {
            alert('Vui lòng nhập đủ các trường bắt buộc!');
            return;
        }

        if (!id) { // thêm mới
            const newRow = tbodyDM.insertRow(-1);
            const nextId = tbodyDM.rows.length; // đơn giản: số thứ tự = số dòng
            newRow.innerHTML = `
        <td>${nextId}</td>
        <td>${ten}</td>
        <td>${nhom}</td>
        <td>${donvi}</td>
        <td>${soLuong}</td>
        <td>${lop}</td>
        <td>${ghiChu || ''}</td>
        <td>
          <button class="nut-sua">Sửa</button>
          <button class="nut-xoa">Xóa</button>
        </td>
      `;
        } else { // cập nhật
            // tìm đúng dòng theo số TT
            const idx = Array.from(tbodyDM.rows).findIndex(r => r.cells[0].textContent.trim() === id);
            if (idx > -1) {
                const row = tbodyDM.rows[idx];
                row.cells[1].textContent = ten;
                row.cells[2].textContent = nhom;
                row.cells[3].textContent = donvi;
                row.cells[4].textContent = soLuong;
                row.cells[5].textContent = lop;
                row.cells[6].textContent = ghiChu;
            }
        }
        closeModal(modalTBien);
        alert('Lưu danh mục thành công!');
    });

    $('#nut-nhap-excel')?.addEventListener('click', () => {
        const fi = $('#file-excel');
        if (!fi || fi.files.length === 0) { alert('Vui lòng chọn file Excel!'); return; }
        const file = fi.files[0];
        const reader = new FileReader();
        reader.onload = () => {
            // demo: chỉ báo tên file; xử lý thực tế sẽ dùng thư viện đọc Excel
            alert(`Đã chọn file: ${file.name}. (Demo import, sẽ triển khai parser XLSX sau)`);
        };
        reader.readAsArrayBuffer(file);
    });

    // ===== Quản lý tình trạng =====
    $('#bang-tinh-trang')?.addEventListener('click', (e) => {
        const btn = e.target.closest('.nut-cap-nhat-tt');
        if (!btn) return;
        const row = btn.closest('tr');
        const select = row.querySelector('.tinh-trang-select');
        const ttMoi = select?.value || '';
        if (!ttMoi) { alert('Vui lòng chọn tình trạng!'); return; }
        row.querySelector('.tt-hien-tai').textContent = ttMoi;
        alert('Cập nhật tình trạng thành công!');
    });

    // ===== Quản lý mượn - trả =====
    $('#bang-muon-tra')?.addEventListener('click', (e) => {
        const btn = e.target.closest('button');
        if (!btn) return;
        const row = btn.closest('tr');

        if (btn.classList.contains('nut-duyet')) {
            row.cells[5].textContent = 'Đã duyệt';
            alert('Đã duyệt yêu cầu!');
        }
        if (btn.classList.contains('nut-tu-choi')) {
            row.cells[5].textContent = 'Từ chối';
            alert('Đã từ chối yêu cầu!');
        }
        if (btn.classList.contains('nut-tra')) {
            const tinhTrang = row.querySelector('.tinh-trang-tra')?.value.trim();
            if (!tinhTrang) { alert('Vui lòng nhập tình trạng khi trả!'); return; }
            row.cells[5].textContent = 'Đã trả';
            row.cells[6].textContent = tinhTrang;
            row.querySelector('.nhom-tra').innerHTML = ''; // ẩn controls trả
            alert('Đã ghi nhận trả thiết bị!');
        }
    });

    $('#nut-in-phieu')?.addEventListener('click', () => window.print());

    // ===== Quản lý bảo trì =====
    const modalBT = $('#modal-bao-tri');
    $('#nut-them-bao-tri')?.addEventListener('click', () => {
        $('#tieu-de-bao-tri').textContent = 'Thêm bảo trì';
        $('#form-bao-tri').reset();
        $('#id-bao-tri').value = '';
        openModal(modalBT);
    });
    $('#dong-bao-tri')?.addEventListener('click', () => closeModal(modalBT));
    $('#huy-bao-tri')?.addEventListener('click', () => closeModal(modalBT));

    const tbodyBT = $('#bang-bao-tri');
    tbodyBT?.addEventListener('click', (e) => {
        const btn = e.target.closest('.nut-sua');
        if (!btn) return;
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
        const moTa = $('#mo-ta-bao-tri').value.trim();
        const trangThai = $('#trang-thai-bao-tri').value.trim();
        if (!tb || !ngay || !moTa || !trangThai) { alert('Nhập đủ thông tin!'); return; }

        if (!id) {
            const newRow = tbodyBT.insertRow(-1);
            const nextId = tbodyBT.rows.length;
            newRow.innerHTML = `
        <td>${nextId}</td>
        <td>${tb}</td>
        <td>${ngay}</td>
        <td>${moTa}</td>
        <td>${trangThai}</td>
        <td><button class="nut-sua">Sửa</button></td>
      `;
        } else {
            const idx = Array.from(tbodyBT.rows).findIndex(r => r.cells[0].textContent.trim() === id);
            if (idx > -1) {
                const row = tbodyBT.rows[idx];
                row.cells[1].textContent = tb;
                row.cells[2].textContent = ngay;
                row.cells[3].textContent = moTa;
                row.cells[4].textContent = trangThai;
            }
        }
        closeModal(modalBT);
        alert('Lưu bảo trì thành công!');
    });

    // ===== Quản lý kiểm kê =====
    const tbodyKK = $('#bang-kiem-ke');
    tbodyKK?.addEventListener('click', (e) => {
        const btn = e.target.closest('.nut-cap-nhat-kk');
        if (!btn) return;
        const row = btn.closest('tr');
        const duKien = parseInt(row.cells[3].textContent, 10);
        const thucTe = parseInt(row.querySelector('.so-luong-thuc-te')?.value, 10);
        if (Number.isNaN(thucTe)) { alert('Nhập số lượng thực tế hợp lệ!'); return; }
        const chenh = thucTe - duKien;
        row.querySelector('.chenh-lech').textContent = chenh === 0 ? '0' : (chenh > 0 ? `+${chenh} (tăng)` : `${chenh} (giảm)`);
        alert('Cập nhật kiểm kê thành công!');
    });

    $('#nut-tao-dot-kiem-ke')?.addEventListener('click', () => {
        alert('Tạo đợt kiểm kê mới (demo).');
    });
    $('#nut-in-bien-ban')?.addEventListener('click', () => window.print());

    // ===== Lập báo cáo =====
    $('#nut-tao-bao-cao')?.addEventListener('click', () => {
        alert('Tạo báo cáo (demo).');
    });

})();
