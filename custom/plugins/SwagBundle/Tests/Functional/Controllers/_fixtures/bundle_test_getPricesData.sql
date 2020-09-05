INSERT IGNORE INTO `s_articles` (`id`, `supplierID`, `name`, `description`, `description_long`, `shippingtime`, `datum`, `active`, `taxID`, `pseudosales`, `topseller`, `metaTitle`, `keywords`, `changetime`, `pricegroupID`, `pricegroupActive`, `filtergroupID`, `laststock`, `crossbundlelook`, `notification`, `template`, `mode`, `main_detail_id`, `available_from`, `available_to`, `configurator_set_id`) VALUES
(273, 14, 'PhpUnitBaseProduct', '', '', NULL, '2019-09-19', 1, 1, 0, 0, '', '', '2019-09-19 10:38:08', 1, 0, NULL, 0, 0, 0, '', 0, 842, NULL, NULL, 38),
(274, 14, 'PHPUNIT - variante', '', '', NULL, '2019-09-19', 1, 1, 0, 0, '', '', '2019-09-19 10:38:25', 1, 0, NULL, 0, 0, 0, '', 0, 846, NULL, NULL, 39);

INSERT IGNORE INTO `s_articles_details` (`id`, `articleID`, `ordernumber`, `suppliernumber`, `kind`, `additionaltext`, `sales`, `active`, `instock`, `stockmin`, `laststock`, `weight`, `position`, `width`, `height`, `length`, `ean`, `unitID`, `purchasesteps`, `maxpurchase`, `minpurchase`, `purchaseunit`, `referenceunit`, `packunit`, `releasedate`, `shippingfree`, `shippingtime`, `purchaseprice`) VALUES
(842, 273, 'PHPUNIT10000', '08154711', 1, '', 0, 1, 1000, 0, 0, '1.000', 0, NULL, NULL, NULL, '', 1, NULL, NULL, 1, '0.2000', '1.000', 'Flaschen', NULL, 0, '', 0),
(843, 273, 'PHPUNIT10000.1', '08154711', 2, '', 0, 1, 0, 0, 0, '1.000', 0, NULL, NULL, NULL, '', 1, NULL, NULL, 1, '0.5000', '1.000', 'Flaschen', NULL, 0, '', 0),
(844, 273, 'PHPUNIT10000.2', '08154711', 2, '', 0, 1, 0, 0, 0, '1.000', 0, NULL, NULL, NULL, '', 1, NULL, NULL, 1, '0.7000', '1.000', 'Flaschen', NULL, 0, '', 0),
(845, 273, 'PHPUNIT10000.3', '08154711', 2, '', 0, 1, 0, 0, 0, '1.000', 0, NULL, NULL, NULL, '', 1, NULL, NULL, 1, '1.0000', '1.000', 'Flaschen', NULL, 0, '', 0),
(846, 274, 'PHPUNIT10001', '08154711', 1, '', 0, 1, 1000, 0, 0, NULL, 0, NULL, NULL, NULL, '', 1, NULL, NULL, 1, '0.2000', '1.000', 'TetraPack', NULL, 0, '', 0),
(847, 274, 'PHPUNIT10001.1', '08154711', 2, '', 0, 1, 10000, 0, 0, NULL, 0, NULL, NULL, NULL, '', 1, NULL, NULL, 1, '0.5000', '1.000', 'TetraPack', NULL, 0, '', 0);

INSERT IGNORE INTO `s_articles_prices` (`id`, `pricegroup`, `from`, `to`, `articleID`, `articledetailsID`, `price`, `pseudoprice`, `baseprice`, `percent`) VALUES
(1046, 'EK', 1, 'beliebig', 273, 842, 16.806722689076, 0, NULL, '0.00'),
(1047, 'EK', 1, 'beliebig', 273, 843, 37.81512605042, 0, NULL, '0.00'),
(1048, 'EK', 1, 'beliebig', 273, 844, 50.420168067227, 0, NULL, '0.00'),
(1049, 'EK', 1, 'beliebig', 273, 845, 67.226890756303, 0, NULL, '0.00'),
(1051, 'EK', 1, 'beliebig', 274, 846, 8.4033613445378, 0, NULL, '0.00'),
(1052, 'EK', 1, 'beliebig', 274, 847, 15.126050420168, 0, NULL, '0.00');

INSERT IGNORE INTO `s_articles_categories` (`id`, `articleID`, `categoryID`) VALUES
(3838, 273, 14),
(3839, 274, 14);

INSERT IGNORE INTO `s_articles_categories_ro` (`id`, `articleID`, `categoryID`, `parentCategoryID`) VALUES
(1012, 273, 14, 14),
(1013, 273, 5, 14),
(1014, 273, 3, 14),
(1015, 274, 14, 14),
(1016, 274, 5, 14),
(1017, 274, 3, 14);

INSERT IGNORE INTO `s_article_configurator_sets` (`id`, `name`, `public`, `type`) VALUES
(38, 'Set-PHPUNIT10000', 0, 0),
(39, 'Set-PHPUNIT10001', 0, 0);

INSERT IGNORE INTO `s_article_configurator_option_relations` (`id`, `article_id`, `option_id`) VALUES
(930, 842, 11),
(931, 843, 35),
(932, 844, 12),
(933, 845, 32),
(934, 846, 88),
(935, 847, 89);

INSERT IGNORE INTO `s_article_configurator_set_group_relations` (`set_id`, `group_id`) VALUES
(38, 5),
(39, 14);

INSERT IGNORE INTO `s_article_configurator_set_option_relations` (`set_id`, `option_id`) VALUES
(38, 11),
(38, 12),
(38, 32),
(38, 35),
(39, 88),
(39, 89);

INSERT IGNORE INTO `s_articles_bundles` (`id`, `articleID`, `name`, `show_name`, `active`, `description`, `rab_type`, `taxID`, `ordernumber`, `max_quantity_enable`, `display_global`, `display_delivery`, `max_quantity`, `valid_from`, `valid_to`, `datum`, `sells`, `bundle_type`, `bundle_position`) VALUES
(2, 273, 'PHPUNITBUNDLE', 0, 1, '', 'pro', NULL, '08154711', 0, 1, 1, 0, NULL, NULL, '2019-09-19 10:30:23', 0, 1, 0);

INSERT IGNORE INTO `s_articles_bundles_articles` (`id`, `bundle_id`, `article_detail_id`, `quantity`, `configurable`, `bundle_group_id`, `position`) VALUES
(3, 2, 846, 1, 1, NULL, 1);

INSERT IGNORE INTO `s_articles_bundles_customergroups` (`id`, `bundle_id`, `customer_group_id`) VALUES
(7, 2, 1);

INSERT IGNORE INTO `s_articles_bundles_prices` (`id`, `bundle_id`, `customer_group_id`, `price`) VALUES
(3, 2, '1', 10);
