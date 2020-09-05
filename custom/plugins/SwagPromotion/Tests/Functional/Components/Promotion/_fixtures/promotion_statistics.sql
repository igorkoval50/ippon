INSERT INTO `s_plugin_promotion` (`id`, `name`, `rules`, `apply_rules`, `type`, `number`, `description`, `detail_description`, `max_usage`, `voucher_id`, `no_vouchers`, `valid_from`, `valid_to`, `stack_mode`, `amount`, `step`, `max_quantity`, `active`, `exclusive`, `shipping_free`, `priority`, `stop_processing`, `show_badge`, `badge_text`, `free_goods_badge_text`, `apply_rules_first`, `show_hint_in_basket`, `discount_display`, `buy_button_mode`) VALUES
(10000, 'Meine neue Promotion 1', '{\"and\":{\"true1\":[]}}', '{\"and\":{\"productCompareRule0.43590175069812487\":[\"detail::ordernumber\",\"=\",\"SW10002.2\"]}}', 'product.absolute', 'ORDERNUMBER', '', '', 0, NULL, 0, '2020-03-01 00:01:00', '2020-03-17 23:59:00', 'global', 10, NULL, NULL, 1, 0, 0, 0, 0, 1, 'BADGE TEXT', '', 0, 1, 'single', 'details'),
(10001, 'Meine neue Promotion 2', '{\"and\":{\"true1\":[]}}', '{\"and\":{\"productCompareRule0.7336643077575902\":[\"price::price\",\">=\",\"19,90\"]}}', 'product.absolute', 'ORDERNUMBER', '', '', 0, NULL, 0, '2020-02-28 00:01:00', '2020-03-16 23:59:00', 'global', 10, NULL, NULL, 1, 0, 0, 0, 0, 1, 'BADGE TEXT', '', 0, 1, 'single', 'details'),
(10002, 'Meine neue Promotion 3', '{\"and\":{\"true1\":[]}}', '{\"and\":{\"productCompareRule0.7336643077575902\":[\"price::price\",\">=\",\"19,90\"]}}', 'product.absolute', 'ORDERNUMBER', '', '', 0, NULL, 0, '2020-02-27 00:01:00', '2020-03-15 23:59:00', 'global', 10, NULL, NULL, 1, 0, 0, 0, 0, 1, 'BADGE TEXT', '', 0, 1, 'single', 'details'),
(10003, 'Meine neue Promotion 4', '{\"and\":{\"true1\":[]}}', '{\"and\":{\"productCompareRule0.7336643077575902\":[\"price::price\",\">=\",\"19,90\"]}}', 'product.absolute', 'ORDERNUMBER', '', '', 0, NULL, 0, '2020-02-26 00:01:00', '2020-03-14 23:59:00', 'global', 10, NULL, NULL, 1, 0, 0, 0, 0, 1, 'BADGE TEXT', '', 0, 1, 'single', 'details');


INSERT INTO `s_plugin_promotion_customer_count` (`id`, `promotion_id`, `customer_id`, `order_id`) VALUES
(1, 10000, 1, 62),
(2, 10001, 1, 63),
(3, 10002, 1, 64),
(4, 10003, 1, 65);


INSERT INTO `s_order` (`id`, `ordernumber`, `userID`, `invoice_amount`, `invoice_amount_net`, `invoice_shipping`, `invoice_shipping_net`, `invoice_shipping_tax_rate`, `ordertime`, `status`, `cleared`, `paymentID`, `transactionID`, `comment`, `customercomment`, `internalcomment`, `net`, `taxfree`, `partnerID`, `temporaryID`, `referer`, `cleareddate`, `trackingcode`, `language`, `dispatchID`, `currency`, `currencyFactor`, `subshopID`, `remote_addr`, `deviceType`, `is_proportional_calculation`, `changed`) VALUES
(62, '20003', 1, 27.85, 23.41, 3.9, 3.28, 19, '2020-03-18 10:18:22', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-18 10:18:22'),
(63, '20004', 1, 27.85, 23.41, 3.9, 3.28, 19, '2020-03-18 10:18:22', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-18 10:18:22'),
(64, '20005', 1, 27.85, 23.41, 3.9, 3.28, 19, '2020-03-18 10:18:22', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-18 10:18:22'),
(65, '20006', 1, 27.85, 23.41, 3.9, 3.28, 19, '2020-03-18 10:18:22', 0, 17, 5, '', '', '', '', 0, 0, '', '', '', NULL, '', '1', 9, 'EUR', 1, 1, '172.18.0.0', 'desktop', 0, '2020-03-18 10:18:22');
