function moModal() {
    document.getElementById('modal-duyet').classList.add('open');
    document.body.classList.add('no-scroll');
}

function dongModal() {
    document.getElementById('modal-duyet').classList.remove('open');
    document.body.classList.remove('no-scroll');
    document.getElementById('duyet-msg').textContent = '';
}

// Bấm ra ngoài modal → tắt
document.getElementById('modal-duyet')?.addEventListener('click', function (e) {
    if (e.target === this) dongModal();
});

// Bắt nút Thao tác
document.querySelector('.ds-phieu')?.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-thao-tac');
    if (!btn) return;

    const jsonStr = btn.dataset.json;
    if (!jsonStr) return;

    try {
        const data = JSON.parse(jsonStr);
        openDuyetModal(data);
    } catch (err) {
        console.error('Lỗi parse JSON:', err);
    }
});

function openDuyetModal(data) {
    document.getElementById('duyet-mams').value = data.maMS;
    document.getElementById('duyet-ma').textContent =
        'KHM-' + new Date(data.header.ngayLap).getFullYear() + '-' + String(data.maMS).padStart(3, '0');
    document.getElementById('duyet-trangthai').textContent = data.trangThai;

    const tbody = document.getElementById('duyet-chitiet');
    tbody.innerHTML = '';
    if (data.chiTiet.length === 0) {
        tbody.innerHTML = '<tr><td colspan="2" style="text-align:center;color:#64748b;">Không có hạng mục</td></tr>';
    } else {
        data.chiTiet.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.tenTB} (${item.donVi})</td>
                <td style="text-align:center;">${item.soLuong}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    document.getElementById('form-duyet').style.display = 'block';
    document.getElementById('modal-title').textContent = 'Điều chỉnh quyết định duyệt';

    const select = document.getElementById('duyet-quyetdinh');
    select.value = data.trangThai === 'Đã duyệt' ? 'approve' : 'reject';

    moModal();
}

// Xử lý lưu quyết định
document.getElementById('form-duyet').onsubmit = function (e) {
    e.preventDefault();
    const maMS = document.getElementById('duyet-mams').value;
    const decision = document.getElementById('duyet-quyetdinh').value;

    document.getElementById('duyet-msg').textContent = 'Đang xử lý...';

    const body = new URLSearchParams();
    body.append('action', 'duyet');
    body.append('maMS', maMS);
    body.append('decision', decision);

    fetch('../controllers/TV_DuyetMuaSamController.php', {
        method: 'POST',
        body: body,
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(json => {
            if (json.success) {
                const newStatus = json.newStatus || (decision === 'approve' ? 'Đã duyệt' : 'Từ chối');

                // Cập nhật trạng thái ngay trên card, dùng class không dấu
                const card = document.querySelector(`.phieu-card[data-phieu-id="${maMS}"]`);
                if (card) {
                    const statusSpan = card.querySelector('.trang-thai');
                    statusSpan.textContent = newStatus;
                    let className = 'trang-thai ';
                    if (newStatus === 'Chờ duyệt') className += 'cho-duyet';
                    else if (newStatus === 'Đã duyệt') className += 'da-duyet';
                    else if (newStatus === 'Từ chối') className += 'tu-choi';
                    statusSpan.className = className;
                }

                dongModal();
            } else {
                document.getElementById('duyet-msg').style.color = '#dc2626';
                document.getElementById('duyet-msg').textContent = json.message || 'Lỗi xử lý';
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('duyet-msg').style.color = '#dc2626';
            document.getElementById('duyet-msg').textContent = 'Lỗi kết nối';
        });
};