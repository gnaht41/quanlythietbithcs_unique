// --- Đăng xuất ---
document.getElementById('nut-dang-xuat').addEventListener('click', () => {
    if (confirm('Bạn có chắc muốn đăng xuất?')) {
        alert('Đăng xuất thành công!');
        window.location.href = '../html/dang-nhap.html';
    }
});

// --- Điều hướng ---
document.querySelectorAll('.thanh-ben a').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const pageId = link.dataset.page;
        document.querySelectorAll('.thanh-ben a').forEach(a => a.classList.remove('active'));
        link.classList.add('active');
        document.querySelectorAll('.trang-an').forEach(page => page.style.display = 'none');
        document.getElementById(pageId).style.display = 'block';
    });
});
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.thanh-ben a[data-page="tong-quan"]').classList.add('active');
});

// --- Quản lý người dùng ---
const modalThem = document.getElementById('modal-them-nguoi-dung');
const formThem = document.getElementById('form-them-nguoi-dung');
const modalSua = document.getElementById('modal-sua-nguoi-dung');
const formSua = document.getElementById('form-sua-nguoi-dung');

document.getElementById('nut-them-nguoi-dung').addEventListener('click', () => {
    formThem.reset();
    modalThem.style.display = 'flex';
});

document.getElementById('dong-modal').addEventListener('click', () => modalThem.style.display = 'none');
document.getElementById('huy-modal').addEventListener('click', () => modalThem.style.display = 'none');

formThem.addEventListener('submit', (e) => {
    e.preventDefault();
    const bang = document.getElementById('bang-nguoi-dung');
    const idMoi = bang.rows.length + 1;
    const tenDangNhap = document.getElementById('ten-dang-nhap').value;
    const matKhau = document.getElementById('mat-khau').value;
    const hoTen = document.getElementById('ho-ten').value;
    const vaiTro = document.getElementById('vai-tro').value;
    const trangThai = document.getElementById('trang-thai').value;

    const newRow = bang.insertRow();
    newRow.innerHTML = `
        <td>${idMoi}</td>
        <td>${tenDangNhap}</td>
        <td>${hoTen}</td>
        <td>${vaiTro.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
        <td>${trangThai === 'hoat-dong' ? 'Hoạt động' : 'Vô hiệu hóa'}</td>
        <td>
            <button class="nut-sua">Sửa</button>
            <button class="nut-xoa">Xóa</button>
            <button class="nut-vo-hieu-hoa">Vô hiệu hóa</button>
        </td>
    `;
    ganSuKienNut(newRow);
    modalThem.style.display = 'none';
    alert('Thêm người dùng thành công!');
});

document.querySelectorAll('.nut-sua').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        const id = row.cells[0].textContent;
        const tenDangNhap = row.cells[1].textContent;
        const hoTen = row.cells[2].textContent;
        const vaiTro = row.cells[3].textContent.toLowerCase().replace(/\s/g, '-');
        const trangThai = row.cells[4].textContent === 'Hoạt động' ? 'hoat-dong' : 'vo-hieu-hoa';

        document.getElementById('id-sua').value = id;
        document.getElementById('ten-dang-nhap-sua').value = tenDangNhap;
        document.getElementById('mat-khau-sua').value = ''; // Reset mật khẩu
        document.getElementById('ho-ten-sua').value = hoTen;
        document.getElementById('vai-tro-sua').value = vaiTro;
        document.getElementById('trang-thai-sua').value = trangThai;
        modalSua.style.display = 'flex';
    });
});

document.getElementById('dong-sua-modal').addEventListener('click', () => modalSua.style.display = 'none');
document.getElementById('huy-sua-modal').addEventListener('click', () => modalSua.style.display = 'none');

formSua.addEventListener('submit', (e) => {
    e.preventDefault();
    const id = document.getElementById('id-sua').value;
    const tenDangNhap = document.getElementById('ten-dang-nhap-sua').value;
    const hoTen = document.getElementById('ho-ten-sua').value;
    const vaiTro = document.getElementById('vai-tro-sua').value;
    const trangThai = document.getElementById('trang-thai-sua').value;

    const bang = document.getElementById('bang-nguoi-dung');
    const row = bang.rows[parseInt(id) - 1];
    row.cells[1].textContent = tenDangNhap;
    row.cells[2].textContent = hoTen;
    row.cells[3].textContent = vaiTro.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase());
    row.cells[4].textContent = trangThai === 'hoat-dong' ? 'Hoạt động' : 'Vô hiệu hóa';

    modalSua.style.display = 'none';
    alert('Cập nhật người dùng thành công!');
});

document.querySelectorAll('.nut-xoa').forEach(btn => {
    btn.addEventListener('click', () => {
        if (confirm('Bạn có chắc muốn xóa người dùng này?')) {
            btn.parentElement.parentElement.remove();
            alert('Xóa người dùng thành công!');
        }
    });
});

document.querySelectorAll('.nut-vo-hieu-hoa').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        const cell = row.cells[4];
        cell.textContent = cell.textContent === 'Hoạt động' ? 'Vô hiệu hóa' : 'Hoạt động';
        alert(cell.textContent === 'Hoạt động' ? 'Kích hoạt lại thành công!' : 'Vô hiệu hóa thành công!');
    });
});

function ganSuKienNut(hang) {
    hang.querySelector('.nut-sua').addEventListener('click', () => {
        const row = hang;
        const id = row.cells[0].textContent;
        const tenDangNhap = row.cells[1].textContent;
        const hoTen = row.cells[2].textContent;
        const vaiTro = row.cells[3].textContent.toLowerCase().replace(/\s/g, '-');
        const trangThai = row.cells[4].textContent === 'Hoạt động' ? 'hoat-dong' : 'vo-hieu-hoa';

        document.getElementById('id-sua').value = id;
        document.getElementById('ten-dang-nhap-sua').value = tenDangNhap;
        document.getElementById('mat-khau-sua').value = ''; // Reset mật khẩu
        document.getElementById('ho-ten-sua').value = hoTen;
        document.getElementById('vai-tro-sua').value = vaiTro;
        document.getElementById('trang-thai-sua').value = trangThai;
        modalSua.style.display = 'flex';
    });
    hang.querySelector('.nut-xoa').addEventListener('click', () => {
        if (confirm('Bạn có chắc muốn xóa người dùng này?')) {
            hang.remove();
            alert('Xóa người dùng thành công!');
        }
    });
    hang.querySelector('.nut-vo-hieu-hoa').addEventListener('click', () => {
        const cell = hang.cells[4];
        cell.textContent = cell.textContent === 'Hoạt động' ? 'Vô hiệu hóa' : 'Hoạt động';
        alert(cell.textContent === 'Hoạt động' ? 'Kích hoạt lại thành công!' : 'Vô hiệu hóa thành công!');
    });
}
document.querySelectorAll('#bang-nguoi-dung tr').forEach(ganSuKienNut);

// --- Phân quyền truy cập ---
document.querySelectorAll('.nut-cap-nhat-quyen').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        const tenDangNhap = row.cells[0].textContent;
        const quyen = [];
        row.querySelectorAll('.quyen:checked').forEach(cb => quyen.push(cb.dataset.quyen));
        alert(`Cập nhật quyền cho ${tenDangNhap}: ${quyen.join(', ')}`);
    });
});

// --- Giám sát hệ thống ---
document.getElementById('nut-tim-kiem').addEventListener('click', () => {
    const ngay = document.getElementById('loc-ngay').value;
    if (ngay) {
        alert(`Tìm kiếm nhật ký ngày ${ngay}`);
    } else {
        alert('Vui lòng chọn ngày!');
    }
});

// --- Thông báo ---
document.getElementById('nut-thong-bao').addEventListener('click', () => {
    const modalTB = document.getElementById('modal-thong-bao');
    modalTB.style.display = modalTB.style.display === 'block' ? 'none' : 'block';
});

document.getElementById('dong-thong-bao').addEventListener('click', () => {
    document.getElementById('modal-thong-bao').style.display = 'none';
});

// --- Đóng modal khi click ngoài ---
window.addEventListener('click', (e) => {
    if (e.target === modalThem) modalThem.style.display = 'none';
    if (e.target === modalSua) modalSua.style.display = 'none';
    if (e.target === document.getElementById('modal-thong-bao')) document.getElementById('modal-thong-bao').style.display = 'none';
});