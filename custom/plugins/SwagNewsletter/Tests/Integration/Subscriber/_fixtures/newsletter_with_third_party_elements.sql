INSERT INTO `s_campaigns_mailings`
(`id`, `datum`, `groups`, `subject`, `sendermail`, `sendername`, `plaintext`, `templateID`, `languageID`, `status`, `locked`, `recipients`, `read`, `clicked`, `customergroup`, `publish`, `timed_delivery`)
VALUES
  (:newsletterId, '2017-05-23', 'a:2:{i:0;a:0:{}i:1;a:0:{}}', 'FooBar4', 'info@example.com', 'Newsletter Absender', 0, 1, 1, 0, NULL, 0, 0, 0, 'EK', 0, NULL);

INSERT INTO `s_campaigns_containers` (`id`, `promotionID`, `value`, `type`, `description`, `position`) VALUES
  (:articleElementId, :newsletterId, '', 'ctArticles', 'Article Group Test', 1),
  (:bannerElementId, :newsletterId, '', 'ctBanner', 'Test banner', 2);

INSERT INTO `s_campaigns_banner` (`parentID`, `image`, `link`, `linkTarget`, `description`) VALUES
  (:bannerElementId, 'media/image/Store.jpg', 'test_link', '_blank', 'Test banner');

INSERT INTO `s_campaigns_component` (`id`, `name`, `x_type`, `convert_function`, `description`, `template`, `cls`, `pluginID`) VALUES
  (:liveShoppingCmpId, 'Live-Shopping', 'newsletter-components-test-shopping', NULL, '', 'test', 'newsletter-test-element', NULL);

INSERT INTO `s_plugin_swag_newsletter_element` (`id`, `newsletterID`, `componentID`, `start_row`, `start_col`, `end_row`, `end_col`) VALUES
  (2505, :newsletterId, :liveShoppingCmpId, 3, 1, 3, 1);

INSERT INTO `s_plugin_swag_newsletter_element_value` (`newsletterID`, `elementID`, `componentID`, `fieldID`, `value`) VALUES
  (:newsletterId, 2505, :liveShoppingCmpId, 20, 'TestLive'),
  (:newsletterId, 2505, :liveShoppingCmpId, 21, '1'),
  (:newsletterId, 2505, :liveShoppingCmpId, 22, '[{\"position\":0,\"type\":\"fix\",\"articleId\":\"178\",\"name\":\"Strandtuch \\\"Ibiza\\\"\"}]');

INSERT INTO `s_campaigns_component_field` (`id`, `componentID`, `name`, `x_type`, `value_type`, `field_label`, `support_text`, `help_title`, `help_text`, `store`, `display_field`, `value_field`, `default_value`, `allow_blank`) VALUES
  (22, :liveShoppingCmpId, 'article_data', 'hiddenfield', 'json', '', '', '', '', '', '', '', '2', 0),
  (21, :liveShoppingCmpId, 'number', 'numberfield', '', 'Anzahl der Liveshopping Artikel', '', '', '', '', '', '', '', 0),
  (20, :liveShoppingCmpId, 'headline', 'textfield', '', 'Überschrift', '', '', '', '', '', '', '', 0);