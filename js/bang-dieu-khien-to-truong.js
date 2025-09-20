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
    const id = document.getElementById('id-ke-hoach').value || bang.rows.length + 1;
    const thietBi = document.getElementById('thiet-bi').value;
    const soLuong = document.getElementById('so-luong').value;
    const lyDo = document.getElementById('ly-do').value;

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

document.querySelectorAll('.nut-sua').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        document.getElementById('id-ke-hoach').value = row.cells[0].textContent;
        document.getElementById('thiet-bi').value = row.cells[1].textContent;
        document.getElementById('so-luong').value = row.cells[2].textContent;
        document.getElementById('ly-do').value = row.cells[3].textContent;
        document.getElementById('tieu-de-modal').textContent = 'Sửa kế hoạch mua sắm';
        modal.style.display = 'flex';
    });
});

document.querySelectorAll('.nut-xoa').forEach(btn => {
    btn.addEventListener('click', () => {
        if (confirm('Bạn có chắc muốn xóa kế hoạch này?')) {
            btn.parentElement.parentElement.remove();
            alert('Xóa kế hoạch thành công!');
        }
    });
});

document.querySelectorAll('.nut-gui').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        row.cells[4].textContent = 'Đã gửi';
        btn.disabled = true;
        alert('Kế hoạch đã được gửi tới Hiệu trưởng!');
    });
});

function ganSuKienNut(hang) {
    hang.querySelector('.nut-sua').addEventListener('click', () => {
        const row = hang;
        document.getElementById('id-ke-hoach').value = row.cells[0].textContent;
        document.getElementById('thiet-bi').value = row.cells[1].textContent;
        document.getElementById('so-luong').value = row.cells[2].textContent;
        document.getElementById('ly-do').value = row.cells[3].textContent;
        document.getElementById('tieu-de-modal').textContent = 'Sửa kế hoạch mua sắm';
        modal.style.display = 'flex';
    });
    hang.querySelector('.nut-xoa').addEventListener('click', () => {
        if (confirm('Bạn có chắc muốn xóa kế hoạch này?')) {
            hang.remove();
            alert('Xóa kế hoạch thành công!');
        }
    });
    hang.querySelector('.nut-gui').addEventListener('click', () => {
        const row = hang;
        row.cells[4].textContent = 'Đã gửi';
        row.querySelector('.nut-gui').disabled = true;
        alert('Kế hoạch đã được gửi tới Hiệu trưởng!');
    });
}
document.querySelectorAll('#bang-ke-hoach tr').forEach(ganSuKienNut);

// --- Theo dõi tình hình thiết bị ---
document.getElementById('nut-loc').addEventListener('click', () => {
    const monHoc = document.getElementById('loc-mon-hoc').value;
    if (monHoc) {
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