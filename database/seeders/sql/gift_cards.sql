-- Inserting into gift_cards table
INSERT INTO `gift_cards` (`id`, `organization_id`, `title`, `user_email`, `barcode`, `discount_type`, `discount`, `start_date`, `expire_date`, `min_purchase`, `max_discount`, `limit`, `status`, `created_at`, `updated_at`, `amount`, `modified_by`) VALUES
('1', NULL, 'Card1', 'mlatture@gmail.com', '0927484940', 'percentage', '12', '2024-02-17', '2025-02-28', '200', '300', '1', '1', '2024-02-17 23:29:56', '2024-03-15 15:16:09', '0', NULL),
('2', NULL, '10 percent off', NULL, '10%', 'percentage', '10', '2024-03-15', '2024-03-29', '50', '20', '10', '1', '2024-03-15 18:33:26', '2024-03-15 18:33:26', '0', NULL),
('3', NULL, 'QAA', 'testcustomer@yopmail.com', '100000', 'percentage', '50', '2024-05-04', '2024-05-13', '10', '100', '1', '1', '2024-05-04 17:17:42', '2024-05-04 17:21:05', '0', NULL),
('4', NULL, '5 Dollars off', 'mark@latture.com', '$5.00', 'fixed_amount', '5', '2024-05-10', '2034-05-10', '100', '5', '1', '1', '2024-05-10 13:15:18', '2024-05-10 13:15:18', '0', NULL);

