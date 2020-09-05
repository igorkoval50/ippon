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
class Shopware_Controllers_Backend_PremsEmotionCmsArticle extends Shopware_Controllers_Backend_Application  {
  protected $model = 'Shopware\CustomModels\PremsEmotionCms\Article';
  protected $alias = 'article';

  public function deleteEmotionArticleAction() {
    parent::deleteAction();
  }

  /**
   * Update einer Einkaufsweltzuordnung. Aktuell kann nur die Position verändert werden.
   */
  public function updateEmotionArticleAction() {
    $id  = $this->Request()->getParam('id');
    $position = $this->Request()->getParam('position');

    $sql = '
      UPDATE
        s_prems_emotion_cms_article
      SET
        position = ?
      WHERE
        id = ?
    ';

    Shopware()->Db()->query($sql, array(
      $position,
      $id
    ));

    $this->View()->assign(array(
      'success' => true,
    ));
  }

  /**
   * Speichert eine Einkaufsweltzuordnung zu einer Artikeldetailseite
   */
  public function createEmotionArticleAction() {
    $articleId  = $this->Request()->getParam('articleId');
    $emotionId = $this->Request()->getParam('emotionId');
    $position = $this->Request()->getParam('position');
    $shopId = $this->Request()->getParam('shopId');

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

    $this->View()->assign(array(
      'success' => true,
    ));
  }

  /**
   * Liefert alle aktiven Einkaufswelten zurück
   */
  public function getEmotionsAction() {
    $select = '
            SELECT
              *
            FROM
               s_emotion
            WHERE
              s_emotion.active = 1
            ';

    $items = Shopware()->Db()->fetchAll($select);

    $this->View()->assign(array(
      'success' => true,
      'data'    => $items,
      'count'   => count($items)
    ));
  }

  /**
   * Liefert alle angelegten Einkaufswelten einer Artikeldetailseite zurück
   */
  public function getEmotionArticleAction() {
    $articleId  = $this->Request()->getParam('articleId');

    $select = '
            SELECT
              s_prems_emotion_cms_article.*, s_emotion.name, s_prems_emotion_cms_article.shop_id AS shopId
            FROM
              s_prems_emotion_cms_article
            LEFT JOIN
              s_emotion
            ON
              s_prems_emotion_cms_article.emotion_id = s_emotion.id
            WHERE
              s_prems_emotion_cms_article.article_id = ?
            ';

    $items = Shopware()->Db()->fetchAll($select, array($articleId));

    $this->View()->assign(array(
      'success' => true,
      'data'    => $items,
      'count'   => count($items)
    ));
  }

  public function prepareProcessAction()
  {
    /** @var \Shopware\PremsEmotionCms\Service\ArticleBatch $batchService */
    $batchService = $this->get('prems_emotion_cms.article_batch');

    try {
      $resultStruct = $batchService->prepareProcess($this->Request()->getParams());
    } catch (\Exception $e) {
      return $this->View()->assign(['success' => false, 'msg' => $e->getMessage()]);
    }

    if ($resultStruct->getTotalResultCount() === 0) {
      return $this->View()->assign(['success' => false, 'msg' => 'No data to export', 'position' => 0, 'count' => 0]);
    }

    $_SESSION['prems_emotion_cms_article']['totalCount'] = $resultStruct->getTotalResultCount();

    return $this->View()->assign([
      'success' => true,
      'position' => $resultStruct->getPosition(),
      'count' => $resultStruct->getTotalResultCount()
    ]);
  }

  public function processAction()
  {
    $limit = $this->Request()->getParam('limit', 100);
    $offset = $this->Request()->getParam('position', 0);

    /** @var \Shopware\PremsEmotionCms\Service\ArticleBatch $batchService */
    $batchService = $this->get('prems_emotion_cms.article_batch');

    try {
      $resultData = $batchService->process($limit, $offset);
      $this->View()->assign(['success' => true, 'data' => $resultData]);
    } catch (\Exception $e) {
      $this->View()->assign(['success' => false, 'msg' => $e->getMessage()]);
    }
  }
}