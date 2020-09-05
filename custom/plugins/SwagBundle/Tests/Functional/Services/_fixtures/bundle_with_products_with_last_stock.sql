INSERT INTO `s_articles` (`id`, `supplierID`, `name`, `description`, `description_long`, `shippingtime`, `datum`, `active`, `taxID`, `pseudosales`, `topseller`, `metaTitle`, `keywords`, `changetime`, `pricegroupID`, `pricegroupActive`, `filtergroupID`, `laststock`, `crossbundlelook`, `notification`, `template`, `mode`, `main_detail_id`, `available_from`, `available_to`, `configurator_set_id`) VALUES
(273, 1, 'Bundle-UnitTest-main-product', 'Short description', '', NULL, '2020-02-06', 1, 1, 0, 0, 'Bundle-UnitTest-main-product', '', '2020-02-06 11:20:08', NULL, 0, NULL, 1, 0, 0, '', 0, 832, NULL, NULL, 38),
(274, 1, 'Bundle-UnitTest-second-product', '', '', NULL, '2020-02-06', 1, 1, 0, 0, '', '', '2020-02-06 11:21:35', NULL, 0, NULL, 1, 0, 0, '', 0, 836, NULL, NULL, 39);


INSERT INTO `s_articles_prices` (`id`, `pricegroup`, `from`, `to`, `articleID`, `articledetailsID`, `price`, `pseudoprice`, `baseprice`, `percent`) VALUES
(1036, 'EK', 1, 'beliebig', 273, 832, 84.033613445378, 0, NULL, '0.00'),
(1037, 'EK', 1, 'beliebig', 273, 833, 84.033613445378, 0, NULL, '0.00'),
(1038, 'EK', 1, 'beliebig', 273, 834, 84.033613445378, 0, NULL, '0.00'),
(1039, 'EK', 1, 'beliebig', 273, 835, 84.033613445378, 0, NULL, '0.00'),
(1041, 'EK', 1, 'beliebig', 274, 836, 84.033613445378, 0, NULL, '0.00'),
(1042, 'EK', 1, 'beliebig', 274, 837, 84.033613445378, 0, NULL, '0.00'),
(1043, 'EK', 1, 'beliebig', 274, 838, 84.033613445378, 0, NULL, '0.00'),
(1044, 'EK', 1, 'beliebig', 274, 839, 84.033613445378, 0, NULL, '0.00');


INSERT INTO `s_articles_details` (`id`, `articleID`, `ordernumber`, `suppliernumber`, `kind`, `additionaltext`, `sales`, `active`, `instock`, `stockmin`, `laststock`, `weight`, `position`, `width`, `height`, `length`, `ean`, `unitID`, `purchasesteps`, `maxpurchase`, `minpurchase`, `purchaseunit`, `referenceunit`, `packunit`, `releasedate`, `shippingfree`, `shippingtime`, `purchaseprice`) VALUES
(832, 273, 'SW10002', '', 1, '', 0, 1, 0, 0, 1, NULL, 0, NULL, NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, '', NULL, 0, '', 0),
(833, 273, 'SW10002.4', '', 2, '', 0, 1, 100, 0, 1, NULL, 0, NULL, NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, '', NULL, 0, '', 0),
(834, 273, 'SW10002.5', '', 2, '', 0, 1, 100, 0, 1, NULL, 0, NULL, NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, '', NULL, 0, '', 0),
(835, 273, 'SW10002.6', '', 2, '', 0, 1, 100, 0, 1, NULL, 0, NULL, NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, '', NULL, 0, '', 0),
(836, 274, 'SW10005', '', 1, '', 0, 1, 0, 0, 1, NULL, 0, NULL, NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, '', NULL, 0, '', 0),
(837, 274, 'SW10005.5', '', 2, '', 0, 1, 100, 0, 1, NULL, 0, NULL, NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, '', NULL, 0, '', 0),
(838, 274, 'SW10005.6', '', 2, '', 0, 1, 100, 0, 1, NULL, 0, NULL, NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, '', NULL, 0, '', 0),
(839, 274, 'SW10005.7', '', 2, '', 0, 1, 100, 0, 1, NULL, 0, NULL, NULL, NULL, '', NULL, NULL, NULL, 1, NULL, NULL, '', NULL, 0, '', 0);


INSERT INTO `s_articles_attributes` (`id`, `articledetailsID`, `attr1`, `attr2`, `attr3`, `attr4`, `attr5`, `attr6`, `attr7`, `attr8`, `attr9`, `attr10`, `attr11`, `attr12`, `attr13`, `attr14`, `attr15`, `attr16`, `attr17`, `attr18`, `attr19`, `attr20`) VALUES
(871, 832, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(872, 833, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(873, 834, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(874, 835, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(875, 836, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(876, 837, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(877, 838, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(878, 839, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);


INSERT INTO `s_articles_bundles` (`id`, `articleID`, `name`, `show_name`, `active`, `description`, `rab_type`, `taxID`, `ordernumber`, `max_quantity_enable`, `display_global`, `display_delivery`, `max_quantity`, `valid_from`, `valid_to`, `datum`, `sells`, `bundle_type`, `bundle_position`) VALUES
(200, 273, 'UNIT TEST BUNDLE', 0, 1, '', 'pro', NULL, '08154711', 0, 1, 1, 0, NULL, NULL, '2020-02-06 11:23:24', 0, 2, 0);


INSERT INTO `s_articles_bundles_articles` (`id`, `bundle_id`, `article_detail_id`, `quantity`, `configurable`, `bundle_group_id`, `position`) VALUES
(222, 200, 836, 1, 1, NULL, 1);


INSERT INTO `s_articles_bundles_customergroups` (`id`, `bundle_id`, `customer_group_id`) VALUES
(333, 200, 1);


INSERT INTO `s_articles_bundles_prices` (`id`, `bundle_id`, `customer_group_id`, `price`) VALUES
(222, 200, '1', 10);
