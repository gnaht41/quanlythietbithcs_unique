-- =====================================================
-- DATABASE
-- =====================================================
DROP DATABASE IF EXISTS qltb;
CREATE DATABASE qltb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qltb;

-- =====================================================
-- VAI TRÒ
-- =====================================================
CREATE TABLE VaiTro (
  maVT INT AUTO_INCREMENT PRIMARY KEY,
  tenVT VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO VaiTro(tenVT) VALUES
('Admin'),
('Giáo viên'),
('Nhân viên'),
('Tổ trưởng'),
('Ban giám hiệu');

-- =====================================================
-- NGƯỜI DÙNG
-- =====================================================
CREATE TABLE NguoiDung (
  maND INT AUTO_INCREMENT PRIMARY KEY,
  hoTen VARCHAR(100) NOT NULL,
  email VARCHAR(150),
  maVT INT,
  FOREIGN KEY (maVT) REFERENCES VaiTro(maVT)
);

INSERT INTO NguoiDung(hoTen,email,maVT) VALUES
('Admin Hệ Thống','admin@thcs.edu.vn',1),
('Nguyễn Văn Giáo','giaovien@thcs.edu.vn',2),
('Trần Thị Thiết Bị','nhanvien@thcs.edu.vn',3),
('Lê Văn Tổ Trưởng','totruong@thcs.edu.vn',4),
('Phạm Thị Hiệu Trưởng','hieutruong@thcs.edu.vn',5);

-- =====================================================
-- TÀI KHOẢN (PASS = 123)
-- =====================================================
CREATE TABLE TaiKhoan (
  username VARCHAR(50) PRIMARY KEY,
  password VARCHAR(50) NOT NULL,
  trangThai ENUM('Hoạt động','Khoá') DEFAULT 'Hoạt động',
  maND INT UNIQUE,
  FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
);

INSERT INTO TaiKhoan(username,password,trangThai,maND) VALUES
('admin','123','Hoạt động',1),
('giaovien','123','Hoạt động',2),
('nhanvien','123','Hoạt động',3),
('totruong','123','Hoạt động',4),
('hieutruong','123','Hoạt động',5);

-- =====================================================
-- MÔN HỌC
-- =====================================================
CREATE TABLE MonHoc (
  maMH INT AUTO_INCREMENT PRIMARY KEY,
  tenMonHoc VARCHAR(100) NOT NULL
);

INSERT INTO MonHoc(tenMonHoc) VALUES
('Toán'),
('Tin học'),
('Vật lý'),
('Âm nhạc');

-- =====================================================
-- THIẾT BỊ
-- =====================================================
CREATE TABLE ThietBi (
  maTB INT AUTO_INCREMENT PRIMARY KEY,
  tenTB VARCHAR(150) NOT NULL,
  soLuongTong INT DEFAULT 0,
  soLuongKhaDung INT DEFAULT 0,
  tinhTrang ENUM('Tốt','Hư nhẹ','Hư nặng','Đang sửa') DEFAULT 'Tốt',
  maMH INT,
  isHidden BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (maMH) REFERENCES MonHoc(maMH)
);

INSERT INTO ThietBi(tenTB,soLuongTong,soLuongKhaDung,tinhTrang,maMH) VALUES
('Máy chiếu',3,3,'Tốt',1),
('Máy tính',10,10,'Tốt',2),
('Loa kéo',2,2,'Hư nhẹ',4),
('Micro',5,5,'Tốt',4),
('Camera',1,1,'Tốt',2);

-- =====================================================
-- PHIẾU MƯỢN
-- =====================================================
CREATE TABLE PhieuMuon (
  maPhieu INT AUTO_INCREMENT PRIMARY KEY,
  ngayMuon DATE,
  ngayTraDuKien DATE,
  ngayTraThucTe DATE,
  trangThai ENUM(
    'Chờ duyệt',
    'Đã duyệt',
    'Đang mượn',
    'Đã trả',
    'Đã hủy',
    'Từ chối'
  ) DEFAULT 'Chờ duyệt',
  maND INT,
  FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
);

INSERT INTO PhieuMuon(ngayMuon,ngayTraDuKien,ngayTraThucTe,trangThai,maND) VALUES
('2024-05-01','2024-05-03','2024-05-03','Đã trả',2),
('2024-05-02','2024-05-04','2024-05-04','Đã trả',2),
('2024-06-01','2024-06-03',NULL,'Đang mượn',2);

-- =====================================================
-- CHI TIẾT PHIẾU MƯỢN
-- =====================================================
CREATE TABLE ChiTietPhieuMuon (
  maCT INT AUTO_INCREMENT PRIMARY KEY,
  maPhieu INT,
  maTB INT,
  soLuong INT,
  tinhTrangKhiTra ENUM('Tốt','Hư','Mất'),
  FOREIGN KEY (maPhieu) REFERENCES PhieuMuon(maPhieu),
  FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
);

INSERT INTO ChiTietPhieuMuon VALUES
(1,1,1,1,'Tốt'),
(2,1,4,1,'Hư'),
(3,2,2,2,'Tốt'),
(4,3,3,1,NULL);

-- =====================================================
-- KIỂM KÊ
-- =====================================================
CREATE TABLE KiemKe (
  maKK INT AUTO_INCREMENT PRIMARY KEY,
  ngayKK DATE,
  loaiKiemKe ENUM('Cuối kỳ','Cuối năm','Đột xuất') DEFAULT 'Cuối kỳ',
  maND INT,
  FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
);

INSERT INTO KiemKe(ngayKK,loaiKiemKe,maND) VALUES
('2024-05-31','Cuối kỳ',3),
('2024-06-30','Cuối năm',3);

-- =====================================================
-- CHI TIẾT KIỂM KÊ
-- =====================================================
CREATE TABLE ChiTietKiemKe (
  maCTKK INT AUTO_INCREMENT PRIMARY KEY,
  maKK INT,
  maTB INT,
  soLuongTruoc INT,
  soLuongThucTe INT,
  FOREIGN KEY (maKK) REFERENCES KiemKe(maKK),
  FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
);

INSERT INTO ChiTietKiemKe VALUES
(1,1,1,3,3),
(2,1,2,10,9),
(3,1,3,2,2),
(4,2,1,3,2),
(5,2,4,5,4);

-- =====================================================
-- KẾ HOẠCH MUA SẮM
-- =====================================================
CREATE TABLE KeHoachMuaSam (
  maMS INT AUTO_INCREMENT PRIMARY KEY,
  ngayLap DATE,
  trangThai ENUM('Chờ duyệt','Đã duyệt','Từ chối') DEFAULT 'Chờ duyệt',
  nguoiLap INT,
  nguoiDuyet INT,
  FOREIGN KEY (nguoiLap) REFERENCES NguoiDung(maND),
  FOREIGN KEY (nguoiDuyet) REFERENCES NguoiDung(maND)
);

CREATE TABLE ChiTietMuaSam (
  maCTMS INT AUTO_INCREMENT PRIMARY KEY,
  maKH INT,
  maTB INT,
  soLuong INT,
  FOREIGN KEY (maKH) REFERENCES KeHoachMuaSam(maMS),
  FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
);

INSERT INTO KeHoachMuaSam(ngayLap,trangThai,nguoiLap) VALUES
('2024-06-05','Đã duyệt',4),
('2024-06-20','Chờ duyệt',4);

INSERT INTO ChiTietMuaSam VALUES
(1,1,2,3),
(2,1,1,1),
(3,2,4,2);

-- =====================================================
-- KẾ HOẠCH THANH LÝ
-- =====================================================
CREATE TABLE KeHoachThanhLy (
  maTL INT AUTO_INCREMENT PRIMARY KEY,
  ngayLap DATE,
  trangThai ENUM('Chờ duyệt','Đã duyệt','Từ chối') DEFAULT 'Chờ duyệt',
  nguoiLap INT,
  nguoiDuyet INT,
  FOREIGN KEY (nguoiLap) REFERENCES NguoiDung(maND),
  FOREIGN KEY (nguoiDuyet) REFERENCES NguoiDung(maND)
);

-- =====================================================
-- NHẬT KÝ HỆ THỐNG (PHP GHI)
-- =====================================================
CREATE TABLE BangGhiLog (
  maLog INT AUTO_INCREMENT PRIMARY KEY,
  thoiGian DATETIME DEFAULT CURRENT_TIMESTAMP,
  maND INT,
  hanhDong VARCHAR(30),
  doiTuong VARCHAR(50),
  doiTuongId INT,
  ghiChu TEXT,
  FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
);

INSERT INTO BangGhiLog(maND,hanhDong,doiTuong,doiTuongId,ghiChu) VALUES
(1,'LOGIN','TaiKhoan',1,'Admin đăng nhập'),
(2,'INSERT','PhieuMuon',1,'Giáo viên gửi yêu cầu mượn'),
(3,'INSERT','KiemKe',1,'Nhân viên tạo đợt kiểm kê');
