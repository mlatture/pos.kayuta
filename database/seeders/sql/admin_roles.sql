-- Inserting into admin_roles table
INSERT INTO `admin_roles` (`created_at`,  `module_access`, `name`, `status`, `updated_at`) VALUES
(NULL, NULL, 'Master Admin', '1', NULL),
('2023-05-05 20:47:48',  '[\"dashboard\",\"rate_tier_management\",\"camping_season_management\",\"sites_management\",\"reservation_management\",\"events_management\",\"season_management\",\"blog_management\",\"content_management\",\"promotion_management\",\"user_section\",\"system_settings\"]', 'Manager', '1', '2023-12-13 13:41:51'),
('2023-12-02 12:34:47', '[\"dashboard\",\"rate_tier_management\",\"camping_season_management\",\"sites_management\",\"reservation_management\",\"events_management\",\"season_management\",\"blog_management\",\"content_management\",\"promotion_management\",\"user_section\",\"system_settings\"]', 'SuperAdmin', '1', '2023-12-13 13:41:34'),
('2023-12-02 13:18:20',  '[\"dashboard\",\"sites_management\"]', 'SiteEditor', '1', '2023-12-13 19:47:43');

