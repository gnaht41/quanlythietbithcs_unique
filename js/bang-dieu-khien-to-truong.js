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

// --- Lập kế hoạch mua sắm ---
const modal = document.getElementById('modal-ke-hoach');
const form = document.getElementById('form-ke-hoach');
const bang = document.getElementById('bang-ke-hoach');

document.getElementById('nut-them-ke-hoach').addEventListener('click', () => {
    form.reset();
    document.getElementById('id-ke-hoach').value = '';
    document.getElementById('tieu-de-modal').textContent = 'Thêm kế hoạch mua sắm';
    modal.style.display = 'flex';
});

document.getElementById('dong-modal').addEventListener('click', () => modal.style.display = 'none');
document.getElementById('huy-modal').addEventListener('click', () => modal.style.display = 'none');

form.addEventListener('submit', (e) => {
    e.preventDefault();
    const thietBi = document.getElementById('thiet-bi').value;
    const soLuong = document.getElementById('so-luong').value;
    const lyDo = document.getElementById('ly-do').value;
    if (!thietBi || !soLuong || !lyDo) {
        alert('Vui lòng điền đầy đủ thông tin!');
        return;
    }
    const id = document.getElementById('id-ke-hoach').value || bang.rows.length + 1;

    if (!document.getElementById('id-ke-hoach').value) {
        const newRow = bang.insertRow();
        newRow.innerHTML = `
            <td>${id}</td>
            <td>${thietBi}</td>
            <td>${soLuong}</td>
            <td>${lyDo}</td>
            <td>Chờ duyệt</td>
            <td>
                <button class="nut-sua">Sửa</button>
                <button class="nut-xoa">Xóa</button>
                <button class="nut-gui">Gửi</button>
            </td>
        `;
        ganSuKienNut(newRow);
    } else {
        const row = bang.rows[parseInt(id) - 1];
        row.cells[1].textContent = thietBi;
        row.cells[2].textContent = soLuong;
        row.cells[3].textContent = lyDo;
    }
    modal.style.display = 'none';
    alert('Lưu kế hoạch thành công!');
});

document.getElementById('nut-in-ke-hoach').addEventListener('click', () => {
    window.print();
    alert('In kế hoạch mua sắm (sử dụng window.print để in trang)');
});

// --- Theo dõi tình hình thiết bị ---
document.getElementById('nut-loc').addEventListener('click', () => {
    const monHoc = document.getElementById('loc-mon-hoc').value;
    if (monHoc) {
        // Demo lọc với data tĩnh
        const bangThietBi = document.getElementById('bang-thiet-bi');
        bangThietBi.innerHTML = ''; // Xóa bảng cũ
        const dataDemo = {
            'toan': [
                { thietBi: 'Máy tính cầm tay', tot: 20, hong: 1, chenhLech: '-1', ghiChu: 'Cần bảo trì' }
            ],
            'ly': [
                { thietBi: 'Bộ thí nghiệm Lý', tot: 15, hong: 2, chenhLech: '-2', ghiChu: 'Hỏng nặng' }
            ],
            'hoa': [
                { thietBi: 'Hóa chất thí nghiệm', tot: 10, hong: 0, chenhLech: '0', ghiChu: 'Tốt' }
            ]
        };
        const data = dataDemo[monHoc] || [];
        data.forEach(item => {
            const row = bangThietBi.insertRow();
            row.innerHTML = `
                <td>${item.thietBi}</td>
                <td>${item.tot}</td>
                <td>${item.hong}</td>
                <td>${item.chenhLech}</td>
                <td>${item.ghiChu}</td>
            `;
        });
        alert(`Lọc thiết bị cho môn học: ${monHoc}`);
    } else {
        alert('Vui lòng chọn môn học!');
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
    if (e.target === modal) modal.style.display = 'none';
    if (e.target === document.getElementById('modal-thong-bao')) document.getElementById('modal-thong-bao').style.display = 'none';
});