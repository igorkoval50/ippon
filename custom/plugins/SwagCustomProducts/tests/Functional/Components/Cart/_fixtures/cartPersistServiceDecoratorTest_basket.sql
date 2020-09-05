INSERT INTO `s_order_basket` (`id`, `sessionID`, `userID`, `articlename`, `articleID`, `ordernumber`, `shippingfree`, `quantity`, `price`, `netprice`, `tax_rate`, `datum`, `modus`, `esdarticle`, `partnerID`, `lastviewport`, `useragent`, `config`, `currencyFactor`) VALUES
(726, 'phpUnitFooBarSessionId', 1, 'Strandtuch \"Ibiza\"', 178, 'SW10178', 0, 1, 19.95, 16.764705882353, 19, '2020-02-14 11:40:50', 0, 0, '', 'account', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.100 Safari/537.36', '', 1),
(727, 'phpUnitFooBarSessionId', 1, 'Farbauswahl', 4, 'Farbauswahl', 0, 1, 0, 0, 19, '2020-02-14 11:40:50', 4, 0, '', 'account', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.100 Safari/537.36', '', 1),
(728, 'phpUnitFooBarSessionId', 1, '#B0AF00', 11, '#B0AF00', 0, 1, 2.9999999999999, 2.5210084033613, 19, '2020-02-14 11:40:50', 4, 0, '', 'account', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.100 Safari/537.36', '', 1),
(729, 'phpUnitFooBarSessionId', 1, 'Bildauswahl', 5, 'Bildauswahl', 0, 1, 0, 0, 19, '2020-02-14 11:40:50', 4, 0, '', 'account', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.100 Safari/537.36', '', 1),
(730, 'phpUnitFooBarSessionId', 1, 'B1', 12, '', 0, 1, 0, 0, 19, '2020-02-14 11:40:50', 4, 0, '', 'account', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.100 Safari/537.36', '', 1);

INSERT INTO `s_order_basket_attributes` (`id`, `basketID`, `attribute1`, `attribute2`, `attribute3`, `attribute4`, `attribute5`, `attribute6`, `swag_custom_products_configuration_hash`, `swag_custom_products_once_price`, `swag_custom_products_mode`) VALUES
(68, 726, NULL, NULL, NULL, NULL, NULL, NULL, 'bce4f1887867bd063dca33341683a8f7', 0, 1),
(69, 727, NULL, NULL, NULL, NULL, NULL, NULL, 'bce4f1887867bd063dca33341683a8f7', 0, 2),
(70, 728, NULL, NULL, NULL, NULL, NULL, NULL, 'bce4f1887867bd063dca33341683a8f7', 0, 3),
(71, 729, NULL, NULL, NULL, NULL, NULL, NULL, 'bce4f1887867bd063dca33341683a8f7', 0, 2),
(72, 730, NULL, NULL, NULL, NULL, NULL, NULL, 'bce4f1887867bd063dca33341683a8f7', 0, 3);
