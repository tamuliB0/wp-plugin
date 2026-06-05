-- DDEV test fixture only. This assumes the default WordPress table prefix is wp_.
-- Plugin code must still use $wpdb->prefix . 'reading_list'.
CREATE TABLE IF NOT EXISTS `wp_reading_list` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'to-read',
  `notes` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wp_reading_list` (`title`, `author`, `status`, `notes`, `created_at`) VALUES
('Clean Code', 'Robert C. Martin', 'read', 'Solid fundamentals.'),
('The Pragmatic Programmer', 'David Thomas', 'reading', NULL),
('PHP Objects, Patterns and Practice', 'Matt Zandstra', 'to-read', NULL),
('Professional WordPress', 'Brad Williams', 'to-read', 'Good reference.'),
('WordPress Plugin Development', 'Yannick Lefebvre', 'to-read', NULL);
