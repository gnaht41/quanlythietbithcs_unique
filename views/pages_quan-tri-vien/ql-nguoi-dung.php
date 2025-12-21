<?php
$filters = [
    'tuKhoa' => $_GET['tuKhoa'] ?? '',
    'maVT' => $_GET['maVT'] ?? '',
    'trangThai' => $_GET['trangThai'] ?? ''
];
$vaiTros = $n->getVaiTroList();
$nguoiDungs = $n->getDanhSachNguoiDung($filters);
?>
<section id="ql-nguoi-dung" class="trang-an" <?php echo ($active_tab != 'ql-nguoi-dung') ? 'style="display:none;"' : ''; ?>>
    <div class="hang-cta">
        <h2>Quản lý tài khoản người dùng</h2>
        <button id="nut-them-nguoi-dung" class="nut-them">Thêm tài khoản</button>
    </div>

    <form method="get" class="bo-loc">
        <input type="hidden" name="tab" value="ql-nguoi-dung">

        <input name="tuKhoa" placeholder="Tìm theo tên/email..." value="<?= htmlspecialchars($filters['tuKhoa']) ?>">

        <select name="maVT">
            <option value="">-- Vai trò --</option>
            <?php foreach ($vaiTros as $vt): ?>
            <option value="<?= $vt['maVT'] ?>" <?= ($filters['maVT'] == $vt['maVT']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($vt['tenVT']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <select name="trangThai">
            <option value="">-- Trạng thái --</option>
            <option value="Hoạt động" <?= ($filters['trangThai'] == 'Hoạt động') ? 'selected' : '' ?>>Hoạt động</option>
            <option value="Khoá" <?= ($filters['trangThai'] == 'Khoá') ? 'selected' : '' ?>>Khoá</option>
        </select>
        <button type="submit" class="btn-primary">Lọc</button>
        <a href="?tab=ql-nguoi-dung" class="btn-secondary">Xóa lọc</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <!-- <th>Ngày tạo</th> -->
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody id="bang-nguoi-dung">
            <?php if (empty($nguoiDungs)): ?>
            <tr>
                <td colspan="6">Không có tài khoản phù hợp</td>
            </tr>
            <?php else: ?>
            <?php foreach ($nguoiDungs as $i => $row): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($row['hoTen'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['tenVT'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['trangThai'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="hanh-dong">
                    <button type="button" name="sua" class="nut-sua" id="nut-sua-nguoi-dung" data-mand="<?= $row['maND'] ?>"
                                                                                         data-hoten="<?= htmlspecialchars($row['hoTen'], ENT_QUOTES, 'UTF-8') ?>"
                                                                                         data-username="<?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ?>"
                                                                                         data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>"
                                                                                         data-tenvt="<?= $row['maVT'] ?>"
                                                                                         data-trangthai="<?= htmlspecialchars($row['trangThai'], ENT_QUOTES, 'UTF-8') ?>"
                                                                                         data-password="<?= htmlspecialchars($row['password'], ENT_QUOTES, 'UTF-8') ?>">
                    Sửa</button>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="mand" value="<?= $row['maND'] ?>">
                        <button type="submit" name="xoa" class="nut-xoa">Xóa</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>
<!-- Modal: Thêm người dùng  aria-hidden="true" --> 
                <div id="modal-them-nguoi-dung" class="modal">
                    <div class="noi-dung-modal" role="dialog" aria-modal="true" tabindex="-1">
                        <button id="dong-modal-nd" class="dong-x" aria-label="Đóng">&times;</button>
                        <h3 id="tieu-de-nd">Thêm tài khoản</h3>
                        <form id="form-nguoi-dung" name="form-nguoi-dung" method="post" enctype="multipart/form-data">
                            <input type="hidden" id="id-nd" />
                            <div class="form-grid">
                                <div class="field">
                                    <label for="ho-ten">Họ tên</label>
                                    <input id="ho-ten" name="ho-ten" required />
                                </div>
                                
                                <div class="field">
                                    <label for="email">Email</label>
                                    <input id="email" name="email" type="email" required />
                                </div>
                                <div class="field">
                                    <label for="vai-tro">Vai trò</label>
                                    <select id="vai-tro" name="vai-tro" required>
                                        <option value="0" default>Chọn vai trò</option>
                                        <option value="1">Admin</option>
                                        <option value="2">Giáo viên</option>
                                        <option value="3">Nhân viên</option>
                                        <option value="4">Tổ trưởng</option>
                                        <option value="5">Ban giám hiệu</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="trang-thai">Trạng thái</label>
                                    <select id="trang-thai" name="trang-thai" required>
                                        <option value="Hoạt động" default>Hoạt động</option>
                                        <option value="Khóa">Khóa</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="tai-khoan">Username</label>
                                    <input id="tai-khoan" name="tai-khoan" required />
                                </div>
                                <div class="field">
                                    <label for="mat-khau">Mật khẩu khởi tạo</label>
                                    <input id="mat-khau" name="mat-khau" type="password" placeholder="Ít nhất 6 ký tự" />
                                </div>
                                <!-- <div class="field">
                                    <label for="ngay-tao">Ngày tạo</label>
                                    <input id="ngay-tao" name="ngay-tao" type="date" />
                                </div> -->
                            </div>
                            <div class="nut-modal">
                                <button type="submit" id="submit" name="submit" class="btn-primary">Lưu</button>
                                <button type="button" id="huy-nd" name="huy-nd" class="btn-secondary">Hủy</button>
                            </div>
                        </form>
                    </div>
                </div>
<!-- Modal sửa người dùng -->
<div id="modal-sua-nguoi-dung" class="modal">
    <div class="noi-dung-modal" role="dialog" aria-modal="true" tabindex="-1">
        <button id="dong-modal-sua" class="dong-x" aria-label="Đóng">&times;</button>
        <h3 id="tieu-de-sua">Sửa tài khoản</h3>
        <form id="form-sua-nguoi-dung" name="form-sua-nguoi-dung" method="post" enctype="multipart/form-data">
            <input type="hidden" id="id-sua-nd" name="id-sua-nd" />
            <div class="form-grid">
                <div class="field">
                    <label for="sua-maND">Mã tài khoản</label>
                    <input id="sua-maND" name="sua-maND" required readonly/>
                </div>
                <div class="field">
                    <label for="sua-hoTen">Họ tên</label>
                    <input id="sua-hoTen" name="sua-hoTen" required />
                </div>
                <div class="field">
                    <label for="sua-email">Email</label>
                    <input id="sua-email" name="sua-email" type="email" required readonly/>
                </div>
                <div class="field">
                    <label for="sua-tenVT">Vai trò</label>
                    <select id="sua-tenVT" name="sua-tenVT" required>
                        <option value="0">Chọn vai trò</option>
                        <option value="1">Admin</option>
                        <option value="2">Giáo viên</option>
                        <option value="3">Nhân viên</option>
                        <option value="4">Tổ trưởng</option>
                        <option value="5">Ban giám hiệu</option>
                    </select>
                </div>
                <div class="field">
                    <label for="sua-username">Tài khoản</label>
                    <input id="sua-username" name="sua-username" required readonly/>
                </div>
                <div class="field">
                    <label for="sua-trangthai">Trạng thái</label>
                    <input id="sua-trangthai" name="sua-trangthai" type="text" required readonly/>
                </div>
                <!-- <div class="field">
                    <label for="sua-trangthai">Trạng thái</label>
                    <select id="sua-trangthai" name="sua-trangthai" required>
                        <option value="Hoạt động">Hoạt động</option>
                        <option value="Khóa">Khóa</option>
                    </select>
                </div> -->
                <div class="field">
                    <label for="sua-password">Mật khẩu</label>
                    <input id="sua-password" name="sua-password" type="password"/>
                </div>
                <!-- <div class="field">
                    <label for="sua-ngayTao">Ngày tạo tài khoản</label>
                    <input id="sua-ngayTao" name="sua-ngayTao" type="date" readonly />
                </div> -->
                <div class="field">
                    <label for="sua-passwordmoi">Mật khẩu mới</label>
                    <input id="sua-passwordmoi" name="sua-passwordmoi" type="password"/>
                </div>
                <div class="field">
                    <label for="sua-passwordnhaplai">Nhập lại mật khẩu mới</label>
                    <input id="sua-passwordnhaplai" name="sua-passwordnhaplai" type="password"/>
                </div>
            </div>
            <div class="nut-modal">
                <button type="submit" id="submit-sua" name="submit-sua" class="btn-primary">Cập nhật</button>
                <button type="button" id="huy-sua" name="huy-sua" class="btn-secondary">Hủy</button>
            </div>
        </form>
    </div>
</div>

