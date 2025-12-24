-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 24, 2025 lúc 06:45 PM
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
  `hanhDong` varchar(50) DEFAULT NULL,
  `doiTuong` varchar(50) DEFAULT NULL,
  `doiTuongId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bangghilog`
--

INSERT INTO `bangghilog` (`maLog`, `thoiGian`, `maND`, `hanhDong`, `doiTuong`, `doiTuongId`) VALUES
(1, '2025-12-24 11:35:48', 1, 'LOGIN', 'TaiKhoan', 1),
(2, '2025-12-24 11:35:48', 2, 'INSERT', 'PhieuMuon', 1),
(4, '2025-12-24 11:35:48', 4, 'INSERT', 'KeHoachMuaSam', 1),
(5, '2025-12-24 11:35:48', 5, 'DUYET', 'KeHoachThanhLy', 1),
(6, '2025-12-24 11:42:10', 3, 'LOGOUT', 'TaiKhoan', 3),
(7, '2025-12-24 11:42:14', 2, 'LOGIN', 'TaiKhoan', 2),
(8, '2025-12-24 11:44:32', 2, 'LOGOUT', 'TaiKhoan', 2),
(9, '2025-12-24 11:44:41', 3, 'LOGIN', 'TaiKhoan', 3),
(10, '2025-12-24 11:47:41', 3, 'INSERT', 'KeHoachThanhLy', 6),
(11, '2025-12-24 11:47:48', 3, 'LOGOUT', 'TaiKhoan', 3),
(12, '2025-12-24 11:47:53', 5, 'LOGIN', 'TaiKhoan', 5),
(13, '2025-12-24 11:48:05', 5, 'DUYET', 'KeHoachThanhLy', 6),
(14, '2025-12-24 11:48:13', 5, 'LOGOUT', 'TaiKhoan', 5),
(15, '2025-12-24 11:48:15', 3, 'LOGIN', 'TaiKhoan', 3),
(16, '2025-12-24 11:48:23', 3, 'DELETE', 'KeHoachThanhLy', 5),
(17, '2025-12-24 11:48:24', 3, 'DELETE', 'KeHoachThanhLy', 2),
(18, '2025-12-24 12:09:47', 2, 'INSERT', 'BaoCaoHuHong', 0),
(19, '2025-12-24 13:10:49', 3, 'LOGOUT', 'TaiKhoan', 3),
(20, '2025-12-24 13:10:53', 2, 'LOGIN', 'TaiKhoan', 2),
(21, '2025-12-24 13:11:08', 2, 'LOGOUT', 'TaiKhoan', 2),
(22, '2025-12-24 13:11:11', 2, 'LOGIN', 'TaiKhoan', 2),
(23, '2025-12-24 13:11:22', 2, 'LOGOUT', 'TaiKhoan', 2),
(24, '2025-12-24 13:11:29', 3, 'LOGIN', 'TaiKhoan', 3),
(25, '2025-12-24 13:55:10', 3, 'LOGOUT', 'TaiKhoan', 3),
(26, '2025-12-24 13:55:13', 3, 'LOGIN', 'TaiKhoan', 3),
(27, '2025-12-24 13:55:55', 3, 'LOGOUT', 'TaiKhoan', 3),
(28, '2025-12-24 13:55:58', 2, 'LOGIN', 'TaiKhoan', 2),
(29, '2025-12-24 14:14:55', 2, 'LOGOUT', 'TaiKhoan', 2),
(30, '2025-12-24 14:15:02', 3, 'LOGIN', 'TaiKhoan', 3),
(31, '2025-12-24 19:42:46', 3, 'LOGOUT', 'TaiKhoan', 3),
(32, '2025-12-24 19:42:48', 3, 'LOGIN', 'TaiKhoan', 3),
(33, '2025-12-24 23:42:53', 3, 'LOGOUT', 'TaiKhoan', 3),
(34, '2025-12-24 23:42:58', 2, 'LOGIN', 'TaiKhoan', 2),
(35, '2025-12-24 23:44:09', 2, 'LOGOUT', 'TaiKhoan', 2),
(36, '2025-12-24 23:44:12', 3, 'LOGIN', 'TaiKhoan', 3),
(37, '2025-12-24 23:48:25', 3, 'LOGOUT', 'TaiKhoan', 3),
(38, '2025-12-24 23:48:28', 2, 'LOGIN', 'TaiKhoan', 2),
(39, '2025-12-24 23:49:09', 2, 'LOGOUT', 'TaiKhoan', 2),
(40, '2025-12-24 23:49:12', 3, 'LOGIN', 'TaiKhoan', 3),
(41, '2025-12-25 00:29:59', 3, 'LOGOUT', 'TaiKhoan', 3),
(42, '2025-12-25 00:30:01', 2, 'LOGIN', 'TaiKhoan', 2),
(43, '2025-12-25 00:30:16', 2, 'INSERT', 'PhieuMuon', 6),
(44, '2025-12-25 00:30:21', 2, 'LOGOUT', 'TaiKhoan', 2),
(45, '2025-12-25 00:30:26', 3, 'LOGIN', 'TaiKhoan', 3),
(46, '2025-12-25 00:37:01', 3, 'UPDATE', 'ThietBi', 1),
(47, '2025-12-25 00:37:09', 3, 'UPDATE', 'ThietBi', 2),
(48, '2025-12-25 00:37:22', 3, 'INSERT', 'KeHoachThanhLy', 7),
(49, '2025-12-25 00:41:59', 3, 'LOGOUT', 'TaiKhoan', 3),
(50, '2025-12-25 00:42:02', 5, 'LOGIN', 'TaiKhoan', 5),
(51, '2025-12-25 00:42:15', 5, 'DUYET', 'KeHoachThanhLy', 7),
(52, '2025-12-25 00:42:19', 5, 'LOGOUT', 'TaiKhoan', 5),
(53, '2025-12-25 00:42:23', 3, 'LOGIN', 'TaiKhoan', 3),
(54, '2025-12-25 00:42:38', 3, 'INSERT', 'KeHoachThanhLy', 8),
(55, '2025-12-25 00:44:02', 3, 'LOGOUT', 'TaiKhoan', 3),
(56, '2025-12-25 00:44:05', 3, 'LOGIN', 'TaiKhoan', 3);

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
(1, '2', '2', 'hư hỏng', 'gãy chân trụ', '2025-12-19 19:06:04', 'dang-xu-ly'),
(0, '2', '2', 'fvb', 'xcfvb', '2025-12-24 12:09:47', 'dang-xu-ly');

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
(3, 2, 2, 1, 'Hư'),
(4, 3, 3, 1, 'Tốt'),
(5, 4, 5, 1, 'Hư'),
(6, 6, 5, 1, NULL);

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
  `tinhTrang` enum('Hư nhẹ','Hư nặng','Mất','Không sửa được') DEFAULT NULL,
  `trangThaiThucHien` enum('Chờ xử lý','Đã xử lý') DEFAULT 'Chờ xử lý' COMMENT 'Trạng thái xử lý thực tế',
  `ngayXuLy` date DEFAULT NULL COMMENT 'Ngày xử lý thực tế'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietthanhly`
--

INSERT INTO `chitietthanhly` (`maCTTL`, `maTL`, `maTB`, `soLuong`, `lyDo`, `tinhTrang`, `trangThaiThucHien`, `ngayXuLy`) VALUES
(6, 6, 3, 1, 'aa', 'Hư nhẹ', 'Đã xử lý', '2025-12-24'),
(7, 7, 1, 1, 'hỏng nhiều', 'Hư nhẹ', 'Đã xử lý', '2025-12-24'),
(8, 8, 1, 1, 'hỏng nhiều', 'Hư nhẹ', 'Chờ xử lý', NULL);

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
  `nguoiDuyet` int(11) DEFAULT NULL,
  `ngayDuyet` date DEFAULT NULL COMMENT 'Ngày phê duyệt kế hoạch',
  `ngayHoanThanh` date DEFAULT NULL COMMENT 'Ngày hoàn tất thanh lý thực tế',
  `phuongPhapThanhLy` enum('Bán phế liệu','Chuyển giao','Tiêu hủy','Khác') DEFAULT 'Bán phế liệu' COMMENT 'Phương pháp thanh lý',
  `ghiChu` text DEFAULT NULL COMMENT 'Ghi chú thêm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kehoachthanhly`
--

INSERT INTO `kehoachthanhly` (`maTL`, `ngayLap`, `trangThai`, `nguoiLap`, `nguoiDuyet`, `ngayDuyet`, `ngayHoanThanh`, `phuongPhapThanhLy`, `ghiChu`) VALUES
(3, '2024-08-15', 'Từ chối', 3, 5, NULL, NULL, 'Bán phế liệu', NULL),
(6, '2025-12-24', 'Đã duyệt', 3, 5, '2025-12-24', '2025-12-24', 'Chuyển giao', 'aa'),
(7, '2025-12-24', 'Đã duyệt', 3, 5, '2025-12-24', '2025-12-24', 'Bán phế liệu', 'aaa'),
(8, '2025-12-24', 'Chờ duyệt', 3, NULL, NULL, NULL, 'Bán phế liệu', 'aaa');

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
(1, '2024-05-01', '2024-05-03', '2024-05-03', 'Từ chối', 2),
(2, '2024-06-01', '2024-06-03', '2025-12-24', 'Chờ duyệt', 2),
(3, '2024-06-05', '2024-06-07', '2025-12-24', 'Đã trả', 2),
(4, '2024-06-10', '2024-06-12', '2025-12-24', 'Đã trả', 2),
(6, '2025-12-24', '2025-12-31', NULL, 'Chờ duyệt', 2);

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
(1, 'Máy chiếu Epson EB-X06', 'Cái', '6', 2, 2, 'Hư nhẹ', 1, 0),
(2, 'Máy tính để bàn', 'Bộ', '8', 10, 9, 'Đang sửa', 2, 0),
(3, 'Loa kéo', 'Cái', '7', 1, 0, 'Hư nhẹ', 4, 0),
(4, 'Micro không dây', 'Cái', '9', 5, 4, 'Tốt', 4, 0),
(5, 'Camera quan sát', 'Cái', '8', 1, 1, 'Tốt', 2, 0),
(7, 'ádasdas', 'cái', '7', 1, 1, 'Tốt', 2, 0);

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
  MODIFY `maLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT cho bảng `chitietmuasam`
--
ALTER TABLE `chitietmuasam`
  MODIFY `maCTMS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `chitietphieumuon`
--
ALTER TABLE `chitietphieumuon`
  MODIFY `maCT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `chitietthanhly`
--
ALTER TABLE `chitietthanhly`
  MODIFY `maCTTL` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `kehoachmuasam`
--
ALTER TABLE `kehoachmuasam`
  MODIFY `maMS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `kehoachthanhly`
--
ALTER TABLE `kehoachthanhly`
  MODIFY `maTL` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `maPhieu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `thietbi`
--
ALTER TABLE `thietbi`
  MODIFY `maTB` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
