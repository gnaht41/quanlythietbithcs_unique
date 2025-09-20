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

// --- Tìm kiếm thiết bị ---
document.getElementById('nut-tim-kiem').addEventListener('click', () => {
    const ten = document.getElementById('tim-kiem-ten').value.toLowerCase();
    const danhMuc = document.getElementById('tim-kiem-danh-muc').value;
    const tinhTrang = document.getElementById('tim-kiem-tinh-trang').value;
    const rows = document.getElementById('bang-tim-kiem').getElementsByTagName('tr');

    for (let row of rows) {
        const cells = row.getElementsByTagName('td');
        const tenThietBi = cells[0].textContent.toLowerCase();
        const dm = cells[1].textContent;
        const tt = cells[2].textContent;

        const matchTen = ten ? tenThietBi.includes(ten) : true;
        const matchDanhMuc = danhMuc ? dm === danhMuc.replace('-', ' ') : true;
        const matchTinhTrang = tinhTrang ? tt === tinhTrang.replace('-', ' ') : true;

        row.style.display = matchTen && matchDanhMuc && matchTinhTrang ? '' : 'none';
    }
});

// --- Gửi yêu cầu mượn ---
const modal = document.getElementById('modal-yeu-cau');
const form = document.getElementById('form-yeu-cau');
const bang = document.getElementById('bang-yeu-cau');

document.getElementById('nut-them-yeu-cau').addEventListener('click', () => {
    form.reset();
    document.getElementById('id-yeu-cau').value = '';
    modal.style.display = 'flex';
});

document.getElementById('dong-modal').addEventListener('click', () => modal.style.display = 'none');
document.getElementById('huy-modal').addEventListener('click', () => modal.style.display = 'none');

form.addEventListener('submit', (e) => {
    e.preventDefault();
    const id = document.getElementById('id-yeu-cau').value || bang.rows.length + 1;
    const thietBi = document.getElementById('thiet-bi-muon').value;
    const ngayMuon = document.getElementById('ngay-muon').value;
    const ngayTra = document.getElementById('ngay-tra').value;
    const mucDich = document.getElementById('muc-dich').value;

    if (!document.getElementById('id-yeu-cau').value) {
        const newRow = bang.insertRow();
        newRow.innerHTML = `
            <td>${id}</td>
            <td>${thietBi.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
            <td>${ngayMuon}</td>
            <td>${ngayTra}</td>
            <td>${mucDich}</td>
            <td>Chờ duyệt</td>
            <td></td>
        `;
    }
    modal.style.display = 'none';
    alert('Yêu cầu mượn đã được gửi!');
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