<h2>Phiếu mượn</h2><button onclick="openCreate()" class="btn btn-primary">Tạo mới</button>
<table>
    <tr>
        <th>STT</th>
        <th>Mã</th>
        <th>Thiết bị</th>
        <th>SL</th>
        <th>Mục đích</th>
        <th>Ngày mượn</th>
        <th>Ngày trả</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
    </tr>
    <tbody id="list"></tbody>
</table>

<div id="modal" class="modal">
    <div class="modal-content">
        <h3 id="modal-title">Tạo phiếu mượn</h3>
        <form onsubmit="save(event)">
            <label>Ngày mượn:</label><input type="date" id="ngaymuon" required>
            <label>Ngày trả:</label><input type="date" id="ngaytra" required>
            <label>Mục đích:</label><select id="mucdich" required>
                <option value="">Chọn mục đích</option>
                <option value="Dạy học">Dạy học</option>
                <option value="Họp phụ huynh">Họp phụ huynh</option>
                <option value="Hội nghị">Hội nghị</option>
                <option value="Thi cử">Thi cử</option>
                <option value="Hoạt động ngoại khóa">Hoạt động ngoại khóa</option>
            </select>
            <label>Thiết bị:</label>
            <div id="equipment-list">
                <div class="equipment"><input type="checkbox" onchange="toggleEquipment(this,'Máy tính Dell','TB-001')">
                    Máy tính Dell (TB-001) - SL: <input type="number" min="1" max="5" value="1"
                        onclick="event.stopPropagation()" onchange="updateSelected()"></div>
                <div class="equipment"><input type="checkbox"
                        onchange="toggleEquipment(this,'Máy chiếu Epson','TB-002')"> Máy chiếu Epson (TB-002) - SL:
                    <input type="number" min="1" max="3" value="1" onclick="event.stopPropagation()"
                        onchange="updateSelected()">
                </div>
                <div class="equipment"><input type="checkbox" onchange="toggleEquipment(this,'Loa Bluetooth','TB-003')">
                    Loa Bluetooth (TB-003) - SL: <input type="number" min="1" max="10" value="1"
                        onclick="event.stopPropagation()" onchange="updateSelected()"></div>
            </div><textarea id="selected-equipment" rows="2" readonly placeholder="Thiết bị đã chọn..."></textarea>
            <label>Địa điểm:</label><input type="text" id="diadiem" placeholder="Phòng học, hội trường..." required>
            <label>Ghi chú:</label><textarea id="ghichu" rows="2" placeholder="Ghi chú thêm (nếu có)"></textarea>
            <button type="submit" class="btn btn-primary" style="margin-right: 10px;">Lưu</button><button type="button"
                onclick="closeModal()" class="btn btn-danger">Hủy</button>
        </form>
    </div>
</div>

<div id="modal-detail" class="modal">
    <div class="modal-content">
        <h3>Chi tiết phiếu</h3>
        <div id="detail-content"></div><button onclick="closeModal('modal-detail')"
            class="btn btn-primary">Đóng</button>
    </div>
</div>

<script>
const API = '../controllers/CT_PhieuMuonAPI.php';
let editId = null;

function formatDate(d) {
    if (!d) return '';
    // Nếu d đã là định dạng DD/MM/YYYY thì trả về luôn
    if (typeof d === 'string' && d.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
        return d;
    }
    // Xử lý định dạng YYYY-MM-DD từ database
    if (typeof d === 'string' && d.match(/^\d{4}-\d{2}-\d{2}$/)) {
        const parts = d.split('-');
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    // Fallback cho Date object
    const date = new Date(d);
    if (isNaN(date.getTime())) return '';

    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

function loadList() {
    fetch(API + '?action=list').then(r => r.json()).then(data => {
        console.log('API Response:', data); // Debug log
        const list = document.getElementById('list');
        if (data.success && data.data.length) {
            list.innerHTML = data.data.map((item, i) => {
                console.log('Item:', item); // Debug log cho từng item
                const equipmentText = item.thietbi;
                let equipmentName = equipmentText;
                let totalQuantity = 0;
                const quantityMatches = equipmentText.match(/SL:(\d+)/g);
                if (quantityMatches) {
                    totalQuantity = quantityMatches.reduce((sum, match) => sum + parseInt(match.replace(
                        'SL:', '')), 0);
                    equipmentName = equipmentText.replace(/\s*SL:\d+/g, '');
                }
                // Chỉ hiển thị nút Sửa cho phiếu "Chờ duyệt"
                const editButton = item.trangthai === 'Chờ duyệt' ?
                    `<button onclick="edit(${item.id})" class="btn btn-warning">Sửa</button>` :
                    `<button class="btn btn-secondary" disabled title="Không thể sửa phiếu đang mượn">Sửa</button>`;
                return `<tr><td>${i + 1}</td><td>${item.ma}</td><td>${equipmentName}</td><td>${totalQuantity || 1}</td><td>${item.mucdich || 'Không có'}</td><td>${formatDate(item.ngaymuon)}</td><td>${formatDate(item.ngaytra)}</td><td><span class="status-${item.trangthai.replace(' ', '-').toLowerCase()}">${item.trangthai}</span></td><td><button onclick="view(${item.id})" class="btn btn-info">Xem</button> ${editButton} <button onclick="del(${item.id})" class="btn btn-danger">Xóa</button></td></tr>`;
            }).join('');
        } else {
            list.innerHTML = '<tr><td colspan="9">Chưa có phiếu</td></tr>';
        }
    });
}

function openCreate() {
    editId = null;
    document.getElementById('modal-title').textContent = 'Tạo phiếu mượn mới';
    // Thiết lập ngày mặc định
    const today = new Date();
    const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);

    document.getElementById('ngaymuon').value = today.toISOString().split('T')[0];
    document.getElementById('ngaytra').value = nextWeek.toISOString().split('T')[0];
    document.getElementById('mucdich').value = '';
    clearEquipment();
    document.getElementById('diadiem').value = '';
    document.getElementById('ghichu').value = '';
    document.getElementById('modal').style.display = 'block';
}

function toggleEquipment(checkbox, name, code) {
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
            const text = el.textContent.split(' - SL:')[0].replace('☐', '').replace('☑', '').trim();
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
    fetch(API + '?action=detail&id=' + id).then(r => r.json()).then(data => {
        if (data.success) {
            const item = data.data;
            document.getElementById('detail-content').innerHTML =
                `<p><b>Mã:</b> ${item.ma}</p><p><b>Thiết bị:</b> ${item.thietbi}</p><p><b>Mục đích:</b> ${item.mucdich}</p><p><b>Ngày mượn:</b> ${formatDate(item.ngaymuon)}</p><p><b>Ngày trả:</b> ${formatDate(item.ngaytra)}</p><p><b>Trạng thái:</b> <span class="status-text">${item.trangthai}</span></p>`;
            document.getElementById('modal-detail').style.display = 'block';
        } else alert('Lỗi: ' + data.message);
    });
}

function edit(id) {
    fetch(API + '?action=detail&id=' + id).then(r => r.json()).then(data => {
        if (data.success) {
            const item = data.data;
            editId = id;
            document.getElementById('modal-title').textContent = 'Sửa phiếu mượn';
            document.getElementById('ngaymuon').value = item.ngaymuon;
            document.getElementById('ngaytra').value = item.ngaytra;
            document.getElementById('mucdich').value = item.mucdich || '';
            clearEquipment();
            if (item.thietbi) document.getElementById('selected-equipment').value = item.thietbi;
            document.getElementById('diadiem').value = item.diadiem || '';
            document.getElementById('ghichu').value = item.ghichu || '';
            document.getElementById('modal').style.display = 'block';
        } else alert('Lỗi: ' + data.message);
    });
}

function save(e) {
    e.preventDefault();
    // Validation ngày tháng trước khi gửi - CHỈ kiểm tra ngày trả > ngày mượn
    const ngayMuonISO = document.getElementById('ngaymuon').value;
    const ngayTraISO = document.getElementById('ngaytra').value;
    const ngayMuon = ngayMuonISO ? new Date(ngayMuonISO) : null;
    const ngayTra = ngayTraISO ? new Date(ngayTraISO) : null;
    // Kiểm tra ngày trả phải sau ngày mượn
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
    console.log('=== FRONTEND SAVE DEBUG ===');
    console.log('mucDich value:', mucDichValue);
    console.log('mucDich length:', mucDichValue.length);
    console.log('mucDich charCodes:', mucDichValue.split('').map(c => c.charCodeAt(0)));

    const form = new FormData();
    form.append('action', editId ? 'update' : 'create');
    if (editId) form.append('id', editId);
    // Gửi ngày ở định dạng ISO (YYYY-MM-DD) - input.date cung cấp ISO
    form.append('ngaymuon', ngayMuonISO);
    form.append('ngaytra', ngayTraISO);
    form.append('mucdich', mucDichValue);
    form.append('diadiem', document.getElementById('diadiem').value);
    form.append('ghichu', document.getElementById('ghichu').value);
    const checkboxes = document.querySelectorAll('.equipment input[type="checkbox"]:checked');
    form.append('tb_count', checkboxes.length);
    checkboxes.forEach((cb, index) => {
        const el = cb.closest('.equipment');
        const text = el.textContent.split(' - SL:')[0].replace('☐', '').replace('☑', '').trim();
        const parts = text.split(' (');
        const name = parts[0];
        const code = parts[1] ? parts[1].replace(')', '') : 'TB-' + index;
        const qty = el.querySelector('input[type="number"]').value;
        form.append(`tb_ma_${index}`, code);
        form.append(`tb_ten_${index}`, name);
        form.append(`tb_sl_${index}`, qty);
    });

    console.log('FormData entries:');
    for (let pair of form.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    fetch(API, {
        method: 'POST',
        body: form
    }).then(r => r.json()).then(result => {
        console.log('API Response:', result);
        if (result.success) {
            alert(editId ? 'Cập nhật thành công' : 'Tạo thành công');
            closeModal();
            loadList();
        } else alert('Lỗi: ' + result.message);
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
        }).then(r => r.json()).then(data => {
            alert(data.success ? 'Xóa thành công' : 'Lỗi: ' + data.message);
            if (data.success) loadList();
        });
    }
}

function closeModal(modalId = 'modal') {
    document.getElementById(modalId).style.display = 'none';
}

document.addEventListener('DOMContentLoaded', loadList);
window.onclick = e => e.target.classList.contains('modal') && closeModal(e.target.id);
</script>

<!-- Thêm validation cho ngày tháng -->
<script src="../js/date-validation.js?v=<?php echo time(); ?>"></script>
<script>
// Force reload - thêm timestamp để tránh cache
console.log('🔄 Script loaded at:', new Date().toISOString());

// Thêm validation ngày tháng cho file cũ này - CHỈ ràng buộc ngày trả > ngày mượn
document.addEventListener('DOMContentLoaded', function() {
    const ngayMuonInput = document.getElementById('ngaymuon');
    const ngayTraInput = document.getElementById('ngaytra');

    if (ngayMuonInput && ngayTraInput) {
        const formatDateForInput = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        // Validation khi thay đổi ngày mượn
        ngayMuonInput.addEventListener('change', function() {
            const ngayMuon = new Date(this.value);
            const ngayTra = ngayTraInput.value ? new Date(ngayTraInput.value) : null;

            // Cập nhật min cho ngày trả (phải sau ngày mượn ít nhất 1 ngày)
            const minNgayTra = new Date(ngayMuon);
            minNgayTra.setDate(minNgayTra.getDate() + 1);
            ngayTraInput.min = formatDateForInput(minNgayTra);
            // Nếu ngày trả đã được chọn và nhỏ hơn hoặc bằng ngày mượn, reset ngày trả
            if (ngayTra && ngayTra <= ngayMuon) {
                alert('⚠️ Ngày trả phải sau ngày mượn!');
                const defaultNgayTra = new Date(ngayMuon);
                defaultNgayTra.setDate(defaultNgayTra.getDate() + 7);
                ngayTraInput.value = formatDateForInput(defaultNgayTra);
            }
            // Nếu chưa có ngày trả, tự động set = ngày mượn + 7 ngày
            if (!ngayTraInput.value) {
                const defaultNgayTra = new Date(ngayMuon);
                defaultNgayTra.setDate(defaultNgayTra.getDate() + 7);
                ngayTraInput.value = formatDateForInput(defaultNgayTra);
            }
        });

        // Validation khi thay đổi ngày trả
        ngayTraInput.addEventListener('change', function() {
            const ngayMuon = ngayMuonInput.value ? new Date(ngayMuonInput.value) : null;
            const ngayTra = new Date(this.value);
            // CHỈ kiểm tra ngày trả phải sau ngày mượn
            if (ngayMuon && ngayTra <= ngayMuon) {
                alert('⚠️ Ngày trả phải sau ngày mượn!');
                const defaultNgayTra = new Date(ngayMuon);
                defaultNgayTra.setDate(defaultNgayTra.getDate() + 7);
                this.value = formatDateForInput(defaultNgayTra);
            }
        });

        console.log('✅ Date validation đã được thiết lập - chỉ ràng buộc ngày trả > ngày mượn');
    }
});
</script>