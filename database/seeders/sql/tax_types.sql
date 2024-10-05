-- Inserting into tax_types table
INSERT INTO `tax_types` (`id`, `organization_id`, `title`, `tax_type`, `tax`, `created_at`, `updated_at`) VALUES
('1', NULL, 'Nontaxed', 'percentage', '0', '2024-02-16 16:38:37', '2024-03-15 16:37:08'),
('2', NULL, 'Apparel', 'percentage', '6.15', '2024-02-18 05:51:41', '2024-05-10 13:08:34'),
('5', NULL, 'POS Tax Type', 'percentage', '1', '2024-04-14 12:11:44', '2024-04-14 12:11:44'),
('6', NULL, 'General Merchandise', 'percentage', '5', '2024-04-14 14:57:42', '2024-04-14 14:57:42'),
('7', NULL, 'Test tax', 'percentage', '10', '2024-05-04 14:02:32', '2024-05-04 14:02:32'),
('8', '1', 'tax', 'percentage', '6.25', '2024-05-10 03:23:09', '2024-05-10 03:23:09'),
('9', '1', 'Cheese Tax', 'percentage', '10.5', '2024-05-10 12:07:04', '2024-05-10 12:07:04');

