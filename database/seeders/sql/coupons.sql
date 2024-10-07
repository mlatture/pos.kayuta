-- Inserting into coupons table
INSERT INTO `coupons` (`added_by`, `code`, `coupon_bearer`, `coupon_type`, `created_at`, `customer_id`, `discount`, `discount_type`, `expire_date`, `id`, `limit`, `max_discount`, `min_purchase`, `seller_id`, `start_date`, `status`, `title`, `updated_at`) VALUES
('admin', 'v0k79kil3x', 'inhouse', NULL, '2023-10-23 07:17:00', NULL, '10.00', 'percentage', '2023-12-27', '1', '1', '50.00', '100.00', NULL, '2023-10-22', '1', 'Site', '2023-10-23 07:17:00'),
('admin', '100off', 'inhouse', NULL, '2023-11-05 18:13:47', NULL, '100.00', 'amount', '2023-12-09', '2', '10', '100.00', '150.00', NULL, '2023-11-05', '1', '100off', '2023-11-05 18:13:47');

