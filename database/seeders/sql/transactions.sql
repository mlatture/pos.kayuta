-- Inserting into transactions table
INSERT INTO `transactions` (`id`, `order_id`, `payment_for`, `payer_id`, `payment_receiver_id`, `paid_by`, `paid_to`, `payment_method`, `payment_status`, `created_at`, `updated_at`, `amount`, `transaction_type`, `order_details_id`) VALUES
('1', '100033', 'Products', '0', NULL, '14', NULL, 'stripe', 'succeeded', '2023-05-18 07:09:36', '2023-05-18 07:09:36', '376000', 'Purchase', NULL),
('2', '100034', 'Products', '0', NULL, '14', NULL, 'stripe', 'succeeded', '2023-05-18 10:42:09', '2023-05-18 10:42:09', '376000', 'Purchase', NULL),
('3', '100045', 'Products', '0', NULL, '14', NULL, 'stripe', 'succeeded', '2023-05-19 05:19:41', '2023-05-19 05:19:41', '40000', 'Purchase', NULL),
('4', '100048', 'Products', '0', NULL, '14', NULL, 'stripe', 'succeeded', '2023-05-19 05:22:31', '2023-05-19 05:22:31', '40000', 'Purchase', NULL),
('5', '100049', 'Products', '0', NULL, '14', NULL, 'stripe', 'succeeded', '2023-05-19 05:22:57', '2023-05-19 05:22:57', '376000', 'Purchase', NULL),
('6', '100050', 'Products', '0', NULL, '14', NULL, 'stripe', 'succeeded', '2023-05-19 05:25:08', '2023-05-19 05:25:08', '40000', 'Purchase', NULL);

