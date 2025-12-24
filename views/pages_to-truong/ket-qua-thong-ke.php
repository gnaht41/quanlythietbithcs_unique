<?php
// views/pages_nhan-vien-thiet-bi/ket-qua-thong-ke.php

require_once __DIR__ . '/../../models/QT_ThongKeModel.php';
require_once __DIR__ . '/../../models/QT_ThietBi.php';

$thongKeModel = new QT_ThongKeModel();
$thietBiModel = new ThietBi();

$trangThaiData = $thongKeModel->getThongKeTrangThai();
$monHocData = $thongKeModel->getThongKeMonHoc();
$lopData = $thongKeModel->getThongKeLop();
$hongNangData = $thongKeModel->getThietBiHongNang();

$allThietBi = $thietBiModel->getAll();

$totalTong = array_sum(array_column($trangThaiData, 'tong'));
$totalKhaDung = array_sum(array_column($trangThaiData, 'total'));

// Cần mua sắm (< 80%)
$canMuaSam = [];
foreach ($allThietBi as $tb) {
    $tong = (int)$tb['soLuongTong'];
    $khaDung = (int)$tb['soLuongKhaDung'];
    if ($tong > 0 && ($khaDung / $tong) < 0.8) {
        $tb['tyLe'] = round(($khaDung / $tong) * 100);
        $tb['canMua'] = $tong - $khaDung;
        $canMuaSam[] = $tb;
    }
}

// Cần thanh lý
$canThanhLy = [];
foreach ($allThietBi as $tb) {
    if (in_array($tb['tinhTrang'], ['Hư nặng', 'Đang sửa']) && $tb['soLuongTong'] > $tb['soLuongKhaDung']) {
        $tb['soHong'] = $tb['soLuongTong'] - $tb['soLuongKhaDung'];
        $canThanhLy[] = $tb;
    }
}

// Biểu đồ data
$trangThaiLabels = json_encode(array_column($trangThaiData, 'label'));
$trangThaiTong = json_encode(array_column($trangThaiData, 'tong'));
$trangThaiKhaDung = json_encode(array_column($trangThaiData, 'total'));

$monHocLabels = json_encode(array_column($monHocData, 'label'));
$monHocTong = json_encode(array_column($monHocData, 'tong'));
$monHocKhaDung = json_encode(array_column($monHocData, 'total'));

$lopLabels = json_encode(array_column($lopData, 'label'));
$lopTong = json_encode(array_column($lopData, 'tong'));
$lopKhaDung = json_encode(array_column($lopData, 'total'));

$hongNangLabels = json_encode(array_column($hongNangData, 'label'));
$hongNangValues = json_encode(array_column($hongNangData, 'hongNang'));
?>

<section id="ket-qua-thong-ke" class="trang-an"
    <?php echo ($active_tab !== 'ket-qua-thong-ke') ? 'style="display:none;"' : ''; ?>>
    <h2>Kết quả thống kê thiết bị</h2>

    <!-- Tổng quan nhanh -->
    <div class="summary-box">
        <div class="summary-item">
            <strong>Tổng thiết bị:</strong> <?php echo number_format($totalTong); ?> cái
        </div>
        <div class="summary-item">
            <strong>Khả dụng:</strong> <?php echo number_format($totalKhaDung); ?> cái
        </div>
        <div class="summary-item">
            <strong>Tỷ lệ khả dụng:</strong>
            <span
                style="color: <?php echo ($totalTong > 0 && ($totalKhaDung / $totalTong) < 0.8) ? '#d32f2f' : '#2e7d32'; ?>; font-weight:bold;">
                <?php echo $totalTong > 0 ? round(($totalKhaDung / $totalTong) * 100, 1) : 0; ?>%
            </span>
        </div>
    </div>

    <div class="tab-container">
        <div class="tab-links">
            <div class="tab-link active" data-tab="tab-tongquan">Tổng quan</div>
            <div class="tab-link" data-tab="tab-monhoc">Theo môn học</div>
            <div class="tab-link" data-tab="tab-lop">Theo lớp</div>
            <div class="tab-link" data-tab="tab-muasam">Cần mua sắm <span
                    class="badge"><?php echo count($canMuaSam); ?></span></div>
            <div class="tab-link" data-tab="tab-thanhly">Cần thanh lý <span
                    class="badge"><?php echo count($canThanhLy); ?></span></div>
        </div>

        <!-- Tổng quan -->
        <div id="tab-tongquan" class="tab-content active">
            <div class="chart-grid">
                <div class="chart-item">
                    <canvas id="chartTrangThaiPie"></canvas>
                </div>
                <div class="chart-item">
                    <canvas id="chartTrangThaiBar"></canvas>
                </div>
            </div>
        </div>

        <!-- Môn học -->
        <div id="tab-monhoc" class="tab-content">
            <div class="chart-grid">
                <div class="chart-item">
                    <canvas id="chartMonHocPie"></canvas>
                </div>
                <div class="chart-item">
                    <canvas id="chartMonHocBar"></canvas>
                </div>
            </div>
        </div>

        <!-- Lớp -->
        <div id="tab-lop" class="tab-content">
            <div class="chart-grid">
                <div class="chart-item">
                    <canvas id="chartLopPie"></canvas>
                </div>
                <div class="chart-item">
                    <canvas id="chartLopBar"></canvas>
                </div>
            </div>
        </div>

        <!-- Cần mua sắm -->
        <div id="tab-muasam" class="tab-content">
            <h3>Cần mua sắm thêm thiết bị</h3>
            <?php if (empty($canMuaSam)): ?>
                <div class="alert-success">Tất cả thiết bị đều đủ khả dụng. Không cần mua thêm!</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tên thiết bị</th>
                                <th>Môn học</th>
                                <th>Lớp</th>
                                <th>Tổng / Khả dụng</th>
                                <th>Tỷ lệ</th>
                                <th>Cần mua thêm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($canMuaSam as $tb): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($tb['tenTB']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($tb['tenMonHoc'] ?? '-'); ?></td>
                                    <td><?php echo $tb['lop']; ?></td>
                                    <td><?php echo $tb['soLuongTong'] . ' / ' . $tb['soLuongKhaDung']; ?></td>
                                    <td><span
                                            class="badge <?php echo $tb['tyLe'] < 50 ? 'danger' : 'warning'; ?>"><?php echo $tb['tyLe']; ?>%</span>
                                    </td>
                                    <td class="highlight"><?php echo $tb['canMua']; ?> cái</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cần thanh lý -->
        <div id="tab-thanhly" class="tab-content">
            <h3>Cần thanh lý thiết bị</h3>
            <?php if (empty($canThanhLy)): ?>
                <div class="alert-success">Không có thiết bị nào cần thanh lý!</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tên thiết bị</th>
                                <th>Môn học</th>
                                <th>Lớp</th>
                                <th>Tình trạng</th>
                                <th>Số lượng hỏng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($canThanhLy as $tb): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($tb['tenTB']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($tb['tenMonHoc'] ?? '-'); ?></td>
                                    <td><?php echo $tb['lop']; ?></td>
                                    <td><span class="badge danger"><?php echo $tb['tinhTrang']; ?></span></td>
                                    <td class="highlight"><?php echo $tb['soHong']; ?> cái</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    /* Tổng quan nhanh */
    .summary-box {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        background: #f8fafc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-size: 16px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .summary-item {
        flex: 1;
        min-width: 200px;
    }

    /* Tab */
    .tab-links {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 20px;
        border-bottom: 2px solid #e2e8f0;
    }

    .tab-link {
        padding: 10px 20px;
        background: #f1f5f9;
        border-radius: 8px 8px 0 0;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
    }

    .tab-link.active {
        background: #3b82f6;
        color: white;
    }

    .badge {
        background: #ef4444;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin-left: 6px;
    }

    .tab-content {
        display: none;
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .tab-content.active {
        display: block;
    }

    /* Biểu đồ */
    .chart-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 25px;
    }

    .chart-item {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* Bảng */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 15px;
    }

    .data-table th {
        background: #1e40af;
        color: white;
        padding: 12px;
        text-align: left;
    }

    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
    }

    .data-table tr:hover {
        background: #f8fafc;
    }

    .highlight {
        background: #fee2e2;
        font-weight: bold;
        color: #991b1b;
    }

    .badge.danger {
        background: #dc2626;
    }

    .badge.warning {
        background: #f59e0b;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        padding: 20px;
        border-radius: 10px;
        font-size: 18px;
        text-align: center;
        border: 1px solid #86efac;
    }
</style>

<script>
    document.querySelectorAll('.tab-link').forEach(l => {
        l.addEventListener('click', function() {
            document.querySelectorAll('.tab-link').forEach(x => x.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(x => x.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(this.dataset.tab).classList.add('active');
        });
    });

    const colors = ['#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6'];

    // Các biểu đồ giữ nguyên logic như trước
    new Chart('chartTrangThaiPie', {
        type: 'pie',
        data: {
            labels: <?php echo $trangThaiLabels; ?>,
            datasets: [{
                data: <?php echo $trangThaiKhaDung; ?>,
                backgroundColor: colors
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Tỷ lệ trạng thái thiết bị'
                }
            }
        }
    });
    new Chart('chartTrangThaiBar', {
        type: 'bar',
        data: {
            labels: <?php echo $trangThaiLabels; ?>,
            datasets: [{
                label: 'Tổng',
                data: <?php echo $trangThaiTong; ?>,
                backgroundColor: '#94a3b8'
            }, {
                label: 'Khả dụng',
                data: <?php echo $trangThaiKhaDung; ?>,
                backgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'So sánh tổng vs khả dụng'
                }
            }
        }
    });

    new Chart('chartMonHocPie', {
        type: 'pie',
        data: {
            labels: <?php echo $monHocLabels; ?>,
            datasets: [{
                data: <?php echo $monHocKhaDung; ?>,
                backgroundColor: colors
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Thiết bị theo môn học'
                }
            }
        }
    });
    new Chart('chartMonHocBar', {
        type: 'bar',
        data: {
            labels: <?php echo $monHocLabels; ?>,
            datasets: [{
                label: 'Tổng',
                data: <?php echo $monHocTong; ?>,
                backgroundColor: '#94a3b8'
            }, {
                label: 'Khả dụng',
                data: <?php echo $monHocKhaDung; ?>,
                backgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'So sánh theo môn học'
                }
            }
        }
    });

    new Chart('chartLopPie', {
        type: 'pie',
        data: {
            labels: <?php echo $lopLabels; ?>,
            datasets: [{
                data: <?php echo $lopKhaDung; ?>,
                backgroundColor: colors
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Thiết bị theo lớp'
                }
            }
        }
    });
    new Chart('chartLopBar', {
        type: 'bar',
        data: {
            labels: <?php echo $lopLabels; ?>,
            datasets: [{
                label: 'Tổng',
                data: <?php echo $lopTong; ?>,
                backgroundColor: '#94a3b8'
            }, {
                label: 'Khả dụng',
                data: <?php echo $lopKhaDung; ?>,
                backgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'So sánh theo lớp'
                }
            }
        }
    });
</script>