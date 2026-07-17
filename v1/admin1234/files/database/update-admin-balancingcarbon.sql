CREATE DATABASE IF NOT EXISTS `carbon` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `carbon`;

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(50) DEFAULT 'Admin',
  `status` varchar(50) DEFAULT 'Active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `admin_users`
  ADD COLUMN IF NOT EXISTS `role` varchar(50) DEFAULT 'Admin',
  ADD COLUMN IF NOT EXISTS `status` varchar(50) DEFAULT 'Active';

INSERT INTO `admin_users` (`username`, `password`, `email`, `role`, `status`)
VALUES (
  'admin',
  '$2y$12$65Vpjmbgr9Z/V9QbYdI70e8k5diIRTSbxsCzrwYoi6d72AfPiPdE2',
  'admin@balancingcarbon.com',
  'Admin',
  'Active'
)
ON DUPLICATE KEY UPDATE
  `password` = VALUES(`password`),
  `email` = VALUES(`email`),
  `role` = VALUES(`role`),
  `status` = VALUES(`status`);

UPDATE `admin_users`
SET
  `username` = 'admin',
  `password` = '$2y$12$65Vpjmbgr9Z/V9QbYdI70e8k5diIRTSbxsCzrwYoi6d72AfPiPdE2',
  `role` = 'Admin',
  `status` = 'Active'
WHERE `email` = 'admin@balancingcarbon.com';
