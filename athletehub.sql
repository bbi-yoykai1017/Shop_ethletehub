-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th3 03, 2026 lúc 01:42 PM
-- Phiên bản máy phục vụ: 10.4.13-MariaDB
-- Phiên bản PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `athletehub`
--
CREATE DATABASE IF NOT EXISTS `athletehub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `athletehub`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_don_hang`
--

DROP TABLE IF EXISTS `chi_tiet_don_hang`;
CREATE TABLE IF NOT EXISTS `chi_tiet_don_hang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `don_hang_id` int(11) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `gia` decimal(15,0) NOT NULL,
  `kich_thuoc_id` int(11) DEFAULT NULL,
  `mau_sac_id` int(11) DEFAULT NULL,
  `thanh_tien` decimal(15,0) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `don_hang_id` (`don_hang_id`),
  KEY `san_pham_id` (`san_pham_id`),
  KEY `kich_thuoc_id` (`kich_thuoc_id`),
  KEY `mau_sac_id` (`mau_sac_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`id`, `don_hang_id`, `san_pham_id`, `so_luong`, `gia`, `kich_thuoc_id`, `mau_sac_id`, `thanh_tien`) VALUES
(1, 1, 1, 2, '149000', 2, 1, '298000'),
(2, 1, 11, 1, '999000', 3, 3, '999000'),
(3, 2, 2, 1, '159000', 1, 2, '159000'),
(4, 2, 12, 1, '949000', 2, 2, '949000'),
(5, 3, 3, 2, '199000', 2, 1, '398000'),
(6, 3, 13, 1, '749000', 3, 1, '749000'),
(7, 4, 4, 1, '249000', 2, 1, '249000'),
(8, 4, 14, 2, '749000', 1, 2, '1498000'),
(9, 5, 5, 1, '399000', 3, 1, '399000'),
(10, 5, 15, 1, '1299000', 2, 1, '1299000'),
(11, 6, 6, 2, '379000', 2, 2, '758000'),
(12, 6, 16, 1, '549000', 3, 1, '549000'),
(13, 7, 7, 1, '129000', 2, 1, '129000'),
(14, 7, 17, 2, '1499000', 3, 3, '2998000'),
(15, 8, 8, 1, '129000', 1, 2, '129000'),
(16, 8, 18, 1, '899000', 2, 2, '899000'),
(17, 9, 9, 2, '229000', 3, 1, '458000'),
(18, 9, 19, 1, '1099000', 2, 2, '1099000'),
(19, 10, 10, 1, '229000', 2, 1, '229000'),
(20, 10, 20, 2, '449000', 3, 1, '898000');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_gio_hang`
--

DROP TABLE IF EXISTS `chi_tiet_gio_hang`;
CREATE TABLE IF NOT EXISTS `chi_tiet_gio_hang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gio_hang_id` int(11) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 1,
  `kich_thuoc_id` int(11) DEFAULT NULL,
  `mau_sac_id` int(11) DEFAULT NULL,
  `ngay_them` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `gio_hang_id` (`gio_hang_id`),
  KEY `san_pham_id` (`san_pham_id`),
  KEY `kich_thuoc_id` (`kich_thuoc_id`),
  KEY `mau_sac_id` (`mau_sac_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_gio_hang`
--

INSERT INTO `chi_tiet_gio_hang` (`id`, `gio_hang_id`, `san_pham_id`, `so_luong`, `kich_thuoc_id`, `mau_sac_id`, `ngay_them`) VALUES
(1, 1, 1, 2, 2, 1, '2026-03-03 13:40:02'),
(2, 1, 11, 1, 3, 3, '2026-03-03 13:40:02'),
(3, 2, 2, 1, 1, 2, '2026-03-03 13:40:02'),
(4, 2, 12, 1, 2, 2, '2026-03-03 13:40:02'),
(5, 3, 3, 2, 2, 1, '2026-03-03 13:40:02'),
(6, 3, 13, 1, 3, 1, '2026-03-03 13:40:02'),
(7, 4, 4, 1, 2, 1, '2026-03-03 13:40:02'),
(8, 4, 14, 2, 1, 2, '2026-03-03 13:40:02'),
(9, 5, 5, 1, 3, 1, '2026-03-03 13:40:02'),
(10, 5, 15, 1, 2, 1, '2026-03-03 13:40:02'),
(11, 6, 6, 2, 2, 2, '2026-03-03 13:40:02'),
(12, 6, 16, 1, 3, 1, '2026-03-03 13:40:02'),
(13, 7, 7, 1, 2, 1, '2026-03-03 13:40:02'),
(14, 7, 17, 2, 3, 3, '2026-03-03 13:40:02'),
(15, 8, 8, 1, 1, 2, '2026-03-03 13:40:02'),
(16, 8, 18, 1, 2, 2, '2026-03-03 13:40:02'),
(17, 9, 9, 2, 3, 1, '2026-03-03 13:40:02'),
(18, 9, 19, 1, 2, 2, '2026-03-03 13:40:02'),
(19, 10, 10, 1, 2, 1, '2026-03-03 13:40:02'),
(20, 10, 20, 2, 3, 1, '2026-03-03 13:40:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_gia`
--

DROP TABLE IF EXISTS `danh_gia`;
CREATE TABLE IF NOT EXISTS `danh_gia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `san_pham_id` int(11) NOT NULL,
  `nguoi_dung_id` int(11) NOT NULL,
  `so_sao` tinyint(4) NOT NULL CHECK (`so_sao` between 1 and 5),
  `binh_luan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_danh_gia` timestamp NOT NULL DEFAULT current_timestamp(),
  `trang_thai` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_danhgia` (`san_pham_id`,`nguoi_dung_id`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_gia`
--

INSERT INTO `danh_gia` (`id`, `san_pham_id`, `nguoi_dung_id`, `so_sao`, `binh_luan`, `ngay_danh_gia`, `trang_thai`) VALUES
(1, 1, 2, 5, 'Áo đẹp, chất liệu tốt, mặc thoải mái', '2026-03-03 13:40:03', 1),
(2, 1, 3, 4, 'Áo ổn, nhưng hơi rộng so với size', '2026-03-03 13:40:03', 1),
(3, 2, 4, 5, 'Rất thích, màu sắc đẹp', '2026-03-03 13:40:03', 1),
(4, 3, 5, 5, 'Quần short co giãn tốt, thoáng mát', '2026-03-03 13:40:03', 1),
(5, 4, 6, 4, 'Legging ôm sát, nhưng hơi dài', '2026-03-03 13:40:03', 1),
(6, 5, 7, 5, 'Áo khoác nhẹ, chống nước tốt', '2026-03-03 13:40:03', 1),
(7, 6, 8, 5, 'Mặc đẹp, ấm áp', '2026-03-03 13:40:03', 1),
(8, 7, 9, 5, 'Tanktop nam chất lượng', '2026-03-03 13:40:03', 1),
(9, 8, 10, 4, 'Nữ mặc đẹp, nhưng màu hơi chói', '2026-03-03 13:40:03', 1),
(10, 9, 2, 5, 'Quần dài ưng ý', '2026-03-03 13:40:03', 1),
(11, 10, 3, 5, 'Rất vừa vặn', '2026-03-03 13:40:03', 1),
(12, 11, 4, 5, 'Giày chạy êm, xứng đáng tiền', '2026-03-03 13:40:03', 1),
(13, 12, 5, 5, 'Nhẹ, đẹp, chạy rất thích', '2026-03-03 13:40:03', 1),
(14, 13, 6, 4, 'Tập gym ổn, nhưng đế hơi cứng', '2026-03-03 13:40:03', 1),
(15, 14, 7, 5, 'Nữ đi đẹp lắm', '2026-03-03 13:40:03', 1),
(16, 15, 8, 4, 'Giày bóng đá chất lượng tốt', '2026-03-03 13:40:03', 1),
(17, 16, 9, 5, 'Đi hằng ngày thoải mái', '2026-03-03 13:40:03', 1),
(18, 17, 10, 5, 'Leo núi bám tốt', '2026-03-03 13:40:03', 1),
(19, 18, 2, 5, 'Cầu lông nhẹ, ôm chân', '2026-03-03 13:40:03', 1),
(20, 19, 3, 4, 'Tennis ổn, giá hơi cao', '2026-03-03 13:40:03', 1),
(21, 20, 4, 5, 'Đi bộ êm, nhẹ', '2026-03-03 13:40:03', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

DROP TABLE IF EXISTS `danh_muc`;
CREATE TABLE IF NOT EXISTS `danh_muc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten_danh_muc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hinh_anh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thu_tu` int(11) DEFAULT 0,
  `trang_thai` tinyint(1) DEFAULT 1,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id`, `ten_danh_muc`, `mo_ta`, `hinh_anh`, `thu_tu`, `trang_thai`, `ngay_tao`) VALUES
(1, 'Quần áo', 'Quần áo thể thao nam nữ, đa dạng kiểu dáng và chất liệu', 'quan-ao.jpg', 1, 1, '2026-03-03 13:40:02'),
(2, 'Giày', 'Giày chạy bộ, giày tập gym, giày bóng đá chính hãng', 'giay.jpg', 2, 1, '2026-03-03 13:40:02'),
(3, 'Thiết bị', 'Dụng cụ tập luyện tại nhà: tạ, thảm, dây kháng lực', 'thiet-bi.jpg', 3, 1, '2026-03-03 13:40:02'),
(4, 'Phụ kiện', 'Bình nước, khăn, túi xách, nón, vớ thể thao', 'phu-kien.jpg', 4, 1, '2026-03-03 13:40:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

DROP TABLE IF EXISTS `don_hang`;
CREATE TABLE IF NOT EXISTS `don_hang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` int(11) NOT NULL,
  `ma_don_hang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_dat` timestamp NOT NULL DEFAULT current_timestamp(),
  `tong_tien` decimal(15,0) NOT NULL,
  `phi_van_chuyen` decimal(15,0) DEFAULT 0,
  `giam_gia` decimal(15,0) DEFAULT 0,
  `tong_cong` decimal(15,0) NOT NULL,
  `trang_thai` enum('cho_xac_nhan','dang_xu_ly','dang_giao','da_giao','da_huy','tra_hang') COLLATE utf8mb4_unicode_ci DEFAULT 'cho_xac_nhan',
  `dia_chi_giao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `phuong_thuc_thanh_toan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghi_chu` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_don_hang` (`ma_don_hang`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`id`, `nguoi_dung_id`, `ma_don_hang`, `ngay_dat`, `tong_tien`, `phi_van_chuyen`, `giam_gia`, `tong_cong`, `trang_thai`, `dia_chi_giao`, `phuong_thuc_thanh_toan`, `ghi_chu`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 2, 'DH001', '2026-03-03 13:40:02', '448000', '30000', '0', '478000', 'da_giao', '45 Đường Nguyễn Huệ, Q.1, TP.HCM', 'tien_mat', NULL, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(2, 3, 'DH002', '2026-03-03 13:40:02', '598000', '30000', '50000', '578000', 'dang_giao', '123 Đường Lê Văn Sỹ, Q.3, TP.HCM', 'chuyen_khoan', NULL, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(3, 4, 'DH003', '2026-03-03 13:40:02', '249000', '0', '0', '249000', 'cho_xac_nhan', '789 Đường Cách Mạng Tháng 8, Q.10, TP.HCM', 'vnpay', NULL, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(4, 5, 'DH004', '2026-03-03 13:40:02', '899000', '30000', '100000', '829000', 'da_giao', '321 Đường Hai Bà Trưng, Q.1, TP.HCM', 'tien_mat', NULL, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(5, 6, 'DH005', '2026-03-03 13:40:02', '1299000', '0', '200000', '1099000', 'dang_xu_ly', '654 Đường Nguyễn Văn Cừ, Q.5, TP.HCM', 'chuyen_khoan', NULL, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(6, 7, 'DH006', '2026-03-03 13:40:02', '349000', '30000', '0', '379000', 'da_huy', '987 Đường Trần Hưng Đạo, Q.1, TP.HCM', 'tien_mat', NULL, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(7, 8, 'DH007', '2026-03-03 13:40:02', '798000', '0', '0', '798000', 'da_giao', '147 Đường Phạm Ngũ Lão, Q.1, TP.HCM', 'vnpay', NULL, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(8, 9, 'DH008', '2026-03-03 13:40:02', '199000', '30000', '0', '229000', 'dang_giao', '258 Đường Bùi Viện, Q.1, TP.HCM', 'tien_mat', NULL, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(9, 10, 'DH009', '2026-03-03 13:40:02', '549000', '0', '50000', '499000', 'cho_xac_nhan', '369 Đường Võ Văn Tần, Q.3, TP.HCM', 'chuyen_khoan', NULL, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(10, 2, 'DH010', '2026-03-03 13:40:02', '1099000', '30000', '100000', '1029000', 'da_giao', '45 Đường Nguyễn Huệ, Q.1, TP.HCM', 'vnpay', NULL, '2026-03-03 13:40:02', '2026-03-03 13:40:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang`
--

DROP TABLE IF EXISTS `gio_hang`;
CREATE TABLE IF NOT EXISTS `gio_hang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` int(11) NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_nguoidung` (`nguoi_dung_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `gio_hang`
--

INSERT INTO `gio_hang` (`id`, `nguoi_dung_id`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(2, 2, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(3, 3, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(4, 4, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(5, 5, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(6, 6, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(7, 7, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(8, 8, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(9, 9, '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(10, 10, '2026-03-03 13:40:02', '2026-03-03 13:40:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hinh_anh_san_pham`
--

DROP TABLE IF EXISTS `hinh_anh_san_pham`;
CREATE TABLE IF NOT EXISTS `hinh_anh_san_pham` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `san_pham_id` int(11) NOT NULL,
  `duong_dan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thu_tu` int(11) DEFAULT 0,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `san_pham_id` (`san_pham_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hinh_anh_san_pham`
--

INSERT INTO `hinh_anh_san_pham` (`id`, `san_pham_id`, `duong_dan`, `thu_tu`, `ngay_tao`) VALUES
(1, 1, 'ao-thun-nam-1.jpg', 1, '2026-03-03 13:40:02'),
(2, 2, 'ao-thun-nu-1.jpg', 1, '2026-03-03 13:40:02'),
(3, 3, 'short-nam-1.jpg', 1, '2026-03-03 13:40:02'),
(4, 4, 'legging-nu-1.jpg', 1, '2026-03-03 13:40:02'),
(5, 5, 'ao-khoac-nam-1.jpg', 1, '2026-03-03 13:40:02'),
(6, 6, 'ao-khoac-nu-1.jpg', 1, '2026-03-03 13:40:02'),
(7, 7, 'tanktop-nam-1.jpg', 1, '2026-03-03 13:40:02'),
(8, 8, 'tanktop-nu-1.jpg', 1, '2026-03-03 13:40:02'),
(9, 9, 'quan-dai-nam-1.jpg', 1, '2026-03-03 13:40:02'),
(10, 10, 'quan-dai-nu-1.jpg', 1, '2026-03-03 13:40:02'),
(11, 11, 'giay-chay-nam-1.jpg', 1, '2026-03-03 13:40:02'),
(12, 12, 'giay-chay-nu-1.jpg', 1, '2026-03-03 13:40:02'),
(13, 13, 'giay-gym-nam-1.jpg', 1, '2026-03-03 13:40:02'),
(14, 14, 'giay-gym-nu-1.jpg', 1, '2026-03-03 13:40:02'),
(15, 15, 'giay-bong-da-1.jpg', 1, '2026-03-03 13:40:02'),
(16, 16, 'giay-da-nang-1.jpg', 1, '2026-03-03 13:40:02'),
(17, 17, 'giay-leo-nui-1.jpg', 1, '2026-03-03 13:40:02'),
(18, 18, 'giay-cau-long-1.jpg', 1, '2026-03-03 13:40:02'),
(19, 19, 'giay-tennis-1.jpg', 1, '2026-03-03 13:40:02'),
(20, 20, 'giay-di-bo-1.jpg', 1, '2026-03-03 13:40:02'),
(21, 21, 'ta-doi-5kg-1.jpg', 1, '2026-03-03 13:40:02'),
(22, 22, 'ta-don-10kg-1.jpg', 1, '2026-03-03 13:40:02'),
(23, 23, 'tham-yoga-1.jpg', 1, '2026-03-03 13:40:02'),
(24, 24, 'day-khang-luc-1.jpg', 1, '2026-03-03 13:40:02'),
(25, 25, 'con-lan-1.jpg', 1, '2026-03-03 13:40:02'),
(26, 26, 'gang-tay-1.jpg', 1, '2026-03-03 13:40:02'),
(27, 27, 'day-nhay-1.jpg', 1, '2026-03-03 13:40:02'),
(28, 28, 'bong-yoga-1.jpg', 1, '2026-03-03 13:40:02'),
(29, 29, 'dai-tap-eo-1.jpg', 1, '2026-03-03 13:40:02'),
(30, 30, 'bang-tay-1.jpg', 1, '2026-03-03 13:40:02'),
(31, 31, 'binh-nuoc-500-1.jpg', 1, '2026-03-03 13:40:02'),
(32, 32, 'binh-nuoc-1l-1.jpg', 1, '2026-03-03 13:40:02'),
(33, 33, 'tui-deo-cheo-1.jpg', 1, '2026-03-03 13:40:02'),
(34, 34, 'balo-1.jpg', 1, '2026-03-03 13:40:02'),
(35, 35, 'khan-tap-1.jpg', 1, '2026-03-03 13:40:02'),
(36, 36, 'non-1.jpg', 1, '2026-03-03 13:40:02'),
(37, 37, 'vo-1.jpg', 1, '2026-03-03 13:40:02'),
(38, 38, 'bang-do-1.jpg', 1, '2026-03-03 13:40:02'),
(39, 39, 'bao-tay-xe-dap-1.jpg', 1, '2026-03-03 13:40:02'),
(40, 40, 'kinh-1.jpg', 1, '2026-03-03 13:40:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kich_thuoc`
--

DROP TABLE IF EXISTS `kich_thuoc`;
CREATE TABLE IF NOT EXISTS `kich_thuoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten_kich_thuoc` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ten_kich_thuoc` (`ten_kich_thuoc`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kich_thuoc`
--

INSERT INTO `kich_thuoc` (`id`, `ten_kich_thuoc`, `mo_ta`) VALUES
(1, 'S', 'Small'),
(2, 'M', 'Medium'),
(3, 'L', 'Large'),
(4, 'XL', 'Extra Large'),
(5, 'XXL', 'Double Extra Large');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `mau_sac`
--

DROP TABLE IF EXISTS `mau_sac`;
CREATE TABLE IF NOT EXISTS `mau_sac` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten_mau_sac` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ma_mau` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hinh_anh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ten_mau_sac` (`ten_mau_sac`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `mau_sac`
--

INSERT INTO `mau_sac` (`id`, `ten_mau_sac`, `ma_mau`, `hinh_anh`) VALUES
(1, 'Đen', '#000000', NULL),
(2, 'Trắng', '#FFFFFF', NULL),
(3, 'Xanh', '#1a73e8', NULL),
(4, 'Đỏ', '#FF0000', NULL),
(5, 'Vàng', '#FFD700', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ma_giam_gia`
--

DROP TABLE IF EXISTS `ma_giam_gia`;
CREATE TABLE IF NOT EXISTS `ma_giam_gia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ma` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loai_giam` enum('phan_tram','tien_mat') COLLATE utf8mb4_unicode_ci NOT NULL,
  `gia_tri` decimal(15,0) NOT NULL,
  `don_hang_toi_thieu` decimal(15,0) DEFAULT 0,
  `so_luong` int(11) DEFAULT 1,
  `da_dung` int(11) DEFAULT 0,
  `ngay_bat_dau` date DEFAULT NULL,
  `ngay_ket_thuc` date DEFAULT NULL,
  `trang_thai` tinyint(1) DEFAULT 1,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma` (`ma`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ma_giam_gia`
--

INSERT INTO `ma_giam_gia` (`id`, `ma`, `mo_ta`, `loai_giam`, `gia_tri`, `don_hang_toi_thieu`, `so_luong`, `da_dung`, `ngay_bat_dau`, `ngay_ket_thuc`, `trang_thai`, `ngay_tao`) VALUES
(1, 'ATHLETE10', 'Giảm 10% cho đơn hàng từ 500k', 'phan_tram', '10', '500000', 100, 0, '2024-01-01', '2024-12-31', 1, '2026-03-03 13:40:03'),
(2, 'ATHLETE50K', 'Giảm 50k cho đơn hàng từ 300k', 'tien_mat', '50000', '300000', 200, 0, '2024-01-01', '2024-12-31', 1, '2026-03-03 13:40:03'),
(3, 'SALE20', 'Giảm 20% tối đa 100k', 'phan_tram', '20', '0', 50, 0, '2024-06-01', '2024-06-30', 1, '2026-03-03 13:40:03'),
(4, 'FREESHIP', 'Miễn phí vận chuyển', 'tien_mat', '30000', '0', 500, 0, '2024-01-01', '2024-12-31', 1, '2026-03-03 13:40:03'),
(5, 'WELCOME', 'Giảm 15% cho khách mới', 'phan_tram', '15', '0', 300, 0, '2024-01-01', '2024-12-31', 1, '2026-03-03 13:40:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

DROP TABLE IF EXISTS `nguoi_dung`;
CREATE TABLE IF NOT EXISTS `nguoi_dung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten_dang_nhap` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mat_khau` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ho_ten` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `so_dien_thoai` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia_chi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vai_tro` enum('admin','khach_hang','tai_khoan_nguoi_ban') COLLATE utf8mb4_unicode_ci DEFAULT 'khach_hang',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `ten_dang_nhap`, `email`, `mat_khau`, `ho_ten`, `so_dien_thoai`, `dia_chi`, `vai_tro`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 'admin', 'admin@email.com', 'admin123', 'Nguyễn Quản Trị', '0901234567', '123 Đường Lê Lợi, Q.1, TP.HCM', 'admin', '2026-03-03 13:40:02', '2026-03-03 13:41:20'),
(2, 'nguyenvanA', 'nguyenvana@email.com', '$2y$10$hashed_password_a', 'Nguyễn Văn An', '0901234568', '45 Đường Nguyễn Huệ, Q.1, TP.HCM', 'khach_hang', '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(3, 'tranthib', 'tranthib@email.com', '$2y$10$hashed_password_b', 'Trần Thị Bích', '0901234569', '123 Đường Lê Văn Sỹ, Q.3, TP.HCM', 'khach_hang', '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(4, 'leminhc', 'leminhc@email.com', '$2y$10$hashed_password_c', 'Lê Minh Cường', '0901234570', '789 Đường Cách Mạng Tháng 8, Q.10, TP.HCM', 'khach_hang', '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(5, 'phamthid', 'phamthid@email.com', '$2y$10$hashed_password_d', 'Phạm Thị Dung', '0901234571', '321 Đường Hai Bà Trưng, Q.1, TP.HCM', 'khach_hang', '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(6, 'hoangvane', 'hoangvane@email.com', '$2y$10$hashed_password_e', 'Hoàng Văn Em', '0901234572', '654 Đường Nguyễn Văn Cừ, Q.5, TP.HCM', 'khach_hang', '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(7, 'vuthif', 'vuthif@email.com', '$2y$10$hashed_password_f', 'Vũ Thị Phương', '0901234573', '987 Đường Trần Hưng Đạo, Q.1, TP.HCM', 'khach_hang', '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(8, 'dangvang', 'dangvang@email.com', '$2y$10$hashed_password_g', 'Đặng Văn Giàu', '0901234574', '147 Đường Phạm Ngũ Lão, Q.1, TP.HCM', 'tai_khoan_nguoi_ban', '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(9, 'bonguyenh', 'bonguyenh@email.com', '$2y$10$hashed_password_h', 'Bô Nguyễn Hạnh', '0901234575', '258 Đường Bùi Viện, Q.1, TP.HCM', 'tai_khoan_nguoi_ban', '2026-03-03 13:40:02', '2026-03-03 13:40:02'),
(10, 'chaui', 'chaui@email.com', '$2y$10$hashed_password_i', 'Châu Thị I', '0901234576', '369 Đường Võ Văn Tần, Q.3, TP.HCM', 'khach_hang', '2026-03-03 13:40:02', '2026-03-03 13:40:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham`
--

DROP TABLE IF EXISTS `san_pham`;
CREATE TABLE IF NOT EXISTS `san_pham` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten_san_pham` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gia` decimal(15,0) NOT NULL,
  `gia_khuyen_mai` decimal(15,0) DEFAULT NULL,
  `so_luong_ton` int(11) DEFAULT 0,
  `danh_muc_id` int(11) DEFAULT NULL,
  `hinh_anh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `luot_xem` int(11) DEFAULT 0,
  `luot_mua` int(11) DEFAULT 0,
  `trang_thai` tinyint(1) DEFAULT 1,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `danh_muc_id` (`danh_muc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id`, `ten_san_pham`, `mo_ta`, `gia`, `gia_khuyen_mai`, `so_luong_ton`, `danh_muc_id`, `hinh_anh`, `luot_xem`, `luot_mua`, `trang_thai`, `ngay_tao`) VALUES
(1, 'Áo thun thể thao nam', 'Áo thun cotton cao cấp, thấm hút mồ hôi tốt', '199000', '149000', 150, 1, 'ao-thun-nam.jpg', 1200, 350, 1, '2026-03-03 13:40:02'),
(2, 'Áo thun thể thao nữ', 'Áo thun nữ form rộng, chất liệu mềm mại', '199000', '159000', 120, 1, 'ao-thun-nu.jpg', 980, 280, 1, '2026-03-03 13:40:02'),
(3, 'Quần short thể thao nam', 'Quần short nam co giãn 4 chiều', '249000', '199000', 200, 1, 'short-nam.jpg', 850, 420, 1, '2026-03-03 13:40:02'),
(4, 'Quần legging nữ', 'Legging nữ tập gym, ôm sát, co giãn tốt', '299000', '249000', 180, 1, 'legging-nu.jpg', 1100, 510, 1, '2026-03-03 13:40:02'),
(5, 'Áo khoác thể thao nam', 'Áo khoác gió chống nước nhẹ', '499000', '399000', 80, 1, 'ao-khoac-nam.jpg', 620, 120, 1, '2026-03-03 13:40:02'),
(6, 'Áo khoác thể thao nữ', 'Áo khoác nữ có mũ, chất liệu thoáng', '479000', '379000', 70, 1, 'ao-khoac-nu.jpg', 540, 95, 1, '2026-03-03 13:40:02'),
(7, 'Áo tanktop nam', 'Áo ba lỗ nam tập gym, thoáng mát', '159000', '129000', 220, 1, 'tanktop-nam.jpg', 730, 410, 1, '2026-03-03 13:40:02'),
(8, 'Áo tanktop nữ', 'Áo ba lỗ nữ phong cách', '159000', '129000', 190, 1, 'tanktop-nu.jpg', 680, 380, 1, '2026-03-03 13:40:02'),
(9, 'Quần dài thể thao nam', 'Quần dài nam ống suông, có túi', '279000', '229000', 140, 1, 'quan-dai-nam.jpg', 410, 180, 1, '2026-03-03 13:40:02'),
(10, 'Quần dài thể thao nữ', 'Quần dài nữ co giãn, thích hợp chạy bộ', '279000', '229000', 130, 1, 'quan-dai-nu.jpg', 390, 165, 1, '2026-03-03 13:40:02'),
(11, 'Giày chạy bộ nam cao cấp', 'Giày chạy bộ đệm khí, giảm chấn tốt', '1299000', '999000', 60, 2, 'giay-chay-nam.jpg', 2100, 670, 1, '2026-03-03 13:40:02'),
(12, 'Giày chạy bộ nữ cao cấp', 'Giày chạy bộ nữ nhẹ, êm chân', '1249000', '949000', 55, 2, 'giay-chay-nu.jpg', 1850, 540, 1, '2026-03-03 13:40:02'),
(13, 'Giày tập gym nam', 'Giày tập tạ đế bằng, độ bám cao', '899000', '749000', 75, 2, 'giay-gym-nam.jpg', 1320, 420, 1, '2026-03-03 13:40:02'),
(14, 'Giày tập gym nữ', 'Giày tập gym nữ thiết kế thời trang', '899000', '749000', 70, 2, 'giay-gym-nu.jpg', 1210, 390, 1, '2026-03-03 13:40:02'),
(15, 'Giày bóng đá cỏ nhân tạo', 'Giày đinh tán phù hợp sân cỏ nhân tạo', '1599000', '1299000', 40, 2, 'giay-bong-da.jpg', 890, 210, 1, '2026-03-03 13:40:02'),
(16, 'Giày thể thao đa năng', 'Giày đi hàng ngày, phối màu trẻ trung', '699000', '549000', 110, 2, 'giay-da-nang.jpg', 1520, 710, 1, '2026-03-03 13:40:02'),
(17, 'Giày leo núi', 'Giày leo núi chống trơn trượt', '1799000', '1499000', 30, 2, 'giay-leo-nui.jpg', 340, 85, 1, '2026-03-03 13:40:02'),
(18, 'Giày cầu lông', 'Giày chuyên cầu lông, nhẹ, ôm chân', '1099000', '899000', 65, 2, 'giay-cau-long.jpg', 760, 290, 1, '2026-03-03 13:40:02'),
(19, 'Giày tennis', 'Giày tennis đế cứng, chống mòn', '1399000', '1099000', 45, 2, 'giay-tennis.jpg', 520, 145, 1, '2026-03-03 13:40:02'),
(20, 'Giày đi bộ', 'Giày đi bộ êm chân, thiết kế đơn giản', '549000', '449000', 120, 2, 'giay-di-bo.jpg', 640, 380, 1, '2026-03-03 13:40:02'),
(21, 'Tạ đôi 5kg', 'Tạ đôi bọc cao su, chống ồn', '299000', '259000', 200, 3, 'ta-doi-5kg.jpg', 820, 540, 1, '2026-03-03 13:40:02'),
(22, 'Tạ đơn 10kg', 'Tạ đơn gang đúc, tay cầm chống trượt', '499000', '399000', 150, 3, 'ta-don-10kg.jpg', 710, 390, 1, '2026-03-03 13:40:02'),
(23, 'Thảm tập yoga', 'Thảm tập dày 10mm, chống trơn', '349000', '299000', 180, 3, 'tham-yoga.jpg', 930, 610, 1, '2026-03-03 13:40:02'),
(24, 'Dây kháng lực', 'Bộ dây kháng lực 5 mức độ', '199000', '149000', 250, 3, 'day-khang-luc.jpg', 670, 490, 1, '2026-03-03 13:40:02'),
(25, 'Con lăn massage', 'Con lăn thể thao giảm đau cơ', '249000', '199000', 140, 3, 'con-lan.jpg', 430, 280, 1, '2026-03-03 13:40:02'),
(26, 'Găng tay tập tạ', 'Găng tay chống chai tay, thấm mồ hôi', '179000', '139000', 210, 3, 'gang-tay.jpg', 550, 390, 1, '2026-03-03 13:40:02'),
(27, 'Dây nhảy thể thao', 'Dây nhảy tốc độ, có thể điều chỉnh', '99000', '79000', 300, 3, 'day-nhay.jpg', 480, 520, 1, '2026-03-03 13:40:02'),
(28, 'Bóng tập yoga', 'Bóng tập 65cm, chịu lực tốt', '199000', '159000', 120, 3, 'bong-yoga.jpg', 290, 160, 1, '2026-03-03 13:40:02'),
(29, 'Đai tập eo', 'Đai lưng hỗ trợ tập nặng', '299000', '249000', 90, 3, 'dai-tap-eo.jpg', 320, 140, 1, '2026-03-03 13:40:02'),
(30, 'Băng tay thể thao', 'Băng quấn tay chống nắng, thấm hút', '79000', '59000', 400, 3, 'bang-tay.jpg', 270, 390, 1, '2026-03-03 13:40:02'),
(31, 'Bình nước giữ nhiệt 500ml', 'Bình thép không gỉ, giữ lạnh 24h', '249000', '199000', 220, 4, 'binh-nuoc-500.jpg', 1150, 720, 1, '2026-03-03 13:40:02'),
(32, 'Bình nước giữ nhiệt 1L', 'Bình lớn dành cho người tập nặng', '329000', '279000', 180, 4, 'binh-nuoc-1l.jpg', 890, 510, 1, '2026-03-03 13:40:02'),
(33, 'Túi thể thao đeo chéo', 'Túi nhỏ gọn, nhiều ngăn', '399000', '329000', 140, 4, 'tui-deo-cheo.jpg', 620, 280, 1, '2026-03-03 13:40:02'),
(34, 'Balo thể thao', 'Balo chống nước, có ngăn đựng giày', '599000', '499000', 100, 4, 'balo.jpg', 740, 320, 1, '2026-03-03 13:40:02'),
(35, 'Khăn tập thấm hút', 'Khăn sợi tre, khô nhanh', '89000', '69000', 350, 4, 'khan-tap.jpg', 430, 550, 1, '2026-03-03 13:40:02'),
(36, 'Nón thể thao', 'Nón lưỡi trai thoáng khí', '149000', '119000', 200, 4, 'non.jpg', 380, 310, 1, '2026-03-03 13:40:02'),
(37, 'Vớ thể thao cổ cao', 'Vớ chuyên dụng cho chạy bộ', '69000', '49000', 500, 4, 'vo.jpg', 290, 470, 1, '2026-03-03 13:40:02'),
(38, 'Băng đô thể thao', 'Băng đô thấm hút mồ hôi', '49000', '39000', 600, 4, 'bang-do.jpg', 210, 390, 1, '2026-03-03 13:40:02'),
(39, 'Bao tay xe đạp', 'Găng tay đệm gel chống tê mỏi', '199000', '159000', 130, 4, 'bao-tay-xe-dap.jpg', 180, 95, 1, '2026-03-03 13:40:02'),
(40, 'Kính thể thao', 'Kính chống UV, chống trầy', '399000', '349000', 80, 4, 'kinh.jpg', 260, 110, 1, '2026-03-03 13:40:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham_kich_thuoc`
--

DROP TABLE IF EXISTS `san_pham_kich_thuoc`;
CREATE TABLE IF NOT EXISTS `san_pham_kich_thuoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `san_pham_id` int(11) NOT NULL,
  `kich_thuoc_id` int(11) NOT NULL,
  `so_luong` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_sanpham_size` (`san_pham_id`,`kich_thuoc_id`),
  KEY `kich_thuoc_id` (`kich_thuoc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham_kich_thuoc`
--

INSERT INTO `san_pham_kich_thuoc` (`id`, `san_pham_id`, `kich_thuoc_id`, `so_luong`) VALUES
(1, 1, 1, 30),
(2, 1, 2, 40),
(3, 1, 3, 35),
(4, 1, 4, 25),
(5, 1, 5, 20),
(6, 2, 1, 25),
(7, 2, 2, 35),
(8, 2, 3, 40),
(9, 2, 4, 30),
(10, 2, 5, 15),
(11, 3, 2, 50),
(12, 3, 3, 60),
(13, 3, 4, 40),
(14, 3, 5, 30),
(15, 4, 1, 40),
(16, 4, 2, 45),
(17, 4, 3, 40),
(18, 4, 4, 30),
(19, 4, 5, 25),
(20, 5, 2, 20),
(21, 5, 3, 25),
(22, 5, 4, 20),
(23, 5, 5, 15),
(24, 6, 1, 15),
(25, 6, 2, 20),
(26, 6, 3, 20),
(27, 6, 4, 15),
(28, 7, 2, 50),
(29, 7, 3, 55),
(30, 7, 4, 40),
(31, 7, 5, 30),
(32, 8, 1, 40),
(33, 8, 2, 45),
(34, 8, 3, 40),
(35, 8, 4, 30),
(36, 9, 2, 30),
(37, 9, 3, 35),
(38, 9, 4, 25),
(39, 9, 5, 20),
(40, 10, 1, 30),
(41, 10, 2, 35),
(42, 10, 3, 30),
(43, 10, 4, 20),
(44, 11, 2, 15),
(45, 11, 3, 18),
(46, 11, 4, 12),
(47, 11, 5, 8),
(48, 12, 1, 14),
(49, 12, 2, 16),
(50, 12, 3, 15),
(51, 12, 4, 10),
(52, 13, 2, 20),
(53, 13, 3, 22),
(54, 13, 4, 18),
(55, 13, 5, 12),
(56, 14, 1, 18),
(57, 14, 2, 20),
(58, 14, 3, 18),
(59, 14, 4, 12),
(60, 15, 2, 10),
(61, 15, 3, 12),
(62, 15, 4, 10),
(63, 15, 5, 6),
(64, 16, 1, 25),
(65, 16, 2, 30),
(66, 16, 3, 28),
(67, 16, 4, 20),
(68, 16, 5, 15),
(69, 17, 2, 8),
(70, 17, 3, 10),
(71, 17, 4, 8),
(72, 17, 5, 4),
(73, 18, 2, 16),
(74, 18, 3, 18),
(75, 18, 4, 14),
(76, 18, 5, 10),
(77, 19, 2, 12),
(78, 19, 3, 14),
(79, 19, 4, 10),
(80, 19, 5, 6),
(81, 20, 1, 30),
(82, 20, 2, 35),
(83, 20, 3, 30),
(84, 20, 4, 20),
(85, 20, 5, 15);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham_mau_sac`
--

DROP TABLE IF EXISTS `san_pham_mau_sac`;
CREATE TABLE IF NOT EXISTS `san_pham_mau_sac` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `san_pham_id` int(11) NOT NULL,
  `mau_sac_id` int(11) NOT NULL,
  `hinh_anh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_sanpham_mau` (`san_pham_id`,`mau_sac_id`),
  KEY `mau_sac_id` (`mau_sac_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham_mau_sac`
--

INSERT INTO `san_pham_mau_sac` (`id`, `san_pham_id`, `mau_sac_id`, `hinh_anh`) VALUES
(1, 1, 1, 'ao-thun-nam-den.jpg'),
(2, 1, 3, 'ao-thun-nam-xanh.jpg'),
(3, 1, 4, 'ao-thun-nam-do.jpg'),
(4, 2, 2, 'ao-thun-nu-trang.jpg'),
(5, 2, 3, 'ao-thun-nu-xanh.jpg'),
(6, 2, 5, 'ao-thun-nu-vang.jpg'),
(7, 3, 1, 'short-nam-den.jpg'),
(8, 3, 3, 'short-nam-xanh.jpg'),
(9, 3, 4, 'short-nam-do.jpg'),
(10, 4, 1, 'legging-nu-den.jpg'),
(11, 4, 2, 'legging-nu-trang.jpg'),
(12, 4, 3, 'legging-nu-xanh.jpg'),
(13, 5, 1, 'ao-khoac-nam-den.jpg'),
(14, 5, 3, 'ao-khoac-nam-xanh.jpg'),
(15, 5, 4, 'ao-khoac-nam-do.jpg'),
(16, 6, 2, 'ao-khoac-nu-trang.jpg'),
(17, 6, 3, 'ao-khoac-nu-xanh.jpg'),
(18, 6, 5, 'ao-khoac-nu-vang.jpg'),
(19, 7, 1, 'tanktop-nam-den.jpg'),
(20, 7, 3, 'tanktop-nam-xanh.jpg'),
(21, 7, 4, 'tanktop-nam-do.jpg'),
(22, 8, 2, 'tanktop-nu-trang.jpg'),
(23, 8, 3, 'tanktop-nu-xanh.jpg'),
(24, 8, 5, 'tanktop-nu-vang.jpg'),
(25, 9, 1, 'quan-dai-nam-den.jpg'),
(26, 9, 3, 'quan-dai-nam-xanh.jpg'),
(27, 9, 4, 'quan-dai-nam-do.jpg'),
(28, 10, 1, 'quan-dai-nu-den.jpg'),
(29, 10, 2, 'quan-dai-nu-trang.jpg'),
(30, 10, 3, 'quan-dai-nu-xanh.jpg'),
(31, 11, 1, 'giay-chay-nam-den.jpg'),
(32, 11, 3, 'giay-chay-nam-xanh.jpg'),
(33, 11, 4, 'giay-chay-nam-do.jpg'),
(34, 12, 2, 'giay-chay-nu-trang.jpg'),
(35, 12, 3, 'giay-chay-nu-xanh.jpg'),
(36, 12, 5, 'giay-chay-nu-vang.jpg'),
(37, 13, 1, 'giay-gym-nam-den.jpg'),
(38, 13, 3, 'giay-gym-nam-xanh.jpg'),
(39, 13, 4, 'giay-gym-nam-do.jpg'),
(40, 14, 2, 'giay-gym-nu-trang.jpg'),
(41, 14, 3, 'giay-gym-nu-xanh.jpg'),
(42, 14, 5, 'giay-gym-nu-vang.jpg'),
(43, 15, 1, 'giay-bong-da-den.jpg'),
(44, 15, 4, 'giay-bong-da-do.jpg'),
(45, 15, 3, 'giay-bong-da-xanh.jpg'),
(46, 16, 1, 'giay-da-nang-den.jpg'),
(47, 16, 2, 'giay-da-nang-trang.jpg'),
(48, 16, 3, 'giay-da-nang-xanh.jpg'),
(49, 17, 1, 'giay-leo-nui-den.jpg'),
(50, 17, 3, 'giay-leo-nui-xanh.jpg'),
(51, 17, 4, 'giay-leo-nui-do.jpg'),
(52, 18, 1, 'giay-cau-long-den.jpg'),
(53, 18, 2, 'giay-cau-long-trang.jpg'),
(54, 18, 3, 'giay-cau-long-xanh.jpg'),
(55, 19, 2, 'giay-tennis-trang.jpg'),
(56, 19, 3, 'giay-tennis-xanh.jpg'),
(57, 19, 4, 'giay-tennis-do.jpg'),
(58, 20, 1, 'giay-di-bo-den.jpg'),
(59, 20, 2, 'giay-di-bo-trang.jpg'),
(60, 20, 3, 'giay-di-bo-xanh.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `yeu_thich`
--

DROP TABLE IF EXISTS `yeu_thich`;
CREATE TABLE IF NOT EXISTS `yeu_thich` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` int(11) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `ngay_them` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_yeuthich` (`nguoi_dung_id`,`san_pham_id`),
  KEY `san_pham_id` (`san_pham_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `yeu_thich`
--

INSERT INTO `yeu_thich` (`id`, `nguoi_dung_id`, `san_pham_id`, `ngay_them`) VALUES
(1, 2, 1, '2026-03-03 13:40:03'),
(2, 2, 3, '2026-03-03 13:40:03'),
(3, 2, 5, '2026-03-03 13:40:03'),
(4, 3, 2, '2026-03-03 13:40:03'),
(5, 3, 4, '2026-03-03 13:40:03'),
(6, 3, 6, '2026-03-03 13:40:03'),
(7, 4, 7, '2026-03-03 13:40:03'),
(8, 4, 9, '2026-03-03 13:40:03'),
(9, 4, 11, '2026-03-03 13:40:03'),
(10, 5, 8, '2026-03-03 13:40:03'),
(11, 5, 10, '2026-03-03 13:40:03'),
(12, 5, 12, '2026-03-03 13:40:03'),
(13, 6, 13, '2026-03-03 13:40:03'),
(14, 6, 14, '2026-03-03 13:40:03'),
(15, 6, 15, '2026-03-03 13:40:03');

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_3` FOREIGN KEY (`kich_thuoc_id`) REFERENCES `kich_thuoc` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_4` FOREIGN KEY (`mau_sac_id`) REFERENCES `mau_sac` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `chi_tiet_gio_hang`
--
ALTER TABLE `chi_tiet_gio_hang`
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_1` FOREIGN KEY (`gio_hang_id`) REFERENCES `gio_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_3` FOREIGN KEY (`kich_thuoc_id`) REFERENCES `kich_thuoc` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_4` FOREIGN KEY (`mau_sac_id`) REFERENCES `mau_sac` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD CONSTRAINT `danh_gia_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `danh_gia_ibfk_2` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  ADD CONSTRAINT `hinh_anh_san_pham_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `san_pham_ibfk_1` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `san_pham_kich_thuoc`
--
ALTER TABLE `san_pham_kich_thuoc`
  ADD CONSTRAINT `san_pham_kich_thuoc_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `san_pham_kich_thuoc_ibfk_2` FOREIGN KEY (`kich_thuoc_id`) REFERENCES `kich_thuoc` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `san_pham_mau_sac`
--
ALTER TABLE `san_pham_mau_sac`
  ADD CONSTRAINT `san_pham_mau_sac_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `san_pham_mau_sac_ibfk_2` FOREIGN KEY (`mau_sac_id`) REFERENCES `mau_sac` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `yeu_thich`
--
ALTER TABLE `yeu_thich`
  ADD CONSTRAINT `yeu_thich_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `yeu_thich_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
