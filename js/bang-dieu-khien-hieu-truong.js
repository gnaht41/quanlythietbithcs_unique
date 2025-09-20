// UI-only scripts. Không xử lý dữ liệu.
// Giữ xác nhận Đăng xuất & điều hướng về trang đăng nhập.
(function () {
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

    // Điều hướng trái → hiện section tương ứng
    $$('.thanh-ben a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            $$('.thanh-ben a').forEach(a => a.classList.remove('active'));
            link.classList.add('active');
            const pageId = link.dataset.page;
            $$('.trang-an').forEach(p => p.style.display = 'none');
            $('#' + pageId).style.display = 'block';
        });
    });

    // Modal helpers
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
    modalTB?.querySelector('.btn-primary')?.addEventListener('click', () => setBadge(0));

    // Duyệt kế hoạch: mở modals UI
    const modalXemKH = $('#modal-xem-ke-hoach');
    const modalDuyetKH = $('#modal-duyet-ke-hoach');
    bindCloseInside(modalXemKH);
    bindCloseInside(modalDuyetKH);

    $('#bang-ke-hoach')?.addEventListener('click', (e) => {
        const btn = e.target.closest('button'); if (!btn) return;
        if (btn.classList.contains('nut-xem')) openModal(modalXemKH);
        if (btn.classList.contains('nut-sua')) openModal(modalDuyetKH);
        if (btn.classList.contains('nut-huy')) alert('(UI) Từ chối — backend sẽ xử lý sau.');
    });

    // Duyệt thanh lý: mở modals UI
    const modalXemTL = $('#modal-xem-thanh-ly');
    const modalDuyetTL = $('#modal-duyet-thanh-ly');
    bindCloseInside(modalXemTL);
    bindCloseInside(modalDuyetTL);

    $('#bang-thanh-ly')?.addEventListener('click', (e) => {
        const btn = e.target.closest('button'); if (!btn) return;
        if (btn.classList.contains('nut-xem')) openModal(modalXemTL);
        if (btn.classList.contains('nut-sua')) openModal(modalDuyetTL);
        if (btn.classList.contains('nut-huy')) alert('(UI) Từ chối — backend sẽ xử lý sau.');
    });

    // Đăng xuất (theo yêu cầu bạn: có xác nhận & điều hướng)
    $('#nut-dang-xuat')?.addEventListener('click', () => {
        const ok = confirm('Bạn có chắc muốn đăng xuất không?');
        if (ok) {
            window.location.href = '../html/dang-nhap.html';
        }
    });

})();
