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
/* ===============================
   THÊM THIẾT BỊ – UI MODERN
   Namespace: ttb-
   =============================== */

#them-thiet-bi {
    max-width: 850px;
    margin: 40px auto;
    padding: 40px 45px;
    background: linear-gradient(180deg,#ffffff,#f8fafc);
    border-radius: 20px;
    box-shadow: 0 25px 45px rgba(0,0,0,0.08);
    font-family: 'Segoe UI', Tahoma, sans-serif;
}

/* TITLE */
#them-thiet-bi h2 {
    text-align: center;
    font-size: 30px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 35px;
    position: relative;
}

#them-thiet-bi h2::after {
    content: "";
    width: 90px;
    height: 4px;
    background: linear-gradient(90deg,#22c55e,#16a34a);
    display: block;
    margin: 12px auto 0;
    border-radius: 4px;
}

/* ALERT */
#them-thiet-bi .alert-danger {
    background: #fee2e2;
    color: #991b1b;
    padding: 15px 18px;
    border-radius: 12px;
    border-left: 5px solid #dc2626;
    margin-bottom: 25px;
    font-size: 14px;
}

/* FORM */
.form-them-thiet-bi {
    display: grid;
    gap: 20px;
}

/* LABEL */
.form-them-thiet-bi label {
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    margin-bottom: 6px;
    display: block;
}

/* INPUT & SELECT */
.form-them-thiet-bi input,
.form-them-thiet-bi select {
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    font-size: 14px;
    background: #fff;
    transition: all .25s ease;
}

.form-them-thiet-bi input::placeholder {
    color: #9ca3af;
}

.form-them-thiet-bi input:focus,
.form-them-thiet-bi select:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.25);
}

/* ROW 2 CỘT */
.form-them-thiet-bi .row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
}

/* SMALL TEXT */
.form-them-thiet-bi small {
    display: block;
    margin-top: 6px;
    font-size: 12.5px;
    color: #6b7280;
}

/* BUTTON ZONE */
.form-them-thiet-bi .text-center {
    text-align: center;
    margin-top: 35px;
}

/* PRIMARY BUTTON */
.form-them-thiet-bi .btn-primary {
    background: linear-gradient(135deg,#22c55e,#16a34a);
    color: #fff;
    padding: 13px 32px;
    border-radius: 14px;
    border: none;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 10px 22px rgba(34,197,94,.4);
    transition: all .25s ease;
}

.form-them-thiet-bi .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(34,197,94,.5);
}

/* SECONDARY BUTTON */
.form-them-thiet-bi .btn-secondary {
    background: #9ca3af;
    color: #fff;
    padding: 13px 32px;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    margin-left: 12px;
    transition: all .25s ease;
}

.form-them-thiet-bi .btn-secondary:hover {
    background: #6b7280;
}

/* REQUIRED STAR */
.form-them-thiet-bi span {
    font-weight: bold;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    #them-thiet-bi {
        padding: 28px 22px;
    }
    .form-them-thiet-bi .row {
        grid-template-columns: 1fr;
    }
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