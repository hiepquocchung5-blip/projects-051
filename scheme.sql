--
-- Table structure for table `users`
--
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `google_id` varchar(255) DEFAULT NULL UNIQUE,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password_hash` varchar(255) DEFAULT NULL,
  `auth_provider` enum('google','native') NOT NULL DEFAULT 'google',
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
  `theme_color` varchar(50) NOT NULL DEFAULT 'premium-gold',
  `base_reward` int(11) NOT NULL DEFAULT 1000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping initial data for table `games` (All 8 Modules)
--
INSERT INTO `games` (`slug`, `title`, `description`, `icon`, `theme_color`, `base_reward`, `is_active`) VALUES
('tictactoe', 'Quantum Tic-Tac', 'Logic combat against system AI.', 'grid-3x3', 'premium-gold', 5000, 1),
('cybermole', 'Target Neutralization', 'High-speed kinetic clicking protocol.', 'target', 'gray-300', 2500, 1),
('urbanbird', 'Urban Flight', 'Navigate the firewall. Infinite evasion.', 'plane-takeoff', 'premium-silver', 1000, 1),
('neonguess', 'Encryption Breach', 'Decrypt the target node. Fewer attempts yield higher payouts.', 'terminal', 'premium-goldDark', 5000, 1),
('gridwars', 'Grid Wars Lite', 'Arena survival combat. Neutralize rogue drone swarms.', 'crosshair', 'red-500', 0, 1),
('cyberjump', 'Cyber Jump', 'Platform traversal simulation. Avoid data spikes.', 'activity', 'premium-gold', 3000, 1),
('dataworm', 'Data Worm', 'Consume packets to expand your physical node length.', 'git-commit', 'premium-silver', 2000, 1),
('nodematch', 'Node Match', 'Memory decryption. Find the matching cryptographic pairs.', 'cpu', 'premium-goldDark', 4000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `source` enum('game_win', 'ad_view', 'daily_login', 'referral', 'admin_bonus', 'unknown') NOT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
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
-- Dumping initial data for table `events`
--
INSERT INTO `events` (`title`, `coin_multiplier`, `start_time`, `end_time`, `is_active`) VALUES
('Global Network Initialization', 1.5, '2026-05-01 00:00:00', '2026-05-31 23:59:59', 1);

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
-- Dumping initial data for table `system_settings`
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

-- Add the temporary bind token column to the users table
ALTER TABLE `users` ADD COLUMN `telegram_bind_token` VARCHAR(50) DEFAULT NULL AFTER `telegram_chat_id`;