INSERT INTO `s_plugin_custom_products_template` VALUES
  (1,'inquiry_custom_product','inquiry_custom_product','',NULL,0,1,0,0);

INSERT INTO `s_plugin_custom_products_template_product_relation` (`template_id`, `article_id`) VALUES
  (1, 178);

INSERT INTO `s_plugin_custom_products_option` VALUES
  (2,1,'text1','','',0,'textfield',0,'','',0,NULL,NULL,NULL,3145728,NULL,NULL,1,NULL,0,0),
  (3,1,'checkbox','','',0,'checkbox',1,'','',0,NULL,NULL,NULL,3145728,NULL,NULL,1,NULL,1,0);

INSERT INTO `s_plugin_custom_products_value` VALUES
  (1,3,'value1','value1','',0,0,0,NULL,NULL),
  (2,3,'value2','value2','',0,1,0,NULL,NULL);

INSERT INTO `s_plugin_custom_products_price` VALUES
  (2,2,NULL,8.4033613445378,NULL,0,1,'Shopkunden',1),
  (3,NULL,1,16.806722689076,NULL,0,1,'Shopkunden',1),
  (4,NULL,2,16.806722689076,NULL,0,1,'Shopkunden',1),
  (5,3,NULL,8.4033613445378,NULL,0,1,'Shopkunden',1);

INSERT INTO `s_plugin_custom_products_configuration_hash` (`id`, `hash`, `configuration`, `permanent`, `template`, `created_at`) VALUES
  (11, 'bb06fe423df634e3106415c3766204b8', '{\"2\":[\"TestText1\"],\"3\":[\"1\",\"2\"],\"number\":\"SW10178\"}', 0, '[{\"id\":\"2\",\"template_id\":\"1\",\"name\":\"text1\",\"description\":\"\",\"ordernumber\":\"\",\"required\":\"0\",\"type\":\"textfield\",\"position\":\"0\",\"default_value\":\"\",\"placeholder\":\"\",\"is_once_surcharge\":\"0\",\"max_text_length\":null,\"min_value\":null,\"max_value\":null,\"max_file_size\":\"3145728\",\"min_date\":null,\"max_date\":null,\"max_files\":\"1\",\"interval\":null,\"could_contain_values\":\"0\",\"allows_multiple_selection\":\"0\",\"prices\":[{\"id\":\"2\",\"option_id\":\"2\",\"value_id\":null,\"surcharge\":\"8.4033613445378\",\"percentage\":\"0\",\"is_percentage_surcharge\":\"0\",\"tax_id\":\"1\",\"customer_group_name\":\"Shopkunden\",\"customer_group_id\":\"1\"}],\"netPrice\":8.4033613445378,\"surcharge\":8.4033613445378,\"tax_id\":\"1\",\"tax\":1.6,\"isTaxFreeDelivery\":false},{\"id\":\"3\",\"template_id\":\"1\",\"name\":\"checkbox\",\"description\":\"\",\"ordernumber\":\"\",\"required\":\"0\",\"type\":\"checkbox\",\"position\":\"1\",\"default_value\":\"\",\"placeholder\":\"\",\"is_once_surcharge\":\"0\",\"max_text_length\":null,\"min_value\":null,\"max_value\":null,\"max_file_size\":\"3145728\",\"min_date\":null,\"max_date\":null,\"max_files\":\"1\",\"interval\":null,\"could_contain_values\":\"1\",\"allows_multiple_selection\":\"0\",\"prices\":[{\"id\":\"5\",\"option_id\":\"3\",\"value_id\":null,\"surcharge\":\"8.4033613445378\",\"percentage\":\"0\",\"is_percentage_surcharge\":\"0\",\"tax_id\":\"1\",\"customer_group_name\":\"Shopkunden\",\"customer_group_id\":\"1\"}],\"netPrice\":8.4033613445378,\"surcharge\":8.4033613445378,\"tax_id\":\"1\",\"tax\":1.6,\"isTaxFreeDelivery\":false,\"values\":[{\"id\":\"1\",\"option_id\":\"3\",\"name\":\"value1\",\"ordernumber\":\"value1\",\"value\":\"\",\"is_default_value\":\"0\",\"position\":\"0\",\"is_once_surcharge\":\"0\",\"media_id\":null,\"seo_title\":null,\"prices\":[{\"id\":\"3\",\"option_id\":null,\"value_id\":\"1\",\"surcharge\":\"16.806722689076\",\"percentage\":\"0\",\"is_percentage_surcharge\":\"0\",\"tax_id\":\"1\",\"customer_group_name\":\"Shopkunden\",\"customer_group_id\":\"1\"}],\"netPrice\":16.806722689076,\"surcharge\":16.806722689076,\"tax_id\":\"1\",\"tax\":3.19,\"isTaxFreeDelivery\":false,\"image\":null},{\"id\":\"2\",\"option_id\":\"3\",\"name\":\"value2\",\"ordernumber\":\"value2\",\"value\":\"\",\"is_default_value\":\"0\",\"position\":\"1\",\"is_once_surcharge\":\"0\",\"media_id\":null,\"seo_title\":null,\"prices\":[{\"id\":\"4\",\"option_id\":null,\"value_id\":\"2\",\"surcharge\":\"16.806722689076\",\"percentage\":\"0\",\"is_percentage_surcharge\":\"0\",\"tax_id\":\"1\",\"customer_group_name\":\"Shopkunden\",\"customer_group_id\":\"1\"}],\"netPrice\":16.806722689076,\"surcharge\":16.806722689076,\"tax_id\":\"1\",\"tax\":3.19,\"isTaxFreeDelivery\":false,\"image\":null}]}]', '2017-06-16 07:33:38');

INSERT INTO `s_order_basket` (`id`, `sessionID`, `userID`, `articlename`, `articleID`, `ordernumber`, `shippingfree`, `quantity`, `price`, `netprice`, `tax_rate`, `datum`, `modus`, `esdarticle`, `partnerID`, `lastviewport`, `useragent`, `config`, `currencyFactor`) VALUES
  (723, 'session_id', 0, 'Strandtuch \"Ibiza\" ', 178, 'SW10178', 0, 1, 16.765, 16.77, 19, '2017-06-16 07:34:11', 0, 0, '', 'checkout', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36', '', 1),
  (724, 'session_id', 0, 'text1', 2, '', 0, 1, 8.4033613445378, 8.4033613445378, 19, '2017-06-16 07:34:11', 4, 0, '', 'checkout', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36', '', 1),
  (725, 'session_id', 0, 'checkbox', 3, '', 0, 1, 8.4033613445378, 8.4033613445378, 19, '2017-06-16 07:34:11', 4, 0, '', 'checkout', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36', '', 1),
  (726, 'session_id', 0, 'value1', 1, 'value1', 0, 1, 16.806722689076, 16.806722689076, 19, '2017-06-16 07:34:11', 4, 0, '', 'checkout', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36', '', 1),
  (727, 'session_id', 0, 'value2', 2, 'value2', 0, 1, 16.806722689076, 16.806722689076, 19, '2017-06-16 07:34:11', 4, 0, '', 'checkout', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36', '', 1),
  (729, 'session_id', 0, 'Warenkorbrabatt', 0, 'SHIPPINGDISCOUNT', 0, 1, -2, -2, 19, '2017-06-16 07:34:20', 4, 0, '', 'checkout', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36', '', 1);

INSERT INTO `s_order_basket_attributes` (`id`, `basketID`, `attribute1`, `attribute2`, `attribute3`, `attribute4`, `attribute5`, `attribute6`, `swag_custom_products_configuration_hash`, `swag_custom_products_once_price`, `swag_custom_products_mode`, `swag_promotion_id`, `swag_is_free_good_by_promotion_id`) VALUES
  (45, 723, NULL, NULL, NULL, NULL, NULL, NULL, 'bb06fe423df634e3106415c3766204b8', 0, 1, NULL, NULL),
  (46, 724, NULL, NULL, NULL, NULL, NULL, NULL, 'bb06fe423df634e3106415c3766204b8', 0, 2, NULL, NULL),
  (47, 725, NULL, NULL, NULL, NULL, NULL, NULL, 'bb06fe423df634e3106415c3766204b8', 0, 2, NULL, NULL),
  (48, 726, NULL, NULL, NULL, NULL, NULL, NULL, 'bb06fe423df634e3106415c3766204b8', 0, 3, NULL, NULL),
  (49, 727, NULL, NULL, NULL, NULL, NULL, NULL, 'bb06fe423df634e3106415c3766204b8', 0, 3, NULL, NULL);