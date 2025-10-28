<?php
// File: views/pages_quan-tri-vien/danh-sach-thiet-bi.php
// Biến $active_tab được kế thừa từ quan-tri-vien.php
require_once __DIR__ . '/../../models/ThietBi.php'; // PHP xử lý data giữ nguyên
$thietBiModel = new ThietBi();
$tenTB_filter = trim($_GET['tenTB'] ?? '');
$maMH_filter = trim($_GET['maMH'] ?? '');
$tinhTrang_filter = trim($_GET['tinhTrang'] ?? '');
$lop_filter = trim($_GET['lop'] ?? '');
$filters = ['tenTB' => $tenTB_filter, 'maMH' => $maMH_filter, 'tinhTrang' => $tinhTrang_filter, 'lop' => $lop_filter];
$searchResult = $thietBiModel->searchDevices($filters);
$devices = $searchResult['data'];
$listMonHoc = $thietBiModel->getMonHoc();
$listTinhTrang = $thietBiModel->getTinhTrang();
?>
<section id="danh-sach-thiet-bi" class="trang-an"
        <?php echo ($active_tab != 'danh-sach-thiet-bi') ? 'style="display:none;"' : ''; ?>>
        <div class="hang-cta">
                <h2>Danh sách thiết bị</h2>
                <form action="" method="GET" class="bo-loc" id="form-tim-kiem-tb">
                        <input type="hidden" name="tab" value="danh-sach-thiet-bi">

                        <?php foreach ($_GET as $key => $value): ?>
                                <?php if (!in_array($key, ['tenTB', 'maMH', 'tinhTrang', 'lop', 'tab'])): // Bỏ qua cả 'tab' 
                                ?>
                                        <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                                                value="<?php echo htmlspecialchars($value); ?>">
                                <?php endif; ?>
                        <?php endforeach; ?>

                        <input name="tenTB" value="<?php echo htmlspecialchars($tenTB_filter); ?>"
                                placeholder="Nhập tên thiết bị..." />
                        <select name="maMH" title="Danh mục/Môn">
                                <option value="">-- Danh mục/Môn --</option>
                                <?php foreach ($listMonHoc as $monHoc): ?>
                                        <option value="<?php echo htmlspecialchars($monHoc['maMH']); ?>"
                                                <?php echo ($maMH_filter == $monHoc['maMH']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($monHoc['tenMonHoc']); ?>
                                        </option>
                                <?php endforeach; ?>
                        </select>
                        <select name="tinhTrang" title="Tình trạng">
                                <option value="">-- Tình trạng --</option>
                                <?php foreach ($listTinhTrang as $tt): ?>
                                        <option value="<?php echo htmlspecialchars($tt); ?>"
                                                <?php echo ($tinhTrang_filter == $tt) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($tt); ?>
                                        </option>
                                <?php endforeach; ?>
                        </select>
                        <input name="lop" value="<?php echo htmlspecialchars($lop_filter); ?>" placeholder="Lớp (VD: 6)" />
                        <button type="submit" class="btn-primary">Tìm</button>
                        <a href="?tab=danh-sach-thiet-bi" class="btn-secondary" style="text-decoration: none;">Xóa lọc</a>
                </form>
        </div>
        <table>
                <thead>
                        <tr>
                                <th>Số TT</th>
                                <th>Tên thiết bị</th>
                                <th>Môn học</th>
                                <th>Đơn vị</th>
                                <th>Số lượng</th>
                                <th>Lớp</th>
                                <th>Tình trạng</th>
                        </tr>
                </thead>
                <tbody id="bang-ds-thiet-bi">
                        <?php if (!empty($devices)): ?>
                                <?php foreach ($devices as $index => $device): ?>
                                        <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><?php echo htmlspecialchars($device['tenTB']); ?></td>
                                                <td><?php echo htmlspecialchars($device['tenMonHoc'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($device['donVi']); ?></td>
                                                <td><?php echo htmlspecialchars($device['soLuong']); ?></td>
                                                <td><?php echo htmlspecialchars($device['lop']); ?></td>
                                                <td><?php echo htmlspecialchars($device['tinhTrang']); ?></td>
                                        </tr>
                                <?php endforeach; ?>
                        <?php else: ?>
                                <tr>
                                        <td colspan="7" style="text-align: center;">Không tìm thấy thiết bị nào phù hợp.</td>
                                </tr>
                        <?php endif; ?>
                </tbody>
        </table>
</section>