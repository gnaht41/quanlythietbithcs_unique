<h2>Phiếu mượn</h2>
<button onclick="openCreate()" class="btn btn-primary">Tạo mới</button>

<table>
    <tr>
        <th>STT</th>
        <th>Mã phiếu</th>
        <th>Thiết bị</th>
        <th>Ngày mượn</th>
        <th>Ngày trả dự kiến</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
    </tr>
    <tbody id="list"></tbody>
</table>

<div id="modal" class="modal">
    <div class="modal-content">
        <h3 id="modal-title">Tạo phiếu mượn</h3>
        <form onsubmit="save(event)">
            <label>Ngày mượn:</label>
            <input type="date" id="ngaymuon" required>

            <label>Ngày trả:</label>
            <input type="date" id="ngaytra" required>

            <label>Mục đích:</label>
            <select id="mucdich" required>
                <option value="">Chọn mục đích</option>
                <option value="Dạy học">Dạy học</option>
                <option value="Họp phụ huynh">Họp phụ huynh</option>
                <option value="Hội nghị">Hội nghị</option>
                <option value="Thi cử">Thi cử</option>
                <option value="Hoạt động ngoại khóa">Hoạt động ngoại khóa</option>
            </select>

            <label>Thiết bị:</label>
            <div id="equipment-list"></div>
            <textarea id="selected-equipment" rows="2" readonly placeholder="Thiết bị đã chọn..."></textarea>

            <button type="submit" class="btn btn-primary">Lưu</button>
            <button type="button" onclick="closeModal()" class="btn btn-danger">Hủy</button>
        </form>
    </div>
</div>

<div id="modal-detail" class="modal">
    <div class="modal-content">
        <h3>Chi tiết phiếu</h3>
        <div id="detail-content"></div>
        <button onclick="closeModal('modal-detail')" class="btn btn-primary">Đóng</button>
    </div>
</div>

<style>
    .equipment {
        padding: 10px;
        margin: 5px 0;
        border: 1px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
    }

    .equipment:hover {
        background-color: #f5f5f5;
    }

    .equipment.selected {
        background-color: #e3f2fd;
        border-color: #2196F3;
    }

    .equipment input[type="checkbox"] {
        margin-right: 10px;
    }

    .equipment input[type="number"] {
        width: 60px;
        margin-left: 10px;
    }

    .equipment-info {
        flex: 1;
    }
</style>

<script>
    const API = '../controllers/CT_PhieuMuonAPI.php';
    let editId = null;
    let availableEquipment = [];

    function formatDate(d) {
        if (!d) return '';
        if (typeof d === 'string' && d.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
            return d;
        }
        if (typeof d === 'string' && d.match(/^\d{4}-\d{2}-\d{2}$/)) {
            const parts = d.split('-');
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
        const date = new Date(d);
        if (isNaN(date.getTime())) return '';

        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function loadList() {
        fetch(API + '?action=list&_=' + Date.now())
            .then(r => r.json())
            .then(data => {
                const list = document.getElementById('list');
                if (data.success && data.data && data.data.length) {
                    list.innerHTML = data.data.map((item, i) => {
                        const editButton = item.trangthai === 'Chờ duyệt' ?
                            `<button onclick="edit(${item.id})" class="btn btn-warning">Sửa</button>` :
                            `<button class="btn btn-secondary" disabled title="Không thể sửa phiếu ${item.trangthai}">Sửa</button>`;

                        return `<tr>
                        <td>${i + 1}</td>
                        <td>${item.ma}</td>
                        <td>${item.thietbi}</td>
                        <td>${formatDate(item.ngaymuon)}</td>
                        <td>${formatDate(item.ngaytra)}</td>
                        <td><span class="status-${item.trangthai.replace(' ', '-').toLowerCase()}">${item.trangthai}</span></td>
                        <td>
                            <button onclick="view(${item.id})" class="btn btn-info">Xem</button>
                            ${editButton}
                            <button onclick="del(${item.id})" class="btn btn-danger">Xóa</button>
                        </td>
                    </tr>`;
                    }).join('');
                } else {
                    list.innerHTML = '<tr><td colspan="7">Chưa có phiếu mượn</td></tr>';
                }
            })
            .catch(err => {
                console.error('Load error:', err);
                document.getElementById('list').innerHTML = '<tr><td colspan="7">Lỗi tải dữ liệu</td></tr>';
            });
    }

    function loadEquipmentList() {
        // Lấy danh sách thiết bị từ database
        fetch('../models/get_thietbi.php?_=' + Date.now())
            .then(r => r.json())
            .then(data => {
                if (data.success && data.data) {
                    availableEquipment = data.data;
                    renderEquipmentList();
                }
            })
            .catch(err => {
                console.error('Load equipment error:', err);
                // Fallback: danh sách cứng
                availableEquipment = [{
                        maTB: 1,
                        tenTB: 'Máy chiếu Epson EB-X06',
                        soLuongKhaDung: 3
                    },
                    {
                        maTB: 2,
                        tenTB: 'Máy tính để bàn',
                        soLuongKhaDung: 9
                    },
                    {
                        maTB: 3,
                        tenTB: 'Loa kéo',
                        soLuongKhaDung: 1
                    },
                    {
                        maTB: 4,
                        tenTB: 'Micro không dây',
                        soLuongKhaDung: 4
                    },
                    {
                        maTB: 5,
                        tenTB: 'Camera quan sát',
                        soLuongKhaDung: 1
                    }
                ];
                renderEquipmentList();
            });
    }

    function renderEquipmentList() {
        const container = document.getElementById('equipment-list');
        container.innerHTML = availableEquipment.map(item => `
        <div class="equipment">
            <input type="checkbox" onchange="toggleEquipment(this, ${item.maTB}, '${item.tenTB}')">
            <div class="equipment-info">
                <strong>${item.tenTB}</strong> (TB-${item.maTB}) - Còn: ${item.soLuongKhaDung}
            </div>
            SL: <input type="number" min="1" max="${item.soLuongKhaDung}" value="1" 
                       onclick="event.stopPropagation()" onchange="updateSelected()">
        </div>
    `).join('');
    }

    function openCreate() {
        editId = null;
        document.getElementById('modal-title').textContent = 'Tạo phiếu mượn mới';

        const today = new Date();
        const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);

        document.getElementById('ngaymuon').value = today.toISOString().split('T')[0];
        document.getElementById('ngaytra').value = nextWeek.toISOString().split('T')[0];
        document.getElementById('mucdich').value = '';
        clearEquipment();
        loadEquipmentList();
        document.getElementById('modal').style.display = 'block';
    }

    function toggleEquipment(checkbox, maTB, tenTB) {
        const el = checkbox.closest('.equipment');
        el.classList.toggle('selected', checkbox.checked);
        updateSelected();
    }

    function updateSelected() {
        const selected = [];
        document.querySelectorAll('.equipment').forEach(el => {
            const cb = el.querySelector('input[type="checkbox"]');
            const qty = el.querySelector('input[type="number"]');
            if (cb.checked) {
                const text = el.querySelector('.equipment-info strong').textContent;
                selected.push(`${text} SL:${qty.value}`);
            }
        });
        document.getElementById('selected-equipment').value = selected.join(', ');
    }

    function clearEquipment() {
        document.querySelectorAll('.equipment').forEach(el => {
            el.querySelector('input[type="checkbox"]').checked = false;
            el.querySelector('input[type="number"]').value = 1;
            el.classList.remove('selected');
        });
        document.getElementById('selected-equipment').value = '';
    }

    function view(id) {
        fetch(API + '?action=detail&id=' + id)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const item = data.data;
                    document.getElementById('detail-content').innerHTML = `
                    <p><b>Mã phiếu:</b> ${item.ma}</p>
                    <p><b>Thiết bị:</b> ${item.thietbi}</p>
                    <p><b>Ngày mượn:</b> ${formatDate(item.ngaymuon)}</p>
                    <p><b>Ngày trả:</b> ${formatDate(item.ngaytra)}</p>
                    <p><b>Trạng thái:</b> <span class="status-text">${item.trangthai}</span></p>
                `;
                    document.getElementById('modal-detail').style.display = 'block';
                } else {
                    alert('Lỗi: ' + data.message);
                }
            });
    }

    function edit(id) {
        fetch(API + '?action=detail&id=' + id)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const item = data.data;
                    editId = id;
                    document.getElementById('modal-title').textContent = 'Sửa phiếu mượn';
                    document.getElementById('ngaymuon').value = item.ngaymuon;
                    document.getElementById('ngaytra').value = item.ngaytra;
                    document.getElementById('mucdich').value = item.mucdich || '';
                    loadEquipmentList();
                    setTimeout(() => {
                        clearEquipment();
                        if (item.thietbi) {
                            document.getElementById('selected-equipment').value = item.thietbi;
                        }
                    }, 500);
                    document.getElementById('modal').style.display = 'block';
                } else {
                    alert('Lỗi: ' + data.message);
                }
            });
    }

    function save(e) {
        e.preventDefault();

        const ngayMuonISO = document.getElementById('ngaymuon').value;
        const ngayTraISO = document.getElementById('ngaytra').value;
        const ngayMuon = ngayMuonISO ? new Date(ngayMuonISO) : null;
        const ngayTra = ngayTraISO ? new Date(ngayTraISO) : null;

        if (!ngayMuon || !ngayTra || ngayTra <= ngayMuon) {
            alert('⚠️ Ngày trả phải sau ngày mượn!');
            return;
        }

        const selectedEquipment = document.getElementById('selected-equipment').value;
        if (!selectedEquipment.trim()) {
            alert('Vui lòng chọn ít nhất một thiết bị!');
            return;
        }

        const mucDichValue = document.getElementById('mucdich').value;

        const form = new FormData();
        form.append('action', editId ? 'update' : 'create');
        if (editId) form.append('id', editId);
        form.append('ngaymuon', ngayMuonISO);
        form.append('ngaytra', ngayTraISO);
        form.append('mucdich', mucDichValue);

        const checkboxes = document.querySelectorAll('.equipment input[type="checkbox"]:checked');
        form.append('tb_count', checkboxes.length);

        checkboxes.forEach((cb, index) => {
            const el = cb.closest('.equipment');
            const text = el.querySelector('.equipment-info strong').textContent;
            const maTB = text.match(/TB-(\d+)/)?.[1] || index;
            const qty = el.querySelector('input[type="number"]').value;

            form.append(`tb_ma_${index}`, 'TB-' + maTB);
            form.append(`tb_ten_${index}`, text);
            form.append(`tb_sl_${index}`, qty);
        });

        fetch(API, {
                method: 'POST',
                body: form
            })
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    alert(editId ? 'Cập nhật thành công' : 'Tạo phiếu thành công');
                    closeModal();
                    loadList();
                } else {
                    alert('Lỗi: ' + result.message);
                }
            });
    }

    function del(id) {
        if (confirm('Xóa phiếu mượn này?')) {
            fetch(API, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        id: id
                    })
                })
                .then(r => r.json())
                .then(data => {
                    alert(data.success ? 'Xóa thành công' : 'Lỗi: ' + data.message);
                    if (data.success) loadList();
                });
        }
    }

    function closeModal(modalId = 'modal') {
        document.getElementById(modalId).style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadList();
        loadEquipmentList();
    });

    window.onclick = e => e.target.classList.contains('modal') && closeModal(e.target.id);
</script>