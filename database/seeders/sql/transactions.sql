-- Inserting into transactions table
INSERT INTO `transactions` (`amount`, `created_at`, `id`, `order_details_id`, `order_id`, `paid_by`, `paid_to`, `payer_id`, `payment_for`, `payment_method`, `payment_receiver_id`, `payment_status`, `transaction_type`, `updated_at`) VALUES
('376000', '2023-05-18 07:09:36', '1', NULL, '100033', '14', NULL, '0', 'Products', 'stripe', NULL, 'succeeded', 'Purchase', '2023-05-18 07:09:36'),
('376000', '2023-05-18 10:42:09', '2', NULL, '100034', '14', NULL, '0', 'Products', 'stripe', NULL, 'succeeded', 'Purchase', '2023-05-18 10:42:09'),
('40000', '2023-05-19 05:19:41', '3', NULL, '100045', '14', NULL, '0', 'Products', 'stripe', NULL, 'succeeded', 'Purchase', '2023-05-19 05:19:41'),
('40000', '2023-05-19 05:22:31', '4', NULL, '100048', '14', NULL, '0', 'Products', 'stripe', NULL, 'succeeded', 'Purchase', '2023-05-19 05:22:31'),
('376000', '2023-05-19 05:22:57', '5', NULL, '100049', '14', NULL, '0', 'Products', 'stripe', NULL, 'succeeded', 'Purchase', '2023-05-19 05:22:57'),
('40000', '2023-05-19 05:25:08', '6', NULL, '100050', '14', NULL, '0', 'Products', 'stripe', NULL, 'succeeded', 'Purchase', '2023-05-19 05:25:08');

