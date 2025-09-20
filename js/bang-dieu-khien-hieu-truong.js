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
            $$('main > section.trang-an').forEach(p => p.style.display = 'none');
            const pageEl = document.getElementById(pageId);
            if (pageEl) pageEl.style.display = 'block';
        });
    });

    // ===== Notifications (bell) =====
    const nutTB = $('#nut-thong-bao');
    const modalTB = $('#modal-thong-bao');
    const dongTB = $('#dong-thong-bao');
    const dongTBX = $('#dong-thong-bao-x');
    const markRead = $('#danh-dau-doc');
    const badgeTB = $('#so-luong-thong-bao');

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

    // ===== Duyệt kế hoạch mua sắm & thanh lý =====
    function attachApproveRejectHandlers(tableSelector) {
        const tbody = $(tableSelector);
        if (!tbody) return;

        tbody.addEventListener('click', (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;
            const row = btn.closest('tr');
            if (btn.classList.contains('nut-duyet')) {
                row.cells[6].textContent = 'Đã phê duyệt'; // cột Trạng thái
                row.cells[7].textContent = row.cells[7].textContent || ''; // cột Ghi chú giữ nguyên
                alert('Phê duyệt thành công!');
                return;
            }
            if (btn.classList.contains('nut-tu-choi')) {
                // mở modal nhập lý do
                openModal($('#modal-tu-choi'));
                const confirmBtn = $('#xac-nhan-tu-choi');
                const cancelBtn = $('#huy-tu-choi');
                const closeX = $('#dong-tu-choi-x');
                const textarea = $('#ghi-chu-tu-choi');

                const cleanup = () => {
                    confirmBtn.onclick = null;
                    cancelBtn.onclick = null;
                    closeX.onclick = null;
                    textarea.value = '';
                };

                confirmBtn.onclick = () => {
                    row.cells[6].textContent = 'Từ chối';
                    row.cells[7].textContent = textarea.value || 'Không có ghi chú';
                    closeModal($('#modal-tu-choi'));
                    cleanup();
                    alert('Từ chối thành công!');
                };
                cancelBtn.onclick = () => { closeModal($('#modal-tu-choi')); cleanup(); };
                closeX.onclick = () => { closeModal($('#modal-tu-choi')); cleanup(); };

                // Đóng bằng Esc khi modal mở
                const onEsc = (ev) => { if (ev.key === 'Escape') { closeModal($('#modal-tu-choi')); cleanup(); window.removeEventListener('keydown', onEsc); } };
                window.addEventListener('keydown', onEsc);
            }
        });
    }
    attachApproveRejectHandlers('#bang-mua-sam');
    attachApproveRejectHandlers('#bang-thanh-ly');

    // ===== Xuất báo cáo =====
    $('#nut-xuat-pdf')?.addEventListener('click', () => {
        window.print(); // demo in PDF
    });
    $('#nut-xuat-excel')?.addEventListener('click', () => {
        alert('Tải báo cáo Excel (demo).');
    });
})();
