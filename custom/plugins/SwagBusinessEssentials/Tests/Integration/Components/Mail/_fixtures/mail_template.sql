INSERT INTO `s_core_plugins_b2b_cgsettings`(
  `customergroup`,
  `allowregister`,
  `requireunlock`,
  `assigngroupbeforeunlock`,
  `registertemplate`,
  `emailtemplatedeny`,
  `emailtemplateallow`
) VALUES (
  'EK',
  1,
  0,
  'EK',
  'login.tpl',
  'sEMAIL_DENY_TEST',
  'sEMAIL_ALLOW_TEST'
);