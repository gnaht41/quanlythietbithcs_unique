// File: views/js/shared-ui.js
// Chứa code JS dùng chung cho layout (ví dụ: sidebar navigation)

(function () {
    // Helpers để chọn element (có thể giữ lại nếu file JS riêng không có)
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

    // Điều hướng trái → hiện đúng section
    $$('.thanh-ben a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault(); // Ngăn link chuyển trang

            // Xử lý class 'active'
            $$('.thanh-ben a').forEach(a => a.classList.remove('active'));
            link.classList.add('active');

            // Lấy ID section từ data-page
            const pageId = link.dataset.page;

            // Ẩn tất cả sections
            $$('.trang-an').forEach(p => p.style.display = 'none');

            // Hiển thị section tương ứng
            const pageEl = $('#' + pageId);
            if (pageEl) {
                pageEl.style.display = 'block';
            }
        });
    });

    // --- Bạn có thể thêm các code JS dùng chung khác vào đây nếu cần ---
    // Ví dụ: Code xử lý nút đăng xuất chung (sửa lại đường dẫn cho đúng)
    $('#nut-dang-xuat-link')?.addEventListener('click', (e) => {
        e.preventDefault(); // Ngăn link chuyển trang ngay lập tức
        const ok = confirm('Bạn có chắc muốn đăng xuất không?');
        if (ok) {
            // Chuyển hướng đến action logout của PHP
            window.location.href = '../index.php?action=logout'; // Đường dẫn này đúng khi đang ở trong thư mục /views/
        }
    });

})(); // Kết thúc IIFE