<section id="phieu-muon" class="trang-an" <?php echo ($active_tab != 'phieu-muon') ? 'style="display:none;"' : ''; ?>>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../controllers/VV_PhieuMuon.php';
$controller = new PhieuMuonController();

/* ======================
XỬ LÝ ACTION
====================== */
$action = $_GET['action'] ?? '';
$view   = $_GET['view'] ?? '';

if ($action === 'duyet' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->duyetNhieu();
}
if ($action === 'tuChoi' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->tuChoiNhieu();
}
if ($action === 'tra' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->traThietBi();
}
?>

<div class="container-phieu-muon">
    <h2 class="section-title">QUẢN LÝ PHIẾU MƯỢN</h2>

    <!-- Thông báo thành công / lỗi -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php
    /* ======================
    CHI TIẾT PHIẾU
    ====================== */
    if ($view === 'detail' && isset($_GET['maPhieu'])):
        $data = $controller->chiTiet();
        $phieu = $data['phieu'];
        $thietBi = $data['thietbi'];
    ?>

        <div class="card">
            <h3 class="card-title">Chi tiết phiếu mượn <?= $phieu['maPhieu'] ?></h3>

            <div class="info-grid">
                <div><strong>Người mượn:</strong> <?= htmlspecialchars($phieu['hoTen']) ?></div>
                <div><strong>Ngày mượn:</strong> <?= $phieu['ngayMuon'] ?></div>
                <div><strong>Ngày trả dự kiến:</strong> <?= $phieu['ngayTraDuKien'] ?></div>
                <div><strong>Trạng thái:</strong> 
                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $phieu['trangThai'])) ?>">
                        <?= $phieu['trangThai'] ?>
                    </span>
                </div>
            </div>
        
            <h4 class="mt-20">Danh sách thiết bị</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã TB</th>
                        <th>Thiết bị</th>
                        <th>Số lượng</th>
                        <th>Đơn vị</th>
                        <th>Tình trạng khi trả</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($tb = $thietBi->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= $tb['maTB'] ?></strong></td>
                            <td><?= $tb['tenTB'] ?></td>
                            <td><?= $tb['soLuong'] ?></td>
                            <td><?= $tb['donVi'] ?></td>
                            <td>
                                <?php
                                if ($phieu['trangThai'] !== 'Đã trả') {
                                    echo 'Chưa trả';
                                } else {
                                    echo $tb['tinhTrangKhiTra'];
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="actions mt-20">
                <a href="?tab=phieu-muon" class="btn btn-secondary">⬅ Quay lại danh sách</a>
            </div>
        </div>

    <?php
    /* ======================
    TRẢ THIẾT BỊ
    ====================== */
    elseif ($view === 'return' && isset($_GET['maPhieu'])):
        $data = $controller->chiTiet();
        $phieu = $data['phieu'];
        $thietBi = $data['thietbi'];
    ?>

        <div class="card">
            <h3 class="card-title">Trả thiết bị – Phiếu PM<?= $phieu['maPhieu'] ?></h3>

            <form method="post" action="?tab=phieu-muon&action=tra&view=return&maPhieu=<?= $phieu['maPhieu'] ?>" class="form-return">
                <input type="hidden" name="maPhieu" value="<?= $phieu['maPhieu'] ?>">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Thiết bị</th>
                            <th>Số lượng</th>
                            <th>Tình trạng khi trả</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($tb = $thietBi->fetch_assoc()): ?>
                            <tr>
                                <td><?= $tb['tenTB'] ?></td>
                                <td><?= $tb['soLuong'] ?></td>
                                <td>
                                    <select name="tinhTrang[<?= $tb['maTB'] ?>]" required class="form-select">
                                        <option value="">-- Chọn tình trạng --</option>
                                        <option value="Tốt">Tốt</option>
                                        <option value="Hư">Hư</option>
                                        <option value="Mất">Mất</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="actions mt-20">
                    <button type="submit" class="btn btn-primary">Xác nhận trả</button>
                    <a href="?tab=phieu-muon" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>

    <?php
    /* ======================
    DANH SÁCH PHIẾU
    ====================== */
    else:
        $data = $controller->index();
        $danhSach = $data['danhSach'];
    ?>

        <!-- Bộ lọc -->
        <div class="card mb-20">
            <form method="get" class="filter-form">
                <input type="hidden" name="tab" value="phieu-muon">

                <div class="filter-row">
                    <input type="text" name="keyword" placeholder="Tên người mượn" value="<?= $_GET['keyword'] ?? '' ?>" class="form-input">

                    <select name="trangThai" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <?php
                        $dsTrangThai = ['Chờ duyệt', 'Đang mượn', 'Đã trả', 'Từ chối'];
                        foreach ($dsTrangThai as $tt):
                        ?>
                            <option value="<?= $tt ?>" <?= (($_GET['trangThai'] ?? '') === $tt) ? 'selected' : '' ?>>
                                <?= $tt ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="date" name="tuNgay" value="<?= $_GET['tuNgay'] ?? '' ?>" class="form-input">
                    <input type="date" name="denNgay" value="<?= $_GET['denNgay'] ?? '' ?>" class="form-input">

                    <button type="submit" class="btn btn-primary">Lọc</button>
                    <a href="?tab=phieu-muon" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
                 <div class="card mb-20">
    <div class="actions">
        <button onclick="xuLy('duyet')" class="btn btn-success">Duyệt</button>
        <button onclick="xuLy('tuChoi')" class="btn btn-danger">Từ chối</button>
    </div>
</div>
           
        <!-- Bảng danh sách -->
        <div class="card">
            
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="chonTatCa">
                        </th>
                        <th>Mã</th>
                        <th>Người mượn</th>
                        <th>Ngày mượn</th>
                        <th>Ngày trả DK</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $danhSach->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <!-- ✅ Ô chọn phiếu -->
                            <input type="checkbox"
                            name="maPhieu[]"
                            value="<?= $row['maPhieu'] ?>"
                            data-trangthai="<?= $row['trangThai'] ?>">

                        </td>

                        <td><strong>P<?= $row['maPhieu'] ?></strong></td>
                        <td><?= htmlspecialchars($row['hoTen']) ?></td>
                        <td><?= $row['ngayMuon'] ?></td>
                        <td><?= $row['ngayTraDuKien'] ?></td>
                        <td><?= $row['trangThai'] ?></td>
                        <td>
                            <a href="?tab=phieu-muon&view=detail&maPhieu=<?= $row['maPhieu'] ?>"
                            class="btn btn-sm btn-info">Xem</a>

                            <?php if ($row['trangThai'] === 'Đã duyệt'): ?>
                                <a href="?tab=phieu-muon&view=return&maPhieu=<?= $row['maPhieu'] ?>"
                                class="btn btn-sm btn-warning">
                                    Ghi nhận trả
                                </a>
                            <?php endif; ?>
                        </td>

                    </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>
        </div>

    <?php endif; ?>
</div>

<!-- CSS đơn giản (có thể tách ra file riêng sau) -->
<style>
    .container-phieu-muon { max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif; }
    .section-title { text-align: center; margin-bottom: 30px; color: #333; }
    .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
    .card-title { margin-top: 0; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
    .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; }
    .alert-success { background: #007bff; color: #edf6efff; border: 1px solid #c3e6cb; }
    .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .table th, .table td { padding: 12px; text-align: left; border: 1px solid #ddd; }
    .table th { background: #5191d0ff; font-weight: bold; }
    .table-hover tbody tr:hover { background: #f1f8ff; }

    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px; margin: 20px 0; }
    .status-badge { padding: 5px 12px; border-radius: 20px; color: #333; font-size: 0.9em; font-weight: bold; }
    .status-chờ-duyệt { background: #ffc107; }
    .status-đang-mượn { background: #007bff; }
    .status-đã-trả { background: #28a745; }
    .status-từ-chối { background: #dc3545; }

    .filter-form .filter-row { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
    .form-input, .form-select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
    .form-select { min-width: 180px; }

    .btn { display: inline-block; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 0.95em; margin-right: 5px; }
    .btn-primary { background: #007bff; color: #fff; border: none; cursor: pointer; }
    .btn-success { background: #28a745; color: #fff; }
    .btn-danger { background: #dc3545; color: #fff; }
    .btn-warning { background: #ffc107; color: #212529; }
    .btn-info { background: #17a2b8; color: #fff; }
    .btn-secondary { background: #6c757d; color: #fff; }
    .btn-sm { padding: 5px 10px; font-size: 0.85em; }

    .actions { margin-top: 20px; }
    .mt-20 { margin-top: 20px; }
    .mb-20 { margin-bottom: 20px; }
    .text-center { text-align: center; }
</style>

</section>
<script>
    document.querySelectorAll('input[name="chonPhieu"]').forEach(radio => {
    radio.addEventListener('click', function () {
        // Nếu click lại chính nó → bỏ chọn
        if (this.checked && this.dataset.clicked === "true") {
            this.checked = false;
            this.dataset.clicked = "false";
        } else {
            // Reset trạng thái các radio khác
            document.querySelectorAll('input[name="chonPhieu"]').forEach(r => {
                r.dataset.clicked = "false";
            });
            this.dataset.clicked = "true";
        }
    });
});
function xuLy(action) {
    const chon = document.querySelectorAll('input[name="maPhieu[]"]:checked');
    if (chon.length === 0) return;

    const dsMaPhieu = [];

    chon.forEach(cb => {
        const tt = cb.dataset.trangthai;

        if (
            (action === 'duyet'  && tt === 'Chờ duyệt') ||
            (action === 'tuChoi' && tt === 'Chờ duyệt')
        ) {
            dsMaPhieu.push(cb.value);
        }
    });

    if (dsMaPhieu.length === 0) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `?tab=phieu-muon&action=${action}`;

    dsMaPhieu.forEach(ma => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'maPhieu[]';
        input.value = ma;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

// Checkbox chọn tất cả
const chkAll = document.getElementById('chonTatCa');

if (chkAll) {
    chkAll.addEventListener('change', function () {
        const tatCa = document.querySelectorAll('input[name="maPhieu[]"]');

        tatCa.forEach(cb => {
            cb.checked = chkAll.checked;
        });
    });
}

// Khi bỏ chọn từng dòng → tự động bỏ chọn "chọn tất cả"
document.querySelectorAll('input[name="maPhieu[]"]').forEach(cb => {
    cb.addEventListener('change', function () {
        if (!this.checked && chkAll) {
            chkAll.checked = false;
        }
    });
});

</script>
