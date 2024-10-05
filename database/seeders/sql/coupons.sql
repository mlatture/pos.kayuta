-- Inserting into coupons table
INSERT INTO `coupons` (`id`, `added_by`, `coupon_type`, `coupon_bearer`, `seller_id`, `customer_id`, `title`, `code`, `start_date`, `expire_date`, `min_purchase`, `max_discount`, `discount`, `discount_type`, `status`, `created_at`, `updated_at`, `limit`) VALUES
('1', 'admin', NULL, 'inhouse', NULL, NULL, 'Site', 'v0k79kil3x', '2023-10-22', '2023-12-27', '100.00', '50.00', '10.00', 'percentage', '1', '2023-10-23 07:17:00', '2023-10-23 07:17:00', '1'),
('2', 'admin', NULL, 'inhouse', NULL, NULL, '100off', '100off', '2023-11-05', '2023-12-09', '150.00', '100.00', '100.00', 'amount', '1', '2023-11-05 18:13:47', '2023-11-05 18:13:47', '10');

