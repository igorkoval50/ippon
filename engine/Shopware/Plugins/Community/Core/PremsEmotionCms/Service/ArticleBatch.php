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

use Shopware\PremsEmotionCms\Struct\PreparationResultStruct;
use Shopware_Plugins_Core_PremsEmotionCms_Bootstrap as Bootstrap;

class ArticleBatch
{
  const ACTION_ADD_EMOTION = 0;
  const ACTION_REMOVE_EMOTION = 1;

  /**
   * @var Bootstrap $bootstrap
   */
  protected $bootstrap;

  /**
   * @var \Shopware\PremsEmotionCms\Service\ArticleBatchData $batchData
   */
  protected $batchData;

  function __construct(Bootstrap $bootstrap, $batchData) {
    $this->bootstrap = $bootstrap;
    $this->batchData = $batchData;
  }

  protected function validateParams($params) {
    if (!$params['shopId'] || !$params['emotionId']) {
      throw new \Exception('Required fields missing');
    }
  }

  public function prepareProcess($params)
  {
    $this->validateParams($params);

    $position = 0;
    $this->openProcessSession($params);

    $recordIds = $this->batchData->getRecordIds($params);

    return new PreparationResultStruct($position, count($recordIds));
  }

  public function openProcessSession($params)
  {
    $_SESSION['prems_emotion_cms_article'] = array(
      'params' => $params,
    );
  }

  public function process($limit, $offset)
  {

    $rows = $this->batchData->getRecords($_SESSION['prems_emotion_cms_article']['params'], $limit, $offset);

    //echo "<pre>"; print_R($_SESSION['prems_emotions_on_detailpage']); echo "</pre>";

    foreach($rows as $row) {
      if ($_SESSION['prems_emotion_cms_article']['params']['type'] == self::ACTION_ADD_EMOTION) {
        $this->batchData->add($row['articleId'],
          $_SESSION['prems_emotion_cms_article']['params']['emotionId'],
          $_SESSION['prems_emotion_cms_article']['params']['position'],
          $_SESSION['prems_emotion_cms_article']['params']['shopId']);
      } elseif ($_SESSION['prems_emotion_cms_article']['params']['type'] == self::ACTION_REMOVE_EMOTION) {
        $this->batchData->remove($row['articleId'],
          $_SESSION['prems_emotion_cms_article']['params']['emotionId'],
          $_SESSION['prems_emotion_cms_article']['params']['position'],
          $_SESSION['prems_emotion_cms_article']['params']['shopId']);
      }
    }

    if ($offset >= $_SESSION['prems_emotion_cms_article']['totalCount']) {
      $limit = $_SESSION['prems_emotion_cms_article']['totalCount'];
    } else {
      $limit = ($limit + $offset);
    }

    return array(
      'position' => $limit
    );
  }

  public function closeProcessSession()
  {
    unset($_SESSION['prems_emotion_cms_article']);
  }


}