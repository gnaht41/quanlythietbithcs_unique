document.getElementById('login-form').addEventListener('submit', function (event) {
    event.preventDefault(); // Ngăn submit mặc định
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // Giả lập danh sách người dùng (thay bằng API nếu có backend)
    const users = {
        'admin': '123',         // Admin
        'hieutruong': '123',      // Hiệu trưởng
        'totruong': '123',          // Tổ trưởng Chuyên môn
        'giaovien': '123',       // Giáo viên
        'nhanvien': '123'        // Nhân viên Thiết bị
    };

    if (users[username] && users[username] === password) {
        alert('Đăng nhập thành công!');
        switch (username) {
            case 'admin':
                window.location.href = 'bang-dieu-khien-quan-tri-vien.html';
                break;
            case 'hieutruong':
                window.location.href = 'bang-dieu-khien-hieu-truong.html';
                break;
            case 'totruong':
                window.location.href = 'bang-dieu-khien-to-truong.html';
                break;
            case 'giaovien':
                window.location.href = 'bang-dieu-khien-giao-vien.html';
                break;
            case 'nhanvien':
                window.location.href = 'bang-dieu-khien-nhan-vien-thiet-bi.html';
                break;
        }
    } else {
        alert('Tài khoản hoặc mật khẩu không đúng!');
    }
});