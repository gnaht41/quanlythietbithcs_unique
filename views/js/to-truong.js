// Modal mở/đóng
function moModal() {
    document.getElementById('modal-phieu').classList.add('open');
    document.body.classList.add('no-scroll');
}

function dongModal() {
    document.getElementById('modal-phieu').classList.remove('open');
    document.body.classList.remove('no-scroll');
}

document.getElementById('modal-phieu')?.addEventListener('click', function (e) {
    if (e.target === this) dongModal();
});

// Lập phiếu mới (thêm mới → bắt buộc có 1 dòng)
function lapPhieuMoi() {
    document.getElementById('maMS').value = '';
    document.querySelector('#bang-chi-tiet tbody').innerHTML = '';
    document.getElementById('modal-title').textContent = 'Lập kế hoạch mua sắm mới';
    document.getElementById('btn-luu').style.display = 'inline-block';
    document.getElementById('btn-them').style.display = 'inline-block';
    themDong(); // Thêm 1 dòng mặc định bắt buộc
    moModal();
}

// Thêm dòng thiết bị
function themDong() {
    const tbody = document.querySelector('#bang-chi-tiet tbody');
    const daChon = Array.from(tbody.querySelectorAll('select[name="maTB[]"]')).map(s => s.value);

    let options = '<option value="">-- Chọn thiết bị --</option>';
    window.dsThietBi.forEach(tb => {
        if (!daChon.includes(String(tb.maTB))) {
            options += `<option value="${tb.maTB}">${tb.tenTB} (${tb.donVi})</option>`;
        }
    });

    if (options === '<option value="">-- Chọn thiết bị --</option>') {
        alert('Không còn thiết bị nào để thêm!');
        return;
    }

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><select name="maTB[]" required>${options}</select></td>
        <td><input type="number" name="soLuong[]" min="1" value="1" required></td>
        <td><button type="button" class="btn-xoa" onclick="xoaDong(this)">🗑</button></td>
    `;
    tbody.appendChild(tr);
}

// Xóa dòng – PHÂN BIỆT THÊM MỚI HAY CHỈNH SỬA
function xoaDong(btn) {
    const maMS = document.getElementById('maMS').value;
    const isThemMoi = maMS === ''; // Phân biệt thêm mới (maMS rỗng)
    const tbody = document.querySelector('#bang-chi-tiet tbody');
    const rows = tbody.querySelectorAll('tr');

    if (isThemMoi && rows.length <= 1) {
        alert('Phải có ít nhất một thiết bị khi thêm mới!');
        return; // Không cho xóa dòng cuối khi thêm mới
    }

    // Nếu chỉnh sửa (maMS có giá trị) → cho xóa hết
    btn.closest('tr').remove();

    // Nếu bảng trống (khi chỉnh sửa) → không làm gì thêm (sẽ xóa phiếu khi lưu)
}

// Xem / chỉnh sửa phiếu
function xemPhieu(data) {
    const tbody = document.querySelector('#bang-chi-tiet tbody');
    tbody.innerHTML = '';
    document.getElementById('maMS').value = data.maMS;

    const choSua = data.trangThai === 'Chờ duyệt';
    document.getElementById('modal-title').textContent = choSua ? `Chỉnh sửa phiếu #${data.maMS}` : `Xem phiếu #${data.maMS}`;

    data.chiTiet.forEach(item => {
        let select = `<select name="maTB[]" ${choSua ? 'required' : 'disabled'}>`;
        select += '<option value="">-- Chọn thiết bị --</option>';
        window.dsThietBi.forEach(tb => {
            const selected = tb.maTB == item.maTB ? 'selected' : '';
            select += `<option value="${tb.maTB}" ${selected}>${tb.tenTB} (${tb.donVi})</option>`;
        });
        select += '</select>';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${select}</td>
            <td><input type="number" name="soLuong[]" min="1" value="${item.soLuong}" ${choSua ? 'required' : 'disabled'}></td>
            <td>${choSua ? '<button type="button" class="btn-xoa" onclick="xoaDong(this)">🗑</button>' : ''}</td>
        `;
        tbody.appendChild(tr);
    });

    // Nếu chỉnh sửa và không có chi tiết → thêm 1 dòng mặc định
    if (choSua && tbody.children.length === 0) {
        themDong();
    }

    document.getElementById('btn-luu').style.display = choSua ? 'inline-block' : 'none';
    document.getElementById('btn-them').style.display = choSua ? 'inline-block' : 'none';

    moModal();
}