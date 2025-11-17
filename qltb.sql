-- ============================
-- TẠO CƠ SỞ DỮ LIỆU
-- ============================
CREATE DATABASE qltb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qltb;

-- ============================
-- BẢNG VaiTro
-- ============================
CREATE TABLE VaiTro (
    maVT INT AUTO_INCREMENT PRIMARY KEY,
    tenVT VARCHAR(50) NOT NULL
);

INSERT INTO VaiTro(tenVT) VALUES
('Admin'), ('Giáo viên'), ('Nhân viên'), ('Tổ trưởng'), ('Ban giám hiệu');

-- ============================
-- BẢNG NguoiDung
-- ============================
CREATE TABLE NguoiDung (
    maND INT AUTO_INCREMENT PRIMARY KEY,
    hoTen VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    ngayTao DATE,
    maVT INT,
    FOREIGN KEY (maVT) REFERENCES VaiTro(maVT)
);

INSERT INTO NguoiDung(hoTen,email,ngayTao,maVT) VALUES
('Nguyễn Văn A','a@gmail.com','2024-01-01',1),
('Trần Thị B','b@gmail.com','2024-01-02',2),
('Lê Văn C','c@gmail.com','2024-01-03',3),
('Phạm Thị D','d@gmail.com','2024-01-04',4),
('Hoàng Gia E','e@gmail.com','2024-01-05',5);

-- ============================
-- BẢNG TaiKhoan
-- ============================
CREATE TABLE TaiKhoan (
    username VARCHAR(50) PRIMARY KEY,
    password VARCHAR(100) NOT NULL,
    trangThai VARCHAR(20),
    maND INT,
    FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
);

INSERT INTO TaiKhoan(username,password,trangThai,maND) VALUES
('admin','123','Hoạt động',1),
('giaovien','123','Hoạt động',2),
('nhanvien','123','Khoá',3),
('totruong','123','Hoạt động',4),
('bgiamhieu','123','Hoạt động',5);

-- ============================
-- BẢNG MonHoc
-- ============================
CREATE TABLE MonHoc (
    maMH INT AUTO_INCREMENT PRIMARY KEY,
    tenMonHoc VARCHAR(100) NOT NULL
);

INSERT INTO MonHoc(tenMonHoc) VALUES
('Toán'), ('Lý'), ('Hóa'), ('Tin học'), ('Sinh học');

-- ============================
-- BẢNG ThietBi
-- ============================
CREATE TABLE ThietBi (
    maTB INT AUTO_INCREMENT PRIMARY KEY,
    tenTB VARCHAR(100) NOT NULL,
    soLuong INT,
    donVi VARCHAR(20),
    tinhTrang VARCHAR(50),
    lop VARCHAR(20),
    maMH INT,
    FOREIGN KEY (maMH) REFERENCES MonHoc(maMH)
);

INSERT INTO ThietBi(tenTB,soLuong,donVi,tinhTrang,lop,maMH) VALUES
('Máy chiếu',3,'cái','Tốt','9A1',1),
('Máy tính',10,'bộ','Tốt','Tin1',4),
('Loa kéo',2,'cái','Hư nhẹ','Âm nhạc',2),
('Camera',1,'cái','Tốt','9A2',1),
('Micro',5,'cái','Bình thường','Nhạc1',5);

-- ============================
-- BẢNG PhieuMuon (dùng maND)
-- ============================
CREATE TABLE PhieuMuon (
    maPhieu INT AUTO_INCREMENT PRIMARY KEY,
    ngayLap DATE,
    ngayMuon DATE,
    ngayTraDuKien DATE,
    soLuong INT,
    trangThai VARCHAR(50),
    ghiChu VARCHAR(200),
    maND INT,
    FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
);

INSERT INTO PhieuMuon(ngayLap,ngayMuon,ngayTraDuKien,soLuong,trangThai,ghiChu,maND) VALUES
('2024-02-01','2024-02-02','2024-02-05',1,'Đang mượn','Mượn máy chiếu',2),
('2024-02-03','2024-02-04','2024-02-07',2,'Đang mượn','Mượn micro',3),
('2024-02-05','2024-02-06','2024-02-08',1,'Đã trả','Trả đúng hạn',1),
('2024-02-06','2024-02-06','2024-02-09',1,'Đang mượn','Mượn loa',4),
('2024-02-07','2024-02-08','2024-02-11',1,'Chờ duyệt','Đợi kiểm tra',5);

-- ============================
-- BẢNG ChiTietPhieuMuon
-- ============================
CREATE TABLE ChiTietPhieuMuon (
    maCT INT AUTO_INCREMENT PRIMARY KEY,
    maPhieu INT,
    maTB INT,
    soLuong INT DEFAULT 1,
    ghiChu VARCHAR(200),
    FOREIGN KEY (maPhieu) REFERENCES PhieuMuon(maPhieu),
    FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
);

INSERT INTO ChiTietPhieuMuon(maPhieu,maTB,soLuong,ghiChu) VALUES
(1,1,1,'Máy chiếu lớp 9A'),
(2,5,2,'Micro cho tiết nhạc'),
(3,2,1,'Mượn máy tính'),
(4,3,1,'Loa kéo'),
(5,4,1,'Camera giám sát');

-- ============================
-- BẢNG BangGhiLog
-- ============================
CREATE TABLE BangGhiLog (
    maLog INT AUTO_INCREMENT PRIMARY KEY,
    thoiGian DATETIME,
    nguoiDung INT,
    hanhDong VARCHAR(100),
    doiTuong VARCHAR(100),
    FOREIGN KEY (nguoiDung) REFERENCES NguoiDung(maND)
);

INSERT INTO BangGhiLog(thoiGian,nguoiDung,hanhDong,doiTuong) VALUES
(NOW(),1,'Đăng nhập','Hệ thống'),
(NOW(),2,'Tạo phiếu mượn','PhieuMuon'),
(NOW(),3,'Cập nhật thiết bị','ThietBi'),
(NOW(),4,'Xoá tài khoản','TaiKhoan'),
(NOW(),5,'Tạo báo cáo','BaoCao');

-- ============================
-- BẢNG KeHoachMuaSam
-- ============================
CREATE TABLE KeHoachMuaSam (
    maMS INT AUTO_INCREMENT PRIMARY KEY,
    ngayLap DATE,
    noiDung VARCHAR(200),
    trangThai VARCHAR(50)
);

INSERT INTO KeHoachMuaSam(ngayLap,noiDung,trangThai) VALUES
('2024-03-01','Mua máy chiếu','Chờ duyệt'),
('2024-03-02','Mua máy tính','Đã duyệt'),
('2024-03-03','Mua camera','Đang xử lý'),
('2024-03-04','Mua micro','Đã duyệt'),
('2024-03-05','Mua loa','Chờ duyệt');

-- ============================
-- BẢNG ChiTietMuaSam
-- ============================
CREATE TABLE ChiTietMuaSam (
    maCTMS INT AUTO_INCREMENT PRIMARY KEY,
    maKH INT,
    maTB INT,
    ghiChu VARCHAR(200),
    FOREIGN KEY (maKH) REFERENCES KeHoachMuaSam(maMS),
    FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
);

INSERT INTO ChiTietMuaSam(maKH,maTB,ghiChu) VALUES
(1,1,'Thêm 1 máy chiếu'),
(2,2,'Thêm 2 bộ máy tính'),
(3,4,'Camera giám sát'),
(4,5,'Micro không dây'),
(5,3,'Loa bổ sung');

-- ============================
-- BẢNG KeHoachThanhLy
-- ============================
CREATE TABLE KeHoachThanhLy (
    maTL INT AUTO_INCREMENT PRIMARY KEY,
    ngayLap DATE,
    ghiChu VARCHAR(200)
);

INSERT INTO KeHoachThanhLy(ngayLap,ghiChu) VALUES
('2024-04-01','Thanh lý hư hỏng'),
('2024-04-02','Thanh lý tồn kho'),
('2024-04-03','Thanh lý micro cũ'),
('2024-04-04','Thanh lý máy tính lỗi'),
('2024-04-05','Thanh lý cuối năm');

-- ============================
-- BẢNG ChiTietThanhLy
-- ============================
CREATE TABLE ChiTietThanhLy (
    maCTTL INT AUTO_INCREMENT PRIMARY KEY,
    maTL INT,
    maTB INT,
    lyDo VARCHAR(200),
    FOREIGN KEY (maTL) REFERENCES KeHoachThanhLy(maTL),
    FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
);

INSERT INTO ChiTietThanhLy(maTL,maTB,lyDo) VALUES
(1,3,'Loa hỏng'),
(2,1,'Máy chiếu mờ'),
(3,5,'Micro yếu'),
(4,2,'Máy tính lỗi'),
(5,4,'Camera cũ');

-- ============================
-- BẢNG KiemKe (PHẦN KIỂM KÊ)
-- ============================
CREATE TABLE KiemKe (
    maKK INT AUTO_INCREMENT PRIMARY KEY,
    ngayKK DATE,
    maND INT,
    ghiChu VARCHAR(200),
    FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
);

INSERT INTO KiemKe(ngayKK,maND,ghiChu) VALUES
('2024-05-01',1,'Kiểm kê đầu kỳ'),
('2024-05-02',2,'Kiểm kê thiết bị Tin'),
('2024-05-03',3,'Kiểm kê phòng Lab'),
('2024-05-04',4,'Kiểm kê nhạc cụ'),
('2024-05-05',5,'Kiểm kê cuối năm');

-- ============================
-- BẢNG ChiTietKiemKe
-- ============================
CREATE TABLE ChiTietKiemKe (
    maCTKK INT AUTO_INCREMENT PRIMARY KEY,
    maKK INT,
    maTB INT,
    soLuongTruoc INT,
    soLuongThucTe INT,
    thongTinThem VARCHAR(200),
    FOREIGN KEY (maKK) REFERENCES KiemKe(maKK),
    FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
);

INSERT INTO ChiTietKiemKe(maKK,maTB,soLuongTruoc,soLuongThucTe,thongTinThem) VALUES
(1,1,3,3,'OK'),
(2,2,10,9,'Thiếu 1 máy'),
(3,3,2,2,'OK'),
(4,5,5,4,'Thiếu 1 micro'),
(5,4,1,1,'OK');

-- ============================
-- BẢNG BaoCao (PHẦN LẬP BÁO CÁO)
-- ============================
CREATE TABLE BaoCao (
    maBC INT AUTO_INCREMENT PRIMARY KEY,
    ngayLap DATE,
    tieuDe VARCHAR(200),
    nguoiLap INT,
    ghiChu VARCHAR(200),
    FOREIGN KEY (nguoiLap) REFERENCES NguoiDung(maND)
);

INSERT INTO BaoCao(ngayLap,tieuDe,nguoiLap,ghiChu) VALUES
('2024-06-01','Báo cáo tháng 6',1,'Không có vấn đề'),
('2024-06-02','Báo cáo thiết bị hỏng',2,'3 thiết bị hỏng'),
('2024-06-03','Báo cáo tồn kho',3,'Kho ổn định'),
('2024-06-04','Báo cáo mượn trả',4,'Số lượng lớn'),
('2024-06-05','Báo cáo tổng hợp',5,'Đã hoàn thành');

-- ============================
-- BẢNG ChiTietBaoCao
-- ============================
CREATE TABLE ChiTietBaoCao (
    maCTBC INT AUTO_INCREMENT PRIMARY KEY,
    maBC INT,
    noiDung TEXT,
    FOREIGN KEY (maBC) REFERENCES BaoCao(maBC)
);

INSERT INTO ChiTietBaoCao(maBC,noiDung) VALUES
(1,'Tình hình ổn định.'),
(2,'Ghi nhận 3 thiết bị hỏng.'),
(3,'Tồn kho đầy đủ.'),
(4,'Mượn trả tăng cao.'),
(5,'Tổng hợp toàn trường.');
