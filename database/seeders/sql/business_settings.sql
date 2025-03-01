-- Inserting into business_settings table
INSERT INTO `business_settings` (created_at,  type, updated_at, value) VALUES
('2020-10-11 12:43:44',  'system_default_currency', '2021-06-04 23:25:29', '1'),
('2020-10-11 12:53:02',  'language', '2021-06-11 02:16:25', '[{\"id\":\"1\",\"name\":\"english\",\"code\":\"en\",\"status\":1}]'),
('2020-10-12 15:29:18',  'mail_config', '2023-09-24 17:28:18', '{\"status\":\"1\",\"name\":\"Kayuta Lake\",\"host\":\"sandbox.smtp.mailtrap.io\",\"driver\":\"smtp\",\"port\":\"2525\",\"username\":\"9e41171e6321d0\",\"email_id\":\"info@kayuta-lake.com\",\"encryption\":\"TLS\",\"password\":\"bd72f3e5674c1a\"}'),
(NULL,  'cash_on_delivery', '2023-05-17 06:50:00', '{\"status\":\"1\"}'),
('2020-11-09 13:36:51',  'ssl_commerz_payment', '2023-01-10 10:51:56', '{\"status\":\"0\",\"environment\":\"sandbox\",\"store_id\":\"\",\"store_password\":\"\"}'),
('2020-11-09 13:51:39',  'paypal', '2023-01-10 10:51:56', '{\"status\":\"0\",\"environment\":\"sandbox\",\"paypal_client_id\":\"\",\"paypal_secret\":\"\"}'),
('2020-11-09 14:01:47',  'stripe', '2023-05-12 11:56:07', '{\"status\":\"1\",\"environment\":\"sandbox\",\"api_key\":\"sk_test_51N6ZB2ClUNYjiHE88zQ0x84EZO9wsIsK03OJfaJonfF8ofYCTqLZZvaI4Su6gIUM9ujionPpZrU2psdOR5oxyh5a000P7KWs4u\",\"published_key\":\"pk_test_51N6ZB2ClUNYjiHE8iIDdwX1V1GnuSBR8dnkyC3usR1c4OIOmszkc5x382vdmDxYJRBmd8xYtlyWfOdrXDTi0wh6u00ieDDbOyk\"}'),
(NULL,  'company_phone', '2020-12-08 19:15:01', '315-831-5077'),
(NULL,  'company_name', '2021-02-27 23:11:53', 'Kayuta Lake Campground Marina and Boat Launch'),
(NULL,  'company_web_logo', '2023-11-29 20:38:10', '2023-11-29-6567a132910e7.png'),
(NULL,  'company_mobile_logo', '2021-02-20 19:30:04', '2021-02-20-6030c88c91911.png'),
(NULL,  'terms_condition', '2021-06-11 06:51:36', '<p>terms and conditions</p>'),
(NULL, 'about_us', '2021-06-11 06:42:53', '<p>this is about us page. hello and hi from about page description..</p>'),
(NULL,  'sms_nexmo', NULL, '{\"status\":\"0\",\"nexmo_key\":\"custo5cc042f7abf4c\",\"nexmo_secret\":\"custo5cc042f7abf4c@ssl\"}'),
(NULL,  'company_email', '2021-03-15 17:29:51', 'info@kayuta.com'),
('2020-10-11 18:53:02',  'colors', '2023-11-29 20:38:10', '{\"primary\":\"#1b7fed\",\"secondary\":\"#000000\"}'),
(NULL,  'company_footer_logo', '2023-09-26 17:59:31', '2023-09-26-65131c03e1cad.png'),
(NULL,  'company_copyright_text', '2021-03-15 17:30:47', 'Copyright WebDaVinci 2023'),
(NULL,  'download_app_apple_stroe', '2020-12-08 17:54:53', '{\"status\":\"1\",\"link\":\"https:\\/\\/www.target.com\\/s\\/apple+store++now?ref=tgt_adv_XS000000&AFID=msn&fndsrc=tgtao&DFA=71700000012505188&CPNG=Electronics_Portable+Computers&adgroup=Portable+Computers&LID=700000001176246&LNM=apple+store+near+me+now&MT=b&network=s&device=c&location=12&targetid=kwd-81913773633608:loc-12&ds_rl=1246978&ds_rl=1248099&gclsrc=ds\"}'),
(NULL,  'download_app_google_stroe', '2020-12-08 17:54:48', '{\"status\":\"1\",\"link\":\"https:\\/\\/play.google.com\\/store?hl=en_US&gl=US\"}'),
('2020-10-11 18:53:02',  'company_fav_icon', '2023-09-26 17:59:31', '2023-09-26-65131c03e6ee9.png'),
(NULL, 'fcm_topic', NULL, ''),
(NULL,  'fcm_project_id', NULL, ''),
(NULL,  'push_notification_key', NULL, 'Put your firebase server key here.'),
(NULL,  'order_pending_message', NULL, '{\"status\":\"1\",\"message\":\"order pen message\"}'),
(NULL,  'order_confirmation_msg', NULL, '{\"status\":\"1\",\"message\":\"Order con Message\"}'),
(NULL,  'order_processing_message', NULL, '{\"status\":\"1\",\"message\":\"Order pro Message\"}'),
(NULL,  'out_for_delivery_message', NULL, '{\"status\":\"1\",\"message\":\"Order ouut Message\"}'),
(NULL,  'order_delivered_message', NULL, '{\"status\":\"1\",\"message\":\"Order del Message\"}'),
(NULL,  'razor_pay', '2021-07-06 17:30:14', '{\"status\":\"0\",\"razor_key\":null,\"razor_secret\":null}'),
(NULL, 'sales_commission', '2023-05-09 01:33:00', '10'),
(NULL, 'seller_registration', '2021-06-05 02:02:48', '1'),
(NULL,  'pnc_language', NULL, '[\"en\"]'),
(NULL,  'order_returned_message', NULL, '{\"status\":\"1\",\"message\":\"Order hh Message\"}'),
(NULL,  'order_failed_message', NULL, '{\"status\":null,\"message\":\"Order fa Message\"}'),
(NULL,  'delivery_boy_assign_message', NULL, '{\"status\":0,\"message\":\"\"}'),
(NULL,  'delivery_boy_start_message', NULL, '{\"status\":0,\"message\":\"\"}'),
(NULL,  'delivery_boy_delivered_message', NULL, '{\"status\":0,\"message\":\"\"}'),
(NULL,  'terms_and_conditions', NULL, ''),
(NULL,  'minimum_order_value', NULL, '1'),
(NULL,  'privacy_policy', '2021-07-06 16:09:07', '<p>my privacy policy</p>

<p>&nbsp;</p>'),
(NULL,  'paystack', '2021-07-06 17:30:35', '{\"status\":\"0\",\"publicKey\":null,\"secretKey\":null,\"paymentUrl\":\"https:\\/\\/api.paystack.co\",\"merchantEmail\":null}'),
(NULL,  'senang_pay', '2021-07-06 17:30:23', '{\"status\":\"0\",\"secret_key\":null,\"merchant_id\":null}'),
(NULL, 'currency_model', NULL, 'single_currency'),
(NULL,  'social_login', NULL, '[{\"login_medium\":\"google\",\"client_id\":\"\",\"client_secret\":\"\",\"status\":\"\"},{\"login_medium\":\"facebook\",\"client_id\":\"\",\"client_secret\":\"\",\"status\":\"\"}]'),
('2023-05-12 10:37:03',  'digital_payment', '2023-05-12 10:37:03', '{\"status\":\"0\"}'),
(NULL,  'phone_verification', NULL, '0'),
(NULL, 'email_verification', NULL, '0'),
(NULL,  'order_verification', NULL, '0'),
(NULL,  'country_code', NULL, 'US'),
(NULL,  'pagination_limit', NULL, '8'),
(NULL,  'shipping_method', NULL, 'sellerwise_shipping'),
(NULL,  'paymob_accept', NULL, '{\"status\":\"0\",\"api_key\":\"\",\"iframe_id\":\"\",\"integration_id\":\"\",\"hmac\":\"\"}'),
(NULL,  'bkash', '2023-01-10 10:51:56', '{\"status\":\"0\",\"environment\":\"sandbox\",\"api_key\":\"\",\"api_secret\":\"\",\"username\":\"\",\"password\":\"\"}'),
(NULL,  'forgot_password_verification', NULL, '0'),
(NULL,  'paytabs', '2021-11-21 08:01:40', '{\"status\":0,\"profile_id\":\"\",\"server_key\":\"\",\"base_url\":\"https:\\/\\/secure-egypt.paytabs.com\\/\"}'),
(NULL,  'stock_limit', NULL, '10'),
(NULL,  'flutterwave', '2023-05-12 10:37:48', '{\"status\":null,\"environment\":\"sandbox\",\"public_key\":null,\"secret_key\":null,\"hash\":null}'),
(NULL,  'mercadopago', '2023-05-12 10:38:04', '{\"status\":null,\"environment\":\"sandbox\",\"public_key\":null,\"access_token\":null}'),
(NULL,  'announcement', NULL, '{\"status\":\"1\",\"color\":\"#0d15fd\",\"text_color\":\"#e8b5b5\",\"announcement\":\"Walking tacos bogo!\"}'),
(NULL,  'fawry_pay', '2022-01-18 14:46:30', '{\"status\":0,\"merchant_code\":\"\",\"security_key\":\"\"}'),
(NULL,  'recaptcha', '2022-01-18 14:46:30', '{\"status\":0,\"site_key\":\"\",\"secret_key\":\"\"}'),
(NULL,  'seller_pos', NULL, '0'),
(NULL,  'liqpay', NULL, '{\"status\":0,\"public_key\":\"\",\"private_key\":\"\"}'),
(NULL,  'paytm', '2023-01-10 10:51:56', '{\"status\":0,\"environment\":\"sandbox\",\"paytm_merchant_key\":\"\",\"paytm_merchant_mid\":\"\",\"paytm_merchant_website\":\"\",\"paytm_refund_url\":\"\"}'),
(NULL,  'refund_day_limit', NULL, '0'),
(NULL,  'business_mode', NULL, 'multi'),
(NULL,  'mail_config_sendgrid', '2023-09-24 17:28:18', '{\"status\":0,\"name\":\"\",\"host\":\"\",\"driver\":\"\",\"port\":\"\",\"username\":\"\",\"email_id\":\"\",\"encryption\":\"\",\"password\":\"\"}'),
(NULL,  'decimal_point_settings', NULL, '2'),
(NULL,  'shop_address', NULL, 'Kayuta Lake Campground Marina and Boat Launch 10892 Campground Road, Forestport, NY 13338'),
(NULL,  'billing_input_by_customer', NULL, '1'),
(NULL,  'wallet_status', NULL, '0'),
(NULL,  'loyalty_point_status', NULL, '0'),
(NULL,  'wallet_add_refund', NULL, '0'),
(NULL,  'loyalty_point_exchange_rate', NULL, '0'),
(NULL,  'loyalty_point_item_purchase_point', NULL, '0'),
(NULL,  'loyalty_point_minimum_point', NULL, '0'),
(NULL,  'minimum_order_limit', NULL, '1'),
(NULL,  'product_brand', '2023-05-13 04:01:14', '0'),
(NULL,  'digital_product', '2023-05-13 04:01:19', '0'),
(NULL,  'delivery_boy_expected_delivery_date_message', NULL, '{\"status\":0,\"message\":\"\"}'),
(NULL,  'order_canceled', NULL, '{\"status\":0,\"message\":\"\"}'),
(NULL,  'refund-policy', '2023-03-04 11:25:36', '{\"status\":1,\"content\":\"\"}'),
(NULL,  'return-policy', '2023-03-04 11:25:36', '{\"status\":1,\"content\":\"\"}'),
(NULL,  'cancellation-policy', '2023-03-04 11:25:36', '{\"status\":1,\"content\":\"\"}'),
(NULL,  'offline_payment', '2023-03-04 11:25:36', '{\"status\":0}'),
(NULL,  'temporary_close', '2023-03-04 11:25:36', '{\"status\":0}'),
(NULL,  'vacation_add', '2023-03-04 11:25:36', '{\"status\":0,\"vacation_start_date\":null,\"vacation_end_date\":null,\"vacation_note\":null}'),
(NULL,  'cookie_setting', '2023-03-04 11:25:36', '{\"status\":0,\"cookie_text\":null}'),
(NULL,  'timezone', NULL, 'US/Eastern'),
(NULL,  'default_location', NULL, '{\"lat\":\"12\",\"lng\":\"1223123\"}'),
(NULL,  'map_url', NULL, 'https://www.google.com/maps/place/Kayuta+Lake+Campground+and+Marina/@43.4084778,-75.167811,17z/data=!4m15!1m5!3m4!2zNDPCsDI0JzMwLjUiTiA3NcKwMTAnMDQuMSJX!8m2!3d43.4084778!4d-75.167811!3m8!1s0x89d927ddbd624039:0x969d34c23dbb96f3!5m2!4m1!1i2!8m2!3d43.408474!4d-75.1631976!16s%2Fg%2F1tg7s_rr?entry=ttu'),
('2023-10-26 05:31:32', 'currency_symbol_position', '2023-10-26 05:31:33', 'left'),
(NULL,  'season_open_date', NULL, '2023-05-13'),
(NULL, 'season_close_date', NULL, '2023-11-30');

