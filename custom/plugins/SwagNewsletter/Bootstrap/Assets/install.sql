CREATE TABLE IF NOT EXISTS `s_campaigns_component` (
  `id`                INT(11)       NOT NULL  AUTO_INCREMENT ,
  `name`              VARCHAR(255)  NOT NULL,
  `x_type`            VARCHAR(255) NOT NULL,
  `convert_function`  VARCHAR(255)  DEFAULT NULL,
  `description`       TEXT          NOT NULL,
  `template`          VARCHAR(255)  NOT NULL,
  `cls`               VARCHAR(255)  NOT NULL,
  `pluginID`          INT(11)       DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `s_campaigns_component_field` (
  `id`            INT(11)       NOT NULL  AUTO_INCREMENT,
  `componentID`   INT(11)       NOT NULL,
  `name`          VARCHAR(255)  NOT NULL,
  `x_type`        VARCHAR(255)  NOT NULL,
  `value_type`    VARCHAR(255)  NOT NULL,
  `field_label`   VARCHAR(255)  NOT NULL,
  `support_text`  VARCHAR(255)  NOT NULL,
  `help_title`    VARCHAR(255)  NOT NULL,
  `help_text`     TEXT          NOT NULL,
  `store`         VARCHAR(255)  NOT NULL,
  `display_field` VARCHAR(255)  NOT NULL,
  `value_field`   VARCHAR(255)  NOT NULL,
  `default_value` VARCHAR(255)  NOT NULL,
  `allow_blank`   INT(1)        NOT NULL,
  PRIMARY  KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `s_campaigns_component` (`id`, `name`, `x_type`, `convert_function`, `description`, `template`, `cls`, `pluginID`) VALUES
(1, 'HTML-Element', 'newsletter-components-text', NULL, '', 'component_html', 'newsletter-html-text-element', NULL),
(2, 'Banner', '', NULL, '', 'component_banner', 'newsletter-banner-element', NULL),
(3, 'Artikel-Gruppe', 'newsletter-components-article', 'getArticle', '', 'component_article', 'newsletter-article-element', NULL),
(4, 'Link', 'newsletter-components-links', 'getLinks', '', 'component_link', 'newsletter-link-element', NULL),
(5, 'Gutschein', 'newsletter-components-text', 'getVoucher', '', 'component_voucher', 'newsletter-voucher-element', NULL),
(6, 'Suggest', '', 'getSuggest', '', 'component_suggest', 'newsletter-suggest-element', NULL);

INSERT IGNORE INTO `s_campaigns_component_field` (`id`, `componentID`, `name`, `x_type`, `value_type`, `field_label`, `support_text`, `help_title`, `help_text`, `store`, `display_field`, `value_field`, `default_value`, `allow_blank`) VALUES
(1, 3, 'headline', 'textfield', '', 'Überschrift', 'Dieses Feld kann leer gelassen werden, wenn keine Überschrift erwünscht ist.', '', '', '', '', '', '', 1),
(2, 3, 'article_data', 'hidden', 'json', '', '', '', '', '', '', '', '', 0),
(3, 1, 'headline', 'textfield', '', 'Überschrift', '', '', '', '', '', '', '', 0),
(4, 1, 'text', 'tinymce', '', 'Text', 'Anzuzeigender Text', 'HTML-Text', 'Geben Sie hier den Text ein, der in dem Element angezeigt werden soll.', '', '', '', '', 0),
(5, 1, 'image', 'mediaselectionfield', '', 'Bild', '', '', '', '', '', '', '', 1),
(6, 1, 'url', 'textfield', '', 'Direktlink', '', '', '', '', '', '', '', 1),
(7, 2, 'description', 'textfield', '', 'Überschrift', '', '', '', '', '', '', '', 1),
(8, 2, 'file', 'mediaselectionfield', '', 'Bild', '', '', '', '', '', '', '', 0),
(9, 2, 'link', 'textfield', '', 'Link', '', '', '', '', '', '', '', 0),
(10, 2, 'target_selection', 'newsletter-components-fields-target-selection', '', 'Link-Ziel', 'Soll sich der Link im Shopware-Fenster oder einem neuen Fenster öffnen?', '', '', '', '', '', '', 0),
(11, 4, 'link_data', 'hidden', 'json', '', '', '', '', '', '', '', '', 0),
(12, 4, 'description', 'textfield', '', 'Überschrift', '', '', '', '', '', '', '', 1),
(13, 5, 'headline', 'textfield', '', 'Überschrift', '', '', '', '', '', '', '', 0),
(14, 5, 'voucher_selection', 'newsletter-components-fields-voucher-selection', '', 'Gutschein', '', '', '', '', '', '', '', 0),
(15, 5, 'text', 'tinymce', '', 'Text', 'Anzuzeigender Text', 'HTML-Text', 'Geben Sie hier den Text ein, der in dem Element angezeigt werden soll.', '', '', '', 'Gutscheincode: {$sVoucher.code}', 0),
(16, 5, 'image', 'mediaselectionfield', '', 'Bild', '', '', '', '', '', '', '', 1),
(17, 5, 'url', 'textfield', '', 'Direktlink', '', '', '', '', '', '', '', 1),
(18, 6, 'headline', 'textfield', '', 'Überschrift', '', '', '', '', '', '', '', 1),
(19, 6, 'number', 'newsletter-components-fields-numberfield', '', 'Anzahl der vorgeschlagenen Artikel', '', '', '', '', '', '', 3, 0);

CREATE TABLE IF NOT EXISTS `s_plugin_swag_newsletter_element` (
  `id`            INT(11) NOT NULL  AUTO_INCREMENT,
  `newsletterID`  INT(11) NOT NULL,
  `componentID`   INT(11) NOT NULL,
  `start_row`     INT(11) NOT NULL,
  `start_col`     INT(11) NOT NULL,
  `end_row`       INT(11) NOT NULL,
  `end_col`       INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `s_plugin_swag_newsletter_element_value` (
  `id`            INT(11) NOT NULL  AUTO_INCREMENT,
  `newsletterID`  INT(11) NOT NULL,
  `elementID`     INT(11) NOT NULL,
  `componentID`   INT(11) NOT NULL,
  `fieldID`       INT(11) NOT NULL,
  `value`         TEXT,
  PRIMARY KEY (`id`),
  KEY `emotionID` (`elementID`),
  KEY `fieldID` (`fieldID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
