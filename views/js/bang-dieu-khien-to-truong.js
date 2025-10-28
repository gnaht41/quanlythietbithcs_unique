// UI-only. Không xử lý dữ liệu; để backend nối sau.
// Giữ: Đăng xuất có xác nhận + điều hướng; bell thông báo; mở/đóng modal.
(function () {
    // ===== Helpers =====
    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
    const openModal = (el) => { if (el) { el.classList.add('open'); document.body.classList.add('no-scroll'); } };
    const closeModal = (el) => { if (el) { el.classList.remove('open'); document.body.classList.remove('no-scroll'); } };

    // ===== Logout =====
    $('#nut-dang-xuat')?.addEventListener('click', () => {
        if (confirm('Bạn có chắc muốn đăng xuất?')) {
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
    const badge = $('#so-luong-thong-bao');

    const setBadge = (n) => { if (!badge) return; badge.style.display = n > 0 ? 'inline-block' : 'none'; badge.textContent = n; };
    const openTB = () => { openModal(modalTB); nutTB?.setAttribute('aria-expanded', 'true'); };
    const closeTB = () => { closeModal(modalTB); nutTB?.setAttribute('aria-expanded', 'false'); };

    nutTB?.addEventListener('click', (e) => { e.stopPropagation(); modalTB?.classList.contains('open') ? closeTB() : openTB(); });
    modalTB?.addEventListener('click', (e) => { if (e.target === modalTB) closeTB(); });
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modalTB?.classList.contains('open')) closeTB(); });
    modalTB?.querySelector('#dong-thong-bao')?.addEventListener('click', closeTB);
    modalTB?.querySelector('#dong-thong-bao-x')?.addEventListener('click', closeTB);
    modalTB?.querySelector('#danh-dau-doc')?.addEventListener('click', () => { setBadge(0); closeTB(); });

    // ===== Kế hoạch mua sắm (UI only) =====
    const modalKH = $('#modal-ke-hoach');
    const formKH = $('#form-ke-hoach');

    $('#nut-them-ke-hoach')?.addEventListener('click', () => {
        $('#tieu-de-modal').textContent = 'Thêm kế hoạch mua sắm';
        formKH?.reset();
        $('#id-ke-hoach').value = '';
        openModal(modalKH);
    });

    // Nút đóng/hủy trong modal
    $('#dong-modal')?.addEventListener('click', () => closeModal(modalKH));
    $('#huy-modal')?.addEventListener('click', () => closeModal(modalKH));

    // Nút "Lưu (UI)" chỉ đóng modal (không thao tác dữ liệu)
    formKH?.querySelector('[data-close]')?.addEventListener('click', () => closeModal(modalKH));

    // Hành động trong bảng (UI only: chỉ mở modal / alert)
    $('#bang-ke-hoach')?.addEventListener('click', (e) => {
        const btn = e.target.closest('button'); if (!btn) return;
        if (btn.classList.contains('nut-sua')) {
            $('#tieu-de-modal').textContent = 'Sửa kế hoạch mua sắm';
            openModal(modalKH);
        }
        if (btn.classList.contains('nut-xoa')) {
            alert('(UI) Xóa kế hoạch — backend sẽ xử lý sau.');
        }
        if (btn.classList.contains('nut-gui')) {
            alert('(UI) Gửi kế hoạch — backend sẽ xử lý gửi duyệt.');
        }
    });

    // ===== Theo dõi thiết bị: nút Lọc (UI only) =====
    $('#nut-loc')?.addEventListener('click', () => {
        const v = $('#loc-mon-hoc')?.value || '';
        if (!v) { alert('Vui lòng chọn môn học/danh mục!'); return; }
        alert('(UI) Thực hiện lọc theo: ' + v);
    });
})();
