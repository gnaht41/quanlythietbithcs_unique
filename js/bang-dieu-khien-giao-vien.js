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
        if (row.cells.length > 0) { // Bỏ qua header
            const cells = row.getElementsByTagName('td');
            const tenThietBi = cells[0].textContent.toLowerCase();
            const dm = cells[1].textContent;
            const tt = cells[2].textContent;

            const matchTen = ten ? tenThietBi.includes(ten) : true;
            const matchDanhMuc = danhMuc ? dm === danhMuc : true;
            const matchTinhTrang = tinhTrang ? tt === tinhTrang : true;

            row.style.display = matchTen && matchDanhMuc && matchTinhTrang ? '' : 'none';
        }
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
    const thietBi = document.getElementById('thiet-bi-muon').value;
    const ngayMuon = new Date(document.getElementById('ngay-muon').value);
    const ngayTra = new Date(document.getElementById('ngay-tra').value);
    const mucDich = document.getElementById('muc-dich').value;

    if (!thietBi || !ngayMuon || !ngayTra || !mucDich) {
        alert('Vui lòng điền đầy đủ thông tin!');
        return;
    }
    if (ngayTra <= ngayMuon) {
        alert('Ngày trả phải sau ngày mượn!');
        return;
    }
    const id = document.getElementById('id-yeu-cau').value || bang.rows.length + 1;

    if (!document.getElementById('id-yeu-cau').value) {
        const newRow = bang.insertRow();
        newRow.innerHTML = `
            <td>${id}</td>
            <td>${thietBi}</td>
            <td>${ngayMuon.toISOString().split('T')[0]}</td>
            <td>${ngayTra.toISOString().split('T')[0]}</td>
            <td>${mucDich}</td>
            <td>Chờ duyệt</td>
            <td></td>
        `;
    }
    modal.style.display = 'none';
    alert('Yêu cầu mượn đã được gửi!');
});

// --- Theo dõi yêu cầu ---
document.querySelectorAll('.nut-huy').forEach(btn => {
    btn.addEventListener('click', () => {
        if (confirm('Bạn có chắc muốn hủy yêu cầu này?')) {
            btn.parentElement.parentElement.remove();
            alert('Yêu cầu đã được hủy!');
        }
    });
});

// --- In lịch sử mượn ---
document.getElementById('nut-in-lich-su').addEventListener('click', () => {
    window.print();
    alert('In lịch sử mượn (sử dụng window.print để in trang)');
});

// --- Báo cáo hư hỏng ---
const modalBaoCao = document.getElementById('modal-bao-cao');
const formBaoCao = document.getElementById('form-bao-cao');
const bangBaoCao = document.getElementById('bang-bao-cao');

document.getElementById('nut-them-bao-cao').addEventListener('click', () => {
    formBaoCao.reset();
    document.getElementById('id-bao-cao').value = '';
    modalBaoCao.style.display = 'flex';
});

document.getElementById('dong-bao-cao').addEventListener('click', () => modalBaoCao.style.display = 'none');
document.getElementById('huy-bao-cao').addEventListener('click', () => modalBaoCao.style.display = 'none');

formBaoCao.addEventListener('submit', (e) => {
    e.preventDefault();
    const thietBi = document.getElementById('thiet-bi-hu-hong').value;
    const moTa = document.getElementById('mo-ta-hu-hong').value;
    const ngayBaoCao = new Date().toISOString().split('T')[0];

    if (!thietBi || !moTa) {
        alert('Vui lòng điền đầy đủ thông tin!');
        return;
    }
    const id = document.getElementById('id-bao-cao').value || bangBaoCao.rows.length + 1;

    if (!document.getElementById('id-bao-cao').value) {
        const newRow = bangBaoCao.insertRow();
        newRow.innerHTML = `
            <td>${id}</td>
            <td>${thietBi}</td>
            <td>${moTa}</td>
            <td>${ngayBaoCao}</td>
            <td>Chờ xử lý</td>
        `;
    }
    modalBaoCao.style.display = 'none';
    alert('Báo cáo hư hỏng đã được gửi!');
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
    if (e.target === modalBaoCao) modalBaoCao.style.display = 'none';
    if (e.target === document.getElementById('modal-thong-bao')) document.getElementById('modal-thong-bao').style.display = 'none';
});

// --- Thông báo tự động (demo) ---
setInterval(() => {
    const thietBiHong = document.querySelectorAll('#danh-muc-thiet-bi tbody td:nth-child(4)[text="Hỏng"]');
    if (thietBiHong.length > 0) {
        alert('Có thiết bị hỏng cần kiểm tra!');
    }
}, 300000); // Kiểm tra mỗi 5 phút