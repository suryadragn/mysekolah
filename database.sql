-- CMS Sekolahku - Database Setup
-- Copyright (c) 2026 suryadragn. All Rights Reserved.
-- =====================================================
-- Jalankan file ini di database MySQL Anda.
-- Buat database baru bernama 'mysekolah' terlebih dahulu.
-- =====================================================

CREATE TABLE IF NOT EXISTS `ms_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin: username=admin, password=admin123
INSERT INTO `ms_users` (`username`, `password`) VALUES
('admin', '$2y$10$BKxCxDED7Go/xssngm1oOeP5qT2lX/4t5zDk6s6tpS20LJM.DXfp6');

CREATE TABLE IF NOT EXISTS `ms_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ms_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ms_admission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `parent_name` varchar(255) DEFAULT NULL,
  `previous_school` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ms_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ms_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_key` varchar(100) NOT NULL,
  `s_value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `s_key` (`s_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default settings
INSERT INTO `ms_settings` (`s_key`, `s_value`) VALUES
('academic_year', '2025/2026'),
('stat_students', '1200'),
('stat_teachers', '85'),
('stat_achievements', '42'),
('stat_extracurricular', '15'),
('site_logo', ''),
('site_favicon', ''),
('hero_bg', ''),
('app_status', 'inactive'),
('app_license_key', ''),
('trial_started_at', '');

CREATE TABLE IF NOT EXISTS `ms_socials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon_text` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default social media (isi URL sesuai kebutuhan)
INSERT INTO `ms_socials` (`platform`, `url`, `icon_text`) VALUES
('Facebook', '#', 'FB'),
('Instagram', '#', 'IG'),
('YouTube', '#', 'YT');
