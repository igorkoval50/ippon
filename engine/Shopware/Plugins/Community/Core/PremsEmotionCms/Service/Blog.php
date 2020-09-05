<?php
/**
 * premsoft
 * Copyright © 2017 Premsoft - Sven Mittreiter
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
 * @copyright  Copyright (c) 2015, premsoft - Sven Mittreiter (http://www.premsoft.de)
 * @author     Sven Mittreiter <info@premsoft.de>
 */
namespace Shopware\PremsEmotionCms\Service;

class Blog {
  /**
   * @var \Enlight_Components_Db_Adapter_Pdo_Mysql $db
   */
  protected $db;

  /**
   * @var \Shopware\Models\Shop\DetachedShop $shop
   */
  protected $shop;

  /** @var \Shopware\PremsEmotionCms\Service\Caching $cachingService */
  protected $cachingService;

  /** @var \Shopware\PremsEmotionCms\Service\EmotionConverter $emotionConverterService */
  protected $emotionConverterService;

  /**
   * @param \Enlight_Components_Db_Adapter_Pdo_Mysql $bootstrap
   * @param \Shopware\Models\Shop\DetachedShop $shop;
   */
  public function __construct(\Enlight_Components_Db_Adapter_Pdo_Mysql $db, $shop, $cachingService, $emotionConverterService) {
    $this->db = $db;
    $this->shop = $shop;
    $this->cachingService = $cachingService;
    $this->emotionConverterService = $emotionConverterService;
  }

  protected function getEmotionsFromDb($id) {
    $sql = '
        SELECT
          *
        FROM
          s_prems_emotion_cms_blog
        WHERE
          blog_id = ?
        AND (shop_id = 0 OR shop_id = ?)
      ';

    $emotions = Shopware()->Db()->fetchAll(
      $sql,
      array(
        $id,
        $this->shop->getId()
      )
    );

    return $emotions;
  }

  /**
   * @param $id
   * @return array|boolean
   */
  public function getEmotions($id) {
    $cache = $this->cachingService->getCache($id);

    if ($cache) {
      $emotions = $cache->load();
    } else {
      $emotions = $this->getEmotionsFromDb($id);
      if (!$emotions) {
        return false;
      }

      $this->emotionConverterService->convertEmotions($emotions);
      $this->cachingService->save($id, $emotions);
    }

    return $emotions;
  }
}