// File: views/js/main.js
(function () {
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

    // --- BỎ CODE XỬ LÝ CLICK SIDEBAR ĐỂ ẨN/HIỆN SECTION ---
    // Việc chuyển trang giờ do href="?tab=..." đảm nhiệm

    // --- CODE ĐĂNG XUẤT ---
    $('#nut-dang-xuat-link')?.addEventListener('click', (e) => {
        e.preventDefault();
        const ok = confirm('Bạn có chắc muốn đăng xuất không?');
        if (ok) {
            window.location.href = '../index.php?action=logout';
        }
    });

    // --- CÁC CODE JS CHUNG KHÁC (NẾU CÓ) ---
    // Ví dụ: Mở/đóng modal thông báo chung (nếu có)

})();