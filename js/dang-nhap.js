(function () {
    const $ = s => document.querySelector(s);

    const users = {
        admin: '123', hieutruong: '123', totruong: '123', giaovien: '123', nhanvien: '123'
    };

    const form = $('#login-form');
    const userEl = $('#username');
    const passEl = $('#password');
    const errEl = $('#error');
    const toggleBtn = $('#toggle-pass');
    const iconEye = $('#icon-eye');
    const capsHint = $('#caps-hint');

    // Toggle password hiển thị/ẩn
    let showing = false;
    toggleBtn.addEventListener('click', () => {
        showing = !showing;
        passEl.type = showing ? 'text' : 'password';
        iconEye.outerHTML = showing
            ? `<svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.8 21.8 0 0 1 5.17-5.88M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.8 21.8 0 0 1-4.12 5.41M1 1l22 22"/><path d="M14.12 14.12A3 3 0 0 1 9.88 9.88"/></svg>`
            : `<svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>`;
    });

    // Cảnh báo Caps Lock
    function capsHandler(e) { capsHint.hidden = !(e.getModifierState && e.getModifierState('CapsLock')); }
    passEl.addEventListener('keydown', capsHandler);
    passEl.addEventListener('keyup', capsHandler);

    function showError(msg) {
        errEl.textContent = msg; errEl.hidden = false;
        form.style.animation = 'shake .25s'; setTimeout(() => form.style.animation = '', 300);
    }

    // CSS animation shake
    const style = document.createElement('style');
    style.textContent = `
    @keyframes shake {
      0%{transform:translateX(0)}
      25%{transform:translateX(-4px)}
      50%{transform:translateX(4px)}
      75%{transform:translateX(-2px)}
      100%{transform:translateX(0)}
    }`;
    document.head.appendChild(style);

    // Submit
    form.addEventListener('submit', e => {
        e.preventDefault(); errEl.hidden = true;
        const u = userEl.value.trim(), p = passEl.value.trim();
        if (!u || !p) { showError('Vui lòng nhập đầy đủ.'); return; }
        if (users[u] && users[u] === p) {
            let target = '';
            switch (u) {
                case 'admin': target = 'bang-dieu-khien-quan-tri-vien.html'; break;
                case 'hieutruong': target = 'bang-dieu-khien-hieu-truong.html'; break;
                case 'totruong': target = 'bang-dieu-khien-to-truong.html'; break;
                case 'giaovien': target = 'bang-dieu-khien-giao-vien.html'; break;
                case 'nhanvien': target = 'bang-dieu-khien-nhan-vien-thiet-bi.html'; break;
                default: target = 'bang-dieu-khien-giao-vien.html';
            }
            window.location.href = target;
        } else {
            showError('Sai tài khoản hoặc mật khẩu!');
            passEl.focus(); passEl.select();
        }
    });

    // Liên hệ Admin
    $('#contact-admin')?.addEventListener('click', e => {
        e.preventDefault();
        alert('Vui lòng liên hệ quản trị viên để đặt lại mật khẩu.');
    });
})();
