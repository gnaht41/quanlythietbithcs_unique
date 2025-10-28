// views/js/dang-nhap.js (Chỉ giữ lại phần UI)
(function () {
    const $ = s => document.querySelector(s);

    const form = $('#login-form'); // Vẫn cần tham chiếu đến form để rung lắc
    const passEl = $('#password');
    const errEl = $('#error'); // Cần để hiển thị lỗi từ PHP
    const toggleBtn = $('#toggle-pass');
    const iconEye = $('#icon-eye');
    const capsHint = $('#caps-hint');

    // Toggle password hiển thị/ẩn (Giữ nguyên)
    let showing = false;
    toggleBtn.addEventListener('click', () => {
        // ... (code ẩn/hiện mật khẩu như cũ) ...
        showing = !showing;
        passEl.type = showing ? 'text' : 'password';
        iconEye.outerHTML = showing
            ? `<svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.8 21.8 0 0 1 5.17-5.88M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.8 21.8 0 0 1-4.12 5.41M1 1l22 22"/><path d="M14.12 14.12A3 3 0 0 1 9.88 9.88"/></svg>`
            : `<svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>`;
    });

    // Cảnh báo Caps Lock (Giữ nguyên)
    function capsHandler(e) { capsHint.hidden = !(e.getModifierState && e.getModifierState('CapsLock')); }
    passEl.addEventListener('keydown', capsHandler);
    passEl.addEventListener('keyup', capsHandler);

    // Hàm hiển thị lỗi (để PHP gọi) - Giữ lại và đảm bảo nó được gọi đúng
    window.showLoginError = function (msg) {
        if (errEl) {
            errEl.textContent = msg;
            errEl.hidden = false;
        }
        if (form) {
            form.style.animation = 'shake .25s';
            setTimeout(() => form.style.animation = '', 300);
        }
        // Tùy chọn: focus lại vào password
        // if(passEl) {
        //   passEl.focus();
        //    passEl.select();
        // }
    }

    // CSS animation shake (Giữ nguyên)
    const style = document.createElement('style');
    style.textContent = `
    @keyframes shake {
      0%{transform:translateX(0)} 25%{transform:translateX(-4px)} 50%{transform:translateX(4px)} 75%{transform:translateX(-2px)} 100%{transform:translateX(0)}
    }`;
    document.head.appendChild(style);

    // --- BỎ ĐI LOGIC SUBMIT CLIENT-SIDE ---
    // form.addEventListener('submit', e => { ... }); // Xóa hoặc comment đoạn này

    // Liên hệ Admin (Giữ nguyên)
    $('#contact-admin')?.addEventListener('click', e => {
        e.preventDefault();
        alert('Vui lòng liên hệ quản trị viên để đặt lại mật khẩu.');
    });
})();