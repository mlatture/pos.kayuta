-- Inserting into gift_cards table
INSERT INTO `gift_cards` (`amount`, `barcode`, `created_at`, `discount`, `discount_type`, `expire_date`, `limit`, `max_discount`, `min_purchase`, `modified_by`, `organization_id`, `start_date`, `status`, `title`, `updated_at`, `user_email`) VALUES
('0', '0927484940', '2024-02-17 23:29:56', '12', 'percentage', '2025-02-28',  '1', '300', '200', NULL, NULL, '2024-02-17', '1', 'Card1', '2024-03-15 15:16:09', 'mlatture@gmail.com'),
('0', '10%', '2024-03-15 18:33:26', '10', 'percentage', '2024-03-29',  '10', '20', '50', NULL, NULL, '2024-03-15', '1', '10 percent off', '2024-03-15 18:33:26', NULL),
('0', '100000', '2024-05-04 17:17:42', '50', 'percentage', '2024-05-13', '1', '100', '10', NULL, NULL, '2024-05-04', '1', 'QAA', '2024-05-04 17:21:05', 'testcustomer@yopmail.com'),
('0', '$5.00', '2024-05-10 13:15:18', '5', 'fixed_amount', '2034-05-10',  '1', '5', '100', NULL, NULL, '2024-05-10', '1', '5 Dollars off', '2024-05-10 13:15:18', 'mark@latture.com');

