// JS chỉ phục vụ giao diện (UI). KHÔNG xử lý dữ liệu.
// Giữ code tối giản để nối backend sau.
(function () {
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

    // Điều hướng trái → hiện đúng section
    $$('.thanh-ben a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            $$('.thanh-ben a').forEach(a => a.classList.remove('active'));
            link.classList.add('active');
            const pageId = link.dataset.page;
            $$('.trang-an').forEach(p => p.style.display = 'none');
            const pageEl = $('#' + pageId);
            if (pageEl) pageEl.style.display = 'block';
        });
    });

    // Mở/đóng modal tiện dụng
    function openModal(el) { if (!el) return; el.classList.add('open'); document.body.classList.add('no-scroll'); }
    function closeModal(el) { if (!el) return; el.classList.remove('open'); document.body.classList.remove('no-scroll'); }
    function bindCloseInside(modal) {
        modal?.querySelectorAll('[data-close]')?.forEach(btn => {
            btn.addEventListener('click', () => closeModal(modal));
        });
        modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(modal); });
    }

    // Thông báo
    const nutTB = $('#nut-thong-bao');
    const modalTB = $('#modal-thong-bao');
    const badge = $('#so-luong-thong-bao');
    const setBadge = (n) => { if (!badge) return; badge.style.display = n > 0 ? 'inline-block' : 'none'; badge.textContent = n; };
    const openTB = () => { openModal(modalTB); nutTB?.setAttribute('aria-expanded', 'true'); };
    const closeTB = () => { closeModal(modalTB); nutTB?.setAttribute('aria-expanded', 'false'); };

    nutTB?.addEventListener('click', () => modalTB?.classList.contains('open') ? closeTB() : openTB());
    bindCloseInside(modalTB);
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modalTB?.classList.contains('open')) closeTB(); });
    // Demo: nhấn "Đã hiểu" sẽ ẩn badge
    modalTB?.querySelector('.btn-primary')?.addEventListener('click', () => setBadge(0));

    // Phiếu mượn: mở các modal UI
    const modalPhieu = $('#modal-phieu');
    const modalXem = $('#modal-xem');
    const picker = $('#picker-thiet-bi');

    $('#nut-them-phieu')?.addEventListener('click', () => openModal(modalPhieu));
    $('#bang-phieu')?.addEventListener('click', (e) => {
        const btn = e.target.closest('button'); if (!btn) return;
        if (btn.classList.contains('nut-sua')) openModal(modalPhieu);
        if (btn.classList.contains('nut-xem')) openModal(modalXem);
        if (btn.classList.contains('nut-in')) openModal(modalXem); // chỉ UI
        if (btn.classList.contains('nut-huy')) alert('(UI) Nhấn Hủy — backend sẽ xử lý sau.');
    });
    $('#nut-them-item')?.addEventListener('click', () => openModal(picker));

    [modalPhieu, modalXem, picker].forEach(bindCloseInside);

    // Báo cáo hư/hỏng: mở modal UI
    const modalBC = $('#modal-bao-cao');
    const modalAnh = $('#modal-anh');

    $('#nut-them-bc')?.addEventListener('click', () => openModal(modalBC));
    $('#bang-bao-cao')?.addEventListener('click', (e) => {
        const btn = e.target.closest('button'); if (!btn) return;
        if (btn.classList.contains('nut-sua')) openModal(modalBC);
        if (btn.classList.contains('nut-huy')) alert('(UI) Nhấn Xóa — backend sẽ xử lý sau.');
    });

    [modalBC, modalAnh].forEach(bindCloseInside);

    // Đăng xuất (UI)
    $('#nut-dang-xuat')?.addEventListener('click', () => {
        alert('(UI) Đăng xuất — chuyển màn hình đăng nhập sau khi nối backend.');
    });

})();
