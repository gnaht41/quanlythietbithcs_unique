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

// --- Duyệt kế hoạch và thanh lý ---
document.querySelectorAll('.nut-phe-duyet').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        row.cells[4].textContent = 'Đã phê duyệt';
        alert('Phê duyệt thành công!');
    });
});

document.querySelectorAll('.nut-tu-choi').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.parentElement.parentElement;
        row.cells[4].textContent = 'Từ chối';
        alert('Từ chối thành công!');
    });
});

// --- Xuất báo cáo ---
document.getElementById('nut-xuat-pdf').addEventListener('click', () => {
    window.print(); // In toàn bộ trang làm demo
    alert('In báo cáo PDF (sử dụng window.print)');
});

document.getElementById('nut-xuat-excel').addEventListener('click', () => {
    alert('Tải báo cáo dưới dạng Excel (sẽ triển khai sau)');
});

// --- Quản lý kiểm kê ---
document.getElementById('nut-tao-dot-kiem-ke').addEventListener('click', () => {
    alert('Tạo đợt kiểm kê mới (sẽ triển khai sau)');
});

document.querySelectorAll('.nut-phe-duyet').forEach(btn => {
    if (btn.textContent === 'Cập nhật') {
        btn.addEventListener('click', () => {
            const row = btn.parentElement.parentElement;
            const duKien = parseInt(row.cells[2].textContent);
            const thucTe = parseInt(row.querySelector('.so-luong-thuc-te').value);
            row.cells[4].textContent = thucTe - duKien;
            alert('Cập nhật kiểm kê thành công!');
        });
    }
});

document.getElementById('nut-in-bien-ban').addEventListener('click', () => {
    window.print();
    alert('In biên bản kiểm kê (sử dụng window.print)');
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
    const modalTB = document.getElementById('modal-thong-bao');
    if (e.target === modalTB) modalTB.style.display = 'none';
});