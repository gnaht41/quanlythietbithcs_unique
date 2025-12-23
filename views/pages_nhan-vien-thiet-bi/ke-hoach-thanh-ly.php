<?php
// views/ke-hoach-thanh-ly.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../controllers/VV_KeHoachThanhLy.php';

$controller = new KeHoachThanhLyController();

// Xử lý thông báo session
$message = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
$error   = null;

// Xử lý duyệt / từ chối / xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['capNhatTrangThai'])) {
        $result = $controller->capNhatTrangThai(
            $_POST['maTL'],
            $_POST['trangThai'],
            $_POST['ghiChu'] ?? null
        );
    } elseif (isset($_POST['xoaKeHoach'])) {
        $result = $controller->xoaKeHoach($_POST['maTL']);
    }

    if (isset($result)) {
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            header("Location: ?tab=ke-hoach-thanh-ly");
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Lấy dữ liệu
$action = $_GET['action'] ?? '';
$maTL   = $_GET['maTL'] ?? null;

// Badge cho trạng thái kế hoạch
$badgeMap = [
    'Chờ duyệt' => 'qltb-warning',
    'Đã duyệt'  => 'qltb-good',
    'Từ chối'   => 'qltb-danger'
];

// Badge riêng cho tình trạng thiết bị
$tinhTrangBadgeMap = [
    'Hư nhẹ'   => 'qltb-warning',
    'Hư nặng'   => 'qltb-danger',
    'Mất'      => 'qltb-danger',
    'Đang sửa' => 'qltb-repair'
];
?>

<style>
/* ===================================
   KẾ HOẠCH THANH LÝ – UI HIỆN ĐẠI
   Namespace: qltb-
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

.qltb-btn-add {
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    padding:10px 22px;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    box-shadow:0 4px 12px rgba(37,99,235,.35);
    transition:transform .2s;
}
.qltb-btn-add:hover {transform:translateY(-2px);}

/* CARD */
.qltb-card {
    background:#fff;
    border-radius:16px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    overflow:hidden;
    margin-bottom:25px;
}

/* TABLE CHUNG */
.qltb-table-card table,
.qltb-detail-table {
    width:100%;
    border-collapse:collapse;
}
.qltb-table-card thead,
.qltb-detail-table thead {
    background:#1f2937;
    color:#fff;
}
.qltb-table-card th,
.qltb-table-card td,
.qltb-detail-table th,
.qltb-detail-table td {
    padding:14px;
    text-align:center;
    border-bottom:1px solid #e5e7eb;
}
.qltb-table-card tbody tr:hover,
.qltb-detail-table tbody tr:hover {
    background:#f1f5f9;
}

/* BADGE - SỬA LẠI ĐỂ ĐẸP VÀ NỔI BẬT HƠN */
.qltb-badge {
    display: inline-block;
    padding: 8px 16px;           /* Tăng padding để badge rộng rãi hơn */
    border-radius: 999px;
    font-size: 14px;             /* Tăng font để dễ đọc */
    font-weight: 600;
    text-align: center;
    min-width: 90px;             /* Đảm bảo badge không bị co quá nhỏ */
}

/* MÀU SẮC */
.qltb-good    { background:#dcfce7; color:#166534; }  /* Xanh lá - Đã duyệt */
.qltb-warning { background:#fef3c7; color:#92400e; }  /* Vàng - Chờ duyệt */
.qltb-danger  { background:#fee2e2; color:#991b1b; }  /* Đỏ - Từ chối */
.qltb-repair  { background:#e0e7ff; color:#3730a3; }  /* Xanh dương nhạt - Đang sửa (dùng cho thiết bị) */
/* ACTION BUTTON */
.qltb-action a,
.qltb-action button {
    padding:8px 14px;
    border-radius:8px;
    font-size:13px;
    text-decoration:none;
    margin:0 4px;
    border:none;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:6px;
}
.qltb-view    {background:#3b82f6;color:#fff;}
.qltb-delete  {background:#ef4444;color:#fff;}
.qltb-approve {background:#16a34a;color:#fff; font-weight:600;}
.qltb-reject  {background:#dc2626;color:#fff;}

/* DETAIL GRID */
.qltb-detail-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(320px,1fr));
    gap:20px;
    margin-bottom:20px;
}
.qltb-info-item {
    background:#f8fafc;
    padding:16px;
    border-radius:12px;
    border-left:4px solid #2563eb;
}

/* EMPTY */
.qltb-empty {
    padding:60px;
    text-align:center;
    color:#6b7280;
    font-size:16px;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .qltb-header {flex-direction:column; align-items:flex-start; gap:15px;}
    .qltb-action a,
    .qltb-action button {
        display:block;
        width:100%;
        margin:6px 0;
        justify-content:center;
    }
    .qltb-detail-grid {grid-template-columns:1fr;}
    table {font-size:14px;}
}
</style>

<section id="qltb-manager">

    <div class="qltb-header">
        <h2>Kế hoạch Thanh lý Thiết bị</h2>
        <a href="?tab=lap-ke-hoach-thanh-ly" class="qltb-btn-add">+ Lập kế hoạch mới</a>
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

    <?php if ($action === 'chi-tiet' && $maTL): ?>
        <!-- ========================= CHI TIẾT KẾ HOẠCH ========================= -->
        <?php
        $res = $controller->getChiTiet($maTL);
        if (!$res['success']) {
            echo '<div class="alert alert-danger">'.htmlspecialchars($res['message']).'</div>';
            echo '<a href="?tab=ke-hoach-thanh-ly" class="btn btn-secondary">← Quay lại</a>';
        } else {
            $kh = $res['data'];
        ?>
            <div class="qltb-card">
                <div class="card-header" style="background:#1f2937;color:#fff;padding:18px 24px;">
                    <h4 style="margin:0;">Chi tiết Kế hoạch Thanh lý - <strong>TL<?= sprintf("%04d", $kh['maTL']) ?></strong></h4>
                </div>
                <div class="card-body" style="padding:30px;">
                    <div class="qltb-detail-grid">
                        <div class="qltb-info-item">
                            <strong>Ngày lập:</strong> <?= date('d/m/Y', strtotime($kh['ngayLap'])) ?><br>
                            <strong>Người lập:</strong> <?= htmlspecialchars($kh['nguoiLap'] ?? 'N/A') ?><br>
                            <strong>Phương pháp thanh lý:</strong> <?= htmlspecialchars($kh['phuongPhapThanhLy']) ?>
                        </div>
                        <div class="qltb-info-item">
                            <strong>Trạng thái:</strong>
                            <span class="qltb-badge <?= $badgeMap[$kh['trangThai']] ?>">
                                <?= htmlspecialchars($kh['trangThai']) ?>
                            </span><br>
                            <?php if ($kh['ngayDuyet']): ?>
                                <strong>Ngày duyệt:</strong> <?= date('d/m/Y', strtotime($kh['ngayDuyet'])) ?><br>
                                <strong>Người duyệt:</strong> <?= htmlspecialchars($kh['nguoiDuyet'] ?? 'N/A') ?>
                            <?php endif; ?>
                            <?php if ($kh['ngayHoanThanh']): ?>
                                <br><strong>Ngày hoàn thành:</strong> <?= date('d/m/Y', strtotime($kh['ngayHoanThanh'])) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($kh['ghiChu']): ?>
                        <div style="margin:25px 0;">
                            <strong>Ghi chú:</strong>
                            <div style="background:#f3f4f6;padding:14px;border-radius:10px;margin-top:8px;">
                                <?= nl2br(htmlspecialchars($kh['ghiChu'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <h5 style="margin:35px 0 15px; color:#1f2937;">Danh sách thiết bị thanh lý (<?= count($kh['chiTiet']) ?> mục)</h5>
                    <div class="table-responsive">
                        <table class="qltb-detail-table">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên thiết bị</th>
                                    <th>Môn học</th>
                                    <th>Số lượng</th>
                                    <th>Đơn vị</th>
                                    <th>Tình trạng</th>
                                    <th>Lý do</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kh['chiTiet'] as $i => $ct): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($ct['tenTB']) ?></td>
                                        <td><?= htmlspecialchars($ct['tenMonHoc'] ?? '-') ?></td>
                                        <td><strong><?= $ct['soLuong'] ?></strong></td>
                                        <td><?= htmlspecialchars($ct['donVi']) ?></td>
                                        <td>
                                            <span class="qltb-badge <?= $tinhTrangBadgeMap[$ct['tinhTrang']] ?? 'qltb-warning' ?>">
                                                <?= htmlspecialchars($ct['tinhTrang']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($ct['lyDo']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- NÚT HÀNH ĐỘNG -->
                    <div style="margin-top:40px; padding-top:20px; border-top:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
                        <div>
                            <a href="?tab=ke-hoach-thanh-ly" class="btn btn-secondary" style="padding:10px 20px; border-radius:10px; text-decoration:none;">
                                ← Quay lại danh sách
                            </a>

                            <?php if ($kh['trangThai'] === 'Chờ duyệt' && in_array($_SESSION['maVT'] ?? 0, [3, 4])): ?>
                                <form method="POST" style="display:inline; margin-left:12px;" onsubmit="return confirm('Xóa kế hoạch này?')">
                                    <input type="hidden" name="maTL" value="<?= $kh['maTL'] ?>">
                                    <button type="submit" name="xoaKeHoach" class="qltb-delete">
                                        <i class="fas fa-trash-alt"></i> Xóa kế hoạch
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                                
                       
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php else: ?>
        <!-- ========================= DANH SÁCH KẾ HOẠCH ========================= -->
        <?php
        $res = $controller->getDanhSach();
        $danhSach = $res['success'] ? $res['data'] : [];
        ?>
        <div class="qltb-card qltb-table-card">
            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã kế hoạch</th>
                                <th>Ngày lập</th>
                                <th>Người lập</th>
                                <th>Phương pháp</th>
                                <th>Số TB</th>
                                <th>Trạng thái</th>
                                <th>Người duyệt</th>
                                <th>Ngày hoàn thành</th> <!-- CỘT MỚI -->
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($danhSach)): ?>
                                <tr>
                                    <td colspan="9" class="qltb-empty"> <!-- colspan tăng lên 9 -->
                                        Chưa có kế hoạch thanh lý nào.
                                    </td>
                                </tr>
                            <?php else: foreach ($danhSach as $kh): ?>
                                <tr>
                                    <td><strong>TL<?= sprintf("%04d", $kh['maTL']) ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($kh['ngayLap'])) ?></td>
                                    <td><?= htmlspecialchars($kh['nguoiLap'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($kh['phuongPhapThanhLy']) ?></td>
                                    <td><strong><?= $kh['soThietBi'] ?></strong></td>
                                    <td>
                                        <span class="qltb-badge <?= $badgeMap[$kh['trangThai']] ?>">
                                            <?= htmlspecialchars($kh['trangThai']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($kh['nguoiDuyet'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($kh['ngayHoanThanh']): ?>
                                            <?= date('d/m/Y', strtotime($kh['ngayHoanThanh'])) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="qltb-action">
                                        <div style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap;">
                                            <a href="?tab=ke-hoach-thanh-ly&action=chi-tiet&maTL=<?= $kh['maTL'] ?>" 
                                               class="qltb-view" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>

                                            <?php if ($kh['trangThai'] === 'Chờ duyệt' && in_array($_SESSION['maVT'] ?? 0, [3, 4])): ?>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Xóa kế hoạch này?')">
                                                    <input type="hidden" name="maTL" value="<?= $kh['maTL'] ?>">
                                                    <button type="submit" name="xoaKeHoach" class="qltb-delete">
                                                        <i class="fas fa-trash-alt"></i> Xóa
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>