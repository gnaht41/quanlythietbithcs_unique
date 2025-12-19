<section id="bao-cao-hu-hong" class="trang-an"
    <?php echo ($active_tab != 'bao-cao-hu-hong') ? 'style="display:none;"' : ''; ?>>
    <div class="header-section">
        <h2>Báo cáo hư hỏng</h2>
        <button id="btn-tao-bao-cao" class="btn btn-primary" style="z-index: 999; position: relative; pointer-events: auto;">
            Tạo báo cáo hư hỏng
        </button>
    </div>

    <!-- Bảng danh sách báo cáo hư hỏng -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã thiết bị</th>
                    <th>Tên thiết bị</th>
                    <th>Tình trạng</th>
                    <th>Ngày báo cáo</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="tbody-bao-cao">
                <!-- Dữ liệu sẽ được load bằng JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Loading indicator -->
    <div id="loading-bao-cao" class="loading-indicator" style="display: none;">
        <div class="spinner"></div>
        <p>Đang tải dữ liệu...</p>
    </div>
</section>

<!-- Test Script -->
<script>
// Tự động detect base URL
const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '');
const apiUrl = baseUrl + '/controllers/BaoCaoAPI.php';

console.log('Base URL:', baseUrl);
console.log('API URL:', apiUrl);

document.addEventListener('DOMContentLoaded', function() {
    console.log('Test: DOM loaded for bao-cao-hu-hong');
    
    const button = document.getElementById('btn-tao-bao-cao');
    console.log('Test: Button found:', button);
    
    if (button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Test: Button clicked directly!');
            
            // Hiển thị modal trực tiếp
            const modal = document.getElementById('modal-bao-cao');
            if (modal) {
                modal.style.display = 'flex';
                console.log('Test: Modal displayed');
                
                // Reset form
                const form = document.getElementById('form-bao-cao');
                if (form) {
                    form.reset();
                }
                
                // Set title
                const title = document.getElementById('modal-bao-cao-title');
                if (title) {
                    title.textContent = 'Tạo báo cáo hư hỏng';
                }
                
                // Load thiết bị đang mượn
                const container = document.getElementById('danh-sach-thiet-bi-dang-muon');
                if (container) {
                    container.innerHTML = '<p class="text-muted">Đang tải danh sách thiết bị...</p>';
                    
                    // Gọi API để lấy thiết bị đang mượn
                    fetch(apiUrl + '?action=lay-thiet-bi-dang-muon')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.length > 0) {
                                let html = '';
                                data.data.forEach((item, index) => {
                                    const ngayMuon = new Date(item.ngayMuon).toLocaleDateString('vi-VN');
                                    const ngayTraDuKien = new Date(item.ngayTraDuKien).toLocaleDateString('vi-VN');
                                    
                                    html += `
                                        <div class="equipment-item-selection" style="border: 1px solid #ddd; padding: 10px; margin: 5px 0; cursor: pointer;">
                                            <input type="radio" name="selectedEquipment" value="${item.maTB}" 
                                                   data-ma-phieu="${item.maPhieu}" data-ten-tb="${item.tenTB}" style="margin-right: 10px;">
                                            <div style="display: inline-block;">
                                                <strong>${item.tenTB}</strong><br>
                                                <small>Mã TB: ${item.maTB} | Số lượng: ${item.soLuong} ${item.donVi}</small><br>
                                                <small>Phiếu: ${item.maPhieu} | Mượn: ${ngayMuon} | Trả dự kiến: ${ngayTraDuKien}</small>
                                            </div>
                                        </div>
                                    `;
                                });
                                container.innerHTML = html;
                                
                                // Thêm event click cho các item
                                container.querySelectorAll('.equipment-item-selection').forEach(item => {
                                    item.addEventListener('click', function() {
                                        const radio = this.querySelector('input[type="radio"]');
                                        radio.checked = true;
                                    });
                                });
                            } else {
                                container.innerHTML = '<p class="text-muted">Không có thiết bị nào đang mượn</p>';
                            }
                        })
                        .catch(error => {
                            container.innerHTML = '<p class="text-muted">Lỗi kết nối: ' + error.message + '</p>';
                        });
                }
                
                // Load danh sách báo cáo hiện có
                loadDanhSachBaoCao();
                
            } else {
                console.log('Test: Modal not found');
            }
        });
        console.log('Test: Direct event listener added');
    }
    
    // Event listeners để đóng modal
    const closeBtn = document.getElementById('close-modal-bao-cao');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            const modal = document.getElementById('modal-bao-cao');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    }
    
    const cancelBtn = document.getElementById('btn-huy-bao-cao');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            const modal = document.getElementById('modal-bao-cao');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    }
    
    // Đóng modal khi click outside
    const modal = document.getElementById('modal-bao-cao');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
    
    // Xử lý submit form
    const form = document.getElementById('form-bao-cao');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Lấy thiết bị được chọn
            const selectedEquipment = document.querySelector('input[name="selectedEquipment"]:checked');
            if (!selectedEquipment) {
                alert('Vui lòng chọn thiết bị cần báo cáo');
                return;
            }
            
            // Lấy dữ liệu form
            const tinhTrang = document.getElementById('tinhTrang').value.trim();
            const noiDungBaoCao = document.getElementById('noiDungBaoCao').value.trim();
            
            if (!tinhTrang || !noiDungBaoCao) {
                alert('Vui lòng điền đầy đủ thông tin');
                return;
            }
            
            // Gửi dữ liệu
            const data = {
                maPhieu: selectedEquipment.dataset.maPhieu,
                maTB: selectedEquipment.value,
                tinhTrang: tinhTrang,
                noiDungBaoCao: noiDungBaoCao
            };
            
            console.log('Sending data:', data);
            
            fetch('../controllers/BaoCaoAPI.php?action=tao-bao-cao', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text(); // Lấy text trước để debug
            })
            .then(text => {
                console.log('Raw response:', text);
                
                try {
                    const result = JSON.parse(text);
                    if (result.success) {
                        alert('Tạo báo cáo thành công!');
                        modal.style.display = 'none';
                        form.reset();
                        loadDanhSachBaoCao();
                    } else {
                        alert('Lỗi: ' + result.message);
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    alert('Lỗi phản hồi từ server: ' + text.substring(0, 200));
                    
                    // Fallback: thử gửi bằng FormData
                    console.log('Trying fallback with FormData...');
                    const formData = new FormData();
                    formData.append('json_data', JSON.stringify(data));
                    
                    fetch('../controllers/BaoCaoAPI.php?action=tao-bao-cao', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(text => {
                        console.log('Fallback response:', text);
                        try {
                            const result = JSON.parse(text);
                            if (result.success) {
                                alert('Tạo báo cáo thành công (fallback)!');
                                modal.style.display = 'none';
                                form.reset();
                                loadDanhSachBaoCao();
                            } else {
                                alert('Lỗi fallback: ' + result.message);
                            }
                        } catch (e2) {
                            alert('Lỗi nghiêm trọng: ' + text.substring(0, 100));
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Lỗi kết nối: ' + error.message);
            });
        });
    }
    
    // Character count cho textarea
    const tinhTrangTextarea = document.getElementById('tinhTrang');
    const noiDungTextarea = document.getElementById('noiDungBaoCao');
    
    if (tinhTrangTextarea) {
        tinhTrangTextarea.addEventListener('input', function() {
            const charCount = this.nextElementSibling;
            if (charCount && charCount.classList.contains('char-count')) {
                charCount.textContent = `${this.value.length}/500 ký tự`;
            }
        });
    }
    
    if (noiDungTextarea) {
        noiDungTextarea.addEventListener('input', function() {
            const charCount = this.nextElementSibling;
            if (charCount && charCount.classList.contains('char-count')) {
                charCount.textContent = `${this.value.length}/1000 ký tự`;
            }
        });
    }
    
    // Function load danh sách báo cáo
    function loadDanhSachBaoCao() {
        const tbody = document.getElementById('tbody-bao-cao');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="7">Đang tải dữ liệu...</td></tr>';
            
            fetch('../controllers/BaoCaoAPI.php?action=danh-sach-bao-cao')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.data.length > 0) {
                            let html = '';
                            data.data.forEach((item, index) => {
                                const ngayBaoCao = new Date(item.ngayBaoCao).toLocaleDateString('vi-VN');
                                const trangThaiClass = item.trangThai === 'da-xu-ly' ? 'success' : 
                                                     item.trangThai === 'huy-bo' ? 'danger' : 'warning';
                                
                                html += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${item.maTB}</td>
                                        <td>${item.tenTB}</td>
                                        <td>${item.tinhTrang.substring(0, 50)}${item.tinhTrang.length > 50 ? '...' : ''}</td>
                                        <td>${ngayBaoCao}</td>
                                        <td><span class="badge badge-${trangThaiClass}">${item.trangThai}</span></td>
                                        <td>
                                            <button onclick="viewBaoCaoDetail(${item.maBaoCao})" class="btn btn-sm btn-view">
                                                Xem chi tiết
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            });
                            tbody.innerHTML = html;
                        } else {
                            tbody.innerHTML = '<tr><td colspan="7">Chưa có báo cáo nào</td></tr>';
                        }
                    } else {
                        tbody.innerHTML = '<tr><td colspan="7">Lỗi: ' + data.message + '</td></tr>';
                    }
                })
                .catch(error => {
                    tbody.innerHTML = '<tr><td colspan="7">Lỗi kết nối: ' + error.message + '</td></tr>';
                });
        }
    }
    
    // Function xem chi tiết báo cáo
    window.viewBaoCaoDetail = function(maBaoCao) {
        console.log('Xem chi tiết báo cáo:', maBaoCao);
        
        // Gọi API để lấy chi tiết
        fetch(apiUrl + '?action=chi-tiet-bao-cao&id=' + maBaoCao)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = data.data;
                    
                    // Điền dữ liệu vào modal
                    document.getElementById('detail-ma-bao-cao').textContent = item.maBaoCao || 'N/A';
                    document.getElementById('detail-ma-phieu-bao-cao').textContent = item.maPhieu || 'N/A';
                    document.getElementById('detail-thiet-bi-bao-cao').textContent = `${item.tenTB} (${item.maTB})` || 'N/A';
                    document.getElementById('detail-tinh-trang').textContent = item.tinhTrang || 'N/A';
                    document.getElementById('detail-noi-dung-bao-cao').textContent = item.noiDungBaoCao || 'N/A';
                    document.getElementById('detail-ngay-bao-cao').textContent = new Date(item.ngayBaoCao).toLocaleString('vi-VN') || 'N/A';
                    document.getElementById('detail-trang-thai-bao-cao').innerHTML = `<span class="status-badge status-${item.trangThai}">${item.trangThai}</span>`;
                    
                    // Hiển thị modal
                    document.getElementById('modal-chi-tiet-bao-cao').style.display = 'flex';
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                alert('Lỗi kết nối: ' + error.message);
            });
    };
    
    // Event listeners cho modal chi tiết
    const closeChiTietBtn = document.getElementById('close-modal-chi-tiet-bao-cao');
    if (closeChiTietBtn) {
        closeChiTietBtn.addEventListener('click', function() {
            document.getElementById('modal-chi-tiet-bao-cao').style.display = 'none';
        });
    }
    
    const dongChiTietBtn = document.getElementById('btn-dong-chi-tiet-bao-cao');
    if (dongChiTietBtn) {
        dongChiTietBtn.addEventListener('click', function() {
            document.getElementById('modal-chi-tiet-bao-cao').style.display = 'none';
        });
    }
    
    // Đóng modal chi tiết khi click outside
    const modalChiTiet = document.getElementById('modal-chi-tiet-bao-cao');
    if (modalChiTiet) {
        modalChiTiet.addEventListener('click', function(e) {
            if (e.target === modalChiTiet) {
                modalChiTiet.style.display = 'none';
            }
        });
    }
});
</script>

<!-- Modal tạo/sửa báo cáo hư hỏng -->
<div id="modal-bao-cao" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-bao-cao-title">Tạo báo cáo hư hỏng</h3>
            <span id="close-modal-bao-cao" class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="form-bao-cao">
                <div class="form-group">
                    <label>Thiết bị đang mượn:</label>
                    <div id="danh-sach-thiet-bi-dang-muon" class="equipment-selection">
                        <p class="text-muted">Đang tải danh sách thiết bị...</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="tinhTrang">Tình trạng hư hỏng:</label>
                    <textarea id="tinhTrang" name="tinhTrang" rows="3" maxlength="500" 
                              placeholder="Mô tả tình trạng hư hỏng của thiết bị..." required></textarea>
                    <div class="char-count">0/500 ký tự</div>
                </div>
                
                <div class="form-group">
                    <label for="noiDungBaoCao">Nội dung báo cáo:</label>
                    <textarea id="noiDungBaoCao" name="noiDungBaoCao" rows="4" maxlength="1000" 
                              placeholder="Mô tả chi tiết về sự cố, nguyên nhân có thể, đề xuất xử lý..." required></textarea>
                    <div class="char-count">0/1000 ký tự</div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Lưu báo cáo
                    </button>
                    <button type="button" id="btn-huy-bao-cao" class="btn btn-secondary">
                        Hủy bỏ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal chi tiết báo cáo -->
<div id="modal-chi-tiet-bao-cao" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Chi tiết báo cáo hư hỏng</h3>
            <span id="close-modal-chi-tiet-bao-cao" class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="detail-section">
                <div class="detail-row">
                    <label>Mã báo cáo:</label>
                    <span id="detail-ma-bao-cao"></span>
                </div>
                <div class="detail-row">
                    <label>Mã phiếu mượn:</label>
                    <span id="detail-ma-phieu-bao-cao"></span>
                </div>
                <div class="detail-row">
                    <label>Thiết bị:</label>
                    <span id="detail-thiet-bi-bao-cao"></span>
                </div>
                <div class="detail-row">
                    <label>Tình trạng:</label>
                    <span id="detail-tinh-trang"></span>
                </div>
                <div class="detail-row">
                    <label>Nội dung báo cáo:</label>
                    <span id="detail-noi-dung-bao-cao"></span>
                </div>
                <div class="detail-row">
                    <label>Ngày báo cáo:</label>
                    <span id="detail-ngay-bao-cao"></span>
                </div>
                <div class="detail-row">
                    <label>Trạng thái:</label>
                    <span id="detail-trang-thai-bao-cao"></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn-dong-chi-tiet-bao-cao" class="btn btn-secondary">
                Đóng
            </button>
        </div>
    </div>
</div>