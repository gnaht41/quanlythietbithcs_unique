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

    // ===== Kế hoạch mua sắm =====
    const modalKH = $('#modal-ke-hoach');
    const formKH = $('#form-ke-hoach');
    const tbodyKH = $('#bang-ke-hoach');

    $('#nut-them-ke-hoach')?.addEventListener('click', () => {
        $('#tieu-de-modal').textContent = 'Thêm kế hoạch mua sắm';
        formKH?.reset();
        $('#id-ke-hoach').value = '';
        openModal(modalKH);
    });
    $('#dong-modal')?.addEventListener('click', () => closeModal(modalKH));
    $('#huy-modal')?.addEventListener('click', () => closeModal(modalKH));

    // Lưu (thêm/sửa) kế hoạch
    formKH?.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = ($('#id-ke-hoach').value || '').trim();
        const ten = ($('#thiet-bi').value || '').trim();
        const sl = ($('#so-luong').value || '').trim();
        const donvi = ($('#don-vi').value || '').trim();
        const lop = ($('#lop-ap-dung').value || '').trim();
        const lydo = ($('#ly-do').value || '').trim();
        const ghichu = ($('#ghi-chu').value || '').trim();

        if (!ten || !sl || !donvi || !lydo) {
            alert('Vui lòng nhập đầy đủ: Tên thiết bị, Số lượng, Đơn vị, Lý do.');
            return;
        }

        // xác định ID tiếp theo
        const nextId = () => {
            const ids = Array.from(tbodyKH.rows).map(r => parseInt(r.cells[0].textContent, 10));
            return (ids.length ? Math.max(...ids) : 0) + 1;
        };

        if (!id) { // thêm
            const tr = tbodyKH.insertRow(-1);
            tr.innerHTML = `
        <td>${nextId()}</td>
        <td>${ten}</td>
        <td>${sl}</td>
        <td>${donvi}</td>
        <td>${lop}</td>
        <td>${lydo}</td>
        <td>Chờ duyệt</td>
        <td>${ghichu || ''}</td>
        <td class="hanh-dong">
          <button class="nut-sua">Sửa</button>
          <button class="nut-xoa">Xóa</button>
          <button class="nut-gui">Gửi</button>
        </td>
      `;
        } else { // cập nhật
            const row = Array.from(tbodyKH.rows).find(r => r.cells[0].textContent.trim() === id);
            if (row) {
                row.cells[1].textContent = ten;
                row.cells[2].textContent = sl;
                row.cells[3].textContent = donvi;
                row.cells[4].textContent = lop;
                row.cells[5].textContent = lydo;
                row.cells[7].textContent = ghichu;
            }
        }

        closeModal(modalKH);
        alert('Lưu kế hoạch thành công!');
    });

    // Ủy quyền sự kiện Hành động (Sửa/Xóa/Gửi)
    tbodyKH?.addEventListener('click', (e) => {
        const btn = e.target.closest('button'); if (!btn) return;
        const row = btn.closest('tr');

        if (btn.classList.contains('nut-sua')) {
            $('#tieu-de-modal').textContent = 'Sửa kế hoạch mua sắm';
            $('#id-ke-hoach').value = row.cells[0].textContent.trim();
            $('#thiet-bi').value = row.cells[1].textContent.trim();
            $('#so-luong').value = row.cells[2].textContent.trim();
            $('#don-vi').value = row.cells[3].textContent.trim();
            $('#lop-ap-dung').value = row.cells[4].textContent.trim();
            $('#ly-do').value = row.cells[5].textContent.trim();
            $('#ghi-chu').value = row.cells[7].textContent.trim();
            openModal(modalKH);
        }

        if (btn.classList.contains('nut-xoa')) {
            if (confirm('Xóa kế hoạch này?')) row.remove();
        }

        if (btn.classList.contains('nut-gui')) {
            row.cells[6].textContent = 'Đã gửi';
            alert('Đã gửi kế hoạch lên Hiệu trưởng để phê duyệt!');
        }
    });

    // In kế hoạch (demo)
    $('#nut-in-ke-hoach')?.addEventListener('click', () => { window.print(); });

    // ===== Theo dõi tình hình thiết bị =====
    const tbodyTD = $('#bang-thiet-bi');
    const dataDemo = {
        'ngu-van': [
            { ten: 'Video/clip tác phẩm Truyện Kiều', danhMuc: 'Ngữ văn', tong: 1, tot: 1, hong: 0, sua: 0, chenh: '0', note: '' },
            { ten: 'Phim tư liệu Văn học dân gian', danhMuc: 'Ngữ văn', tong: 1, tot: 1, hong: 0, sua: 0, chenh: '0', note: '' },
        ],
        'toan': [
            { ten: 'Bộ vẽ trên bảng', danhMuc: 'Toán', tong: 7, tot: 7, hong: 0, sua: 0, chenh: '0', note: '' },
            { ten: 'Bộ Thống kê & Xác suất', danhMuc: 'Toán', tong: 7, tot: 6, hong: 1, sua: 0, chenh: '-1', note: 'Thiếu 1 bộ' },
        ],
        'ngoai-ngu': [
            { ten: 'Tivi 65"', danhMuc: 'Ngoại ngữ', tong: 1, tot: 1, hong: 0, sua: 0, chenh: '0', note: '' },
            { ten: 'Thiết bị âm thanh di động', danhMuc: 'Ngoại ngữ', tong: 1, tot: 1, hong: 0, sua: 0, chenh: '0', note: '' },
        ],
        'gdcd': [
            { ten: 'Bộ tranh truyền thống dân tộc', danhMuc: 'GDCD', tong: 1, tot: 1, hong: 0, sua: 0, chenh: '0', note: '' },
            { ten: 'Tranh phòng chống bạo lực gia đình', danhMuc: 'GDCD', tong: 1, tot: 1, hong: 0, sua: 0, chenh: '0', note: '' },
        ],
        'lsdl': [
            { ten: 'Quả địa cầu hành chính', danhMuc: 'Lịch sử & Địa lý', tong: 2, tot: 2, hong: 0, sua: 0, chenh: '0', note: '' },
            { ten: 'Bản đồ hành chính Việt Nam', danhMuc: 'Lịch sử & Địa lý', tong: 2, tot: 2, hong: 0, sua: 0, chenh: '0', note: '' },
        ],
        'khtn': [
            { ten: 'Bộ giá thí nghiệm', danhMuc: 'KHTN', tong: 2, tot: 2, hong: 0, sua: 0, chenh: '0', note: '' },
            { ten: 'Kính lúp', danhMuc: 'KHTN', tong: 10, tot: 9, hong: 1, sua: 0, chenh: '-1', note: 'Hỏng 1 cái' },
        ],
        'cong-nghe': [
            { ten: 'Bộ dụng cụ cơ khí', danhMuc: 'Công nghệ', tong: 1, tot: 1, hong: 0, sua: 0, chenh: '0', note: '' },
            { ten: 'Bộ vật liệu điện', danhMuc: 'Công nghệ', tong: 2, tot: 2, hong: 0, sua: 0, chenh: '0', note: '' },
        ],
        'tin-hoc': [
            { ten: 'Màn hình 65"', danhMuc: 'Tin học', tong: 2, tot: 2, hong: 0, sua: 0, chenh: '0', note: '' },
        ],
        'gdtc': [
            { ten: 'Quả bóng rổ', danhMuc: 'GDTC', tong: 5, tot: 5, hong: 0, sua: 0, chenh: '0', note: '' },
            { ten: 'Quả bóng bàn', danhMuc: 'GDTC', tong: 30, tot: 29, hong: 1, sua: 0, chenh: '-1', note: 'Mất 1 quả' },
        ],
        'am-nhac': [
            { ten: 'Electric keyboard', danhMuc: 'Âm nhạc', tong: 1, tot: 1, hong: 0, sua: 0, chenh: '0', note: '' },
            { ten: 'Recorder', danhMuc: 'Âm nhạc', tong: 6, tot: 6, hong: 0, sua: 0, chenh: '0', note: '' },
        ],
        'my-thuat': [
            { ten: 'Giá vẽ', danhMuc: 'Mỹ thuật', tong: 5, tot: 5, hong: 0, sua: 0, chenh: '0', note: '' },
        ],
        'hdtn': [
            { ten: 'Bộ tranh thiên tai', danhMuc: 'HĐ trải nghiệm', tong: 1, tot: 1, hong: 0, sua: 0, chenh: '0', note: '' },
        ],
        'dung-chung': [
            { ten: 'Bảng nhóm', danhMuc: 'Dùng chung', tong: 30, tot: 30, hong: 0, sua: 0, chenh: '0', note: '' },
            { ten: 'Tủ đựng thiết bị', danhMuc: 'Dùng chung', tong: 2, tot: 2, hong: 0, sua: 0, chenh: '0', note: '' },
        ],
    };

    $('#nut-loc')?.addEventListener('click', () => {
        const key = $('#loc-mon-hoc')?.value || '';
        if (!key) { alert('Vui lòng chọn môn học/danh mục!'); return; }
        tbodyTD.innerHTML = '';
        (dataDemo[key] || []).forEach(item => {
            const tr = tbodyTD.insertRow(-1);
            tr.innerHTML = `
        <td>${item.ten}</td>
        <td>${item.danhMuc}</td>
        <td>${item.tong}</td>
        <td>${item.tot}</td>
        <td>${item.hong}</td>
        <td>${item.sua}</td>
        <td>${item.chenh}</td>
        <td>${item.note || ''}</td>
      `;
        });
    });

})();
