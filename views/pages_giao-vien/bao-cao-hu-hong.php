<h2>Báo cáo hư hỏng</h2>
<button onclick="openCreate()" class="btn btn-primary">Tạo báo cáo</button>

<table>
    <tr>
        <th>STT</th>
        <th>Mã BC</th>
        <th>Tên TB</th>
        <th>Tình trạng</th>
        <th>Ngày BC</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
    </tr>
    <tbody id="list"></tbody>
</table>

<div id="modal" class="modal">
    <div class="modal-content">
        <h3 id="title">Tạo báo cáo hư hỏng</h3>
        <form onsubmit="save(event)">
            <label>Chọn thiết bị:</label>
            <div id="equipment-list"></div>

            <label>Tình trạng hư hỏng:</label>
            <textarea id="tinhtrang" placeholder="Mô tả tình trạng hư hỏng..." required rows="3"></textarea>

            <label>Nội dung chi tiết:</label>
            <textarea id="noidung" placeholder="Mô tả chi tiết về hư hỏng..." required rows="4"></textarea>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Lưu báo cáo</button>
                <button type="button" onclick="closeModal()" class="btn btn-danger">Hủy</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-detail" class="modal">
    <div class="modal-content">
        <h3>Chi tiết báo cáo</h3>
        <div id="detail"></div>
        <button onclick="closeModal('modal-detail')" class="btn btn-primary">Đóng</button>
    </div>
</div>

<script>
const API = '../controllers/CT_BaoCaoAPI_Simple.php';
let editId = null;

function loadList() {
    fetch(API + '?action=danh-sach-bao-cao&_=' + Date.now())
        .then(r => r.json())
        .then(data => {
            const list = document.getElementById('list');
            if (data.success && data.data && data.data.length) {
                list.innerHTML = data.data.map((item, i) => `
                    <tr>
                        <td>${i + 1}</td>
                        <td>BC-${item.maBaoCao}</td>
                        <td>${item.tenTB}</td>
                        <td>${item.tinhTrang.substring(0, 50)}${item.tinhTrang.length > 50 ? '...' : ''}</td>
                        <td>${new Date(item.ngayBaoCao).toLocaleDateString('vi-VN')}</td>
                        <td><span class="status-text">${item.trangThai || 'Đang xử lý'}</span></td>
                        <td>
                            <button onclick="view(${item.maBaoCao})" class="btn btn-info">Xem</button>
                            <button onclick="edit(${item.maBaoCao})" class="btn btn-warning">Sửa</button>
                            <button onclick="del(${item.maBaoCao})" class="btn btn-danger">Xóa</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                list.innerHTML = '<tr><td colspan="7">Chưa có báo cáo</td></tr>';
            }
        })
        .catch(err => {
            console.error('Load error:', err);
            document.getElementById('list').innerHTML = '<tr><td colspan="7">Lỗi tải dữ liệu</td></tr>';
        });
}

function openCreate() {
    editId = null;
    document.getElementById('title').textContent = 'Tạo báo cáo hư hỏng';
    document.getElementById('tinhtrang').value = '';
    document.getElementById('noidung').value = '';
    loadEquipment();
    showModal('modal');
}

function loadEquipment() {
    fetch(API + '?action=lay-thiet-bi-dang-muon&_=' + Date.now())
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('equipment-list');
            if (data.success && data.data && data.data.length) {
                container.innerHTML = data.data.map(item => `
                    <div class="equipment" onclick="selectEquipment(this)">
                        <input type="radio" name="equipment" value="${item.maTB}" 
                               data-ten="${item.tenTB}" data-phieu="${item.maPhieu}">
                        <strong>${item.tenTB}</strong> (${item.maTB}) - Phiếu: ${item.maPhieu}
                    </div>
                `).join('');
            } else {
                container.innerHTML =
                    '<div class="no-equipment">Không có thiết bị đang mượn. Vui lòng tạo phiếu mượn trước.</div>';
            }
        })
        .catch(err => {
            console.error('Load equipment error:', err);
            document.getElementById('equipment-list').innerHTML =
                '<div class="no-equipment">Lỗi tải danh sách thiết bị</div>';
        });
}

function selectEquipment(el) {
    document.querySelectorAll('.equipment').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
}

function save(e) {
    e.preventDefault();

    const selected = document.querySelector('input[name="equipment"]:checked');
    if (!selected && !editId) {
        alert('Vui lòng chọn thiết bị!');
        return;
    }

    const data = {
        tinhTrang: document.getElementById('tinhtrang').value,
        noiDungBaoCao: document.getElementById('noidung').value
    };

    if (editId) {
        data.maBaoCao = editId;
    } else {
        data.maTB = selected.value;
        data.tenTB = selected.dataset.ten;
        data.maPhieu = selected.dataset.phieu;
    }

    const url = API + '?action=' + (editId ? 'cap-nhat-bao-cao' : 'tao-bao-cao');

    fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                closeModal();
                loadList();
            } else {
                alert('Lỗi: ' + result.message);
            }
        })
        .catch(err => {
            console.error('Save error:', err);
            alert('Lỗi khi lưu báo cáo');
        });
}

function view(id) {
    fetch(API + '?action=chi-tiet-bao-cao&id=' + id)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                document.getElementById('detail').innerHTML = `
                    <p><b>Mã báo cáo:</b> BC-${item.maBaoCao}</p>
                    <p><b>Mã thiết bị:</b> ${item.maTB}</p>
                    <p><b>Tên thiết bị:</b> ${item.tenTB}</p>
                    <p><b>Tình trạng hư hỏng:</b> ${item.tinhTrang}</p>
                    <p><b>Nội dung chi tiết:</b> ${item.noiDungBaoCao}</p>
                    <p><b>Ngày báo cáo:</b> ${new Date(item.ngayBaoCao).toLocaleString('vi-VN')}</p>
                    <p><b>Trạng thái:</b> <span class="status-text">${item.trangThai || 'Đang xử lý'}</span></p>
                `;
                showModal('modal-detail');
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
}

function edit(id) {
    fetch(API + '?action=chi-tiet-bao-cao&id=' + id)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                editId = id;
                document.getElementById('title').textContent = 'Sửa báo cáo hư hỏng';
                document.getElementById('tinhtrang').value = item.tinhTrang;
                document.getElementById('noidung').value = item.noiDungBaoCao;
                document.getElementById('equipment-list').innerHTML = `
                    <div class="equipment selected">
                        <input type="radio" name="equipment" value="${item.maTB}" 
                               data-ten="${item.tenTB}" checked>
                        <strong>${item.tenTB}</strong> (${item.maTB}) - Đang chỉnh sửa
                    </div>
                `;
                showModal('modal');
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
}

function del(id) {
    if (confirm('Bạn có chắc chắn muốn xóa báo cáo này?')) {
        fetch(API + '?action=xoa-bao-cao', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    maBaoCao: id
                })
            })
            .then(r => r.json())
            .then(data => {
                alert(data.success ? 'Xóa báo cáo thành công!' : 'Lỗi: ' + data.message);
                if (data.success) loadList();
            });
    }
}

function showModal(id) {
    document.getElementById(id).style.display = 'block';
}

function closeModal(id = 'modal') {
    document.getElementById(id).style.display = 'none';
}

document.addEventListener('DOMContentLoaded', loadList);
window.onclick = e => e.target.classList.contains('modal') && closeModal(e.target.id);
</script>

<style>
.equipment {
    padding: 10px;
    margin: 5px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.equipment:hover {
    background-color: #f5f5f5;
}

.equipment.selected {
    background-color: #e3f2fd;
    border-color: #2196F3;
}

.equipment input[type="radio"] {
    margin-right: 10px;
}

.no-equipment {
    padding: 20px;
    text-align: center;
    color: #999;
    font-style: italic;
}

.form-buttons {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}
</style>