INSERT INTO `s_campaigns_mailings`
(`id`, `datum`, `groups`, `subject`, `sendermail`, `sendername`, `plaintext`, `templateID`, `languageID`, `status`, `locked`, `recipients`, `read`, `clicked`, `customergroup`, `publish`, `timed_delivery`)
VALUES
(:newsletterId, '2017-05-23', 'a:2:{i:0;a:0:{}i:1;a:0:{}}', 'FooBar', 'info@example.com', 'Newsletter Absender', 0, 1, 1, 0, NULL, 0, 0, 0, 'EK', 0, NULL);