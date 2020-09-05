<?php
/**
 * premsoft
 * Copyright © 2016 Premsoft - Sven Mittreiter
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
 * @copyright  Copyright (c) 2016, premsoft - Sven Mittreiter (http://www.premsoft.de)
 * @author     Sven Mittreiter <info@premsoft.de>
 */

namespace Shopware\CustomModels\PremsEmotionCms;

use Shopware\Components\Model\ModelEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model für die Einkaufsweltenzuordnung über Artikeltab
 * @ORM\Entity
 * @ORM\Table(name="s_prems_emotion_cms_article")
 */
class Article extends ModelEntity
{
  /**
   * @var integer $id
   *
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  protected $id;

  /**
   * @var
   * @ORM\Column(name="emotion_id", type="integer")
   */
  protected $emotionId;

  /**
   * @var
   * @ORM\Column(name="position", type="integer")
   */
  protected $position;

  /**
   * @var
   * @ORM\Column(name="article_id", type="integer")
   */
  protected $articleId;

  /**
   * @var
   * @ORM\Column(name="shop_id", type="integer")
   */
  protected $shopId;

  /**
   * @return mixed
   */
  public function getPosition()
  {
    return $this->position;
  }

  /**
   * @param mixed $position
   */
  public function setPosition($position)
  {
    $this->position = $position;
  }

  /**
   * @param mixed $articleId
   */
  public function setArticleId($articleId)
  {
    $this->articleId = $articleId;
  }

  /**
   * @return mixed
   */
  public function getArticleId()
  {
    return $this->articleId;
  }

  /**
   * @param mixed $shopId
   */
  public function setShopId($shopId)
  {
    $this->shopId = $shopId;
  }

  /**
   * @return mixed
   */
  public function getShopId()
  {
    return $this->shopId;
  }

  /**
   * @param mixed $emotionId
   */
  public function setEmotionId($emotionId)
  {
    $this->emotionId = $emotionId;
  }

  /**
   * @return mixed
   */
  public function getEmotionId()
  {
    return $this->emotionId;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }
}