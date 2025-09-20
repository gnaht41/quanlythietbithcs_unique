// ========== Đăng xuất ==========
(function () {
    const btnLogout = document.getElementById('nut-dang-xuat');
    if (btnLogout) {
        btnLogout.addEventListener('click', () => {
            if (confirm('Bạn có chắc muốn đăng xuất?')) {
                alert('Đăng xuất thành công!');
                // Điều chỉnh đường dẫn nếu khác dự án của bạn
                window.location.href = '../html/dang-nhap.html';
            }
        });
    }

    // ========== Điều hướng ==========
    document.querySelectorAll('.thanh-ben a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const pageId = link.dataset.page;
            document.querySelectorAll('.thanh-ben a').forEach(a => a.classList.remove('active'));
            link.classList.add('active');
            // Ẩn tất cả các trang
            document.querySelectorAll('main > div.trang-an').forEach(page => page.style.display = 'none');
            // Hiện trang được chọn
            const pageEl = document.getElementById(pageId);
            if (pageEl) pageEl.style.display = 'block';
        });
    });

    // ========== Tìm kiếm thiết bị ==========
    const nutTim = document.getElementById('nut-tim-kiem');
    if (nutTim) {
        nutTim.addEventListener('click', () => {
            const ten = (document.getElementById('tim-kiem-ten')?.value || '').toLowerCase();
            const danhMuc = document.getElementById('tim-kiem-danh-muc')?.value || '';
            const tinhTrang = document.getElementById('tim-kiem-tinh-trang')?.value || '';
            const table = document.getElementById('bang-tim-kiem');
            if (!table) return;
            const rows = table.getElementsByTagName('tr');
            for (let row of rows) {
                const cells = row.getElementsByTagName('td');
                if (cells.length === 0) continue; // skip header
                const tenThietBi = (cells[0].textContent || '').toLowerCase();
                const dm = cells[1].textContent || '';
                const tt = cells[2].textContent || '';
                const matchTen = ten ? tenThietBi.includes(ten) : true;
                const matchDM = danhMuc ? dm === danhMuc : true;
                const matchTT = tinhTrang ? tt === tinhTrang : true;
                row.style.display = (matchTen && matchDM && matchTT) ? '' : 'none';
            }
        });
    }

    // ========== Modals helpers ==========
    function openModal(el) { if (el) { el.classList.add('open'); document.body.classList.add('no-scroll'); } }
    function closeModal(el) { if (el) { el.classList.remove('open'); document.body.classList.remove('no-scroll'); } }

    // ========== Gửi yêu cầu mượn ==========
    const modalYeuCau = document.getElementById('modal-yeu-cau');
    const formYC = document.getElementById('form-yeu-cau');
    const bangYC = document.getElementById('bang-yeu-cau');
    const tbodyYC = bangYC ? bangYC.querySelector('tbody') : null;

    const btnThemYC = document.getElementById('nut-them-yeu-cau');
    const btnDongYC = document.getElementById('dong-modal');
    const btnHuyYC = document.getElementById('huy-modal');

    btnThemYC?.addEventListener('click', () => {
        formYC?.reset();
        const hid = document.getElementById('id-yeu-cau'); if (hid) hid.value = '';
        openModal(modalYeuCau);
    });
    btnDongYC?.addEventListener('click', () => closeModal(modalYeuCau));
    btnHuyYC?.addEventListener('click', () => closeModal(modalYeuCau));

    formYC?.addEventListener('submit', (e) => {
        e.preventDefault();
        const thietBi = document.getElementById('thiet-bi-muon')?.value || '';
        const ngayMuonStr = document.getElementById('ngay-muon')?.value || '';
        const ngayTraStr = document.getElementById('ngay-tra')?.value || '';
        const mucDich = document.getElementById('muc-dich')?.value || '';

        if (!thietBi || !ngayMuonStr || !ngayTraStr || !mucDich) {
            alert('Vui lòng điền đầy đủ thông tin!'); return;
        }
        const ngayMuon = new Date(ngayMuonStr);
        const ngayTra = new Date(ngayTraStr);
        if (!(ngayMuon instanceof Date) || isNaN(+ngayMuon) || !(ngayTra instanceof Date) || isNaN(+ngayTra) || (ngayTra <= ngayMuon)) {
            alert('Ngày trả phải sau ngày mượn!'); return;
        }

        const id = document.getElementById('id-yeu-cau')?.value || ((tbodyYC?.rows.length || 0) + 1);
        const row = tbodyYC?.insertRow(-1);
        if (row) {
            row.innerHTML = `
        <td>${id}</td>
        <td>${thietBi}</td>
        <td>${ngayMuon.toISOString().split('T')[0]}</td>
        <td>${ngayTra.toISOString().split('T')[0]}</td>
        <td>${mucDich}</td>
        <td>Chờ duyệt</td>
        <td></td>
      `;
        }
        closeModal(modalYeuCau);
        alert('Yêu cầu mượn đã được gửi!');
    });

    // ========== Theo dõi yêu cầu (demo hủy) ==========
    document.querySelectorAll('.nut-huy').forEach(btn => {
        btn.addEventListener('click', () => {
            if (confirm('Bạn có chắc muốn hủy yêu cầu này?')) {
                btn.closest('tr')?.remove();
                alert('Yêu cầu đã được hủy!');
            }
        });
    });

    // ========== In lịch sử mượn ==========
    document.getElementById('nut-in-lich-su')?.addEventListener('click', () => window.print());

    // ========== Báo cáo hư hỏng ==========
    const modalBC = document.getElementById('modal-bao-cao');
    const formBC = document.getElementById('form-bao-cao');
    const btnOpenBC = document.getElementById('nut-them-bao-cao');
    const btnCloseBC = document.getElementById('dong-bao-cao');
    const btnHuyBC = document.getElementById('huy-bao-cao');
    const bangBC = document.getElementById('bang-bao-cao');
    const tbodyBC = bangBC ? bangBC.querySelector('tbody') : null;

    btnOpenBC?.addEventListener('click', () => { formBC?.reset(); openModal(modalBC); });
    btnCloseBC?.addEventListener('click', () => closeModal(modalBC));
    btnHuyBC?.addEventListener('click', () => closeModal(modalBC));

    formBC?.addEventListener('submit', (e) => {
        e.preventDefault();
        const thietBi = document.getElementById('thiet-bi-hu-hong')?.value || '';
        const moTa = document.getElementById('mo-ta-hu-hong')?.value || '';
        const ngayBC = new Date();
        if (!thietBi || !moTa) { alert('Vui lòng điền đầy đủ thông tin báo cáo!'); return; }
        const row = tbodyBC?.insertRow(-1);
        if (row) {
            row.innerHTML = `
        <td>${(tbodyBC.rows.length)}</td>
        <td>${thietBi}</td>
        <td>${moTa}</td>
        <td>${ngayBC.toISOString().split('T')[0]}</td>
        <td>Chờ xử lý</td>
      `;
        }
        closeModal(modalBC);
        alert('Báo cáo hư hỏng đã được gửi!');
    });

    // ========== Thông báo (chuông) — FIXED ==========
    const nutTB = document.getElementById('nut-thong-bao');
    const modalTB = document.getElementById('modal-thong-bao');
    const dongTB = document.getElementById('dong-thong-bao');
    const btnCloseTBX = document.getElementById('dong-thong-bao-x');
    const btnMarkRead = document.getElementById('danh-dau-doc');
    const badgeTB = document.getElementById('so-luong-thong-bao');

    function capNhatSoThongBao(n) {
        if (!badgeTB) return;
        if (n > 0) { badgeTB.textContent = n; badgeTB.style.display = 'inline-block'; }
        else { badgeTB.style.display = 'none'; }
    }
    function openTB() {
        openModal(modalTB);
        nutTB?.setAttribute('aria-expanded', 'true');
        const firstFocusable = modalTB.querySelector('#dong-thong-bao-x') || modalTB.querySelector('button');
        firstFocusable?.focus();
    }
    function closeTB() {
        closeModal(modalTB);
        nutTB?.setAttribute('aria-expanded', 'false');
        nutTB?.focus();
    }

    // Mở/đóng bằng chuông
    nutTB?.addEventListener('click', (e) => {
        e.stopPropagation();
        modalTB?.classList.contains('open') ? closeTB() : openTB();
    });

    // Nút đóng & đánh dấu đã đọc
    dongTB?.addEventListener('click', closeTB);
    btnCloseTBX?.addEventListener('click', closeTB);
    btnMarkRead?.addEventListener('click', () => { capNhatSoThongBao(0); closeTB(); });

    // Click ngoài nội dung để đóng
    modalTB?.addEventListener('click', (e) => {
        if (e.target === modalTB) closeTB();
    });

    // ESC để đóng
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalTB?.classList.contains('open')) closeTB();
    });

    // Demo: nếu badge trống, set = 1 để thấy rõ
    if (badgeTB && (badgeTB.textContent.trim() === '' || badgeTB.textContent.trim() === '0')) capNhatSoThongBao(1);
})();
