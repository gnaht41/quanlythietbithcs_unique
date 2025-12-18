<?php
// File: views/them-thiet-bi.php (nên tách riêng để dễ quản lý)

require_once __DIR__ . '/../../models/VV_QLThietBi.php'; // tên file model của bạn

$thietBiModel = new ThietBiModel();

// --- Lấy danh sách môn học ---
$listMonHoc = $thietBiModel->getMonHoc(); // phải trả về mảng có maMH, tenMonHoc

// Danh sách cố định theo ENUM trong CSDL
$listTinhTrang = ['Tốt', 'Hư nhẹ', 'Hư nặng', 'Đang sửa'];
$listLop = ['6', '7', '8', '9']; // chỉ những giá trị này được phép

$error_msg = '';

// --- Xử lý thêm thiết bị mới ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenTB          = trim($_POST['tenTB'] ?? '');
    $maMH           = $_POST['maMH'] ?? '';
    $donVi          = trim($_POST['donVi'] ?? '') ?: null; // cho phép rỗng → null
    $soLuongTong    = max(0, intval($_POST['soLuongTong'] ?? 0));
    $soLuongKhaDung = max(0, intval($_POST['soLuongKhaDung'] ?? 0));
    $lop            = $_POST['lop'] ?? null; // nếu chọn "-- Không áp dụng --" → null
    $tinhTrang      = $_POST['tinhTrang'] ?? '';

    // Validate theo đúng ràng buộc CSDL
    if (empty($tenTB)) {
        $error_msg = "Tên thiết bị là bắt buộc.";
    } elseif (empty($maMH)) {
        $error_msg = "Vui lòng chọn môn học.";
    } elseif ($soLuongTong < 1) {
        $error_msg = "Số lượng tổng phải ít nhất là 1.";
    } elseif ($soLuongKhaDung > $soLuongTong) {
        $error_msg = "Số lượng khả dụng không được lớn hơn số lượng tổng.";
    } elseif (!empty($lop) && !in_array($lop, $listLop)) {
        $error_msg = "Lớp không hợp lệ.";
    } elseif (!in_array($tinhTrang, $listTinhTrang)) {
        $error_msg = "Tình trạng không hợp lệ.";
    } else {
        // Gọi method thêm trong model
        $result = $thietBiModel->addDevice(
            $tenTB,
            $maMH,
            $donVi,
            $soLuongTong,
            $soLuongKhaDung,
            $tinhTrang,
            $lop  // có thể là null → DB sẽ chấp nhận vì ENUM cho phép NULL nếu không NOT NULL
        );

        if ($result) {
            // Redirect về danh sách (giữ bộ lọc nếu có)
            $redirect = '?tab=quan-ly-thiet-bi';
            $filters = ['tenTB', 'maMH', 'tinhTrang', 'lop'];
            foreach ($filters as $f) {
                if (!empty($_GET[$f])) {
                    $redirect .= "&$f=" . urlencode($_GET[$f]);
                }
            }

            echo "<script>
                alert('Thêm thiết bị thành công!');
                window.location.href = '$redirect';
            </script>";
            exit;
        } else {
            $error_msg = "Lỗi khi thêm thiết bị vào cơ sở dữ liệu. Vui lòng thử lại.";
        }
    }
}
?>

<style>
section#them-thiet-bi {
    padding: 30px;
    background-color: #ffffff;
    border-radius: 12px;
    max-width: 700px;
    margin: 30px auto;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    font-family: Arial, Helvetica, sans-serif;
}

section#them-thiet-bi h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 26px;
    color: #333;
}

.form-them-thiet-bi label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #444;
}

.form-them-thiet-bi input,
.form-them-thiet-bi select {
    width: 100%;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #ddd;
    font-size: 15px;
    box-sizing: border-box;
}

.form-them-thiet-bi input:focus,
.form-them-thiet-bi select:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0,123,255,0.3);
}

.row {
    display: flex;
    gap: 20px;
}

.row .mb-2 {
    flex: 1;
}

.mb-2 {
    margin-bottom: 20px;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 11px 28px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    padding: 11px 28px;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 12px 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-size: 15px;
}

.text-center {
    text-align: center;
    margin-top: 30px;
}
</style>

<section id="them-thiet-bi">
    <h2>Thêm thiết bị mới</h2>

    <?php if (!empty($error_msg)): ?>
        <div class="alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <form action="?tab=them-thiet-bi" method="POST" class="form-them-thiet-bi">
        <div class="mb-2">
            <label>Tên thiết bị <span style="color:red;">*</span></label>
            <input type="text" name="tenTB" required placeholder="Ví dụ: Máy chiếu Epson EB-X06" value="<?php echo htmlspecialchars($_POST['tenTB'] ?? ''); ?>">
        </div>

        <div class="mb-2">
            <label>Môn học <span style="color:red;">*</span></label>
            <select name="maMH" required>
                <option value="">-- Chọn môn học --</option>
                <?php foreach ($listMonHoc as $mon): ?>
                    <option value="<?php echo $mon['maMH']; ?>" <?php echo ($_POST['maMH'] ?? '') == $mon['maMH'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($mon['tenMonHoc']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-2">
            <label>Đơn vị (Cái, Bộ, Chiếc...)</label>
            <input type="text" name="donVi" placeholder="Ví dụ: Cái" value="<?php echo htmlspecialchars($_POST['donVi'] ?? ''); ?>">
        </div>

        <div class="row">
            <div class="mb-2">
                <label>Số lượng tổng <span style="color:red;">*</span></label>
                <input type="number" name="soLuongTong" required min="1" value="<?php echo $_POST['soLuongTong'] ?? '1'; ?>">
            </div>
            <div class="mb-2">
                <label>Số lượng khả dụng <span style="color:red;">*</span></label>
                <input type="number" name="soLuongKhaDung" required min="0" value="<?php echo $_POST['soLuongKhaDung'] ?? '1'; ?>">
                <small style="color:#666; font-size:13px;">Thường bằng tổng khi mới nhập kho</small>
            </div>
        </div>

        <div class="mb-2">
            <label>Lớp áp dụng</label>
            <select name="lop">
                <option value="">-- Không áp dụng (dùng chung) --</option>
                <?php foreach ($listLop as $l): ?>
                    <option value="<?php echo $l; ?>" <?php echo ($_POST['lop'] ?? '') == $l ? 'selected' : ''; ?>>
                        Lớp <?php echo $l; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-2">
            <label>Tình trạng ban đầu <span style="color:red;">*</span></label>
            <select name="tinhTrang" required>
                <option value="">-- Chọn tình trạng --</option>
                <?php foreach ($listTinhTrang as $tt): ?>
                    <option value="<?php echo $tt; ?>" <?php echo ($tt === 'Tốt' || ($_POST['tinhTrang'] ?? '') == $tt) ? 'selected' : ''; ?>>
                        <?php echo $tt; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="btn-primary">Thêm thiết bị</button>
            <a href="?tab=quan-ly-thiet-bi" class="btn-secondary">Hủy bỏ</a>
        </div>
    </form>
</section>