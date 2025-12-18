<?php
// ===============================
// FILE: views/sua-thiet-bi.php
// ===============================

require_once __DIR__ . '/../../models/VV_QLThietBi.php';
require_once __DIR__ . '/../../controllers/VV_QLThietBi.php';

$thietBiModel = new ThietBiModel();
$thietBiController = new ThietBiController();

// -------------------------------
// LẤY maTB (ƯU TIÊN POST, SAU ĐÓ GET)
// -------------------------------
$maTB = $_POST['maTB'] ?? $_GET['maTB'] ?? null;
$maTB = $maTB ? (int)$maTB : null;

$error_msg   = '';
$success_msg = '';
$device      = null;

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
// DANH SÁCH DỮ LIỆU PHỤ
// -------------------------------
$listMonHoc    = $thietBiModel->getMonHoc();
$listTinhTrang = ['Tốt', 'Hư nhẹ', 'Hư nặng', 'Đang sửa'];
$listLop       = ['6', '7', '8', '9'];

// -------------------------------
// XỬ LÝ SUBMIT FORM
// -------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $device) {

    $tenTB          = trim($_POST['tenTB'] ?? '');
    $maMH           = $_POST['maMH'] ?? '';
    $donVi          = trim($_POST['donVi'] ?? '');
    $soLuongTong    = (int)($_POST['soLuongTong'] ?? 0);
    $soLuongKhaDung = (int)($_POST['soLuongKhaDung'] ?? 0);
    $lop            = $_POST['lop'] ?? null;
    $tinhTrang      = $_POST['tinhTrang'] ?? '';

    // -------------------------------
    // VALIDATE
    // -------------------------------
    if (
        $tenTB === '' ||
        $maMH === '' ||
        $soLuongTong < 0 ||
        $soLuongKhaDung < 0 ||
        $soLuongKhaDung > $soLuongTong
    ) {
        $error_msg = "Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.";
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
            echo "<script>
                alert('Cập nhật thiết bị thành công!');
                window.location.href='?tab=quan-ly-thiet-bi';
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
section#sua-thiet-bi {
    padding: 30px;
    background: #fff;
    border-radius: 12px;
    max-width: 700px;
    margin: 30px auto;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
}
h2 { text-align:center; margin-bottom:25px }
label { font-weight:600 }
input, select {
    width:100%; padding:10px;
    border-radius:6px; border:1px solid #ddd;
}
.row { display:flex; gap:15px }
.row > div { flex:1 }
.mb-2 { margin-bottom:18px }
.btn-primary {
    background:#007bff; color:#fff;
    padding:10px 25px; border:none;
    border-radius:6px; cursor:pointer;
}
.btn-secondary {
    background:#6c757d; color:#fff;
    padding:10px 25px; border-radius:6px;
    text-decoration:none;
}
.alert { padding:12px; border-radius:6px; margin-bottom:15px }
.alert-danger { background:#f8d7da; color:#721c24 }
</style>

<section id="sua-thiet-bi">
    <h2>Sửa thông tin thiết bị</h2>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <?php if ($device): ?>
        <form method="POST" class="form-sua-thiet-bi">

            <!-- GIỮ maTB -->
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
                        <option value="<?= $mh['maMH'] ?>"
                            <?= $device['maMH'] == $mh['maMH'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mh['tenMonHoc']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-2">
                <label>Đơn vị</label>
                <input type="text" name="donVi" value="<?= htmlspecialchars($device['donVi']) ?>">
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
                        <option value="<?= $l ?>" <?= $device['lop'] == $l ? 'selected' : '' ?>>
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

            <div style="text-align:center;margin-top:25px">
                <button class="btn-primary">Lưu thay đổi</button>
                <a href="?tab=quan-ly-thiet-bi" class="btn-secondary">Hủy</a>
            </div>
        </form>
    <?php endif; ?>
</section>
