<?php
/**
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
namespace Shopware\PremsEmotionCms\Service;

use Shopware_Plugins_Core_PremsEmotionCms_Bootstrap as Bootstrap;

class ArticleBatchData
{
  /**
   * @var Bootstrap $bootstrap
   */
  protected $bootstrap;

  /**
   * BatchData constructor.
   * @param Bootstrap $bootstrap
   */
  function __construct(Bootstrap $bootstrap) {
    $this->bootstrap = $bootstrap;
  }

  protected function createStatementSql($params) {
    /**$test = '
      SELECT
        a.id as articleId
      FROM
        s_articles AS a, s_articles_categories_ro AS c
      WHERE
        active = 1
      AND 
        a.id = c.articleID
      AND 
        c.categoryID IN '.$params['categories'].'
      GROUP BY 
        articleId
    ';*/

    $joinTables = null;
    $joinWhere = null;

    $categories = null;
    if ($params['categories'] && strlen($params['categories']) > 2) {
      $categories = str_replace('[', '(', $params['categories']);
      $categories = str_replace(']', ')', $categories);
    }

    if ($categories) {
      $joinTables .= ', s_articles_categories_ro AS c';
      $joinWhere .= '
        AND 
          a.id = c.articleID
        AND 
          c.categoryID IN '.$categories.'
      ';
    }

    $sql = '
      SELECT
        a.id as articleId
      FROM
        s_articles AS a '.$joinTables.'
      WHERE
        active = 1 '.$joinWhere.'
      GROUP BY 
        articleId        
    ';

    return $sql;
  }

  protected function createStatementParams($params) {
    $statementParams = array();

    /**if (array_key_exists('limit', $params)) {
    $statementParams['limit'] = $params['limit'];
    }*/
    return $statementParams;
  }

  public function getRecords($params, $limit, $offset)
  {
    $params['limit'] = $offset.', '.$limit;
    $statementSql = $this->createStatementSql($params);
    $statementParams = $this->createStatementParams($params);

    $statementSql .= ' LIMIT '.$params['limit'];

    try {
      $records = Shopware()->Db()->fetchAll($statementSql, $statementParams);
    } catch(\Exception $e)  {
      echo $e->getMessage();
    }

    return $records;
  }

  public function getRecordIds($params)
  {
    $statementSql = $this->createStatementSql($params);
    $statementParams = $this->createStatementParams($params);

    $records = Shopware()->Db()->fetchAll($statementSql, $statementParams);

    return $records;
  }

  public function add($articleId, $emotionId, $position, $shopId) {
    $sql = '
      INSERT INTO
        s_prems_emotion_cms_article
      (article_id, emotion_id, position, shop_id)
      VALUES
        (?, ?, ?, ?)
    ';

    Shopware()->Db()->query($sql, array(
      $articleId,
      $emotionId,
      $position,
      $shopId
    ));
  }

  public function remove($articleId, $emotionId, $position, $shopId) {
    Shopware()->Db()->delete('s_prems_emotion_cms_article', array(
        'article_id = ?' => $articleId,
        'emotion_id = ?' => $emotionId,
        'position = ?' => $position,
        'shop_id = ?' => $shopId,
      )
    );
  }
}