INSERT INTO `s_campaigns_mailings`
(`id`, `datum`, `groups`, `subject`, `sendermail`, `sendername`, `plaintext`, `templateID`, `languageID`, `status`, `locked`, `recipients`, `read`, `clicked`, `customergroup`, `publish`, `timed_delivery`)
VALUES
(:newsletterId, '2017-05-23', 'a:2:{i:0;a:0:{}i:1;a:0:{}}', 'FooBar2', 'info@example.com', 'Newsletter Absender', 0, 1, 1, 0, NULL, 0, 0, 0, 'EK', 0, NULL);

INSERT INTO `s_campaigns_containers` (`id`, `promotionID`, `value`, `type`, `description`, `position`) VALUES
(:articleElementId, :newsletterId, '', 'ctArticles', 'Article Group Test', 1),
(:bannerElementId, :newsletterId, '', 'ctBanner', 'Test banner', 2);

INSERT INTO `s_campaigns_banner` (`parentID`, `image`, `link`, `linkTarget`, `description`) VALUES
(:bannerElementId, 'media/image/Store.jpg', 'test_link', '_blank', 'Test banner');