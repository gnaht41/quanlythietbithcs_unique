<?php
// views/lap-ke-hoach-thanh-ly.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['maND'])) {
    header("Location: login.php"); // thay bằng trang login thực tế
    exit;
}

require_once __DIR__ . '/../../controllers/VV_KeHoachThanhLy.php';

$controller = new KeHoachThanhLyController();

// === SỬA: KIỂM TRA QUYỀN ĐÚNG CÁCH ===
$permissionCheck = checkPermission([3, 5]); // Hàm có sẵn trong controller file
if (!$permissionCheck['success']) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($permissionCheck['message']) . '</div>';
    exit;
}

// Lấy danh sách thiết bị
$resThietBi = $controller->getThietBiCanThanhLy();
$thietBiList = $resThietBi['success'] ? $resThietBi['data'] : [];

// Xử lý submit
$message = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
$error   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->lapKeHoach($_POST);
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        header("Location: ?tab=ke-hoach-thanh-ly");
        exit;
    } else {
        $error = $result['message'];
    }
}
?>

<style>
/* ===================================
   LẬP KẾ HOẠCH THANH LÝ – UI HIỆN ĐẠI
   Namespace: qltb- (đồng bộ toàn hệ thống)
   =================================== */
#qltb-manager {
    padding:30px;
    background:#f4f6f9;
    font-family:'Segoe UI',Tahoma,sans-serif;
    min-height:100vh;
}

#qltb-manager .qltb-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

#qltb-manager h2 {
    font-size:26px;
    font-weight:700;
    color:#1f2937;
}

/* CARD */
.qltb-card {
    background:#fff;
    border-radius:16px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    overflow:hidden;
}

/* FORM NHÓM */
.qltb-form-group {
    margin-bottom:20px;
}
.qltb-form-group label {
    display:block;
    margin-bottom:8px;
    font-weight:600;
    color:#374151;
}
.qltb-form-group input,
.qltb-form-group select,
.qltb-form-group textarea {
    width:100%;
    padding:12px 14px;
    border:1px solid #d1d5db;
    border-radius:10px;
    font-size:15px;
}
.qltb-form-group textarea {
    min-height:100px;
    resize:vertical;
}

/* BẢNG THIẾT BỊ */
.qltb-device-table {
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}
.qltb-device-table th,
.qltb-device-table td {
    padding:12px;
    text-align:left;
    border-bottom:1px solid #e5e7eb;
}
.qltb-device-table th {
    background:#f8fafc;
    font-weight:600;
}
.qltb-device-table input[type="number"],
.qltb-device-table input[type="text"],
.qltb-device-table select {
    width:100%;
    padding:8px;
    border:1px solid #cbd5e1;
    border-radius:6px;
}

/* BADGE */
.qltb-badge {
    padding:5px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
}
.qltb-good    {background:#dcfce7;color:#166534;}
.qltb-warning {background:#fef3c7;color:#92400e;}
.qltb-danger  {background:#fee2e2;color:#991b1b;}
.qltb-repair  {background:#e0e7ff;color:#3730a3;}

/* BUTTON */
.qltb-btn-primary {
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    padding:12px 30px;
    border:none;
    border-radius:10px;
    font-weight:600;
    font-size:16px;
    cursor:pointer;
    box-shadow:0 4px 12px rgba(37,99,235,.35);
}
.qltb-btn-secondary {
    background:#6b7280;
    color:#fff;
    padding:12px 24px;
    border:none;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .qltb-header {
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }
    .qltb-device-table {
        font-size:14px;
    }
    .qltb-device-table th,
    .qltb-device-table td {
        padding:8px;
    }
}
</style>

<section id="qltb-manager">

    <div class="qltb-header">
        <h2>Lập Kế hoạch Thanh lý Thiết bị</h2>
        <a href="?tab=ke-hoach-thanh-ly" class="qltb-btn-secondary">← Quay lại danh sách</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" style="border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.05);">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius:12px;">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="qltb-card">
        <div class="card-body" style="padding:30px;">
            <form method="POST" id="formLapKeHoach">

                <!-- THÔNG TIN CHUNG -->
                <div class="row">
                    <div class="col-md-4 qltb-form-group">
                        <label>Ngày lập</label>
                        <input type="date" name="ngayLap" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4 qltb-form-group">
                        <label>Phương pháp thanh lý</label>
                        <select name="phuongPhapThanhLy" required>
                            <option value="Bán phế liệu">Bán phế liệu</option>
                            <option value="Chuyển giao">Chuyển giao</option>
                            <option value="Tiêu hủy">Tiêu hủy</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                    <div class="col-md-4 qltb-form-group">
                        <label>Ghi chú (tùy chọn)</label>
                        <textarea name="ghiChu" placeholder="Ghi chú thêm về kế hoạch..."></textarea>
                    </div>
                </div>

                <hr style="margin:30px 0; border-color:#e5e7eb;">

                <!-- DANH SÁCH THIẾT BỊ THANH LÝ -->
                <h4 style="margin-bottom:20px; color:#1f2937;">Chọn thiết bị cần thanh lý</h4>

                <?php if (empty($thietBiList)): ?>
                    <div class="qltb-empty" style="padding:40px; text-align:center;">
                        Hiện tại không có thiết bị nào đủ điều kiện thanh lý (hư hoặc đang sửa và còn khả dụng).
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="qltb-device-table">
                            <thead>
                                <tr>
                                    <th width="25%">Tên thiết bị</th>
                                    <th width="15%">Môn học</th>
                                    <th width="10%">Số lượng</th>
                                    <th width="10%">Đơn vị</th>
                                    <th width="12%">Tình trạng</th>
                                    <th width="10%">Số lượng</th>
                                    <th width="18%">Lý do thanh lý</th>
                                    <th width="10%">Chọn</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($thietBiList as $tb): ?>
                                    <?php
                                    $badgeClass = match($tb['tinhTrang']) {
                                        'Hư nhẹ'   => 'qltb-warning',
                                        'Hư nặng'   => 'qltb-danger',
                                        'Đang sửa' => 'qltb-repair',
                                        default    => 'qltb-warning'
                                    };
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($tb['tenTB']) ?></td>
                                        <td><?= htmlspecialchars($tb['tenMonHoc'] ?? '-') ?></td>
                                        <td><strong><?= $tb['soLuongCanThanhLy'] ?></strong></td>
                                        <td><?= htmlspecialchars($tb['donVi']) ?></td>
                                        <td><span class="qltb-badge <?= $badgeClass ?>"><?= $tb['tinhTrang'] ?></span></td>
                                        <td>
                                            <input type="number" name="soLuong[<?= $tb['maTB'] ?>]" min="1" max="<?= $tb['soLuongKhaDung'] ?>" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="text" name="lyDo[<?= $tb['maTB'] ?>]" placeholder="Nhập lý do...">
                                            <input type="hidden" name="tinhTrang[<?= $tb['maTB'] ?>]" value="<?= $tb['tinhTrang'] ?>">
                                        </td>
                                        <td style="text-align:center;">
                                            <input type="checkbox" name="maTB[]" value="<?= $tb['maTB'] ?>" onchange="toggleRow(this)">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top:25px; text-align:right;">
                        <button type="submit" class="qltb-btn-primary" onclick="return validateForm()">
                            <i class="fas fa-save"></i> Lưu Kế hoạch Thanh lý
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>

<script>
function toggleRow(checkbox) {
    const row = checkbox.closest('tr');
    const inputs = row.querySelectorAll('input[type="number"], input[type="text"]');
    if (checkbox.checked) {
        row.style.backgroundColor = '#fefce8';
        inputs[0]?.focus();
    } else {
        row.style.backgroundColor = '';
        inputs.forEach(i => i.value = '');
    }
}

function validateForm() {
    const errorDiv = document.getElementById('formError');
    errorDiv.classList.add('d-none');
    errorDiv.innerHTML = '';

    const checkboxes = document.querySelectorAll('input[name="maTB[]"]:checked');
    if (checkboxes.length === 0) {
        errorDiv.innerHTML = 'Vui lòng chọn ít nhất một thiết bị để thanh lý.';
        errorDiv.classList.remove('d-none');
        return false;
    }

    let errors = [];
    checkboxes.forEach(cb => {
        const row = cb.closest('tr');
        const tenTB = row.querySelector('td:nth-child(1)').textContent.trim();
        const soLuongInput = row.querySelector('input[type="number"]');
        const lyDoInput = row.querySelector('input[type="text"]');

        const soLuong = soLuongInput.value.trim();
        const lyDo = lyDoInput.value.trim();

        if (!soLuong || parseInt(soLuong) <= 0) {
            errors.push(`<strong>${tenTB}</strong>: Vui lòng nhập số lượng hợp lệ (lớn hơn 0).`);
        }
        if (!lyDo) {
            errors.push(`<strong>${tenTB}</strong>: Vui lòng nhập lý do thanh lý.`);
        }
    });

    if (errors.length > 0) {
        errorDiv.innerHTML = '<ul style="margin:10px 0; padding-left:20px;">' + 
            errors.map(e => '<li>' + e + '</li>').join('') + '</ul>';
        errorDiv.classList.remove('d-none');
        return false;
    }

    return true;
}
</script>