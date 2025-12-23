<?php
// ===================================
// FILE: views/quan-ly-danh-muc.php
// ===================================
require_once __DIR__ . '/../../models/VV_QLThietBi.php';
require_once __DIR__ . '/../../controllers/VV_QLThietBi.php';

$thietBiModel = new ThietBiModel();
$thietBiController = new ThietBiController();

/* ==============================
   XỬ LÝ XÓA THIẾT BỊ
   ============================== */
if (isset($_GET['action'], $_GET['maTB']) && $_GET['action'] === 'xoa-thiet-bi') {
    $maTB = (int)$_GET['maTB'];
    $result = $thietBiController->deleteDevice($maTB);

    echo "<script>
        alert('" . ($result['success'] ? "Xóa thiết bị thành công!" : "Xóa thất bại: {$result['message']}") . "');
        window.location.href='?tab=quan-ly-thiet-bi';
    </script>";
    exit;
}

/* ==============================
   LẤY DỮ LIỆU
   ============================== */
$filters = [
    'tenTB'     => trim($_GET['tenTB'] ?? ''),
    'maMH'      => trim($_GET['maMH'] ?? ''),
    'tinhTrang' => trim($_GET['tinhTrang'] ?? ''),
    'lop'       => trim($_GET['lop'] ?? '')
];

$devices        = $thietBiController->searchDevices($filters)['data'] ?? [];
$listMonHoc     = $thietBiController->getMonHocList()['data'] ?? [];
$listTinhTrang  = ['Tốt','Hư nhẹ','Hư nặng','Đang sửa'];
$listLop        = ['6','7','8','9'];

$badgeMap = [
    'Tốt'       => 'qltb-good',
    'Hư nhẹ'    => 'qltb-warning',
    'Hư nặng'   => 'qltb-danger',
    'Đang sửa'  => 'qltb-repair'
];
?>

<style>
/* ===============================
   QUẢN LÝ THIẾT BỊ – UI MODERN
   Namespace: qltb-
   =============================== */
#qltb-manager {
    padding:30px;
    background:#f4f6f9;
    font-family:'Segoe UI',Tahoma,sans-serif;
}

/* HEADER */
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
.qltb-btn-add {
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    padding:10px 22px;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    box-shadow:0 4px 12px rgba(37,99,235,.35);
}

/* FILTER */
.qltb-filter {
    background:#fff;
    padding:18px;
    border-radius:14px;
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:12px;
    margin-bottom:25px;
    box-shadow:0 6px 20px rgba(0,0,0,.06);
}
.qltb-filter input,
.qltb-filter select {
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #d1d5db;
    width:100%;
}

/* ACTION FILTER */
.qltb-filter-actions {
    display:flex;
    gap:10px;
}
.qltb-filter-actions button {
    flex:1;
    background:#2563eb;
    color:#fff;
    border:none;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
}
.qltb-reset {
    flex:1;
    background:#9ca3af;
    color:#fff;
    border-radius:8px;
    text-decoration:none;
    text-align:center;
    padding:10px;
}

/* TABLE CARD */
.qltb-table-card {
    background:#fff;
    border-radius:16px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    overflow-x:auto;
}
.qltb-table-card table {
    width:100%;
    border-collapse:collapse;
}
.qltb-table-card thead {
    background:#1f2937;
    color:#fff;
}
.qltb-table-card th,
.qltb-table-card td {
    padding:14px;
    text-align:center;
    border-bottom:1px solid #e5e7eb;
}
.qltb-table-card tbody tr:hover {
    background:#f1f5f9;
}

/* BADGE */
.qltb-badge {
    padding:6px 12px;
    border-radius:999px;
    font-size:13px;
    font-weight:600;
}
.qltb-good    {background:#dcfce7;color:#166534;}
.qltb-warning {background:#fef3c7;color:#92400e;}
.qltb-danger  {background:#fee2e2;color:#991b1b;}
.qltb-repair  {background:#e0e7ff;color:#3730a3;}

/* ACTION BUTTON */
.qltb-action a {
    padding:6px 12px;
    border-radius:8px;
    font-size:13px;
    text-decoration:none;
    margin:0 3px;
}
.qltb-edit   {background:#f59e0b;color:#fff;}
.qltb-delete {background:#ef4444;color:#fff;}

/* EMPTY */
.qltb-empty {
    padding:30px;
    text-align:center;
    color:#6b7280;
}

/* RESPONSIVE */
@media (max-width: 992px) {
    .qltb-filter {
        grid-template-columns:repeat(2,1fr);
    }
    .qltb-filter-actions {
        grid-column:span 2;
    }
}
@media (max-width: 576px) {
    .qltb-filter {
        grid-template-columns:1fr;
    }
    .qltb-filter-actions {
        flex-direction:column;
    }
}
</style>

<section id="qltb-manager">

    <!-- HEADER -->
    <div class="qltb-header">
        <h2>Danh sách thiết bị</h2>
        <a href="?tab=them-thiet-bi" class="qltb-btn-add">+ Thêm thiết bị</a>
    </div>

    <!-- FILTER -->
    <form method="GET" class="qltb-filter">
        <input type="hidden" name="tab" value="quan-ly-thiet-bi">

        <input type="text" name="tenTB" placeholder="Tên thiết bị"
               value="<?= htmlspecialchars($filters['tenTB']) ?>">

        <select name="maMH">
            <option value="">-- Môn học --</option>
            <?php foreach ($listMonHoc as $mh): ?>
                <option value="<?= $mh['maMH'] ?>" <?= $filters['maMH']==$mh['maMH']?'selected':'' ?>>
                    <?= htmlspecialchars($mh['tenMonHoc']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="tinhTrang">
            <option value="">-- Tình trạng --</option>
            <?php foreach ($listTinhTrang as $tt): ?>
                <option <?= $filters['tinhTrang']==$tt?'selected':'' ?>><?= $tt ?></option>
            <?php endforeach; ?>
        </select>

        <select name="lop">
            <option value="">-- Lớp --</option>
            <?php foreach ($listLop as $l): ?>
                <option <?= $filters['lop']==$l?'selected':'' ?>>Lớp <?= $l ?></option>
            <?php endforeach; ?>
        </select>

        <div class="qltb-filter-actions">
            <button type="submit">Tìm kiếm</button>
            <a href="?tab=quan-ly-thiet-bi" class="qltb-reset">Xóa lọc</a>
        </div>
    </form>

    <!-- TABLE -->
    <div class="qltb-table-card">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên thiết bị</th>
                    <th>Môn học</th>
                    <th>Đơn vị</th>
                    <th>Số lượng</th>
                    <th>Lớp</th>
                    <th>Tình trạng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($devices): foreach ($devices as $i=>$d): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($d['tenTB']) ?></td>
                    <td><?= htmlspecialchars($d['tenMonHoc'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($d['donVi'] ?? '-') ?></td>
                    <td><b><?= $d['soLuongTong'] ?></b> / <?= $d['soLuongKhaDung'] ?></td>
                    <td><?= htmlspecialchars($d['lop'] ?? '-') ?></td>
                    <td>
                        <span class="qltb-badge <?= $badgeMap[$d['tinhTrang']] ?>">
                            <?= $d['tinhTrang'] ?>
                        </span>
                    </td>
                    <td class="qltb-action">
                        <a class="qltb-edit" href="?tab=sua-thiet-bi&maTB=<?= $d['maTB'] ?>">Sửa</a>
                        <a class="qltb-delete"
                           href="?tab=quan-ly-thiet-bi&action=xoa-thiet-bi&maTB=<?= $d['maTB'] ?>"
                           onclick="return confirm('Xóa thiết bị này?')">
                           Xóa
                        </a>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="8" class="qltb-empty">Không có thiết bị phù hợp</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
