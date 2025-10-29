# QUẢN LÝ THIẾT BỊ TRƯỜNG THCS - UNIQUE

## Việc làm của mỗi người:

1.  **Cập nhật file Layout Chính (Route):**
    * Vào file layout của vai trò mình (ví dụ: `views/giao-vien.php`, `views/quan-tri-vien.php`...).==
    * Kiểm tra và sửa lại đoạn "INCLUDE CÁC TRANG CON" để đảm bảo `require_once` đúng các page.
    * **Ví dụ (cho `giao-vien.php`):**
        ```php
        <?php // --- INCLUDE CÁC TRANG CON ---
        // Biến $active_tab đã được định nghĩa ở trên
        require_once 'pages_giao-vien/tong-quan.php';
        require_once 'pages_giao-vien/danh-sach-thiet-bi.php'; 
        require_once 'pages_giao-vien/phieu-muon.php';
        require_once 'pages_giao-vien/lich-su-muon.php';
        require_once 'pages_giao-vien/bao-cao-hu-hong.php';
        ?>
        ```

2.  **Cập nhật file Sidebar:**
    * Vào file sidebar của vai trò mình (ví dụ: `views/partials/sidebar-giaovien.php`).
    * **Ví dụ (cho `sidebar-giaovien.php`):**
        ```php
        <aside class="thanh-ben">
            <ul>
                <li><a href="?tab=tong-quan" class="<?php echo ($active_tab == 'tong-quan') ? 'active' : ''; ?>">Tổng quan</a></li>
                <li><a href="?tab=danh-sach-thiet-bi" class="<?php echo ($active_tab == 'danh-sach-thiet-bi') ? 'active' : ''; ?>">Danh sách thiết bị</a></li>
                </ul>
        </aside>
        ```

3.  **Thêm nội dung vào Page (Section con):**
    * Mở file section con cần làm (ví dụ: `views/pages_giao-vien/bao-cao-hu-hong.php`).
    * **Giữ nguyên** thẻ `<section>` và đoạn code PHP kiểm tra `$active_tab`.
    * **Xóa** thẻ `<h2>...</h2>` bên trong và thêm nội dung HTML/PHP của bạn vào.
    * **Ví dụ:**
        ```php
        <section id="bao-cao-hu-hong" class="trang-an"
            <?php echo ($active_tab != 'bao-cao-hu-hong') ? 'style="display:none;"' : ''; ?>>
        
                <h2>Quản lý Báo cáo Hư hỏng</h2> <- Chỉ code vào đây

        </section>
        ```

4.  **Code Logic:**
    * Tạo các file Controller và Model mới để xử lý logic backend.
    * Nên đặt tên file theo chức năng (ví dụ: `models/PhieuMuon.php`, `controllers/PhieuMuonController.php`) để tránh xung đột.

5.  **CSS/JS:**
    * Nếu cần thêm CSS hoặc JS đặc thù cho vai trò, hãy thêm vào file CSS/JS riêng của vai trò đó (ví dụ: `css/giao-vien.css`, `js/giao-vien.js`).
    * Các file này đã được tự động nạp bởi `header.php` và `footer.php`.

6.  **CSDL:**
    * Nếu có bất kỳ thay đổi nào về cấu trúc CSDL (thêm/sửa/xóa bảng, cột), hãy xuất file script `.sql` và gửi vào nhóm.

## Sơ đồ cây

```
├── 📁 controllers
│   └── 🐘 AuthController.php
├── 📁 models
│   ├── 🐘 Database.php
│   ├── 🐘 ThietBi.php
│   └── 🐘 User.php
├── 📁 views
│   ├── 📁 css
│   │   ├── 🎨 dang-nhap.css
│   │   ├── 🎨 giao-vien.css
│   │   ├── 🎨 hieu-truong.css
│   │   ├── 🎨 main.css
│   │   ├── 🎨 nhan-vien-thiet-bi.css
│   │   ├── 🎨 quan-tri-vien.css
│   │   └── 🎨 to-truong.css
│   ├── 📁 js
│   │   ├── 📄 dang-nhap.js
│   │   ├── 📄 giao-vien.js
│   │   ├── 📄 hieu-truong.js
│   │   ├── 📄 main.js
│   │   ├── 📄 nhan-vien-thiet-bi.js
│   │   ├── 📄 quan-tri-vien.js
│   │   └── 📄 to-truong.js
│   ├── 📁 pages_giao-vien
│   │   ├── 🐘 bao-cao-hu-hong.php
│   │   ├── 🐘 danh-sach-thiet-bi.php
│   │   ├── 🐘 lich-su-muon.php
│   │   ├── 🐘 phieu-muon.php
│   │   └── 🐘 tong-quan.php
│   ├── 📁 pages_hieu-truong
│   │   ├── 🐘 bao-cao-thong-ke.php
│   │   ├── 🐘 danh-sach-thiet-bi.php
│   │   ├── 🐘 duyet-ke-hoach.php
│   │   ├── 🐘 duyet-thanh-ly.php
│   │   └── 🐘 tong-quan.php
│   ├── 📁 pages_nhan-vien-thiet-bi
│   │   ├── 🐘 danh-sach-thiet-bi.php
│   │   ├── 🐘 lap-bao-cao.php
│   │   ├── 🐘 phieu-muon.php
│   │   ├── 🐘 quan-ly-bao-tri.php
│   │   ├── 🐘 quan-ly-danh-muc.php
│   │   ├── 🐘 quan-ly-kiem-ke.php
│   │   └── 🐘 tong-quan.php
│   ├── 📁 pages_quan-tri-vien
│   │   ├── 🐘 danh-sach-thiet-bi.php
│   │   ├── 🐘 nhat-ky.php
│   │   ├── 🐘 phan-quyen.php
│   │   ├── 🐘 ql-nguoi-dung.php
│   │   └── 🐘 tong-quan.php
│   ├── 📁 pages_to-truong
│   │   ├── 🐘 danh-sach-thiet-bi.php
│   │   ├── 🐘 lap-ke-hoach-mua-sam.php
│   │   ├── 🐘 theo-doi-thiet-bi.php
│   │   └── 🐘 tong-quan.php
│   ├── 📁 partials
│   │   ├── 🐘 footer.php
│   │   ├── 🐘 header.php
│   │   ├── 🐘 sidebar-admin.php
│   │   ├── 🐘 sidebar-giaovien.php
│   │   ├── 🐘 sidebar-hieutruong.php
│   │   ├── 🐘 sidebar-nhanvienthietbi.php
│   │   └── 🐘 sidebar-totruong.php
│   ├── 🐘 dang-nhap.php
│   ├── 🐘 giao-vien.php
│   ├── 🐘 hieu-truong.php
│   ├── 🐘 nhan-vien-thiet-bi.php
│   ├── 🐘 quan-tri-vien.php
│   └── 🐘 to-truong.php
├── 📝 README.md
└── 🐘 index.php
```