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
    const ten = document.getElementById('ten-thiet-bi').value;
    const nhom = document.getElementById('nhom-thiet-bi').value;
    const danhMuc = document.getElementById('danh-muc').value;
    const soLuong = document.getElementById('so-luong').value;
    if (!ten || !nhom || !danhMuc || !soLuong) {
        alert('Vui lòng điền đầy đủ thông tin!');
        return;
    }
    const id = document.getElementById('id-thiet-bi').value || bangDanhMuc.rows.length + 1;

    if (!document.getElementById('id-thiet-bi').value) {
        const newRow = bangDanhMuc.insertRow();
        newRow.innerHTML = `
            <td>${id}</td>
            <td>${ten}</td>
            <td>${nhom.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
            <td>${danhMuc}</td>
            <td>${soLuong}</td>
            <td><button class="nut-sua">Sửa</button> <button class="nut-xoa">Xóa</button></td>
        `;
        ganSuKienNutDanhMuc(newRow);
    } else {
        const row = bangDanhMuc.rows[parseInt(id) - 1];
        row.cells[1].textContent = ten;
        row.cells[2].textContent = nhom.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase());
        row.cells[3].textContent = danhMuc;
        row.cells[4].textContent = soLuong;
    }
    modalThietBi.style.display = 'none';
    alert('Cập nhật danh mục thành công!');
});

document.getElementById('nut-nhap-excel').addEventListener('click', () => {
    const fileInput = document.getElementById('file-excel');
    if (fileInput.files.length > 0) {
        const reader = new FileReader();
        reader.onload = (e) => {
            alert(`Nhập file Excel: ${fileInput.files[0].name} - Nội dung (demo): ${e.target.result.substring(0, 100)}... (sẽ triển khai xử lý Excel sau)`);
        };
        reader.readAsText(fileInput.files[0]);
    } else {
        alert('Vui lòng chọn file Excel!');
    }
});

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
        if (!tinhTrang) {
            alert('Vui lòng nhập tình trạng trả!');
            return;
        }
        row.cells[5].textContent = 'Đã trả';
        row.cells[6].textContent = tinhTrang;
        alert('Ghi nhận trả thiết bị thành công!');
    });
});

document.getElementById('nut-in-phieu').addEventListener('click', () => {
    window.print();
    alert('In phiếu mượn (sử dụng window.print để in trang)');
});

// --- Quản lý bảo trì/sửa chữa ---
const modalBaoTri = document.getElementById('modal-bao-tri');
const formBaoTri = document.getElementById('form-bao-tri');
const bangBaoTri = document.getElementById('bang-bao-tri');

document.getElementById('nut-them-bao-tri').addEventListener('click', () => {
    formBaoTri.reset();
    document.getElementById('id-bao-tri').value = '';
    document.getElementById('tieu-de-bao-tri').textContent = 'Thêm bảo trì';
    modalBaoTri.style.display = 'flex';
});

document.getElementById('dong-bao-tri').addEventListener('click', () => modalBaoTri.style.display = 'none');
document.getElementById('huy-bao-tri').addEventListener('click', () => modalBaoTri.style.display = 'none');

formBaoTri.addEventListener('submit', (e) => {
    e.preventDefault();
    const thietBi = document.getElementById('thiet-bi-bao-tri').value;
    const ngay = document.getElementById('ngay-bao-tri').value;
    const moTa = document.getElementById('mo-ta-bao-tri').value;
    const trangThai = document.getElementById('trang-thai-bao-tri').value;
    if (!thietBi || !ngay || !moTa || !trangThai) {
        alert('Vui lòng điền đầy đủ thông tin!');
        return;
    }
    const id = document.getElementById('id-bao-tri').value || bangBaoTri.rows.length + 1;

    if (!document.getElementById('id-bao-tri').value) {
        const newRow = bangBaoTri.insertRow();
        newRow.innerHTML = `
            <td>${id}</td>
            <td>${thietBi}</td>
            <td>${ngay}</td>
            <td>${moTa}</td>
            <td>${trangThai.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
            <td><button class="nut-sua">Sửa</button></td>
        `;
        ganSuKienNutBaoTri(newRow);
    } else {
        const row = bangBaoTri.rows[parseInt(id) - 1];
        row.cells[1].textContent = thietBi;
        row.cells[2].textContent = ngay;
        row.cells[3].textContent = moTa;
        row.cells[4].textContent = trangThai.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
    modalBaoTri.style.display = 'none';
    alert('Cập nhật bảo trì thành công!');
});

function ganSuKienNutBaoTri(hang) {
    hang.querySelector('.nut-sua').addEventListener('click', () => {
        const row = hang;
        document.getElementById('id-bao-tri').value = row.cells[0].textContent;
        document.getElementById('thiet-bi-bao-tri').value = row.cells[1].textContent;
        document.getElementById('ngay-bao-tri').value = row.cells[2].textContent;
        document.getElementById('mo-ta-bao-tri').value = row.cells[3].textContent;
        document.getElementById('trang-thai-bao-tri').value = row.cells[4].textContent.toLowerCase().replace(/\s/g, '-');
        document.getElementById('tieu-de-bao-tri').textContent = 'Sửa bảo trì';
        modalBaoTri.style.display = 'flex';
    });
}
document.querySelectorAll('#bang-bao-tri tr').forEach(ganSuKienNutBaoTri);

// --- Quản lý kiểm kê ---
document.getElementById('nut-tao-dot-kiem-ke').addEventListener('click', () => {
    alert('Tạo đợt kiểm kê mới (sẽ triển khai sau)');
});

document.querySelectorAll('.nut-cap-nhat').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        const duKien = parseInt(row.cells[3].textContent);
        const thucTe = parseInt(row.querySelector('.so-luong-thuc-te').value);
        if (isNaN(thucTe)) {
            alert('Vui lòng nhập số lượng thực tế hợp lệ!');
            return;
        }
        const chenhLech = thucTe - duKien;
        row.cells[5].textContent = chenhLech + (chenhLech > 0 ? ' (tăng)' : (chenhLech < 0 ? ' (giảm)' : ''));
        alert('Cập nhật kiểm kê thành công!');
    });
});

document.getElementById('nut-in-bien-ban').addEventListener('click', () => {
    window.print();
    alert('In biên bản kiểm kê (sử dụng window.print để in trang)');
});

// --- Lập báo cáo ---
document.getElementById('nut-tao-bao-cao').addEventListener('click', () => {
    alert('Tạo báo cáo định kỳ (sẽ triển khai sau)');
});

document.querySelectorAll('.nut-in').forEach(btn => {
    btn.addEventListener('click', () => {
        window.print();
        alert('In báo cáo (sử dụng window.print để in trang)');
    });
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
    if (e.target === modalBaoTri) modalBaoTri.style.display = 'none';
    if (e.target === document.getElementById('modal-thong-bao')) document.getElementById('modal-thong-bao').style.display = 'none';
});

// --- Thông báo tự động (demo) ---
setInterval(() => {
    const hong = document.querySelectorAll('#bang-tinh-trang td:nth-child(3)[text="Hỏng"]');
    if (hong.length > 0) {
        alert('Có thiết bị hỏng cần kiểm tra!');
    }
}, 300000); // Kiểm tra mỗi 5 phút