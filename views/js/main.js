// File: views/js/main.js (Phiên bản chỉ xử lý PHP)
(function () {
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

    // --- KHÔNG CÓ CODE XỬ LÝ CLICK SIDEBAR ĐỂ ẨN/HIỆN SECTION ---

    // --- CODE ĐĂNG XUẤT ---
    $('#nut-dang-xuat-link')?.addEventListener('click', (e) => {
        e.preventDefault();
        const ok = confirm('Bạn có chắc muốn đăng xuất không?');
        if (ok) {
            window.location.href = '../index.php?action=logout';
        }
    });

    // --- CÁC CODE JS CHUNG KHÁC ---

})();