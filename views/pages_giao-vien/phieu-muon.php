<section id="phieu-muon" class="trang-an"
    <?php echo ($active_tab != 'phieu-muon') ? 'style="display:none;"' : ''; ?>>
    <div class="header-section">
        <h2>Quản lý phiếu mượn</h2>
        <button id="btn-tao-phieu-muon" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo phiếu mượn
        </button>
    </div>

    <!-- Bộ lọc -->
    <div class="filter-section">
        <div class="filter-group">
            <label for="filter-trang-thai">Trạng thái:</label>
            <select id="filter-trang-thai">
                <option value="">Tất cả</option>
                <option value="cho-duyet">Chờ duyệt</option>
                <option value="da-duyet">Đã duyệt</option>
                <option value="da-huy">Đã hủy</option>
                <option value="tu-choi">Từ chối</option>
                <option value="dang-muon">Đang mượn</option>
                <option value="da-tra">Đã trả</option>
            </select>
        </div>
        <button id="btn-xoa-loc" class="btn btn-outline">Xóa bộ lọc</button>
    </div>

    <!-- Bảng danh sách phiếu mượn -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã phiếu</th>
                    <th>Thiết bị</th>
                    <th>Ngày mượn</th>
                    <th>Ngày trả dự kiến</th>
                    <th>Mục đích</th>
                    <th>Số lượng</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="tbody-phieu-muon">
                <!-- Dữ liệu sẽ được load bằng JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Loading indicator -->
    <div id="loading-phieu-muon" class="loading-indicator" style="display: none;">
        <div class="spinner"></div>
        <p>Đang tải dữ liệu...</p>
    </div>
</section>

<!-- Modal tạo/sửa phiếu mượn -->
<div id="modal-phieu-muon" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Tạo phiếu mượn</h3>
            <span id="close-modal" class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="form-phieu-muon">
                <div class="form-group">
                    <label for="ngayMuon">Ngày mượn:</label>
                    <input type="date" id="ngayMuon" name="ngayMuon" required>
                </div>
                
                <div class="form-group">
                    <label for="ngayTraDuKien">Ngày trả dự kiến:</label>
                    <input type="date" id="ngayTraDuKien" name="ngayTraDuKien" required>
                </div>
                
                <div class="form-group">
                    <label for="mucDich">Mục đích mượn:</label>
                    <div class="purpose-input-container">
                        <textarea id="mucDich" name="mucDich" rows="3" maxlength="500" 
                                  placeholder="Nhập mục đích mượn thiết bị..." required></textarea>
                        <div class="char-count">0/500 ký tự</div>
                        <div id="purpose-suggestions" class="purpose-suggestions"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Thiết bị mượn:</label>
                    <button type="button" id="btn-them-thiet-bi" class="btn btn-outline">
                        <i class="fas fa-plus"></i> Thêm thiết bị
                    </button>
                    <div id="danh-sach-thiet-bi-chon" class="selected-equipment">
                        <p class="text-muted">Chưa chọn thiết bị nào</p>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Lưu phiếu</button>
                    <button type="button" id="btn-huy" class="btn btn-secondary">Hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal chọn thiết bị -->
<div id="modal-chon-thiet-bi" class="modal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3>Chọn thiết bị mượn</h3>
            <span id="close-modal-thiet-bi" class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="search-section">
                <input type="text" id="search-thiet-bi" placeholder="Tìm kiếm thiết bị...">
                <div class="bulk-actions">
                    <button type="button" id="btn-chon-tat-ca" class="btn btn-outline">Chọn tất cả</button>
                    <button type="button" id="btn-bo-chon-tat-ca" class="btn btn-outline">Bỏ chọn tất cả</button>
                </div>
            </div>
            <div id="grid-thiet-bi" class="equipment-grid">
                <!-- Danh sách thiết bị sẽ được load bằng JavaScript -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn-xac-nhan-chon" class="btn btn-primary">Xác nhận</button>
            <button type="button" id="btn-dong-chon-thiet-bi" class="btn btn-secondary">Đóng</button>
        </div>
    </div>
</div>

<!-- Modal chi tiết phiếu mượn -->
<div id="modal-chi-tiet" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Chi tiết phiếu mượn</h3>
            <span id="close-modal-chi-tiet" class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="detail-section">
                <div class="detail-row">
                    <label>Mã phiếu:</label>
                    <span id="detail-ma-phieu"></span>
                </div>
                <div class="detail-row">
                    <label>Ngày mượn:</label>
                    <span id="detail-ngay-muon"></span>
                </div>
                <div class="detail-row">
                    <label>Ngày trả dự kiến:</label>
                    <span id="detail-ngay-tra"></span>
                </div>
                <div class="detail-row">
                    <label>Mục đích:</label>
                    <span id="detail-muc-dich"></span>
                </div>
                <div class="detail-row">
                    <label>Trạng thái:</label>
                    <span id="detail-trang-thai"></span>
                </div>
                <div class="detail-row">
                    <label>Thiết bị:</label>
                    <div id="detail-thiet-bi"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn-dong-chi-tiet" class="btn btn-secondary">Đóng</button>
        </div>
    </div>
</div>