-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 20, 2025 lúc 03:43 PM
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
  `thoiGian` datetime DEFAULT current_timestamp(),
  `maND` int(11) DEFAULT NULL,
  `hanhDong` varchar(255) NOT NULL,
  `doiTuong` varchar(100) NOT NULL,
  `doiTuongId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bangghilog`
--

INSERT INTO `bangghilog` (`maLog`, `thoiGian`, `maND`, `hanhDong`, `doiTuong`, `doiTuongId`) VALUES
(1, '2025-12-20 16:35:10', 1, 'LOGIN', 'TaiKhoan', 1),
(2, '2025-12-20 16:35:10', 2, 'INSERT', 'PhieuMuon', 1),
(4, '2025-12-20 16:35:10', 4, 'INSERT', 'KeHoachMuaSam', 1),
(5, '2025-12-20 16:35:10', 5, 'DUYET', 'KeHoachThanhLy', 1),
(6, '2025-12-20 16:35:55', 2, 'LOGIN', 'TaiKhoan', 2),
(7, '2025-12-20 16:44:06', 2, 'PM:PM251220404|TB:Loa SL:1|MD:HDNK|NM:20/12|NT:27/', 'PhieuMuon', 0),
(8, '2025-12-20 16:45:38', 2, 'PM:PM251220878|TB:Máy SL:1|MD:HPH|NM:20/12|NT:27/1', 'PhieuMuon', 0),
(9, '2025-12-20 16:46:30', 2, 'PM:PM251220773|TB:Máy SL:3|MD:TC|NM:20/12|NT:27/01', 'PhieuMuon', 0),
(10, '2025-12-20 16:52:10', 2, 'PM:PM251220790|TB:Máy SL:1|MD:HPH|NM:20/12|NT:07/0', 'PhieuMuon', 0),
(11, '2025-12-20 17:03:39', 2, 'PM:PM251220177|TB:Loa SL:1|MD:DH|NM:20/12|NT:07/01', 'PhieuMuon', 0),
(12, '2025-12-20 17:06:40', 2, 'PM:PM251220881|TB:Máy SL:1|MD:DH|NM:20/12|NT:08/01', 'PhieuMuon', 0),
(13, '2025-12-20 17:18:00', 2, 'PM:PM251220515|TB:Máy SL:1|MD:DH|NM:20/12|NT:06/01', 'PhieuMuon', 0),
(16, '2025-12-20 18:10:55', 2, 'PM:PM251220988|TB:Máy SL:2|MD:HDNK|NM:21/12/2025|NT:04/01/2026', 'PhieuMuon', 0),
(17, '2025-12-20 18:42:40', 2, 'PM:PM251220435|TB:Máy SL:2|MD:HPH|NM:21/12/2025|NT:01/01/2026|TT:Chờ duyệt', 'PhieuMuon', 0),
(18, '2025-12-20 18:45:10', 2, 'PM:PM251220174|TB:Máy SL:1|MD:TC|NM:22/12/2025|NT:03/01/2026|TT:Chờ duyệt', 'PhieuMuon', 0),
(19, '2025-12-20 18:48:26', 2, 'BC:BC251220619|TB:Loa Bluetooth|TT:bị hư|ND:bị hỏng nhiều lắm nha', 'BaoCaoHuHong', 0),
(20, '2025-12-20 19:37:32', 2, 'LOGIN', 'TaiKhoan', 2),
(21, '2025-12-20 19:54:47', 2, 'LOGOUT', 'TaiKhoan', 2),
(22, '2025-12-20 19:55:07', 1, 'LOGIN', 'TaiKhoan', 1),
(23, '2025-12-20 19:55:44', 1, 'LOGOUT', 'TaiKhoan', 1),
(24, '2025-12-20 19:55:46', 2, 'LOGIN', 'TaiKhoan', 2),
(25, '2025-12-20 21:42:16', 2, 'LOGOUT', 'TaiKhoan', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baocaohuhong`
--

CREATE TABLE `baocaohuhong` (
  `maBaoCao` int(11) NOT NULL,
  `maPhieu` varchar(20) NOT NULL,
  `maTB` varchar(20) NOT NULL,
  `tinhTrang` text NOT NULL,
  `noiDungBaoCao` text DEFAULT NULL,
  `ngayBaoCao` datetime DEFAULT current_timestamp(),
  `trangThai` enum('dang-xu-ly','da-xu-ly','huy-bo') DEFAULT 'dang-xu-ly'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `baocaohuhong`
--

INSERT INTO `baocaohuhong` (`maBaoCao`, `maPhieu`, `maTB`, `tinhTrang`, `noiDungBaoCao`, `ngayBaoCao`, `trangThai`) VALUES
(1, '2', '2', 'hư hỏng', 'gãy chân trụ', '2025-12-19 19:06:04', 'dang-xu-ly');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietkiemke`
--

CREATE TABLE `chitietkiemke` (
  `maCTKK` int(11) NOT NULL,
  `maKK` int(11) DEFAULT NULL,
  `maTB` int(11) DEFAULT NULL,
  `soLuongTruoc` int(11) DEFAULT NULL,
  `soLuongThucTe` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietkiemke`
--

INSERT INTO `chitietkiemke` (`maCTKK`, `maKK`, `maTB`, `soLuongTruoc`, `soLuongThucTe`) VALUES
(1, 1, 1, 3, 3),
(2, 1, 2, 10, 9),
(3, 2, 3, 2, 1),
(4, 2, 4, 5, 4),
(5, 3, 5, 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietmuasam`
--

CREATE TABLE `chitietmuasam` (
  `maCTMS` int(11) NOT NULL,
  `maKH` int(11) DEFAULT NULL,
  `maTB` int(11) DEFAULT NULL,
  `soLuong` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietmuasam`
--

INSERT INTO `chitietmuasam` (`maCTMS`, `maKH`, `maTB`, `soLuong`) VALUES
(1, 1, 2, 3),
(2, 1, 1, 1),
(3, 2, 4, 2),
(4, 3, 3, 1),
(5, 4, 5, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietphieumuon`
--

CREATE TABLE `chitietphieumuon` (
  `maCT` int(11) NOT NULL,
  `maPhieu` int(11) DEFAULT NULL,
  `maTB` int(11) DEFAULT NULL,
  `soLuong` int(11) DEFAULT NULL,
  `tinhTrangKhiTra` enum('Tốt','Hư','Mất') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietphieumuon`
--

INSERT INTO `chitietphieumuon` (`maCT`, `maPhieu`, `maTB`, `soLuong`, `tinhTrangKhiTra`) VALUES
(1, 1, 1, 1, 'Tốt'),
(2, 1, 4, 1, 'Hư'),
(3, 2, 2, 1, NULL),
(4, 3, 3, 1, NULL),
(5, 4, 5, 1, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietthanhly`
--

CREATE TABLE `chitietthanhly` (
  `maCTTL` int(11) NOT NULL,
  `maTL` int(11) DEFAULT NULL,
  `maTB` int(11) DEFAULT NULL,
  `soLuong` int(11) DEFAULT NULL,
  `lyDo` varchar(255) DEFAULT NULL,
  `tinhTrang` enum('Hư nhẹ','Hư nặng','Mất','Không sửa được') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietthanhly`
--

INSERT INTO `chitietthanhly` (`maCTTL`, `maTL`, `maTB`, `soLuong`, `lyDo`, `tinhTrang`) VALUES
(1, 1, 3, 1, 'Loa hư không sửa được', 'Hư nặng'),
(2, 1, 4, 1, 'Micro bị mất', 'Mất'),
(3, 2, 2, 1, 'Máy tính lỗi', 'Hư nhẹ'),
(4, 4, 1, 1, 'Máy chiếu cũ', 'Không sửa được'),
(5, 5, 5, 1, 'Camera hỏng', 'Hư nặng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kehoachmuasam`
--

CREATE TABLE `kehoachmuasam` (
  `maMS` int(11) NOT NULL,
  `ngayLap` date DEFAULT NULL,
  `trangThai` enum('Chờ duyệt','Đã duyệt','Từ chối') DEFAULT NULL,
  `nguoiLap` int(11) DEFAULT NULL,
  `nguoiDuyet` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kehoachmuasam`
--

INSERT INTO `kehoachmuasam` (`maMS`, `ngayLap`, `trangThai`, `nguoiLap`, `nguoiDuyet`) VALUES
(1, '2024-06-20', 'Đã duyệt', 4, 5),
(2, '2024-07-10', 'Chờ duyệt', 4, NULL),
(3, '2024-08-01', 'Từ chối', 4, 5),
(4, '2024-09-01', 'Đã duyệt', 4, 5),
(5, '2024-10-01', 'Chờ duyệt', 4, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kehoachthanhly`
--

CREATE TABLE `kehoachthanhly` (
  `maTL` int(11) NOT NULL,
  `ngayLap` date DEFAULT NULL,
  `trangThai` enum('Chờ duyệt','Đã duyệt','Từ chối') DEFAULT NULL,
  `nguoiLap` int(11) DEFAULT NULL,
  `nguoiDuyet` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kehoachthanhly`
--

INSERT INTO `kehoachthanhly` (`maTL`, `ngayLap`, `trangThai`, `nguoiLap`, `nguoiDuyet`) VALUES
(1, '2024-06-25', 'Đã duyệt', 3, 5),
(2, '2024-07-20', 'Chờ duyệt', 3, NULL),
(3, '2024-08-15', 'Từ chối', 3, 5),
(4, '2024-09-10', 'Đã duyệt', 3, 5),
(5, '2024-10-05', 'Chờ duyệt', 3, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kiemke`
--

CREATE TABLE `kiemke` (
  `maKK` int(11) NOT NULL,
  `ngayKK` date DEFAULT NULL,
  `loaiKiemKe` enum('Cuối kỳ','Cuối năm','Đột xuất') DEFAULT NULL,
  `maND` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kiemke`
--

INSERT INTO `kiemke` (`maKK`, `ngayKK`, `loaiKiemKe`, `maND`) VALUES
(1, '2024-05-31', 'Cuối kỳ', 3),
(2, '2024-06-30', 'Cuối năm', 3),
(3, '2024-07-15', 'Đột xuất', 3),
(4, '2024-08-31', 'Cuối kỳ', 3),
(5, '2024-12-31', 'Cuối năm', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `monhoc`
--

CREATE TABLE `monhoc` (
  `maMH` int(11) NOT NULL,
  `tenMonHoc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `monhoc`
--

INSERT INTO `monhoc` (`maMH`, `tenMonHoc`) VALUES
(1, 'Toán'),
(2, 'Tin học'),
(3, 'Vật lý'),
(4, 'Âm nhạc'),
(5, 'Sinh học');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `maND` int(11) NOT NULL,
  `hoTen` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `maVT` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung`
--

INSERT INTO `nguoidung` (`maND`, `hoTen`, `email`, `maVT`) VALUES
(1, 'Quản trị hệ thống', 'admin@thcs.edu.vn', 1),
(2, 'Nguyễn Văn Giáo', 'giaovien@thcs.edu.vn', 2),
(3, 'Trần Thị Thiết Bị', 'nhanvien@thcs.edu.vn', 3),
(4, 'Lê Văn Tổ Trưởng', 'totruong@thcs.edu.vn', 4),
(5, 'Phạm Thị Hiệu Trưởng', 'hieutruong@thcs.edu.vn', 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieumuon`
--

CREATE TABLE `phieumuon` (
  `maPhieu` int(11) NOT NULL,
  `ngayMuon` date DEFAULT NULL,
  `ngayTraDuKien` date DEFAULT NULL,
  `ngayTraThucTe` date DEFAULT NULL,
  `trangThai` enum('Chờ duyệt','Đã duyệt','Đang mượn','Đã trả','Đã hủy','Từ chối') DEFAULT NULL,
  `maND` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phieumuon`
--

INSERT INTO `phieumuon` (`maPhieu`, `ngayMuon`, `ngayTraDuKien`, `ngayTraThucTe`, `trangThai`, `maND`) VALUES
(1, '2024-05-01', '2024-05-03', '2024-05-03', 'Đã trả', 2),
(2, '2024-06-01', '2024-06-03', NULL, 'Đang mượn', 2),
(3, '2024-06-05', '2024-06-07', NULL, 'Chờ duyệt', 2),
(4, '2024-06-10', '2024-06-12', NULL, 'Từ chối', 2),
(5, '2024-06-15', '2024-06-17', NULL, 'Đã hủy', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

CREATE TABLE `taikhoan` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `trangThai` enum('Hoạt động','Khoá') DEFAULT 'Hoạt động',
  `maND` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`username`, `password`, `trangThai`, `maND`) VALUES
('admin', '123', 'Hoạt động', 1),
('giaovien', '123', 'Hoạt động', 2),
('hieutruong', '123', 'Hoạt động', 5),
('nhanvien', '123', 'Hoạt động', 3),
('totruong', '123', 'Hoạt động', 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thietbi`
--

CREATE TABLE `thietbi` (
  `maTB` int(11) NOT NULL,
  `tenTB` varchar(150) NOT NULL,
  `donVi` varchar(20) DEFAULT NULL,
  `lop` enum('6','7','8','9') DEFAULT NULL,
  `soLuongTong` int(11) DEFAULT 0,
  `soLuongKhaDung` int(11) DEFAULT 0,
  `tinhTrang` enum('Tốt','Hư nhẹ','Hư nặng','Đang sửa') DEFAULT 'Tốt',
  `maMH` int(11) DEFAULT NULL,
  `isHidden` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thietbi`
--

INSERT INTO `thietbi` (`maTB`, `tenTB`, `donVi`, `lop`, `soLuongTong`, `soLuongKhaDung`, `tinhTrang`, `maMH`, `isHidden`) VALUES
(1, 'Máy chiếu Epson EB-X06', 'Cái', '6', 3, 3, 'Tốt', 1, 0),
(2, 'Máy tính để bàn', 'Bộ', '8', 10, 9, 'Tốt', 2, 0),
(3, 'Loa kéo', 'Cái', '7', 2, 1, 'Hư nhẹ', 4, 0),
(4, 'Micro không dây', 'Cái', '9', 5, 4, 'Tốt', 4, 0),
(5, 'Camera quan sát', 'Cái', '8', 1, 1, 'Tốt', 2, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vaitro`
--

CREATE TABLE `vaitro` (
  `maVT` int(11) NOT NULL,
  `tenVT` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vaitro`
--

INSERT INTO `vaitro` (`maVT`, `tenVT`) VALUES
(1, 'Admin'),
(5, 'Ban giám hiệu'),
(2, 'Giáo viên'),
(3, 'Nhân viên'),
(4, 'Tổ trưởng');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bangghilog`
--
ALTER TABLE `bangghilog`
  ADD PRIMARY KEY (`maLog`),
  ADD KEY `maND` (`maND`);

--
-- Chỉ mục cho bảng `baocaohuhong`
--
ALTER TABLE `baocaohuhong`
  ADD PRIMARY KEY (`maBaoCao`);

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
  ADD PRIMARY KEY (`maCTMS`),
  ADD KEY `maKH` (`maKH`),
  ADD KEY `maTB` (`maTB`);

--
-- Chỉ mục cho bảng `chitietphieumuon`
--
ALTER TABLE `chitietphieumuon`
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
-- Chỉ mục cho bảng `kehoachmuasam`
--
ALTER TABLE `kehoachmuasam`
  ADD PRIMARY KEY (`maMS`),
  ADD KEY `nguoiLap` (`nguoiLap`),
  ADD KEY `nguoiDuyet` (`nguoiDuyet`);

--
-- Chỉ mục cho bảng `kehoachthanhly`
--
ALTER TABLE `kehoachthanhly`
  ADD PRIMARY KEY (`maTL`),
  ADD KEY `nguoiLap` (`nguoiLap`),
  ADD KEY `nguoiDuyet` (`nguoiDuyet`);

--
-- Chỉ mục cho bảng `kiemke`
--
ALTER TABLE `kiemke`
  ADD PRIMARY KEY (`maKK`),
  ADD KEY `maND` (`maND`);

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
  ADD KEY `maND` (`maND`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `maND` (`maND`);

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
  ADD PRIMARY KEY (`maVT`),
  ADD UNIQUE KEY `tenVT` (`tenVT`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bangghilog`
--
ALTER TABLE `bangghilog`
  MODIFY `maLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `baocaohuhong`
--
ALTER TABLE `baocaohuhong`
  MODIFY `maBaoCao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `chitietkiemke`
--
ALTER TABLE `chitietkiemke`
  MODIFY `maCTKK` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `chitietmuasam`
--
ALTER TABLE `chitietmuasam`
  MODIFY `maCTMS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `chitietphieumuon`
--
ALTER TABLE `chitietphieumuon`
  MODIFY `maCT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `chitietthanhly`
--
ALTER TABLE `chitietthanhly`
  MODIFY `maCTTL` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `kehoachmuasam`
--
ALTER TABLE `kehoachmuasam`
  MODIFY `maMS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `kehoachthanhly`
--
ALTER TABLE `kehoachthanhly`
  MODIFY `maTL` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `kiemke`
--
ALTER TABLE `kiemke`
  MODIFY `maKK` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `monhoc`
--
ALTER TABLE `monhoc`
  MODIFY `maMH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `maND` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `phieumuon`
--
ALTER TABLE `phieumuon`
  MODIFY `maPhieu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `thietbi`
--
ALTER TABLE `thietbi`
  MODIFY `maTB` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  MODIFY `maVT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bangghilog`
--
ALTER TABLE `bangghilog`
  ADD CONSTRAINT `bangghilog_ibfk_1` FOREIGN KEY (`maND`) REFERENCES `nguoidung` (`maND`);

--
-- Các ràng buộc cho bảng `chitietmuasam`
--
ALTER TABLE `chitietmuasam`
  ADD CONSTRAINT `chitietmuasam_ibfk_1` FOREIGN KEY (`maKH`) REFERENCES `kehoachmuasam` (`maMS`),
  ADD CONSTRAINT `chitietmuasam_ibfk_2` FOREIGN KEY (`maTB`) REFERENCES `thietbi` (`maTB`);

--
-- Các ràng buộc cho bảng `chitietphieumuon`
--
ALTER TABLE `chitietphieumuon`
  ADD CONSTRAINT `chitietphieumuon_ibfk_1` FOREIGN KEY (`maPhieu`) REFERENCES `phieumuon` (`maPhieu`),
  ADD CONSTRAINT `chitietphieumuon_ibfk_2` FOREIGN KEY (`maTB`) REFERENCES `thietbi` (`maTB`);

--
-- Các ràng buộc cho bảng `chitietthanhly`
--
ALTER TABLE `chitietthanhly`
  ADD CONSTRAINT `chitietthanhly_ibfk_1` FOREIGN KEY (`maTL`) REFERENCES `kehoachthanhly` (`maTL`),
  ADD CONSTRAINT `chitietthanhly_ibfk_2` FOREIGN KEY (`maTB`) REFERENCES `thietbi` (`maTB`);

--
-- Các ràng buộc cho bảng `kehoachmuasam`
--
ALTER TABLE `kehoachmuasam`
  ADD CONSTRAINT `kehoachmuasam_ibfk_1` FOREIGN KEY (`nguoiLap`) REFERENCES `nguoidung` (`maND`),
  ADD CONSTRAINT `kehoachmuasam_ibfk_2` FOREIGN KEY (`nguoiDuyet`) REFERENCES `nguoidung` (`maND`);

--
-- Các ràng buộc cho bảng `kehoachthanhly`
--
ALTER TABLE `kehoachthanhly`
  ADD CONSTRAINT `kehoachthanhly_ibfk_1` FOREIGN KEY (`nguoiLap`) REFERENCES `nguoidung` (`maND`),
  ADD CONSTRAINT `kehoachthanhly_ibfk_2` FOREIGN KEY (`nguoiDuyet`) REFERENCES `nguoidung` (`maND`);

--
-- Các ràng buộc cho bảng `kiemke`
--
ALTER TABLE `kiemke`
  ADD CONSTRAINT `kiemke_ibfk_1` FOREIGN KEY (`maND`) REFERENCES `nguoidung` (`maND`);

--
-- Các ràng buộc cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD CONSTRAINT `nguoidung_ibfk_1` FOREIGN KEY (`maVT`) REFERENCES `vaitro` (`maVT`);

--
-- Các ràng buộc cho bảng `phieumuon`
--
ALTER TABLE `phieumuon`
  ADD CONSTRAINT `phieumuon_ibfk_1` FOREIGN KEY (`maND`) REFERENCES `nguoidung` (`maND`);

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
