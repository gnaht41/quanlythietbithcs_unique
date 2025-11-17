-- TẠO DATABASE (nếu chưa có)
CREATE DATABASE IF NOT EXISTS qltb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qltb;

-- ============================
-- BẢNG VaiTro
-- ============================
CREATE TABLE IF NOT EXISTS VaiTro (
  maVT INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tenVT VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO VaiTro(tenVT) VALUES
('Admin'), ('Giáo viên'), ('Nhân viên'), ('Tổ trưởng'), ('Ban giám hiệu');

-- ============================
-- BẢNG NguoiDung
-- ============================
CREATE TABLE IF NOT EXISTS NguoiDung (
  maND INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  hoTen VARCHAR(120) NOT NULL,
  email VARCHAR(150),
  ngayTao DATE DEFAULT CURRENT_DATE,
  maVT INT UNSIGNED,
  CONSTRAINT fk_nguoidung_vaitro FOREIGN KEY (maVT) REFERENCES VaiTro(maVT)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO NguoiDung(hoTen,email,ngayTao,maVT) VALUES
('Nguyễn Văn A','a@gmail.com','2024-01-01',1),
('Trần Thị B','b@gmail.com','2024-01-02',2),
('Lê Văn C','c@gmail.com','2024-01-03',3),
('Phạm Thị D','d@gmail.com','2024-01-04',4),
('Hoàng Gia E','e@gmail.com','2024-01-05',5);

-- ============================
-- BẢNG TaiKhoan
-- ============================
CREATE TABLE IF NOT EXISTS TaiKhoan (
  username VARCHAR(50) PRIMARY KEY,
  password VARCHAR(255) NOT NULL,
  trangThai VARCHAR(20) DEFAULT 'Hoạt động',
  maND INT UNSIGNED,
  CONSTRAINT fk_taikhoan_nguoidung FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO TaiKhoan(username,password,trangThai,maND) VALUES
('admin','123','Hoạt động',1),
('giaovien','123','Hoạt động',2),
('nhanvien','123','Khoá',3),
('totruong','123','Hoạt động',4),
('bgiamhieu','123','Hoạt động',5);

-- ============================
-- BẢNG MonHoc
-- ============================
CREATE TABLE IF NOT EXISTS MonHoc (
  maMH INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tenMonHoc VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO MonHoc(tenMonHoc) VALUES
('Toán'), ('Lý'), ('Hóa'), ('Tin học'), ('Sinh học');

-- ============================
-- BẢNG ThietBi
-- ============================
CREATE TABLE IF NOT EXISTS ThietBi (
  maTB INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tenTB VARCHAR(100) NOT NULL,
  soLuong INT UNSIGNED DEFAULT 0,
  donVi VARCHAR(20),
  tinhTrang VARCHAR(50),
  lop VARCHAR(20),
  maMH INT UNSIGNED,
  ngayNhap DATE,
  ngayThanhLy DATE,
  ghiChu VARCHAR(255),
  CONSTRAINT fk_thietbi_monhoc FOREIGN KEY (maMH) REFERENCES MonHoc(maMH)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ThietBi(tenTB,soLuong,donVi,tinhTrang,lop,maMH) VALUES
('Máy chiếu',3,'cái','Tốt','9A1',1),
('Máy tính',10,'bộ','Tốt','Tin1',4),
('Loa kéo',2,'cái','Hư nhẹ','Âm nhạc',2),
('Camera',1,'cái','Tốt','9A2',1),
('Micro',5,'cái','Bình thường','Nhạc1',5);

-- ============================
-- BẢNG PhieuMuon
-- ============================
CREATE TABLE IF NOT EXISTS PhieuMuon (
  maPhieu INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ngayLap DATE DEFAULT CURRENT_DATE,
  ngayMuon DATE,
  ngayTraDuKien DATE,
  soLuong INT UNSIGNED DEFAULT 0,
  trangThai VARCHAR(50),
  ghiChu VARCHAR(200),
  maND INT UNSIGNED NOT NULL,
  CONSTRAINT fk_phieumuon_nguoidung FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO PhieuMuon(ngayLap,ngayMuon,ngayTraDuKien,soLuong,trangThai,ghiChu,maND) VALUES
('2024-02-01','2024-02-02','2024-02-05',1,'Đang mượn','Mượn máy chiếu',2),
('2024-02-03','2024-02-04','2024-02-07',2,'Đang mượn','Mượn micro',3),
('2024-02-05','2024-02-06','2024-02-08',1,'Đã trả','Trả đúng hạn',1),
('2024-02-06','2024-02-06','2024-02-09',1,'Đang mượn','Mượn loa',4),
('2024-02-07','2024-02-08','2024-02-11',1,'Chờ duyệt','Đợi kiểm tra',5);

-- ============================
-- BẢNG ChiTietPhieuMuon
-- ============================
CREATE TABLE IF NOT EXISTS ChiTietPhieuMuon (
  maCT INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maPhieu INT UNSIGNED NOT NULL,
  maTB INT UNSIGNED NOT NULL,
  soLuong INT UNSIGNED NOT NULL DEFAULT 1,
  ghiChu VARCHAR(200),
  UNIQUE KEY ux_phieu_tb (maPhieu, maTB),
  CONSTRAINT fk_ctpm_phieu FOREIGN KEY (maPhieu) REFERENCES PhieuMuon(maPhieu)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ctpm_tb FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ChiTietPhieuMuon(maPhieu,maTB,soLuong,ghiChu) VALUES
(1,1,1,'Máy chiếu lớp 9A'),
(1,2,2,'2 bộ máy tính cho phòng Tin'),
(2,5,2,'Micro cho tiết nhạc'),
(3,2,1,'Mượn máy tính'),
(4,3,1,'Loa kéo');

-- ============================
-- BẢNG KeHoachMuaSam + ChiTietMuaSam
-- ============================
CREATE TABLE IF NOT EXISTS KeHoachMuaSam (
  maMS INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ngayLap DATE,
  noiDung VARCHAR(200),
  trangThai VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO KeHoachMuaSam(ngayLap,noiDung,trangThai) VALUES
('2024-03-01','Mua máy chiếu','Chờ duyệt'),
('2024-03-02','Mua máy tính','Đã duyệt'),
('2024-03-03','Mua camera','Đang xử lý'),
('2024-03-04','Mua micro','Đã duyệt'),
('2024-03-05','Mua loa','Chờ duyệt');

CREATE TABLE IF NOT EXISTS ChiTietMuaSam (
  maCTMS INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maKH INT UNSIGNED NOT NULL,
  maTB INT UNSIGNED NOT NULL,
  soLuong INT UNSIGNED NOT NULL DEFAULT 1,
  donGia DECIMAL(12,2) DEFAULT 0,
  ghiChu VARCHAR(200),
  CONSTRAINT fk_ctms_kehoach FOREIGN KEY (maKH) REFERENCES KeHoachMuaSam(maMS)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ctms_tb FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  UNIQUE KEY ux_ctms_kh_tb (maKH, maTB)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ChiTietMuaSam(maKH,maTB,soLuong,donGia,ghiChu) VALUES
(1,1,1,1500000,'Thêm 1 máy chiếu'),
(2,2,2,12000000,'Thêm 2 bộ máy tính'),
(3,4,1,2000000,'Camera giám sát'),
(4,5,4,500000,'Micro không dây'),
(5,3,2,800000,'Loa bổ sung');

-- ============================
-- BẢNG KeHoachThanhLy + ChiTietThanhLy
-- ============================
CREATE TABLE IF NOT EXISTS KeHoachThanhLy (
  maTL INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ngayLap DATE,
  ghiChu VARCHAR(200)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO KeHoachThanhLy(ngayLap,ghiChu) VALUES
('2024-04-01','Thanh lý hư hỏng'),
('2024-04-02','Thanh lý tồn kho'),
('2024-04-03','Thanh lý micro cũ'),
('2024-04-04','Thanh lý máy tính lỗi'),
('2024-04-05','Thanh lý cuối năm');

CREATE TABLE IF NOT EXISTS ChiTietThanhLy (
  maCTTL INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maTL INT UNSIGNED NOT NULL,
  maTB INT UNSIGNED NOT NULL,
  lyDo VARCHAR(200),
  tinhTrangKhiThanhLy VARCHAR(100),
  giaTriConLai DECIMAL(12,2) DEFAULT 0,
  CONSTRAINT fk_cttl_tl FOREIGN KEY (maTL) REFERENCES KeHoachThanhLy(maTL)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cttl_tb FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  UNIQUE KEY ux_cttl_tl_tb (maTL, maTB)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ChiTietThanhLy(maTL,maTB,lyDo,tinhTrangKhiThanhLy,giaTriConLai) VALUES
(1,3,'Loa hỏng','Hư nặng',0),
(2,1,'Máy chiếu mờ','Hư nhẹ',200000),
(3,5,'Micro yếu','Yếu',50000),
(4,2,'Máy tính lỗi','Ổ cứng hỏng',1000000),
(5,4,'Camera cũ','Cũ',0);

-- ============================
-- BẢNG KiemKe + ChiTietKiemKe
-- ============================
CREATE TABLE IF NOT EXISTS KiemKe (
  maKK INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ngayKK DATE,
  maND INT UNSIGNED NOT NULL,
  ghiChu VARCHAR(200),
  CONSTRAINT fk_kiemke_nguoidung FOREIGN KEY (maND) REFERENCES NguoiDung(maND)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO KiemKe(ngayKK,maND,ghiChu) VALUES
('2024-05-01',1,'Kiểm kê đầu kỳ'),
('2024-05-02',2,'Kiểm kê thiết bị Tin'),
('2024-05-03',3,'Kiểm kê phòng Lab'),
('2024-05-04',4,'Kiểm kê nhạc cụ'),
('2024-05-05',5,'Kiểm kê cuối năm');

CREATE TABLE IF NOT EXISTS ChiTietKiemKe (
  maCTKK INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maKK INT UNSIGNED NOT NULL,
  maTB INT UNSIGNED NOT NULL,
  soLuongTruoc INT UNSIGNED DEFAULT 0,
  soLuongThucTe INT UNSIGNED DEFAULT 0,
  thongTinThem VARCHAR(200),
  CONSTRAINT fk_ctkk_kk FOREIGN KEY (maKK) REFERENCES KiemKe(maKK)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ctkk_tb FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  UNIQUE KEY ux_ctkk_kk_tb (maKK, maTB)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ChiTietKiemKe(maKK,maTB,soLuongTruoc,soLuongThucTe,thongTinThem) VALUES
(1,1,3,3,'OK'),
(2,2,10,9,'Thiếu 1 máy'),
(3,3,2,2,'OK'),
(4,5,5,4,'Thiếu 1 micro'),
(5,4,1,1,'OK');

-- ============================
-- BẢNG ReportType, BaoCao, ChiTietBaoCao
-- ============================
CREATE TABLE IF NOT EXISTS ReportType (
  maLoai INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tenLoai VARCHAR(100) NOT NULL,
  moTa VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ReportType(tenLoai,moTa) VALUES
('Tần suất mượn','Thống kê số lần mượn theo giáo viên / thiết bị / khoảng thời gian'),
('Tình trạng thiết bị','Thống kê Tốt/Hỏng/Đang sửa phục vụ kiểm kê'),
('Kế hoạch mua sắm','Tổng hợp nhu cầu để lập kế hoạch mua sắm'),
('Kiểm kê','Báo cáo kiểm kê'),
('Tùy chỉnh','Báo cáo do người dùng cấu hình');

CREATE TABLE IF NOT EXISTS BaoCao (
  maBC INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ngayLap DATE DEFAULT CURRENT_DATE,
  maLoai INT UNSIGNED NOT NULL,
  tieuDe VARCHAR(200),
  tuNgay DATE,
  denNgay DATE,
  nguoiLap INT UNSIGNED NOT NULL,
  trangThai VARCHAR(50) DEFAULT 'Chờ',
  ghiChu VARCHAR(500),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_baocao_loai FOREIGN KEY (maLoai) REFERENCES ReportType(maLoai)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_baocao_nguoidung FOREIGN KEY (nguoiLap) REFERENCES NguoiDung(maND)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO BaoCao(ngayLap,maLoai,tieuDe,tuNgay,denNgay,nguoiLap,trangThai,ghiChu) VALUES
('2024-06-01',1,'Thống kê mượn tháng 6','2024-06-01','2024-06-30',1,'Hoàn thành','Tổng hợp tần suất mượn'),
('2024-06-02',2,'Thiết bị hỏng tháng 6','2024-06-01','2024-06-30',2,'Hoàn thành','3 thiết bị hỏng'),
('2024-06-03',3,'Kế hoạch mua sắm năm học 2024','2024-07-01','2024-07-31',3,'Chờ','Dự thảo kế hoạch');

CREATE TABLE IF NOT EXISTS ChiTietBaoCao (
  maCTBC INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  maBC INT UNSIGNED NOT NULL,
  maTB INT UNSIGNED,
  metricKey VARCHAR(100),
  metricValue DECIMAL(18,4),
  noiDung TEXT,
  CONSTRAINT fk_ctbc_bc FOREIGN KEY (maBC) REFERENCES BaoCao(maBC)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ctbc_tb FOREIGN KEY (maTB) REFERENCES ThietBi(maTB)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ChiTietBaoCao(maBC,maTB,metricKey,metricValue,noiDung) VALUES
(1,1,'so_lan_muon',5,'Máy chiếu được mượn 5 lần trong kỳ'),
(1,2,'so_lan_muon',12,'Máy tính được mượn 12 lần trong kỳ'),
(2,3,'so_luong_hong',3,'Loa kéo hỏng 3 cái'),
(3,NULL,'du_toan_kinh_phi',45000000,'Kinh phí dự toán mua sắm'),
(3,2,'so_luong_de_xem_xet',3,'Đề xuất thêm 3 bộ máy tính');

-- ============================
-- BẢNG BangGhiLog (polymorphic)
-- ============================
CREATE TABLE IF NOT EXISTS BangGhiLog (
  maLog INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  thoiGian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  maNguoiThucHien INT UNSIGNED NULL,
  hanhDong VARCHAR(80) NOT NULL,
  doiTuongLoai VARCHAR(80) NULL,
  doiTuongId VARCHAR(100) NULL,
  doiTuongSnapshot JSON NULL,
  ghiChu TEXT NULL,
  INDEX idx_obj (doiTuongLoai, doiTuongId),
  INDEX idx_thoigian (thoiGian),
  CONSTRAINT fk_log_nguoidung FOREIGN KEY (maNguoiThucHien) REFERENCES NguoiDung(maND)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mẫu log ban đầu (5 dòng)
INSERT INTO BangGhiLog(maNguoiThucHien,hanhDong,doiTuongLoai,doiTuongId,doiTuongSnapshot,ghiChu) VALUES
(1,'LOGIN','NguoiDung','1',JSON_OBJECT('ip','127.0.0.1'),'Đăng nhập thành công'),
(2,'CREATE','PhieuMuon','1',JSON_OBJECT('maPhieu',1,'ngayMuon','2024-02-02'),'Tạo phiếu mượn'),
(3,'UPDATE','ThietBi','2',JSON_OBJECT('old',JSON_OBJECT('soLuong',10),'new',JSON_OBJECT('soLuong',9)),'Giảm số lượng'),
(4,'DELETE','TaiKhoan','nhanvien',JSON_OBJECT('username','nhanvien'),'Xóa tài khoản'),
(NULL,'SYSTEM','Backup',NULL,NULL,'Backup tự động');

-- ============================
-- TRIGGERS: tự động ghi log khi CRUD trên các bảng chính
-- (LƯU Ý: triggers đọc session variable @maND để biết "ai" thao tác.
--  Trong PHP: trước thao tác hãy chạy: SET @maND = <maND>;
-- )
-- ============================
DELIMITER $$

-- ---------- ThietBi ----------
CREATE TRIGGER trg_thietbi_after_insert
AFTER INSERT ON ThietBi
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'INSERT', 'ThietBi', CAST(NEW.maTB AS CHAR),
          JSON_OBJECT('maTB', NEW.maTB, 'tenTB', NEW.tenTB, 'soLuong', NEW.soLuong),
          CONCAT('Thêm thiết bị ', NEW.tenTB));
END$$

CREATE TRIGGER trg_thietbi_after_update
AFTER UPDATE ON ThietBi
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'UPDATE', 'ThietBi', CAST(NEW.maTB AS CHAR),
          JSON_OBJECT('old', JSON_OBJECT('tenTB', OLD.tenTB, 'soLuong', OLD.soLuong),
                      'new', JSON_OBJECT('tenTB', NEW.tenTB, 'soLuong', NEW.soLuong)),
          CONCAT('Cập nhật thiết bị ', NEW.tenTB));
END$$

CREATE TRIGGER trg_thietbi_after_delete
AFTER DELETE ON ThietBi
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'DELETE', 'ThietBi', CAST(OLD.maTB AS CHAR),
          JSON_OBJECT('maTB', OLD.maTB, 'tenTB', OLD.tenTB, 'soLuong', OLD.soLuong),
          CONCAT('Xóa thiết bị ', OLD.tenTB));
END$$

-- ---------- PhieuMuon ----------
CREATE TRIGGER trg_phieumuon_after_insert
AFTER INSERT ON PhieuMuon
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'INSERT', 'PhieuMuon', CAST(NEW.maPhieu AS CHAR),
          JSON_OBJECT('maPhieu', NEW.maPhieu, 'ngayMuon', NEW.ngayMuon, 'maND', NEW.maND),
          CONCAT('Tạo phiếu mượn #', NEW.maPhieu));
END$$

CREATE TRIGGER trg_phieumuon_after_update
AFTER UPDATE ON PhieuMuon
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'UPDATE', 'PhieuMuon', CAST(NEW.maPhieu AS CHAR),
          JSON_OBJECT('old', JSON_OBJECT('trangThai', OLD.trangThai, 'soLuong', OLD.soLuong),
                      'new', JSON_OBJECT('trangThai', NEW.trangThai, 'soLuong', NEW.soLuong)),
          CONCAT('Cập nhật phiếu mượn #', NEW.maPhieu));
END$$

CREATE TRIGGER trg_phieumuon_after_delete
AFTER DELETE ON PhieuMuon
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'DELETE', 'PhieuMuon', CAST(OLD.maPhieu AS CHAR),
          JSON_OBJECT('maPhieu', OLD.maPhieu, 'maND', OLD.maND),
          CONCAT('Xóa phiếu mượn #', OLD.maPhieu));
END$$

-- ---------- NguoiDung ----------
CREATE TRIGGER trg_nguoidung_after_insert
AFTER INSERT ON NguoiDung
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'INSERT', 'NguoiDung', CAST(NEW.maND AS CHAR),
          JSON_OBJECT('maND', NEW.maND, 'hoTen', NEW.hoTen, 'email', NEW.email),
          CONCAT('Tạo người dùng ', NEW.hoTen));
END$$

CREATE TRIGGER trg_nguoidung_after_update
AFTER UPDATE ON NguoiDung
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'UPDATE', 'NguoiDung', CAST(NEW.maND AS CHAR),
          JSON_OBJECT('old', JSON_OBJECT('hoTen', OLD.hoTen, 'email', OLD.email),
                      'new', JSON_OBJECT('hoTen', NEW.hoTen, 'email', NEW.email)),
          CONCAT('Cập nhật người dùng ', NEW.hoTen));
END$$

CREATE TRIGGER trg_nguoidung_after_delete
AFTER DELETE ON NguoiDung
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'DELETE', 'NguoiDung', CAST(OLD.maND AS CHAR),
          JSON_OBJECT('maND', OLD.maND, 'hoTen', OLD.hoTen, 'email', OLD.email),
          CONCAT('Xóa người dùng ', OLD.hoTen));
END$$

-- ---------- TaiKhoan ----------
CREATE TRIGGER trg_taikhoan_after_insert
AFTER INSERT ON TaiKhoan
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'INSERT', 'TaiKhoan', NEW.username,
          JSON_OBJECT('username', NEW.username, 'trangThai', NEW.trangThai),
          CONCAT('Tạo tài khoản ', NEW.username));
END$$

CREATE TRIGGER trg_taikhoan_after_update
AFTER UPDATE ON TaiKhoan
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'UPDATE', 'TaiKhoan', NEW.username,
          JSON_OBJECT('old', JSON_OBJECT('trangThai', OLD.trangThai), 'new', JSON_OBJECT('trangThai', NEW.trangThai)),
          CONCAT('Cập nhật tài khoản ', NEW.username));
END$$

CREATE TRIGGER trg_taikhoan_after_delete
AFTER DELETE ON TaiKhoan
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'DELETE', 'TaiKhoan', OLD.username,
          JSON_OBJECT('username', OLD.username, 'trangThai', OLD.trangThai),
          CONCAT('Xóa tài khoản ', OLD.username));
END$$

-- ---------- KeHoachMuaSam ----------
CREATE TRIGGER trg_kehoachmuasam_after_insert
AFTER INSERT ON KeHoachMuaSam
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'INSERT', 'KeHoachMuaSam', CAST(NEW.maMS AS CHAR),
          JSON_OBJECT('maMS', NEW.maMS, 'noiDung', NEW.noiDung),
          CONCAT('Tạo kế hoạch mua sắm #', NEW.maMS));
END$$

CREATE TRIGGER trg_kehoachmuasam_after_update
AFTER UPDATE ON KeHoachMuaSam
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'UPDATE', 'KeHoachMuaSam', CAST(NEW.maMS AS CHAR),
          JSON_OBJECT('old', JSON_OBJECT('noiDung', OLD.noiDung, 'trangThai', OLD.trangThai),
                      'new', JSON_OBJECT('noiDung', NEW.noiDung, 'trangThai', NEW.trangThai)),
          CONCAT('Cập nhật kế hoạch mua sắm #', NEW.maMS));
END$$

CREATE TRIGGER trg_kehoachmuasam_after_delete
AFTER DELETE ON KeHoachMuaSam
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'DELETE', 'KeHoachMuaSam', CAST(OLD.maMS AS CHAR),
          JSON_OBJECT('maMS', OLD.maMS, 'noiDung', OLD.noiDung),
          CONCAT('Xóa kế hoạch mua sắm #', OLD.maMS));
END$$

-- ---------- KeHoachThanhLy ----------
CREATE TRIGGER trg_kehoachthanly_after_insert
AFTER INSERT ON KeHoachThanhLy
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'INSERT', 'KeHoachThanhLy', CAST(NEW.maTL AS CHAR),
          JSON_OBJECT('maTL', NEW.maTL, 'ghiChu', NEW.ghiChu),
          CONCAT('Tạo kế hoạch thanh lý #', NEW.maTL));
END$$

CREATE TRIGGER trg_kehoachthanly_after_update
AFTER UPDATE ON KeHoachThanhLy
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'UPDATE', 'KeHoachThanhLy', CAST(NEW.maTL AS CHAR),
          JSON_OBJECT('old', JSON_OBJECT('ghiChu', OLD.ghiChu), 'new', JSON_OBJECT('ghiChu', NEW.ghiChu)),
          CONCAT('Cập nhật kế hoạch thanh lý #', NEW.maTL));
END$$

CREATE TRIGGER trg_kehoachthanly_after_delete
AFTER DELETE ON KeHoachThanhLy
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'DELETE', 'KeHoachThanhLy', CAST(OLD.maTL AS CHAR),
          JSON_OBJECT('maTL', OLD.maTL, 'ghiChu', OLD.ghiChu),
          CONCAT('Xóa kế hoạch thanh lý #', OLD.maTL));
END$$

-- ---------- KiemKe ----------
CREATE TRIGGER trg_kiemke_after_insert
AFTER INSERT ON KiemKe
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'INSERT', 'KiemKe', CAST(NEW.maKK AS CHAR),
          JSON_OBJECT('maKK', NEW.maKK, 'ngayKK', NEW.ngayKK),
          CONCAT('Tạo phiếu kiểm kê #', NEW.maKK));
END$$

CREATE TRIGGER trg_kiemke_after_update
AFTER UPDATE ON KiemKe
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'UPDATE', 'KiemKe', CAST(NEW.maKK AS CHAR),
          JSON_OBJECT('old', JSON_OBJECT('ghiChu', OLD.ghiChu), 'new', JSON_OBJECT('ghiChu', NEW.ghiChu)),
          CONCAT('Cập nhật phiếu kiểm kê #', NEW.maKK));
END$$

CREATE TRIGGER trg_kiemke_after_delete
AFTER DELETE ON KiemKe
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'DELETE', 'KiemKe', CAST(OLD.maKK AS CHAR),
          JSON_OBJECT('maKK', OLD.maKK, 'ghiChu', OLD.ghiChu),
          CONCAT('Xóa phiếu kiểm kê #', OLD.maKK));
END$$

-- ---------- BaoCao ----------
CREATE TRIGGER trg_baocao_after_insert
AFTER INSERT ON BaoCao
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'INSERT', 'BaoCao', CAST(NEW.maBC AS CHAR),
          JSON_OBJECT('maBC', NEW.maBC, 'maLoai', NEW.maLoai, 'tieuDe', NEW.tieuDe),
          CONCAT('Tạo báo cáo #', NEW.maBC));
END$$

CREATE TRIGGER trg_baocao_after_update
AFTER UPDATE ON BaoCao
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'UPDATE', 'BaoCao', CAST(NEW.maBC AS CHAR),
          JSON_OBJECT('old', JSON_OBJECT('trangThai', OLD.trangThai), 'new', JSON_OBJECT('trangThai', NEW.trangThai)),
          CONCAT('Cập nhật báo cáo #', NEW.maBC));
END$$

CREATE TRIGGER trg_baocao_after_delete
AFTER DELETE ON BaoCao
FOR EACH ROW
BEGIN
  INSERT INTO BangGhiLog(maNguoiThucHien, hanhDong, doiTuongLoai, doiTuongId, doiTuongSnapshot, ghiChu)
  VALUES (IFNULL(@maND, NULL), 'DELETE', 'BaoCao', CAST(OLD.maBC AS CHAR),
          JSON_OBJECT('maBC', OLD.maBC, 'tieuDe', OLD.tieuDe),
          CONCAT('Xóa báo cáo #', OLD.maBC));
END$$

DELIMITER ;
