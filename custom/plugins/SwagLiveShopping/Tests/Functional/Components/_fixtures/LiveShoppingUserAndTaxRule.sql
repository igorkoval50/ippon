INSERT INTO `s_core_countries_states` (`id`, `countryID`, `name`, `shortcode`, `position`, `active`) VALUES
(70, 23, 'PHPUnitTest', 'FB', NULL, 1);

INSERT INTO `s_core_tax_rules` (`id`, `areaID`, `countryID`, `stateID`, `groupID`, `customer_groupID`, `tax`, `name`, `active`) VALUES
(3, 3, 23, 70, 4, 1, '10.00', 'PHPUnitTest', 1);

INSERT INTO `s_user` (`id`, `password`, `encoder`, `email`, `active`, `accountmode`, `confirmationkey`, `paymentID`, `doubleOptinRegister`, `doubleOptinEmailSentDate`, `doubleOptinConfirmDate`, `firstlogin`, `lastlogin`, `sessionID`, `newsletter`, `validation`, `affiliate`, `customergroup`, `paymentpreset`, `language`, `subshopID`, `referer`, `pricegroupID`, `internalcomment`, `failedlogins`, `lockeduntil`, `default_billing_address_id`, `default_shipping_address_id`, `title`, `salutation`, `firstname`, `lastname`, `birthday`, `customernumber`, `login_token`, `changed`, `password_change_date`, `register_opt_in_id`) VALUES
(5, '$2y$10$rLnUR.8wNQFVnapW6Rw6KeZqmicNR6torejhKkikeqLT6vljXYzXi', 'bcrypt', 'foo@bar.at', 1, 0, '', 7, 0, NULL, NULL, '2020-01-10', '2020-01-13 13:06:52', 'PHPUnitTestSessionId', 0, '', 0, 'EK', 0, '1', 1, '', NULL, '', 0, NULL, 7, 7, NULL, 'mr', 'Foo', 'Bar', NULL, '20007', '5d973e9e-60d4-45f0-90e3-f96a674fe7a7.1', '2020-01-10 11:19:20', '2020-01-10 11:19:19', NULL);

INSERT INTO `s_user_addresses` (`id`, `user_id`, `company`, `department`, `salutation`, `title`, `firstname`, `lastname`, `street`, `zipcode`, `city`, `country_id`, `state_id`, `ustid`, `phone`, `additional_address_line1`, `additional_address_line2`) VALUES
(7, 5, NULL, NULL, 'mr', NULL, 'Foo', 'Bar', 'FooBar 22', '12345', 'TEST', 23, NULL, NULL, NULL, NULL, NULL);
