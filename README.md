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
    * Tạo các file Controller và Model mới để xử lý logic backend, kèm tên viết tắt của mình ở trước
    (ví dụ: `models/QT_PhieuMuon.php`, `controllers/QT_PhieuMuonController.php`)

5.  **CSS/JS:**
    * Nếu cần thêm CSS hoặc JS đặc thù cho vai trò, hãy thêm vào file CSS/JS riêng của vai trò đó (ví dụ: `css/giao-vien.css`, `js/giao-vien.js`).
    * Các file này đã được tự động nạp bởi `header.php` và `footer.php` do tui đã include sẵn.

5.  **Cây thư mục:**
```
├── 📁 controllers
│   ├── 🐘 QT_AuthController.php
├── 📁 models
│   ├── 🐘 QT_Database.php
├── 📁 views
│   ├── 📁 css
│   │   ├── 🎨 dang-nhap.css
│   │   ├── 🎨 giao-vien.css
│   │   ├── 🎨 main.css
│   ├── 📁 img
│   │   └── 🖼️ UniqueLogo.jpg
│   ├── 📁 js
│   │   ├── 📄 dang-nhap.js
│   │   ├── 📄 giao-vien.js
│   │   ├── 📄 main.js
│   ├── 📁 pages_giao-vien
│   │   ├── 🐘 bao-cao-hu-hong.php
│   ├── 📁 pages_hieu-truong
│   │   ├── 🐘 danh-sach-thiet-bi.php
│   ├── 📁 pages_nhan-vien-thiet-bi
│   │   ├── 🐘 danh-sach-thiet-bi.php
│   ├── 📁 pages_quan-tri-vien
│   │   ├── 🐘 danh-sach-thiet-bi.php
│   ├── 📁 pages_to-truong
│   │   ├── 🐘 danh-sach-thiet-bi.php
│   ├── 📁 partials
│   │   ├── 🐘 footer.php
│   │   ├── 🐘 header.php
│   │   ├── 🐘 sidebar-admin.php
│   ├── 🐘 dang-nhap.php
│   ├── 🐘 giao-vien.php
├── 📝 README.md
├── 🐘 index.php
└── 📄 qltb.sql
```