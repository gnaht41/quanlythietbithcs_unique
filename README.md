# QUẢN LÝ THIẾT BỊ TRƯỜNG THCS - UNIQUE

## Sơ đồ cây

```
├── 📁 controllers
│   └── 🐘 AuthController.php
├── 📁 models
│   ├── 🐘 Database.php
│   └── 🐘 User.php
├── 📁 views
│   ├── 📁 css
│   │   ├── 🎨 dang-nhap.css
│   │   ├── 🎨 giao-vien.css
│   │   ├── 🎨 hieu-truong.css
│   │   ├── 🎨 nhan-vien-thiet-bi.css
│   │   ├── 🎨 quan-tri-vien.css
│   │   └── 🎨 to-truong.css
│   ├── 📁 js
│   │   ├── 📄 dang-nhap.js
│   │   ├── 📄 giao-vien.js
│   │   ├── 📄 hieu-truong.js
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

## Việc làm của mỗi người:
1. Vào cái actor của mỗi người, sửa lại cái route (là file views/tenactor.php) sửa đoạn INCLUDE CÁC TRANG CON (nếu cần thiết)
2. Thêm các pages mới cho route của actor đó (tôi đã chia ra rồi) - tôi xoá hết chỉ để mỗi thẻ h2 thôi vì đây là source cũ (chưa chỉnh giao diện)
3. Code logic vào controller và model (cái này có thể tạo file mới trách đụng code của nhau)
4. Thêm code css và js (nếu cần thiết) vào thư mục riêng của từng actor (tránh lỗi)
