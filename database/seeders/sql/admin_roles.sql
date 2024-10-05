-- Inserting into admin_roles table
INSERT INTO `admin_roles` (`id`, `name`, `module_access`, `status`, `created_at`, `updated_at`) VALUES
('1', 'Master Admin', NULL, '1', NULL, NULL),
('7', 'Manager', '[\"dashboard\",\"rate_tier_management\",\"camping_season_management\",\"sites_management\",\"reservation_management\",\"events_management\",\"season_management\",\"blog_management\",\"content_management\",\"promotion_management\",\"user_section\",\"system_settings\"]', '1', '2023-05-05 20:47:48', '2023-12-13 13:41:51'),
('8', 'SuperAdmin', '[\"dashboard\",\"rate_tier_management\",\"camping_season_management\",\"sites_management\",\"reservation_management\",\"events_management\",\"season_management\",\"blog_management\",\"content_management\",\"promotion_management\",\"user_section\",\"system_settings\"]', '1', '2023-12-02 12:34:47', '2023-12-13 13:41:34'),
('9', 'SiteEditor', '[\"dashboard\",\"sites_management\"]', '1', '2023-12-02 13:18:20', '2023-12-13 19:47:43');

