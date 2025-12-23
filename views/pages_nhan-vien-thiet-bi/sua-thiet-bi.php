<?php
// ===============================
// FILE: views/sua-thiet-bi.php (ĐÃ SỬA LỖI UNDEFINED VARIABLE)
// ===============================

require_once __DIR__ . '/../../models/VV_QLThietBi.php';
require_once __DIR__ . '/../../models/QT_Log.php';

$thietBiModel = new ThietBiModel();
$logModel     = new Log();

// -------------------------------
// KHỞI ĐỘNG SESSION AN TOÀN
// -------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BẮT BUỘC ĐĂNG NHẬP
if (!isset($_SESSION['maND'])) {
    header("Location: dang-nhap.php"); // Thay bằng trang login thực tế
    exit();
}
$maNguoiDung = $_SESSION['maND'];

// -------------------------------
// KHỞI TẠO CÁC BIẾN CẦN THIẾT ĐỂ TRÁNH WARNING
// -------------------------------
$error_msg = '';   // ← QUAN TRỌNG: Phải có dòng này
$device    = null; // ← Và dòng này

// -------------------------------
// LẤY maTB TỪ POST HOẶC GET
// -------------------------------
$maTB = $_POST['maTB'] ?? $_GET['maTB'] ?? null;
$maTB = $maTB ? (int)$maTB : null;

// -------------------------------
// LẤY THÔNG TIN THIẾT BỊ
// -------------------------------
if ($maTB) {
    $device = $thietBiModel->getDeviceById($maTB);
    if (!$device) {
        $error_msg = "Không tìm thấy thiết bị với mã $maTB.";
    }
} else {
    $error_msg = "Thiếu mã thiết bị.";
}

// -------------------------------
// DANH SÁCH DỮ LIỆU HỖ TRỢ
// -------------------------------
$listMonHoc    = $thietBiModel->getMonHoc();
$listTinhTrang = ['Tốt', 'Hư nhẹ', 'Hư nặng', 'Đang sửa'];
$listLop       = ['6', '7', '8', '9'];

// -------------------------------
// XỬ LÝ FORM: CẬP NHẬT + GHI LOG
// -------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $device) {

    $tenTB          = trim($_POST['tenTB'] ?? '');
    $maMH           = $_POST['maMH'] ?? '';
    $donVi          = trim($_POST['donVi'] ?? '');
    $soLuongTong    = (int)($_POST['soLuongTong'] ?? 0);
    $soLuongKhaDung = (int)($_POST['soLuongKhaDung'] ?? 0);
    $lop            = $_POST['lop'] ?? null;
    $tinhTrang      = $_POST['tinhTrang'] ?? '';

    // VALIDATE
    if (
        $tenTB === '' ||
        $maMH === '' ||
        $soLuongTong < 0 ||
        $soLuongKhaDung < 0 ||
        $soLuongKhaDung > $soLuongTong ||
        $tinhTrang === ''
    ) {
        $error_msg = "Dữ liệu không hợp lệ. Vui lòng kiểm tra lại các trường bắt buộc.";
    } else {

        $result = $thietBiModel->updateDevice(
            $maTB,
            $tenTB,
            $maMH,
            $donVi,
            $lop,
            $soLuongTong,
            $soLuongKhaDung,
            $tinhTrang,
            false
        );

        if ($result) {
            // GHI LOG
            $logModel->ghiLog(
                $maNguoiDung,
                'UPDATE',
                'ThietBi',
                $maTB
            );

            echo "<script>
                alert('Cập nhật thiết bị \"$tenTB\" thành công!');
                window.location.href = '?tab=quan-ly-thiet-bi';
            </script>";
            exit;
        } else {
            $error_msg = "Lỗi khi cập nhật thiết bị.";
        }
    }
}
?>

<!-- =============================== -->
<!-- GIAO DIỆN -->
<!-- =============================== -->

<style>
/* ===============================
   SỬA THIẾT BỊ – UI MODERN
   Namespace: stb-
   =============================== */

#sua-thiet-bi {
    max-width: 820px;
    margin: 30px auto;
    background: linear-gradient(180deg,#ffffff,#f9fafb);
    padding: 35px 40px;
    border-radius: 18px;
    box-shadow: 0 20px 40px rgba(0,0,0,.08);
    font-family: 'Segoe UI', Tahoma, sans-serif;
}

/* TITLE */
#sua-thiet-bi h2 {
    text-align: center;
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 30px;
    position: relative;
}
#sua-thiet-bi h2::after {
    content: "";
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg,#2563eb,#1d4ed8);
    display: block;
    margin: 10px auto 0;
    border-radius: 4px;
}

/* ALERT */
#sua-thiet-bi .alert {
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
}
#sua-thiet-bi .alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border-left: 5px solid #dc2626;
}

/* FORM */
#sua-thiet-bi form {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
}

/* LABEL */
#sua-thiet-bi label {
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    margin-bottom: 6px;
    display: block;
}

/* INPUT + SELECT */
#sua-thiet-bi input,
#sua-thiet-bi select {
    width: 100%;
    padding: 11px 14px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    font-size: 14px;
    transition: all .25s ease;
    background: #fff;
}

#sua-thiet-bi input:focus,
#sua-thiet-bi select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.2);
}

/* TWO COLUMN ROW */
#sua-thiet-bi .row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

/* BUTTONS */
#sua-thiet-bi .btn-primary {
    background: linear-gradient(135deg,#2563eb,#1d4ed8);
    color: #fff;
    padding: 12px 28px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    box-shadow: 0 8px 18px rgba(37,99,235,.35);
    transition: all .25s ease;
}

#sua-thiet-bi .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(37,99,235,.45);
}

#sua-thiet-bi .btn-secondary {
    background: #9ca3af;
    color: #fff;
    padding: 12px 28px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    margin-left: 10px;
    transition: all .25s ease;
}

#sua-thiet-bi .btn-secondary:hover {
    background: #6b7280;
}

/* ACTION AREA */
#sua-thiet-bi .stb-actions {
    text-align: center;
    margin-top: 30px;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    #sua-thiet-bi {
        padding: 25px 20px;
    }
    #sua-thiet-bi .row {
        grid-template-columns: 1fr;
    }
}
</style>


<section id="sua-thiet-bi">
    <h2>Sửa thông tin thiết bị</h2>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <?php if ($device): ?>
        <form method="POST">
            <input type="hidden" name="maTB" value="<?= $maTB ?>">

            <div class="mb-2">
                <label>Tên thiết bị *</label>
                <input type="text" name="tenTB" required value="<?= htmlspecialchars($device['tenTB']) ?>">
            </div>

            <div class="mb-2">
                <label>Môn học *</label>
                <select name="maMH" required>
                    <option value="">-- Chọn môn --</option>
                    <?php foreach ($listMonHoc as $mh): ?>
                        <option value="<?= $mh['maMH'] ?>" <?= $device['maMH'] == $mh['maMH'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mh['tenMonHoc']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-2">
                <label>Đơn vị</label>
                <input type="text" name="donVi" value="<?= htmlspecialchars($device['donVi'] ?? '') ?>">
            </div>

            <div class="row">
                <div class="mb-2">
                    <label>Số lượng tổng *</label>
                    <input type="number" min="0" name="soLuongTong" required value="<?= $device['soLuongTong'] ?>">
                </div>
                <div class="mb-2">
                    <label>Số lượng khả dụng *</label>
                    <input type="number" min="0" name="soLuongKhaDung" required value="<?= $device['soLuongKhaDung'] ?>">
                </div>
            </div>

            <div class="mb-2">
                <label>Lớp</label>
                <select name="lop">
                    <option value="">-- Không áp dụng --</option>
                    <?php foreach ($listLop as $l): ?>
                        <option value="<?= $l ?>" <?= ($device['lop'] ?? '') == $l ? 'selected' : '' ?>>
                            Lớp <?= $l ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-2">
                <label>Tình trạng *</label>
                <select name="tinhTrang" required>
                    <option value="">-- Chọn --</option>
                    <?php foreach ($listTinhTrang as $tt): ?>
                        <option value="<?= $tt ?>" <?= $device['tinhTrang'] == $tt ? 'selected' : '' ?>>
                            <?= $tt ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="text-align:center; margin-top:25px">
                <button class="btn-primary">Lưu thay đổi</button>
                <a href="?tab=quan-ly-thiet-bi" class="btn-secondary">Hủy</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-danger">Không thể tải thông tin thiết bị để chỉnh sửa.</div>
    <?php endif; ?>
</section>