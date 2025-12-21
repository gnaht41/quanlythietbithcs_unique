// modal thêm
const openBtnThem = document.getElementById('nut-them-nguoi-dung');
const modalThem = document.getElementById('modal-them-nguoi-dung');
const closeBtnX = document.getElementById('dong-modal-nd');
const closeBtnHuy = document.getElementById('huy-nd');
// mở modal
openBtnThem.addEventListener('click', () => {
    modalThem.classList.add('open');
    document.body.classList.add('no-scroll');
});
// đóng dấu X
closeBtnX.addEventListener('click', () => {
    modalThem.classList.remove('open');
    document.body.classList.remove('no-scroll');
});
// Đóng nút hủy
closeBtnHuy.addEventListener('click', () => {
    modalThem.classList.remove('open');
    document.body.classList.remove('no-scroll');
});
//đóng khi click bên ngoài model
modalThem.addEventListener('click', (e) => {
    if (e.target === modalThem) {
        modalThem.classList.remove('open');
        document.body.classList.remove('no-scroll');
    }
});

// modal sua
//const openBtnSua = document.getElementById('nut-sua-nguoi-dung');
const openBtnSua = document.querySelectorAll('.nut-sua');
const modalSua = document.getElementById('modal-sua-nguoi-dung');
const closeBtnXSua = document.getElementById('dong-modal-sua');
const closeBtnHuySua = document.getElementById('huy-sua');
// mở modal
// openBtnSua.addEventListener('click', () => {
//   modalSua.classList.add('open');
//   document.body.classList.add('no-scroll');
// });
// mở modal cho từng nút sửa
openBtnSua.forEach(btn => {
    btn.addEventListener('click', () => {
        modalSua.classList.add('open');
        document.body.classList.add('no-scroll');

        // Nếu cần load dữ liệu vào form thì lấy data-mand ở đây
        const maND = btn.dataset.mand;
        const hoTen = btn.dataset.hoten;
        const username = btn.dataset.username;
        const email = btn.dataset.email;
        const tenVT = btn.dataset.tenvt;
        const trangthai = btn.dataset.trangthai;
        // const password = btn.dataset.password;
        // const ngayTao = btn.dataset.ngaytao;
        document.getElementById('sua-maND').value = maND;
        document.getElementById('sua-hoTen').value = hoTen;
        document.getElementById('sua-email').value = email;
        document.getElementById('sua-tenVT').value = tenVT;
        document.getElementById('sua-username').value = username;
        document.getElementById('sua-trangthai').value = trangthai;
        // document.getElementById('sua-password').value = password;
        // document.getElementById('sua-ngayTao').value = ngayTao;
    });
});
// đóng dấu X
closeBtnXSua.addEventListener('click', () => {
    modalSua.classList.remove('open');
    document.body.classList.remove('no-scroll');
});
// Đóng nút hủy
closeBtnHuySua.addEventListener('click', () => {
    modalSua.classList.remove('open');
    document.body.classList.remove('no-scroll');
});
//đóng khi click bên ngoài model
modalSua.addEventListener('click', (e) => {
    if (e.target === modalSua) {
        modalSua.classList.remove('open');
        document.body.classList.remove('no-scroll');
    }
});