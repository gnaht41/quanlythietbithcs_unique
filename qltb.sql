-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 28, 2025 lúc 08:18 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `qltb`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bangghilog`
--

CREATE TABLE `bangghilog` (
  `maLog` int(11) NOT NULL,
  `thoiGian` datetime DEFAULT NULL,
  `nguoiDung` char(4) DEFAULT NULL,
  `hanhDong` varchar(100) DEFAULT NULL,
  `doiTuong` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bangghilog`
--

INSERT INTO `bangghilog` (`maLog`, `thoiGian`, `nguoiDung`, `hanhDong`, `doiTuong`) VALUES
(1, '2025-10-28 12:28:40', 'ND01', 'Thêm thiết bị', 'ThietBi'),
(2, '2025-10-28 12:28:40', 'ND02', 'Duyệt kế hoạch', 'KeHoachMuaSam'),
(3, '2025-10-28 12:28:40', 'ND03', 'Cập nhật thông tin', 'ThietBi'),
(4, '2025-10-28 12:28:40', 'ND04', 'Tạo phiếu mượn', 'PhieuMuon'),
(5, '2025-10-28 12:28:40', 'ND05', 'Ghi nhận báo cáo', 'BienBanBaoCao');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bienbanbaocao`
--

CREATE TABLE `bienbanbaocao` (
  `maBC` int(11) NOT NULL,
  `loaiBC` varchar(50) DEFAULT NULL,
  `thoiGian` date DEFAULT NULL,
  `noiDung` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bienbanbaocao`
--

INSERT INTO `bienbanbaocao` (`maBC`, `loaiBC`, `thoiGian`, `noiDung`) VALUES
(1, 'Kiểm kê', '2025-10-01', 'Tổng hợp kết quả kiểm kê quý 3'),
(2, 'Sự cố', '2025-10-10', 'Báo cáo sự cố micro hỏng'),
(3, 'Bảo trì', '2025-09-15', 'Bảo trì bảng tương tác'),
(4, 'Nhập mới', '2025-09-20', 'Nhập thêm máy tính phòng Tin'),
(5, 'Thanh lý', '2025-11-05', 'Thanh lý thiết bị hỏng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietkiemke`
--

CREATE TABLE `chitietkiemke` (
  `maCTKK` int(11) NOT NULL,
  `maKK` int(11) DEFAULT NULL,
  `maTB` int(11) DEFAULT NULL,
  `duKien` int(11) DEFAULT NULL,
  `thucTe` int(11) DEFAULT NULL,
  `chenLech` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietkiemke`
--

INSERT INTO `chitietkiemke` (`maCTKK`, `maKK`, `maTB`, `duKien`, `thucTe`, `chenLech`) VALUES
(1, 1, 101, 3, 3, 0),
(2, 2, 102, 10, 9, -1),
(3, 3, 103, 2, 2, 0),
(4, 4, 104, 1, 1, 0),
(5, 5, 105, 4, 3, -1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietmuasam`
--

CREATE TABLE `chitietmuasam` (
  `maCTKH` varchar(10) NOT NULL,
  `maKH` varchar(10) DEFAULT NULL,
  `moTa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietmuasam`
--

INSERT INTO `chitietmuasam` (`maCTKH`, `maKH`, `moTa`) VALUES
('CTKH01', 'KH01', 'Mua 5 bộ máy tính Dell'),
('CTKH02', 'KH02', 'Mua 2 máy chiếu Epson'),
('CTKH03', 'KH03', 'Mua 4 micro Sony'),
('CTKH04', 'KH04', 'Thay bảng tương tác Samsung'),
('CTKH05', 'KH05', 'Thêm loa Bluetooth phòng nhạc');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietmuon`
--

CREATE TABLE `chitietmuon` (
  `maCT` int(11) NOT NULL,
  `maPhieu` int(11) DEFAULT NULL,
  `maTB` int(11) DEFAULT NULL,
  `soLuong` int(11) DEFAULT NULL,
  `ghiChu` varchar(255) DEFAULT NULL,
  `thoiGian` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietmuon`
--

INSERT INTO `chitietmuon` (`maCT`, `maPhieu`, `maTB`, `soLuong`, `ghiChu`, `thoiGian`) VALUES
(1, 1, 101, 1, 'Máy chiếu Epson', '2025-10-26'),
(2, 2, 105, 2, 'Micro không dây', '2025-10-27'),
(3, 3, 102, 1, 'Máy tính Dell', '2025-10-27'),
(4, 4, 103, 1, 'Loa Sony', '2025-10-27'),
(5, 5, 104, 1, 'Bảng tương tác', '2025-10-28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietthanhly`
--

CREATE TABLE `chitietthanhly` (
  `maCTTL` int(11) NOT NULL,
  `maTL` int(11) DEFAULT NULL,
  `maTB` int(11) DEFAULT NULL,
  `lyDo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietthanhly`
--

INSERT INTO `chitietthanhly` (`maCTTL`, `maTL`, `maTB`, `lyDo`) VALUES
(1, 1, 103, 'Loa méo tiếng'),
(2, 2, 101, 'Máy chiếu mờ'),
(3, 3, 105, 'Micro yếu pin'),
(4, 4, 102, 'Máy tính lỗi ổ cứng'),
(5, 5, 104, 'Bảng hư cảm ứng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kehoachkiemke`
--

CREATE TABLE `kehoachkiemke` (
  `maKK` int(11) NOT NULL,
  `ngayKiemKe` date DEFAULT NULL,
  `ghiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kehoachkiemke`
--

INSERT INTO `kehoachkiemke` (`maKK`, `ngayKiemKe`, `ghiChu`) VALUES
(1, '2025-08-01', 'Kiểm kê đầu năm'),
(2, '2025-09-01', 'Kiểm kê bổ sung'),
(3, '2025-10-01', 'Kiểm kê giữa kỳ'),
(4, '2025-11-01', 'Kiểm kê cuối kỳ'),
(5, '2025-12-01', 'Kiểm kê định kỳ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kehoachmuasam`
--

CREATE TABLE `kehoachmuasam` (
  `maKH` varchar(10) NOT NULL,
  `ngayLap` date DEFAULT NULL,
  `noiDung` varchar(255) DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kehoachmuasam`
--

INSERT INTO `kehoachmuasam` (`maKH`, `ngayLap`, `noiDung`, `trangThai`) VALUES
('KH01', '2025-09-01', 'Mua máy tính mới', 'Đã duyệt'),
('KH02', '2025-09-10', 'Mua máy chiếu', 'Đang chờ'),
('KH03', '2025-09-15', 'Mua micro không dây', 'Đang xét'),
('KH04', '2025-09-20', 'Thay bảng tương tác', 'Đã duyệt'),
('KH05', '2025-09-25', 'Bổ sung loa phòng học', 'Đang chờ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kehoachthanhly`
--

CREATE TABLE `kehoachthanhly` (
  `maTL` int(11) NOT NULL,
  `ngayLap` date DEFAULT NULL,
  `ghiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kehoachthanhly`
--

INSERT INTO `kehoachthanhly` (`maTL`, `ngayLap`, `ghiChu`) VALUES
(1, '2025-07-01', 'Thanh lý thiết bị hỏng'),
(2, '2025-08-01', 'Thanh lý tồn kho'),
(3, '2025-09-01', 'Thanh lý micro cũ'),
(4, '2025-10-01', 'Thanh lý máy tính cũ'),
(5, '2025-11-01', 'Thanh lý cuối năm');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `monhoc`
--

CREATE TABLE `monhoc` (
  `maMH` int(11) NOT NULL,
  `tenMonHoc` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `monhoc`
--

INSERT INTO `monhoc` (`maMH`, `tenMonHoc`) VALUES
(1, 'Toán'),
(2, 'Vật lý'),
(3, 'Tin học'),
(4, 'Ngữ văn'),
(5, 'Âm nhạc');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `maND` char(4) NOT NULL,
  `hoTen` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `maVT` tinyint(4) DEFAULT NULL,
  `ngayTao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung`
--

INSERT INTO `nguoidung` (`maND`, `hoTen`, `email`, `maVT`, `ngayTao`) VALUES
('ND01', 'Nguyễn Văn A', 'a.nguyen@school.edu.vn', 1, '2025-10-28'),
('ND02', 'Trần Thị B', 'b.tran@school.edu.vn', 2, '2025-10-28'),
('ND03', 'Lê Văn C', 'c.le@school.edu.vn', 3, '2025-10-28'),
('ND04', 'Phạm Thị D', 'd.pham@school.edu.vn', 4, '2025-10-28'),
('ND05', 'Nguyễn Hữu E', 'e.nguyen@school.edu.vn', 5, '2025-10-28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieumuon`
--

CREATE TABLE `phieumuon` (
  `maPhieu` int(11) NOT NULL,
  `ngayLap` date DEFAULT NULL,
  `ngayMuon` date DEFAULT NULL,
  `ngayTraDuKien` date DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT NULL,
  `nguoiLap` char(4) DEFAULT NULL,
  `ghiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phieumuon`
--

INSERT INTO `phieumuon` (`maPhieu`, `ngayLap`, `ngayMuon`, `ngayTraDuKien`, `trangThai`, `nguoiLap`, `ghiChu`) VALUES
(1, '2025-10-25', '2025-10-26', '2025-10-28', 'Đang mượn', 'ND04', 'Mượn máy chiếu'),
(2, '2025-10-25', '2025-10-27', '2025-10-29', 'Đang mượn', 'ND04', 'Mượn micro'),
(3, '2025-10-26', '2025-10-27', '2025-10-28', 'Đã trả', 'ND03', 'Thiết bị hoạt động tốt'),
(4, '2025-10-26', '2025-10-27', '2025-10-28', 'Đang mượn', 'ND05', 'Mượn loa Bluetooth'),
(5, '2025-10-27', '2025-10-28', '2025-10-30', 'Đang xử lý', 'ND02', 'Đợi duyệt phiếu');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

CREATE TABLE `taikhoan` (
  `username` varchar(50) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `maND` char(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`username`, `password`, `maND`) VALUES
('admin', '123', 'ND01'),
('giaovien', '123', 'ND04'),
('hieutruong', '123', 'ND02'),
('nhanvien', '123', 'ND05'),
('totruong', '123', 'ND03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thietbi`
--

CREATE TABLE `thietbi` (
  `maTB` int(11) NOT NULL,
  `tenTB` varchar(100) DEFAULT NULL,
  `soLuong` int(11) DEFAULT NULL,
  `donVi` varchar(20) DEFAULT NULL,
  `tinhTrang` varchar(50) DEFAULT NULL,
  `lop` int(11) DEFAULT NULL,
  `maMH` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thietbi`
--

INSERT INTO `thietbi` (`maTB`, `tenTB`, `soLuong`, `donVi`, `tinhTrang`, `lop`, `maMH`) VALUES
(101, 'Máy chiếu Epson', 3, 'cái', 'Tốt', 9, 2),
(102, 'Máy tính Dell', 10, 'bộ', 'Tốt', 8, 3),
(103, 'Loa Sony', 2, 'cái', 'Bình thường', 7, 5),
(104, 'Bảng tương tác', 1, 'cái', 'Tốt', 9, 1),
(105, 'Micro không dây', 4, 'cái', 'Cần bảo trì', 6, 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vaitro`
--

CREATE TABLE `vaitro` (
  `maVT` tinyint(4) NOT NULL,
  `tenVT` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vaitro`
--

INSERT INTO `vaitro` (`maVT`, `tenVT`) VALUES
(1, 'Admin'),
(2, 'Hiệu trưởng'),
(3, 'Tổ trưởng'),
(4, 'Giáo viên'),
(5, 'Nhân viên thiết bị');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bangghilog`
--
ALTER TABLE `bangghilog`
  ADD PRIMARY KEY (`maLog`);

--
-- Chỉ mục cho bảng `bienbanbaocao`
--
ALTER TABLE `bienbanbaocao`
  ADD PRIMARY KEY (`maBC`);

--
-- Chỉ mục cho bảng `chitietkiemke`
--
ALTER TABLE `chitietkiemke`
  ADD PRIMARY KEY (`maCTKK`),
  ADD KEY `maKK` (`maKK`),
  ADD KEY `maTB` (`maTB`);

--
-- Chỉ mục cho bảng `chitietmuasam`
--
ALTER TABLE `chitietmuasam`
  ADD PRIMARY KEY (`maCTKH`),
  ADD KEY `maKH` (`maKH`);

--
-- Chỉ mục cho bảng `chitietmuon`
--
ALTER TABLE `chitietmuon`
  ADD PRIMARY KEY (`maCT`),
  ADD KEY `maPhieu` (`maPhieu`),
  ADD KEY `maTB` (`maTB`);

--
-- Chỉ mục cho bảng `chitietthanhly`
--
ALTER TABLE `chitietthanhly`
  ADD PRIMARY KEY (`maCTTL`),
  ADD KEY `maTL` (`maTL`),
  ADD KEY `maTB` (`maTB`);

--
-- Chỉ mục cho bảng `kehoachkiemke`
--
ALTER TABLE `kehoachkiemke`
  ADD PRIMARY KEY (`maKK`);

--
-- Chỉ mục cho bảng `kehoachmuasam`
--
ALTER TABLE `kehoachmuasam`
  ADD PRIMARY KEY (`maKH`);

--
-- Chỉ mục cho bảng `kehoachthanhly`
--
ALTER TABLE `kehoachthanhly`
  ADD PRIMARY KEY (`maTL`);

--
-- Chỉ mục cho bảng `monhoc`
--
ALTER TABLE `monhoc`
  ADD PRIMARY KEY (`maMH`);

--
-- Chỉ mục cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`maND`),
  ADD KEY `maVT` (`maVT`);

--
-- Chỉ mục cho bảng `phieumuon`
--
ALTER TABLE `phieumuon`
  ADD PRIMARY KEY (`maPhieu`),
  ADD KEY `nguoiLap` (`nguoiLap`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`username`),
  ADD KEY `maND` (`maND`);

--
-- Chỉ mục cho bảng `thietbi`
--
ALTER TABLE `thietbi`
  ADD PRIMARY KEY (`maTB`),
  ADD KEY `maMH` (`maMH`);

--
-- Chỉ mục cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  ADD PRIMARY KEY (`maVT`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bangghilog`
--
ALTER TABLE `bangghilog`
  MODIFY `maLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `chitietkiemke`
--
ALTER TABLE `chitietkiemke`
  MODIFY `maCTKK` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `chitietmuon`
--
ALTER TABLE `chitietmuon`
  MODIFY `maCT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `chitietthanhly`
--
ALTER TABLE `chitietthanhly`
  MODIFY `maCTTL` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `phieumuon`
--
ALTER TABLE `phieumuon`
  MODIFY `maPhieu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitietkiemke`
--
ALTER TABLE `chitietkiemke`
  ADD CONSTRAINT `chitietkiemke_ibfk_1` FOREIGN KEY (`maKK`) REFERENCES `kehoachkiemke` (`maKK`),
  ADD CONSTRAINT `chitietkiemke_ibfk_2` FOREIGN KEY (`maTB`) REFERENCES `thietbi` (`maTB`);

--
-- Các ràng buộc cho bảng `chitietmuasam`
--
ALTER TABLE `chitietmuasam`
  ADD CONSTRAINT `chitietmuasam_ibfk_1` FOREIGN KEY (`maKH`) REFERENCES `kehoachmuasam` (`maKH`);

--
-- Các ràng buộc cho bảng `chitietmuon`
--
ALTER TABLE `chitietmuon`
  ADD CONSTRAINT `chitietmuon_ibfk_1` FOREIGN KEY (`maPhieu`) REFERENCES `phieumuon` (`maPhieu`),
  ADD CONSTRAINT `chitietmuon_ibfk_2` FOREIGN KEY (`maTB`) REFERENCES `thietbi` (`maTB`);

--
-- Các ràng buộc cho bảng `chitietthanhly`
--
ALTER TABLE `chitietthanhly`
  ADD CONSTRAINT `chitietthanhly_ibfk_1` FOREIGN KEY (`maTL`) REFERENCES `kehoachthanhly` (`maTL`),
  ADD CONSTRAINT `chitietthanhly_ibfk_2` FOREIGN KEY (`maTB`) REFERENCES `thietbi` (`maTB`);

--
-- Các ràng buộc cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD CONSTRAINT `nguoidung_ibfk_1` FOREIGN KEY (`maVT`) REFERENCES `vaitro` (`maVT`);

--
-- Các ràng buộc cho bảng `phieumuon`
--
ALTER TABLE `phieumuon`
  ADD CONSTRAINT `phieumuon_ibfk_1` FOREIGN KEY (`nguoiLap`) REFERENCES `nguoidung` (`maND`);

--
-- Các ràng buộc cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD CONSTRAINT `taikhoan_ibfk_1` FOREIGN KEY (`maND`) REFERENCES `nguoidung` (`maND`);

--
-- Các ràng buộc cho bảng `thietbi`
--
ALTER TABLE `thietbi`
  ADD CONSTRAINT `thietbi_ibfk_1` FOREIGN KEY (`maMH`) REFERENCES `monhoc` (`maMH`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
