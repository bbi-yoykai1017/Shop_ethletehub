-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th3 04, 2026 lúc 07:49 AM
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
-- Cấu trúc bảng cho bảng `bien_the_san_pham`
--

DROP TABLE IF EXISTS `bien_the_san_pham`;
CREATE TABLE IF NOT EXISTS `bien_the_san_pham` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID biến thể',
  `san_pham_id` int(11) NOT NULL COMMENT 'ID sản phẩm',
  `kich_thuoc_id` int(11) DEFAULT NULL COMMENT 'ID kích thước',
  `mau_sac_id` int(11) DEFAULT NULL COMMENT 'ID màu',
  `so_luong_ton` int(11) DEFAULT 0 COMMENT 'Số lượng tồn kho',
  `hinh_anh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh biến thể',
  `gia_them` decimal(15,2) DEFAULT 0.00 COMMENT 'Giá thêm (nếu có)',
  `trang_thai` tinyint(4) DEFAULT 1 COMMENT 'Trạng thái',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bien_the` (`san_pham_id`,`kich_thuoc_id`,`mau_sac_id`),
  KEY `kich_thuoc_id` (`kich_thuoc_id`),
  KEY `mau_sac_id` (`mau_sac_id`),
  KEY `idx_san_pham_id` (`san_pham_id`),
  KEY `idx_so_luong_ton` (`so_luong_ton`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng biến thể sản phẩm (Size + Màu)';

--
-- Đang đổ dữ liệu cho bảng `bien_the_san_pham`
--

INSERT INTO `bien_the_san_pham` (`id`, `san_pham_id`, `kich_thuoc_id`, `mau_sac_id`, `so_luong_ton`, `hinh_anh`, `gia_them`, `trang_thai`, `ngay_tao`) VALUES
(1, 1, 3, 1, 50, 'ao-thun-nam-den-M.jpg', '0.00', 1, '2026-01-10 08:00:00'),
(2, 1, 4, 1, 45, 'ao-thun-nam-den-L.jpg', '0.00', 1, '2026-01-10 08:00:00'),
(3, 1, 3, 3, 40, 'ao-thun-nam-xanh-M.jpg', '0.00', 1, '2026-01-10 08:00:00'),
(4, 1, 4, 5, 35, 'ao-thun-nam-do-L.jpg', '0.00', 1, '2026-01-10 08:00:00'),
(5, 1, 3, 2, 30, 'ao-thun-nam-trang-M.jpg', '0.00', 1, '2026-01-10 08:00:00'),
(6, 2, 2, 2, 38, 'ao-thun-nu-trang-S.jpg', '0.00', 1, '2026-01-10 09:30:00'),
(7, 2, 3, 3, 42, 'ao-thun-nu-xanh-M.jpg', '0.00', 1, '2026-01-10 09:30:00'),
(8, 2, 4, 5, 35, 'ao-thun-nu-vang-L.jpg', '0.00', 1, '2026-01-10 09:30:00'),
(9, 2, 3, 1, 40, 'ao-thun-nu-den-M.jpg', '0.00', 1, '2026-01-10 09:30:00'),
(10, 3, 3, 1, 60, 'short-nam-den-M.jpg', '0.00', 1, '2026-01-10 11:00:00'),
(11, 3, 4, 3, 55, 'short-nam-xanh-L.jpg', '0.00', 1, '2026-01-10 11:00:00'),
(12, 3, 3, 5, 50, 'short-nam-do-M.jpg', '0.00', 1, '2026-01-10 11:00:00'),
(13, 4, 2, 1, 45, 'legging-nu-den-S.jpg', '0.00', 1, '2026-01-10 13:15:00'),
(14, 4, 3, 1, 50, 'legging-nu-den-M.jpg', '0.00', 1, '2026-01-10 13:15:00'),
(15, 4, 4, 1, 40, 'legging-nu-den-L.jpg', '0.00', 1, '2026-01-10 13:15:00'),
(16, 4, 3, 2, 35, 'legging-nu-trang-M.jpg', '0.00', 1, '2026-01-10 13:15:00'),
(17, 11, 7, 1, 35, 'giay-chay-nam-35-den.jpg', '0.00', 1, '2026-01-11 08:00:00'),
(18, 11, 8, 1, 40, 'giay-chay-nam-36-den.jpg', '0.00', 1, '2026-01-11 08:00:00'),
(19, 11, 9, 1, 45, 'giay-chay-nam-37-den.jpg', '0.00', 1, '2026-01-11 08:00:00'),
(20, 11, 10, 1, 50, 'giay-chay-nam-38-den.jpg', '0.00', 1, '2026-01-11 08:00:00'),
(21, 11, 11, 1, 48, 'giay-chay-nam-39-den.jpg', '0.00', 1, '2026-01-11 08:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cai_dat`
--

DROP TABLE IF EXISTS `cai_dat`;
CREATE TABLE IF NOT EXISTS `cai_dat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `khoa` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gia_tri` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loai_du_lieu` enum('text','number','boolean','json') COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `mo_ta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `khoa` (`khoa`),
  KEY `idx_khoa` (`khoa`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng cài đặt hệ thống';

--
-- Đang đổ dữ liệu cho bảng `cai_dat`
--

INSERT INTO `cai_dat` (`id`, `khoa`, `gia_tri`, `loai_du_lieu`, `mo_ta`, `ngay_cap_nhat`) VALUES
(1, 'ten_website', 'AthleteHub - Cửa Hàng Đồ Thể Thao', 'text', 'Tên website', '2026-03-04 14:48:24'),
(2, 'email_admin', 'admin@athletehub.com', 'text', 'Email liên hệ admin', '2026-03-04 14:48:24'),
(3, 'email_support', 'support@athletehub.com', 'text', 'Email hỗ trợ khách hàng', '2026-03-04 14:48:24'),
(4, 'so_dien_thoai', '0912345678', 'text', 'Số điện thoại chính', '2026-03-04 14:48:24'),
(5, 'so_dien_thoai_ho_tro', '0987654321', 'text', 'Số điện thoại hỗ trợ', '2026-03-04 14:48:24'),
(6, 'dia_chi', '123 Đường Thể Thao, Q1, TP.HCM, Việt Nam', 'text', 'Địa chỉ công ty', '2026-03-04 14:48:24'),
(7, 'phi_ship_mac_dinh', '25000', 'number', 'Phí vận chuyển mặc định (VNĐ)', '2026-03-04 14:48:24'),
(8, 'mien_phi_ship_tu', '500000', 'number', 'Miễn phí ship từ số tiền (VNĐ)', '2026-03-04 14:48:24'),
(9, 'thoi_gian_ship_tieu_chuan', '2-3', 'text', 'Thời gian giao hàng tiêu chuẩn (ngày)', '2026-03-04 14:48:24'),
(10, 'thoi_gian_ship_nhanh', '1', 'text', 'Thời gian giao hàng nhanh (ngày)', '2026-03-04 14:48:24'),
(11, 'tong_tien_don_toi_thieu', '50000', 'number', 'Tổng tiền đơn hàng tối thiểu (VNĐ)', '2026-03-04 14:48:24'),
(12, 'so_luong_san_pham_max_gio', '99', 'number', 'Số lượng sản phẩm tối đa trong giỏ', '2026-03-04 14:48:24'),
(13, 'ngay_lam_viec_bat_dau', '08:00', 'text', 'Giờ bắt đầu làm việc', '2026-03-04 14:48:24'),
(14, 'ngay_lam_viec_ket_thuc', '21:00', 'text', 'Giờ kết thúc làm việc', '2026-03-04 14:48:24'),
(15, 'che_do_bao_tri', 'false', 'boolean', 'Website đang bảo trì?', '2026-03-04 14:48:24'),
(16, 'thong_bao_bao_tri', 'Website đang bảo trì. Vui lòng quay lại sau!', 'text', 'Thông báo khi bảo trì', '2026-03-04 14:48:24'),
(17, 'logo_url', 'logo-athletehub.png', 'text', 'URL logo website', '2026-03-04 14:48:24'),
(18, 'favicon_url', 'favicon.ico', 'text', 'URL favicon', '2026-03-04 14:48:24'),
(19, 'so_san_pham_trang_chu', '12', 'number', 'Số sản phẩm hiển thị trang chủ', '2026-03-04 14:48:24'),
(20, 'so_san_pham_trang_danh_sach', '20', 'number', 'Số sản phẩm hiển thị mỗi trang', '2026-03-04 14:48:24'),
(21, 'facebook_url', 'https://facebook.com/athletehub', 'text', 'URL Facebook', '2026-03-04 14:48:24'),
(22, 'instagram_url', 'https://instagram.com/athletehub', 'text', 'URL Instagram', '2026-03-04 14:48:24'),
(23, 'youtube_url', 'https://youtube.com/@athletehub', 'text', 'URL Youtube', '2026-03-04 14:48:24'),
(24, 'zalo_url', 'https://zalo.me/athletehub', 'text', 'URL Zalo', '2026-03-04 14:48:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_don_hang`
--

DROP TABLE IF EXISTS `chi_tiet_don_hang`;
CREATE TABLE IF NOT EXISTS `chi_tiet_don_hang` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID chi tiết',
  `don_hang_id` int(11) NOT NULL COMMENT 'ID đơn hàng',
  `san_pham_id` int(11) NOT NULL COMMENT 'ID sản phẩm',
  `bien_the_san_pham_id` int(11) DEFAULT NULL COMMENT 'ID biến thể',
  `kich_thuoc_id` int(11) DEFAULT NULL COMMENT 'ID kích thước',
  `mau_sac_id` int(11) DEFAULT NULL COMMENT 'ID màu',
  `so_luong` int(11) NOT NULL COMMENT 'Số lượng',
  `gia` decimal(15,2) NOT NULL COMMENT 'Giá bán',
  `thanh_tien` decimal(15,2) NOT NULL COMMENT 'Thành tiền',
  PRIMARY KEY (`id`),
  KEY `bien_the_san_pham_id` (`bien_the_san_pham_id`),
  KEY `kich_thuoc_id` (`kich_thuoc_id`),
  KEY `mau_sac_id` (`mau_sac_id`),
  KEY `idx_don_hang_id` (`don_hang_id`),
  KEY `idx_san_pham_id` (`san_pham_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng chi tiết đơn hàng';

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`id`, `don_hang_id`, `san_pham_id`, `bien_the_san_pham_id`, `kich_thuoc_id`, `mau_sac_id`, `so_luong`, `gia`, `thanh_tien`) VALUES
(1, 1, 1, 1, 3, 1, 2, '149000.00', '298000.00'),
(2, 1, 11, 21, 9, 1, 1, '999000.00', '999000.00'),
(3, 2, 2, 6, 2, 2, 1, '159000.00', '159000.00'),
(4, 2, 4, 14, 3, 1, 1, '249000.00', '249000.00'),
(5, 3, 3, 11, 3, 5, 2, '199000.00', '398000.00'),
(6, 4, 5, NULL, 4, 1, 1, '399000.00', '399000.00'),
(7, 5, 3, 11, 3, 5, 1, '199000.00', '199000.00'),
(8, 5, 1, 1, 3, 1, 1, '149000.00', '149000.00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_gio_hang`
--

DROP TABLE IF EXISTS `chi_tiet_gio_hang`;
CREATE TABLE IF NOT EXISTS `chi_tiet_gio_hang` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID chi tiết',
  `gio_hang_id` int(11) NOT NULL COMMENT 'ID giỏ hàng',
  `san_pham_id` int(11) NOT NULL COMMENT 'ID sản phẩm',
  `bien_the_san_pham_id` int(11) DEFAULT NULL COMMENT 'ID biến thể (size + màu)',
  `kich_thuoc_id` int(11) DEFAULT NULL COMMENT 'ID kích thước (dự phòng)',
  `mau_sac_id` int(11) DEFAULT NULL COMMENT 'ID màu (dự phòng)',
  `so_luong` int(11) NOT NULL DEFAULT 1 COMMENT 'Số lượng',
  `gia` decimal(15,2) DEFAULT NULL COMMENT 'Giá lúc thêm',
  `ngay_them` datetime DEFAULT current_timestamp() COMMENT 'Ngày thêm',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_chi_tiet` (`gio_hang_id`,`san_pham_id`,`bien_the_san_pham_id`),
  KEY `bien_the_san_pham_id` (`bien_the_san_pham_id`),
  KEY `kich_thuoc_id` (`kich_thuoc_id`),
  KEY `mau_sac_id` (`mau_sac_id`),
  KEY `idx_gio_hang_id` (`gio_hang_id`),
  KEY `idx_san_pham_id` (`san_pham_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng chi tiết giỏ hàng';

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_gio_hang`
--

INSERT INTO `chi_tiet_gio_hang` (`id`, `gio_hang_id`, `san_pham_id`, `bien_the_san_pham_id`, `kich_thuoc_id`, `mau_sac_id`, `so_luong`, `gia`, `ngay_them`) VALUES
(1, 1, 1, 1, 3, 1, 2, '149000.00', '2026-03-03 14:10:00'),
(2, 1, 11, 21, 9, 1, 1, '999000.00', '2026-03-03 14:15:00'),
(3, 2, 2, 6, 2, 2, 1, '159000.00', '2026-03-03 13:30:00'),
(4, 2, 4, 14, 3, 1, 1, '249000.00', '2026-03-03 13:40:00'),
(5, 3, 3, 11, 3, 5, 2, '199000.00', '2026-03-03 14:50:00'),
(6, 4, 5, NULL, 4, 1, 1, '399000.00', '2026-03-03 14:25:00'),
(7, 5, 12, NULL, 10, 1, 1, '949000.00', '2026-03-03 15:20:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_gia`
--

DROP TABLE IF EXISTS `danh_gia`;
CREATE TABLE IF NOT EXISTS `danh_gia` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID đánh giá',
  `san_pham_id` int(11) NOT NULL COMMENT 'ID sản phẩm',
  `nguoi_dung_id` int(11) NOT NULL COMMENT 'ID người dùng',
  `so_sao` int(11) DEFAULT NULL COMMENT 'Số sao (1-5)',
  `binh_luan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bình luận đánh giá',
  `trang_thai` tinyint(4) DEFAULT 1 COMMENT 'Trạng thái (1=hiển thị, 0=ẩn)',
  `ngay_danh_gia` datetime DEFAULT current_timestamp() COMMENT 'Ngày đánh giá',
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_danh_gia` (`san_pham_id`,`nguoi_dung_id`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`),
  KEY `idx_san_pham_id` (`san_pham_id`),
  KEY `idx_so_sao` (`so_sao`),
  KEY `idx_trang_thai` (`trang_thai`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng đánh giá sản phẩm';

--
-- Đang đổ dữ liệu cho bảng `danh_gia`
--

INSERT INTO `danh_gia` (`id`, `san_pham_id`, `nguoi_dung_id`, `so_sao`, `binh_luan`, `trang_thai`, `ngay_danh_gia`, `ngay_cap_nhat`) VALUES
(1, 1, 2, 5, 'Áo rất đẹp, chất lượng tốt, mặc thoải mái. Giặt mềm không co rút. Rất hài lòng', 1, '2026-02-20 10:30:00', '2026-02-20 10:30:00'),
(2, 1, 3, 4, 'Áo ổn, nhưng hơi rộng so với size. Chất liệu tốt, giặt được nhiều lần', 1, '2026-02-21 14:15:00', '2026-02-21 14:15:00'),
(3, 1, 4, 5, 'Rất thích áo, màu sắc đẹp, không phai sau giặt. Mặc rất thoáng mát', 1, '2026-02-22 09:45:00', '2026-02-22 09:45:00'),
(4, 1, 5, 4, 'Áo tốt, giáo hợp lý. Tuy nhiên cộ dài một chút so với bình thường', 1, '2026-02-23 16:20:00', '2026-02-23 16:20:00'),
(5, 1, 6, 5, 'Áo đẹp, chất lượng, giá cả phải chăng. Sẽ mua lại', 1, '2026-02-24 11:00:00', '2026-02-24 11:00:00'),
(6, 3, 7, 5, 'Quần short rất tốt, co giãn tốt, thoáng mát. Phù hợp cho chạy bộ', 1, '2026-02-25 13:30:00', '2026-02-25 13:30:00'),
(7, 3, 8, 4, 'Quần ổn, nhưng thời gian đầu hơi chặt. Sau giặt vừa vặn hơn', 1, '2026-02-26 15:45:00', '2026-02-26 15:45:00'),
(8, 3, 9, 5, 'Rất thích quần, cảm giác mềm mại, thoáng mát. Sẽ mua thêm size khác', 1, '2026-02-27 10:15:00', '2026-02-27 10:15:00'),
(9, 11, 2, 5, 'Giày rất đẹp và êm chân. Tôi chạy bộ 10km không cảm thấy mệt. Xứng đáng tiền', 1, '2026-02-28 08:00:00', '2026-02-28 08:00:00'),
(10, 11, 4, 4, 'Giày tốt, đế êm, bám chân tốt. Hơi chặt ban đầu nhưng thoải mái sau', 1, '2026-03-01 12:30:00', '2026-03-01 12:30:00'),
(11, 11, 7, 5, 'Giày chạy rất chất lượng. Chạy 15km không cảm thấy đau chân. Rất hài lòng', 1, '2026-03-02 09:20:00', '2026-03-02 09:20:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

DROP TABLE IF EXISTS `danh_muc`;
CREATE TABLE IF NOT EXISTS `danh_muc` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID danh mục',
  `ten_danh_muc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên danh mục',
  `mo_ta` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả danh mục',
  `hinh_anh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh danh mục',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `trang_thai` tinyint(4) DEFAULT 1 COMMENT 'Trạng thái (1=hoạt động, 0=ẩn)',
  `ngay_tao` datetime DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ten_danh_muc` (`ten_danh_muc`),
  KEY `idx_trang_thai` (`trang_thai`),
  KEY `idx_thu_tu` (`thu_tu`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng danh mục sản phẩm';

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id`, `ten_danh_muc`, `mo_ta`, `hinh_anh`, `thu_tu`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 'Quần áo', 'Quần áo thể thao nam nữ, đa dạng kiểu dáng, chất liệu cao cấp, thoáng khí, co giãn tốt', 'quan-ao.jpg', 1, 1, '2026-01-01 00:00:00', '2026-03-03 14:00:00'),
(2, 'Giày', 'Giày chạy bộ, giày tập gym, giày bóng đá, giày tennis chính hãng, đủ size', 'giay.jpg', 2, 1, '2026-01-01 00:00:00', '2026-03-03 14:00:00'),
(3, 'Thiết bị', 'Dụng cụ tập luyện tại nhà: tạ, thảm yoga, dây kháng lực, máy chạy bộ', 'thiet-bi.jpg', 3, 1, '2026-01-01 00:00:00', '2026-03-03 14:00:00'),
(4, 'Phụ kiện', 'Bình nước, khăn thể thao, túi xách, nón, vớ, đai hỗ trợ, băng dán', 'phu-kien.jpg', 4, 1, '2026-01-01 00:00:00', '2026-03-03 14:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

DROP TABLE IF EXISTS `don_hang`;
CREATE TABLE IF NOT EXISTS `don_hang` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID đơn hàng',
  `nguoi_dung_id` int(11) NOT NULL COMMENT 'ID người dùng',
  `ma_don_hang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã đơn hàng',
  `tong_tien` decimal(15,2) NOT NULL COMMENT 'Tổng tiền hàng',
  `tien_giam` decimal(15,2) DEFAULT 0.00 COMMENT 'Tiền giảm giá',
  `thanh_tien` decimal(15,2) NOT NULL COMMENT 'Thành tiền',
  `ma_phieu_giam` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã phiếu giảm',
  `phuong_thuc_thanh_toan` enum('tien_mat','credit_card','bank_transfer','e_wallet') COLLATE utf8mb4_unicode_ci DEFAULT 'tien_mat' COMMENT 'Phương thức thanh toán',
  `trang_thai` enum('cho_xu_ly','da_thanh_toan','dang_giao','hoan_thanh','da_huy','da_giao') COLLATE utf8mb4_unicode_ci DEFAULT 'cho_xu_ly' COMMENT 'Trạng thái đơn hàng',
  `ghi_chu` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ghi chú đơn hàng',
  `ngay_dat` datetime DEFAULT current_timestamp() COMMENT 'Ngày đặt',
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ngay_giao_hang` date DEFAULT NULL COMMENT 'Ngày giao hàng',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_don_hang` (`ma_don_hang`),
  KEY `idx_nguoi_dung_id` (`nguoi_dung_id`),
  KEY `idx_ma_don_hang` (`ma_don_hang`),
  KEY `idx_trang_thai` (`trang_thai`),
  KEY `idx_ngay_dat` (`ngay_dat`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng đơn hàng';

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`id`, `nguoi_dung_id`, `ma_don_hang`, `tong_tien`, `tien_giam`, `thanh_tien`, `ma_phieu_giam`, `phuong_thuc_thanh_toan`, `trang_thai`, `ghi_chu`, `ngay_dat`, `ngay_cap_nhat`, `ngay_giao_hang`) VALUES
(1, 2, 'DH001', '1297000.00', '0.00', '1322000.00', NULL, 'tien_mat', 'da_giao', 'Giao lúc tối nhất', '2026-02-25 10:30:00', '2026-03-03 14:00:00', '2026-02-28'),
(2, 3, 'DH002', '408000.00', '50000.00', '383000.00', 'SAVE10', 'credit_card', 'da_giao', 'Giao vào chiều', '2026-02-26 14:15:00', '2026-03-03 14:00:00', '2026-03-01'),
(3, 4, 'DH003', '398000.00', '0.00', '423000.00', NULL, 'bank_transfer', 'dang_giao', 'Giao vào buổi sáng', '2026-02-27 09:45:00', '2026-03-03 14:00:00', NULL),
(4, 5, 'DH004', '399000.00', '0.00', '424000.00', NULL, 'tien_mat', 'cho_xu_ly', 'Gọi trước khi giao', '2026-03-01 11:00:00', '2026-03-03 14:00:00', NULL),
(5, 2, 'DH005', '299000.00', '50000.00', '274000.00', 'WELCOME', 'e_wallet', 'da_giao', '', '2026-03-02 08:20:00', '2026-03-03 14:00:00', '2026-03-03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gia_tri_thong_so`
--

DROP TABLE IF EXISTS `gia_tri_thong_so`;
CREATE TABLE IF NOT EXISTS `gia_tri_thong_so` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID giá trị thông số',
  `san_pham_id` int(11) NOT NULL COMMENT 'ID sản phẩm',
  `thong_so_id` int(11) NOT NULL COMMENT 'ID thông số',
  `gia_tri` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Giá trị thông số',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_gia_tri` (`san_pham_id`,`thong_so_id`),
  KEY `thong_so_id` (`thong_so_id`),
  KEY `idx_san_pham_id` (`san_pham_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng giá trị thông số sản phẩm';

--
-- Đang đổ dữ liệu cho bảng `gia_tri_thong_so`
--

INSERT INTO `gia_tri_thong_so` (`id`, `san_pham_id`, `thong_so_id`, `gia_tri`) VALUES
(1, 1, 1, '100% Cotton'),
(2, 1, 2, 'XS, S, M, L, XL, XXL'),
(3, 1, 3, '150g'),
(4, 1, 4, 'Việt Nam'),
(5, 1, 5, 'Bình thường'),
(6, 2, 1, '95% Cotton, 5% Spandex'),
(7, 2, 2, 'XS, S, M, L, XL'),
(8, 2, 3, '140g'),
(9, 2, 4, 'Việt Nam'),
(10, 2, 5, 'Co giãn'),
(11, 3, 1, '85% Polyester, 15% Spandex'),
(12, 3, 2, 'S, M, L, XL, XXL'),
(13, 3, 3, '180g'),
(14, 3, 4, 'Việt Nam'),
(15, 3, 5, 'Co giãn mạnh'),
(16, 4, 1, '85% Nylon, 15% Spandex'),
(17, 4, 2, 'XS, S, M, L, XL'),
(18, 4, 3, '200g'),
(19, 4, 4, 'Việt Nam'),
(20, 4, 5, 'Co giãn 4 chiều'),
(21, 11, 1, 'Mesh, Rubber, Foam'),
(22, 11, 2, '35 - 43'),
(23, 11, 3, '280g/pair'),
(24, 11, 4, 'Trung Quốc'),
(25, 11, 5, 'Không co giãn');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang`
--

DROP TABLE IF EXISTS `gio_hang`;
CREATE TABLE IF NOT EXISTS `gio_hang` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID giỏ hàng',
  `nguoi_dung_id` int(11) DEFAULT NULL COMMENT 'ID người dùng (NULL = khách vãng lai)',
  `id_phien_lam_viec` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Session ID (cho khách vãng lai)',
  `ngay_tao` datetime DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_gio_hang` (`nguoi_dung_id`,`id_phien_lam_viec`),
  KEY `idx_nguoi_dung_id` (`nguoi_dung_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng giỏ hàng';

--
-- Đang đổ dữ liệu cho bảng `gio_hang`
--

INSERT INTO `gio_hang` (`id`, `nguoi_dung_id`, `id_phien_lam_viec`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 2, 'session_user_2', '2026-03-03 08:00:00', '2026-03-03 14:20:00'),
(2, 3, 'session_user_3', '2026-03-03 09:30:00', '2026-03-03 13:45:00'),
(3, 4, 'session_user_4', '2026-03-03 10:15:00', '2026-03-03 15:00:00'),
(4, 5, 'session_user_5', '2026-03-03 11:00:00', '2026-03-03 14:30:00'),
(5, NULL, 'session_guest_001', '2026-03-03 12:00:00', '2026-03-03 15:30:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hinh_anh_san_pham`
--

DROP TABLE IF EXISTS `hinh_anh_san_pham`;
CREATE TABLE IF NOT EXISTS `hinh_anh_san_pham` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID hình ảnh',
  `san_pham_id` int(11) NOT NULL COMMENT 'ID sản phẩm',
  `duong_dan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Đường dẫn/URL ảnh',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `la_chinh` tinyint(4) DEFAULT 0 COMMENT 'Là ảnh chính?',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_san_pham_id` (`san_pham_id`),
  KEY `idx_la_chinh` (`la_chinh`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng hình ảnh sản phẩm';

--
-- Đang đổ dữ liệu cho bảng `hinh_anh_san_pham`
--

INSERT INTO `hinh_anh_san_pham` (`id`, `san_pham_id`, `duong_dan`, `thu_tu`, `la_chinh`, `ngay_tao`) VALUES
(1, 1, 'ao-thun-nam-1.jpg', 1, 1, '2026-01-10 08:00:00'),
(2, 1, 'ao-thun-nam-2.jpg', 2, 0, '2026-01-10 08:00:00'),
(3, 1, 'ao-thun-nam-3.jpg', 3, 0, '2026-01-10 08:00:00'),
(4, 1, 'ao-thun-nam-4.jpg', 4, 0, '2026-01-10 08:00:00'),
(5, 2, 'ao-thun-nu-1.jpg', 1, 1, '2026-01-10 09:30:00'),
(6, 2, 'ao-thun-nu-2.jpg', 2, 0, '2026-01-10 09:30:00'),
(7, 2, 'ao-thun-nu-3.jpg', 3, 0, '2026-01-10 09:30:00'),
(8, 3, 'short-nam-1.jpg', 1, 1, '2026-01-10 11:00:00'),
(9, 3, 'short-nam-2.jpg', 2, 0, '2026-01-10 11:00:00'),
(10, 4, 'legging-nu-1.jpg', 1, 1, '2026-01-10 13:15:00'),
(11, 4, 'legging-nu-2.jpg', 2, 0, '2026-01-10 13:15:00'),
(12, 11, 'giay-chay-nam-1.jpg', 1, 1, '2026-01-11 08:00:00'),
(13, 11, 'giay-chay-nam-2.jpg', 2, 0, '2026-01-11 08:00:00'),
(14, 11, 'giay-chay-nam-3.jpg', 3, 0, '2026-01-11 08:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kich_thuoc`
--

DROP TABLE IF EXISTS `kich_thuoc`;
CREATE TABLE IF NOT EXISTS `kich_thuoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID kích thước',
  `ten` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên kích thước (XS, S, M, L, XL, XXL, ...)',
  `mo_ta` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả kích thước',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ten` (`ten`),
  KEY `idx_ten` (`ten`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng kích thước sản phẩm';

--
-- Đang đổ dữ liệu cho bảng `kich_thuoc`
--

INSERT INTO `kich_thuoc` (`id`, `ten`, `mo_ta`) VALUES
(1, 'XS', 'Extra Small - Cho người có kích thước nhỏ'),
(2, 'S', 'Small - Kích thước nhỏ'),
(3, 'M', 'Medium - Kích thước vừa (phổ biến nhất)'),
(4, 'L', 'Large - Kích thước lớn'),
(5, 'XL', 'Extra Large - Kích thước rất lớn'),
(6, 'XXL', 'Extra Extra Large - Kích thước siêu lớn'),
(7, '35', 'Size 35 (Giày)'),
(8, '36', 'Size 36 (Giày)'),
(9, '37', 'Size 37 (Giày)'),
(10, '38', 'Size 38 (Giày)'),
(11, '39', 'Size 39 (Giày)'),
(12, '40', 'Size 40 (Giày)'),
(13, '41', 'Size 41 (Giày)'),
(14, '42', 'Size 42 (Giày)'),
(15, '43', 'Size 43 (Giày)');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `mau_sac`
--

DROP TABLE IF EXISTS `mau_sac`;
CREATE TABLE IF NOT EXISTS `mau_sac` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID màu',
  `ten` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên màu (Đen, Trắng, Xanh, Đỏ, Vàng, ...)',
  `ma_hex` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã hex màu (#000000)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ten` (`ten`),
  KEY `idx_ten` (`ten`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng màu sắc';

--
-- Đang đổ dữ liệu cho bảng `mau_sac`
--

INSERT INTO `mau_sac` (`id`, `ten`, `ma_hex`) VALUES
(1, 'Đen', '#000000'),
(2, 'Trắng', '#FFFFFF'),
(3, 'Xanh đậm', '#0066CC'),
(4, 'Xanh nhạt', '#00CCFF'),
(5, 'Đỏ', '#FF0000'),
(6, 'Cam', '#FF6600'),
(7, 'Vàng', '#FFCC00'),
(8, 'Xanh lá', '#00CC00'),
(9, 'Tím', '#9900CC'),
(10, 'Hồng', '#FF66CC'),
(11, 'Nâu', '#996633'),
(12, 'Xám', '#999999');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ma_giam_gia`
--

DROP TABLE IF EXISTS `ma_giam_gia`;
CREATE TABLE IF NOT EXISTS `ma_giam_gia` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID mã giảm',
  `ma_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã giảm giá',
  `mo_ta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả',
  `phan_tram_giam` int(11) DEFAULT NULL COMMENT 'Phần trăm giảm',
  `so_tien_giam` int(11) DEFAULT NULL COMMENT 'Số tiền giảm (cố định)',
  `don_hang_toi_thieu` decimal(15,2) DEFAULT 0.00 COMMENT 'Đơn hàng tối thiểu',
  `giam_toi_da` decimal(15,2) DEFAULT NULL COMMENT 'Giảm tối đa',
  `ngay_bat_dau` datetime NOT NULL COMMENT 'Ngày bắt đầu',
  `ngay_ket_thuc` datetime NOT NULL COMMENT 'Ngày kết thúc',
  `gioi_han_su_dung` int(11) DEFAULT NULL COMMENT 'Giới hạn sử dụng',
  `da_su_dung` int(11) DEFAULT 0 COMMENT 'Đã sử dụng',
  `trang_thai` tinyint(4) DEFAULT 1 COMMENT 'Trạng thái (1=hoạt động)',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_code` (`ma_code`),
  KEY `idx_ma_code` (`ma_code`),
  KEY `idx_trang_thai` (`trang_thai`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng mã giảm giá/voucher';

--
-- Đang đổ dữ liệu cho bảng `ma_giam_gia`
--

INSERT INTO `ma_giam_gia` (`id`, `ma_code`, `mo_ta`, `phan_tram_giam`, `so_tien_giam`, `don_hang_toi_thieu`, `giam_toi_da`, `ngay_bat_dau`, `ngay_ket_thuc`, `gioi_han_su_dung`, `da_su_dung`, `trang_thai`, `ngay_tao`) VALUES
(1, 'SAVE10', 'Giảm 10% tất cả sản phẩm', 10, NULL, '100000.00', '100000.00', '2026-02-01 00:00:00', '2026-04-30 23:59:59', 1000, 42, 1, '2026-03-04 14:48:23'),
(2, 'SAVE20', 'Giảm 20% cho đơn từ 500k', 20, NULL, '500000.00', '150000.00', '2026-02-01 00:00:00', '2026-04-30 23:59:59', 500, 28, 1, '2026-03-04 14:48:23'),
(3, 'SHIP25', 'Giảm 25k phí vận chuyển', NULL, 25000, '250000.00', '25000.00', '2026-02-01 00:00:00', '2026-04-30 23:59:59', 800, 15, 1, '2026-03-04 14:48:23'),
(4, 'WELCOME', 'Giảm 50k cho khách mới', NULL, 50000, '300000.00', '50000.00', '2026-01-01 00:00:00', '2026-12-31 23:59:59', 200, 38, 1, '2026-03-04 14:48:23'),
(5, 'SPRING2026', 'Giảm 15% mùa xuân', 15, NULL, '200000.00', '120000.00', '2026-03-01 00:00:00', '2026-05-31 23:59:59', 500, 5, 1, '2026-03-04 14:48:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

DROP TABLE IF EXISTS `nguoi_dung`;
CREATE TABLE IF NOT EXISTS `nguoi_dung` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID người dùng',
  `ten` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Họ tên',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email duy nhất',
  `mat_khau` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mật khẩu hash',
  `so_dien_thoai` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số điện thoại',
  `dia_chi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa chỉ',
  `anh_dai_dien` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL ảnh đại diện',
  `vai_tro` enum('admin','khach_hang') COLLATE utf8mb4_unicode_ci DEFAULT 'khach_hang' COMMENT 'Vai trò: admin hoặc khách hàng',
  `trang_thai` enum('hoat_dong','binh_luan','bi_khoa') COLLATE utf8mb4_unicode_ci DEFAULT 'hoat_dong' COMMENT 'Trạng thái tài khoản',
  `ngay_tao` datetime DEFAULT current_timestamp() COMMENT 'Ngày tạo tài khoản',
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật',
  `lan_dang_nhap_cuoi` datetime DEFAULT NULL COMMENT 'Lần đăng nhập cuối cùng',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_vai_tro` (`vai_tro`),
  KEY `idx_trang_thai` (`trang_thai`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng người dùng (khách hàng và admin)';

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `ten`, `email`, `mat_khau`, `so_dien_thoai`, `dia_chi`, `anh_dai_dien`, `vai_tro`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `lan_dang_nhap_cuoi`) VALUES
(1, 'Admin AthleteHub', 'admin@email.com', 'admin123', '0912345678', '123 Đường Thể Thao, TP.HCM', 'admin-avatar.jpg', 'admin', 'hoat_dong', '2026-01-15 08:30:00', '2026-03-04 14:30:54', '2026-03-03 14:20:00'),
(2, 'Nguyễn Văn A', 'khach1@example.com', '$2y$10$5v5l7bY9nZ9d8k7j6h5g4f3e2d1c0b9a8f7e6d5c4b3a2z1y0x9w8v', '0987654321', '456 Đường Lê Lợi, Q1, TP.HCM', 'avatar-1.jpg', 'khach_hang', 'hoat_dong', '2026-01-20 10:15:00', '2026-03-04 14:30:54', '2026-03-03 09:45:00'),
(3, 'Trần Thị B', 'khach2@example.com', '$2y$10$5v5l7bY9nZ9d8k7j6h5g4f3e2d1c0b9a8f7e6d5c4b3a2z1y0x9w8v', '0912123456', '789 Đường Nguyễn Huệ, Q1, TP.HCM', 'avatar-2.jpg', 'khach_hang', 'hoat_dong', '2026-01-25 14:30:00', '2026-03-04 14:30:54', '2026-03-02 16:20:00'),
(4, 'Lê Văn C', 'khach3@example.com', '$2y$10$5v5l7bY9nZ9d8k7j6h5g4f3e2d1c0b9a8f7e6d5c4b3a2z1y0x9w8v', '0923456789', '321 Đường Trần Hưng Đạo, Q5, TP.HCM', 'avatar-3.jpg', 'khach_hang', 'hoat_dong', '2026-02-01 11:00:00', '2026-03-04 14:30:54', '2026-03-01 13:15:00'),
(5, 'Phạm Thị D', 'khach4@example.com', '$2y$10$5v5l7bY9nZ9d8k7j6h5g4f3e2d1c0b9a8f7e6d5c4b3a2z1y0x9w8v', '0934567890', '654 Đường Võ Văn Kiệt, Q4, TP.HCM', 'avatar-4.jpg', 'khach_hang', 'hoat_dong', '2026-02-05 09:30:00', '2026-03-04 14:30:54', '2026-03-03 11:45:00'),
(6, 'Hoàng Văn E', 'khach5@example.com', '$2y$10$5v5l7bY9nZ9d8k7j6h5g4f3e2d1c0b9a8f7e6d5c4b3a2z1y0x9w8v', '0945678901', '987 Đường Lý Thường Kiệt, Q10, TP.HCM', 'avatar-5.jpg', 'khach_hang', 'hoat_dong', '2026-02-10 15:20:00', '2026-03-04 14:30:54', '2026-02-28 10:30:00'),
(7, 'Võ Thị F', 'khach6@example.com', '$2y$10$5v5l7bY9nZ9d8k7j6h5g4f3e2d1c0b9a8f7e6d5c4b3a2z1y0x9w8v', '0956789012', '246 Đường Cách Mạng Tháng 8, Q3, TP.HCM', 'avatar-6.jpg', 'khach_hang', 'hoat_dong', '2026-02-15 12:45:00', '2026-03-04 14:30:54', '2026-03-03 08:00:00'),
(8, 'Bùi Văn G', 'khach7@example.com', '$2y$10$5v5l7bY9nZ9d8k7j6h5g4f3e2d1c0b9a8f7e6d5c4b3a2z1y0x9w8v', '0967890123', '369 Đường Đinh Bộ Lĩnh, Q1, TP.HCM', 'avatar-7.jpg', 'khach_hang', 'hoat_dong', '2026-02-20 16:10:00', '2026-03-04 14:30:54', '2026-03-02 14:25:00'),
(9, 'Đặng Thị H', 'khach8@example.com', '$2y$10$5v5l7bY9nZ9d8k7j6h5g4f3e2d1c0b9a8f7e6d5c4b3a2z1y0x9w8v', '0978901234', '753 Đường Hai Bà Trưng, Q1, TP.HCM', 'avatar-8.jpg', 'khach_hang', 'hoat_dong', '2026-02-25 13:35:00', '2026-03-04 14:30:54', '2026-03-01 17:40:00'),
(10, 'Trương Văn I', 'khach9@example.com', '$2y$10$5v5l7bY9nZ9d8k7j6h5g4f3e2d1c0b9a8f7e6d5c4b3a2z1y0x9w8v', '0989012345', '159 Đường Pasteur, Q1, TP.HCM', 'avatar-9.jpg', 'khach_hang', 'hoat_dong', '2026-02-28 10:20:00', '2026-03-04 14:30:54', '2026-03-03 12:00:00'),
(11, 'Phan Văn J', 'khach10@example.com', '$2y$10$5v5l7bY9nZ9d8k7j6h5g4f3e2d1c0b9a8f7e6d5c4b3a2z1y0x9w8v', '0912345679', '852 Đường Ngô Tất Tố, Q1, TP.HCM', 'avatar-10.jpg', 'khach_hang', 'hoat_dong', '2026-03-01 07:50:00', '2026-03-04 14:30:54', '2026-03-03 15:30:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham`
--

DROP TABLE IF EXISTS `san_pham`;
CREATE TABLE IF NOT EXISTS `san_pham` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID sản phẩm',
  `danh_muc_id` int(11) DEFAULT NULL COMMENT 'ID danh mục',
  `ten` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên sản phẩm',
  `mo_ta` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả sản phẩm',
  `mo_ta_chi_tiet` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả chi tiết',
  `gia` decimal(15,2) NOT NULL COMMENT 'Giá bán',
  `gia_goc` decimal(15,2) DEFAULT NULL COMMENT 'Giá gốc',
  `phan_tram_giam` int(11) DEFAULT 0 COMMENT 'Phần trăm giảm giá',
  `ngay_bat_dau_giam` datetime DEFAULT NULL COMMENT 'Ngày bắt đầu giảm',
  `ngay_ket_thuc_giam` datetime DEFAULT NULL COMMENT 'Ngày kết thúc giảm',
  `trung_binh_sao` float DEFAULT 0 COMMENT 'Điểm đánh giá trung bình',
  `so_luong_danh_gia` int(11) DEFAULT 0 COMMENT 'Số lượng đánh giá',
  `hinh_anh_chinh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hình ảnh chính',
  `trang_thai` tinyint(4) DEFAULT 1 COMMENT 'Trạng thái (1=hoạt động, 0=ẩn)',
  `la_noi_bat` tinyint(4) DEFAULT 0 COMMENT 'Là sản phẩm nổi bật?',
  `ngay_tao` datetime DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngay_cap_nhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_danh_muc_id` (`danh_muc_id`),
  KEY `idx_trang_thai` (`trang_thai`),
  KEY `idx_gia` (`gia`),
  KEY `idx_trung_binh_sao` (`trung_binh_sao`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng sản phẩm';

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id`, `danh_muc_id`, `ten`, `mo_ta`, `mo_ta_chi_tiet`, `gia`, `gia_goc`, `phan_tram_giam`, `ngay_bat_dau_giam`, `ngay_ket_thuc_giam`, `trung_binh_sao`, `so_luong_danh_gia`, `hinh_anh_chinh`, `trang_thai`, `la_noi_bat`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, 'Áo thun nam cao cấp', 'Áo thun nam vải 100% cotton', 'Áo thun nam với chất liệu vải cotton cao cấp, thoáng mát, thấm hút mồ hôi tốt. Thích hợp để mặc hàng ngày hoặc tập luyện. Có nhiều màu lựa chọn', '149000.00', '219000.00', 32, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 4.8, 42, 'ao-thun-nam.jpg', 1, 1, '2026-01-10 08:00:00', '2026-03-03 14:30:00'),
(2, 1, 'Áo thun nữ thoáng mát', 'Áo thun nữ vải co giãn', 'Áo thun nữ được thiết kế với vải co giãn, mềm mại, thoáng khí. Giúp bạn nữ tự tin khi tập luyện hoặc đi dạo. Chất liệu giặt dễ, bền lâu', '159000.00', '229000.00', 31, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 38, 'ao-thun-nu.jpg', 1, 1, '2026-01-10 09:30:00', '2026-03-03 14:30:00'),
(3, 1, 'Quần short nam chất lượng', 'Quần short nam vải thể thao co giãn', 'Quần short nam được làm từ vải thể thao cao cấp, co giãn 4 chiều, rất thoải mái. Có túi khóa an toàn để bảo vệ điều tệ cá nhân', '199000.00', '299000.00', 33, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 35, 'short-nam.jpg', 1, 1, '2026-01-10 11:00:00', '2026-03-03 14:30:00'),
(4, 1, 'Quần legging nữ cao cấp', 'Quần legging nữ vải yoga cao cấp', 'Quần legging nữ với vải yoga cao cấp, giữ hình dáng tốt. Có túi bên hông để chứa điện thoại, chìa khóa. Phù hợp cho yoga, tập gym', '249000.00', '379000.00', 34, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 4.9, 40, 'legging-nu.jpg', 1, 1, '2026-01-10 13:15:00', '2026-03-03 14:30:00'),
(5, 1, 'Áo khoác chống nước nam', 'Áo khoác chống nước, chất liệu polyester', 'Áo khoác chống nước được thiết kế chuyên cho các hoạt động ngoài trời. Nhẹ, gấp gọn được, không chiếm chỗ khi chuẩn bị', '399000.00', '579000.00', 31, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 31, 'ao-khoac-nam.jpg', 1, 0, '2026-01-10 14:45:00', '2026-03-03 14:30:00'),
(6, 1, 'Áo khoác nữ ấm áp', 'Áo khoác nữ giữ ấm, chất liệu soft', 'Áo khoác nữ với chất liệu mềm mại, ấm áp. Thích hợp cho những ngày lạnh hoặc đi tập luyện buổi sáng. Có zip kéo tiện lợi', '379000.00', '549000.00', 31, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 28, 'ao-khoac-nu.jpg', 1, 0, '2026-01-10 16:00:00', '2026-03-03 14:30:00'),
(7, 1, 'Tanktop nam thoáng mát', 'Tanktop nam vải cotton, co giãn', 'Tanktop nam được làm từ vải cotton 100%, thoáng mát, phù hợp để tập luyện vào những ngày nắng nóng. Có nhiều màu để lựa chọn', '129000.00', '189000.00', 32, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 25, 'tanktop-nam.jpg', 1, 0, '2026-01-10 17:30:00', '2026-03-03 14:30:00'),
(8, 1, 'Tanktop nữ thể thao', 'Tanktop nữ vải co giãn, áo lót tích hợp', 'Tanktop nữ được thiết kế với vải co giãn 4 chiều, có áo lót hỗ trợ. Thoáng mát, thoải mái khi tập luyện', '129000.00', '189000.00', 32, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 4.8, 22, 'tanktop-nu.jpg', 1, 0, '2026-01-10 19:00:00', '2026-03-03 14:30:00'),
(9, 1, 'Quần dài nam chuyên động', 'Quần dài nam vải thể thao, co giãn', 'Quần dài nam với vải thể thao cao cấp, co giãn tốt. Phù hợp cho chạy bộ, tập gym hoặc hoạt động ngoài trời', '229000.00', '349000.00', 34, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 32, 'quan-dai-nam.jpg', 1, 0, '2026-01-10 20:15:00', '2026-03-03 14:30:00'),
(10, 1, 'Quần dài nữ chuyên động', 'Quần dài nữ vải yoga cao cấp', 'Quần dài nữ với vải yoga cao cấp, giữ hình dáng. Có túi bên hông, dây buộc tiện lợi. Phù hợp cho yoga, tập gym', '229000.00', '349000.00', 34, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 30, 'quan-dai-nu.jpg', 1, 0, '2026-01-10 21:45:00', '2026-03-03 14:30:00'),
(11, 2, 'Giày chạy bộ nam cao cấp', 'Giày chạy bộ nam có đệm shock', 'Giày chạy bộ nam được thiết kế với công nghệ đệm shock tối ưu, giảm chấn thương cho chân. Nhẹ, thoáng khí, phù hợp chạy bộ dài', '999000.00', '1299000.00', 23, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 45, 'giay-chay-nam.jpg', 1, 1, '2026-01-11 08:00:00', '2026-03-03 14:30:00'),
(12, 2, 'Giày chạy bộ nữ nhẹ', 'Giày chạy bộ nữ nhẹ, thoáng khí', 'Giày chạy bộ nữ được thiết kế nhẹ nhàng, thoáng khí. Có đệm shock tốt, phù hợp cho chạy bộ hay đi bộ dạo', '949000.00', '1249000.00', 24, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 42, 'giay-chay-nu.jpg', 1, 1, '2026-01-11 09:30:00', '2026-03-03 14:30:00'),
(13, 2, 'Giày tập gym nam chắc chắn', 'Giày tập gym nam ổn định, chắc chắn', 'Giày tập gym nam được thiết kế chắc chắn, ổn định. Phù hợp để tập luyện trên các máy gym, nhất là nâng tạ', '749000.00', '999000.00', 25, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 4.9, 38, 'giay-gym-nam.jpg', 1, 0, '2026-01-11 11:00:00', '2026-03-03 14:30:00'),
(14, 2, 'Giày tập gym nữ tinh tế', 'Giày tập gym nữ thoáng mát, êm', 'Giày tập gym nữ được thiết kế tinh tế, thoáng mát. Có hỗ trợ cốt chân, phù hợp cho tập gym', '749000.00', '999000.00', 25, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 4.8, 35, 'giay-gym-nu.jpg', 1, 0, '2026-01-11 12:30:00', '2026-03-03 14:30:00'),
(15, 2, 'Giày bóng đá nam chuyên nghiệp', 'Giày bóng đá nam cắn cỏ, kiểm soát tốt', 'Giày bóng đá nam với đế cắn cỏ chuyên nghiệp, giúp bám chân tốt trên sân cỏ. Kiểm soát bóng tuyệt vời', '1299000.00', '1599000.00', 19, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 4.7, 28, 'giay-bong-da.jpg', 1, 0, '2026-01-11 14:00:00', '2026-03-03 14:30:00'),
(16, 2, 'Giày đi hằng ngày nam thoải mái', 'Giày đi hằng ngày nam thoải mái, bám chân', 'Giày đi hằng ngày nam được thiết kế thoải mái, bám chân tốt. Phù hợp cho đi chơi, đi làm', '549000.00', '749000.00', 27, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 4.9, 33, 'giay-hangngay-nam.jpg', 1, 0, '2026-01-11 15:30:00', '2026-03-03 14:30:00'),
(17, 2, 'Giày leo núi có kháng nước', 'Giày leo núi chuyên dụng, kháng nước', 'Giày leo núi được thiết kế chuyên dụng, kháng nước tốt. Bám chân tốt trên các bề mặt khác nhau', '1499000.00', '1899000.00', 21, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 20, 'giay-leo-nui.jpg', 1, 0, '2026-01-11 17:00:00', '2026-03-03 14:30:00'),
(18, 2, 'Giày cầu lông nhẹ linh hoạt', 'Giày cầu lông nhẹ, linh hoạt, êm', 'Giày cầu lông được thiết kế nhẹ nhàng, linh hoạt. Hỗ trợ tốt cho các động tác nhanh trên sân cầu lông', '899000.00', '1199000.00', 25, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 26, 'giay-caulo.jpg', 1, 0, '2026-01-11 18:30:00', '2026-03-03 14:30:00'),
(19, 2, 'Giày tennis chắc chắn ổn định', 'Giày tennis bền, ổn định, giá hợp lý', 'Giày tennis được thiết kế chắc chắn, ổn định. Phù hợp cho chơi tennis ở mọi cấp độ', '1099000.00', '1399000.00', 21, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 4.7, 24, 'giay-tennis.jpg', 1, 0, '2026-01-11 20:00:00', '2026-03-03 14:30:00'),
(20, 2, 'Giày đi bộ nữ êm thoải mái', 'Giày đi bộ nữ êm, nhẹ, thích dã ngoại', 'Giày đi bộ nữ được thiết kế êm áp, nhẹ. Phù hợp cho đi bộ dạo hay hoạt động ngoài trời', '449000.00', '649000.00', 31, '2026-02-15 00:00:00', '2026-04-15 23:59:59', 5, 40, 'giay-dibo-nu.jpg', 1, 0, '2026-01-11 21:30:00', '2026-03-03 14:30:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `su_dung_ma_giam_gia`
--

DROP TABLE IF EXISTS `su_dung_ma_giam_gia`;
CREATE TABLE IF NOT EXISTS `su_dung_ma_giam_gia` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nguoi_dung_id` int(11) NOT NULL COMMENT 'ID người dùng',
  `ma_giam_gia_id` int(11) NOT NULL COMMENT 'ID mã giảm',
  `don_hang_id` int(11) DEFAULT NULL COMMENT 'ID đơn hàng',
  `ngay_su_dung` datetime DEFAULT current_timestamp() COMMENT 'Ngày sử dụng',
  PRIMARY KEY (`id`),
  KEY `ma_giam_gia_id` (`ma_giam_gia_id`),
  KEY `don_hang_id` (`don_hang_id`),
  KEY `idx_nguoi_dung_id` (`nguoi_dung_id`),
  KEY `idx_ngay_su_dung` (`ngay_su_dung`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lịch sử sử dụng mã giảm giá';

--
-- Đang đổ dữ liệu cho bảng `su_dung_ma_giam_gia`
--

INSERT INTO `su_dung_ma_giam_gia` (`id`, `nguoi_dung_id`, `ma_giam_gia_id`, `don_hang_id`, `ngay_su_dung`) VALUES
(1, 3, 1, 2, '2026-02-26 14:15:00'),
(2, 5, 4, 4, '2026-03-01 11:00:00'),
(3, 2, 4, 5, '2026-03-02 08:20:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_so`
--

DROP TABLE IF EXISTS `thong_so`;
CREATE TABLE IF NOT EXISTS `thong_so` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID thông số',
  `ten_thong_so` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên thông số (Chất liệu, Kích cỡ, Trọng lượng, ...)',
  `mo_ta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả thông số',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ten_thong_so` (`ten_thong_so`),
  KEY `idx_ten_thong_so` (`ten_thong_so`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng thông số sản phẩm';

--
-- Đang đổ dữ liệu cho bảng `thong_so`
--

INSERT INTO `thong_so` (`id`, `ten_thong_so`, `mo_ta`) VALUES
(1, 'Chất liệu', 'Thành phần chính của sản phẩm'),
(2, 'Kích cỡ', 'Các size có sẵn'),
(3, 'Trọng lượng', 'Cân nặng của sản phẩm'),
(4, 'Xuất xứ', 'Nước sản xuất'),
(5, 'Độ co giãn', 'Mức độ co giãn vải');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `yeu_thich`
--

DROP TABLE IF EXISTS `yeu_thich`;
CREATE TABLE IF NOT EXISTS `yeu_thich` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID yêu thích',
  `nguoi_dung_id` int(11) NOT NULL COMMENT 'ID người dùng',
  `san_pham_id` int(11) NOT NULL COMMENT 'ID sản phẩm',
  `ngay_them` datetime DEFAULT current_timestamp() COMMENT 'Ngày thêm',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_yeu_thich` (`nguoi_dung_id`,`san_pham_id`),
  KEY `idx_nguoi_dung_id` (`nguoi_dung_id`),
  KEY `idx_san_pham_id` (`san_pham_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng danh sách yêu thích';

--
-- Đang đổ dữ liệu cho bảng `yeu_thich`
--

INSERT INTO `yeu_thich` (`id`, `nguoi_dung_id`, `san_pham_id`, `ngay_them`) VALUES
(1, 2, 1, '2026-02-10 14:30:00'),
(2, 2, 3, '2026-02-11 10:15:00'),
(3, 2, 11, '2026-02-12 16:45:00'),
(4, 3, 2, '2026-02-13 09:00:00'),
(5, 3, 4, '2026-02-14 13:20:00'),
(6, 4, 5, '2026-02-15 11:10:00'),
(7, 4, 12, '2026-02-16 15:50:00'),
(8, 5, 6, '2026-02-17 10:30:00'),
(9, 6, 7, '2026-02-18 14:45:00'),
(10, 7, 8, '2026-02-19 09:15:00');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `san_pham`
--
ALTER TABLE `san_pham` ADD FULLTEXT KEY `ft_tim_kiem` (`ten`,`mo_ta`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bien_the_san_pham`
--
ALTER TABLE `bien_the_san_pham`
  ADD CONSTRAINT `bien_the_san_pham_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bien_the_san_pham_ibfk_2` FOREIGN KEY (`kich_thuoc_id`) REFERENCES `kich_thuoc` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bien_the_san_pham_ibfk_3` FOREIGN KEY (`mau_sac_id`) REFERENCES `mau_sac` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_3` FOREIGN KEY (`bien_the_san_pham_id`) REFERENCES `bien_the_san_pham` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_4` FOREIGN KEY (`kich_thuoc_id`) REFERENCES `kich_thuoc` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_5` FOREIGN KEY (`mau_sac_id`) REFERENCES `mau_sac` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `chi_tiet_gio_hang`
--
ALTER TABLE `chi_tiet_gio_hang`
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_1` FOREIGN KEY (`gio_hang_id`) REFERENCES `gio_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_3` FOREIGN KEY (`bien_the_san_pham_id`) REFERENCES `bien_the_san_pham` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_4` FOREIGN KEY (`kich_thuoc_id`) REFERENCES `kich_thuoc` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_5` FOREIGN KEY (`mau_sac_id`) REFERENCES `mau_sac` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`);

--
-- Các ràng buộc cho bảng `gia_tri_thong_so`
--
ALTER TABLE `gia_tri_thong_so`
  ADD CONSTRAINT `gia_tri_thong_so_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gia_tri_thong_so_ibfk_2` FOREIGN KEY (`thong_so_id`) REFERENCES `thong_so` (`id`) ON DELETE CASCADE;

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
-- Các ràng buộc cho bảng `su_dung_ma_giam_gia`
--
ALTER TABLE `su_dung_ma_giam_gia`
  ADD CONSTRAINT `su_dung_ma_giam_gia_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `su_dung_ma_giam_gia_ibfk_2` FOREIGN KEY (`ma_giam_gia_id`) REFERENCES `ma_giam_gia` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `su_dung_ma_giam_gia_ibfk_3` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE SET NULL;

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
