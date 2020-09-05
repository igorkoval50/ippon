
CREATE TABLE IF NOT EXISTS `s_plugin_custom_products_template` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `internal_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 `description` longtext COLLATE utf8_unicode_ci,
 `media_id` int(11) DEFAULT NULL,
 `step_by_step_configurator` tinyint(1) NOT NULL,
 `active` tinyint(1) NOT NULL,
 `confirm_input` tinyint(1) NOT NULL,
 `variants_on_top` tinyint(1) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `UNIQ_SPCPT_internal_name` (`internal_name`),
 KEY `IDX_SPCPT_media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `s_plugin_custom_products_configuration_hash` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `configuration` longtext COLLATE utf8_unicode_ci NOT NULL,
 `permanent` tinyint(1) NOT NULL,
 `template` longtext COLLATE utf8_unicode_ci NOT NULL,
 `created_at` datetime NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `UNIQ_SPCPCH_hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `s_plugin_custom_products_option` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `template_id` int(11) NOT NULL,
 `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `description` longtext COLLATE utf8_unicode_ci,
 `ordernumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 `required` tinyint(1) DEFAULT NULL,
 `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `position` int(11) DEFAULT NULL,
 `default_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 `placeholder` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 `is_once_surcharge` tinyint(1) DEFAULT NULL,
 `max_text_length` int(11) DEFAULT NULL,
 `min_value` int(11) DEFAULT NULL,
 `max_value` int(11) DEFAULT NULL,
 `max_file_size` int(11) DEFAULT NULL,
 `min_date` datetime DEFAULT NULL,
 `max_date` datetime DEFAULT NULL,
 `max_files` int(11) DEFAULT NULL,
 `interval` double DEFAULT NULL,
 `could_contain_values` tinyint(1) NOT NULL,
 `allows_multiple_selection` tinyint(1) DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `IDX_SPCPO_template_id` (`template_id`),
 CONSTRAINT `FK_SPCPO_template_id` FOREIGN KEY (`template_id`) REFERENCES `s_plugin_custom_products_template` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `s_plugin_custom_products_value` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `option_id` int(11) NOT NULL,
 `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `ordernumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 `value` longtext COLLATE utf8_unicode_ci NOT NULL,
 `is_default_value` tinyint(1) DEFAULT NULL,
 `position` int(11) DEFAULT NULL,
 `is_once_surcharge` tinyint(1) DEFAULT NULL,
 `media_id` int(11) DEFAULT NULL,
 `seo_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `IDX_SPCPV_option_id` (`option_id`),
 CONSTRAINT `FK_SPCPV_option_id` FOREIGN KEY (`option_id`) REFERENCES `s_plugin_custom_products_option` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `s_plugin_custom_products_price` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `option_id` int(11) DEFAULT NULL,
 `value_id` int(11) DEFAULT NULL,
 `surcharge` double DEFAULT NULL,
 `percentage` double DEFAULT NULL,
 `is_percentage_surcharge` tinyint(1) DEFAULT NULL,
 `tax_id` int(11) NOT NULL,
 `customer_group_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `customer_group_id` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `IDX_SPCPP_option_id` (`option_id`),
 KEY `IDX_SPCPP_value_id` (`value_id`),
 KEY `search_idx` (`tax_id`,`option_id`,`value_id`),
 CONSTRAINT `FK_SPCPP_option_id` FOREIGN KEY (`option_id`) REFERENCES `s_plugin_custom_products_option` (`id`),
 CONSTRAINT `FK_SPCPP_value_id` FOREIGN KEY (`value_id`) REFERENCES `s_plugin_custom_products_value` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `s_plugin_custom_products_template_product_relation` (
 `template_id` int(11) NOT NULL,
 `article_id` int(11) NOT NULL,
 PRIMARY KEY (`template_id`,`article_id`),
 UNIQUE KEY `UNIQ_SPCPTPR_article_id` (`article_id`),
 KEY `IDX_SPCPTPR_template_id` (`template_id`),
 CONSTRAINT `FK_SPCPTPR_template_id` FOREIGN KEY (`template_id`) REFERENCES `s_plugin_custom_products_template` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
