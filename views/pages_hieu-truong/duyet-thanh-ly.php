<?php
// views/duyet-thanh-ly.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SỬA: Dùng JavaScript redirect thay header()
if (!isset($_SESSION['maND']) || $_SESSION['maVT'] != 5) {
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

require_once __DIR__ . '/../../controllers/VV_KeHoachThanhLy.php';

$controller = new KeHoachThanhLyController();

// Xử lý thông báo
$message = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
$error   = null;

// Xử lý duyệt / từ chối
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['capNhatTrangThai'])) {
    $result = $controller->capNhatTrangThai(
        $_POST['maTL'],
        $_POST['trangThai'],
        $_POST['ghiChu'] ?? null
    );

    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        echo '<script>window.location.href = "?tab=duyet-thanh-ly";</script>';
        exit;
    } else {
        $error = $result['message'];
    }
}

// ... phần còn lại giữ nguyên như bạn đã có
// Lấy dữ liệu
$action = $_GET['action'] ?? '';
$maTL   = $_GET['maTL'] ?? null;

// Badge trạng thái kế hoạch
$badgeMap = [
    'Chờ duyệt' => 'qltb-warning',
    'Đã duyệt'  => 'qltb-good',
    'Từ chối'   => 'qltb-danger'
];

// Badge tình trạng thiết bị
$tinhTrangBadgeMap = [
    'Hư nhẹ'   => 'qltb-warning',
    'Hư nặng'   => 'qltb-danger',
    'Đang sửa' => 'qltb-repair'
];
?>

<style>
/* ĐỒNG BỘ HOÀN TOÀN VỚI HỆ THỐNG qltb- */
#qltb-manager {
    padding:30px;
    background:#f4f6f9;
    font-family:'Segoe UI',Tahoma,sans-serif;
    min-height:100vh;
}

.qltb-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.qltb-header h2 {
    font-size:26px;
    font-weight:700;
    color:#1f2937;
    margin:0;
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

/* CARD & TABLE */
.qltb-card {
    background:#fff;
    border-radius:16px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    overflow:hidden;
    margin-bottom:25px;
}

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
.qltb-device-table tbody tr:hover {
    background:#f1f5f9;
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
.qltb-reject {
    background:#dc2626;
    color:#fff;
    padding:14px 30px;
    border:none;
    border-radius:10px;
    font-weight:600;
    font-size:16px;
    cursor:pointer;
}

/* DETAIL */
.qltb-detail-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(300px,1fr));
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
    .qltb-header {flex-direction:column; gap:15px;}
    .qltb-detail-grid {grid-template-columns:1fr;}
}
</style>

<section id="duyet-thanh-ly" class="trang-an" <?php echo ($active_tab != 'duyet-thanh-ly') ? 'style="display:none;"' : ''; ?>>

    <div class="qltb-header">
        <h2>Duyệt Kế Hoạch Thanh Lý Thiết Bị</h2>
        <a href="?tab=duyet-thanh-ly" class="qltb-btn-secondary">← Quay lại danh sách chung</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" style="border-radius:12px; margin-bottom:25px;">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius:12px; margin-bottom:25px;">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($action === 'chi-tiet' && $maTL): ?>
        <!-- CHI TIẾT KẾ HOẠCH -->
        <?php
        $res = $controller->getChiTiet($maTL);
        if (!$res['success'] || $res['data']['trangThai'] !== 'Chờ duyệt') {
            echo '<div class="alert alert-warning" style="padding:20px; border-radius:12px;">Kế hoạch này không tồn tại hoặc không ở trạng thái chờ duyệt.</div>';
        } else {
            $kh = $res['data'];
        ?>
            <div class="qltb-card">
                <div class="card-header" style="background:#1f2937;color:#fff;padding:18px 24px;">
                    <h4 style="margin:0;">Chi tiết Kế Hoạch Thanh Lý - <strong>TL<?= sprintf("%04d", $kh['maTL']) ?></strong></h4>
                </div>
                <div class="card-body" style="padding:30px;">
                    <div class="qltb-detail-grid">
                        <div class="qltb-info-item">
                            <strong>Ngày lập:</strong> <?= date('d/m/Y', strtotime($kh['ngayLap'])) ?><br>
                            <strong>Người lập:</strong> <?= htmlspecialchars($kh['nguoiLap'] ?? 'N/A') ?><br>
                            <strong>Phương pháp thanh lý:</strong> <?= htmlspecialchars($kh['phuongPhapThanhLy']) ?>
                        </div>
                        <div class="qltb-info-item">
                            <strong>Số lượng thiết bị:</strong> <strong><?= count($kh['chiTiet']) ?></strong> mục<br>
                            <strong>Trạng thái:</strong> <span class="qltb-badge qltb-warning">Chờ duyệt</span>
                        </div>
                    </div>

                    <?php if ($kh['ghiChu']): ?>
                        <div style="margin:25px 0;">
                            <strong>Ghi chú của người lập:</strong>
                            <div style="background:#f3f4f6;padding:14px;border-radius:10px;margin-top:8px;">
                                <?= nl2br(htmlspecialchars($kh['ghiChu'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <h5 style="margin:35px 0 15px; color:#1f2937;">Danh sách thiết bị đề nghị thanh lý</h5>
                    <div class="table-responsive">
                        <table class="qltb-device-table">
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

                    <!-- NÚT DUYỆT / TỪ CHỐI -->
                    <div style="margin-top:50px; padding:30px; background:#f8fafc; border-radius:12px; text-align:center;">
                        <h3 style="margin-bottom:25px; color:#1f2937;">Hiệu trưởng quyết định</h3>
                        <div style="display:flex; justify-content:center; gap:30px; flex-wrap:wrap; align-items:end;">
                            <form method="POST">
                                <input type="hidden" name="maTL" value="<?= $kh['maTL'] ?>">
                                <input type="hidden" name="trangThai" value="Từ chối">
                                <div style="margin-bottom:15px;">
                                    <textarea name="ghiChu" class="form-control" rows="3" placeholder="Lý do từ chối (tùy chọn)" style="width:380px; max-width:100%;"></textarea>
                                </div>
                                <button type="submit" name="capNhatTrangThai" class="qltb-reject"
                                        onclick="return confirm('Xác nhận TỪ CHỐI kế hoạch này?')">
                                    Từ chối kế hoạch
                                </button>
                            </form>

                            <form method="POST">
                                <input type="hidden" name="maTL" value="<?= $kh['maTL'] ?>">
                                <input type="hidden" name="trangThai" value="Đã duyệt">
                                <button type="submit" name="capNhatTrangThai" class="qltb-btn-primary"
                                        onclick="return confirm('Xác nhận DUYỆT kế hoạch?\nThiết bị sẽ bị trừ khỏi kho ngay lập tức.')">
                                    Duyệt kế hoạch
                                </button>
                            </form>
                        </div>
                                            <!-- NÚT IN PHIẾU THANH LÝ -->
                    <div style="margin-top:50px; text-align:center;">
                        <button onclick="printPhieu()" class="qltb-btn-primary" style="padding:14px 40px; font-size:16px;">
                            <i class="fas fa-print"></i> In phiếu thanh lý
                        </button>
                        <p style="margin-top:12px; font-size:14px; color:#6b7280;">
                            Phiếu sẽ được định dạng đẹp để in ra giấy A4
                        </p>
                    </div>

                    <!-- PHIẾU IN - ẨN TRÊN MÀN HÌNH, CHỈ HIỆN KHI IN -->
                    <div id="print-phieu" style="display:none;">
                        <style media="print">
                            @page { size: A4; margin: 15mm; }
                            body { font-family: 'Times New Roman', Times, serif; font-size: 14pt; line-height: 1.5; }
                            #print-phieu { display: block !important; }
                            .no-print { display: none !important; }
                            table { border-collapse: collapse; width: 100%; }
                            table th, table td { border: 1px solid #000; padding: 8px; }
                            table th { background-color: #f0f0f0; }
                        </style>

                        <div style="max-width: 900px; margin: 0 auto; padding: 20px;">
                            <h1 style="text-align:center; font-size:28pt; margin-bottom:10px;">PHIẾU THANH LÝ THIẾT BỊ</h1>
                            <p style="text-align:center; font-size:16pt; margin-bottom:30px;">
                                Trường THCS .................... - Năm học 2025-2026
                            </p>

                            <table style="width:100%; margin-bottom:30px; font-size:14pt;">
                                <tr>
                                    <td style="width:50%;"><strong>Mã kế hoạch:</strong> TL<?= sprintf("%04d", $kh['maTL']) ?></td>
                                    <td style="width:50%; text-align:right;"><strong>Ngày lập:</strong> <?= date('d/m/Y', strtotime($kh['ngayLap'])) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Người lập:</strong> <?= htmlspecialchars($kh['nguoiLap'] ?? 'N/A') ?></td>
                                    <td style="text-align:right;"><strong>Phương pháp thanh lý:</strong> <?= htmlspecialchars($kh['phuongPhapThanhLy']) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong>Trạng thái:</strong> <?= htmlspecialchars($kh['trangThai']) ?></td>
                                </tr>
                                <?php if ($kh['ngayDuyet']): ?>
                                <tr>
                                    <td><strong>Ngày duyệt:</strong> <?= date('d/m/Y', strtotime($kh['ngayDuyet'])) ?></td>
                                    <td style="text-align:right;"><strong>Người duyệt:</strong> <?= htmlspecialchars($kh['nguoiDuyet'] ?? 'N/A') ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($kh['ngayHoanThanh']): ?>
                                <tr>
                                    <td colspan="2"><strong>Ngày hoàn thành thanh lý:</strong> <?= date('d/m/Y', strtotime($kh['ngayHoanThanh'])) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>

                            <?php if ($kh['ghiChu']): ?>
                            <div style="margin:20px 0;">
                                <strong>Ghi chú:</strong><br>
                                <div style="border:1px solid #000; padding:12px; margin-top:8px;">
                                    <?= nl2br(htmlspecialchars($kh['ghiChu'])) ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <h3 style="text-align:center; margin:40px 0 20px; font-size:20pt;">DANH SÁCH THIẾT BỊ THANH LÝ</h3>
                            <table style="width:100%; font-size:13pt;">
                                <thead>
                                    <tr style="background:#e0e0e0;">
                                        <th style="padding:10px;">STT</th>
                                        <th style="padding:10px;">Tên thiết bị</th>
                                        <th style="padding:10px;">Môn học</th>
                                        <th style="padding:10px;">Số lượng</th>
                                        <th style="padding:10px;">Đơn vị</th>
                                        <th style="padding:10px;">Tình trạng</th>
                                        <th style="padding:10px;">Lý do thanh lý</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kh['chiTiet'] as $i => $ct): ?>
                                    <tr>
                                        <td style="text-align:center; padding:8px;"><?= $i + 1 ?></td>
                                        <td style="padding:8px;"><?= htmlspecialchars($ct['tenTB']) ?></td>
                                        <td style="text-align:center; padding:8px;"><?= htmlspecialchars($ct['tenMonHoc'] ?? '-') ?></td>
                                        <td style="text-align:center; padding:8px;"><strong><?= $ct['soLuong'] ?></strong></td>
                                        <td style="text-align:center; padding:8px;"><?= htmlspecialchars($ct['donVi']) ?></td>
                                        <td style="text-align:center; padding:8px;"><?= htmlspecialchars($ct['tinhTrang']) ?></td>
                                        <td style="padding:8px;"><?= htmlspecialchars($ct['lyDo']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr style="background:#f5f5f5; font-weight:bold;">
                                        <td colspan="3" style="padding:12px; text-align:right;">Tổng số thiết bị:</td>
                                        <td style="padding:12px; text-align:center;"><?= count($kh['chiTiet']) ?></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div style="margin-top:80px; display:flex; justify-content:space-between; font-size:14pt;">
                                <div style="text-align:center;">
                                    <p><strong>Người lập kế hoạch</strong></p>
                                    <br><br><br><br>
                                    <p><em>(Ký, họ tên)</em></p>
                                </div>
                                <div style="text-align:center;">
                                    <p><strong>Hiệu trưởng</strong></p>
                                    <p style="font-size:12pt; margin-top:10px;">(Đã duyệt ngày <?= $kh['ngayDuyet'] ? date('d/m/Y', strtotime($kh['ngayDuyet'])) : '__/__/____' ?>)</p>
                                    <br><br><br>
                                    <p><em>(Ký, họ tên, đóng dấu)</em></p>
                                </div>
                            </div>

                            <div style="margin-top:50px; text-align:center; font-size:12pt; color:#666;">
                                Phiếu được in ngày <?= date('d/m/Y') ?> lúc <?= date('H:i') ?>
                            </div>
                        </div>
                    </div>

                    <script>
                    function printPhieu() {
                        var printContent = document.getElementById('print-phieu');
                        printContent.style.display = 'block';
                        window.print();
                    }
                    </script>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php else: ?>
        <!-- DANH SÁCH KẾ HOẠCH CHỜ DUYỆT -->
        <?php
        $res = $controller->getDanhSach();
        $allPlans = $res['success'] ? $res['data'] : [];
        $danhSach = array_filter($allPlans, fn($p) => $p['trangThai'] === 'Chờ duyệt');
        ?>
        <div class="qltb-card">
            <div class="card-body" style="padding:30px;">
                <h4 style="margin-bottom:20px; color:#1f2937;">Kế hoạch đang chờ duyệt (<strong><?= count($danhSach) ?></strong>)</h4>

                <?php if (empty($danhSach)): ?>
                    <div class="qltb-empty">
                        Hiện tại không có kế hoạch nào đang chờ duyệt.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="qltb-device-table">
                            <thead>
                                <tr>
                                    <th>Mã kế hoạch</th>
                                    <th>Ngày lập</th>
                                    <th>Người lập</th>
                                    <th>Phương pháp</th>
                                    <th>Số TB</th>
                                    <th>Ghi chú</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($danhSach as $kh): ?>
                                    <tr>
                                        <td><strong>TL<?= sprintf("%04d", $kh['maTL']) ?></strong></td>
                                        <td><?= date('d/m/Y', strtotime($kh['ngayLap'])) ?></td>
                                        <td><?= htmlspecialchars($kh['nguoiLap'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($kh['phuongPhapThanhLy']) ?></td>
                                        <td><strong><?= $kh['soThietBi'] ?></strong></td>
                                        <td><?= $kh['ghiChu'] ? htmlspecialchars(substr($kh['ghiChu'], 0, 50)) . '...' : '-' ?></td>
                                        <td>
                                            <a href="?tab=duyet-thanh-ly&action=chi-tiet&maTL=<?= $kh['maTL'] ?>" 
                                               class="qltb-btn-primary" style="padding:8px 16px; font-size:14px;">
                                                Xem & Duyệt
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</section>