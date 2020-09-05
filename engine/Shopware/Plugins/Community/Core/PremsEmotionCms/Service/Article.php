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

class Article {
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

  /**
   * Liest die Einkaufswelten, welcher beim Artikel Tab zugewiesen wurden
   * @param $id
   * @return array
   */
  protected function getEmotionsFromDb($id) {
    $sql = '
        SELECT
          *
        FROM
          s_prems_emotion_cms_article
        WHERE
          article_id = ?
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
   * Liest die Einkaufswelten welche im CMS Bereich zugewiesen wurden
   * @param $id
   * @return array
   */
  protected function getEmotions2FromDb($id) {
    $sql = '
      SELECT
        main.id, main.position, main.before_default, emotions.*
      FROM
        s_prems_emotion_cms_article2_articles AS articles
      LEFT JOIN
        s_prems_emotion_cms_article2 AS main
      ON
        articles.main_id = main.id
      LEFT JOIN
        s_prems_emotion_cms_article2_emotions AS emotions
      ON
        emotions.main_id = main.id
      WHERE
        articles.article_id = ?
     ';

    $emotions = Shopware()->Db()->fetchAll(
      $sql,
      array(
        $id
      )
    );

    return $emotions;
  }

  /**
   * Merged die zugewiesenen Einkaufswelten für einen Artikel. Es werden die Einkaufswelten aus der Tab Zuordnung und
   * vom CMS Menüpunkt zusammen geführt.
   * @param $emotions
   * @param $emotions2
   * @return array
   */
  protected function mergeEmotions($emotions, $emotions2) {
    if (!$emotions) {
      return ($emotions2);
    }

    if (!$emotions2) {
      return($emotions);
    }

    if ($emotions && $emotions2) {
      foreach($emotions2 as $row) {
        if ($row['before_default']) {
          array_push($emotions, $row);
        } else {
          array_unshift($emotions, $row);
        }
      }
    }

    return $emotions;
  }

  /**
   * @param $id
   * @return array|boolean
   */
  public function getEmotions($id) {
    $cache = $this->cachingService->getCache($id);

    if ($cache) {
      $emotionsMerged = $cache->load();
    } else {
      $emotions2 = $this->getEmotions2FromDb($id);
      $emotions = $this->getEmotionsFromDb($id);
      if (!$emotions && !$emotions2) {
        return false;
      }
      $emotionsMerged = $this->mergeEmotions($emotions, $emotions2);

      $this->emotionConverterService->convertEmotions($emotionsMerged);
      $this->cachingService->save($id, $emotionsMerged);
    }

    return $emotionsMerged;
  }
}