<?php
/**
 * premsoft
 * Copyright © 2018 Premsoft - Sven Mittreiter
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License, supplemented by an additional
 * permission, and of our proprietary license can be found
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, titles and interests in the
 * above trademarks remain entirely with the trademark owners.
 *
 * @copyright  Copyright (c) 2018, premsoft - Sven Mittreiter (http://www.premsoft.de)
 * @author     Sven Mittreiter <info@premsoft.de>
 */
namespace Shopware\PremsEmotionCms\Config;

use Shopware_Plugins_Core_PremsEmotionCms_Bootstrap as Bootstrap;

class Update {
  /**
   * @var Bootstrap $bootstrap
   */
  protected $bootstrap;

  /**
   * @param Bootstrap $bootstrap
   */
  public function __construct(Bootstrap $bootstrap) {
    $this->bootstrap = $bootstrap;
  }

  public function execute($version) {
    if (version_compare($version, '1.2.0', '<')) {
      $parentId = $this->bootstrap->Menu()->findOneBy(array('label' => 'Einkaufswelten CMS'));
      $menu = new \Shopware\PremsEmotionCms\Config\Menu($this->bootstrap);
      $menu->createArticle2CmsMenu($parentId);

      Shopware()->Loader()->registerNamespace(
        'Shopware\CustomModels',
        $this->bootstrap->Path() . 'Models/'
      );

      Shopware()->Db()->exec('
        CREATE TABLE IF NOT EXISTS `s_prems_emotion_cms_article2` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          `position` int(11) NOT NULL,
          `before_default` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
      ');

      Shopware()->Db()->exec('
        CREATE TABLE IF NOT EXISTS `s_prems_emotion_cms_article2_articles` (
          `main_id` int(11) NOT NULL,
          `article_id` int(11) NOT NULL,
          PRIMARY KEY (`main_id`,`article_id`),
          KEY `IDX_EA1C6FE9627EA78A` (`main_id`),
          KEY `IDX_EA1C6FE97294869C` (`article_id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;      
      ');

      Shopware()->Db()->exec('
        CREATE TABLE IF NOT EXISTS `s_prems_emotion_cms_article2_emotions` (
          `main_id` int(11) NOT NULL,
          `emotion_id` int(11) NOT NULL,
          PRIMARY KEY (`main_id`,`emotion_id`),
          KEY `IDX_80AEABA9627EA78A` (`main_id`),
          KEY `IDX_80AEABA91EE4A582` (`emotion_id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
      ');

      $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
      $metaDataCache->deleteAll();
    }
  }

}