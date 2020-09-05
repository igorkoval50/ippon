INSERT INTO `s_plugin_promotion` (`id`, `name`, `rules`, `apply_rules`, `type`, `number`, `description`, `detail_description`, `max_usage`, `voucher_id`, `no_vouchers`, `valid_from`, `valid_to`, `stack_mode`, `amount`, `step`, `max_quantity`, `active`, `exclusive`, `shipping_free`, `priority`, `stop_processing`, `show_badge`, `badge_text`, `free_goods_badge_text`, `apply_rules_first`, `show_hint_in_basket`, `discount_display`, `buy_button_mode`) VALUES
(1, 'promotion1', '{\"and\":{\"true1\":[]}}', '{\"and\":{\"true1\":[]}}', 'product.absolute', '08154711', '', '', 0, NULL, 0, '2020-01-03 00:00:00', '2020-01-13 00:00:00', 'global', 10, NULL, NULL, 1, 0, 0, 0, 0, 1, 'asd', '', 0, 1, 'single', 'details'),
(2, 'promotion2', '{\"and\":{\"true1\":[]}}', '{\"and\":{\"true1\":[]}}', 'product.absolute', '08154712', '', '', 0, NULL, 0, '2020-01-10 00:00:00', '2020-01-20 00:00:00', 'global', 10, NULL, NULL, 1, 0, 0, 0, 0, 1, 'asd', '', 0, 1, 'single', 'details'),
(3, 'promotion3', '{\"and\":{\"true1\":[]}}', '{\"and\":{\"true1\":[]}}', 'product.absolute', '08154713', '', '', 0, NULL, 0, '2020-02-01 00:00:00', '2020-02-28 00:00:00', 'global', 10, NULL, NULL, 1, 0, 0, 0, 0, 1, 'asd', '', 0, 1, 'single', 'details');

INSERT INTO `s_order` (`id`, `ordernumber`, `userID`, `invoice_amount`, `invoice_amount_net`, `invoice_shipping`, `invoice_shipping_net`, `invoice_shipping_tax_rate`, `ordertime`, `status`, `cleared`, `paymentID`, `transactionID`, `comment`, `customercomment`, `internalcomment`, `net`, `taxfree`, `partnerID`, `temporaryID`, `referer`, `cleareddate`, `trackingcode`, `language`, `dispatchID`, `currency`, `currencyFactor`, `subshopID`, `remote_addr`, `deviceType`, `is_proportional_calculation`, `changed`) VALUES
(59, '20003', 1, 190.9, 160.43, 3.9, 3.28, 19, '2020-01-01 07:17:06', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-19 07:17:06'),
(60, '20004', 1, 190.9, 160.43, 3.9, 3.28, 19, '2020-01-04 07:17:06', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-19 07:17:06'),
(61, '20005', 1, 190.9, 160.43, 3.9, 3.28, 19, '2020-01-12 07:17:06', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-19 07:17:06'),
(62, '20006', 1, 190.9, 160.43, 3.9, 3.28, 19, '2020-01-12 07:17:06', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-19 07:17:06'),
(63, '20065', 1, 190.9, 160.43, 3.9, 3.28, 19, '2020-01-18 07:17:06', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-19 07:17:06'),
(64, '20007', 1, 190.9, 160.43, 3.9, 3.28, 19, '2020-02-01 07:17:06', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-19 07:17:06'),
(65, '20008', 1, 190.9, 160.43, 3.9, 3.28, 19, '2020-02-04 07:17:06', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-19 07:17:06'),
(66, '20009', 1, 190.9, 160.43, 3.9, 3.28, 19, '2020-02-12 07:17:06', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-19 07:17:06'),
(67, '20010', 1, 190.9, 160.43, 3.9, 3.28, 19, '2020-02-18 07:17:06', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-19 07:17:06');

INSERT INTO `s_plugin_promotion_customer_count` (`id`, `promotion_id`, `customer_id`, `order_id`) VALUES
(1, 1, 1, 59),
(2, 1, 1, 60),
(3, 1, 1, 61),
(4, 2, 1, 62),
(5, 2, 1, 63),
(6, 3, 1, 64),
(7, 3, 1, 65),
(8, 3, 1, 66),
(9, 3, 1, 67);
