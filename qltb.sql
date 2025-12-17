-- =====================================================
-- RESET DATABASE
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

INSERT INTO VaiTro VALUES
(1,'Admin'),
(2,'Giáo viên'),
(3,'Nhân viên'),
(4,'Tổ trưởng'),
(5,'Ban giám hiệu');

-- =====================================================
-- NGƯỜI DÙNG
-- =====================================================
CREATE TABLE NguoiDung (
  maND INT AUTO_INCREMENT PRIMARY KEY,
  hoTen VARCHAR(100) NOT NULL,
  email VARCHAR(150),
  maVT INT NOT NULL,
  FOREIGN KEY (maVT) REFERENCES VaiTro(maVT)
);

INSERT INTO NguoiDung VALUES
(1,'Quản trị hệ thống','admin@thcs.edu.vn',1),
(2,'Nguyễn Văn Giáo','giaovien@thcs.edu.vn',2),
(3,'Trần Thị Thiết Bị','nhanvien@thcs.edu.vn',3),
(4,'Lê Văn Tổ Trưởng','totruong@thcs.edu.vn',4),
(5,'Phạm Thị Hiệu Trưởng','hieutruong@thcs.edu.vn',5);

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

INSERT INTO TaiKhoan VALUES
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

INSERT INTO MonHoc VALUES
(1,'Toán'),
(2,'Tin học'),
(3,'Vật lý'),
(4,'Âm nhạc'),
(5,'Sinh học');

-- =====================================================
-- THIẾT BỊ (CÓ ĐƠN VỊ + KHỐI LỚP 6–9)
-- =====================================================
CREATE TABLE ThietBi (
  maTB INT AUTO_INCREMENT PRIMARY KEY,
  tenTB VARCHAR(150) NOT NULL,
  donVi VARCHAR(20),
  lop ENUM('6','7','8','9'),
  soLuongTong INT DEFAULT 0,
  soLuongKhaDung INT DEFAULT 0,
  tinhTrang ENUM('Tốt','Hư nhẹ','Hư nặng','Đang sửa') DEFAULT 'Tốt',
  maMH INT,
  isHidden BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (maMH) REFERENCES MonHoc(maMH)
);

INSERT INTO ThietBi VALUES
(1,'Máy chiếu Epson EB-X06','Cái','6',3,3,'Tốt',1,0),
(2,'Máy tính để bàn','Bộ','8',10,9,'Tốt',2,0),
(3,'Loa kéo','Cái','7',2,1,'Hư nhẹ',4,0),
(4,'Micro không dây','Cái','9',5,4,'Tốt',4,0),
(5,'Camera quan sát','Cái','8',1,1,'Tốt',2,0);

-- =====================================================
-- PHIẾU MƯỢN
-- =====================================================
CREATE TABLE PhieuMuon (
  maPhieu INT AUTO_INCREMENT PRIMARY KEY,
  ngayMuon DATE,
  ngayTraDuKien DATE,
  ngayTraThucTe DATE,
  trangThai ENUM('Chờ duyệt','Đã duyệt','Đang mượn','Đã trả','Đã hủy','Từ chối'),
  maND INT,
  FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
);

INSERT INTO PhieuMuon VALUES
(1,'2024-05-01','2024-05-03','2024-05-03','Đã trả',2),
(2,'2024-06-01','2024-06-03',NULL,'Đang mượn',2),
(3,'2024-06-05','2024-06-07',NULL,'Chờ duyệt',2),
(4,'2024-06-10','2024-06-12',NULL,'Từ chối',2),
(5,'2024-06-15','2024-06-17',NULL,'Đã hủy',2);

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
(3,2,2,1,NULL),
(4,3,3,1,NULL),
(5,4,5,1,NULL);

-- =====================================================
-- KIỂM KÊ
-- =====================================================
CREATE TABLE KiemKe (
  maKK INT AUTO_INCREMENT PRIMARY KEY,
  ngayKK DATE,
  loaiKiemKe ENUM('Cuối kỳ','Cuối năm','Đột xuất'),
  maND INT,
  FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
);

INSERT INTO KiemKe VALUES
(1,'2024-05-31','Cuối kỳ',3),
(2,'2024-06-30','Cuối năm',3),
(3,'2024-07-15','Đột xuất',3),
(4,'2024-08-31','Cuối kỳ',3),
(5,'2024-12-31','Cuối năm',3);

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
(3,2,3,2,1),
(4,2,4,5,4),
(5,3,5,1,1);

-- =====================================================
-- KẾ HOẠCH MUA SẮM
-- =====================================================
CREATE TABLE KeHoachMuaSam (
  maMS INT AUTO_INCREMENT PRIMARY KEY,
  ngayLap DATE,
  trangThai ENUM('Chờ duyệt','Đã duyệt','Từ chối'),
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

INSERT INTO KeHoachMuaSam VALUES
(1,'2024-06-20','Đã duyệt',4,5),
(2,'2024-07-10','Chờ duyệt',4,NULL),
(3,'2024-08-01','Từ chối',4,5),
(4,'2024-09-01','Đã duyệt',4,5),
(5,'2024-10-01','Chờ duyệt',4,NULL);

INSERT INTO ChiTietMuaSam VALUES
(1,1,2,3),
(2,1,1,1),
(3,2,4,2),
(4,3,3,1),
(5,4,5,1);

-- =====================================================
-- KẾ HOẠCH THANH LÝ
-- =====================================================
CREATE TABLE KeHoachThanhLy (
  maTL INT AUTO_INCREMENT PRIMARY KEY,
  ngayLap DATE,
  trangThai ENUM('Chờ duyệt','Đã duyệt','Từ chối'),
  nguoiLap INT,
  nguoiDuyet INT,
  FOREIGN KEY (nguoiLap) REFERENCES NguoiDung(maND),
  FOREIGN KEY (nguoiDuyet) REFERENCES NguoiDung(maND)
);

CREATE TABLE ChiTietThanhLy (
  maCTTL INT AUTO_INCREMENT PRIMARY KEY,
  maTL INT,
  maTB INT,
  soLuong INT,
  lyDo VARCHAR(255),
  tinhTrang ENUM('Hư nhẹ','Hư nặng','Mất','Không sửa được'),
  FOREIGN KEY (maTL) REFERENCES KeHoachThanhLy(maTL),
  FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
);

INSERT INTO KeHoachThanhLy VALUES
(1,'2024-06-25','Đã duyệt',3,5),
(2,'2024-07-20','Chờ duyệt',3,NULL),
(3,'2024-08-15','Từ chối',3,5),
(4,'2024-09-10','Đã duyệt',3,5),
(5,'2024-10-05','Chờ duyệt',3,NULL);

INSERT INTO ChiTietThanhLy VALUES
(1,1,3,1,'Loa hư không sửa được','Hư nặng'),
(2,1,4,1,'Micro bị mất','Mất'),
(3,2,2,1,'Máy tính lỗi','Hư nhẹ'),
(4,4,1,1,'Máy chiếu cũ','Không sửa được'),
(5,5,5,1,'Camera hỏng','Hư nặng');

-- =====================================================
-- NHẬT KÝ HỆ THỐNG
-- =====================================================
CREATE TABLE BangGhiLog (
  maLog INT AUTO_INCREMENT PRIMARY KEY,
  thoiGian DATETIME DEFAULT CURRENT_TIMESTAMP,
  maND INT,
  hanhDong VARCHAR(50),
  doiTuong VARCHAR(50),
  doiTuongId INT,
  FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
);

INSERT INTO BangGhiLog VALUES
(1,NOW(),1,'LOGIN','TaiKhoan',1),
(2,NOW(),2,'INSERT','PhieuMuon',1),
(3,NOW(),3,'INSERT','KiemKe',1),
(4,NOW(),4,'INSERT','KeHoachMuaSam',1),
(5,NOW(),5,'DUYET','KeHoachThanhLy',1);
