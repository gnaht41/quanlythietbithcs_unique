<?php
// File: views/quan-ly-danh-muc.php
require_once __DIR__ . '/../../models/VV_QLThietBi.php';
require_once __DIR__ . '/../../controllers/VV_QLThietBi.php';
$thietBiModel = new ThietBiModel();
$thietBiController = new ThietBiController();

// --- XỬ LÝ XÓA THIẾT BỊ ---
if (isset($_GET['action']) && $_GET['action'] === 'xoa-thiet-bi' && isset($_GET['maTB'])) {
    $maTB = (int)$_GET['maTB'];
    $result = $thietBiController->deleteDevice($maTB);
    
    if ($result['success']) {
        echo "<script>
            alert('Xóa thiết bị thành công!');
            window.location.href = '?tab=quan-ly-thiet-bi';
        </script>";
    } else {
        echo "<script>
            alert('Xóa thiết bị thất bại: " . addslashes($result['message']) . "');
            window.location.href = '?tab=quan-ly-thiet-bi';
        </script>";
    }
    exit;
}

// --- LẤY DỮ LIỆU CHO VIEW ---
$filters = [
    'tenTB'      => trim($_GET['tenTB'] ?? ''),
    'maMH'       => trim($_GET['maMH'] ?? ''),
    'tinhTrang'  => trim($_GET['tinhTrang'] ?? ''),
    'lop'        => trim($_GET['lop'] ?? '')
];

// Lấy danh sách thiết bị theo bộ lọc
$searchResult = $thietBiController->searchDevices($filters);
$devices = $searchResult['data'] ?? [];

// Lấy danh sách môn học
$listMonHoc = $thietBiController->getMonHocList()['data'] ?? [];

// Danh sách tình trạng (lấy từ ENUM trong CSDL)
$listTinhTrang = ['Tốt', 'Hư nhẹ', 'Hư nặng', 'Đang sửa'];

// Danh sách lớp cố định theo ENUM
$listLop = ['6', '7', '8', '9'];
?>

<style>
/* Giữ nguyên toàn bộ CSS bạn đã có */
#quan-ly-danh-muc {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin: 20px auto;
    max-width: 1200px;
}

.hang-cta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.hang-cta h2 {
    font-size: 24px;
    color: #333;
}

.hang-cta .btn-primary {
    background-color: #0d6efd;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
}

.hang-cta .btn-primary:hover {
    background-color: #0b5ed7;
}

.bo-loc {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
    align-items: end;
}

.bo-loc input,
.bo-loc select {
    padding: 8px 10px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

.bo-loc button,
.bo-loc a.btn-secondary {
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
}

.bo-loc button.btn-primary {
    background-color: #0d6efd;
    border: none;
    color: #fff;
    cursor: pointer;
}

.bo-loc a.btn-secondary {
    background-color: #6c757d;
    color: #fff;
    display: inline-block;
    text-align: center;
}

.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}

.table th,
.table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #dee2e6;
}

.table th {
    background-color: #343a40;
    color: #fff;
}

.table tbody tr:hover {
    background-color: #f1f1f1;
}

.table .btn-sm {
    padding: 5px 10px;
    font-size: 13px;
    border-radius: 4px;
    text-decoration: none;
    margin: 0 3px;
}

.table .btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.table .btn-danger {
    background-color: #dc3545;
    color: #fff;
}

@media screen and (max-width: 992px) {
    .bo-loc {
        flex-direction: column;
        align-items: stretch;
    }
    .bo-loc input,
    .bo-loc select,
    .bo-loc button,
    .bo-loc a {
        width: 100%;
    }
}
</style>

<section id="quan-ly-danh-muc">
    <div class="hang-cta">
        <h2>Danh sách thiết bị</h2>
        <a href="?tab=them-thiet-bi" class="btn-primary">Thêm thiết bị</a>
    </div>

    <!-- Form tìm kiếm / lọc -->
    <form method="GET" class="bo-loc">
        <input type="hidden" name="tab" value="quan-ly-danh-muc">
        
        <input type="text" name="tenTB" value="<?php echo htmlspecialchars($filters['tenTB']); ?>" 
               placeholder="Tên thiết bị">

        <select name="maMH">
            <option value="">-- Chọn môn học --</option>
            <?php foreach ($listMonHoc as $mon): ?>
                <option value="<?php echo $mon['maMH']; ?>" 
                    <?php echo ($filters['maMH'] == $mon['maMH']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($mon['tenMonHoc']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="tinhTrang">
            <option value="">-- Tình trạng --</option>
            <?php foreach ($listTinhTrang as $tt): ?>
                <option value="<?php echo $tt; ?>" 
                    <?php echo ($filters['tinhTrang'] == $tt) ? 'selected' : ''; ?>>
                    <?php echo $tt; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="lop">
            <option value="">-- Lớp --</option>
            <?php foreach ($listLop as $l): ?>
                <option value="<?php echo $l; ?>" 
                    <?php echo ($filters['lop'] == $l) ? 'selected' : ''; ?>>
                    Lớp <?php echo $l; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-primary">Tìm kiếm</button>
        <a href="?tab=quan-ly-danh-muc" class="btn-secondary">Xóa lọc</a>
    </form>

    <!-- Bảng danh sách thiết bị -->
    <div style="overflow-x:auto;">
        <table class="table">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Tên thiết bị</th>
                    <th>Môn học</th>
                    <th>Đơn vị</th>
                    <th>Số lượng<br><small>(Tổng / Khả dụng)</small></th>
                    <th>Lớp</th>
                    <th>Tình trạng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($devices)): ?>
                    <?php foreach ($devices as $index => $device): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($device['tenTB']); ?></td>
                            <td><?php echo htmlspecialchars($device['tenMonHoc'] ?? 'Không có môn'); ?></td>
                            <td><?php echo htmlspecialchars($device['donVi'] ?? '-'); ?></td>
                            <td>
                                <strong><?php echo $device['soLuongTong']; ?></strong> /
                                <?php echo $device['soLuongKhaDung']; ?>
                            </td>
                            <td><?php echo htmlspecialchars($device['lop'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($device['tinhTrang']); ?></td>
                            <td>
                                <a href="?tab=sua-thiet-bi&maTB=<?php echo $device['maTB']; ?>" 
                                   class="btn-sm btn-warning">Sửa</a>
                                <a href="?tab=quan-ly-thiet-bi&action=xoa-thiet-bi&maTB=<?php echo $device['maTB']; ?>" 
                                   class="btn-sm btn-danger"
                                   onclick="return confirm('Bạn có chắc muốn xóa thiết bị \"<?php echo addslashes($device['tenTB']); ?>\" không?');">
                                    Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:20px;">
                            Không tìm thấy thiết bị nào phù hợp với bộ lọc.
                        </td>
                    </tr>
                <?php endif; ?>  <!-- Đóng khối if (!empty($devices)) -->
            </tbody>
        </table>
    </div>
</section>