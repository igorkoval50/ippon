INSERT INTO `s_articles_bundles` (`id`, `articleID`, `name`, `show_name`, `active`, `description`, `rab_type`, `taxID`, `ordernumber`, `max_quantity_enable`, `display_global`, `display_delivery`, `max_quantity`, `valid_from`, `valid_to`, `datum`, `sells`, `bundle_type`, `bundle_position`) VALUES
(222, 153, 'New Bundle', 0, 1, '', 'pro', NULL, '08154711', 0, 0, 1, 0, NULL, NULL, '2020-03-17 08:56:20', 0, 2, 0);


INSERT INTO `s_articles_bundles_articles` (`id`, `bundle_id`, `article_detail_id`, `quantity`, `configurable`, `bundle_group_id`, `position`) VALUES
(301, 222, 409, 1, 1, NULL, 1);


INSERT INTO `s_articles_bundles_customergroups` (`id`, `bundle_id`, `customer_group_id`) VALUES
(601, 222, 1);


INSERT INTO `s_articles_bundles_prices` (`id`, `bundle_id`, `customer_group_id`, `price`) VALUES
(202, 222, '1', 10);


UPDATE `s_core_translations` SET objectdata = 'a:3:{s:10:\"txtArtikel\";s:39:\"Flip Flops, available in several colors\";s:19:\"txtlangbeschreibung\";s:407:\"<p>Ita si ardor laxe eu Cogo arx subaudio. Os, color attero, an. Hi inservio quatenus/quatinus, eu. Pax percunctor, ut. Me Mica. Ne, in plus neo pars ne navale quo Heniis varius, St. Iam vae ne inter Idem devenio parum accredo Neco nam byssus ales an Seu re is, mors vir incrementabiliter hio desisto Noxa chirographum eo, os, agon os nec lac enucleo his ile pes vos Suppellex, re. Vae libro corium Pul.</p>\";s:12:\"txtzusatztxt\";s:8:\"Blue ONE\";}'
WHERE id = 205;


INSERT INTO `s_core_translations` (`id`, `objecttype`, `objectdata`, `objectkey`, `objectlanguage`, `dirty`) VALUES
(285, 'article', 'a:1:{s:12:\"txtzusatztxt\";s:8:\"Blue TWO\";}', 179, '2', 1);
