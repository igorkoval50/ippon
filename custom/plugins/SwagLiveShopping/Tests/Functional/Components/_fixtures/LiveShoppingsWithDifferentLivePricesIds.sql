INSERT INTO `s_articles_lives` (`id`, `article_id`, `type`, `name`, `active`, `order_number`, `max_quantity_enable`, `max_quantity`, `max_purchase`, `valid_from`, `valid_to`, `datum`, `sells`) VALUES
(1, 13, 1, 'Liveshopping 1', 1, '08154711-1', 0, 0, 0, '2019-10-21 00:00:00', '2099-10-31 00:00:00', '2019-10-21 08:00:00', 0),
(2, 14, 2, 'Liveshopping 2', 1, '08154711-2', 0, 0, 0, '2019-10-21 00:00:00', '2099-10-21 00:00:00', '2019-10-21 08:00:00', 0),
(3, 15, 1, 'Liveshopping 3', 1, '08154711-3', 0, 0, 0, '2019-10-21 00:00:00', '2099-10-31 00:00:00', '2019-10-21 08:00:00', 0);

INSERT INTO `s_articles_live_customer_groups` (`id`, `live_shopping_id`, `customer_group_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(4, 3, 1);

INSERT INTO `s_articles_live_prices` (`id`, `live_shopping_id`, `customer_group_id`, `price`, `endprice`) VALUES
(101, 1, 1, 2.1008403361345, 0.84033613445378),
(202, 2, 1, 8.4033613445378, 0.084033613445378),
(303, 3, 1, 2.0168067226891, 0.84033613445378);

INSERT INTO `s_articles_live_shoprelations` (`id`, `live_shopping_id`, `shop_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(4, 3, 1);
