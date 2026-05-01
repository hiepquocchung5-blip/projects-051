-- phpMyAdmin SQL Dump
-- Project 051: Urbanix Gaming Portal
-- Generation Time: System Initial Setup

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `urbanix_db`
--
CREATE DATABASE IF NOT EXISTS `urbanix_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `urbanix_db`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `google_id` varchar(255) NOT NULL UNIQUE,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `urban_coins` bigint(20) NOT NULL DEFAULT 0,
  `mmk_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `telegram_chat_id` varchar(100) DEFAULT NULL,
  `role` enum('player','admin') NOT NULL DEFAULT 'player',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--
CREATE TABLE IF NOT EXISTS `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL UNIQUE,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'gamepad-2',
  `theme_color` varchar(50) NOT NULL DEFAULT 'neon-cyan',
  `base_reward` int(11) NOT NULL DEFAULT 1000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `games`
--
INSERT INTO `games` (`slug`, `title`, `description`, `icon`, `theme_color`, `base_reward`, `is_active`) VALUES
('tictactoe', 'Quantum Tic-Tac-Toe', 'Play against rogue AI. Earn coins per win.', 'grid-3x3', 'neon-cyan', 5000, 1),
('cybermole', 'Cyber-Mole', 'Timed clicking simulation. High risk, high reward.', 'target', 'neon-purple', 2500, 1),
('urbanbird', 'Urbanix Bird', 'Dodge the firewall pillars. Infinite runner.', 'bird', 'green-400', 1000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
-- Tracks every coin addition (ads, game wins) to prevent cheating
--
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `source` enum('game_win', 'ad_view', 'daily_login', 'referral', 'admin_bonus') NOT NULL,
  `reference_id` varchar(100) DEFAULT NULL COMMENT 'Game slug or Ad network ID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--
CREATE TABLE IF NOT EXISTS `withdrawals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `payment_method` enum('KPay', 'WaveMoney') NOT NULL DEFAULT 'KPay',
  `amount_mmk` decimal(10,2) NOT NULL,
  `status` enum('pending', 'approved', 'rejected', 'processing') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `events`
-- Controls Global Multipliers via CMS
--
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `coin_multiplier` decimal(3,1) NOT NULL DEFAULT 1.0,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `events`
--
INSERT INTO `events` (`title`, `coin_multiplier`, `start_time`, `end_time`, `is_active`) VALUES
('Weekend Neon Overdrive', 2.0, '2026-05-02 00:00:00', '2026-05-04 23:59:59', 0);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--
CREATE TABLE IF NOT EXISTS `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `system_settings`
--
INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('coin_to_mmk_rate', '10000000'),
('mmk_base_value', '1000'),
('ad_interval_seconds', '60'),
('minimum_withdrawal_mmk', '1000');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;