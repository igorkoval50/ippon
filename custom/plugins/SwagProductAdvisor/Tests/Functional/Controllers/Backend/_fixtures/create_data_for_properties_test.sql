INSERT INTO `s_product_streams` (`id`, `name`, `conditions`, `type`, `sorting`, `description`, `sorting_id`) VALUES
(42, 'Default Stream', '{\"Shopware\\\\Bundle\\\\SearchBundle\\\\Condition\\\\CategoryCondition\":{\"categoryIds\":[3]}}', 1, '\"{\\\"Shopware\\\\\\\\Bundle\\\\\\\\SearchBundle\\\\\\\\Sorting\\\\\\\\PriceSorting\\\":{\\\"direction\\\":\\\"ASC\\\"}}\"', '', 3);

INSERT INTO `s_plugin_product_advisor_advisor` (`id`, `stream_id`, `teaser_banner_id`, `active`, `name`, `description`, `info_link_text`, `button_text`, `remaining_posts_title`, `listing_title_filtered`, `highlight_top_hit`, `top_hit_title`, `min_matching_attributes`, `listing_layout`, `mode`, `last_listing_sort`) VALUES
(42, 42, 696, 0, 'Default advisor', 'Sample description', 'Info', 'Search', 'Products left', 'Title', 0, 'Top match', 0, 'show_matches_and_misses', 'sidebar_mode', 'ASC');

INSERT INTO `s_articles_avoid_customergroups` (`articleID`, `customergroupID`) VALUES
(272, 1);

INSERT INTO `s_filter` (`id`, `name`, `position`, `comparable`, `sortmode`) VALUES
(2, 'PhpUnit_filter', 0, 1, 0);

INSERT INTO `s_filter_options` (`id`, `name`, `filterable`) VALUES
(8, 'PhpUnit_option', 1);

INSERT INTO `s_filter_relations` (`id`, `groupID`, `optionID`, `position`) VALUES
(6, 2, 8, 0);

INSERT INTO `s_filter_values` (`id`, `optionID`, `value`, `position`, `media_id`) VALUES
(42, 8, 'PhpUnit_foo', 0, NULL),
(43, 8, 'PhpUnit_bar', 1, NULL),
(44, 8, 'PhpUnit_foo_bar', 2, NULL),
(45, 8, 'PhpUnit_bar_foo', 3, NULL);
