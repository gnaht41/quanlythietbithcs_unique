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

// --- Quản lý danh mục thiết bị ---
const modalThietBi = document.getElementById('modal-thiet-bi');
const formThietBi = document.getElementById('form-thiet-bi');
const bangDanhMuc = document.getElementById('bang-danh-muc');

document.getElementById('nut-them-thiet-bi').addEventListener('click', () => {
    formThietBi.reset();
    document.getElementById('id-thiet-bi').value = '';
    document.getElementById('tieu-de-modal').textContent = 'Thêm thiết bị';
    modalThietBi.style.display = 'flex';
});

document.getElementById('dong-modal').addEventListener('click', () => modalThietBi.style.display = 'none');
document.getElementById('huy-modal').addEventListener('click', () => modalThietBi.style.display = 'none');

formThietBi.addEventListener('submit', (e) => {
    e.preventDefault();
    const id = document.getElementById('id-thiet-bi').value || bangDanhMuc.rows.length + 1;
    const ten = document.getElementById('ten-thiet-bi').value;
    const danhMuc = document.getElementById('danh-muc').value;
    const soLuong = document.getElementById('so-luong').value;

    if (!document.getElementById('id-thiet-bi').value) {
        const newRow = bangDanhMuc.insertRow();
        newRow.innerHTML = `
            <td>${id}</td>
            <td>${ten}</td>
            <td>${danhMuc.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
            <td>${soLuong}</td>
            <td>
                <button class="nut-sua">Sửa</button>
                <button class="nut-xoa">Xóa</button>
            </td>
        `;
        ganSuKienNutDanhMuc(newRow);
    } else {
        const row = bangDanhMuc.rows[parseInt(id) - 1];
        row.cells[1].textContent = ten;
        row.cells[2].textContent = danhMuc.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase());
        row.cells[3].textContent = soLuong;
    }
    modalThietBi.style.display = 'none';
    alert('Cập nhật danh mục thành công!');
});

document.getElementById('nut-nhap-excel').addEventListener('click', () => {
    alert('Chức năng nhập từ Excel sẽ được triển khai sau!');
});

document.querySelectorAll('.nut-sua').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        document.getElementById('id-thiet-bi').value = row.cells[0].textContent;
        document.getElementById('ten-thiet-bi').value = row.cells[1].textContent;
        document.getElementById('danh-muc').value = row.cells[2].textContent.toLowerCase().replace(/\s/g, '-');
        document.getElementById('so-luong').value = row.cells[3].textContent;
        document.getElementById('tieu-de-modal').textContent = 'Sửa thiết bị';
        modalThietBi.style.display = 'flex';
    });
});

document.querySelectorAll('.nut-xoa').forEach(btn => {
    btn.addEventListener('click', () => {
        if (confirm('Bạn có chắc muốn xóa thiết bị này?')) {
            btn.parentElement.parentElement.remove();
            alert('Xóa thiết bị thành công!');
        }
    });
});

function ganSuKienNutDanhMuc(hang) {
    hang.querySelector('.nut-sua').addEventListener('click', () => {
        const row = hang;
        document.getElementById('id-thiet-bi').value = row.cells[0].textContent;
        document.getElementById('ten-thiet-bi').value = row.cells[1].textContent;
        document.getElementById('danh-muc').value = row.cells[2].textContent.toLowerCase().replace(/\s/g, '-');
        document.getElementById('so-luong').value = row.cells[3].textContent;
        document.getElementById('tieu-de-modal').textContent = 'Sửa thiết bị';
        modalThietBi.style.display = 'flex';
    });
    hang.querySelector('.nut-xoa').addEventListener('click', () => {
        if (confirm('Bạn có chắc muốn xóa thiết bị này?')) {
            hang.remove();
            alert('Xóa thiết bị thành công!');
        }
    });
}
document.querySelectorAll('#bang-danh-muc tr').forEach(ganSuKienNutDanhMuc);

// --- Quản lý tình trạng thiết bị ---
document.querySelectorAll('.nut-cap-nhat').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        const tinhTrang = row.querySelector('.tinh-trang-select').value;
        row.cells[2].textContent = tinhTrang.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase());
        alert('Cập nhật tình trạng thành công!');
    });
});

// --- Quản lý mượn - trả ---
document.querySelectorAll('.nut-duyet').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        row.cells[5].textContent = 'Đã duyệt';
        alert('Yêu cầu đã được duyệt!');
    });
});

document.querySelectorAll('.nut-tu-choi').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        row.cells[5].textContent = 'Từ chối';
        alert('Yêu cầu đã bị từ chối!');
    });
});

document.querySelectorAll('.nut-tra').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        const tinhTrang = row.querySelector('.tinh-trang-tra').value;
        row.cells[5].textContent = 'Đã trả';
        if (tinhTrang) row.insertCell(6).textContent = `Tình trạng: ${tinhTrang}`;
        alert('Ghi nhận trả thiết bị thành công!');
    });
});

document.getElementById('nut-in-phieu').addEventListener('click', () => {
    alert('In phiếu mượn (sẽ triển khai sau)');
});

// --- Quản lý bảo trì/sửa chữa ---
document.getElementById('nut-them-bao-tri').addEventListener('click', () => {
    alert('Thêm bảo trì sẽ được triển khai sau!');
});

// --- Quản lý kiểm kê ---
document.getElementById('nut-tao-dot-kiem-ke').addEventListener('click', () => {
    alert('Tạo đợt kiểm kê sẽ được triển khai sau!');
});

document.querySelectorAll('.nut-cap-nhat-kiem-ke').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        const soLuongThucTe = row.querySelector('.so-luong-thuc-te').value;
        const chenhLech = soLuongThucTe - parseInt(row.cells[3].textContent.split('/')[0]);
        row.cells[4].textContent = chenhLech;
        alert('Cập nhật kiểm kê thành công!');
    });
});

document.getElementById('nut-in-bien-ban').addEventListener('click', () => {
    alert('In biên bản kiểm kê (sẽ triển khai sau)');
});

// --- Lập báo cáo ---
document.getElementById('nut-tao-bao-cao').addEventListener('click', () => {
    alert('Tạo báo cáo sẽ được triển khai sau!');
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
    if (e.target === modalThietBi) modalThietBi.style.display = 'none';
    if (e.target === document.getElementById('modal-thong-bao')) document.getElementById('modal-thong-bao').style.display = 'none';
});